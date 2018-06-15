<?php
/**
 * @package   coolrunner-api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK\Models\Properties;

/**
 * Class OpeningHours
 *
 * @property Timespan $monday
 * @property Timespan $tuesday
 * @property Timespan $wednesday
 * @property Timespan $thursday
 * @property Timespan $friday
 * @property Timespan $saturday
 * @property Timespan $sunday
 *
 * @package CoolRunnerSDK\Models\Properties
 */
class OpeningHours
    extends Property {
    /** @var Timespan */
    protected
        $monday,
        $tuesday,
        $wednesday,
        $thursday,
        $friday,
        $saturday,
        $sunday;

    public function __construct($obj) {
        $this->monday = new Timespan($obj['monday']['from'], $obj['monday']['to']);
        $this->tuesday = new Timespan($obj['tuesday']['from'], $obj['tuesday']['to']);
        $this->wednesday = new Timespan($obj['wednesday']['from'], $obj['wednesday']['to']);
        $this->thursday = new Timespan($obj['thursday']['from'], $obj['thursday']['to']);
        $this->friday = new Timespan($obj['friday']['from'], $obj['friday']['to']);
        $this->saturday = new Timespan($obj['saturday']['from'], $obj['saturday']['to']);
        $this->sunday = new Timespan($obj['sunday']['from'], $obj['sunday']['to']);
    }

    public function __get($name) {
        return isset($this->{$name}) ? $this->{$name} : null;
    }

    public function __toString() {
        return $this->toString();
    }

    /**
     * Output the opening hours in a specified format
     *
     * @param string $format
     *
     * @return string
     */
    public function toString($format = ':day: :from - :to<br>') {
        $days = array(
            'Monday'    => $this->monday,
            'Tuesday'   => $this->tuesday,
            'Wednesday' => $this->wednesday,
            'Thursday'  => $this->thursday,
            'Friday'    => $this->friday,
            'Saturday'  => $this->saturday,
            'Sunday'    => $this->sunday
        );

        $ret = array();
        foreach ($days as $day => $values) {
            $str = str_replace(
                array(
                    ':day',
                    ':from',
                    ':to'
                ), array(
                    $day,
                    $values->from,
                    $values->to
                ), $format);
            $ret[] = $str;
        }

        return implode("", $ret);
    }
}