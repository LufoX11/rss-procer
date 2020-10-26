<?php

/**
 * Memcached handler.
 */

Loader::lib(array('MemcachedDriver'), true);

class Da_Handler_MemcachedException extends Exception {}

class Da_Handler_Memcached
{
    /**
     * @var string Default driver to use.
     */
    public static $defaultDriver = 'memcached';

    /**
     * @var array Configuration for the handler.
     */
    protected $_cfg;

    /**
     * @var object Memcached handler.
     */
    private static $_handler;

    /**
     * Builds a new handler.
     *
     * @param $cfg array Configuration for the handler.
     * @return void.
     */
    public function __construct(array $cfg)
    {
        $this->_cfg = $cfg;
    }

    /**
     * Sets the cache driver to use.
     *
     * @param $driver string Driver name.
     * @return object Self object.
     */
    public function setCacheDriver($driver)
    {
        // TODO: Change to use $this->defaultDriver when code migration is done
        self::$defaultDriver = $driver;

        return $this;
    }

    /**
     * Gets the cache driver.
     *
     * @return string Cache driver.
     */
    public function getCacheDriver()
    {
        if (!$this->defaultDriver) {
            $this->setCacheDriver('memcached');
        }

        return $this->defaultDriver;
    }

    /**
     * Fetchs a stored value from cache.
     *
     * @param $key string The key used to store the value.
     * @return mixed The stored value on success; NULL otherwise.
     */
    public static function fetch($key)
    {
        $handler = self::getHandler();
        $res = $handler->get($key);
        if ($errorCode = $handler->getResultCode()) {
            $res = null;
        }

        return $res;
    }

    /**
     * Stores a value in cache.
     *
     * @param $key string Data identifier label.
     * @param $value mixed Value to store.
     * @param $ttl integer Time to live in memory in seconds (0 means forever).
     * @return boolean TRUE on success; FALSE otherwise.
     */
    public static function store($key, $value, $ttl = 0)
    {
        $handler = self::getHandler();
        $res = $handler->set($key, $value, $ttl);
        if ($errorCode = $handler->getResultCode()) {
            $res = false;
        }

        return $res;
    }

    /**
     * Removes a stored variable from cache.
     *
     * @param $key string Data identifier label.
     * @param $ttl integer Time to live in memory before you be able to use this key again (0 means forever).
     * @return boolean TRUE on success; FALSE otherwise.
     */
    public static function delete($key, $ttl = 0)
    {
        $handler = self::getHandler();
        $res = $handler->delete($key);
        if ($errorCode = $handler->getResultCode()) {
            $res = false;
        }

        return $res;
    }

    /**
     * Increments numeric item's value.
     *
     * @param $key string The key of the item to increment. 
     * @param $offset The amount by which to increment the item's value.
     * @return boolean TRUE on success; FALSE otherwise.
     */
    public static function increment($key, $offset = 1)
    {
        $handler = self::getHandler();
        $res = $handler->increment($key, $offset);
        if ($errorCode = $handler->getResultCode()) {
            $res = false;
        }

        return $res;
    }

    /**
     * Decrements numeric item's value.
     *
     * @param $key string The key of the item to decrement. 
     * @param $offset The amount by which to decrement the item's value.
     * @return boolean TRUE on success; FALSE otherwise.
     */
    public static function decrement($key, $offset = 1)
    {
        $handler = self::getHandler();
        $res = $handler->decrement($key, $offset);
        if ($errorCode = $handler->getResultCode()) {
            $res = false;
        }

        return $res;
    }

    /**
     * Gets connection handler.
     *
     * @param $driver string Cache driver to use.
     * @return object Connection handler.
     */
    public static function getHandler($driver = null)
    {
        global $appCfg;

        // TODO: Once all models were migrated to use this object as instance, we should:
        // 1) Delete global $appCfg and use the config read by $this->_cfg
        // 2) Change all methods to be "public function" instead of "public static function"
        // 3) Replace every "self::" calls with "$this->" and delete the "isset($this)" logic
        if (isset($this) && $this->_cfg) {
            $cfg = $this->_cfg;
        } else {
            $cfg = $appCfg;
        }
        if (!$driver) {
            if (isset($this)) {
                $driver = $this->getCacheDriver();
            } else {
                $driver = self::$defaultDriver;
            }
        }
        if (!$cfg) {
            throw new Da_Handler_MemcachedException('$cfg not initializated.');
        }
        if (!isset(self::$_handler)) {
            self::$_handler = new MemcachedDriver($cfg['cache'][$driver]);
        }

        return self::$_handler;
    }
}
