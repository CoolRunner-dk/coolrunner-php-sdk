<?php
/**
 * @package   coolrunner-api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK\Models\Properties;

/**
 * Class Prices
 *
 * @property string $code
 * @property string $description
 * @property bool   $required
 *
 * @package CoolRunnerSDK\Models\Properties
 */
class Service
    extends Property {
    /** @var string */
    protected $code, $description;
    /** @var bool */
    protected $required;

    public static function getList($services) {
        foreach ($services as $i => &$service) {
            $service = new self($service);
        }

        return $services;
    }
}