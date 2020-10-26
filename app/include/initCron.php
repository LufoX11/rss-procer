<?php

/**
 * Init file for crons and processes.
 */

$rootDir = realpath(dirname(__FILE__) . '/../../');
require "{$rootDir}/lib/Loader.php";

/**
 * General exception handler.
 */
set_exception_handler(function ($exception) {

    Loader::lib(array('Email'), true);

    $cfg = parse_ini_file(dirname(__FILE__) . '/../../app/config/main.ini', true);
    $fileName = $exception->getFile();
    $receivers = explode('|', $cfg['alerts']['cron']['toEmail']);

    // Send an alert email
    $Email = new Email();
    foreach ($receivers as $v) {
        $Email->send(array(
            'fromName' => $cfg['alerts']['cron']['fromName'],
            'fromEmail' => $cfg['alerts']['cron']['fromEmail'],
            'toEmail' => $v,
            'subject' => "RSSPROCER_CRON_ALERT: Uncaught Exception in: {$fileName}",
            'message' => $exception
        ));
    }
});

require "{$rootDir}/cron/Cron.php";
