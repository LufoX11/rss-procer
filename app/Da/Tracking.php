<?php

/**
 * Contacts data access.
 */

Loader::da(array('BaseMongo'), true);

class Da_Tracking extends Da_BaseMongo
{
    /**
     * Fetches OVERALL_CLICKS.
     *
     * @param $service string Service name.
     * @return array An array of clicks data on success; FALSE otherwise.
     */
    public function fetchOverallClicks($service)
    {
        $limitDate = array(
            'DAY' => strtotime('-1 month'), // 1 Month ago
            'MONTH' => strtotime('-6 month') // 6 Months ago
        );
        $res = array(
            'byDay' => array(),
            'byMonth' => array()
        );
        foreach (array('DAY', 'MONTH') as $v) {
            $raw = $this->group(
                array('source', 'action', 'extra'),
                array(
                    'service' => $service,
                    'timestamp' => array('$gt' => new MongoDate($limitDate[$v]))
                ),
                array(
                    'byDate' => $v
                ));
            if ($raw['count']) {
                $res['by' . ucfirst(strtolower($v))] = $raw['retval'];
            }
        }

        return $res;
    }
}
