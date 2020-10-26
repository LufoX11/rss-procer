<?php

/**
 * Renders the application interface for Tablet devices.
 */

$Slim->get('/tablet', function () use ($Slim, $appCfg)
{
    $deviceType = Hp_Browser::getDeviceType();
    $showGoogle =
        in_array($deviceType, array(
            ImobileDetector::DEVICE_TYPE_ANDROID_TABLET,
            ImobileDetector::DEVICE_TYPE_GENERIC));
    $showApple =
        in_array($deviceType, array(
            ImobileDetector::DEVICE_TYPE_IPAD,
            ImobileDetector::DEVICE_TYPE_GENERIC));
    $showAll = $showApple && $showGoogle;

    $Slim->render(
        'tabletContent.tpl',
        array(
            'services' => $appCfg['services'],
            'deviceType' => $deviceType,
            'showGoogle' => $showGoogle,
            'showApple' => $showApple,
            'showAll' => $showAll,
            'q' => $Slim->request()->get('q')
        )
    );
});
