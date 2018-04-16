<?php
/**
 * @package   coolrunner-api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK\Models\Properties;


/**
 * Class Coordinate
 *
 * @property double|float $longitude
 * @property double|float $latitude
 *
 * @package CoolRunnerSDK\Models\Properties
 */
class Coordinates
    extends Property {
    /** @var double|float */
    protected $longitude, $latitude;

    /**
     * @return string
     */
    public function __toString() {
        return "$this->latitude $this->longitude";
    }
}