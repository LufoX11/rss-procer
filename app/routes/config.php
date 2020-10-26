<?php

/**
 * Renders the application interface for Config.
 */

$Slim->get('/:source/:deviceId/:deviceType/:deviceVersion/config',
    $mdwDeviceGuessing,
    $mdwCache(),
    function ($source, $deviceId, $deviceType, $deviceVersion) use ($Slim)
{
    Loader::mo(array('NewsMapper'), true);

    // User session
    $user = Mo_UsersMapper::sessionUp($source, $deviceId, $deviceType, $deviceVersion);

    $appCfg = $Slim->config('appCfg');
    $client = new Mo_Client(array(
        'source' => $source,
        'deviceId' => $deviceId,
        'deviceType' => $deviceType,
        'deviceVersion' => $deviceVersion
    ));

    // Text size configuration
    if (!($defaultTextSize = (int) $user->getSetting(Mo_UserSetting::TEXT_SIZE))) {
        $defaultTextSize = 0;
    }

    // Theme configuration
    if (!($defaultTheme = $user->getSetting(Mo_UserSetting::THEME))) {
        $defaultTheme = (isset($appCfg['service']['theme']) ? $appCfg['service']['theme'] : 'default');
    }

    // Info
    $appInfo = array(
        'deviceBrand' => ucwords($client->getOsBrand()),
        'deviceCategory' => ucwords($deviceType == ImobileDetector::DEVICE_TYPE_GENERIC ?
            (Hp_Browser::isMobile() ? 'smartphone' : 'pc') : Mo_Client::getDeviceCategory($deviceType)),
        'deviceTypeName' => ucwords(Mo_Client::getDeviceTypeName($deviceType)),
        'deviceVersion' => ($deviceVersion == 'unknown' ? Utils::escape('Desconocida') : $deviceVersion)
    );

    // Available themes
    $themes = array();
    foreach (Mo_UserSetting::$themes as $k => $set) {
        foreach ($set as $v) {
            if (!in_array($v, Mo_UserSetting::$specialThemes)) {
                $themes[$k][] = $v;
            }
        }
    }

    $Slim->render(
        'configContent.tpl',
        array(
            'client' => $client,
            'user' => $user,
            'serviceData' => $appCfg['service'],
            'defaultTextSize' => $defaultTextSize,
            'defaultTheme' => $defaultTheme,
            'themes' => $themes,
            'appVersion' => $appCfg['front']['versioning']['application'],
            'appInfo' => $appInfo,
            'canShowTheme' => !Hp_Browser::isBlackberry()
        )
    );
});
