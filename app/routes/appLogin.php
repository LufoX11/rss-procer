<?php

/**
 * Save session data for users from mobile application.
 */

$Slim->get('/app/:deviceId/:deviceType/:deviceVersion',
    $mdwDeviceGuessing,
    function ($deviceId, $deviceType, $deviceVersion) use ($Slim)
{
    Loader::Hp(array('Name'));

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

    // Render options
    View_Renderer::setPanel(null);
    View_Renderer::setHeader('guestHeader.tpl');
    View_Renderer::setLayout('guestLayout.tpl');

    $Slim->render(
        'guestLoginContent.tpl',
        array(
            'client' => new Mo_Client(array(
                'deviceId' => $deviceId,
                'deviceType' => $deviceType,
                'deviceVersion' => $deviceVersion
            )),
            'services' => $services,
            'deviceId' => $deviceId,
            'deviceType' => $deviceType,
            'deviceVersion' => $deviceVersion,
            'avoidBackButton' => true
        )
    );
});
