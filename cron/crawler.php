<?php

/**
 * Crawler process.
 */

////
// Options and Settings.
////

$rootDir = realpath(dirname(__FILE__) . '/../');

require "{$rootDir}/app/include/initCron.php";

Loader::hp(array('String'));
Loader::lib(array('Utils', 'Slim/Log'));
Loader::da(array('Handler/Mysql'));
Loader::vendor(array('Slim/Extras/LogWriter/TimestampLogFileWriter'));

$usage = ''
    . "\nUSAGE:\n\n"
    . "php crawler.php <service_name>\n\n";
$serviceName = (isset($argv[1]) ? $argv[1] : null);
$force = (isset($argv[2]) ? (bool) $argv[2] : false);

if ($serviceName && $serviceName == '--help') {
    die($usage);
}
if (!$serviceName) {
    die("\nError: Missing required arguments.\n\n---{$usage}");
}

$appCfg = array(
    'main' => parse_ini_file("{$rootDir}/app/config/main.ini", true),
    'services' => parse_ini_file("{$rootDir}/app/config/services.ini", true),
    'paths' => parse_ini_file("{$rootDir}/app/config/paths.ini", true),
    'cache' => parse_ini_file("{$rootDir}/app/config/cache.ini", true),
    'databases' => parse_ini_file("{$rootDir}/app/config/databases.ini", true),
    'crawler' => parse_ini_file("{$rootDir}/app/config/crawler.ini", true)
);

if (!isset($appCfg['services'][$serviceName])) {
    die("\nError: Invalid service name.\n\n---{$usage}");
}

// array(1): The process list should only check for the first param key (0 = file name, 1 = service name in $argv)
// to determine if the process is still alive or not, otherwise if we pass "force", it might fail as grep would be:
// "grep cron/crawler.php laarena force" and wouldn't match against other process like "grep cron/crawler.php laarena".
$cronLockFile = "/tmp/crawler-{$serviceName}.lock";
Cron::lock(3600, $cronLockFile, array(1));

$appCfg['paths']['sys']['service']['img'] = "{$rootDir}/public/static/img/services/{$serviceName}";
$appCfg['service'] = $appCfg['services'][$serviceName];
$appCfg['database'] = $appCfg['databases'][$serviceName];
$appCfg['crawler'] = $appCfg['crawler'][$serviceName];
Da_Handler_Mysql::$defaultDriver = $serviceName;

// Special PHP config
setlocale(LC_NUMERIC, 'en_US');
date_default_timezone_set($appCfg['service']['sys']['timezone']);

$TimestampLogFileWriter = new TimestampLogFileWriter(array('path' => "{$rootDir}/logs/cron/"));
$logger = new Slim\Log($TimestampLogFileWriter);
$logger->setLevel(Slim\Log::INFO);

////
// Main
////

Loader::lib(array('Curl', 'GoogleUrlShortenerApi'));
Loader::mo(array('Channel', 'Sorting', 'News', 'NewsMapper', 'FrontCache'));
Loader::vendor(array('Readability/Readability', 'WideImage/WideImage'));

$Cron = new Cron($appCfg);
$Curl = new Curl(array(
    CURLOPT_HTTPHEADER => array('Content-type: text/xml'),
    CURLOPT_HEADER => false,
    CURL_HTTP_VERSION_1_1 => true,
    CURLOPT_ENCODING => 'gzip, deflate',
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_USERAGENT => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) '
        . 'Chrome/28.0.1500.95 Safari/537.36'
));
$GoogleUrlShortenerApi = new GoogleUrlShortenerApi(
    array('apiKey' => $appCfg['main']['framework']['googleSimpleApiKey']));
$translationList = (isset($appCfg['crawler']['translationList']) ?
    $appCfg['crawler']['translationList'] : array());
$stats = array(
    'Report Date' => date('Y-m-d H:i:s'),
    'Total Time' => 0,

    // Sources
    'Processed Sources' => 0,
    'Failed Sources' => 0,
    'Not Updated Sources' => 0,
    'Sources Time' => 0,
    'Failed Sources List' => '',

    // Channels
    'Processed Channels' => 0,
    'Failed Channels' => 0,
    'Channels Time' => 0,
    'Failed Channels List' => '',

    // News
    'Processed News' => 0,
    'Failed News' => 0,
    'News Time' => 0,
    'Failed News List' => '',
    'Failed News Fallback' => 'NO',
    'Failed News Fallback Used' => 'NO',

    // Sorting
    'Processed Sorting' => 0,
    'Failed Sorting' => 0,
    'Sorting Time' => 0,

    // Others
    'Exclusion filter failure' => 0
);

