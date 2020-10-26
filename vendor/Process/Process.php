<?php

/**
 * This library provides a way to check for processes in memory.
 */

class Process
{
    /**
     * @var string Location of the "ps" command in the filesystem.
     */
    protected $_psCommand = '/bin/ps';

    /**
     * @var string Location of the "grep" command in the filesystem.
     */
    protected $_grepCommand = '/bin/grep';

    /**
     * @var string Location of the "wc" command in the filesystem.
     */
    protected $_wcCommand = '/usr/bin/wc';

    /**
     * Checks for scripts running in returns the amount of them.
     *
     * @param $script string The process name to filter (you can specify params).
     * @return integer The amount of processes found (matched agains $script); 0 otherwise.
     */
    public function phpProcessCount($script)
    {
        $args = array(
            "{$this->_psCommand} u -C php |",
            "{$this->_grepCommand} " . '"' . escapeshellcmd($script) . '" |',
            "{$this->_wcCommand} -l"
        );
        $total = shell_exec(implode(' ', $args));

        // We have to remove the current process from the list.
        // For inconsistent results (not number, negative or zero), it will return 0.
        $res = max(trim($total) - 1, 0);

        return $res;
    }
}
