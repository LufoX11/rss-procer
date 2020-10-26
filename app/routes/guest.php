<?php

/**
 * Renders the application interface for Home (default).
 */

$Slim->map('/(:source)',
    $mdwDeviceGuessing,
    function ($source = null) use ($Slim)
{
    if ($source || $Slim->request()->post('service')) {

        // Set a cookie to avoid doing some app branding actions (like showing "cambiar de servicio")
        if ($Slim->request()->post('loginType') == 'rssprocer') {
            $Slim->setEncryptedCookie('loginType', 'rssprocer');
        } else {
            $Slim->setEncryptedCookie('loginType', 'customer');
        }

        // Do login
        $service = ($source ? $source : $Slim->request()->post('service'));
        if ($Slim->request()->post('deviceId')
            && $Slim->request()->post('deviceType')
            && $Slim->request()->post('deviceVersion'))
        {
            $deviceId = $Slim->request()->post('deviceId');
            $deviceType = $Slim->request()->post('deviceType');
            $deviceVersion = $Slim->request()->post('deviceVersion');
        } else {
            $deviceType = Hp_Browser::getDeviceType();
            $deviceId = Mo_UsersMapper::fetchDeviceDataFromCookies('deviceId');
            $deviceVersion = 'unknown';
        }
        $Slim->redirect("/{$service}/{$deviceId}/{$deviceType}/{$deviceVersion}");
    } else {

        ////
        // IMPORTANT: If you modify the following block, you should also check appLogin.php
        // Debug URL: /app/123456/bb/6
        ////

        Loader::Hp(array('Name'));

        // Retrieve user session from cookies
        $deviceId = $deviceType = $deviceVersion = null;
        if ($client = Mo_UsersMapper::fetchDeviceDataFromCookies()) {
            $deviceId = $client->deviceId;
            $deviceType = $client->deviceType;
            $deviceVersion = $client->deviceVersion;
        }

        // Let the user choose the service
        $appCfg = $Slim->config('appCfg');
        $services = array_flip(Hp_Name::fetchServicesTypes());
        array_walk($services, function (&$v) { $v = array(); });
        $Hp_View = new Hp_View(array('Path'), $appCfg);
        foreach ($appCfg['main']['services'] as $v) {
            $services[Hp_Name::getServiceTypeCoolName($appCfg['services'][$v]['type'])][] = array(
                'id' => $v,
                'title' => "{$appCfg['services'][$v]['title']} - {$appCfg['services'][$v]['scopeDescription']}",
                'suggestedBy' => (isset($appCfg['services'][$v]['suggestedBy']) ?
                    $appCfg['services'][$v]['suggestedBy'] : null),
                'icon' => $Hp_View->getImg("services/{$v}/icon.png")
            );
        }
        $services = array_filter($services); // Remove empty categories
    }

    // Render options
    View_Renderer::setPanel(null);
    View_Renderer::setHeader('guestHeader.tpl');
    View_Renderer::setLayout('guestLayout.tpl');

    $Slim->render(
        'guestLoginContent.tpl',
        array(
            'client' => $client,
            'services' => $services,
            'deviceId' => $deviceId,
            'deviceType' => $deviceType,
            'deviceVersion' => $deviceVersion,
            'avoidBackButton' => true
        )
    );
})->via('GET', 'POST');
