<?php

/**
 * This class contains functions for manipulating the templates cache.
 */

Loader::mo(array('Abstract'), true);

class Mo_FrontCache extends Mo_Abstract
{
    /**
     * @const MC keys.
     */
    const MC_KEY_EXPIRATION = 1800; // 60 * 30 (30 mins)
    const MC_KEY = 'Mo_FrontCache::1';
    const MC_KEY_BY_SERVICE = 'Mo_FrontCache::service-%s::1';

    /**
     * Increments the cache.
     *
     * @param $service string Service name.
     * @return string Current session version.
     */
    public static function increment($service = null)
    {
        $key = self::getKey($service);
        if (!self::get($service)) {
            self::store($service, 1);
        }
        $res = Da_Handler_Memcached::increment($key);

        return $res;
    }

    /**
     * Gets the user cache for frontend.
     *
     * @param $service string Service name.
     * @return mixed Current session version or FALSE.
     */
    public static function get($service = null)
    {
        if (($res = Da_Handler_Memcached::fetch(self::getKey($service))) === null) {
            self::store($service, 1);
        }

        return $res;
    }

    /**
     * Stores a value.
     *
     * @param $service string Service name.
     * @return boolean TRUE on success; FALSE otherwise.
     */
    protected static function store($service, $val)
    {
        $res = Da_Handler_Memcached::store(self::getKey($service), $val, self::MC_KEY_EXPIRATION);

        return $res;
    }

    /**
     * Gets the cache key.
     *
     * @param $service string Service name.
     * @return string Cache key.
     */
    protected static function getKey($service = null)
    {
        if ($service) {
            $res = sprintf(self::MC_KEY_BY_SERVICE, $service);
        } else {
            $res = self::MC_KEY;
        }

        return $res;
    }
}
