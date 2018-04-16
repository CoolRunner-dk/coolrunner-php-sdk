<?php
/**
 * @package   coolrunner-api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK\Models\Properties;


/**
 * Class TimeSpan
 *
 * @property string $from Time for opening | HH:MM
 * @property string $to   Time for closing | HH:MM
 *
 * @package CoolRunnerSDK\Models\Properties
 */
class Timespan {
    protected $from, $to;

    public function __construct($from, $to) {
        $this->from = $from;
        $this->to = $to;
    }

    public function __toString() {
        return "$this->from - $this->to";
    }

    public function __get($name) {
        return $this->{$name};
    }
}