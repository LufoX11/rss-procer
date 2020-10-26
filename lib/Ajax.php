<?php

/**
 * AJAX responses helper.
 */

abstract class Ajax
{
    /**
     * @var array Additional headers to add to the response.
     */
    public static $additionalHeaders = array();

    /**
     * Sets a "successfull" response.
     *
     * @param $Slim object A Slim object.
     * @param $data array Data to return.
     * @return void.
     */
    public static function setSuccess($Slim, array $data = null)
    {
        if (!$data) {
            $data = array('Successful request.');
        }
        self::_setResponse($Slim, 200, $data);
    }

    /**
     * Sets an internal error response.
     *
     * @param $Slim object A Slim object.
     * @param $data array Data to return.
     * @return void.
     */
    public static function setError($Slim, array $data = null)
    {
        if (!$data) {
            $data = array('Request error.');
        }
        self::_setResponse($Slim, 500, $data);
    }

    /**
     * Sets a "not found" response.
     *
     * @param $Slim object A Slim object.
     * @param $data array Data to return.
     * @return void.
     */
    public static function setNotFound($Slim, array $data = null)
    {
        if (!$data) {
            $data = array('Not found.');
        }
        self::_setResponse($Slim, 404, $data);
    }

    /**
     * Sets a "forbidden" response.
     *
     * @param $Slim object A Slim object.
     * @param $data array Data to return.
     * @return void.
     */
    public static function setForbidden($Slim, array $data = null)
    {
        if (!$data) {
            $data = array('Forbidden action.');
        }
        self::_setResponse($Slim, 403, $data);
    }

    /**
     * Sets a "bad request" response.
     *
     * @param $Slim object A Slim object.
     * @param $data array Data to return.
     * @return void.
     */
    public static function setBadRequest($Slim, array $data = null)
    {
        if (!$data) {
            $data = array('Bad request.');
        }
        self::_setResponse($Slim, 400, $data);
    }

    /**
     * Sets the final response object.
     *
     * @param $Slim object A Slim object.
     * @param $code integer HTTP code.
     * @param $data array Data to return.
     * @return void.
     */
    protected static function _setResponse($Slim, $code, array $data = null)
    {
        if (!$data) {
            $data = array('Unknown.');
        }
        foreach (self::$additionalHeaders as $k => $v) {
            $Slim->response()->header($k, $v);
        }
        $Slim->response()->status($code);
        $Slim->response()->header('Content-type', 'application/json');
        $Slim->response()->body(json_encode($data));
        $Slim->stop();
    }
}
