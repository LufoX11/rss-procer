<?php

/**
 * Contact mapper.
 */

Loader::mo(array('Abstract', 'Contact'), true);
Loader::da(array('Contacts'), true);

class Mo_ContactMapper extends Mo_Abstract
{
    /**
     * @see parent.
     */
    public function getDbHandler()
    {
        if (!$this->_dbHandler) {
            $this->setDbHandler('Da_Contacts');
        }

        return $this->_dbHandler;
    }

    /**
     * Fetchs all contact messages.
     *
     * @param $limit integer Amount of results to retrieve.
     * @param $offset integer Registers offset to start retrieving.
     * @return array A collection of Mo_Contact objects on success; EMPTY ARRAY otherwise.
     */
    public function fetch($limit = null, $offset = null)
    {
        // From Database
        $res = array();
        if ($raw = $this->getDbHandler()->fetch(false, $limit, $offset)) {
            foreach ($raw as $v) {
                $res[$v['id']] = new Mo_Contact(array(
                    'id' => $v['id'],
                    'name' => $v['name'],
                    'email' => $v['email'],
                    'reason' => $v['reason'],
                    'description' => $v['description'],
                    'service' => $v['service'],
                    'deviceData' => $v['devicedata'],
                    'ip' => $v['ip'],
                    'userAgent' => $v['useragent'],
                    'timestamp' => $v['timestamp'],
                    'status' => $v['status']
                ));
            }
        }

        return $res;
    }

    /**
     * Finds a contact message by ID.
     *
     * @param $id integer Contact ID.
     * @return mixed Mo_Contact if found; NULL otherwise.
     */
    public function findById($id)
    {
        if ($res = $this->getDbHandler()->fetchRow('contact', array('id' => $id))) {
            $res = new Mo_Contact(array(
                'id' => $res['id'],
                'name' => $res['name'],
                'email' => $res['email'],
                'reason' => $res['reason'],
                'description' => $res['description'],
                'service' => $res['service'],
                'deviceData' => $res['devicedata'],
                'ip' => $res['ip'],
                'userAgent' => $res['useragent'],
                'timestamp' => $res['timestamp'],
                'status' => $res['status']
            ));
        }

        return $res;
    }

    /**
     * Saves the object in database.
     *
     * @return mixed Message ID on success; FALSE otherwise.
     */
    public function save(Mo_Contact $Mo_Contact)
    {
        $data = array(
            'name' => $Mo_Contact->name,
            'email' => $Mo_Contact->email,
            'reason' => $Mo_Contact->reason,
            'description' => $Mo_Contact->description,
            'service' => $Mo_Contact->service,
            'devicedata' => $Mo_Contact->deviceData,
            'ip' => $Mo_Contact->ip,
            'useragent' => $Mo_Contact->userAgent,
            'ip' => $Mo_Contact->ip,
            'timestamp' => ($Mo_Contact->id ? $Mo_Contact->timestamp : date('Y-m-d H:i:s')),
            'status' => $Mo_Contact->status
        );

        if ($id = $Mo_Contact->id) {
            $this->getDbHandler()->update('contact', $data, array('id' => $Mo_Contact->id));
        } else {
            $id = $this->getDbHandler()->insert('contact', $data);
        }

        return $id;
    }
}
