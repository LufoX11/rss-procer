<?php

/**
 * Sorting object model.
 */

Loader::mo(array('Base'), true);

class Mo_Sorting extends Mo_Base
{
    /**
     * @var integer Channel ID from Channel object.
     */
    protected $_channels_id;

    /**
     * @var array Sorting value.
     */
    protected $_value;

    ////
    // Setters and Getters
    ////

    public function setChannels_id($id)
    {
        $this->_channels_id = (integer) $id;
    }

    public function getChannels_id()
    {
        return $this->_channels_id;
    }

    public function setValue($value)
    {
        $this->_value = (array) $value;
    }

    public function getValue()
    {
        return $this->_value;
    }
}
