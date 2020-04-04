<?php
/**
 * Created by Florian Pradines
 */

namespace Skeerel\Util;


use Skeerel\Exception\IllegalArgumentException;

class Random {

    /**
     * @param int $length
     * @return string
     * @throws IllegalArgumentException
     */
    public static function token($length = 40){
        if(!is_int($length) || intval($length) <= 8) {
            throw new IllegalArgumentException("length must be an integer value > 8");
        }

        if (function_exists('random_bytes')) {
            try {
                return bin2hex(random_bytes($length));
            } catch(\Exception $ignore) {}
        }

        if (function_exists('openssl_random_pseudo_bytes')) {
            return bin2hex(openssl_random_pseudo_bytes($length));
        }

        return bin2hex(mcrypt_create_iv($length, MCRYPT_DEV_URANDOM));
    }
}