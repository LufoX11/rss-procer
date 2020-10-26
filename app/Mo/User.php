<?php

/**
 * User object model.
 */

Loader::mo(array('Base', 'UserSetting'), true);

class Mo_User extends Mo_Base
{
    /**
     * @var integer User ID.
     */
    protected $_id;

    /**
     * @var string A unique ID to identify the client.
     */
    protected $_deviceId;

    /**
     * @var string The type of the device from which the request is being made.
     */
    protected $_deviceType;

    /**
     * @var string The device version.
     */
    protected $_deviceVersion;

    /**
     * @var string Creation date.
     */
    protected $_timestamp;

    /**
     * @var array A collection of UserSetting objects.
     */
    protected $_settings;

    /**
     * Retrieves a value from settings.
     *
     * @param $settingKey string Setting key.
     * @return mixed Setting value on success; NULL otherwise.
     */
    public function getSetting($settingKey)
    {
        $res = null;
        if (array_key_exists($settingKey, $this->settings)) {
            $res = $this->settings[$settingKey]->value;
        }

        return $res;
    }

    ////
    // Setters and Getters
    ////

    public function setId($id)
    {
        $this->_id = (integer) $id;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setDeviceId($id)
    {
        $this->_deviceId = (string) $id;
    }

    public function getDeviceId()
    {
        return $this->_deviceId;
    }

    public function setDeviceType($type)
    {
        $this->_deviceType = (string) $type;
    }

    public function getDeviceType()
    {
        return $this->_deviceType;
    }

    public function setDeviceVersion($version)
    {
        $this->_deviceVersion = (string) $version;
    }

    public function getDeviceVersion()
    {
        return $this->_deviceVersion;
    }

    public function setTimestamp($timestamp)
    {
        $this->_timestamp = (string) $timestamp;
    }

    public function getTimestamp()
    {
        return $this->_timestamp;
    }

    public function setSettings(array $settings)
    {
        $this->_settings = $settings;
    }

    public function getSettings()
    {
        return $this->_settings;
    }
}
