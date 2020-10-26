<?php

/**
 * Stats data access.
 */

Loader::da(array('BaseMysql'), true);

class Da_Stats extends Da_BaseMysql
{
    /**
     * @see parent.
     */
    public function __construct(array $cfg)
    {
        parent::__construct($cfg);
        $this->setDbDriver('default');
    }

    /**
     * Fetches NEW_USERS by time lapse.
     *
     * @param $by string [day|week|month|year].
     * @return array An array of users on success; FALSE otherwise.
     */
    public function fetchNewUsers($by = 'day')
    {
        $this->setDbAccessHandlerType(self::HANDLER_READ);
        switch ($by) {
            case 'day': default:
                $by = 'DATE';
                $timestamp = date('Y-m-d', strtotime('-1 month'));
                break;

            case 'week':
                $by = 'WEEK';
                $timestamp = date('Y-m-d', strtotime('-3 month'));
                break;

            case 'month':
                $by = 'MONTH';
                $timestamp = date('Y-m-d', strtotime('-6 month'));
                break;

            case 'year':
                $by = 'YEAR';
                $timestamp = date('Y-m-d', strtotime('-1 year'));
                break;
        }

        $res = $this->query(sprintf(''
            . 'SELECT COUNT(*) total, DATE(timestamp) timestamp '
            . 'FROM users '
            . 'WHERE deviceid NOT LIKE "guest-%%" '
                . 'AND deviceid NOT LIKE "123%%" '
                . 'AND timestamp >= "%s"'
            . 'GROUP BY %s(timestamp) '
            . 'ORDER BY timestamp DESC',
            $timestamp,
            $by));

        return $res;
    }

    /**
     * Fetches BY_DEVICETYPE.
     *
     * @return array An array of device types stats on success; FALSE otherwise.
     */
    public function fetchByDevicetype()
    {
        $this->setDbAccessHandlerType(self::HANDLER_READ);
        $res = $this->query(''
            . 'SELECT COUNT(*) total, devicetype '
            . 'FROM users '
            . 'WHERE deviceid NOT LIKE "guest-%%" AND deviceid NOT LIKE "123%%" '
            . 'GROUP BY devicetype');

        return $res;
    }

    /**
     * Fetches USERS_CUSTOMIZATION.
     *
     * @return array An array with totals of users customizations on success; FALSE otherwise.
     */
    public function fetchUsersCustomization()
    {
        Loader::Mo(array('UserSetting'), true);

        $this->setDbAccessHandlerType(self::HANDLER_READ);
        $inClause = array(
            $this->getHandler()->quote(Mo_UserSetting::LOCATION),
            $this->getHandler()->quote(Mo_UserSetting::TEXT_SIZE),
            $this->getHandler()->quote(Mo_UserSetting::THEME),
        );
        $sql = sprintf(''
            . 'SELECT COUNT(*) total, s.key, u.deviceid '
            . 'FROM users_settings s '
            . 'INNER JOIN users u ON s.users_id = u.id '
            . 'WHERE u.deviceid NOT LIKE "guest-%%" AND u.deviceid NOT LIKE "123%%" '
            . 'GROUP BY s.key '
            . 'HAVING s.key IN (%s)',
            implode(',', $inClause));
        if ($res = $this->query($sql)) {
            array_walk($res, function (&$v) { unset($v['deviceid']); });
        }

        return $res;
    }

    /**
     * Fetches USERS_TOTALS.
     *
     * @return array Summary of total of users on success; FALSE otherwise.
     */
    public function fetchUsersTotals()
    {
        $this->setDbAccessHandlerType(self::HANDLER_READ);
        $sql = ''
            . 'SELECT COUNT(deviceid) total, "mobile" type '
            . 'FROM users '
            . 'WHERE deviceid NOT LIKE "guest-%%" AND deviceid NOT LIKE "123%%" '
            . 'UNION '
            . 'SELECT COUNT(deviceid) total, "web" type '
            . 'FROM users '
            . 'WHERE deviceid LIKE "guest-%%" OR deviceid LIKE "123%%"';

        if ($res = $this->query($sql)) {
            $res[] = array(
                'total' => $res[0]['total'] + $res[1]['total'],
                'type' => 'total'
            );
        }

        return $res;
    }
}
