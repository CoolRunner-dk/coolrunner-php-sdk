<?php
/**
 * @package   coolrunner-api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK;

/**
 * Class CurlResponse
 *
 * @package CoolRunnerSDK
 */
class CurlResponse {
    protected $_data, $_info, $_errors;

    const HTTP_OK      = 200;
    const HTTP_CREATED = 201;

    const HTTP_UNAUTHORIZED         = 401;
    const HTTP_FORBIDDEN            = 403;
    const HTTP_NOT_FOUND            = 404;
    const HTTP_NOT_ACCEPTABLE       = 406;
    const HTTP_GONE                 = 410;
    const HTTP_IM_A_LITTLE_TEAPOT   = 418;
    const HTTP_UNPROCESSABLE_ENTITY = 422;
    const HTTP_TOO_MANY_REQUESTS    = 429;
    const HTTP_INTERNAL             = 500;
    const HTTP_SERVICE_UNAVAILABLE  = 503;

    /**
     * CurlResponse constructor.
     * Intented for use with CoolRunnerSDK
     *
     * @param string $data   Curl response data
     * @param array  $info   Curl error information
     * @param string $errors Curl response information
     */
    public function __construct($data, $info, $errors) {
        $this->_data = $data;
        $this->_info = $info;
        $this->_errors = $errors;
    }

    /**
     * @return int
     */
    public function getHttpResponseCode() {
        return $this->_info['http_code'];
    }

    /**
     * @param int $code HTTP response code
     *
     * @return bool True if HTTP reponse code matches, else false
     */
    public function is($code) {
        return $this->getHttpResponseCode() === intval($code);
    }

    /**
     * @return bool True if HTTP response code is 200, else false
     */
    public function isOk() {
        return $this->getHttpResponseCode() === self::HTTP_OK || $this->getHttpResponseCode() === self::HTTP_CREATED;
    }

    /**
     * @return bool True if HTTP response code is 401, else false
     */
    public function isUnauthorized() {
        return $this->is(self::HTTP_UNAUTHORIZED);
    }

    /**
     * @return bool True if HTTP response code is 403, else false
     */
    public function isForbidden() {
        return $this->is(self::HTTP_FORBIDDEN);
    }

    /**
     * @return bool True if HTTP response code is 404, else false
     */
    public function isNotFound() {
        return $this->is(self::HTTP_NOT_FOUND);
    }

    /**
     * @return bool True if HTTP response code is 406, else false
     */
    public function isNotAcceptable() {
        return $this->is(self::HTTP_NOT_ACCEPTABLE);
    }

    /**
     * @return bool True if HTTP response code is 410, else false
     */
    public function isGone() {
        return $this->is(self::HTTP_GONE);
    }

    /**
     * @return bool True if HTTP response code is 418, else false
     */
    public function isImALittleTeapot() {
        return $this->is(self::HTTP_IM_A_LITTLE_TEAPOT);
    }

    /**
     * @return bool True if HTTP response code is 422, else false
     */
    public function isUnprocessableEntity() {
        return $this->is(self::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @return bool True if HTTP response code is 429, else false
     */
    public function isTooManyRequests() {
        return $this->is(self::HTTP_TOO_MANY_REQUESTS);
    }

    /**
     * @return bool True if HTTP response code is 500, else false
     */
    public function isInternalError() {
        return $this->is(self::HTTP_INTERNAL);
    }

    /**
     * @return bool True if HTTP response code is 503, else false
     */
    public function isServiceUnavailable() {
        return $this->is(self::HTTP_INTERNAL);
    }

    /**
     * @return bool True is HTTP response code is an unspecified error code, else false
     */
    public function isError() {
        return !$this->isUnauthorized() && !$this->isNotFound() && !$this->isOk();
    }

    /**
     * @return string
     */
    public function getErrorMsg() {
        $obj = json_decode($this);

        return $obj && isset($obj->message) ? $obj->message : null;
    }

    /**
     * @return string Response data from the curl request
     */
    public function getData() {
        return $this->_data;
    }

    /**
     * @return string
     */
    public function __toString() {
        if (API::DEBUG_MODE) {
            return json_encode(json_decode($this->_data), constant('JSON_PRETTY_PRINT') ? 128 : 0);
        }

        return (string)$this->_data;
    }

    /**
     * Return Curl data as a string
     *
     * @return string
     */
    public function toString() {
        return (string)$this;
    }

    /**
     * Wrapper for json_decode($curl_response, $assoc, $depth, $options)
     *
     * @param bool $assoc
     * @param int  $depth
     * @param int  $options
     *
     * @return mixed
     */
    public function jsonDecode($assoc = false, $depth = 512, $options = 0) {
        return json_decode($this, $assoc, $depth, $options);
    }
}