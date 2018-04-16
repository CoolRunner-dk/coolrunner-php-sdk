<?php
/**
 * @package   coolrunner-api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK;


use CoolRunnerSDK\Models\Error;
use CoolRunnerSDK\Models\Shipments\Shipment;
use CoolRunnerSDK\Models\Shipments\ShipmentResponse;

class APILight {
    protected static $_version         = null;
    protected static $_base_url        = null;
    protected static $_instance        = false;
    protected static $_default_headers = null;
    protected static $_raw             = false;
    protected static $_assoc           = true;

    protected static $token;

    const CARRIER_GLS      = 'GLS';
    const CARRIER_DAO      = 'DAO';
    const CARRIER_POSTNORD = 'postnord';
    const DEBUG_MODE       = true;

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
     * @param bool   $raw          Return all output as raw json instead of decoded arrays
     * @param bool   $assoc        Set to true if the data returned should be an associative array,
     *                             or false if the data returned should be an stdClass.
     *                             This option has no effect if <b>$raw</b> is set to true
     *
     * @return self
     */
    public static function &load($email, $token, $developer_id = null, $assoc = true, $raw = false) {
        if (!is_null($developer_id) && (is_int($developer_id) || is_string($developer_id))) {
            self::$_default_headers['X-Developer-Id'] .= " | $developer_id";
        }

        if (self::$_instance === false) {
            $apitoken = base64_encode("$email:$token");
            self::$_raw = $raw;
            self::$_assoc = $assoc;
            self::$_instance = new self($apitoken);
        }
        return self::$_instance;
    }

    /**
     * Get the current instance of CoolRunner API
     *
     * @return self|false CoolRunnerAPI if instance has been loaded, or false on failure
     */
    public static function getInstance() {
        return self::$_instance;
    }

    /**
     * Get the API v3 base url
     *
     * @return string
     */
    public static function getBaseUrl() {
        return self::$_base_url;
    }

    public function isRaw() {
        return self::$_raw;
    }

    public function isAssoc() {
        return self::$_assoc;
    }

    /**
     * @param string $url
     * @param string $method POST|GET
     * @param array  $data
     *
     * @return CurlResponse
     */
    public function get($url, $method = 'GET', $data = []) {
        $data = array_filter($data);

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

        $ch = curl_init($url);
        curl_setopt_array($ch, $opts);

        $data = curl_exec($ch);
        $info = curl_getinfo($ch);
        $error = curl_error($ch);

        $resp = new CurlResponse($data, $info, $error);

        if ($resp->isUnauthorized() || $resp->isError() || $resp->isNotFound()) {
            Error::log($resp->getHttpResponseCode(), $resp->getErrorMsg());
        }

        curl_close($ch);

        return $resp;
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
     * @param string $zipcode      Zipcode. eg. 9000
     * @param string $city         <i>[Optional]</i> City. eg. Aalborg
     * @param string $street       <i>[Optional]</i> Street. eg. Slotsgade 8
     *
     * @return array|string|false
     *
     * @see https://docs.coolrunner.dk/v3/#find CoolRunner API v3 Docs Servicepoints/Find
     */
    public function findServicepoints($carrier, $country_code, $zipcode, $city = '', $street = '') {
        $url = self::$_base_url . "servicepoints/$carrier";

        $data = ['country_code' => $country_code, 'zipcode' => $zipcode, 'city' => $city, 'street' => $street];

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
        $url = self::$_base_url . "servicepoints/$carrier/$id";

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
     * @see https://docs.coolrunner.dk/v3/#gat-3 CoolRunner API v3 Docs Products/Get
     */
    public function getProducts($from_country_code, $to_country_code = '') {
        $url = self::$_base_url . "products/$from_country_code";

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
     * @return ShipmentResponse
     */
    public function createShipment($shipment) {
        if (is_object($shipment) && get_class($shipment) === Shipment::class) {
            return $shipment->create();
        }
    }

    public function getShipment($package_number) {
        $url = self::$_base_url . "shipments/$package_number";

        $resp = $this->get($url, 'GET');

        if ($resp->isOk()) {
            return self::$_raw ? $resp->getData() : $resp->jsonDecode(self::$_assoc);
        }
        return false;
    }

    public function getShipmentLabel($package_number) {
        $url = self::$_base_url . "shipments/$package_number/label";

        $resp = $this->get($url, 'GET');

        if ($resp->isOk()) {
            return self::$_raw ? $resp->getData() : $resp->jsonDecode(self::$_assoc);
        }
        return false;
    }

    public function getShipmentTracking($package_number) {
        $url = self::$_base_url . "shipments/$package_number/tracking";

        $resp = $this->get($url, 'GET');

        if ($resp->isOk()) {
            return self::$_raw ? $resp->getData() : $resp->jsonDecode(self::$_assoc);
        }
        return false;
    }
}