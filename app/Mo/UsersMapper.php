<?php

/**
 * Users mapper.
 */

Loader::mo(array('Abstract', 'User'), true);

class Mo_UsersMapperException extends Exception {}

class Mo_UsersMapper extends Mo_Abstract
{
    /**
     * @const MC keys.
     */
    const MC_KEY_BY_SERVICE_DEVICEID_DEVICETYPE = 'Mo_UsersMapper::service-%s::deviceId-%s::deviceType-%s::1';

    /**
     * @var array A collection of User object.
     */
    protected static $_users = array();

    /**
     * Fetchs a User by they device ID.
     *
     * @param $service string Service name.
     * @param $deviceId string User device ID.
     * @param $deviceType string User device type.
     * @return mixed User object on success; FALSE otherwise.
     */
    public static function findByDeviceIdAndType($service, $deviceId, $deviceType)
    {
        $mcKey = sprintf(self::MC_KEY_BY_SERVICE_DEVICEID_DEVICETYPE, $service, $deviceId, $deviceType);

        // From Local cache
        if (!($res = self::_findByDeviceIdAndTypeFromLocalCache($service, $deviceId, $deviceType))) {

            // From Memcached
            if (!($res = Da_Handler_Memcached::fetch($mcKey))) {

                // From Database
                if ($res = Da_Handler_Mysql::fetchRow(
                    'users', array('deviceid' => $deviceId, 'devicetype' => $deviceType)))
                {
                    $settings = array();
                    if ($raw = Da_Handler_Mysql::fetchAll('users_settings', array('users_id' => $res['id']))) {
                        foreach ($raw as $v) {
                            $settings[$v['key']] = new Mo_UserSetting(array(
                                'users_id' => $v['users_id'],
                                'key' => $v['key'],
                                'value' => unserialize($v['value'])
                            ));
                        }
                    }
                    $res = new Mo_User(array(
                        'id' => $res['id'],
                        'deviceId' => $res['deviceid'],
                        'deviceType' => $res['devicetype'],
                        'deviceVersion' => $res['deviceversion'],
                        'timestamp' => $res['timestamp'],
                        'settings' => $settings
                    ));
                    self::$_users["{$service}-{$deviceId}-{$deviceType}"] = $res;
                    Da_Handler_Memcached::store($mcKey, $res);
                }
            } else {
                self::$_users["{$service}-{$deviceId}-{$deviceType}"] = $res;
            }
        }

        return $res;
    }

    /**
     * Adds a new user.
     *
     * @param $service string Service name.
     * @param $Mo_User object A Mo_User object.
     * @return boolean TRUE on success; FALSE otherwise.
     */
    public static function insert($service, Mo_User $Mo_User)
    {
        $res = (bool) Da_Handler_Mysql::insert(
            'users',
            array(
                'deviceid' => $Mo_User->deviceId,
                'devicetype' => $Mo_User->deviceType,
                'deviceversion' => $Mo_User->deviceVersion
            ),
            array('ignore' => true)
        );
        $mcKey = sprintf(
            self::MC_KEY_BY_SERVICE_DEVICEID_DEVICETYPE, $service, $Mo_User->deviceId, $Mo_User->deviceType);
        Da_Handler_Memcached::delete($mcKey);
        unset(self::$_users["{$service}-{$Mo_User->deviceId}-{$Mo_User->deviceType}"]);

        return $res;
    }

    /**
     * Adds new user setting.
     *
     * @param $service string Service name.
     * @param $user object Mo_User object.
     * @param $setting object A Mo_UserSetting object to save.
     * @param $incrementUserCache boolean Whether we should increment the user front cache or not.
     * @return boolean TRUE on success; FALSE otherwise.
     */
    public static function insertSetting($service, Mo_User $user, Mo_UserSetting $setting,
        $incrementUserCache = true)
    {
        $res = (bool) Da_Handler_Mysql::insert(
            'users_settings',
            array(
                'users_id' => $setting->users_id,
                'key' => $setting->key,
                'value' => serialize($setting->value)
            ),
            array('update' => array('value')));

        $mcKey = sprintf(self::MC_KEY_BY_SERVICE_DEVICEID_DEVICETYPE, $service, $user->deviceId, $user->deviceType);
        Da_Handler_Memcached::delete($mcKey);
        unset(self::$_users["{$service}-{$user->deviceId}-{$user->deviceType}"]);

        if ($incrementUserCache) {
            self::cacheSessionIncrement($service, $user);
        }

        return $res;
    }

