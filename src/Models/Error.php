<?php
/**
 * @package   coolrunner-api-v3-sdk
 * @author    Morten Harders
 * @copyright 2018
 */

namespace CoolRunnerSDK\Models;

/**
 * Class Error
 *
 * @package CoolRunnerSDK\Models
 */
class Error {
    /**
     * @param int  $code
     * @param null $msg
     *
     * @return bool
     */
    public static function log($code, $msg = null) {
        $db = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 3);
        return error_log(
            sprintf(
                "CoolRunner API SDK Error: %s | %s | %s",
                $code,
                $msg ? $msg : 'Unhandled error',
                sprintf('%s:%s', $db[1]['file'], $db[1]['line'])
            )
        );
    }
}