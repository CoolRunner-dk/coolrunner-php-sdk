<?php
/**
 * @package   coolrunner-api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK\Models\Shipments;


use CoolRunnerSDK\API;
use CoolRunnerSDK\APILight;
use CoolRunnerSDK\Models\Error;
use CoolRunnerSDK\Models\Products\Product;
use CoolRunnerSDK\Models\Products\ProductList;
use CoolRunnerSDK\Models\Properties\Person;
use CoolRunnerSDK\Models\ServicePoints\ServicepointCarrierList;
use CoolRunnerSDK\Models\ServicePoints\ServicepointList;

/**
 * Class Shipment
 *
 * @property Person $sender
 * @property Person $receiver
 * @property string $length
 * @property string $width
 * @property string $height
 * @property string $weight
 * @property string $carrier
 * @property string $carrier_product
 * @property string $carrier_service
 * @property string $reference
 * @property string $description
 * @property string $servicepoint_id
 * @property string $label_format
 *
 * @package CoolRunnerSDK\Models\Shipments
 */
class Shipment {
    /** @var Person */
    protected $receiver, $sender;
    /** @var string */
    protected
        $length, $width, $height, $weight,
        $carrier, $carrier_product, $carrier_service,
        $reference, $description,
        $servicepoint_id, $label_format = 'LabelPrint';

    /**
     * Shipment constructor.
     *
     * @param null $data
     */
    public function __construct($data = null) {
        if (!is_null($data) && is_array($data)) {
            foreach ($data as $key => $value) {
                if ($key === 'droppoint_id') {
                    $key = 'servicepoint_id';
                }
                if (property_exists($this, $key)) {
                    $this->{$key} = $value;
                }
            }
        }
    }

    /**
     * @return bool|ShipmentResponse|mixed|string
     */
    public function create() {
        if ($this->validate() === true && $api = API::getInstance()) {
            $create_data = json_decode(json_encode(get_object_vars($this)), true);

            $create_data['droppoint_id'] = $create_data['servicepoint_id'];

            $resp = $api->get(API::getBaseUrl() . 'shipments', 'POST', $create_data);

            if ($resp->isOk()) {
                $resp = $api->get($resp->jsonDecode(true)['_links']['self']);
            }

            $raw = false;
            $assoc = false;
            if (is_object($api) && get_class($api) === APILight::class) {
                $raw = $api->isRaw();
                $assoc = $api->isAssoc();
            }

            if ($resp->isOk()) {
                if ($raw || $assoc) {
                    return $raw ? $resp->getData() : $resp->jsonDecode($assoc);
                }
                return ShipmentResponse::create($resp->jsonDecode(true));
            }
        } else {
            if (!API::getInstance()) {
                Error::log(500, 'CoolRunner SDK must be instantiated before being able to push data | ' . __FILE__);
            }
        }

        return false;
    }

    public function validate() {
        $res = $this->validateFields(
            array(
                'sender', 'receiver',
                'length', 'width', 'height', 'weight',
                'carrier', 'carrier_product', 'carrier_service',
                'label_format', 'servicepoint_id'
            )
        );

        return $res;
    }

    /**
     * @param string[] $fields
     *
     * @return array|true
     */
    protected function validateFields($fields) {
        $error = array();
        foreach ($fields as $field) {
            if (!isset($this->{$field})) {
                $error[] = $field;
            }

            if (in_array($field, array('sender', 'receiver')) &&
                isset($this->{$field}) &&
                is_object($this->{$field}) &&
                get_class($this->{$field}) === Person::class &&
                $p_errors = $this->{$field}->{'validate' . ucfirst($field)}()) {
                if ($p_errors !== true) {
                    foreach ($p_errors as $val) {
                        $error[] = "$field:$val";
                    }
                }
            }
        }

        return !empty($error) ? $error : true;
    }

    /**
     * @return ServicepointList[]|ServicepointCarrierList|false
     */
    public function getPossibleCarriers() {
        if ($api = API::getInstance()) {
            if (get_class($api) === APILight::class) {
                Error::log(500, 'CoolRunner SDK Light doesn\'t support helper methods for Shipments | ' . __FILE__);
                return false;
            }
            $ret = array();

            if (isset($this->carrier)) {
                $carriers = array(strtolower($this->carrier));
            } else {
                $carriers = array('postnord', 'gls', 'dao');
            }

            foreach ($carriers as $carrier) {
                $ret[$carrier] = $api->findServicepoints($carrier, $this->receiver->country, $this->receiver->zipcode, $this->receiver->city, $this->receiver->street1);
            }


            $raw = false;
            $assoc = false;
            if (is_object($api) && get_class($api) === APILight::class) {
                $raw = $api->isRaw();
                $assoc = $api->isAssoc();
            }
            if ($raw || $assoc) {
                return $raw ? json_encode($ret, JSON_PRETTY_PRINT) : json_decode(json_encode($ret));
            }
            return $ret;
        } else {
            Error::log(500, 'CoolRunner SDK must be instantiated before being able to pull data | ' . __FILE__);
        }

        return false;
    }

    /**
     * @return bool|\CoolRunnerSDK\Models\Products\ProductList|Product[]
     */
    public function getPossibleProducts() {
        if ($api = API::getInstance()) {
            if (get_class($api) === APILight::class) {
                Error::log(500, 'CoolRunner SDK Light doesn\'t support helper methods for Shipments | ' . __FILE__);
                return false;
            }
            $prods = $api->getProducts($this->sender->country, $this->receiver->country);

            if (isset($this->carrier)) {
                return $prods->getCarrier($this->carrier)->getPossibleProducts($this);
            } else {
                $ret = new ProductList();
                foreach ($prods as $key => $carrier) {
                    $tmp_prods = $carrier->getPossibleProducts($this);
                    if ($tmp_prods) {
                        $ret[] = $tmp_prods[0];
                    }
                }
            }
        } else {
            Error::log(500, 'CoolRunner SDK must be instantiated before being able to pull data | ' . __FILE__);
        }

        return false;
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function __get($name) {
        return isset($this->{$name}) ? $this->{$name} : null;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value) {
        $this->{strtolower($name)} = $value;
    }
}