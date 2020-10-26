<?php

/**
 * Helper file for template for requesting data from the current browser.
 */

Loader::hp(array('Base', 'Browser'), true);

class Hp_View_Browser extends Hp_Base
{
    /**
     * Tells whether we should display the elements for smartphones or tablet / PC.
     *
     * @return boolean TRUE if we should gisplay elements for smartphones; FALSE otherwise.
     */
    public function showMini()
    {
        $res = Hp_Browser::showMini();

        return $res;
    }

    /**
     * Tells if the device is mobile.
     *
     * @return boolean TRUE if the device is mobile; FALSE otherwise.
     */
    public function isMobile()
    {
        $res = Hp_Browser::isMobile();

        return $res;
    }
}
