<?php

/**
 * Helper file for front-end app scheme related functions.
 */

Loader::hp(array('Base'), true);

class Hp_Appearance extends Hp_Base
{
    /**
     * @var array Menu elements.
     */
    private $_mainMenu = array(
        array('Novedades' => true),
        array('Secciones' => true),
        array('Configuración' => true),
        array('Herramientas' => true)
    );

    /**
     * Builds a new front-end structure.
     *
     * @return array Configuration for the front-end structure.
     */
    public function make()
    {
        $res = array(
            'mainMenu' => $this->_makeMainMenu()
        );

        return $res;
    }

    /**
     * Returns the generated configuration for the main menu structure.
     *
     * @param $filterInvisible boolean TRUE if we want to filter invisible elements.
     * @return array Main menu configuration.
     */
    public function getMainMenu($filterInvisible = false)
    {
        $res = $this->_makeMainMenu();
        if ($filterInvisible) {
            $res = array_filter($res, function ($v) {
                return current($v);
            });
        }

        return $res;
    }

    /**
     * Builds the main menu (top menu) configuration.
     *
     * @return array Main menu configuration.
     */
    protected function _makeMainMenu()
    {
        $res = $this->_mainMenu;
        if (isset($this->_cfg['mainMenu'])
                && $raw = array_filter(explode('|', $this->_cfg['mainMenu']))) {
            for ($i = 0; $i <= count($res); $i ++) {
                if ($element = array_filter(explode(',', current($raw)))) {
                    $res[$i] = array($element[0] => (isset($element[1]) ? (bool) $element[1] : false));
                    next($raw);
                }
            }
        }

        return $res;
    }
}
