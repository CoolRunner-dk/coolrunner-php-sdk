<?php
/**
 * @package   coolrunner-api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK\Models\Properties;

/**
 * Class Address
 *
 * @property string $street
 * @property string $zipcode
 * @property string $city
 * @property string $country_code;
 *
 * @package CoolRunnerSDK\Models\Properties
 */
class Address
    extends Property {
    /** @var string */
    protected
        $street,
        $zip_code,
        $city,
        $country_code;

    /**
     * @param string $format    Format of the output string
     *                          Placeholders:<br>
     *                          <ul>
     *                          <li>{street} - Street and number</li>
     *                          <li>{country} - ISO 3166-1 alpha-2 country code</li>
     *                          <li>{zip} - Zipcode</li>
     *                          <li>{city} - City</li>
     *                          </ul>
     *
     * @return string
     */
    public function toString($format = ':street, :country-:zip :city') {
        $format = str_replace(array(':street', ':zip', ':city', ':country'), array($this->street, $this->zipcode, $this->city, $this->country_code), $format);

        return $format;
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->toString();
    }
}