    /**
     * Creates a new user session.
     *
     * @param $service string Service name.
     * @param $deviceId integer User device ID.
     * @param $deviceType string User device type.
     * @param $deviceVersion string User device version.
     * @return Mo_User on success; FALSE otherwise.
     */
    public static function sessionUp($service, $deviceId, $deviceType, $deviceVersion)
    {
        if (!($res = self::findByDeviceIdAndType($service, $deviceId, $deviceType))
            || $res->deviceType != $deviceType || $res->deviceVersion != $deviceVersion)
        {
            $res = (bool) self::insert($service, new Mo_User(array(
                'deviceId' => $deviceId,
                'deviceType' => $deviceType,
                'deviceVersion' => $deviceVersion
            )));
            if ($res) {
                $res = self::findByDeviceIdAndType($service, $deviceId, $deviceType);
            }
        }
        if ($res instanceof Mo_User) {
            self::saveDeviceDataInCookies($res);
        }

        return $res;
    }

    /**
     * Increments the user cache for frontend.
     *
     * @param $service string Service name.
     * @param $user Mo_User User object.
     * @return integer Current session version.
     */
    public static function cacheSessionIncrement($service, Mo_User $user)
    {
        if (!($res = $user->getSetting(Mo_UserSetting::SESSION_VERSION))) {
            $res = 0;
        }
        $setting = new Mo_UserSetting(array(
            'users_id' => $user->id,
            'key' => Mo_UserSetting::SESSION_VERSION,
            'value' => ++ $res
        ));
        self::insertSetting($service, $user, $setting, false);

        return $res;
    }

    /**
     * Gets the user cache for frontend.
     *
     * @param $service string Service name.
     * @param $user Mo_User User object.
     * @return string Current session version.
     */
    public static function cacheSessionGet($service, Mo_User $user)
    {
        if (!($res = $user->getSetting(Mo_UserSetting::SESSION_VERSION))) {
            $res = self::cacheSessionIncrement($service, $user);
        }

        return $res;
    }

    /**
     * Fetches device data from browser cookies.
     *
     * @param $field string Get only this field.
     * @param $options array Options:
     *                       'fallbackToGuest' => true: If no data in cookies then create a new guest type session.
     * @return mixed Mo_User object / field content on success; NULL otherwise.
     */
    public static function fetchDeviceDataFromCookies($field = null, array $options = array())
    {
        Loader::lib(array('ImobileDetector'), true);

        // Defaults
        $defaultOptions = array(
            'fallbackToGuest' => true
        );
        $options = array_merge($defaultOptions, $options);

        // If not exists data in cookie, create a guest session (if enabled)
        $Slim = \Slim\Slim::getInstance();
        if (!($res = $Slim->getEncryptedCookie('user')) && $options['fallbackToGuest']) {
            $res = new Mo_User(array(
                'deviceId' => 'guest-' . sha1('::Sherlock::' . microtime() . mt_getrandmax()),
                'deviceType' => ImobileDetector::DEVICE_TYPE_GENERIC,
                'deviceVersion' => 'unknown'
            ));
        }

        // There's data in cookie or guest session newly created
        if ($res && !$res instanceof Mo_User) {
            $res = json_decode($res, true);
            $res = new Mo_User(array(
                'deviceId' => $res['deviceId'],
                'deviceType' => $res['deviceType'],
                'deviceVersion' => $res['deviceVersion']
            ));
        }

        // Get only the specific field
        if ($res && $field) {
            $res = $res->$field;
        }

        return $res;
    }

    /**
     * Saves user session data in browser cookies.
     *
     * @param $Mo_User object Mo_User.
     * @return void.
     */
    public static function saveDeviceDataInCookies(Mo_User $Mo_User)
    {
        $Slim = \Slim\Slim::getInstance();
        $userData = json_encode(array(
            'deviceId' => $Mo_User->deviceId,
            'deviceType' => $Mo_User->deviceType,
            'deviceVersion' => $Mo_User->deviceVersion));

        $Slim->setEncryptedCookie('user', $userData);
    }

    /**
     * Checks if the theme is available for choosing.
     *
     * @param $name string Theme name.
     * @return boolean TRUE if it's available; FALSE otherwise.
     */
    public static function isThemeAvailable($name)
    {
        $res = false;
        foreach (Mo_UserSetting::$themes as $v) {
            if ($res = in_array($name, $v)) {
                break;
            }
        }

        return $res;
    }

    /**
     * Fetchs a User from local cache.
     *
     * @param $service string Service name.
     * @param $deviceId string User device ID.
     * @param $deviceType string User device type.
     * @return mixed User object on success; FALSE otherwise.
     */
    protected static function _findByDeviceIdAndTypeFromLocalCache($service, $deviceId, $deviceType)
    {
        $res = false;
        if (isset(self::$_users["{$service}-{$deviceId}-{$deviceType}"])) {
            $res = self::$_users["{$service}-{$deviceId}-{$deviceType}"];
        }

        return $res;
    }
}
