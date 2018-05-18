<?php
/**
 * @package   api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK\Models\Shipments;

use CoolRunnerSDK\Models\CoolObject;

/**
 * Class ShipmentTrackingEntry
 *
 * @property string $timestamp
 * @property string $details
 * @property string $event
 *
 * @package CoolRunnerSDK\Models\Shipments
 */
class ShipmentTrackingEntry
    extends CoolObject {
    protected $timestamp, $details, $event;


    public function __construct($data) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * This method compiles a list of ShipmentTrackingEntries
     *
     * @param $data
     *
     * @return array
     */
    public static function getList($data) {
        $ret = array();
        foreach ($data as $event_info) {
            $ret[] = new self($event_info);
        }
        return $ret;
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
     * Get the timestamp as a DateTime object
     *
     * @return bool|\DateTime
     */
    public function getDateTime() {
        return \DateTime::createFromFormat('Y-m-d H:i', $this->timestamp);
    }
}