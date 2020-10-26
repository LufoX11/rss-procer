<?php

/**
 * News object model.
 */

Loader::mo(array('Base'), true);

class Mo_News extends Mo_Base
{
    /**
     * @var integer News ID.
     */
    protected $_id;

    /**
     * @var string 
     */
    protected $_channels_id;

    /**
     * @var string News title.
     */
    protected $_title;

    /**
     * @var string News short description.
     */
    protected $_summary;

    /**
     * @var string News body.
     */
    protected $_description;

    /**
     * @var string Publication date and time.
     */
    protected $_datetime;

    /**
     * @var string Link to access the original web news.
     */
    protected $_link;

    /**
     * @var string Short link to the news link.
     */
    protected $_shortlink;

    /**
     * @var string News image source.
     */
    protected $_image;

    /**
     * @var string News unique identity.
     */
    protected $_checksum;

    /**
     * Generates a checksum for the news.
     *
     * @param $channelId integer News channel ID.
     * @param $title string News title.
     * @return string A hash for the news.
     */
    public static function generateChecksum($channelId, $title)
    {
        $res = sha1("iMaat::{$channelId}::{$title}");

        return $res;
    }

    /**
     * Gets the image in medium size.
     *
     * @return string Image path.
     */
    public function getMdImage()
    {
        $res = '';
        if ($this->getImage()) {
            $img = pathinfo($this->getImage());
            if (isset($img['extension'])) {
                $res = "{$img['dirname']}/{$img['filename']}-md.{$img['extension']}";
            }
        }

        return $res;
    }

    /**
     * Gets the image in big size.
     *
     * @return string Image path.
     */
    public function getBgImage()
    {
        $res = '';
        if ($this->getImage()) {
            $img = pathinfo($this->getImage());
            if (isset($img['extension'])) {
                $res = "{$img['dirname']}/{$img['filename']}-bg.{$img['extension']}";
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

    public function setChannels_id($id)
    {
        $this->_channels_id = (integer) $id;
    }

    public function getChannels_id()
    {
        return $this->_channels_id;
    }

    public function setTitle($name)
    {
        $this->_title = trim($name);
    }

    public function getTitle()
    {
        return $this->_title;
    }

    public function setSummary($text)
    {
        $this->_summary = trim($text);
    }

    public function getSummary()
    {
        return $this->_summary;
    }

    public function setDescription($text)
    {
        $this->_description = trim($text);
    }

    public function getDescription()
    {
        return $this->_description;
    }

    public function setDatetime($datetime)
    {
        $this->_datetime = date('Y-m-d H:i:s', strtotime($datetime));
    }

    public function getDatetime()
    {
        return $this->_datetime;
    }

    public function setLink($link)
    {
        $this->_link = trim($link);
    }

    public function getLink()
    {
        return $this->_link;
    }

    public function setShortlink($link)
    {
        $this->_shortlink = trim($link);
    }

    public function getShortlink()
    {
        return $this->_shortlink;
    }

    public function setImage($source)
    {
        $this->_image = trim($source);
    }

    public function getImage()
    {
        return $this->_image;
    }

    public function setChecksum($checksum)
    {
        $this->_checksum = $checksum;
    }

    public function getChecksum()
    {
        return $this->_checksum;
    }
}
