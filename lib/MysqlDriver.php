<?php

/**
 * This is the MYSQL database connector driver.
 */

class MysqlDriverException extends Exception {}

class MysqlDriver extends MySQLi
{
    /**
     * @const Connection retries before throwing connection exception.
     */
    const RETRIES = 2;

    /**
     * Connects to MYSQL server.
     *
     * @param $cfg array 'host', 'user', 'password', 'database' and 'port' connection data.
     * @return object Connection handler.
     */
    public function __construct(array $cfg)
    {
        $attemps = self::RETRIES + 1;
        while ($attemps > 0) {
            parent::__construct(
                $cfg['host'], $cfg['user'], $cfg['password'], $cfg['database'], $cfg['port']);

            if ($connError = mysqli_connect_error()) {
                $attemps --;
            } else {
                return;
            }
        }

        throw new MysqlDriverException($connError);
    }

    /**
     * Helper function for native query() driver method.
     *
     * @param $query string Query string.
     * @return object MYSQL resultset.
     */
    public function query($query)
    {
        if ($res = parent::query($query)) {
            return $res;
        }

        throw new MysqlDriverException($this->error);
    }

    /**
     * Gets the num of rows returned.
     *
     * @param $query string Query string.
     * @return integer Num of rows returned.
     */
    public function count($query)
    {
        if ($res = parent::query($query)) {
            return $res->num_rows;
        }

        throw new MysqlDriverException($this->error);
    }

    /**
     * Improves MYSQL use of memory freeing it after connection is closed.
     *
     * @return void.
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Escapes a string.
     *
     * @param $str string String to escape.
     * @return string Escaped string.
     */
    public function escape($str)
    {
        $res = parent::real_escape_string($str);

        return $res;
    }

    /**
     * Escapes a string and adds double quotes to the escaped value.
     *
     * @param $str string String to escape.
     * @return string Escaped string.
     */
    public function quote($str)
    {
        $res = '"' . parent::real_escape_string($str) . '"';

        return $res;
    }

    /**
     * Fetchs a resultset as an associative array.
     *
     * @param $result object A mysqli_result object.
     * @return array All result rows.
     */
    public function fetchArray(mysqli_result $result)
    {
        $res = array();
        while ($row = $result->fetch_assoc()) {
            $res[] = $row;
        }

        return $res;
    }
}
