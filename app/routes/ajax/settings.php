<?php

/**
 * AJAX responser for user settings actions.
 */

$Slim->post('/:source/:deviceId/:deviceType/:deviceVersion/ajax/user/settings/:action(/:key)',
    $mdwDeviceGuessing,
    function ($source, $deviceId, $deviceType, $deviceVersion, $action, $key) use ($Slim)
{
    // User session
    $user = Mo_UsersMapper::sessionUp($source, $deviceId, $deviceType, $deviceVersion);

    if (isset($key) && !in_array($key, Mo_UserSetting::$keys)) {
        Ajax::setError($Slim, array(Utils::escape('Dato erróneo: "key".')));
        return;
    }
    switch ($action) {
        case 'insert':
            $setting = new Mo_UserSetting(array(
                'users_id' => $user->id,
                'key' => $key,
                'value' => $Slim->request()->post('data')
            ));
            if (Mo_UsersMapper::insertSetting($source, $user, $setting)) {
                Ajax::setSuccess($Slim, array(
                    'msg' => Utils::escape('Los datos se guardaron.'),
                    'data' => $Slim->request()->post('data')
                ));
            } else {
                Ajax::setError($Slim);
            }
            break;

        default:
            Ajax::setBadRequest($Slim, array(Utils::escape('Faltan datos: "action".')));
    }
});