foreach ($appCfg['crawler']['sources'] as $sk => $s) {
    try {
        ////
        // Fetch external resource (XML document)
        ////

        Utils::time();
        $sourceFilePath = sprintf('%s/logs/cron/files/%s-%s-%s',
            $rootDir, $serviceName, strtotime(date('Y-m-d')), basename($s));
        if (!$raw = $Curl->exec($s)) {
            $stats['Failed Sources'] ++;
            $stats['Failed Sources List'] .= "{$s}; ";
            $logger->error("Could not retrieve data from source: {$s}.");
            continue;
        }

        // Fix encoding, repair bad XML strings and convert rare chars
        $raw = Hp_String::convertRareChars($raw);
        $inputEncoding = 'utf8';
        if (!mb_detect_encoding($raw, 'UTF-8', true)) {
            $inputEncoding = 'latin1';
        }
        $tidy = new tidy();
        $raw = $tidy->repairString($raw, array(
            'input-encoding' => $inputEncoding,
            'output-encoding' => 'utf8',
            'input-xml' => true,
            'output-xml' => true));
        $raw = Utils::sanitizeString($raw, array(
            'stripTags' => 'NONE',
            'clean' => false,
            'fixEncoding' => !mb_detect_encoding($raw, 'UTF-8', true),
            'decodeEntities' => false));
        $raw = Hp_String::removeUnnecessaryTags($raw, array('script', 'style'));

        if (!($xml = simplexml_load_string($raw))) {
            $stats['Failed Sources'] ++;
            $stats['Failed Sources List'] .= "{$s}; ";
            $logger->error("Invalid document from source: {$s}.");

            // Alert email
            $Cron->sendAlertEmail(
                "RSSPROCER_CRON_ALERT: Invalid document from source: {$s}.",
                "Details:\n\n---\n{$raw}");

            continue;
        }
        if (isset($appCfg['crawler']['feed']['namespace'])) {
            $xml->registerXPathNamespace('f', $appCfg['crawler']['feed']['namespace']);
        }

        $stats['Processed Sources'] ++;
        if (file_exists($sourceFilePath)) {
            $currentFile = file_get_contents($sourceFilePath);
            if (sha1($currentFile) == sha1($raw) && !$force) {
                $logger->info("File has not changed since last time. File: {$sourceFilePath}.");
                $stats['Not Updated Sources'] ++;
                continue;
            }
        }
        Utils::fileWrite($sourceFilePath, $raw, 'w');
        $stats['Sources Time'] += $t = Utils::time(true);
        $stats['Total Time'] += $t;

        ////
        // Save Channel
        ////

        Utils::time();
        $channel = reset($xml->xpath(Utils::getIniValue($appCfg['crawler']['channel']['root'])));
        if (isset($appCfg['crawler']['channelsTitles'][$sk])) {
            $title = Utils::getIniValue($appCfg['crawler']['channelsTitles'][$sk]);
        } else if (!($title = (string) reset($channel->xpath(Utils::getIniValue(
            $appCfg['crawler']['channel']['title']))))) {

            $stats['Failed Sources'] ++;
            $stats['Failed Sources List'] .= "{$s}; ";
            $logger->error("Missing required data (title) in channel from source: {$s}.");

            // Alert email
            $Cron->sendAlertEmail(
                "RSSPROCER_CRON_ALERT: Missing required data (title) in channel from source: {$s}.",
                "Details:\n\n---\n{$raw}");

            continue;
        }
        $description = (isset($appCfg['crawler']['channel']['description']) ?
            (string) reset($channel->xpath(Utils::getIniValue(
            $appCfg['crawler']['channel']['description']))) : null);
        if (isset($appCfg['crawler']['channelsIconsDir'])) {
            $image = "local://{$appCfg['crawler']['channelsIcons'][$sk]}";
        } else {
            $image = (isset($appCfg['crawler']['channel']['image']) ?
                (string) reset($channel->xpath(Utils::getIniValue(
                $appCfg['crawler']['channel']['image']))) : null);
            if (!WideImage::isValidFormat($image)) {
                $stats['Failed Sources'] ++;
                $stats['Failed Sources List'] .= "{$s}; ";
                $logger->error("Invalid channel image format from source: {$s}.");
                continue;
            }
        }

        // Sanitizes the values
        $title = Utils::sanitizeString($title, array('translations' => $translationList));
        if ($description) {
            $description = Utils::sanitizeString($description, array('translations' => $translationList));
        }

        // Save static image (if image from XML)
        try {
            if ($image && !isset($appCfg['crawler']['channelsIconsDir'])) {
                $imageHandler = WideImage::load($image);
                if ($imageHandler->getHeight() > $imageHandler->getWidth()) {
                    // Resize to fit the width (height will be bigger, so will crop to fit)
                    $imageHandler
                        ->resize(81)
                        ->crop('center', 'center', 80, 80)
                        ->saveToFile("{$appCfg['paths']['sys']['service']['img']}/channel-{$sk}.jpg");
                } else {
                    // Resize to fit the height (width will be bigger, so will crop to fit)
                    $imageHandler
                        ->resize(null, 81)
                        ->crop('center', 'center', 80, 80)
                        ->saveToFile("{$appCfg['paths']['sys']['service']['img']}/channel-{$sk}.jpg");
                }
            }
        } catch (WideImage_Exception $e) {
            // Nothing to do
        }

        $Mo_Channel = new Mo_Channel(array(
            'title' => $title,
            'description' => $description,
            'image' => $image
        ));
        if (!(Mo_NewsMapper::insertChannel($Mo_Channel, $serviceName))) {
            $stats['Failed Channels'] ++;
            $stats['Failed Channels List'] .= $Mo_Channel->title . '; ';
            $logger->error("Could not insert channel from source: {$s}.");

            // Alert email
            $Cron->sendAlertEmail(
                "RSSPROCER_CRON_ALERT: Could not insert channel from source: {$s}.",
                "Details:\n\n---\nTitle: '{$title}'\nDescription: '{$description}'\nImage: '{$image}'");

            continue;
        }
        $stats['Processed Channels'] ++;
        $stats['Channels Time'] += $t = Utils::time(true);
        $stats['Total Time'] += $t;

        ////
        // Save News
        ////

        Utils::time();
        $channels = Mo_NewsMapper::fetchChannels($serviceName);
        if (!($currentChannel = Mo_NewsMapper::findChannelByTitle($Mo_Channel->title, $serviceName))) {
            $stats['Failed Channels'] ++;
            $stats['Failed Channels List'] .= $Mo_Channel->title . '; ';
            $logger->error("Couldn't find channel by title: {$s}.");

            // Alert email
            $Cron->sendAlertEmail(
                "RSSPROCER_CRON_ALERT: Couldn't find channel by title: {$s}.",
                "Details:\n\n---\nTitle: '{$Mo_Channel->title}'");

            continue;
        }
        $news = $xml->xpath(Utils::getIniValue($appCfg['crawler']['news']['root']));
        if (is_array($news) && count($news) == 1) {
            $news = current($news);
        }
        $allNews = array();
        $newsCounter = 0;
        foreach ($news as $n) {

            $newsHasFailed = false;

            // News limit per channel
            if (++ $newsCounter > $appCfg['main']['framework']['maxNewsToShow']) {
                break;
            }

            // Set the namespace (required for ATOM feeds)
            // Note: We may include more than one namespace
            if (isset($appCfg['crawler']['feed']['namespace'])) {
                $n->registerXPathNamespace('f', $appCfg['crawler']['feed']['namespace']);
            }
            if (isset($appCfg['crawler']['feed']['namespace1'])) {
                $n->registerXPathNamespace('f1', $appCfg['crawler']['feed']['namespace1']);
            }

            if (!($title = mb_substr(trim((string) reset($n->xpath(Utils::getIniValue(
                $appCfg['crawler']['news']['title'])))), 0, 255)))
            {
                if (Utils::getIniOption($appCfg['crawler']['news']['description'], 'followLink')) {

                    // There's still a chance to retrieve the title from the news remote content
                    $logger->error("Missing required data (title) in news from source: {$s}. "
                        . 'Fallback to remote content title...');
                } else {
                    $stats['Failed News'] ++;
                    $logger->error("Missing required data (title) in news from source: {$s}.");
                    continue;
                }
            }
            $link = trim(isset($appCfg['crawler']['news']['link']) ?
                (string) reset($n->xpath(Utils::getIniValue(
                $appCfg['crawler']['news']['link']))) : null);

            // Special link formatter
            if (isset($appCfg['crawler']['newsLinksFormatter'])) {
                $link = str_replace('%LINK%', $link, $appCfg['crawler']['newsLinksFormatter']);
            }

            $datetime = trim(isset($appCfg['crawler']['news']['datetime']) ?
                (string) reset($n->xpath(Utils::getIniValue(
                $appCfg['crawler']['news']['datetime']))) : date('Y-m-d H:i:s'));

            // Determine if the image must be fetched from link
            $image = null;
            $getRemoteImage = false;
            if (isset($appCfg['crawler']['news']['image']))
            {
                if ($image = Utils::getIniOption($appCfg['crawler']['news']['image'], 'useLinkImage')) {
                    $getRemoteImage = 'fromDescription';
                } else if ($image = Utils::getIniOption($appCfg['crawler']['news']['image'], 'useSummaryImage')) {
                    $getRemoteImage = 'fromSummary';
                } else {
                    $image = isset($appCfg['crawler']['news']['image']) ?
                        trim((string) reset($n->xpath(Utils::getIniValue(
                        $appCfg['crawler']['news']['image'])))) : null;
                }
            }

            // Determine if we need to retrieve data from web for news body
            if (Utils::getIniOption($appCfg['crawler']['news']['description'], 'followLink')) {
                $htmlCurl = new Curl(array(
                    CURLOPT_ENCODING => 'gzip, deflate',
                    CURLOPT_USERAGENT => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) '
                        . 'Chrome/28.0.1500.95 Safari/537.36',
                    CURLOPT_CONNECTTIMEOUT => 10,
                    CURLOPT_TIMEOUT => 10
                ));
                $linkParts = parse_url($link);
                if (isset($appCfg['crawler']['links']['protocol'])) {
                    $link = str_ireplace($linkParts['scheme'], $appCfg['crawler']['links']['protocol'], $link);
                }
                if (isset($appCfg['crawler']['links']['domain'])) {
                    $link = str_ireplace($linkParts['host'], $appCfg['crawler']['links']['domain'], $link);
                }
                try {
                    if ($raw = $htmlCurl->exec($link)) {

                        // Avoid excesive hits
                        if (isset($appCfg['crawler']['floodProtection'])) {
                            $floodProtection = explode(',', $appCfg['crawler']['floodProtection']);
                            sleep(rand($floodProtection[0], $floodProtection[1]));
                        }

                        // Get image from document
                        // Note: Must be done here (before removing unnecessary tags) because some news might have
                        // the image in scripting blocks instead of the news body.
                        if ($getRemoteImage) {
                            $contentForImage = $raw; // fromDescription
                            if ($getRemoteImage == 'fromSummary') {
                                $contentForImage =
                                    (string) reset($n->xpath(Utils::getIniValue($appCfg['crawler']['news']['summary'])));
                            }
                            if (preg_match($image, $contentForImage, $matches) && isset($matches[1])) {
                                $image = end($matches);

                                // Special images formatter
                                if (isset($appCfg['crawler']['newsImagesFormatter'])) {
                                    $image = str_replace('%IMAGE%', $image, $appCfg['crawler']['newsImagesFormatter']);
                                }
                            } else {
                                $image = null;
                            }
                        }

                        // Config Exclusion list and strip unnecessary tags (like script and style)
                        // $rawT needed to perform rollback
                        $raw = $rawT = Hp_String::removeUnnecessaryTags($raw, array('script', 'style'));
                        if (isset($appCfg['crawler']['exclusionList'])) {
                            foreach ($appCfg['crawler']['exclusionList'] as $v) {
                                $raw = preg_filter($v, '', $raw);
                            }

                            // Rollback on failure
                            if (!$raw) {
                                $raw = $rawT;
                                $stats['Exclusion filter failure'] ++;
                            }
                        }

                        // We need the retrieved text to be in UTF-8 format for Readability
                        $raw = Utils::sanitizeString(
                            $raw,
                            array(
                                'stripTags' => 'NONE',
                                'clean' => false));

                        $Readability = new Readability($raw);
                        $Readability->init();
                        if (!($description = trim($Readability->getContent()->innerHTML))) {
                            $newsHasFailed = true;
                            $stats['Failed News'] ++;
                            $stats['Failed News List'] .= "{$link}; ";
                            $logger->error("Could not determine news body from source: {$s}. URI: {$link}.");
                        }

                        // Try to get the news title from the parsed link as a fallback
                        // if the title is not present in the XML
                        if (!$title && !$title = trim($Readability->getTitle()->innerHTML)) {
                            $stats['Failed News'] ++;
                            $logger->error("Fallback failed. Couldn't retrieve title from remote content "
                                . "in news from source: {$s}.");
                            continue;
                        }
                    } else {
                        $stats['Failed News'] ++;
                        $stats['Failed News List'] .= "{$link}; ";
                        $logger->error("Could not retrieve news body data from source: {$s}. URI: {$link}.");
                        continue;
                    }
                } catch (CurlException $e) {
                    $stats['Failed News'] ++;
                    $stats['Failed News List'] .= "{$link}; ";
                    $logger->error("Exception: Could not retrieve news body data from source: {$s}."
                        . " HTTP code: {$htmlCurl->getHttpCode()}. URI: {$link}.");
                }
            } else {
                $description = trim((string) reset($n->xpath(Utils::getIniValue(
                    $appCfg['crawler']['news']['description']))));

                // "Remote" should be "Content" instead.
                if ($getRemoteImage) {
                    $contentForImage = $description; // fromDescription
                    if ($getRemoteImage == 'fromSummary') {
                        $contentForImage =
                            (string) reset($n->xpath(Utils::getIniValue($appCfg['crawler']['news']['summary'])));
                    }
                    if (preg_match($image, $contentForImage, $matches) && isset($matches[1])) {
                        $image = end($matches);

                        // Special images formatter
                        if (isset($appCfg['crawler']['newsImagesFormatter'])) {
                            $image = str_replace('%IMAGE%', $image, $appCfg['crawler']['newsImagesFormatter']);
                        }
                    } else {
                        $image = null;
                    }
                }

                // Config Exclusion list and strip unnecessary tags (like script and style)
                $descriptionT = $description;
                if (isset($appCfg['crawler']['exclusionList'])) {
                    foreach ($appCfg['crawler']['exclusionList'] as $v) {
                        $description = preg_filter($v, '', $description);
                    }

                    // Rollback on failure
                    if (!$description) {
                        $description = $descriptionT;
                        $stats['Exclusion filter failure'] ++;
                    }
                }

                $description = Utils::sanitizeString(
                    $description,
                    array(
                        'stripTags' => 'NONE',
                        'clean' => false));
            }

            // Determine if we need to retrieve data from description for summary content
            if (isset($appCfg['crawler']['news']['summary'])) {
                if (Utils::getIniOption($appCfg['crawler']['news']['summary'], 'copy')) {
                    $summary = $description;
                } else {
                    $stats['Failed News Fallback'] = 'YES';
                    if (($summary = trim((string) reset($n->xpath(Utils::getIniValue(
                        $appCfg['crawler']['news']['summary']))))) && $newsHasFailed)
                    {
                        // Could not get news description from link, so fallbacking to summary from raw feed
                        $stats['Failed News Fallback Used'] = 'YES';
                        $description = $summary;
                    }
                }
            } else {
                $summary = null;
            }

            if (!$description) {
                $stats['Failed News'] ++;
                $stats['Failed News List'] .= "{$link}; ";
                $logger->error("Missing required data (description) in news from source: {$s}. URI: {$link}.");
                continue;
            }

            // Shorten URI
            $shortLink = null;
            if ($link) {
                try {
                    $shortLink = $GoogleUrlShortenerApi->shorten((string) $link);
                } catch (GoogleUrlShortenerApiException $e) {
                    $shortLink = null;
                }
            }

            // Save static image (if image from XML)
            try {
                if ($image && WideImage::isValidFormat($image)) {
                    $imageName = sha1($image);

                    // Save Medium image
                    $fgcContext = stream_context_create(array('http' => array( 
                        'timeout' => 5 // Max 5 secs for reading an image
                    ))); 
                    $imageBin = file_get_contents($image, false, $fgcContext);
                    $imageHandler = WideImage::load($imageBin);
                    if ($imageHandler->getHeight() > $imageHandler->getWidth()) {
                        $imageHandler
                            ->resize(81)
                            ->crop('center', 'center', 80, 80)
                            ->saveToFile("{$appCfg['paths']['sys']['service']['img']}/cache/{$imageName}-md.jpg");
                    } else {
                        $imageHandler
                            ->resize(null, 81)
                            ->crop('center', 'center', 80, 80)
                            ->saveToFile("{$appCfg['paths']['sys']['service']['img']}/cache/{$imageName}-md.jpg");
                    }

                    // Save large image
                    $imageHandler = WideImage::load($imageBin);
                    if ($imageHandler->getHeight() > 280) {
                        $imageHandler = $imageHandler->resize(null, 280);
                    }
                    if ($imageHandler->getWidth() > 280) {
                        $imageHandler = $imageHandler->resize(280);
                    }
                    $imageHandler->saveToFile("{$appCfg['paths']['sys']['service']['img']}/cache/{$imageName}-bg.jpg");
                    $image = "{$appCfg['paths']['web']['static']['img']}/services/{$serviceName}/cache/{$imageName}.jpg";
                } else {
                    $image = null;
                }
            } catch (WideImage_Exception $e) {
                $image = null;
            }

            // Sanitizes the values
            $title = Utils::sanitizeString($title, array('translations' => $translationList));
            $description = Utils::sanitizeString(
                $description,
                array(
                    'translations' => $translationList,
                    'stripTags' => 'MOST'));
            if ($summary) {
                $summary = mb_substr(Utils::sanitizeString($summary, array('translations' => $translationList)),
                    0, 450);
            }

            $checksum = Mo_News::generateChecksum($currentChannel->id, $title);
            $allNews[$checksum] = $Mo_News = new Mo_News(array(
                'channels_id' => $currentChannel->id,
                'title' => $title,
                'summary' => $summary,
                'description' => $description,
                'datetime' => $datetime,
                'link' => $link,
                'shortlink' => $shortLink,
                'image' => $image,
                'checksum' => $checksum
            ));
            if (!(Mo_NewsMapper::insertNews($Mo_News, $serviceName))) {
                $stats['Failed News'] ++;
                $stats['Failed News List'] .= "{$link}; ";
                $logger->error("Could not insert news from source: {$s}.");

                // Alert email
                $Cron->sendAlertEmail(
                    "RSSPROCER_CRON_ALERT: Could not insert news from source: {$s}.",
                    "Details:\n\n---\n" . print_r($Mo_News, true));

                continue;
            }
            $stats['Processed News'] ++;
        }
        $stats['News Time'] += $t = Utils::time(true);
        $stats['Total Time'] += $t;

        ////
        // Save Sorting
        ////

        Utils::time();
        $Mo_Sorting = new Mo_Sorting(array(
            'channels_id' => $currentChannel->id,
            'value' => array_keys($allNews)
        ));
        if (!Mo_NewsMapper::insertSorting($Mo_Sorting)) {
            $stats['Failed Sorting'] ++;
            $logger->error("Could not insert sorting data from source: {$s}.");
            continue;
        }
        $stats['Processed Sorting'] ++;
        $stats['Sorting Time'] += $t = Utils::time(true);
        $stats['Total Time'] += $t;
    } catch (Exception $e) {
        $stats['Failed Sources'] ++;
        $stats['Failed Sources List'] .= "{$s}; ";
        $logger->error("Unknown exception in source: {$s}.\nDetails: {$e}");

        // Alert email
        $Cron->sendAlertEmail(
            "RSSPROCER_CRON_ALERT: Unknown exception in source: {$s}.",
            "Details:\n\n---\n{$e}");

        continue;
    }
}

////
// Post actions
////

// Increment front cache if new articles
if ($stats['Not Updated Sources'] == count($appCfg['crawler']['sources'])) {
    $logger->info("Cache no updated because of no new items. Source: {$serviceName}.");
} else {
    Mo_FrontCache::increment($serviceName);
}

////
// Stats and Reports
////

$statsFile = $stats;
array_walk($statsFile, function(&$v, $k) { $v = "{$k}: {$v}"; });
$statsFile = implode("\n", $statsFile);
echo "\n---\n{$statsFile}\n\n";
Utils::fileWrite(
    sprintf('%s/data/reports/cron/%s.txt', $rootDir, strtotime($stats['Report Date'])), $statsFile);
Cron::unlock($cronLockFile);
