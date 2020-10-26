<?php

/**
 * Channel object model.
 */

Loader::mo(array('Base'), true);

class Mo_Channel extends Mo_Base
{
    /**
     * @var integer Channel ID.
     */
    protected $_id;

    /**
     * @var string Channel title.
     */
    protected $_title;

    /**
     * @var string Channel description.
     */
    protected $_description;

    /**
     * @var string Channel URI image source.
     */
    protected $_image;

    /**
     * Gets a 16x16 icon from saved image.
     *
     * @return string Icon path on success; Empty string otherwise.
     */
    public function getImageIcon()
    {
        $res = '';
        if ($this->getImage()) {
            $img = pathinfo($this->getImage());
            if (isset($img['extension'])) {
                $res = "{$img['filename']}-icon.{$img['extension']}";
                if (isset($img['dirname']) && $img['dirname'] != 'local:') {
                    $res = "{$img['dirname']}/{$res}";
                }
            }
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

    public function setTitle($name)
    {
        $this->_title = trim(ucwords($name));
    }

    public function getTitle()
    {
        return $this->_title;
    }

    public function setDescription($text)
    {
        $this->_description = trim($text);
    }

    public function getDescription()
    {
        return $this->_description;
    }

    public function setImage($source)
    {
        $this->_image = trim($source);
    }

    public function getImage()
    {
        return $this->_image;
    }
}
