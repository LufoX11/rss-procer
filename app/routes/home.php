<?php

/**
 * Renders the application interface for Home (default).
 */

$Slim->get('/:source/:deviceId/:deviceType/:deviceVersion/home',
    $mdwDeviceGuessing,
    $mdwCache(),
    function ($source, $deviceId, $deviceType, $deviceVersion) use ($Slim)
{
    // Only for testing purposes
    if ($Slim->request()->get('testErrorPage')) {
        throw new Exception('Testing: testErrorPage');
    }

    Loader::mo(array('NewsMapper'), true);

    // User session
    $user = Mo_UsersMapper::sessionUp($source, $deviceId, $deviceType, $deviceVersion);

    // News data
    $appCfg = $Slim->config('appCfg');
    $news = array();
    $newsTitles = array();
    $sorting = (isset($appCfg['service']['sorting']) ? $appCfg['service']['sorting'] : 'datetime');
    if ($topics = Mo_NewsMapper::fetchChannels($appCfg['service']['name'])) {
        foreach ($topics as $k => $v) {
            if ($raw = Mo_NewsMapper::fetchNews($k, $appCfg['service']['name'])) {

                // Avoid showing duplicates
                foreach ($raw as $rk => $rv) {
                    if (in_array($rv->title, $newsTitles)) {
                        unset($raw[$rk]);
                    } else {
                        $newsTitles[] = $rv->title;
                    }
                }

                if ($raw) {
                    $news += $raw;
                }
            }
        }
        if ($sorting == 'datetime') {
            uasort($news, function ($a, $b) {
                return ($a->datetime < $b->datetime);
            });

        }
        $news = array_slice($news, 0, $appCfg['main']['framework']['maxNewsToShow']);
    } else {
        $topics = array();
    }
    $newsForPreload = array();
    array_walk($news, function ($v) use (&$newsForPreload) {
        $newsForPreload[] = '"news/' . $v->checksum . '"';
    });
    $newsForPreload = implode(', ', $newsForPreload);

    // Read news
    $readNews = ($user->getSetting(Mo_UserSetting::READ_NEWS) ?
        $user->getSetting(Mo_UserSetting::READ_NEWS) : array());

    $Slim->render(
        'homeContent.tpl',
        array(
            'client' => new Mo_Client(array(
                'source' => $source,
                'deviceId' => $deviceId,
                'deviceType' => $deviceType,
                'deviceVersion' => $deviceVersion
            )),
            'user' => $user,
            'serviceData' => $appCfg['service'],
            'news' => $news,
            'newsForPreload' => $newsForPreload,
            'topics' => $topics,
            'readNews' => $readNews
        )
    );
})->name('home');
