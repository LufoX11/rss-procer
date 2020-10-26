<?php

/**
 * This library contains functions to access the Google Weather public API.
 */

libxml_use_internal_errors(true);

require dirname(__FILE__) . '/Curl.php';

class GoogleWeatherApiException extends Exception {}

class GoogleWeatherApi
{
    /**
     * @var array Data about the locations where the API can fetch data from.
     */
    private $_locations = array(

        // Buenos Aires
        'buenos_aires' => array(
            'Buenos Aires, Ciudad',
            'argentina,buenos aires'
        ),
        'buenos_aires_ezeiza' => array(
            'Buenos Aires, Ezeiza',
            'argentina,buenos aires,ezeiza'
        ),
        'buenos_aires_bahiablanca' => array(
            'Buenos Aires, Bahía Blanca',
            'argentina,buenos aires,bahia blanca'
        ),
        'buenos_aires_bolivar' => array(
            'Buenos Aires, Bolívar',
            'argentina,buenos aires,bolivar'
        ),
        'buenos_aires_laplata' => array(
            'Buenos Aires, La Plata',
            'argentina,buenos aires,la plata'
        ),
        'buenos_aires_mardelplata' => array(
            'Buenos Aires, Mar del Plata',
            'argentina,buenos aires,mar del plata'
        ),
        'buenos_aires_olavarria' => array(
            'Buenos Aires, Olavarría',
            'argentina,buenos aires,olavarria'
        ),
        'buenos_aires_pehuajo' => array(
            'Buenos Aires, Pehuajó',
            'argentina,buenos aires,pehuajo'
        ),
        'buenos_aires_saladillo' => array(
            'Buenos Aires, Saladillo',
            'argentina,buenos aires,saladillo'
        ),
        'buenos_aires_tandil' => array(
            'Buenos Aires, Tandil',
            'argentina,buenos aires,tandil'
        ),
/*
        // Catamarca
        'catamarca_sanfernandodelvalledecatamarca' => array(
            'Catamarca, Ciudad',
            'argentina,catamarca,catamarca'
        ),
*/
        // Chaco
        'chaco_resistencia' => array(
            'Chaco, Ciudad',
            'argentina,chaco,resistencia'
        ),
/*
        // Chubut
        'chubut_trelew' => array(
            'Chubut, Trelew',
            'argentina,chubut,trelew'
        ),

        // Cordoba
        'cordoba_cordoba' => array(
            'Córdoba, Ciudad',
            'argentina,cordoba,cordoba'
        ),

        // Corrientes
        'corrientes_corrientes' => array(
            'Corrientes, Ciudad',
            'argentina,corrientes,corrientes'
        ),

        // Entre Ríos
        'entre_rios_gualeguaychu' => array(
            'Entre Ríos, Gualeguaychú',
            'argentina,entre rios,gualeguaychu'
        ),

        // Formosa
        'formosa_formosa' => array(
            'Formosa, Ciudad',
            'argentina,formosa,formosa'
        ),

        // Jujuy
        'jujuy_jujuy' => array(
            'Jujuy, Ciudad',
            'argentina,jujuy,jujuy'
        ),
*/
        // La Pampa
        'la_pampa_santarosa' => array(
            'La Pampa, Ciudad',
            'argentina,la pampa,santa rosa'
        ),
/*
        // La Rioja
        'la_rioja_larioja' => array(
            'La Rioja, Ciudad',
            'argentina,la rioja,la rioja'
        ),

        // Mendoza
        'mendoza_mendoza' => array(
            'Mendoza, Ciudad',
            'argentina,mendoza,mendoza'
        ),

        // Misiones
        'misiones_iguazu' => array(
            'Misiones, Iguazú',
            'argentina,misiones,iguazu'
        ),

        // Neuquén
        'neuquen_neuquen' => array(
            'Neuquén, Ciudad',
            'argentina,neuquen,neuquen'
        ),

        // Río Negro
        'rio_negro_bariloche' => array(
            'Río Negro, Bariloche',
            'argentina,rio negro,bariloche'
        ),
        'rio_negro_viedma' => array(
            'Río Negro, Ciudad',
            'argentina,rio negro,viedma'
        ),
*/
        // Salta
        'salta_salta' => array(
            'Salta, Ciudad',
            'argentina,salta,salta'
        ),
/*
        // San Juan
        'san_juan_sanjuan' => array(
            'San Juan, Ciudad',
            'argentina,san juan,san juan'
        ),

        // San Luis
        'san_luis_sanluis' => array(
            'San Luis, Ciudad',
            'argentina,san luis,san luis'
        ),

        // Santa Fe
        'santa_fe_rafaela' => array(
            'Santa Fe, Rafaela',
            'argentina,santa fe,rafaela'
        ),
        'santa_fe_rosario' => array(
            'Santa Fe, Rosario',
            'argentina,santa fe,santa fe'
        ),
        'santa_fe_santafe' => array(
            'Santa Fe, Ciudad',
            'argentina,santa fe,santa fe'
        ),

        // Santiago del Estero
        'santiago_del_estero_santiagodelestero' => array(
            'Santiago del Estero, Ciudad',
            'argentina,santiago del estero,santiago del estero'
        ),

        // Tierra del Fuego
        'tierra_del_fuego_ushuaia' => array(
            'Tierra del Fuego, Ciudad',
            'argentina,tierra del fuego,ushuaia'
        ),
*/
        // Tucumán
        'tucuman_tucuman' => array(
            'Tucumán, Ciudad',
            'argentina,tucuman,tucuman'
        )
    );

