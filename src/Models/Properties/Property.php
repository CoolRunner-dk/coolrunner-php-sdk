<?php
/**
 * @package   coolrunner-api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK\Models\Properties;


use CoolRunnerSDK\Models\CoolObject;

abstract class Property
    extends CoolObject implements \JsonSerializable {
    public function __construct($obj) {
        if (!is_array($obj)) {
            $obj = json_decode(json_encode($obj), true);
        }
        if ($obj) {
            foreach ($obj as $key => $value) {
                $this->{$key} = $value;
            }
        }
    }

    public function __get($name) {
        return isset($this->{$name}) ? $this->{$name} : null;
    }

    public function __set($name, $value) {
        $this->{strtolower($name)} = $value;
    }

    public function jsonSerialize() {
        return get_object_vars($this);
    }
}