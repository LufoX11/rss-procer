<?php

/**
 * Renders the application interface for Weather.
 */

$Slim->get('/:source/:deviceId/:deviceType/:deviceVersion/weather',
    $mdwDeviceGuessing,
    $mdwCache(),
    function ($source, $deviceId, $deviceType, $deviceVersion) use ($Slim)
{
    // User session
    Mo_UsersMapper::sessionUp($source, $deviceId, $deviceType, $deviceVersion);
    $user = Mo_UsersMapper::findByDeviceIdAndType($source, $deviceId, $deviceType);
    $appCfg = $Slim->config('appCfg');
    if ($user->getSetting(Mo_UserSetting::LOCATION)) {
        $appCfg['service']['location'] = $user->getSetting(Mo_UserSetting::LOCATION);
    }

    // Weather options
    $Mo_Applet_Weather = Mo_Applet::get('Weather', array(
        'cfg' => $appCfg,
        'ds' => array(
            'name' => $appCfg['main']['sys']['weatherSource'],
            'options' => array(
                'key' => $appCfg['main']['framework']['wundergroundWeatherApiKey']
            )
         ),
        'location' => $appCfg['service']['location']));

    $Slim->render(
        'weatherContent.tpl',
        array(
            'client' => new Mo_Client(array(
                'source' => $source,
                'deviceId' => $deviceId,
                'deviceType' => $deviceType,
                'deviceVersion' => $deviceVersion
            )),
            'user' => $user,
            'serviceData' => $appCfg['service'],
            'userLocation' => $appCfg['service']['location'],
            'weather' => $Mo_Applet_Weather->fetch(),
            'weatherLocations' => $Mo_Applet_Weather->fetchLocations()
        )
    );
});
