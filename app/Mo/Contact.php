<?php

/**
 * Contact object model.
 */

Loader::mo(array('Base'), true);

class Mo_Contact extends Mo_Base
{
    /**
     * @var Message status.
     */
    const STATUS_DELETED = 'deleted';
    const STATUS_UNREAD = 'unread';
    const STATUS_READ = 'read';
    const STATUS_SUSPECT = 'suspect';

    /**
     * @var Message reason.
     */
    const REASON_ASK = 'ask';
    const REASON_COMPLAINT = 'complaint';
    const REASON_SUGGESTION = 'suggestion';

    /**
     * @var integer Contact ID.
     */
    protected $_id;

    /**
     * @var string Contact name.
     */
    protected $_name;

    /**
     * @var string Contact email.
     */
    protected $_email;

    /**
     * @var string Contact reason.
     */
    protected $_reason;

    /**
     * @var string Message.
     */
    protected $_description;

    /**
     * @var string Service name.
     */
    protected $_service;

    /**
     * @var string Client's device data.
     */
    protected $_deviceData;

    /**
     * @var string Client's IP.
     */
    protected $_ip;

    /**
     * @var string Client's user agent.
     */
    protected $_userAgent;

    /**
     * @var string Creation date.
     */
    protected $_timestamp;

    /**
     * @var string Message status.
     */
    protected $_status;

    ////
    // Setters and Getters
    ////

    public function setId($id)
    {
        $this->_id = (integer) $id;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setName($name)
    {
        $this->_name = (string) $name;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function setEmail($email)
    {
        $this->_email = (string) $email;
    }

    public function getEmail()
    {
        return $this->_email;
    }

    public function setReason($reason)
    {
        $this->_reason = (string) $reason;
    }

    public function getReason()
    {
        return $this->_reason;
    }

    public function setDescription($description)
    {
        $this->_description = (string) $description;
    }

    public function getDescription()
    {
        return $this->_description;
    }

    public function setService($name)
    {
        $this->_service = (string) $name;
    }

    public function getService()
    {
        return $this->_service;
    }

    public function setDeviceData($deviceData)
    {
        $this->_deviceData = (string) $deviceData;
    }

    public function getDeviceData()
    {
        return $this->_deviceData;
    }

    public function setIp($ip)
    {
        $this->_ip = (string) $ip;
    }

    public function getIp()
    {
        return $this->_ip;
    }

    public function setUserAgent($userAgent)
    {
        $this->_userAgent = (string) $userAgent;
    }

    public function getUserAgent()
    {
        return $this->_userAgent;
    }

    public function setTimestamp($timestamp)
    {
        $this->_timestamp = (string) $timestamp;
    }

    public function getTimestamp()
    {
        return $this->_timestamp;
    }

    public function setStatus($status)
    {
        $this->_status = (string) $status;
    }

    public function getStatus()
    {
        return $this->_status;
    }
}
