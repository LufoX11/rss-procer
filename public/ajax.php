<?php

/**
 * AJAX responser.
 */

require '../app/include/init.php';
require '../lib/Loader.php';

Loader::lib(array('Utils', 'Ajax', 'Slim/Slim'));
use Slim\Slim;
Slim::registerAutoloader();

Loader::mo(array('Client', 'Applet', 'UsersMapper'));
Loader::hp(array('Appearance', 'View', 'Browser'));

// System config
$appCfg = array(
    'main' => parse_ini_file('../app/config/main.ini', true),
    'front' => parse_ini_file('../app/config/front.ini', true),
    'paths' => parse_ini_file('../app/config/paths.ini', true),
    'service' => parse_ini_file('../app/config/services.ini', true),
    'applets' => parse_ini_file('../app/config/applets.ini', true),
    'cache' => parse_ini_file('../app/config/cache.ini', true),
    'databases' => parse_ini_file('../app/config/databases.ini', true)
);

if (isset($_SERVER['HTTP_REFERER'])) {
    $uri = str_ireplace(array('http://', 'https://'), '', $_SERVER['HTTP_REFERER']);
    $uriParts = array_values(array_filter(explode('/', current(explode('?', $uri)))));
    $appCfg['paths']['web']['site']['base'] = 'http://' . implode('/', array_slice($uriParts, 0, 5));
}
$appCfg['paths']['fs']['public'] = dirname(__FILE__);
$serviceName = substr($_SERVER['REQUEST_URI'], 1, stripos($_SERVER['REQUEST_URI'], '/', 1) - 1);
$appCfg['service'] = $appCfg['service'][$serviceName];
$appCfg['service']['name'] = $serviceName;
$appCfg['database'] = $appCfg['databases'][$serviceName];
$appCfg['applets'] = (isset($appCfg['applets'][$serviceName]) ? $appCfg['applets'][$serviceName] : array());

// Framework initialization
Da_Handler_Mysql::$defaultDriver = $serviceName;
$Slim = new Slim(array(
    'appCfg' => $appCfg,
    'cookies.secret_key' => 'PRONTO_shake',
    'mode' => $appCfg['main']['framework']['mode']
));

// Config for environment
require '../app/modes/production.php';

// Controllers
require '../app/hooks/slimBefore.php';
require '../app/hooks/slimBeforeDispatch.php';
require '../app/filters/mobileDevices.php';
require '../app/middlewares/deviceGuessing.php';
require '../app/routes/ajax/applets/headerWeather.php';
require '../app/routes/ajax/applets/weather.php';
require '../app/routes/ajax/settings.php';
require '../app/routes/ajax/application.php';
require '../app/routes/ajax/news.php';
require '../app/routes/ajax/contact.php';

$Slim->run();
