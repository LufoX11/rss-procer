<?php

/**
 * Renders the application interface for a topic (channel).
 */

$Slim->get('/:source/:deviceId/:deviceType/:deviceVersion/topic/:topicId',
    $mdwDeviceGuessing,
    $mdwCache(),
    function ($source, $deviceId, $deviceType, $deviceVersion, $topicId) use ($Slim)
{
    Loader::mo(array('NewsMapper'), true);

    // User session
    $user = Mo_UsersMapper::sessionUp($source, $deviceId, $deviceType, $deviceVersion);

    // News data
    $appCfg = $Slim->config('appCfg');
    if (!($topics = Mo_NewsMapper::fetchChannels($appCfg['service']['name']))) {
        $topics = array();
    }
    if (!($news = Mo_NewsMapper::fetchNews($topicId, $appCfg['service']['name']))) {
        $news = array();
    }
    $newsForPreload = array();
    array_walk($news, function ($v) use (&$newsForPreload) {
        $newsForPreload[] = '"news/' . $v->checksum . '"';
    });
    $newsForPreload = implode(', ', $newsForPreload);

    // Read news
    $readNews = ($user->getSetting(Mo_UserSetting::READ_NEWS) ?
        $user->getSetting(Mo_UserSetting::READ_NEWS) : array());

    $Slim->render(
        'topicContent.tpl',
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
            'newsForPreload' => $newsForPreload,
            'topics' => $topics,
            'readNews' => $readNews
        )
    );
});
