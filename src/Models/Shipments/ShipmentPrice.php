<?php
/**
 * @package   coolrunner-api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK\Models\Shipments;


use CoolRunnerSDK\Models\Properties\Property;

/**
 * Class ShipmentPrice
 *
 * @property float|double $incl_tax
 * @property float|double $excl_tax
 *
 * @package CoolRunnerSDK\Models\Shipments
 */
class ShipmentPrice
    extends Property {
    /** @var double|float */
    protected $incl_tax, $excl_tax;
}