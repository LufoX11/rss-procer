<?php

/**
 * Abstract model.
 */

Loader::da(array('Handler/Memcached', 'Handler/Mysql', 'Handler/Mongo'), true);

abstract class Mo_Abstract
{
    /**
     * @var array Configuration for the db handler.
     */
    protected $_cfg;

    /**
     * @var object Database handler.
     */
    protected $_dbHandler;

    /**
     * @var object Cache handler.
     */
    protected $_cacheHandler;

    /**
     * Builds a new mapper.
     *
     * @param $cfg array Configuration for the db handler.
     * @return void.
     */
    public function __construct(array $cfg)
    {
        $this->_cfg = $cfg;
    }

    /**
     * Gets the current db handler.
     *
     * @return object Db handler.
     */
    public function getDbHandler()
    {
        if (!$this->_dbHandler) {
            $this->setDbHandler('Da_Handler_Mysql');
        }

        return $this->_dbHandler;
    }

    /**
     * Gets the current cache handler.
     *
     * @return object Cache handler.
     */
    public function getCacheHandler()
    {
        if (!$this->_cacheHandler) {
            $this->setCacheHandler('Da_Handler_Memcached');
        }

        return $this->_cacheHandler;
    }

    /**
     * Sets the database handler.
     *
     * @param $dbHandler mixed The name of the db handler or the already initializated handler.
     * @return object Self object.
     */
    public function setDbHandler($dbHandler)
    {
        if (is_string($dbHandler)) {
            $dbHandler = new $dbHandler($this->_cfg);
        }
        if (!$dbHandler instanceof Da_Handler_Mysql
                && !$dbHandler instanceof Da_Handler_Mongo) {
            throw new Exception('Invalid database handler provided.');
        }
        $this->_dbHandler = $dbHandler;

        return $this;
    }

    /**
     * Sets the cache handler.
     *
     * @param $cacheHandler mixed The name of the cache handler or the already initializated handler.
     * @return object Self object.
     */
    public function setCacheHandler($cacheHandler)
    {
        if (is_string($cacheHandler)) {
            $cacheHandler = new $cacheHandler($this->_cfg);
        }
        if (!$cacheHandler instanceof Da_Handler_Memcached) {
            throw new Exception('Invalid cache handler provided.');
        }
        $this->_cacheHandler = $cacheHandler;

        return $this;
    }
}
