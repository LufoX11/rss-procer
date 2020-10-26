<?php

/**
 * Messages data access.
 */

Loader::da(array('BaseMysql'), true);

class Da_Messages extends Da_BaseMysql
{
    /**
     * Fetchs news by they checksums.
     *
     * @param $service string The service name to search for.
     * @return mixed An ARRAY of messages on success; FALSE otherwise.
     */
    public static function fetchByService($service)
    {
        $handler = self::getHandler(self::HANDLER_READ, 'default');
        $res = self::query(
            sprintf(''
                . 'SELECT * '
                . 'FROM messages '
                . 'WHERE status = "enabled" AND (service = "all" OR service = "%s")',
                $handler->escape($service)),
            'default');

        return $res;
    }
}
