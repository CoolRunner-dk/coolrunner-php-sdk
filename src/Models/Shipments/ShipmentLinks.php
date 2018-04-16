<?php
/**
 * @package   coolrunner-api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK\Models\Shipments;


use CoolRunnerSDK\Models\Properties\Property;

/**
 * Class ShipmentLinks
 *
 * @property string $self
 * @property string $label
 * @property string $tracking
 *
 * @package CoolRunnerSDK\Models\Shipments
 */
class ShipmentLinks
    extends Property {
    protected $self, $label, $tracking;
}