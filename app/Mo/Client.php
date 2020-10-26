<?php

/**
 * Client object model.
 */

Loader::mo(array('Base'), true);

class Mo_ClientException extends Exception {}

class Mo_Client extends Mo_Base
{
    /**
     * Brands.
     */
    const APPLE = 'apple';
    const RIM = 'rim';
    const GOOGLE = 'google';
    const GENERIC = 'genérico';

    /**
     * @var array Device types.
     */
    protected static $_deviceTypes = array(
        self::APPLE => array(
            'iphone' => 'ih',
            'ipod' => 'io',
            'ipad' => 'ia'
        ),
        self::RIM => array(
            'blackberry' => 'bb',
            'playbook' => 'pb'
        ),
        self::GOOGLE => array(
            'android' => 'aa',
            'android_tablet' => 'at'
        ),
        self::GENERIC => array(
            'genérico' => 'gn'
        )
    );

    /**
     * @var array Device categories.
     */
    protected static $_deviceCategories = array(
        'smartphones' => array('ih', 'io', 'bb', 'aa', 'gn'),
        'tablets' => array('ia', 'pb', 'at')
    );

    /**
     * @var string The service source font.
     */
    protected $_source;

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
     * @var string The operative system's brand.
     */
    protected $_osBrand;

    /**
     * @var string The device category.
     */
    protected $_deviceCategory;

    /**
     * @see Mo_Base
     */
    public function __construct(array $values)
    {
        if (array_key_exists('deviceType', $values)) {
            foreach (self::$_deviceTypes as $k => $v) {
                if (in_array($values['deviceType'], $v)) {
                    $this->_osBrand = $k;
                    break;
                }
            }
        }

        parent::__construct($values);
    }

    /**
     * Gets device types.
     *
     * @return array Device types.
     */
    public static function getDeviceTypes()
    {
        $res = array();
        array_walk_recursive(self::$_deviceTypes, function($v, $k) use(&$res) {
            $res[$k] = $v;
        });

        return $res;
    }

    /**
     * Gets the device category.
     *
     * @param $deviceType string Device type.
     * @return string Device category: "browser", "smartphone" or "tablet".
     */
    public static function getDeviceCategory($deviceType)
    {
        $res = 'browser';
        if (in_array($deviceType, self::$_deviceCategories['smartphones'])) {
            $res = 'smartphone';
        } else if (in_array($deviceType, self::$_deviceCategories['tablets'])) {
            $res = 'tablet';
        }

        return $res;
    }

    /**
     * Gets the device type commercial name.
     *
     * @param $deviceType string Device type.
     * @return string Device name: "blackberry", "ipod", etc.
     */
    public static function getDeviceTypeName($deviceType)
    {
        $res = 'unknown';
        foreach (self::$_deviceTypes as $v) {
            if (($res = array_search($deviceType, $v)) !== false) {
                break;
            }
        }

        return $res;
    }

    ////
    // Setters and Getters
    ////

    public function setSource($name)
    {
        $this->_source = (string) $name;
    }

    public function getSource()
    {
        return $this->_source;
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

    public function setOsBrand($version)
    {
        $this->_osBrand = (string) $version;
    }

    public function getOsBrand()
    {
        return $this->_osBrand;
    }
}
