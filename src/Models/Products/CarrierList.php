<?php
/**
 * @package   coolrunner-api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK\Models\Products;


use CoolRunnerSDK\Models\CoolArrayObject;

/**
 * Class CarrierList
 *
 * @access  private
 * @package CoolRunnerSDK\Models\Products
 */
class CarrierList
    extends CoolArrayObject {

    /**
     * CarrierList constructor.
     *
     * @param array $carriers
     */
    public function __construct($carriers = array()) {
        foreach ($carriers as $key => $types) {
            $this->__data[strtoupper($key)] = new ProductTypeList($types);
        }
    }

    /**
     * @param $carrier
     *
     * @return bool|ProductTypeList
     */
    public function getCarrier($carrier) {
        return $this->offsetGet($carrier);
    }

    /**
     * @param mixed $offset
     *
     * @return bool|ProductTypeList
     */
    public function offsetGet($offset) {
        return parent::offsetGet(strtoupper($offset));
    }

    public function getPossibleProducts($shipment) {
        /** @var ProductTypeList $carrier */
        $results = array();
        foreach ($this as $carrier) {
            $prods = $carrier->getPossibleProducts($shipment);
            $results = array_merge($prods ? $prods->toArray() : array(),$results);
        }

        return new ProductList($results);
    }
}