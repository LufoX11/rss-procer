<?php

/**
 * Stats generator.
 */

////
// Options and Settings.
////

$rootDir = realpath(dirname(__FILE__) . '/../');

require "{$rootDir}/lib/Loader.php";
Loader::lib(array('Utils', 'Slim/Log'));
Loader::vendor(array('Slim/Extras/LogWriter/TimestampLogFileWriter'));

$appCfg = array(
    'main' => parse_ini_file("{$rootDir}/app/config/main.ini", true),
    'services' => parse_ini_file("{$rootDir}/app/config/services.ini", true),
    'databases' => parse_ini_file("{$rootDir}/app/config/databases.ini", true)
);

// Special PHP config
setlocale(LC_NUMERIC, 'en_US');
date_default_timezone_set('America/Argentina/Buenos_Aires');

$TimestampLogFileWriter = new TimestampLogFileWriter(array('path' => "{$rootDir}/logs/cron/"));
$logger = new Slim\Log($TimestampLogFileWriter);
$logger->setLevel(Slim\Log::INFO);

////
// Main
////

Loader::mo(array('Stat'));

$Mo_StatMapper = new Mo_StatMapper($appCfg);
$stats = array(
    'Report Date' => date('Y-m-d H:i:s'),
    'Total Time' => 0,

    // Services
    'Processed Services' => 0,
    'Failed Services' => 0,

    // Stats
    'Processed Stats' => 0,
    'Failed Stats' => 0
);

foreach ($appCfg['main']['services'] as $sk => $s) {
    try {
        Utils::time();

        // USERS_TOTALS
        $usersTotal = $Mo_StatMapper->fetchUsersTotals($s);
        if ($Mo_StatMapper->save($usersTotal)) {
            $stats['Processed Stats'] ++;
        } else {
            $stats['Failed Stats'] ++;
        }

        // NEW_USERS 
        $newUsers = $Mo_StatMapper->fetchNewUsers($s);
        if ($Mo_StatMapper->save($newUsers)) {
            $stats['Processed Stats'] ++;
        } else {
            $stats['Failed Stats'] ++;
        }

        // BY_DEVICETYPE
        $byDevicetype = $Mo_StatMapper->fetchByDevicetype($s);
        if ($Mo_StatMapper->save($byDevicetype)) {
            $stats['Processed Stats'] ++;
        } else {
            $stats['Failed Stats'] ++;
        }

        // USERS_CUSTOMIZATION
        $usersCustomization = $Mo_StatMapper->fetchUsersCustomization($s);
        if ($Mo_StatMapper->save($usersCustomization)) {
            $stats['Processed Stats'] ++;
        } else {
            $stats['Failed Stats'] ++;
        }

        $stats['Processed Services'] ++;
        $stats['Total Time'] += Utils::time(true);
    } catch (Exception $e) {
        $stats['Failed Services'] ++;
        $logger->error("Unknown exception in service: {$s}.\nDetails: {$e}");
        continue;
    }
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
