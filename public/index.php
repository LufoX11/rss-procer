<?php

/**
 * Boot file.
 */

require '../app/include/init.php';
require '../lib/Loader.php';

$appCfgMain = parse_ini_file('../app/config/main.ini', true);

// Application general status
if ($appCfgMain['framework']['status'] == 'offline') {
    $refreshUri = htmlentities("http://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}", ENT_QUOTES, 'UTF-8');
    include '../app/View/tpl/offline.html';
    exit();
}

Loader::lib(array('Utils', 'Slim/Slim'));
use Slim\Slim;
Slim::registerAutoloader();

Loader::file('../app/View/Renderer.php');
Loader::mo(array('Client', 'Applet', 'UsersMapper', 'FrontCache'));
Loader::hp(array('Appearance', 'Browser', 'View'));

// System config
$appCfg = array(
    'main' => $appCfgMain,
    'front' => parse_ini_file('../app/config/front.ini', true),
    'paths' => parse_ini_file('../app/config/paths.ini', true),
    'services' => parse_ini_file('../app/config/services.ini', true),
    'applets' => parse_ini_file('../app/config/applets.ini', true),
    'cache' => parse_ini_file('../app/config/cache.ini', true),
    'appearance' => parse_ini_file('../app/config/appearance.ini', true),
    'databases' => parse_ini_file('../app/config/databases.ini', true)
);

$uriParts = array_values(array_filter(explode('/', current(explode('?', $_SERVER['REQUEST_URI'])))));
$appCfg['paths']['web']['site']['base'] = '/' . implode('/', array_slice($uriParts, 0, 4));
$appCfg['paths']['fs']['public'] = dirname(__FILE__);
if (current(explode('?', $_SERVER['REQUEST_URI'])) != '/') {
    $serviceName = substr($_SERVER['REQUEST_URI'], 1, stripos($_SERVER['REQUEST_URI'], '/', 1) - 1);
    if (in_array($serviceName, $appCfg['main']['services'])) {
        $appCfg['service'] = $appCfg['services'][$serviceName];
        $appCfg['applets'] = (isset($appCfg['applets'][$serviceName]) ? $appCfg['applets'][$serviceName] : array());
        $appCfg['service']['name'] = $serviceName;
        $appCfg['database'] = $appCfg['databases'][$serviceName];
        $appCfg['appearance'] = (isset($appCfg['appearance'][$serviceName]) ? $appCfg['appearance'][$serviceName] : array());
    }
}

// Special PHP config
if (isset($serviceName) && isset($appCfg['service']['sys']['locale'])) {
    setlocale(LC_ALL, $appCfg['service']['sys']['locale']);
}
setlocale(LC_NUMERIC, 'en_US');
date_default_timezone_set((isset($serviceName) && isset($appCfg['service']['sys']['timezone']) ?
    $appCfg['service']['sys']['timezone'] : 'America/Argentina/Buenos_Aires'));

// Framework initialization
if (isset($serviceName)) {
    Da_Handler_Mysql::$defaultDriver = $serviceName;
}
$Slim = new Slim(array(
    'view' => new View_Renderer(array(
        'appCfg' => $appCfg,
        'domain' => ($_SERVER['REQUEST_URI'] == '/' ? 'guest' : 'public1')
    )),
    'appCfg' => $appCfg,
    'debug' => ($appCfg['main']['framework']['mode'] == 'development'),
    'templates.path' => realpath(dirname(__FILE__) . '/../app/View/tpl'),
    'cookies.secret_key' => 'PRONTO_shake',
    'cookies.lifetime' => '25 years',
    'mode' => $appCfg['main']['framework']['mode']
));
View_Renderer::setExclusiveMode($Slim->getEncryptedCookie('loginType') == 'rssprocer');

// Config for environment
require '../app/modes/production.php';

// Controllers
require '../app/hooks/slimBefore.php';
require '../app/hooks/slimBeforeDispatch.php';
require '../app/filters/mobileDevices.php';
require '../app/middlewares/deviceGuessing.php';
require '../app/middlewares/cache.php';
require '../app/routes/guest.php';
require '../app/routes/appLogin.php';
require '../app/routes/boot.php';
require '../app/routes/guestLegal.php';
require '../app/routes/config.php';
require '../app/routes/home.php';
require '../app/routes/topics.php';
require '../app/routes/topic.php';
require '../app/routes/news.php';
require '../app/routes/weather.php';
require '../app/routes/about.php';
require '../app/routes/recommend.php';
require '../app/routes/more.php';
require '../app/routes/applets/customForm.php';

$Slim->run();
