<?php
/**
 * @package   api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK;


final class InstanceStore {
    private static $_instances = array();

    public static function hasInstance($type) {
        return isset(self::$_instances[$type]);
    }

    /**
     * @param string $type
     *
     * @return API|APILight|mixed|null
     */
    public static function &getInstance($type) {
        return self::$_instances[$type];
    }

    /**
     * @param string             $type
     * @param API|APILight|mixed $instance
     *
     * @return API|APILight|mixed
     */
    public static function &setInstance($type, &$instance) {
        self::$_instances[$type] = $instance;
        return $instance;
    }
}