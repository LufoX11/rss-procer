<?php

/**
 * Base object.
 */

class Mo_BaseException extends Exception {}

abstract class Mo_Base
{
    /**
     * Initializes the object.
     *
     * @param $values array An array of key-value pars with all object default values.
     * @param $options array An array of options for the object initialization.
     * @return void.
     */
    public function __construct(array $values = null, array $options = null)
    {
        if (is_array($values)) {
            $this->setValues($values);
        }
    }

    /**
     * Sets the object.
     *
     * @param $values array An array of key-value pars with all object default values.
     * @return object Current object.
     */
    public function setValues(array $values)
    {
        $methods = get_class_methods($this);
        foreach ($values as $k => $v) {
            $method = 'set' . ucfirst($k);
            $this->$method($v);
        }

        return $this;
    }

    /**
     * Sets a new object property.
     *
     * @param $name string Property name (must exists).
     * @param $value mixed Value for the property.
     * @return void.
     */
    public function __set($name, $value)
    {
        $method = 'set' . ucfirst($name);
        if (!method_exists($this, $method)) {
            throw new Mo_BaseException("SET: Invalid property ('{$name}').");
        }
        $this->$method($value);
    }

    /**
     * Gets a property from the object.
     *
     * @param $name string Property name (must exists).
     * @return mixed Property value.
     */
    public function __get($name)
    {
        $method = 'get' . ucfirst($name);
        if (!method_exists($this, $method)) {
            throw new Mo_BaseException("GET: Invalid property ('{$name}').");
        }

        return $this->$method();
    }
}
