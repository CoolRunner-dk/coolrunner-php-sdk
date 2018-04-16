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
 * @property float|double $price_incl_tax
 * @property float|double $price_excl_tax
 *
 * @package CoolRunnerSDK\Models\Shipments
 */
class ShipmentPrice
    extends Property {
    /** @var double|float */
    protected $price_incl_tax, $price_excl_tax;
}