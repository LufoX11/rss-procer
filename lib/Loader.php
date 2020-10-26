<?php

/**
 * Libraries loader.
 */

abstract class Loader
{
    /**
     * @var array Directory routes.
     */
    public static $map = array(
        'libraries' => 'lib',
        'models' => 'app/Mo',
        'dataAccessors' => 'app/Da',
        'helpers' => 'app/Hp',
        'vendors' => 'vendor'
    );

    /**
     * Loads a library.
     *
     * @param $fileName array Names of the files (or relative paths if they have subdirectories) to include.
     * @param $once boolean If TRUE it will try a "include_once" instead of "include".
     * @return void.
     */
    public static function lib(array $fileName, $once = false)
    {
        foreach ($fileName as $v) {
            self::file(dirname(__FILE__) . '/../' . self::$map['libraries'] . "/{$v}.php", $once);
        }
    }

    /**
     * Loads a model.
     *
     * @param $fileName array Names of the files (or relative paths if they have subdirectories) to include.
     * @param $once boolean If TRUE it will try a "include_once" instead of "include".
     * @return void.
     */
    public static function mo(array $fileName, $once = false)
    {
        foreach ($fileName as $v) {
            self::file(dirname(__FILE__) . '/../' . self::$map['models'] . "/{$v}.php", $once);
        }
    }

    /**
     * Loads a data accessor.
     *
     * @param $fileName array Names of the files (or relative paths if they have subdirectories) to include.
     * @param $once boolean If TRUE it will try a "include_once" instead of "include".
     * @return void.
     */
    public static function da(array $fileName, $once = false)
    {
        foreach ($fileName as $v) {
            self::file(dirname(__FILE__) . '/../' . self::$map['dataAccessors'] . "/{$v}.php", $once);
        }
    }

    /**
     * Loads a helper.
     *
     * @param $fileName array Names of the files (or relative paths if they have subdirectories) to include.
     * @param $once boolean If TRUE it will try a "include_once" instead of "include".
     * @return void.
     */
    public static function hp(array $fileName, $once = false)
    {
        foreach ($fileName as $v) {
            self::file(dirname(__FILE__) . '/../' . self::$map['helpers'] . "/{$v}.php", $once);
        }
    }


    /**
     * Loads a vendor library.
     *
     * @param $fileName array Names of the files (or relative paths if they have subdirectories) to include.
     * @param $once boolean If TRUE it will try a "include_once" instead of "include".
     * @return void.
     */
    public static function vendor(array $fileName, $once = false)
    {
        foreach ($fileName as $v) {
            self::file(dirname(__FILE__) . '/../' . self::$map['vendors'] . "/{$v}.php", $once);
        }
    }

    /**
     * Loads a specific file.
     *
     * @param $path string Absolute path to the file.
     * @param $once boolean If TRUE it will try a "include_once" instead of "include".
     * @return boolean TRUE on success; FALSE otherwise.
     */
    public static function file($path, $once = false)
    {
        if ($once) {
            $res = include_once $path;
        } else {
            $res = include $path;
        }

        return $res;
    }
}
