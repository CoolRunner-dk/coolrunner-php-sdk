<?php
/**
 * @package   api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK\Models\Shipments;

use CoolRunnerSDK\Models\CoolObject;

/**
 * Class ShipmentTracking
 *
 * @property string                  $package_number
 * @property string                  $carrier
 * @property ShipmentTrackingEntry[] $tracking
 *
 * @package CoolRunnerSDK\Models\Shipments
 */
class ShipmentTracking
    extends CoolObject
    implements \ArrayAccess, \Iterator, \Countable {
    protected $package_number, $carrier;

    /** @var ShipmentTrackingEntry[] */
    protected $tracking = array();

    public function __construct($data) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                if ($key !== 'tracking') {
                    $this->{$key} = $value;
                } else {
                    $this->{$key} = ShipmentTrackingEntry::getList($value);
                }
            }
        }
    }

    public function __get($name) {
        return isset($this->{$name}) ? $this->{$name} : null;
    }

    public function __set($name, $value) {
        if (property_exists($this, $name)) {
            $this->{$name} = !isset($this->{$name}) ? $value : $this->{$name};
        }
    }

    // region ArrayAccess

    public function offsetExists($offset) {
        return isset($this->tracking[$offset]);
    }

    /**
     * @return bool|ShipmentTrackingEntry|mixed
     */
    public function offsetGet($offset) {
        return $this->offsetExists($offset) ? $this->tracking[$offset] : false;
    }

    public function offsetSet($offset, $value) {
        $this->tracking[$offset] = $value;
    }

    public function offsetUnset($offset) {
        unset($this->tracking[$offset]);
    }

    // endregion

    //region Countable

    /**
     * @inheritdoc
     */
    public function count() {
        return count($this->tracking);
    }

    // endregion

    // region Iterator

    /**
     * @inheritdoc
     */
    public function rewind() {
        return reset($this->tracking);
    }

    /**
     * @inheritdoc
     */
    public function current() {
        return current($this->tracking);
    }

    /**
     * @inheritdoc
     */
    public function next() {
        return next($this->tracking);
    }

    /**
     * @inheritdoc
     */
    public function key() {
        return key($this->tracking);
    }

    /**
     * @inheritdoc
     */
    function valid() {
        return $this->key() !== null && $this->key() !== false;
    }

    // endregion
}