<?php
/**
 * @package   api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK;


class PCN {
    protected static $_instance = false;
    protected static $_base_url = 'https://api.coolrunner.dk/pcn/';

    /**
     * @var bool|API
     */
    protected $_api = false;
    /**
     * @var CurlResponse|null
     */
    protected $_last_response = null;

    protected function __construct($api_instance) {
        $this->_api = $api_instance;
    }

    public static function load($email, $token, $developer_id = null) {
        if (self::$_instance === false) {
            self::$_instance = new self(API::load($email, $token, $developer_id));
        }

        return self::$_instance;
    }

    /**
     * Get PDF from order
     *
     * @param string $unique_id The full link with the unique_id to this call is returned with the create shipment response!
     *
     * @return bool|array
     *
     * @see https://docs.coolrunner.dk/pcn/#api-PDF-Get_PDF
     */
    public function getPdf($unique_id) {
        $this->_last_response = $this->_api->get("pdf/$unique_id");

        if ($this->_last_response->isOk()) {
            return $this->_last_response->jsonDecode(true);
        }

        return false;
    }

    /**
     * PCN update dao package size
     *
     * <sub>These functions are only available for PCN / Invent Nord</sub>
     *
     * @param string $pcn_pack_id PCN Unineq Pack Id
     * @param int    $weight      The new package weight in grams
     * @param int    $length      The new package length in cm
     * @param int    $height      The new package height in cm
     * @param int    $width       The new package width in cm
     *
     * @return bool|array
     *
     * @see https://docs.coolrunner.dk/pcn/#api-PCN_Update-Update_DAO_Package_size
     */
    public function updateWeight($pcn_pack_id, $weight, $length, $height, $width) {
        $data = array(
            'pcn_pack_id' => $pcn_pack_id,
            'weight'      => $weight,
            'length'      => $length,
            '$height'     => $height,
            'width'       => $width
        );

        if ($weight > 0 && $length > 0 && $length > 0 && $height > 0 && $width > 0) {
            $this->_last_response = $this->_api->get(self::$_base_url . 'update/weight', 'POST', $data);

            if ($this->_last_response->isOk()) {
                return $this->_last_response->jsonDecode(true);
            }
        }

        return false;
    }

    /**
     * Create new PCN order
     *
     *  array(
     *      'order_number'          => 'demo1234',
     *      'receiver_name'         => 'Morten Harders',
     *      'receiver_attention'    => '',
     *      'receiver_street1'      => 'Stationsvej 24B',
     *      'receiver_street2'      => '',
     *      'receiver_zipcode'      => '9440',
     *      'receiver_city'         => 'Biersted, Aabybro',
     *      'receiver_country'      => 'DK',
     *      'receiver_phone'        => '28626622',
     *      'receiver_email'        => 'mh@coolrunner.dk',
     *      'receiver_notify_sms'   => '28626622',
     *      'receiver_notify_email' => 'mh@coolrunner.dk',
     *      'droppoint_id'          => '9006',
     *      'droppoint_name'        => 'POSTHUS KVICKLY',
     *      'droppoint_street1'     => 'Dannebrogsgade 64',
     *      'droppoint_zipcode'     => 9000,
     *      'droppoint_city'        => 'Aalborg',
     *      'droppoint_country'     => 'DK',
     *      'carrier'               => 'pdk',
     *      'carrier_product'       => 'private',
     *      'carrier_service'       => 'droppoint',
     *      'reference'             => 'demo1234',
     *      'description'           => '',
     *      'comment'               => '',
     *      'order_lines'           => array(
     *          array(
     *              'item_number' => '6294720456614',
     *              'qty'         => 1
     *          ),
     *          array(
     *              'item_number' => '8945698474845',
     *              'qty'         => 1
     *          )
     *      )
     *  )
     *
     * @param array $params
     *
     *
     * @return array|false
     *
     * @see https://docs.coolrunner.dk/pcn/#api-PCN_Order-Create_PCN_order
     */
    public function create($params) {
        $params = is_object($params) && get_class($params) === get_class(new \stdClass()) ? json_decode(json_encode($params), true) : is_array($params) ? $params : false;
        if ($params !== false) {
            $req_params = array(
                'order_number',
                'receiver_name',
                'receiver_attention',
                'receiver_street1',
                'receiver_street2',
                'receiver_zipcode',
                'receiver_city',
                'receiver_country',
                'receiver_phone',
                'receiver_email',
                'receiver_notify_sms',
                'receiver_notify_email',
                'droppoint_id',
                'droppoint_street1',
                'droppoint_zipcode',
                'droppoint_city',
                'droppoint_country',
                'carrier',
                'carrier_product',
                'carrier_service',
                'reference',
                'description',
                'comment',
                'order_lines'
            );

            $verified_params = array();
            foreach ($req_params as $param) {
                $verified_params[$param] = in_array($param, array_keys($params));
            }

            if (!in_array(false, $verified_params)) {
                $this->_last_response = $this->_api->get(self::$_base_url . 'order/create', 'POST', $params, 'json');

                if ($this->_last_response->isOk()) {
                    return $this->_last_response->jsonDecode(true);
                }
            }
        }

        return false;
    }

    /**
     * @return CurlResponse|null
     */
    public function getLastResponse() {
        return $this->_last_response;
    }

    /**
     * @return string|null
     */
    public function getLastErrorMsg() {
        return !is_null($this->_last_response) ? $this->_last_response->getErrorMsg() : null;
    }
}