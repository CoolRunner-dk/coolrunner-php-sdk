<?php
/**
 * @package   coolrunner-api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK\Models\Properties;

/**
 * Class Weight
 *
 * @property int $from
 * @property int $to
 *
 * @package CoolRunnerSDK\Models\Properties
 */
class Weight
    extends Property {
    /** @var int */
    protected $from, $to;

    /**
     * Check if the specified weight is in the range of the product
     *
     * @param $weight
     *
     * @return bool
     */
    public function isInRange($weight) {
        return $weight >= $this->from && $weight <= $this->to;
    }
}