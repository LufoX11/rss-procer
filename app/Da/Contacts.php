<?php

/**
 * Contacts data access.
 */

Loader::da(array('BaseMysql'), true);

class Da_Contacts extends Da_BaseMysql
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
     * Fetchs all contact messages.
     *
     * @param $all boolean Whether we should retrieve all messages, including 'deleted'.
     * @param $limit integer Amount of results to retrieve.
     * @param $offset integer Registers offset to start retrieving.
     * @return mixed Contact messages data on success; FALSE otherwise.
     */
    public function fetch($all = false, $limit = null, $offset = null)
    {
        $this->setDbAccessHandlerType(self::HANDLER_READ);
        $limit = (is_null($limit) ? 30 : $limit);
        $offset = (is_null($offset) ? 0 : $offset);
        $inClause = implode(', ', array(
            $this->getHandler()->quote(Mo_Contact::STATUS_READ),
            $this->getHandler()->quote(Mo_Contact::STATUS_UNREAD)));

        $res = $this->query(sprintf(''
            . 'SELECT * FROM contact '
            . (!$all ? "WHERE status IN ({$inClause}) " : '')
            . 'ORDER by id DESC '
            . 'LIMIT %d, %d',
            $offset,
            $limit),
            'default');

        return $res;
    }
}
