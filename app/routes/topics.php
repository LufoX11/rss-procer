<?php

/**
 * Renders the application interface for Topics (channels).
 */

$Slim->get('/:source/:deviceId/:deviceType/:deviceVersion/topics',
    $mdwDeviceGuessing,
    $mdwCache(),
    function ($source, $deviceId, $deviceType, $deviceVersion) use ($Slim)
{
    Loader::mo(array('NewsMapper'), true);

    // User session
    $user = Mo_UsersMapper::sessionUp($source, $deviceId, $deviceType, $deviceVersion);

    // Topics data
    $appCfg = $Slim->config('appCfg');
    $news = array();
    if ($topics = Mo_NewsMapper::fetchChannels($appCfg['service']['name'])) {
        foreach ($topics as $k => $v) {
            if (!($news[$k] = Mo_NewsMapper::fetchNews($k, $appCfg['service']['name']))) {
                unset($topics[$k]);
            }
        }
    } else {
        $topics = array();
    }

    $Slim->render(
        'topicsContent.tpl',
        array(
            'client' => new Mo_Client(array(
                'source' => $source,
                'deviceId' => $deviceId,
                'deviceType' => $deviceType,
                'deviceVersion' => $deviceVersion
            )),
            'user' => $user,
            'serviceData' => $appCfg['service'],
            'news' => $news,
            'topics' => $topics
        )
    );
});
