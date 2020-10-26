<?php

/**
 * Tracking mapper.
 */

Loader::mo(array('Abstract'), true);
Loader::da(array('Tracking'), true);

class Mo_TrackingMapper extends Mo_Abstract
{
    /**
     * @see parent.
     */
    public function getDbHandler()
    {
        if (!$this->_dbHandler) {
            $this->setDbHandler('Da_Tracking');
        }

        return $this->_dbHandler;
    }

    /**
     * Saves the object in database.
     *
     * @return mixed Message ID on success; FALSE otherwise.
     */
    public function save(Mo_Tracking $Mo_Tracking)
    {
        $data = array(
            'service' => $Mo_Tracking->service,
            'source' => $Mo_Tracking->source,
            'action' => $Mo_Tracking->action,
            'extra' => $Mo_Tracking->extra,
            'deviceid' => $Mo_Tracking->deviceid,
            'devicetype' => $Mo_Tracking->devicetype,
            'deviceversion' => $Mo_Tracking->deviceversion,
            'appversion' => $Mo_Tracking->appversion,
            'timestamp' => $Mo_Tracking->timestamp
        );

        $res = $this->getDbHandler()->insert($data);

        return $res;
    }
}
