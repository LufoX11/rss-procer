<?php

/**
 * AJAX responser for Weather applet.
 */

$Slim->get('/:source/:deviceId/:deviceType/:deviceVersion/ajax/applet/weather/get/:location',
    $mdwDeviceGuessing,
    function ($source, $deviceId, $deviceType, $deviceVersion, $location) use ($Slim)
{
    $appCfg = $Slim->config('appCfg');
    $Hp_View = new Hp_View(array('Path'), $appCfg);

    // User session
    Mo_UsersMapper::sessionUp($source, $deviceId, $deviceType, $deviceVersion);
    $user = Mo_UsersMapper::findByDeviceIdAndType($source, $deviceId, $deviceType);
    if ($theme = $user->getSetting(Mo_UserSetting::THEME)) {
        foreach (Mo_UserSetting::$themes as $k => $v) {
            if (in_array($theme, $v)) {
                $Hp_View->setImagesSubdir("themes/{$k}");
                break;
            }
        }
    }

    $Mo_Applet_Weather = Mo_Applet::get('Weather', array(
        'cfg' => $appCfg,
        'ds' => array(
            'name' => $appCfg['main']['sys']['weatherSource'],
            'options' => array(
                'key' => $appCfg['main']['framework']['wundergroundWeatherApiKey']
            )
         ),
        'location' => $location));
    if ($weather = $Mo_Applet_Weather->fetch()) {
        $res = array();
        if ($weather->current['temperature']) {
            $res['current'] = array(
                'date' => ucwords(strftime('%A %d/%m/%y')),
                'location' => $weather->location,
                'title' => 'Hoy',
                'icon' => $Hp_View->getImg($weather->current['icon']),
                'description' => $weather->current['description'],
                'temperature' => $weather->current['temperature'],
                'humidity' => $weather->current['humidity']
            );
        } else {
            $res['current'] = array(
                'temperature' => null,
                'icon' => $Hp_View->getImg('maintenance.png')
            );
        }
        $currentDay = time();
        foreach ($weather->forecast as $v) {
            $day = ($currentDay == time() ? 'Hoy' : ucwords(strftime('%A', $currentDay)));
            $currentDay += 86400;
            $res['forecast'][] = array(
                'title' => $day,
                'icon' => $Hp_View->getImg($v['icon']),
                'description' => $v['description'],
                'high' => $v['high'],
                'low' => $v['low']
            );
        }

        // Save settings in user session
        $user = Mo_UsersMapper::sessionUp($source, $deviceId, $deviceType, $deviceVersion);
        $setting = new Mo_UserSetting(array(
            'users_id' => $user->id,
            'key' => Mo_UserSetting::LOCATION,
            'value' => $location
        ));
        Mo_UsersMapper::insertSetting($source, $user, $setting);

        Ajax::setSuccess($Slim, $res);
    } else {
        Ajax::setError($Slim);
    }
});
