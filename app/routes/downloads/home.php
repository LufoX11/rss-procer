<?php

/**
 * Renders the application interface for Home (default).
 */

$Slim->get('/(home)', function () use ($Slim)
{
    if (Hp_Browser::getDeviceType() != ImobileDetector::DEVICE_TYPE_GENERIC) {
        if (Hp_Browser::isTablet()) {
            $Slim->redirect('/tablet?q=' . $Slim->request()->get('q'));
        } else {
            $Slim->redirect('/smartphone?q=' . $Slim->request()->get('q'));
        }
    }

    $Slim->render(
        'homeContent.tpl',
        array(
            'q' => $Slim->request()->get('q')
        )
    );
});
