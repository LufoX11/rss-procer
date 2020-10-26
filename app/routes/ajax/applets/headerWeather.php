<?php

/**
 * AJAX responser for Header-Weather applet.
 */

$Slim->get('/:source/:deviceId/:deviceType/:deviceVersion/ajax/applet/headerWeather/get',
    $mdwDeviceGuessing,
    function ($source, $deviceId, $deviceType, $deviceVersion) use ($Slim)
{
    $appCfg = $Slim->config('appCfg');
    $Hp_View = new Hp_View(array('Path'), $appCfg);

    // User session
    $user = Mo_UsersMapper::sessionUp($source, $deviceId, $deviceType, $deviceVersion);
    if ($user->getSetting(Mo_UserSetting::LOCATION)) {
        $appCfg['service']['location'] = $user->getSetting(Mo_UserSetting::LOCATION);
    }
    if ($theme = $user->getSetting(Mo_UserSetting::THEME)) {
        if (Mo_UsersMapper::isThemeAvailable($theme)) {
            foreach (Mo_UserSetting::$themes as $k => $v) {
                if (in_array($theme, $v)) {
                    $Hp_View->setImagesSubdir("themes/{$k}");
                    break;
                }
            }
        } else {
            $Hp_View->setImagesSubdir("themes/black");
        }
    }

    // We check HTTP_REFERER because we need to know the section from where we are making the request
    if (isset($_SERVER['HTTP_REFERER'])) {
        $uriParts = array_values(array_filter(explode('/', current(explode('?',
            str_ireplace(array('http://', 'https://'), '', $_SERVER['HTTP_REFERER']))))));
        $headerMenuActive = (isset($uriParts[5]) ? $uriParts[5] : 'home');
        $uri = $Hp_View->getLink('weather');
    } else {
        // Default value
        $headerMenuActive = 'home';
        $uri = '#';
    }

    $Mo_Applet_Weather = Mo_Applet::get('Weather', array(
        'cfg' => $appCfg,
        'ds' => array(
            'name' => $appCfg['main']['sys']['weatherSource'],
            'options' => array(
                'key' => $appCfg['main']['framework']['wundergroundWeatherApiKey']
            )
         ),
        'location' => $appCfg['service']['location']));
    if (($weather = $Mo_Applet_Weather->fetch()) && $weather->current['temperature']) {
        Ajax::setSuccess($Slim, array(
            'link' => ($headerMenuActive == 'weather' ? '#' : $uri),
            'date' => ucwords(strftime('%A %d/%m/%y')),
            'temperature' => $weather->current['temperature'],
            'humidity' => $weather->current['humidity'],
            'icon' => $Hp_View->getImg($weather->current['icon']),
            'location' => "<strong>{$weather->location}</strong>"
        ));
    } else {
        Ajax::setError($Slim, array(
            'link' => ($headerMenuActive == 'weather' ? '#' : $uri),
            'temperature' => null,
            'icon' => $Hp_View->getImg('maintenance.png')
        ));
    }
});
