<?php
/**
 * @package   coolrunner-api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK\Models;

/**
 * Class CoolArrayObject
 *
 * @package CoolRunnerSDK\Models
 */
abstract class CoolArrayObject
    implements \ArrayAccess, \Countable, \Iterator {
    protected $__data = array();

    // region ArrayAccess

    /**
     * @inheritdoc
     */
    public function offsetExists($offset) {
        return isset($this->__data[$offset]);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset) {
        return $this->offsetExists($offset) ? $this->__data[$offset] : false;
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value) {
        $this->__data[$offset] = $value;
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset) {
        unset($this->__data[$offset]);
    }

    // endregion

    //region Countable

    /**
     * @inheritdoc
     */
    public function count() {
        return count($this->__data);
    }

    // endregion

    // region Iterator

    /**
     * @inheritdoc
     */
    public function rewind() {
        return reset($this->__data);
    }

    /**
     * @inheritdoc
     */
    public function current() {
        return current($this->__data);
    }

    /**
     * @inheritdoc
     */
    public function next() {
        return next($this->__data);
    }

    /**
     * @inheritdoc
     */
    public function key() {
        return key($this->__data);
    }

    /**
     * @inheritdoc
     */
    function valid() {
        return $this->key() !== null && $this->key() !== false;
    }

    // endregion
}