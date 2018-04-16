<?php
/**
 * @package   coolrunner-api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK\Models\Properties;

/**
 * Class MaxSize
 *
 * @property int $length
 * @property int $height
 * @property int $width
 * @property int $weight
 *
 * @package CoolRunnerSDK\Models\Properties
 */
class MaxSize
    extends Property {
    /** @var int */
    protected $length, $height, $width, $weight;

    /**
     * Check if the specified size is within the limitations
     *
     * All measurements must be in metric!
     *
     * @param int $length in cm
     * @param int $width  in cm
     * @param int $height in cm
     * @param int $weight in grams
     *
     * @return bool
     */
    public function check($length, $width, $height, $weight) {
        $limit = array(0 => $this->length, 1 => $this->height, 2 => $this->width);
        $test = array(0 => $length, 1 => $height, 2 => $width);
        sort($limit);
        sort($test);

        $limit[3] = $this->weight;
        $test[3] = $weight;

        $limit = array_values($limit);
        $test = array_values($test);

        $results = array();
        for ($i = 0; $i < count($limit); $i++) {
            if ($i === 3) {
                $results[$i] = $test[$i] < $limit[$i];
            } else {
                $results[$i] = $test[$i] <= $limit[$i];
            }
        }

        return !in_array(false, $results);
    }
}