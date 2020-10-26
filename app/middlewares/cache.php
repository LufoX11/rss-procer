<?php

/**
 * Cache middleware layer for frontend.
 *
 * Preferred method: ETAG
 * We'll use URL + user session version + service front version + app front version + app version
 * Expiration time is only 1 sec by default because we want the browser to check every time if it has
 * to call the page, but as the etag won't change, it will retrieve the page from local cache.
 */

$mdwCache = function ($expiration = '+1 sec') use ($Slim)
{
    return function () use ($expiration, $Slim)
    {
        $appCfg = $Slim->config('appCfg');
        $routeName = $Slim->router()->getCurrentRoute()->getName();

        if ($appCfg['main']['framework']['pagesCache']) {

            // Guest mode by default
            $url = 'guest';
            $userVersion = '1';
            $serviceFrontVersion = '1';
			$appFrontVersion = Mo_FrontCache::get();
            $currentUrlParts = array_filter(explode('/', current(explode('?', $_SERVER['REQUEST_URI']))));
            if (count($currentUrlParts) >= 4) {

                // Normal mode
                $url = current(explode('?', $_SERVER['REQUEST_URI']));
                list($source, $deviceId, $deviceType, $deviceVersion) =
                    array_values(array_filter(explode('/', $url)));
                $user = Mo_UsersMapper::sessionUp($source, $deviceId, $deviceType, $deviceVersion);
                $userVersion = Mo_UsersMapper::cacheSessionGet($source, $user);
                $serviceFrontVersion = Mo_FrontCache::get($source);
                if ($serviceFrontVersion === false || $appFrontVersion === false) {
                    $userVersion = Mo_UsersMapper::cacheSessionIncrement($source, $user);
                }
            }
            $appVersion = $appCfg['front']['versioning']['application'];

            // Exceptions (pages that must be always revalidated)
            $exception = '';
            switch ($routeName) {
                case 'more':
                    $exception = md5(microtime() . mt_getrandmax());
                    break;
            }

            $Slim->etag(sha1("{$url}{$userVersion}{$serviceFrontVersion}{$appFrontVersion}{$appVersion}{$exception}"));
            $Slim->expires($expiration);
        }
    };
};
