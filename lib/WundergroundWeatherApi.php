<?php

/**
 * This library contains functions to access the Wunderground Weather API.
 */

require dirname(__FILE__) . '/Curl.php';

class WundergroundWeatherApiException extends Exception {}

class WundergroundWeatherApi
{
    /**
     * @var array Data about the locations where the API can fetch data from.
     */
    private $_locations = array(

        // Buenos Aires
        'buenos_aires' => array(
            'Buenos Aires, Ciudad',
            'argentina/buenos_aires'
        ),
        'buenos_aires_ezeiza' => array(
            'Buenos Aires, Ezeiza',
            'argentina/ezeiza'
        ),
        'buenos_aires_bahiablanca' => array(
            'Buenos Aires, Bahía Blanca',
            'argentina/bahia_blanca'
        ),
        'buenos_aires_bolivar' => array(
            'Buenos Aires, Bolívar',
            'argentina/bolivar'
        ),
        'buenos_aires_laplata' => array(
            'Buenos Aires, La Plata',
            'argentina/la_plata'
        ),
        'buenos_aires_mardelplata' => array(
            'Buenos Aires, Mar del Plata',
            'argentina/mar_del_plata'
        ),
        'buenos_aires_olavarria' => array(
            'Buenos Aires, Olavarría',
            'argentina/olavarria'
        ),
        'buenos_aires_pehuajo' => array(
            'Buenos Aires, Pehuajó',
            'argentina/pehuajo'
        ),
        'buenos_aires_tandil' => array(
            'Buenos Aires, Tandil',
            'argentina/tandil'
        ),
/*
        // Catamarca
        'catamarca_sanfernandodelvalledecatamarca' => array(
            'Catamarca, Ciudad',
            'argentina/catamarca'
        ),
*/
        // Chaco
        'chaco_resistencia' => array(
            'Chaco, Ciudad',
            'argentina/resistencia'
        ),
/*
        // Chubut
        'chubut_trelew' => array(
            'Chubut, Trelew',
            'argentina/trelew'
        ),
*/
        // Cordoba
        'cordoba_cordoba' => array(
            'Córdoba, Ciudad',
            'argentina/cordoba'
        ),
/*
        // Corrientes
        'corrientes_corrientes' => array(
            'Corrientes, Ciudad',
            'argentina/corrientes'
        ),

        // Entre Ríos
        'entre_rios_gualeguaychu' => array(
            'Entre Ríos, Gualeguaychú',
            'argentina/gualeguaychu'
        ),

        // Formosa
        'formosa_formosa' => array(
            'Formosa, Ciudad',
            'argentina/formosa'
        ),

        // Jujuy
        'jujuy_jujuy' => array(
            'Jujuy, Ciudad',
            'argentina/jujuy'
        ),
*/
        // La Pampa
        'la_pampa_santarosa' => array(
            'La Pampa, Ciudad',
            'argentina/santa_rosa'
        ),
/*
        // La Rioja
        'la_rioja_larioja' => array(
            'La Rioja, Ciudad',
            'argentina/la_rioja'
        ),

        // Mendoza
        'mendoza_mendoza' => array(
            'Mendoza, Ciudad',
            'argentina/mendoza'
        ),

        // Misiones
        'misiones_iguazu' => array(
            'Misiones, Iguazú',
            'argentina/iguazu'
        ),

        // Neuquén
        'neuquen_neuquen' => array(
            'Neuquén, Ciudad',
            'argentina/neuquen'
        ),

        // Río Negro
        'rio_negro_bariloche' => array(
            'Río Negro, Bariloche',
            'argentina/bariloche'
        ),
        'rio_negro_viedma' => array(
            'Río Negro, Ciudad',
            'argentina/viedma'
        ),
*/
        // Salta
        'salta_salta' => array(
            'Salta, Ciudad',
            'argentina/salta'
        ),
/*
        // San Juan
        'san_juan_sanjuan' => array(
            'San Juan, Ciudad',
            'argentina/san_juan'
        ),

        // San Luis
        'san_luis_sanluis' => array(
            'San Luis, Ciudad',
            'argentina/san_luis'
        ),

        // Santa Fe
        'santa_fe_rafaela' => array(
            'Santa Fe, Rafaela',
            'argentina/rafaela'
        ),
        'santa_fe_rosario' => array(
            'Santa Fe, Rosario',
            'argentina/rosario'
        ),
        'santa_fe_santafe' => array(
            'Santa Fe, Ciudad',
            'argentina/santa_fe'
        ),

        // Santiago del Estero
        'santiago_del_estero_santiagodelestero' => array(
            'Santiago del Estero, Ciudad',
            'argentina/santiago_del_estero'
        ),

        // Tierra del Fuego
        'tierra_del_fuego_ushuaia' => array(
            'Tierra del Fuego, Ciudad',
            'argentina/ushuaia'
        ),
*/
        // Tucumán
        'tucuman_tucuman' => array(
            'Tucumán, Ciudad',
            'argentina/tucuman'
        )
    );

