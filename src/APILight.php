<?php
/**
 * @package   coolrunner-api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK;


use CoolRunnerSDK\Models\Error;
use CoolRunnerSDK\Models\Shipments\Shipment;
use CoolRunnerSDK\Models\Shipments\ShipmentInfo;

class APILight {
    protected static $_version         = null;
    protected static $_base_url        = null;
    protected static $_instance        = false;
    protected static $_default_headers = null;
    protected static $_raw             = false;
    protected static $_assoc           = true;

    protected static $token;

    /**
     * @var null|CurlResponse
     */
    protected $_last_response = null;

    /**
     * Carrier GLS
     */
    const CARRIER_GLS = 'GLS';
    /**
     * Carrier DAO
     */
    const CARRIER_DAO = 'DAO';
    /**
     * Carrier PostNord
     */
    const CARRIER_POSTNORD = 'postnord';

    /**
     * Debug mode
     * Alter JSON outputs to be beautified
     */
    const DEBUG_MODE = true;

    const OUTPUT_MODE_RAW    = 0;
    const OUTPUT_MODE_ASSOC  = 1;
    const OUTPUT_MODE_OBJECT = 2;

    /**
     * API constructor.
     *
     * @param $token
     */
    protected function __construct($token) {
        self::$token = $token;
        self::$_base_url = API::getBaseUrl();
        self::$_version = API::getVersion();
        self::$_default_headers = API::getDefaultHeaders();
    }

    /**
     * Load the CoolRunner SDK singleton
     *
     * @param string $email        E-mail used for integration with CoolRunner API v3
     * @param string $token        Token used for integration with CoolRunner API v3
     * @param string $developer_id [optional] Developer ID - If this library is used with a third party extention
     *                             please enter your extentions name and/or your company name
     * @param int    $mode         Change the output mode of the API (see output mode constants)
     *
     * @see APILight::OUTPUT_MODE_RAW
     * @return self
     */
    public static function load($email, $token, $developer_id = null, $mode = self::OUTPUT_MODE_ASSOC) {
        if (!is_null($developer_id) && (is_int($developer_id) || is_string($developer_id))) {
            self::$_default_headers['X-Developer-Id'] .= " | $developer_id";
        }

        if (InstanceStore::hasInstance(get_class()) === false) {
            $apitoken = base64_encode("$email:$token");
            self::$_raw = $mode === self::OUTPUT_MODE_RAW;
            self::$_assoc = $mode === self::OUTPUT_MODE_ASSOC;
            $new = new self($apitoken);
            $instance = InstanceStore::setInstance(get_class(), $new);
        } else {
            $instance = InstanceStore::getInstance(get_class());
        }

        return $instance;
    }

    /**
     * Get the current instance of CoolRunner API
     *
     * @return self|false CoolRunnerAPI if instance has been loaded, or false on failure
     */
    public static function &getInstance() {
        if (InstanceStore::hasInstance(get_class())) {
            return InstanceStore::getInstance(get_class());
        } else {
            $false = false;
            return $false;
        }
    }

    /**
     * Get the API v3 base url
     *
     * @return string
     */
    public static function getBaseUrl() {
        return static::$_base_url;
    }

    public function isRaw() {
        return self::$_raw;
    }

    public function isAssoc() {
        return self::$_assoc;
    }

    /**
     * Execute a cURL request against a given address with POST/GET and custom data
     *
     * If the request method is GET, then the supplied data will be url encoded and appended to the url.
     *
     * If the request method is POST, then the supplied data will be url encoded and sent with the request
     *
     * If enc_type is json, then the supplied data will be json encoded and sent with the request, along with appropriate content headers
     *
     * @param string $url
     * @param string $method   POST|GET
     * @param array  $data
     * @param string $enc_type Form encoding type. Allowed values: 'json', ''
     *
     * @return CurlResponse
     */
    public function get($url, $method = 'GET', $data = array(), $enc_type = '') {
        $opts = [
            CURLOPT_HTTPHEADER     => $this->buildHeaders(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
        ];

        switch (strtoupper($method)) {
            case 'GET':
                $url .= !empty($data) ? '?' . http_build_query($data) : '';
                break;
            case 'POST':
                $opts[CURLOPT_POST] = 1;
                $opts[CURLOPT_POSTFIELDS] = http_build_query($data);
                break;
            default:
                throw new \InvalidArgumentException("Parameter method must be either POST or GET for " . __METHOD__);
        }

        switch (strtolower($enc_type)) {
            case 'json':
                $opts[CURLOPT_POSTFIELDS] = json_encode($data);
                $opts[CURLOPT_HTTPHEADER] = array_merge(
                    array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($opts[CURLOPT_POSTFIELDS])
                    ), $opts[CURLOPT_HTTPHEADER]
                );
                break;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, $opts);

        $data = curl_exec($ch);
        $info = curl_getinfo($ch);
        $error = curl_error($ch);

        $this->_last_response = new CurlResponse($data, $info, $error);

        if ($this->_last_response->isUnauthorized() || $this->_last_response->isError() || $this->_last_response->isNotFound()) {
            Error::log($this->_last_response->getHttpResponseCode(), $this->_last_response->getErrorMsg());
        }

        curl_close($ch);

        return $this->_last_response;
    }

    /**
     * @return array
     */
    protected function buildHeaders() {
        $def_headers = self::$_default_headers;

        $def_headers['Authorization'] = 'Basic ' . self::$token;

        $headers = [];
        foreach ($def_headers as $key => $header) {
            $headers[] = "$key: $header";
        }

        return $headers;
    }

    /**
     * Find closest servicepoints for a specific address.
     *
     * @param string $carrier      Carrier to poll (DAO, GLS, postnord)
     * @param string $country_code ISO 3166-1 Alpha-2 format. eg. DK
     * @param string $zip_code     Zipcode. eg. 9000
     * @param string $city         <i>[Optional]</i> City. eg. Aalborg
     * @param string $street       <i>[Optional]</i> Street. eg. Slotsgade 8
     *
     * @return array|string|false
     *
     * @see https://docs.coolrunner.dk/v3/#find CoolRunner API v3 Docs Servicepoints/Find
     */
    public function findServicepoints($carrier, $country_code, $zip_code, $city = '', $street = '') {
        $url = self::getBaseUrl() . "servicepoints/$carrier";

        $data = ['country_code' => $country_code, 'zip_code' => $zip_code, 'city' => $city, 'street' => $street];

        $resp = $this->get($url, 'GET', $data);

        $resp->getErrorMsg();

        if ($resp->isOk()) {
            return self::$_raw ? $resp->getData() : $resp->jsonDecode('true');
        } else {
            return false;
        }
    }

    /**
     * Get servicepoint by ID
     *
     * @param string $carrier Carrier to poll (DAO, GLS, postnord)
     * @param int    $id      Servicepoint id. eg. 97405
     *
     * @return array|bool Associative array matching return data
     *
     * @see https://docs.coolrunner.dk/v3/#find CoolRunner API v3 Docs Servicepoints/Get
     */
    public function getServicepoint($carrier, $id) {
        $url = self::getBaseUrl() . "servicepoints/$carrier/$id";

        $resp = $this->get($url, 'GET');

        if ($resp->isOk()) {
            return self::$_raw ? $resp->getData() : $resp->jsonDecode('true');
        } else {
            return false;
        }
    }

    /**
     * @param string $from_country_code
     * @param string $to_country_code
     *
     * @return array|bool
     *
     * @see https://docs.coolrunner.dk/v3/#get-3 CoolRunner API v3 Docs Products/Get
     */
    public function getProducts($from_country_code, $to_country_code = '') {
        $url = self::getBaseUrl() . "products/$from_country_code";

        $resp = $this->get($url, 'GET');

        if ($resp->isOk()) {
            return self::$_raw ? $resp->getData() : $resp->jsonDecode('true');
        } else {
            return false;
        }
    }

    /**
     * @param Shipment $shipment
     *
     * @return ShipmentInfo|false
     */
    public function createShipment($shipment) {
        if (is_object($shipment) && get_class($shipment) === Shipment::class) {
            return $shipment->create();
        }

        return false;
    }

    /**
     * @param $package_number
     *
     * @return bool|array|\stdClass|string
     */
    public function getShipment($package_number) {
        $url = self::getBaseUrl() . "shipments/$package_number";

        $resp = $this->get($url, 'GET');

        if ($resp->isOk()) {
            return self::$_raw ? $resp->getData() : $resp->jsonDecode(self::$_assoc);
        }
        return false;
    }


    /**
     * @param $package_number
     *
     * @return bool|string
     */
    public function getShipmentLabel($package_number) {
        $url = self::getBaseUrl() . "shipments/$package_number/label";

        $resp = $this->get($url, 'GET');

        if ($resp->isOk()) {
            return $resp->getData();
        }
        return false;
    }

    /**
     * @param $package_number
     *
     * @return bool|string
     */
    public function getShipmentTracking($package_number) {
        $url = self::getBaseUrl() . "shipments/$package_number/tracking";

        $resp = $this->get($url, 'GET');

        if ($resp->isOk()) {
            return self::$_raw ? $resp->getData() : $resp->jsonDecode(self::$_assoc);
        }
        return false;
    }
}