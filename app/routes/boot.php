<?php

/**
 * Renders the application interface for Home (default).
 */

$Slim->get('/:source/:deviceId/:deviceType/:deviceVersion',
    $mdwDeviceGuessing,
    $mdwCache(),
    function ($source, $deviceId, $deviceType, $deviceVersion) use ($Slim)
{
    $appCfg = $Slim->config('appCfg');
    $Hp_View = new Hp_View(array('Path'), $appCfg);

    // Service data
    $serviceData = array();
    $serviceData['loaderImg'] = (isset($appCfg['service']['loaderImage']) ?
        $Hp_View->getImg("services/{$appCfg['service']['name']}/{$appCfg['service']['loaderImage']}") :
        $Hp_View->getImg("themes://rssprocer-thumb.png"));
    $serviceData['loaderText'] = (isset($appCfg['service']['loaderText']) ?
        $appCfg['service']['loaderText'] : null);
    $serviceData['pageTitle'] = $appCfg['service']['pageTitle'];
    $serviceData['pageDescription'] = $appCfg['service']['pageDescription'];
    $serviceData['favicon'] = $appCfg['service']['favicon'];

    // Render options
    View_Renderer::setPanel(null);
    View_Renderer::setHeader(null);
    View_Renderer::setLayout('bootLayout.tpl');

    $Slim->render(
        'boot.tpl',
        array(
            'client' => new Mo_Client(array(
                'source' => $source,
                'deviceId' => $deviceId,
                'deviceType' => $deviceType,
                'deviceVersion' => $deviceVersion
            )),
            'serviceData' => $serviceData,
            'requestedUri' => $Hp_View->getLink('mainMenuHome'),
            'iframeUri' => $Hp_View->getLink('mainMenuHome') . '?boot=1'
        )
    );
});
