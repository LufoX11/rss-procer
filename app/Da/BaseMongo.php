<?php

/**
 * Base object for Mongo database.
 */

Loader::da(array('Handler/Mongo'), true);

abstract class Da_BaseMongo extends Da_Handler_Mongo {}
