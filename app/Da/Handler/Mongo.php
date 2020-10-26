<?php

/**
 * MongoDB handler.
 */

Loader::vendor(array('SimpleMongoDb/SimpleMongoDb'), true);

class Da_Handler_MongoException extends Exception {}

class Da_Handler_Mongo
{
    /**
     * @var string Default driver to use.
     */
    protected $_defaultDriver = 'default';

    /**
     * @var string Default collection.
     */
    protected $_defaultCollection = 'tracking';

    /**
     * @var array Configuration for the handler.
     */
    protected $_cfg;

    /**
     * @var object Database handler.
     */
    protected $_handler = null;

    /**
     * Builds a new handler.
     *
     * @param $cfg array Configuration for the handler.
     * @return void.
     */
    public function __construct(array $cfg)
    {
        $this->_cfg = $cfg;
    }
 
    /**
     * Sets the database driver to use.
     *
     * @param $driver string Driver name.
     * @return object Self object.
     */
    public function setDbDriver($driver)
    {
        $this->_defaultDriver = $driver;

        return $this;
    }

    /**
     * Sets the collection.
     *
     * @param $name string Collection name.
     * @return object Self object.
     */
    public function setDbCollection($name)
    {
        $this->_defaultCollection = $name;

        return $this;
    }

    /**
     * Gets the database driver.
     *
     * @return string Database driver.
     */
    public function getDbDriver()
    {
        if (!$this->_defaultDriver) {
            $this->setDbDriver('default');
        }

        return $this->_defaultDriver;
    }

    /**
     * Gets the database collection.
     *
     * @return string Database collection.
     */
    public function getDbCollection()
    {
        if (!$this->_defaultCollection) {
            $this->setDbCollection('tracking');
        }

        return $this->_defaultCollection;
    }

    /**
     * Inserts a new Mongo object.
     *
     * @param $data array Data to save.
     * @param $options array Saving options:
     *                       Array(
     *                           'driver' => (string) <default>: Mongo driver to use,
     *                           'collection' => (string) <tracking>: Collection name
     *                       )
     * @param $collection string Collection name.
     * @return boolean TRUE on success; FALSE otherwise.
     */
    public function insert(array $data, array $options = null)
    {
        if (isset($options['driver'])) {
            $this->setDbDriver($options['driver']);
        }
        if (isset($options['collection'])) {
            $this->setDbCollection($options['collection']);
        }
        $res = $this->getHandler()->save($this->getDbCollection(), $data);

        return $res;
    }

    /**
     * Fetchs a rowset.
     *
     * @param $where array Conditions for retrieving. Ie.: Array('id' => 1, 'name' => 'Peter').
     * @param $cols array Return only from specific columns. Ie.: Array('id', 'name').
     * @param $options array Options for the fetch.
     *                       Array(
     *                           'driver' => (string) <default>: Mongo driver to use,
     *                           'collection' => (string) <tracking>: Collection name
     *                           'sort' => (array) '<col_name>' => [1|-1]: 1 = ASC; -1 = DESC,
     *                           'limit' => (integer) '<number>'
     *                       )
     * @return array A rowset on success; NULL otherwise.
     */
    public function fetchAll(array $where = array(), array $cols = array(), array $options = null)
    {
        if (isset($options['driver'])) {
            $this->setDbDriver($options['driver']);
        }
        if (isset($options['collection'])) {
            $this->setDbCollection($options['collection']);
        }
        if ($cols) {
            $options['fields'] = $cols;
        }

        $res = null;
        if ($raw = $this->getHandler()->finda($this->getDbCollection(), $where, $options)) {
            $res = $raw;
        }

        return $res;
    }

    /**
     * Performs an operation similar to SQL's GROUP BY command.
     *
     * @param $cols array Fields to group by. Ie.: Array('id', 'name').
     * @param $where array Conditions for retrieving. Ie.: Array('id' => 1, 'name' => 'Peter').
     * @param $options array Options for the fetch.
     *                       Array(
     *                           'driver' => (string) <default>: Mongo driver to use,
     *                           'collection' => (string) <tracking>: Collection name,
     *                           'byDate' => (string)  [DAY|MONTH|YEAR]
     *                           'byDateFieldName' => (string) Name of the date field. Default is 'timestamp'.
     *                       )
     * @return array A rowset on success; NULL otherwise.
     */
    public function group(array $cols, array $where = array(), array $options = null)
    {
        if (isset($options['driver'])) {
            $this->setDbDriver($options['driver']);
        }
        if (isset($options['collection'])) {
            $this->setDbCollection($options['collection']);
        }
        $initial = array('count' => 0);
        $reduce = 'function (obj, prev) { prev.count ++; }';
        if (isset($options['byDate'])) {
            switch (strtoupper($options['byDate'])) {
                case 'DAY':
                    $groupingKey = "date.getFullYear() + '/' + (date.getMonth() + 1) + '/' + date.getDate()";
                    break;

                case 'MONTH':
                    $groupingKey = "date.getFullYear() + '/' + (date.getMonth() + 1) + '/1'";
                    break;

                case 'YEAR': default:
                    $groupingKey = "date.getFullYear() + '/1/1'";
                    break;
            }
            $fieldName = (isset($options['byDateFieldName']) ? $options['byDateFieldName'] : 'timestamp');
            $initial = array('count' => array());
            $reduce = "function (obj, prev) {
                var date = new Date(obj.{$fieldName});
                var dateKey = {$groupingKey};
                if (typeof prev.count[dateKey] == 'undefined') {
                    prev.count[dateKey] = 1;
                } else {
                    prev.count[dateKey] ++;
                }
            }";
        }
        $keys = array();
        array_walk($cols, function ($v, $k) use (&$keys) { $keys[$v] = true; });

        $res = null;
        if ($raw = $this->getHandler()->group($this->getDbCollection(), $keys, $initial, $reduce, $where)) {
            $res = $raw;
        }

        return $res;
    }
 
    /**
     * Gets connection handler.
     *
     * @param $driver string MySQL driver to use.
     * @param $collection string Collection name.
     * @return object Connection handler.
     */
    public function getHandler($driver = null, $collection = null)
    {
        $cfg = $this->_cfg;
        if (!$driver) {
            $driver = $this->getDbDriver();
        }
        if (!$collection) {
            $collection = $this->getDbCollection();
        }
        if (!$cfg) {
            throw new Da_Handler_MongoException('$cfg not initializated.');
        }
        if (!isset($this->_handler)) {
            $driverCfg = $cfg['databases']['mongodb'][$driver];
            $Mongo = new Mongo("{$driverCfg['host']}:{$driverCfg['port']}", array(
                'username' => $driverCfg['user'],
                'password' => $driverCfg['password'],
                'db' => $driverCfg['database']
            ));
            $this->_handler = new SimpleMongoDb();
            $this->_handler->addConnection($Mongo, $driverCfg['database'], (array) $collection);
        }

        return $this->_handler;
    }
}
