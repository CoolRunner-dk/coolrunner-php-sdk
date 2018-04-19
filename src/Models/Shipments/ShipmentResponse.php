<?php
/**
 * @package   coolrunner-api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK\Models\Shipments;

use CoolRunnerSDK\API;
use CoolRunnerSDK\Models\Error;
use CoolRunnerSDK\Models\Properties\Person;

/**
 * Class ShipmentResponse
 *
 * @property string        $package_number
 * @property ShipmentPrice $price
 * @property ShipmentLinks $_links
 * @property Person        $sender
 * @property Person        $receiver
 * @property string        $servicepoint_id
 * @property string        $length
 * @property string        $width
 * @property string        $height
 * @property string        $weight
 *
 * @package CoolRunnerSDK\Models\Shipments
 */
class ShipmentResponse {
    protected
        $package_number, $price, $_links, $product,
        $sender, $receiver, $servicepoint_id,
        $length, $width, $height, $weight;

    protected static $raw = false, $assoc = true;

    public static function create($init_data) {
        return (new self($init_data))->getSelf();
    }

    protected function __construct($data = null) {
        if (!is_null($data) && is_array($data)) {
            foreach ($data as $key => $value) {
                if ($key === 'droppoint_id') {
                    $key = 'servicepoint_id';
                }
                $this->{$key} = $value;
            }
        }
        $this->price = new ShipmentPrice($this->price);
        $this->_links = new ShipmentLinks($this->_links);
        $this->sender = new Person($this->sender);
        $this->receiver = new Person($this->receiver);
    }

    public function getSelf() {
        if ($api = API::getInstance()) {
            return new self($api->get($this->_links->self)->jsonDecode(true));
        } else {
            Error::log(500, 'CoolRunner SDK must be instantiated before being able to pull data | ' . __FILE__);
        }

        return false;
    }

    /**
     * @return bool|\CoolRunnerSDK\CurlResponse
     */
    public function getLabel() {
        if ($api = API::getInstance()) {
            return $api->getShipmentLabel($this->package_number);
        } else {
            Error::log(500, 'CoolRunner SDK must be instantiated before being able to pull data | ' . __FILE__);
        }

        return false;
    }

    public function getTracking() {
        if ($api = API::getInstance()) {
            return $api->getShipmentTracking($this->package_number);
        } else {
            Error::log(500, 'CoolRunner SDK must be instantiated before being able to pull data | ' . __FILE__);
        }

        return false;
    }

    public function __get($name) {
        return isset($this->{$name}) ? $this->{$name} : null;
    }

    public function __set($name, $value) {
        if (property_exists($this, $name)) {
            $this->{$name} = !isset($this->{$name}) ? $value : $this->{$name};
        }
    }
}