<?php

/**
 * Renders the application interface for Smartphone devices.
 */

$Slim->get('/smartphone', function () use ($Slim, $appCfg)
{
    $deviceType = Hp_Browser::getDeviceType();
    $showGoogle =
        in_array($deviceType, array(
            ImobileDetector::DEVICE_TYPE_ANDROID,
            ImobileDetector::DEVICE_TYPE_GENERIC));
    $showApple =
        in_array($deviceType, array(
            ImobileDetector::DEVICE_TYPE_IPHONE,
            ImobileDetector::DEVICE_TYPE_IPOD,
            ImobileDetector::DEVICE_TYPE_GENERIC));
    $showRim =
        in_array($deviceType, array(
            ImobileDetector::DEVICE_TYPE_BLACKBERRY,
            ImobileDetector::DEVICE_TYPE_GENERIC));

    $Slim->render(
        'smartphoneContent.tpl',
        array(
            'services' => $appCfg['services'],
            'deviceType' => $deviceType,
            'showGoogle' => $showGoogle,
            'showApple' => $showApple,
            'showRim' => $showRim,
            'q' => $Slim->request()->get('q')
        )
    );
});
