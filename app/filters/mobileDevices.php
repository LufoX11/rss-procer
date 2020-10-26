<?php

/**
 * Filter rules for mobile devices (default).
 */

// Extra filtering rules for URI params
// The "guess" keyword is allowed to let mdwDeviceGuessing to choose the proper device type
Slim\Route::setDefaultConditions(array(
    'source' => '(' . implode('|', $appCfg['main']['services']) . ')',
    'deviceType' => '(app|guess|' . implode('|', Mo_Client::getDeviceTypes()) . ')'
));
