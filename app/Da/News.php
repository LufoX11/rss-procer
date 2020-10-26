<?php

/**
 * News data access.
 */

Loader::da(array('BaseMysql'), true);

class Da_News extends Da_BaseMysql
{
    /**
     * Fetchs news by they checksums.
     *
     * @param $checksums array An array of news checksums.
     * @return mixed An ARRAY of news on success; FALSE otherwise.
     */
    public static function fetchByChecksum(array $checksums)
    {
        if (!$checksums) {
            return false;
        }
        $handler = self::getHandler(self::HANDLER_READ);
        array_walk($checksums, function (&$v) use ($handler) { $v = $handler->quote($v); });
        $res = self::query(sprintf(''
            . 'SELECT * '
            . 'FROM news '
            . 'WHERE checksum IN (%s)',
            implode(',', $checksums)));

        return $res;
    }
}
