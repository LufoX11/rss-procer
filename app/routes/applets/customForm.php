<?php

/**
 * Renders the application interface for the applet: CustomForm.
 */

$Slim->get('/:source/:deviceId/:deviceType/:deviceVersion/applets/customForm',
    $mdwDeviceGuessing,
    $mdwCache(),
    function ($source, $deviceId, $deviceType, $deviceVersion) use ($Slim, $appCfg)
{
    $Hp_View = new Hp_View(array('Path'), $appCfg);

    // User session
    $user = Mo_UsersMapper::sessionUp($source, $deviceId, $deviceType, $deviceVersion);

    $applet = (isset($appCfg['applets']['customForm']) ? $appCfg['applets']['customForm'] : null);
    if (!$applet || !$appCfg['applets']['customForm']['enabled']) {
        $Slim->redirect($Hp_View->getLink('more'));
    }
    $appletFields = array();
    $formData = array();
    for ($i = 0; $i <= count($applet); $i ++) {
        if (!isset($applet["field_{$i}"])) {
            continue;
        }
        list($appletFields[$i]['type'], $appletFields[$i]['name'], $appletFields[$i]['title'],
            $appletFields[$i]['description'], $appletFields[$i]['required']) = explode('|', $applet["field_{$i}"]);

        // Form default data (empty)
        $formData[$appletFields[$i]['name']] = '';
    }

    $Slim->render(
        'applets/customFormContent.tpl',
        array(
            'client' => new Mo_Client(array(
                'source' => $source,
                'deviceId' => $deviceId,
                'deviceType' => $deviceType,
                'deviceVersion' => $deviceVersion
            )),
            'user' => $user,
            'serviceData' => $appCfg['service'],
            'jqmCache' => false,
            'applet' => $applet,
            'appletFields' => $appletFields,
            'formData' => $formData
        )
    );
});
