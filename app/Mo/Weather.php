<?php

/**
 * Weather object model.
 */

Loader::mo(array('Base'), true);

class Mo_WeatherException extends Exception {}

class Mo_Weather extends Mo_Base
{
    /**
     * @var string Location from wheare we are retrieving data from.
     */
    protected $_location;

    /**
     * @var array Data about the current day.
     */
    protected $_current;

    /**
     * @var array Data for the next 3 days.
     */
    protected $_forecast;

    /**
     * @see Mo_Base
     */
    public function __construct(array $values)
    {
        if (!array_key_exists('location', $values)) {
            throw new Mo_WeatherException("Required property: 'location'.");
        }

        parent::__construct($values);
    }

    ////
    // Setters and Getters
    ////

    public function setLocation($name)
    {
        $this->_location = (string) $name;
    }

    public function getLocation()
    {
        return $this->_location;
    }

    public function setCurrent($current)
    {
        $this->_current = (array) $current;
    }

    public function getCurrent()
    {
        return $this->_current;
    }

    public function setForecast($forecast)
    {
        $this->_forecast = (array) $forecast;
    }

    public function getForecast()
    {
        return $this->_forecast;
    }
}
