<?php

/**
 * Renders the application interface for Topics (channels).
 */

$Slim->get('/:source/:deviceId/:deviceType/:deviceVersion/more',
    $mdwDeviceGuessing,
    $mdwCache(),
    function ($source, $deviceId, $deviceType, $deviceVersion) use ($Slim)
{
    $appCfg = $Slim->config('appCfg');

    // User session
    $user = Mo_UsersMapper::sessionUp($source, $deviceId, $deviceType, $deviceVersion);

    // Applets
    foreach ($appCfg['applets'] as $k => $v) {
        if (!$v['enabled']) {
            unset($appCfg['applets'][$k]);
        }
    }

    $Slim->render(
        'moreContent.tpl',
        array(
            'client' => new Mo_Client(array(
                'source' => $source,
                'deviceId' => $deviceId,
                'deviceType' => $deviceType,
                'deviceVersion' => $deviceVersion
            )),
            'user' => $user,
            'serviceData' => $appCfg['service'],
            'userApplets' => $appCfg['applets']
        )
    );
})->name('more');
