<?php

/**
 * Renders the application interface for About.
 */

$Slim->map('/:source/:deviceId/:deviceType/:deviceVersion/about',
    $mdwDeviceGuessing,
    $mdwCache(),
    function ($source, $deviceId, $deviceType, $deviceVersion) use ($Slim)
{
    $appCfg = $Slim->config('appCfg');

    // User session
    $user = Mo_UsersMapper::sessionUp($source, $deviceId, $deviceType, $deviceVersion);

    $Slim->render(
        'aboutContent.tpl',
        array(
            'client' => new Mo_Client(array(
                'source' => $source,
                'deviceId' => $deviceId,
                'deviceType' => $deviceType,
                'deviceVersion' => $deviceVersion
            )),
            'user' => $user,
            'serviceData' => $appCfg['service'],
            'supportEmail' => $appCfg['main']['sys']['emails']['email'],
        )
    );
})->via('GET', 'POST');
