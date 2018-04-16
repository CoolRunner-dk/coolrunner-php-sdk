<?php
/**
 * @package   coolrunner-api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK\Models\Properties;

/**
 * Class Person
 *
 * @package CoolRunnerSDK\Models\Properties
 *
 * @see
 */
class Person {
    public
        $name, $attention, $street1,
        $street2, $zipcode, $city,
        $country, $phone, $email,
        $notify_sms, $notify_email;

    /**
     * Person constructor.
     *
     * Input array keys:
     * <code>
     * array(
     *  'name'         => 'Heinz Ketchup',
     *  'attention'    => 'Mayo Naise',[Optional]
     *  'street1'      => 'Fridge street 1337',
     *  'street2'      => '',                   [Optional]
     *  'zipcode'     => '1234',
     *  'city'         => 'Freezeville',
     *  'country'      => 'DK',
     *  'phone'        => '12341234',
     *  'email'        => 'heinz@ketchup.food',
     *  'notify_sms'   => '12341234',           [Defaults to phone for receiver if not set]
     *  'notify_email' => 'heinz@ketchup.food'  [Defaults to phone for receiver if not set]
     * )
     * </code>
     *
     * @param \stdClass|array $data
     */
    public function __construct($data) {
        if (is_object($data)) {
            $data = json_decode(json_encode($data), true);
        }

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $key = $key === 'street' ? 'street1' : $key;
                if (property_exists($this, $key)) {
                    $this->{$key} = $value;
                }
            }
        }
    }

    /**
     * Validate the sender
     *
     * @return string[]|true Returns an array of invalid fields, or true if all fields are valid
     */
    public function validateSender() {
        $required_fields = array('name', 'street1', 'zipcode', 'city', 'country', 'phone', 'email');
        return $this->validateFields($required_fields);
    }

    /**
     * Validate the receiver
     *
     * @param bool $explicit Set to false if notify_sms and notify_email should default to phone and email properties respectively
     *
     * @return string[]|true Returns an array of invalid fields, or true if all fields are valid
     */
    public function validateReceiver($explicit = false) {
        $required_fields = array('name', 'street1', 'zipcode', 'city', 'country', 'phone', 'email', 'notify_sms', 'notify_email');
        $errors = $this->validateFields($required_fields);
        if (is_array($errors) && !$explicit) {
            if (in_array('notify_sms', $errors) && !in_array('phone', $errors)) {
                $this->notify_sms = $this->phone;
                unset($errors[array_search('notify_sms', $errors)]);
            }
            if (in_array('notify_email', $errors) && !in_array('email', $errors)) {
                $this->notify_email = $this->email;
                unset($errors[array_search('notify_email', $errors)]);
            }
        }

        return !is_array($errors) || empty($errors) ? true : array_values($errors);
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
        }

        return !empty($error) ? $error : true;
    }
}