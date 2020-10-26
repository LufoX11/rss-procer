<?php

/**
 * Applet name: Weather.
 * Displays data about the current weather and date in the header navbar.
 */

Loader::mo(array('Abstract', 'Weather', 'FrontCache'), true);

class Mo_Applet_Weather extends Mo_Abstract
{
    /**
     * @const MC keys.
     */
    const MC_KEY_BY_LOCATION = 'Mo_Applet_Weather::location-%s::1';
    const MC_TIME = 3600; // 1 Hour.

    /**
     * @var array Weather data (for multiple gets).
     *            Array
     *            (
     *                [<location>] => Array
     *                    (
     *                        <data>
     *                    )
     *            )
     */
    protected static $_data;

    /**
     * @var string Location to retrieve data from.
     */
    protected $_location;

    /**
     * @var object Datasource to use (library).
     */
    protected $_ds;

    /**
     * Initializes the object.
     *
     * @param $options array Options for the Applet.
     *
     *                       Among the options, you MUST include 'cfg' => $appCfg:
     *                       $options = array(<your_custom_settings>, 'cfg' => $appCfg)
     *
     *                       Other options:
     *                       'ds' => array(
     *                           'name': Library class name to initialize (from /lib).
     *                           'options' => Initialization options for the library.
     *                       ),
     *                       'location': Location name to retrieve the weather.
     * @return void.
     */
    public function __construct(array $options)
    {
        parent::__construct($options['cfg']);

        Loader::lib(array($options['ds']['name']), true);

        $this->_location = $options['location'];
        $this->_ds = new $options['ds']['name']($options['ds']['options']);
    }

    /**
     * Retrieves weather data.
     *
     * @return mixed ARRAY with weather data on success; FALSE otherwise.
     */
    public function fetch()
    {
        try {
            $mcKey = sprintf(self::MC_KEY_BY_LOCATION, $this->_location);
            $cacheHandler = $this->getCacheHandler();

            // From Local cache
            if (($res = $this->_fetchFromLocalCache()) === null) {

                // From Cache
                if (($res = $cacheHandler->fetch($mcKey)) === null) {

                    // From API
                    if ($res = $this->_ds->fetch($this->_location)) {
                        $res = new Mo_Weather(array(
                            'location' => $res['location'],
                            'current' => $res['current'],
                            'forecast' => $res['forecast']
                        ));
                    }
                    $this->_saveInCache($res);
                } else {
                    self::$_data[$this->_location] = $res;
                }
            }
        } catch (Exception $e) {

            // Save to avoid hitting ds again
            $res = false;
            $this->_saveInCache($res);
        }

        return $res;
    }

    /**
     * Fetchs a list of all available locations in the API.
     *
     * @return array List of available locations.
     */
    public function fetchLocations()
    {
        $res = $this->_ds->fetchLocations();

        return $res;
    }

    /**
     * Retrieves weather data from local cache.
     *
     * @return mixed ARRAY with data on success; NULL otherwise.
     */
    protected function _fetchFromLocalCache()
    {
        $res = null;
        if (isset(self::$_data[$this->_location])) {
            $res = self::$_data[$this->_location];
        }

        return $res;
    }

    /**
     * Saves data in Cache.
     *
     * @param $data mixed Data to save.
     * @return void.
     */
    protected function _saveInCache($data)
    {
        $mcKey = sprintf(self::MC_KEY_BY_LOCATION, $this->_location);
        $cacheHandler = $this->getCacheHandler();

        self::$_data[$this->_location] = $data;
        $cacheHandler->store($mcKey, $data, self::MC_TIME);
        Mo_FrontCache::increment();
    }
}
