<?php

/**
 * AJAX responser for application actions.
 */

$Slim->get('/:source/:deviceId/:deviceType/:deviceVersion/ajax/application/:action',
    $mdwDeviceGuessing,
    function ($source, $deviceId, $deviceType, $deviceVersion, $action) use ($Slim)
{
    $appCfg = $Slim->config('appCfg');

    switch ($action) {
        case 'getAppVersion':
            Ajax::setSuccess($Slim, array('appVersion' => $appCfg['front']['versioning']['application']));
            break;

        case 'getCurrentTextSizes':
            $textSizes = '';
            foreach (Mo_UserSetting::$textSizeSelectors as $k => $v) {
                $textSizes .= "{$k}{font-size:" . ($v + (int) $Slim->request()->get('size')) . 'px;}';
            }
            Ajax::setSuccess($Slim, array('data' => $textSizes));
            break;

        default:
            Ajax::setBadRequest($Slim, array(sprintf(Utils::escape('Faltan datos: "%s".'), 'action')));
    }
});
