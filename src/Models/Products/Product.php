<?php
/**
 * @package   coolrunner-api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK\Models\Products;


use CoolRunnerSDK\Models\Properties\MaxSize;
use CoolRunnerSDK\Models\Properties\Prices;
use CoolRunnerSDK\Models\Properties\Service;
use CoolRunnerSDK\Models\Properties\Weight;

/**
 * Class Product
 *
 * @property string    $title
 * @property MaxSize   $max_size
 * @property Weight    $weight
 * @property Prices    $prices
 * @property Service[] $services
 *
 * @package CoolRunnerSDK\Models\Products
 */
class Product {
    protected $title;
    protected $max_size;
    protected $weight;
    protected $prices;
    protected $services;

    public function __construct($data) {
        $this->title = $data['title'];
        $this->max_size = new MaxSize($data['max_size']);
        $this->weight = new Weight($data['weight']);
        $this->prices = new Prices($data['prices']);
        $this->services = Service::getList($data['services']);
    }

    public function __get($name) {
        return isset($this->{$name}) ? $this->{$name} : null;
    }

    public function __set($name, $value) {
        $this->{strtolower($name)} = $value;
    }
}