    /**
     * @var array Custom icons for weather condition.
     */
    private $_customIcons = array(
        'sunny.gif' => 'weather-sun.png',
        'mostly_sunny.gif' => 'weather-sun.png',
        'chance_of_storm.gif' => 'weather-storm.png',
        'thunderstorm.gif' => 'weather-storm.png',
        'storm.gif' => 'weather-storm.png',
        'cloudy.gif' => 'weather-cloud.png',
        'mostly_cloudy.gif' => 'weather-cloud.png',
        'partly_cloudy.gif' => 'weather-cloud.png',
        'chance_of_rain.gif' => 'weather-rain.png',
        'showers.gif' => 'weather-rain.png',
        'rain_snow.gif' => 'weather-rain.png',
        'rain.gif' => 'weather-rain.png',
        'fog.gif' => 'weather-fog.png',
        'foggy.gif' => 'weather-fog.png',
        'smoke.gif' => 'weather-fog.png',
        'mist.gif' => 'weather-fog.png',
        'haze.gif' => 'weather-fog.png',
        'dust.gif' => 'weather-fog.png',
        'chance_of_snow.gif' => 'weather-snow.png',
        'flurries.gif' => 'weather-snow.png',
        'snow.gif' => 'weather-snow.png',
        'icy.gif' => 'weather-snow.png',
        'sleet.gif' => 'weather-snow.png'
    );

    /**
     * @var array Config.
     */
    protected $_cfg = array(
        'uri' => 'http://www.google.com/ig/api'
    );

    /**
     * @var object CURL object.
     */
    private $_curl;

    /**
     * Builds a new GoogleWeatherApi.
     *
     * @return void.
     */
    public function __construct()
    {
        $this->_curl = new Curl();
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
        $params = array(
            'weather' => $this->_locations[$location][1],
            'hl' => 'es'
        );
        try {
            if (($raw = $this->_curl->exec($this->_cfg['uri'], $params))
                    && $raw = simplexml_load_string(utf8_encode($raw))) {
                if (!($w = $raw->weather[0]) || !($wFi = $w->forecast_information[0])) {
                    throw new GoogleWeatherApiException("Received data error for location: {$location}.");
                }
                $wC = $w->current_conditions[0];
                $wFc = $w->forecast_conditions;
                $res = array(
                    'location' => $this->_locations[$location][0],
                    'current' => array(
                        'description' => (string) $wC->condition[0]->attributes()->data,
                        'temperature' => (string) $wC->temp_c[0]->attributes()->data,
                        'humidity' => (string) $wC->humidity[0]->attributes()->data,
                        'icon' => (isset($this->_customIcons[basename($wC->icon[0]->attributes()->data)]) ?
                            $this->_customIcons[basename($wC->icon[0]->attributes()->data)] : 'question.png'),
                    ),
                    'forecast' => array()
                );
                foreach ($wFc as $k => $v) {
                    $res['forecast'][] = array(
                        'day' => (string) $v->day_of_week[0]->attributes()->data,
                        'low' => (string) $v->low[0]->attributes()->data,
                        'high' => (string) $v->high[0]->attributes()->data,
                        'icon' => (isset($this->_customIcons[basename($v->icon[0]->attributes()->data)]) ?
                            $this->_customIcons[basename($v->icon[0]->attributes()->data)] : 'question.png'),
                        'description' => (string) $v->condition[0]->attributes()->data
                    );
                }

                // This is to remove "Humedad: ...%" from data
                preg_match('/(\d)+/', $res['current']['humidity'], $matches);
                $res['current']['humidity'] = current($matches);
            } else {
                throw new GoogleWeatherApiException('Connection error.');
            }
        } catch (CurlException $e) {
            throw new GoogleWeatherApiException($e);
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
