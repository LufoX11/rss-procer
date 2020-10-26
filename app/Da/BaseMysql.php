<?php

/**
 * Base object.
 */

Loader::da(array('Handler/Mysql'), true);

abstract class Da_BaseMysql extends Da_Handler_Mysql {}
