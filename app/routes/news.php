<?php

/**
 * Renders the application interface for a news.
 */

$Slim->get('/:source/:deviceId/:deviceType/:deviceVersion/news/:newsChecksum',
    $mdwDeviceGuessing,
    $mdwCache(),
    function ($source, $deviceId, $deviceType, $deviceVersion, $newsChecksum) use ($Slim)
{
    Loader::mo(array('NewsMapper'), true);

    // User session
    $user = Mo_UsersMapper::sessionUp($source, $deviceId, $deviceType, $deviceVersion);

    // News data
    $appCfg = $Slim->config('appCfg');
    $topics = Mo_NewsMapper::fetchChannels($appCfg['service']['name']);
    if ($news = Mo_NewsMapper::findNewsByChecksum($newsChecksum, $appCfg['service']['name'])) {

        // Share buttons
        $shareUrls = array(
            'facebook' => 'http://m.facebook.com/sharer.php?u=' . urlencode($news->shortlink),
            'twitter' => 'http://mobile.twitter.com/home?status='
                . urlencode(mb_substr($news->title, 0, 100) . '... ' . $news->shortlink . ' #RSSProcer')
        );
    } else {
        $Slim->flashNow('error', Utils::escape('La noticia no está disponible.'));
    }

    $Slim->render(
        'newsContent.tpl',
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
            'topic' => (isset($news) ? $topics[$news->channels_id] : null),
            'shareUrls' => (isset($shareUrls) ? $shareUrls : array()),
            'src' => $Slim->request()->get('src')
        )
    );
});
