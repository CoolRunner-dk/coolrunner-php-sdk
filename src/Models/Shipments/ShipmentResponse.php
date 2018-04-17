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

        $this->test_save_to_cache($data);
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
            $res = $api->get($this->_links->label);

            if ($res->isOk()) {
                return $res->getData();
            }
        } else {
            Error::log(500, 'CoolRunner SDK must be instantiated before being able to pull data | ' . __FILE__);
        }

        return false;
    }

    public function getTracking() {
        if ($api = API::getInstance()) {
            $res = $api->get($this->_links->tracking);
            if ($res->isOk()) {
                return $res->getData();
            }
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

    /**
     * @param mixed  $data
     * @param string $cache_dir
     */
    public function test_save_to_cache($data = null, $cache_dir = 'cache') {
        if (isset($this->package_number)) {
            $data = json_encode(!is_null($data) ? $data : $this, JSON_PRETTY_PRINT);

            file_put_contents($_SERVER['DOCUMENT_ROOT'] . "$cache_dir/$this->package_number.json", $data);
        }
    }

    /**
     * @param string $package_number
     * @param string $cache_dir
     *
     * @return bool|ShipmentResponse
     */
    public static function test_get_from_cache($package_number, $cache_dir = 'cache') {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "$cache_dir/$package_number.json")) {
            return new self(json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "$cache_dir/$package_number.json"), true));
        }

        return false;
    }

    /**
     * @param string $cache_dir
     *
     * @return ShipmentResponse[]
     */
    public static function test_get_all_from_cache($cache_dir = 'cache') {
        $ret = array();
        foreach (glob($_SERVER['DOCUMENT_ROOT'] . "$cache_dir/*.json") as $filepath) {
            $ret[] = new self(json_decode(file_get_contents($filepath), true));
        }

        return $ret;
    }
}