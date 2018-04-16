<?php
/**
 * @package   coolrunner-api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK\Models\ServicePoints;


use CoolRunnerSDK\Models\CoolArrayObject;

class ServicepointList
    extends CoolArrayObject {

    public function __construct($servicepoints = array()) {
        foreach ($servicepoints as $key => $servicepoint) {
            if (is_object($servicepoint) && get_class($servicepoint) === Servicepoint::class) {
                $this->__data[$key] = $servicepoint;
            } else {
                $this->__data[$key] = new Servicepoint($servicepoint);
            }
        }
    }

    public function getServicepoint($id) {
        foreach ($this->__data as $servicepoint) {
            if (intval($servicepoint->id) === intval($id)) {
                return $servicepoint;
            }
        }

        return false;
    }
}