<?php

/**
 * Basic functionality for a cron file.
 */

class Cron
{
    protected $_appCfg = null;

    /**
     * Builds a new Cron object.
     *
     * @param $appCfg array CRON configuration.
     * @return void.
     */
    public function __construct(array $appCfg)
    {
        $this->_appCfg = $appCfg;
    }

    /**
     * Locks the cron process to be unique (no other process will be allowed for this cron routine).
     *
     * @param $time integer Time for automatic unlocking the process (in secs) in case the process fails.
     * @param $lockFile string Absolute path to the cron file's PID.
     * @param $importantParams array An array containing the $argv keys to use for the process filter
     *                               in phpProcessCount(...).
     * @param $signal integer One of the PCNTL signals constants: http://php.net/pcntl.constants.php
     * @return void.
     */
    public static function lock($time = null, $lockFile = null, array $importantParams = null, $signal = SIG_BLOCK)
    {
        global $argv;

        Loader::Vendor(array('Process/Process'));

        $process = new Process(true);
        if (!$lockFile) {
            $lockFile = self::getLockFile();
        }

        // If no provided, it will match against all the given params (not recommended if the process might receive
        // no significative params like limits or other ones that don't change the flow of the core process).
        $cmdProc = "{$argv[0]}";
        if ($importantParams) {
            foreach ($importantParams as $v) {
                $cmdProc .= " {$argv[$v]}";
            }
        } else {
            $cmdProc .= ' ' . implode(' ', array_slice($argv, 1));
        }

        if (file_exists($lockFile)) {
            $oldPid = trim(file_get_contents($lockFile));
            clearstatcache();
            if ($time && (filemtime($lockFile) + $time) > time()) {
                if ($process->phpProcessCount($cmdProc)) {
                    die("\n\n---\nThere is another process running. Exiting...\n\n");
                } else {
                    echo "\n\n---\nThere isn't another process running but exists an old lock file. Deleting it...\n\n";
                }
            } else {
                echo "\n\n---\nAutomaticaly removing lock file after $time seconds...";
                if ($oldPid && posix_kill($oldPid, $signal) && $process->phpProcessCount($cmdProc)) {
                    die("\n\n---\nThere is another process running. Impossible, exiting...\n\n");            
                } else {
                    echo "\n\n---\nDeleting stale lock file...\n\n";
                }
            }
            self::unlock();
        }
        file_put_contents($lockFile, getmypid());
    }

    /**
     * Unlocks a previously locked cron process.
     *
     * @param $lockFile string Absolute path to the cron file's PID.
     * @return void.
     */
    public static function unlock($lockFile = null)
    {
        if (!$lockFile) {
            $lockFile = self::getLockFile();
        }
        if (file_exists($lockFile)) {
            unlink($lockFile);
        }
    }

    /**
     * Gets the absolute path and name of the PID file.
     *
     * @param $params array Params to calculate the file ID.
     * @return string The PID file.
     */
	public static function getLockFile(array $params = null)
    {
        global $argv;

        if (!$params) {
            $params = $argv;
        }
		array_shift($params);
		sort($params);
        $id = md5(implode('-', $params));
		$res = '/tmp/' . basename($_SERVER['SCRIPT_NAME'], '.php') . '-' . md5(implode('-', $params)) . '.lock';

		return $res;
	}

    /**
     * Sends an alert email.
     *
     * @param $subject string Email subject.
     * @param $message string Email content.
     * @return void.
     */
    public function sendAlertEmail($subject, $message)
    {
        Loader::lib(array('Email'), true);

        $Email = new Email();
        $receivers = explode('|', $this->_appCfg['main']['alerts']['cron']['toEmail']);
        foreach ($receivers as $v) {
            $Email->send(array(
                'fromName' => $this->_appCfg['main']['alerts']['cron']['fromName'],
                'fromEmail' => $this->_appCfg['main']['alerts']['cron']['fromEmail'],
                'toEmail' => $v,
                'subject' => $subject,
                'message' => $message,
                'isHTML' => false
            ));
        }
    }
}
