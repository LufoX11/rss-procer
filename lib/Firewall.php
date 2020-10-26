<?php

/**
 * Firewall functions.
 */

class Firewall
{
    /**
     * @const MC keys.
     */
    const MC_KEY_ID = 'Firewall::id-%s::1';

    /**
     * @var array Configuration.
     */
    protected $_cfg;

    /**
     * @var string IP / Device for the checkings.
     */
    protected $_id;

    /**
     * @var object Cache instance.
     */
    protected $_cache;

    /**
     * Build a new Firewall object.
     *
     * @param $cfg array Configuration for the firewall:
     *                   'floodCount' integer Max requests (in the floodTime period) to consider as attack.
     *                   'floodTime' integer Time (in secs) to live for the request cache.
     *                   'banTime' integer Time (in msec) to ban an attacker.
     *                   'blacklist' string Not allowed IPs / Devices (comma separated).
     *                   'whitelist' string Allowed IPs / Devices (comma separated).
     * @param $options array Firewall options:
     *                       'cache' object Cache instance.
     *                       'id' string IP / Device to validate.
     * @return void.
     */
    public function __construct(array $cfg, array $options = null)
    {
        $this->_cfg = $cfg;
        if (isset($options['cache'])) {
            $this->_cache = $options['cache'];
        }
        if (isset($options['id'])) {
            $this->_id = $options['id'];
        }
    }

    /**
     * Checks whether the IP / Device is blacklisted or not.
     *
     * @param $id string IP / Device to check.
     * @return boolean TRUE if it's in the blacklist; FALSE otherwise.
     */
    public function isBlacklisted($id = null)
    {
        if (!$id) {
            $id = $this->_id;
        }
        $res = in_array($id, explode(',', $this->_cfg['blacklist']));

        return $res;
    }

    /**
     * Checks whether the IP / Device is whitelisted or not.
     *
     * @param $id string IP / Device to check.
     * @return boolean TRUE if it's in the whitelist; FALSE otherwise.
     */
    public function isWhitelisted($id = null)
    {
        if (!$id) {
            $id = $this->_id;
        }
        $res = in_array($id, explode(',', $this->_cfg['whitelist']));

        return $res;
    }

    /**
     * Checks whether the IP / Device is performing a flood attack.
     *
     * @param $id string IP / Device to check.
     * @return boolean TRUE if flooding detected; FALSE otherwise.
     */
    public function isFlooding($id = null)
    {
        if (!$id) {
            $id = $this->_id;
        }
        $mcKey = sprintf(self::MC_KEY_ID, $id);
        $res = false;
        if ($idObject = $this->_cache->get($mcKey)) {
            if ($idObject != 'flood') {
                if ($res = $idObject >= $this->_cfg['floodCount']) {
                    $this->_cache->set($mcKey, 'flood', $this->_cfg['banTime']);
                } else {
                    $this->_cache->increment($mcKey);
                }
            } else {
                $res = true;
            }
        } else {
            $this->_cache->set($mcKey, 1, $this->_cfg['floodTime']);
        }

        return $res;
    }
}
