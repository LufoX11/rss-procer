<?php

/**
 * AJAX responser for Tracking post action.
 */

$Slim->post('/tracking/post', function () use ($Slim)
{
    $data = $Slim->request()->post('data');
    $Mo_TrackingMapper = new Mo_TrackingMapper($Slim->config('appCfg'));
    $trackingData = new Mo_Tracking(array(
        'service' => (isset($data['service']) ? $data['service'] : null),
        'source' => (isset($data['source']) ? $data['source'] : null),
        'action' => (isset($data['action']) ? $data['action'] : 'open'),
        'extra' => (isset($data['extra']) ? $data['extra'] : null),
        'deviceid' => (isset($data['deviceId']) ? $data['deviceId'] : null),
        'devicetype' => (isset($data['deviceType']) ? $data['deviceType'] : null),
        'deviceversion' => (isset($data['deviceVersion']) ? $data['deviceVersion'] : null),
        'appversion' => (isset($data['appVersion']) ? $data['appVersion'] : null),
        'timestamp' => new MongoDate()
    ));
    $Mo_TrackingMapper->save($trackingData);
});
