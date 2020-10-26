<?php

/**
 * Tracking object model.
 */

Loader::mo(array('Base', 'TrackingMapper'), true);

class Mo_Tracking extends Mo_Base
{
    /**
     * @var array Available tracking actions.
     */
    protected $_actions = array('click', 'open');

    /**
     * @var string Service name.
     */
    protected $_service;

    /**
     * @var string Action source.
     */
    protected $_source;

    /**
     * @var string Action type.
     */
    protected $_action;

    /**
     * @var string Additional data..
     */
    protected $_extra;

    /**
     * @var string Device ID.
     */
    protected $_deviceid;

    /**
     * @var string Device type.
     */
    protected $_devicetype;

    /**
     * @var string Device version.
     */
    protected $_deviceversion;

    /**
     * @var string Application version.
     */
    protected $_appversion;

    /**
     * @var string Creation date and time.
     */
    protected $_timestamp;

    ////
    // Setters and Getters
    ////

    public function setService($service)
    {
        $this->_service = (string) $service;
    }

    public function getService()
    {
        return $this->_service;
    }

    public function setSource($source)
    {
        $this->_source = (string) $source;
    }

    public function getSource()
    {
        return $this->_source;
    }

    public function setAction($action)
    {
        if (!in_array($action, $this->_actions)) {
            throw new Exception('Invalid action.');
        }
        $this->_action = (string) $action;
    }

    public function getAction()
    {
        return $this->_action;
    }

    public function setExtra($extra)
    {
        $this->_extra = (string) $extra;
    }

    public function getExtra()
    {
        return $this->_extra;
    }

    public function setDeviceid($deviceid)
    {
        $this->_deviceid = (string) $deviceid;
    }

    public function getDeviceid()
    {
        return $this->_deviceid;
    }

    public function setDevicetype($devicetype)
    {
        $this->_devicetype = (string) $devicetype;
    }

    public function getDevicetype()
    {
        return $this->_devicetype;
    }

    public function setDeviceversion($deviceversion)
    {
        $this->_deviceversion = (string) $deviceversion;
    }

    public function getDeviceversion()
    {
        return $this->_deviceversion;
    }

    public function setAppversion($appversion)
    {
        $this->_appversion = (string) $appversion;
    }

    public function getAppversion()
    {
        return $this->_appversion;
    }

    public function setTimestamp($timestamp)
    {
        if (!$timestamp instanceof MongoDate) {
            throw new Exception('"timestamp" must be an instance of MongoDate.');
        }
        $this->_timestamp = $timestamp;
    }

    public function getTimestamp()
    {
        return $this->_timestamp;
    }
}
