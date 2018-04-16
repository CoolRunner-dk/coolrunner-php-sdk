<?php
/**
 * @package   coolrunner-api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK\Models\ServicePoints;


use CoolRunnerSDK\Models\JSONParsable;
use CoolRunnerSDK\Models\Properties\Address;
use CoolRunnerSDK\Models\Properties\Coordinates;
use CoolRunnerSDK\Models\Properties\OpeningHours;

/**
 * Class Servicepoint
 *
 * @property int          $id
 * @property string       $name
 * @property int          $distance
 * @property Address      $address
 * @property Coordinates  $coordinates
 * @property OpeningHours $opening_hours
 *
 * @package CoolRunnerSDK\Models
 */
class Servicepoint {
    /** @var int */
    protected $id;
    /** @var string */
    protected $name;
    /** @var int */
    protected $distance;
    /** @var Address */
    protected $address;
    /** @var Coordinates */
    protected $coordinates;
    /** @var OpeningHours */
    protected $opening_hours;
    /** @var string $carrier */
    protected $carrier;

    /**
     * @param string $json
     *
     * @return self[]|ServicepointList|false
     */
    public static function parseArrayFromJSON($json) {
        if (is_string($json)) {
            $obj = json_decode($json, true);

            $obj = $obj['servicepoints'];

            $ret = array();
            foreach ($obj as $key => $servicepoints) {
                $ret[$key] = new Servicepoint($servicepoints);
            }
            return new ServicepointList($ret);
        }

        return false;
    }

    /**
     * @param string $json
     *
     * @return self|false
     */
    public static function parseFromJSON($json) {
        if (is_string($json)) {
            $obj = json_decode($json, true);

            return new self($obj);
        }

        return false;
    }

    public function __construct($data) {
        if (!is_null($data) && is_array($data)) {
            foreach ($data as $key => $value) {
                $this->{$key} = $value;
            }
        }

        $this->filter();
    }

    private function filter() {
        $this->address = new Address($this->address);
        $this->coordinates = new Coordinates($this->coordinates);
        $this->opening_hours = new OpeningHours($this->opening_hours);
    }

    public function __get($name) {
        return $this->{$name};
    }

    public function __set($name, $value) {
        $this->{$name} = isset($this->{$name}) ? $this->{$name} : $value;
    }
}