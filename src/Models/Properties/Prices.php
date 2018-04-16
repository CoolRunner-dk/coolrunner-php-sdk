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
 * @property double|float $incl_tax
 * @property double|float $excl_tax
 *
 * @package CoolRunnerSDK\Models\Properties
 */
class Prices
    extends Property {
    /** @var double|float */
    protected $incl_tax, $excl_tax;
}