<?php
/**
 * @package   coolrunner-api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK;


use CoolRunnerSDK\Models\Error;
use CoolRunnerSDK\Models\Products\CarrierList;
use CoolRunnerSDK\Models\Products\CountryList;
use CoolRunnerSDK\Models\ServicePoints\Servicepoint;
use CoolRunnerSDK\Models\Shipments\Shipment;
use CoolRunnerSDK\Models\Shipments\ShipmentResponse;

/**
 * Class API
 *
 * @package CoolRunnerSDK
 */
class API {
    protected static $_version         = '1.0.0';
    protected static $_base_url        = 'https://api.coolrunner.dk/v3/';
    protected static $_instance        = false;
    protected static $_default_headers = [
        'X-Developer-Id' => 'PHP SDK ',
        "Cache-Control: no-cache"
    ];

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

    /**
     * API constructor.
     *
     * @param $token
     */
    protected function __construct($token) {
        self::$token = $token;
    }

    /**
     * Load the CoolRunner SDK singleton
     *
     * @param string $email        E-mail used for integration with CoolRunner API v3
     * @param string $token        Token used for integration with CoolRunner API v3
     * @param string $developer_id [optional] Developer ID - If this library is used with a third party extention
     *                             please enter your extentions name and/or your company name
     *
     * @return API
     */
    public static function &load($email, $token, $developer_id = null) {
        if (!is_null($developer_id) && (is_int($developer_id) || is_string($developer_id))) {
            self::$_default_headers['X-Developer-Id'] .= " | $developer_id";
        }

        if (self::$_instance === false) {
            $apitoken = base64_encode("$email:$token");
            self::$_instance = new self($apitoken);
        }
        return self::$_instance;
    }

    /**
     * Get the current instance of CoolRunner API
     *
     * @return self|APILight|false CoolRunnerAPI if instance has been loaded, or false on failure
     */
    public static function getInstance() {
        return self::$_instance !== false ? self::$_instance : APILight::getInstance();
    }

    /**
     * @return string
     */
    public static function getBaseUrl() {
        return self::$_base_url;
    }

    /**
     * @return string
     */
    public static function getVersion() {
        return self::$_version;
    }

    /**
     * @return array
     */
    public static function getDefaultHeaders() {
        return self::$_default_headers;
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
     * @return CurlResponse|null
     */
    public function getLastResponse() {
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
     * @return Servicepoint[]|false
     *
     * @see https://docs.coolrunner.dk/v3/#find CoolRunner API v3 Docs Servicepoints/Find
     */
    public function findServicepoints($carrier, $country_code, $zip_code, $city = '', $street = '') {
        $url = self::$_base_url . "servicepoints/$carrier";

        $data = ['country_code' => $country_code, 'zipcode' => $zip_code, 'city' => $city, 'street' => $street];

        $resp = $this->get($url, 'GET', $data);

        $resp->getErrorMsg();

        if ($resp->isOk()) {
            return Servicepoint::parseArrayFromJSON($resp->toString());
        }
        return false;
    }

    /**
     * Get servicepoint by ID
     *
     * @param string $carrier Carrier to poll (DAO, GLS, postnord)
     * @param int    $id      Servicepoint id. eg. 97405
     *
     * @return bool|Servicepoint
     *
     * @see https://docs.coolrunner.dk/v3/#find CoolRunner API v3 Docs Servicepoints/Get
     */
    public function getServicepoint($carrier, $id) {
        $url = self::$_base_url . "servicepoints/$carrier/$id";

        $resp = $this->get($url, 'GET');

        if ($resp->isOk()) {
            return Servicepoint::parseFromJSON($resp->toString());
        }
        return false;
    }

    /**
     * @param string $from_country_code
     * @param string $to_country_code
     *
     * @return bool|CountryList|CarrierList[]|CarrierList|false
     *
     * @see https://docs.coolrunner.dk/v3/#gat-3 CoolRunner API v3 Docs Products/Get
     */
    public function getProducts($from_country_code, $to_country_code = '') {
        $url = self::$_base_url . "products/$from_country_code";

        $resp = $this->get($url, 'GET');

        if ($resp->isOk()) {
            $list = CountryList::parseFromJSON($resp->toString());
            return $to_country_code !== '' ? CountryList::parseFromJSON($resp->toString())->getCountry($to_country_code) : $list;
        }
        return false;
    }

    /**
     * @param Shipment $shipment
     *
     * @return ShipmentResponse|false
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
     * @return bool|ShipmentResponse
     */
    public function getShipment($package_number) {
        $url = self::$_base_url . "shipments/$package_number";

        $resp = $this->get($url, 'GET');

        if ($resp->isOk()) {
            return ShipmentResponse::create($resp->jsonDecode(true));
        }
        return false;
    }

    /**
     * @param $package_number
     *
     * @return bool|string
     */
    public function getShipmentLabel($package_number) {
        $url = self::$_base_url . "shipments/$package_number/label";

        $resp = $this->get($url, 'GET');

        if ($resp->isOk()) {
            return $resp->getData();
        }
        return false;
    }

    /**
     * @param $package_number
     *
     * @return bool|array
     */
    public function getShipmentTracking($package_number) {
        $url = self::$_base_url . "shipments/$package_number/tracking";

        $resp = $this->get($url, 'GET');

        if ($resp->isOk()) {
            return $resp->jsonDecode(true);
        }
        return false;
    }
}