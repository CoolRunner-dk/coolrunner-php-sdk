<?php
/**
 * @package   coolrunner-api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK\Models\ServicePoints;


use CoolRunnerSDK\Models\CoolArrayObject;

class ServicepointCarrierList
    extends CoolArrayObject {

    public function __construct($carriers = array()) {
        foreach ($carriers as $key => $types) {
            $this->__data[strtoupper($key)] = new ServicepointList($types);
        }
    }

    /**
     * @param $key
     *
     * @return bool|ServicepointList
     */
    public function &getCarrierServicepoints($key) {
        return $this->__data[strtoupper($key)];
    }

    public function offsetGet($offset) {
        return parent::offsetGet(strtoupper($offset));
    }

    public function offsetSet($offset, $value) {
        parent::offsetSet(strtoupper($offset), $value);
    }

}