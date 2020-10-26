<?php

/**
 * Renders the application interface for Recommend.
 */

$Slim->map('/:source/:deviceId/:deviceType/:deviceVersion/recommend',
    $mdwDeviceGuessing,
    $mdwCache(),
    function ($source, $deviceId, $deviceType, $deviceVersion) use ($Slim)
{
    // User session
    $user = Mo_UsersMapper::sessionUp($source, $deviceId, $deviceType, $deviceVersion);

    $appCfg = $Slim->config('appCfg');

    $Slim->render(
        'recommendContent.tpl',
        array(
            'client' => new Mo_Client(array(
                'source' => $source,
                'deviceId' => $deviceId,
                'deviceType' => $deviceType,
                'deviceVersion' => $deviceVersion
            )),
            'user' => $user,
            'serviceData' => $appCfg['service'],
            'jqmCache' => false
        )
    );
})->via('GET', 'POST');
