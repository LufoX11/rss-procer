<?php

/**
 * Helper file for building elements for the front-end layer.
 */

Loader::hp(array('Base'), true);

class Hp_View_Path extends Hp_Base
{
    /**
     * @var string Icons final folder (this is for theming them).
     */
    protected $_imagesSubdir = 'themes/black';

    /**
     * Builds a new Hp_View_Path object.
     * Important: We force to pass the config data because it's required for this helper to work properly.
     *
     * @param $cfg array Configuration required by the Helper.
     * @return void.
     */
    public function __construct(array $cfg)
    {
        parent::__construct($cfg);
    }

    /**
     * Sets the subdirectory for images.
     *
     * @param $path string Relative path to images subdirectory.
     * @return void.
     */
    public function setImagesSubdir($path)
    {
        $this->_imagesSubdir = $path;
    }

    /**
     * Returns the URL to load a Javascript file.
     *
     * @param $file string Javascript file name (without path).
     * @return string Javascript file path.
     */
    public function getJs($file)
    {
        $path = $this->_cfg['paths']['web']['static']['js'];
        $version = $this->_cfg['front']['versioning']['platform']
            . $this->_cfg['front']['versioning']['javascript'];
        $res = "{$path}/{$file}?{$version}";

        return $res;
    }

    /**
     * Returns the URL to load a CSS file.
     *
     * @param $file string CSS file name (without path).
     * @return string CSS file path.
     */
    public function getCss($file)
    {
        $path = $this->_cfg['paths']['web']['static']['css'];
        $version = $this->_cfg['front']['versioning']['platform']
            . $this->_cfg['front']['versioning']['css'];
        $res = "{$path}/{$file}?{$version}";

        return $res;
    }

    /**
     * Returns the URL to load an IMAGE file.
     *
     * @param $file string Image file name (without path).
     * @return string Image file path.
     */
    public function getImg($file)
    {
        // Check if the image if needed from the service directory
        $path = $this->_cfg['paths']['web']['static']['img'] . '/' . $this->_imagesSubdir;
        if (stripos($file, 'local://') === 0) {
            $file = str_ireplace('local://', '', $file);
        } else if (stripos($file, 'themes://') === 0) {

            // Access from /img/themes
            $file = str_ireplace('themes://', '', $file);
            $path = "{$this->_cfg['paths']['web']['static']['img']}/themes";
        } else if (stripos($file, 'services/') !== false) {
            $path = $this->_cfg['paths']['web']['static']['img'];
        }
        $file = str_ireplace(' ', '%20', $file);
        $version = $this->_cfg['front']['versioning']['platform']
            . $this->_cfg['front']['versioning']['images'];
        $res = "{$path}/{$file}?{$version}";

        return $res;
    }

    /**
     * Builds an absolute link to the specified resource.
     *
     * @param $resource string Final relative path / resource for the link.
     *                         Exceptional resource names (returns specific/dynamic links):
     *                         -> mainMenuHome: The current home link.
     * @param $params array Array of parameters for the resource.
     * @param $options array Special options.
     *                       'base': A custom base path for the resource.
     *                       'removeProtocol': Removes the protocol in the final URI.
     * @return string Generated link.
     */
    public function getLink($resource = null, array $params = null, array $options = null)
    {
        $base = $this->_cfg['paths']['web']['site']['base'];
        switch ($resource) {
            case 'default':
                $base = $this->_cfg['paths']['web']['site']['default'];
                $resource = '';
                break;

            case 'mainMenuHome':
                $Hp_Appearance = new Hp_Appearance($this->_cfg['appearance']);
                switch (key($Hp_Appearance->getMainMenu(true))) {
                    case 0:
                        $resource = 'home';
                        break;

                    case 1:
                        $resource = 'topics';
                        break;

                    case 2:
                        $resource = 'weather';
                        break;

                    case 3:
                        $resource = 'more';
                        break;
                }
                break;

            case 'guestLogin':
                $base = $resource = '';
                break;
        }
        if (!$base && !$resource) {
            $base = '/';
        }

        // Options: Custom base
        if (isset($options['base'])) {
            $base = $options['base'];
        }

        $res = ''
            . $base // Domain
            . ($resource ? "/{$resource}" : '') // Resource
            . ($params ? '?' . http_build_query($params) : ''); // Params

        // Replace double slashes
        $res = preg_replace('@^([a-zA-Z]+:/)+(.*)$@', '$1/$2', preg_replace('@(/)+@', '/', $res));

        // Options: Remove protocol
        if (isset($options['removeProtocol'])) {
            $res = preg_replace('@^([a-zA-Z]+://)+(.*)$@', '$2', $res);
        }

        return $res;
    }
}
