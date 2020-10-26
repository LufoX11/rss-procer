<?php

/**
 * User settings object model.
 */

Loader::mo(array('Base'), true);

class Mo_UserSetting extends Mo_Base
{
    /**
     * @const Available user settings.
     */
    const LOCATION = 'location';
    const READ_NEWS = 'readNews';
    const TEXT_SIZE = 'textSize';
    const THEME = 'theme';
    const SESSION_VERSION = 'session_version';

    /**
     * @var array Settings friendly titles.
     */
    public static $keysTitles = array(
        self::LOCATION => 'Ubicación',
        self::TEXT_SIZE => 'Tamaño del texto',
        self::THEME => 'Tema'
    );

    /**
     * @var array Available keys.
     */
    public static $keys = array(
        self::LOCATION,
        self::READ_NEWS,
        self::TEXT_SIZE,
        self::THEME,
        self::SESSION_VERSION
    );

    /**
     * @var array All available themes.
     */
    public static $themes = array(
        'black' => array(
            'default',
            'suave'
        ),
        'white' => array(
            'oscuro'
        )
    );

    /**
     * @var array These themes will not be shown for selecting, but they will be available
     *            through configuration.
     */
    public static $specialThemes = array();

    /**
     * @var array CSS selectors for text size setting.
     */
    public static $textSizeSelectors = array(
        '.ui-loader h1' => 15,
        '.ui-bar h1, .ui-bar h2, .ui-bar h3, .ui-bar h4, .ui-bar h5, .ui-bar h6' => 16,
        '.ui-navbar li .ui-btn, .ui-navbar .ui-navbar-toggle .ui-btn' => 16,
        '.ui-navbar-expanded .ui-btn' => 14,
        '.ui-bar .ui-btn' => 13,
        '.ui-mini .ui-btn-inner' => 13,
        '.ui-collapsible-heading' => 16,
        '.ui-controlgroup-label' => 16,
        '.ui-controlgroup .ui-checkbox label, .ui-controlgroup .ui-radio label' => 16,
        'label.ui-select' => 16,
        'label.ui-input-text' => 16,
        'input.ui-input-text, textarea.ui-input-text' => 16,
        '.ui-li-divider, .ui-li-static' => 14,
        '.ui-li-heading' => 16,
        '.ui-li-desc' => 12,
        '.ui-li-has-count .ui-li-count' => 11,
        'label.ui-slider' => 16,
        'span.ui-slider-label' => 16,
        '.text, .text p, .text ul li, .text table td, .text table th, ' => 14,
        '.text h3' => 16,
        '.content-news h1' => 21,
        '.content-news h5' => 12,
        'div[data-role="content"], div[data-role="content"] p, div[data-role="content"] ul li, div[data-role="content"] table td, div[data-role="content"] table th' => 14,
        'div[data-role="content"] h3' => 16,
        '.ui-li-divider .ui-btn-text, .ui-li-static .ui-btn-text, .ui-btn-inner' => 16,
        '.ui-li-divider .ui-mini .ui-btn-text, .ui-li-static .ui-mini .ui-btn-text' => 16
    );

    /**
     * @var integer Users ID.
     */
    protected $_users_id;

    /**
     * @var string An identify key for the value.
     */
    protected $_key;

    /**
     * @var mixed A value to save.
     */
    protected $_value;

    ////
    // Setters and Getters
    ////

    public function setUsers_id($id)
    {
        $this->_users_id = (integer) $id;
    }

    public function getUsers_id()
    {
        return $this->_users_id;
    }

    public function setKey($key)
    {
        $this->_key = (string) $key;
    }

    public function getKey()
    {
        return $this->_key;
    }

    public function setValue($value)
    {
        $this->_value = $value;
    }

    public function getValue()
    {
        return $this->_value;
    }
}
