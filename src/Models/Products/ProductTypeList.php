<?php
/**
 * @package   coolrunner-api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK\Models\Products;


use CoolRunnerSDK\Models\CoolArrayObject;
use CoolRunnerSDK\Models\Shipments\Shipment;

class ProductTypeList
    extends CoolArrayObject {

    public function __construct($types = array()) {
        foreach ($types as $key => $products) {
            $this->__data[strtoupper($key)] = new ProductList($products);
        }
    }

    /**
     * @param $type
     *
     * @return bool|ProductList
     */
    public function getType($type) {
        return $this->offsetGet($type);
    }

    /**
     * @param Shipment $shipment
     *
     * @return ProductList|Product[]
     */
    public function getPossibleProducts($shipment) {
        $prods = array();
        foreach ($this->__data as $key => $type) {
            /** @var ProductList $type */
            if ($prod = $type->findProduct($shipment)) {
                $prods[] = $prod;
            }
        }

        return !empty($prods) ? new ProductList($prods) : false;
    }

    public function offsetGet($offset) {
        return parent::offsetGet(strtoupper($offset));
    }
}