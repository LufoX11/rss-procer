<?php

/**
 * Helper file for requesting data from the current browser.
 */

Loader::hp(array('Base'), true);
Loader::lib(array('ImobileDetector'), true);

class Hp_Browser extends Hp_Base
{
    /**
     * @var boolean Is a mobile device.
     */
    protected static $_isMobile;

    /**
     * @var boolean Is a tablet device.
     */
    protected static $_isTablet;

    /**
     * @var string Device type.
     */
    protected static $_deviceType;

    /**
     * Tells whether we should display the elements for smartphones or tablet / PC.
     *
     * @return boolean TRUE if we should display elements for smartphones; FALSE otherwise.
     */
    public static function showMini()
    {
        $res = self::isMobile() && !self::isTablet();

        return $res;
    }

    /**
     * Tells if the device is mobile.
     *
     * @param $guess boolean Force to guess from HTTP headers.
     * @return boolean TRUE if the device is mobile; FALSE otherwise.
     */
    public static function isMobile($guess = false)
    {
        if (!isset(self::$_isMobile)) {

            // Try setting from device type or fallback to guessing mode (by checking HTTP headers)
            if (!$guess && isset(self::$_deviceType)) {
                self::$_isMobile = true;
            } else {
                $iMobileDetector = new ImobileDetector();
                self::$_isMobile = $iMobileDetector->isMobile();
            }
        }

        return self::$_isMobile;
    }

    /**
     * Tells if the device is a mobile tablet.
     *
     * @param $guess boolean Force to guess from HTTP headers.
     * @return boolean TRUE if the device is a mobile tablet; FALSE otherwise.
     */
    public static function isTablet($guess = false)
    {
        if (!isset(self::$_isTablet)) {

            // Try setting from device type or fallback to guessing mode (by checking HTTP headers)
            if (!$guess && isset(self::$_deviceType)) {
                self::$_isTablet = (Mo_Client::getDeviceCategory(self::$_deviceType) == 'tablet');
            } else {
                $iMobileDetector = new ImobileDetector();
                self::$_isTablet = $iMobileDetector->isTablet();
            }
        }

        return self::$_isTablet;
    }

    /**
     * Tells the device type.
     *
     * @return string Device type according to ImobileDetector::DEVICE_TYPE_*
     */
    public static function getDeviceType()
    {
        if (!isset(self::$_deviceType)) {
            $iMobileDetector = new ImobileDetector();
            self::$_deviceType = $iMobileDetector->getDeviceType();
        }

        return self::$_deviceType;
    }

    /**
     * Tells if the device type is Blackberry.
     *
     * @return boolean TRUE if Blackberry; FALSE otherwise.
     */
    public static function isBlackberry()
    {
        $res = (self::getDeviceType() == ImobileDetector::DEVICE_TYPE_BLACKBERRY);

        return $res;
    }

    /**
     * Sets the device type.
     *
     * @param $deviceType string Device type code like "bb", "pb", etc.
     * @return void.
     */
    public static function setDeviceType($deviceType)
    {
        self::$_deviceType = $deviceType;
    }
}
