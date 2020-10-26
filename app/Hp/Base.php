<?php

/**
 * Base object and configuration for Helpers.
 */

abstract class Hp_Base
{
    /**
     * @var array Configuration file (INI file).
     */
    protected $_cfg;

    /**
     * Builds a new Helper object.
     *
     * @param $cfg array Configuration required by the Helper.
     * @return void.
     */
    public function __construct(array $cfg = null)
    {
        if ($cfg) {
            $this->setCfg($cfg);
        }
    }

    /**
     * Sets the configuration required for the helper to use.
     *
     * @param $cfg array Configuration required by the Helper.
     * @return void.
     */
    public function setCfg(array $cfg)
    {
        $this->_cfg = $cfg;
    }
}
