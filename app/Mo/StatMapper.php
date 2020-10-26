<?php

/**
 * Stat mapper.
 */

Loader::mo(array('Abstract'), true);
Loader::da(array('Stats', 'Tracking'), true);

class Mo_StatMapper extends Mo_Abstract
{
    /**
     * @see parent.
     */
    public function getDbHandler()
    {
        if (!$this->_dbHandler) {
            $this->setDbHandler('Da_Stats');
        }

        return $this->_dbHandler;
    }

    /**
     * Fetches NEW_USERS.
     *
     * @param $service string Service name.
     * @return Mo_Stat.
     */
    public function fetchNewUsers($service)
    {
        // From Database
        $handler = $this->getDbHandler()->setDbDriver($service);
        $raw = array();
        $raw['byDay'] = $handler->fetchNewUsers('day');
        $raw['byWeek'] = $handler->fetchNewUsers('week');
        $raw['byMonth'] = $handler->fetchNewUsers('month');
        $raw['byYear'] = $handler->fetchNewUsers('year');
        $res = new Mo_Stat(array(
            'service' => $service,
            'type' => Mo_Stat::NEW_USERS,
            'data' => $raw,
            'timestamp' => date('Y-m-d H:i:s')
        ));

        return $res;
    }

    /**
     * Fetches BY_DEVICETYPE.
     *
     * @param $service string Service name.
     * @return Mo_Stat.
     */
    public function fetchByDevicetype($service)
    {
        // From Database
        $raw = $this->getDbHandler()->setDbDriver($service)->fetchByDevicetype();
        $res = new Mo_Stat(array(
            'service' => $service,
            'type' => Mo_Stat::BY_DEVICETYPE,
            'data' => $raw,
            'timestamp' => date('Y-m-d H:i:s')
        ));

        return $res;
    }

    /**
     * Fetches USERS_CUSTOMIZATION.
     *
     * @param $service string Service name.
     * @return Mo_Stat.
     */
    public function fetchUsersCustomization($service)
    {
        // From Database
        $raw = $this->getDbHandler()->setDbDriver($service)->fetchUsersCustomization();
        $res = new Mo_Stat(array(
            'service' => $service,
            'type' => Mo_Stat::USERS_CUSTOMIZATION,
            'data' => $raw,
            'timestamp' => date('Y-m-d H:i:s')
        ));

        return $res;
    }

    /**
     * Fetches USERS_TOTALS.
     *
     * @param $service string Service name.
     * @return Mo_Stat.
     */
    public function fetchUsersTotals($service)
    {
        // From Database
        $raw = $this->getDbHandler()->setDbDriver($service)->fetchUsersTotals();
        $res = new Mo_Stat(array(
            'service' => $service,
            'type' => Mo_Stat::USERS_TOTALS,
            'data' => $raw,
            'timestamp' => date('Y-m-d H:i:s')
        ));

        return $res;
    }

    /**
     * Fetches OVERALL_CLICKS.
     *
     * @param $service string Service name.
     * @return Mo_Stat.
     */
    public function fetchOverallClicks($service)
    {
        // From Database
        $currentDbHandler = $this->getDbHandler();
        $raw = $this->setDbHandler('Da_Tracking')->getDbHandler()->fetchOverallClicks($service);
        $res = new Mo_Stat(array(
            'service' => $service,
            'type' => Mo_Stat::OVERALL_CLICKS,
            'data' => $raw,
            'timestamp' => date('Y-m-d H:i:s')
        ));
        $this->setDbHandler($currentDbHandler);

        return $res;
    }

    /**
     * Retrieves all stats data.
     *
     * @param $service string Service name.
     * @return array A collection of Mo_Stat objects on success; EMPTY array otherwise.
     */
    public function fetchAll($service = null)
    {
        $res = array();
        if ($service) {
            if ($raw = $this->getDbHandler()->fetchAll('stats', array('service' => $service))) {
                foreach ($raw as $v) {
                    $res[$v['type']] = new Mo_Stat(array(
                        'service' => $v['service'],
                        'type' => $v['type'],
                        'data' => json_decode($v['data']),
                        'timestamp' => $v['timestamp']
                    ));
                }
            }
        } else {
            if ($raw = $this->getDbHandler()->fetchAll('stats')) {
                foreach ($raw as $v) {
                    $res[$v['type']][$v['service']] = new Mo_Stat(array(
                        'service' => $v['service'],
                        'type' => $v['type'],
                        'data' => json_decode($v['data']),
                        'timestamp' => $v['timestamp']
                    ));
                }
            }
        }

        return $res;
    }

    /**
     * Saves the object in database.
     *
     * @return mixed Message ID on success; FALSE otherwise.
     */
    public function save(Mo_Stat $Mo_Stat)
    {
        $handler = $this->getDbHandler()->setDbDriver('default');
        $data = array(
            'service' => $Mo_Stat->service,
            'type' => $Mo_Stat->type,
            'data' => json_encode($Mo_Stat->data),
            'timestamp' => ($Mo_Stat->id ? $Mo_Stat->timestamp : date('Y-m-d H:i:s'))
        );

        if ($id = $Mo_Stat->id) {
            $handler->update('stats', $data, array('id' => $Mo_Stat->id));
        } else {
            $id = $handler->insert('stats', $data, array('update' => array('data', 'timestamp')));
        }

        return $id;
    }
}
