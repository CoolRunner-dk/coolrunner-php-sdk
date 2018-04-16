<?php
/**
 * @package   coolrunner-api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK\Models\Products;


use CoolRunnerSDK\Models\CoolArrayObject;
use CoolRunnerSDK\Models\Carrier;

/**
 * Class CountryList
 *
 * @access  private
 * @package CoolRunnerSDK\Models\Products
 */
class CountryList
    extends CoolArrayObject {

    /**
     * CountryList constructor.
     *
     * @param $data
     */
    protected function __construct($data = array()) {
        foreach ($data as $country_code => $carriers) {
            $this->__data[strtoupper($country_code)] = new CarrierList($carriers);
        }
    }

    /**
     * @param $country_code
     *
     * @return false|CarrierList
     */
    public function getCountry($country_code) {
        return $this->offsetGet($country_code);
    }

    /**
     * @return string[]
     */
    public function getCountryCodes() {
        return array_keys($this->__data);
    }

    /**
     * @param $json
     *
     * @return false|CountryList|CarrierList[]
     */
    public static function parseFromJSON($json) {
        $data = json_decode($json, true);
        if ($data) {
            return new self($data);
        }

        return false;
    }

    /**
     * @param mixed $offset
     *
     * @return bool|CarrierList
     */
    public function offsetGet($offset) {
        return parent::offsetGet(strtoupper($offset));
    }
}