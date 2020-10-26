<?php

/**
 * Helper file for getting readable names from internal ones.
 */

Loader::hp(array('Base'), true);

class Hp_Name extends Hp_Base
{
    /**
     * @var array Applications names.
     */
    protected static $_applications = array(
        'mprocer' => 'M-Procer',
        'rssprocer' => 'RSS Procer'
    );

    /**
     * @var array Types of services.
     */
    protected static $_servicesTypes = array(
        'newspaper' => 'Diarios / Periódicos',
        'magazine' => 'Revistas',
        'technology' => 'Tecnología',
        'green' => 'Ecología / Salud',
        'work' => 'Laborales',
        'general' => 'Información General',
    );

    /**
     * Gets the application's cool name.
     *
     * @param $name string Internal name (in config file).
     * @return mixed Application cool name on success; Empty string if not found.
     */
    public static function getCoolName($name)
    {
        $res = '';
        if (array_key_exists($name, self::$_applications)) {
            $res = self::$_applications[$name];
        }

        return $res;
    }

    /**
     * Gets the cool name of the service type.
     *
     * @param $name string Internal name (in config file).
     * @return mixed Cool name of the service type on success; Empty string if not found.
     */
    public static function getServiceTypeCoolName($name)
    {
        $res = '';
        if (array_key_exists($name, self::$_servicesTypes)) {
            $res = self::$_servicesTypes[$name];
        }

        return $res;
    }

    /**
     * Fetches all the applications names.
     */
    public static function fetchApplicationsNames()
    {
        return self::$_applications;
    }

    /**
     * Fetches all services types.
     */
    public static function fetchServicesTypes()
    {
        return self::$_servicesTypes;
    }
}
