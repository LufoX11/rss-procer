<?php

/**
 * This class acts as the main view helper.
 */
class Hp_View
{
    /**
     * @var array App configuration.
     */
    protected $_cfg;

    /**
     * @var array Resources to retrieve data from.
     */
    protected $_resources = array();

    /**
     * Builds a new View object.
     *
     * @param $resources array Resources to load.
     * @param $cfg array App configuration for passing to the helpers.
     * @return void.
     */
    public function __construct(array $resources = null, array $cfg = null)
    {
        $this->_cfg = $cfg;
        if ($resources) {
            foreach ($resources as $v) {
                $this->addHelper($v);
            }
        }
    }

    /**
     * Executes the method and args in the top level resource.
     * When adding helpers with addHelper() you put the element at the top of the stack, then
     * when you call a method for Hp_View it will search for it first at the top of the stack
     * (the last one you added) and so on.
     *
     * @see http://www.php.net/manual/en/language.oop5.overloading.php#object.call
     */
    public function __call($method, $args)
    {
        if ($this->_resources) {
            foreach ($this->_resources as $v) {
                if (method_exists($v, $method)) {
                    return call_user_func_array(array($v, $method), $args);
                }
            }
        }

        throw new BadMethodCallException("Method '{$method}' doesn't exists.");
    }

    /**
     * Adds the helper as a new resource at the top of the stack.
     *
     * @param $resource string Resource name. Ie.: 'Path' to refer to 'Hp_View_Path'.
     */
    public function addHelper($resource)
    {
        Loader::hp(array("View/{$resource}"), true);

        $class = "Hp_View_{$resource}";
        array_unshift($this->_resources, new $class($this->_cfg));
    }
}
