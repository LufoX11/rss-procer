<?php

/**
 * This is the Memcached connector driver.
 */

class MemcachedDriver extends Memcached
{
    /**
     * Connects to Memcached servers.
     *
     * @param $cfg array A collection of arrays containing 'host', 'port' and 'weight' data.
     * @return void.
     */
    public function __construct(array $cfg)
    {
        parent::__construct();
        $this->addServers($cfg);
        $this->setOption(self::OPT_LIBKETAMA_COMPATIBLE, true);
    }
}
