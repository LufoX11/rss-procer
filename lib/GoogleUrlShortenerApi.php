<?php

/**
 * This library contains functions to connect to Goo.gl API.
 *
 * ---
 * Notes:
 *
 * 1) Rate limit: While it's not official, it's supposed to be rate limits between hits:
 *        a) X per USER per SEC / MINUTE / HOUR / IP.
 *        b) 1000000 million total per DAY (this is official and for free accounts).
 *    If limits are overpassed, we will receive a 403 HTTP error.
 *
 * ---
 * Use:
 *
 * $api = new GoogleUrlShortenerApi(array(
 *     'apiKey' => [API key]));
 */

require_once dirname(__FILE__) . '/Curl.php';

class GoogleUrlShortenerApiException extends Exception {}

class GoogleUrlShortenerApi
{
    /**
     * @var object CURL instance.
     */
    protected $_Curl;

    /**
     * @var array Initial config.
     */
    protected $_cfg = array(
        'requestUrl' => 'https://www.googleapis.com/urlshortener/v1',
        'apiKey' => ''
    );

    /**
     * Initializes GooglApi object.
     *
     * @param $options array Initialization options.
     * @return void.
     */
    public function __construct(array $options = null)
    {
        $this->_Curl = new Curl(array(
            CURLOPT_HTTPHEADER => array('Content-type: application/json')
        ));
        if (isset($options['apiKey'])) {
            $this->_cfg['apiKey'] = $options['apiKey'];
        }
    }

    /**
     * Encodes and shorts an URI.
     *
     * @param $uri string URI to short.
     * @return string Shortened URI on success; Empty string otherwise.
     */
    public function shorten($uri)
    {
        $res = '';
        $requestUrl = $this->_cfg['requestUrl'] . sprintf('/url?key=%s', $this->_cfg['apiKey']);
        $params = array(
            'longUrl' => $uri
        );
        try {
            if ($raw = $this->_Curl->exec($requestUrl, $params, 'post')) {
                $raw = json_decode($raw, true);
                if (!isset($raw['error']) && isset($raw['id'])) {
                    $res = $raw['id'];
                } else {
                    throw new GoogleUrlShortenerApiException('Fail when shortening.');
                }
            }
        } catch (CurlException $e) {
            switch ($e->getCode()) {
                case 400:
                    // No problem, this means that invalid data was sent.
                    break;

                case 403:
                    throw new GoogleUrlShortenerApiException('Hits have reached the Google rate limit.');
            }
        }

        return $res;
    }
}
