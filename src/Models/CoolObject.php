<?php
/**
 * @package   api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK\Models;


class CoolObject implements \JsonSerializable {

    /**
     * Convert to JSON
     *
     * @param bool $pretty Returned data should be formatted
     *
     * @return string Resulting JSON
     */
    public function toJson($pretty = false) {
        return json_encode(get_object_vars($this), $pretty ? constant('JSON_PRETTY_PRINT') : 0);
    }

    /**
     * Convert to Array
     *
     * @return array
     */
    public function toArray() {
        return json_decode($this->toJson(), true);
    }

    public function jsonSerialize() {
        return $this->toJson();
    }
}