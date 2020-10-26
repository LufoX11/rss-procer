<?php

/**
 * Tracking system responser.
 */

require '../app/include/init.php';
require '../lib/Loader.php';

Loader::lib(array('Slim/Slim'));
use Slim\Slim;
Slim::registerAutoloader();

Loader::vendor(array('SimpleMongoDb/SimpleMongoDb'));
Loader::mo(array('Tracking'));

// System config
$appCfg = array(
    'main' => parse_ini_file('../app/config/main.ini', true),
    'databases' => parse_ini_file('../app/config/databases.ini', true)
);

// Framework initialization
$Slim = new Slim(array(
    'appCfg' => $appCfg,
    'cookies.secret_key' => 'PRONTO_shake',
    'mode' => $appCfg['main']['framework']['mode']
));

// Config for environment
require '../app/modes/production.php';

// Controllers
require '../app/routes/tracking/post.php';

$Slim->run();
