<?php
/**
 * @package   coolrunner-api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK\Models\Products;


use CoolRunnerSDK\Models\CoolArrayObject;
use CoolRunnerSDK\Models\Shipments\Shipment;

/**
 * Class ProductList
 *
 * @package CoolRunnerSDK\Models\Products
 */
class ProductList
    extends CoolArrayObject {

    /**
     * ProductList constructor.
     *
     * @param $products
     */
    public function __construct($products = array()) {
        foreach ($products as $key => $product) {
            if (is_object($product) && get_class($product) === Product::class) {
                $this->__data[$key] = $product;
            } else {
                $this->__data[$key] = new Product($product);
            }
        }

        usort($this->__data, function ($a, $b) {
            /**
             * @var Product $a
             * @var Product $b
             */
            return $a->max_size->weight > $b->max_size->weight;
        });
    }

    /**
     * @param Shipment $shipment
     *
     * @return Product|false
     */
    public function findProduct($shipment) {
        foreach ($this->__data as $key => $product) {
            /** @var Product $product */
            if ($product->max_size->check($shipment->length, $shipment->width, $shipment->height, $shipment->weight)) {
                return $product;
            }
        }

        return false;
    }
}