<?php

/**
 * AJAX responser for news actions.
 */

$Slim->map('/:source/:deviceId/:deviceType/:deviceVersion/ajax/news/:action',
    $mdwDeviceGuessing,
    function ($source, $deviceId, $deviceType, $deviceVersion, $action) use ($Slim)
{
    // User session
    $user = Mo_UsersMapper::sessionUp($source, $deviceId, $deviceType, $deviceVersion);

    switch ($action) {
        case 'setReadNews':
            $readNews = ($user->getSetting(Mo_UserSetting::READ_NEWS) ?
                $user->getSetting(Mo_UserSetting::READ_NEWS) : array());
            if ($newsChecksum = $Slim->request()->post('data')) {
                if (!in_array($newsChecksum, $readNews)) {
                    $readNews[] = $newsChecksum;
                    $setting = new Mo_UserSetting(array(
                        'users_id' => $user->id,
                        'key' => Mo_UserSetting::READ_NEWS,
                        'value' => $readNews
                    ));
                    Mo_UsersMapper::insertSetting($source, $user, $setting);
                }

                Ajax::setSuccess($Slim, array(
                    'msg' => Utils::escape('Los datos se guardaron.')
                ));
            } else {
                Ajax::setBadRequest($Slim, array(sprintf(Utils::escape('Faltan datos: "%s".'), 'data')));
            }
            break;

        default:
            Ajax::setBadRequest($Slim, array(sprintf(Utils::escape('Faltan datos: "%s".'), 'action')));
    }
})->via('GET', 'POST');
