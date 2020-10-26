<?php

/**
 * Applets factory.
 */

class Mo_AppletException extends Exception {}

abstract class Mo_Applet
{
    /**
     * Private constructors.
     */
    private function __construct() {}
    private function __clone() {}

    /**
     * Builds a new Applet.
     *
     * @param $name string The name of the applet.
     * @param $options array Initialization options.
     * @return object A new objetc instance.
     */
    public static function get($name, array $options = null)
    {
        if (Loader::file(dirname(__FILE__) . "/Applet/{$name}.php", true)) {
            $applet = "Mo_Applet_{$name}";
        } else {
            throw new Mo_AppletException("Applet not found: {$name}");
        }

        return new $applet($options);
    }
}
