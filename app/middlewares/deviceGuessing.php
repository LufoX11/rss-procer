<?php

/**
 * Device guessing middleware. It checks and properly sets the device data.
 */

$mdwDeviceGuessing = function (\Slim\Route $route) use ($Slim)
{
    $params = $route->getParams();
    $deviceType = (isset($params['deviceType']) ?
        $params['deviceType'] : Hp_Browser::getDeviceType());

    // If device type is "guess" and we have all required URL data, then try to guess the device type by HTTP headers
    if ($deviceType == 'guess'
        && isset($params['source'])
        && isset($params['deviceId'])
        && isset($params['deviceVersion']))
    {
        $deviceType = Hp_Browser::getDeviceType();
        $Slim->redirect($Slim->urlFor('home', array(
            'source' => $route->getParam('source'),
            'deviceId' => $route->getParam('deviceId'),
            'deviceType' => $deviceType,
            'deviceVersion' => $route->getParam('deviceVersion'))));
    }

    // If device is generic, it might be a PC, so force to a hard isMobile() to guess from HTTP headers
    if ($deviceType == ImobileDetector::DEVICE_TYPE_GENERIC) {
        Hp_Browser::isMobile(true);
    }
    Hp_Browser::setDeviceType($deviceType);
};