    /**
     * @var array Custom icons for weather condition.
     */
    private $_customIcons = array(
        'sunny' => 'weather-sun.png',
        'clear' => 'weather-sun.png',
        'mostlysunny' => 'weather-sun.png',
        'partlysunny' => 'weather-sun.png',
        'chancetstorms' => 'weather-storm.png',
        'tstorms' => 'weather-storm.png',
        'unknown' => 'weather-storm.png',
        'cloudy' => 'weather-cloud.png',
        'mostlycloudy' => 'weather-cloud.png',
        'partlycloudy' => 'weather-cloud.png',
        'chancerain' => 'weather-rain.png',
        'rain' => 'weather-rain.png',
        'rain_snow' => 'weather-rain.png',
        'fog' => 'weather-fog.png',
        'hazy' => 'weather-fog.png',
        'chancesnow' => 'weather-snow.png',
        'flurries' => 'weather-snow.png',
        'chanceflurries' => 'weather-snow.png',
        'snow' => 'weather-snow.png',
        'sleet' => 'weather-snow.png',
        'chancesleet' => 'weather-snow.png'
    );

    /**
     * @var array Config.
     */
    protected $_cfg = array(
        'key' => null,
        'uri' => 'http://api.wunderground.com/api/%KEY%/conditions/forecast/lang:SP/q/%LOCATION%.json'
    );

    /**
     * @var object CURL object.
     */
    private $_curl;

    /**
     * Builds a new WundergroundWeatherApi.
     *
     * @param array $options Initialization options.
     *                       'key': API key.
     * @return void.
     */
    public function __construct(array $options = null)
    {
        $this->_curl = new Curl();
        $this->_cfg['key'] = (isset($options['key']) ? $options['key'] : null);
    }

    /**
     * Retrieves the weather from a specific location.
     *
     * @param $location string Location to retrieve data from (check $this->_locations).
     * @return array Data about the weather from the specified location on success; FALSE otherwise.
     */
    public function fetch($location)
    {
        $res = false;
        $uri = str_replace(
            array('%KEY%', '%LOCATION%'),
            array($this->_cfg['key'], $this->_locations[$location][1]),
            $this->_cfg['uri']);
        try {
            if (($raw = $this->_curl->exec($uri)) && $raw = json_decode(utf8_encode($raw), true)) {
                if (isset($raw['response']['error'])) {
                    $error = $raw['response']['error'];
                    throw new WundergroundWeatherApiException(
                        "Type: '{$error['type']}' - Message: '{$error['description']}' - "
                        . "Location: '{$location}'.");
                }
                $wC = $raw['current_observation'];
                $wFc = $raw['forecast']['simpleforecast']['forecastday'];
                $res = array(
                    'location' => $this->_locations[$location][0],
                    'current' => array(
                        'description' => $wC['weather'],
                        'temperature' => $wC['temp_c'],
                        'humidity' => $wC['relative_humidity'],
                        'icon' => (isset($this->_customIcons[$wC['icon']]) ?
                            $this->_customIcons[$wC['icon']] : 'question.png'),
                    ),
                    'forecast' => array()
                );
                foreach ($wFc as $k => $v) {
                    $res['forecast'][] = array(
                        'day' => $v['date']['weekday'],
                        'low' => $v['low']['celsius'],
                        'high' => $v['high']['celsius'],
                        'icon' => (isset($this->_customIcons[$v['icon']]) ?
                            $this->_customIcons[$v['icon']] : 'question.png'),
                        'description' => $v['conditions']
                    );
                }

                // This is to remove "Humedad: ...%" from data
                preg_match('/(\d)+/', $res['current']['humidity'], $matches);
                $res['current']['humidity'] = current($matches);
            } else {
                throw new WundergroundWeatherApiException('Connection error.');
            }
        } catch (CurlException $e) {
            throw new WundergroundWeatherApiException($e);
        }

        return $res;
    }

    /**
     * Fetchs a list of all available locations.
     *
     * @return array List of available locations.
     */
    public function fetchLocations()
    {
        ksort($this->_locations);

        return $this->_locations;
    }
}
