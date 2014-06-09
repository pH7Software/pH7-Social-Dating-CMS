<?php
/**
 * Generate a PBKDF2 key derivation of a supplied password
 *
 * This is a hash_pbkdf2() implementation for PHP versions 5.3 and 5.4.
 * @link http://www.php.net/manual/en/function.hash-pbkdf2.php
 *
 * @param string $algo
 * @param string $password
 * @param string $salt
 * @param int $iterations
 * @param int $length
 * @param bool $rawOutput
 *
 * @return string
 */
namespace {
defined('PH7') or exit('Restricted access');

 if (!function_exists('hash_pbkdf2'))
 {
    function hash_pbkdf2($algo, $password, $salt, $iterations, $length = 0, $rawOutput = false)
    {
    // check for hashing algorithm
    if (!in_array(strtolower($algo), hash_algos())) {
        trigger_error(sprintf(
            '%s(): Unknown hashing algorithm: %s',
            __FUNCTION__, $algo
        ), E_USER_WARNING);
        return false;
    }

    // check for type of iterations and length
    foreach (array(4 => $iterations, 5 => $length) as $index => $value) {
        if (!is_numeric($value)) {
            trigger_error(sprintf(
                '%s() expects parameter %d to be long, %s given',
                __FUNCTION__, $index, gettype($value)
            ), E_USER_WARNING);
            return null;
        }
    }

    // check iterations
    $iterations = (int)$iterations;
    if ($iterations <= 0) {
        trigger_error(sprintf(
            '%s(): Iterations must be a positive integer: %d',
            __FUNCTION__, $iterations
        ), E_USER_WARNING);
        return false;
    }

    // check length
    $length = (int)$length;
    if ($length < 0) {
        trigger_error(sprintf(
            '%s(): Iterations must be greater than or equal to 0: %d',
            __FUNCTION__, $length
        ), E_USER_WARNING);
        return false;
    }

    // check salt
    if (strlen($salt) > PHP_INT_MAX - 4) {
        trigger_error(sprintf(
            '%s(): Supplied salt is too long, max of INT_MAX - 4 bytes: %d supplied',
            __FUNCTION__, strlen($salt)
        ), E_USER_WARNING);
        return false;
    }

    // initialize
    $derivedKey = '';
    $loops = 1;
    if ($length > 0) {
        $loops = (int)ceil($length / strlen(hash($algo, '', $rawOutput)));
    }

    // hash for each blocks
    for ($i = 1; $i <= $loops; $i++) {
        $digest = hash_hmac($algo, $salt . pack('N', $i), $password, true);
        $block = $digest;
        for ($j = 1; $j < $iterations; $j++) {
            $digest = hash_hmac($algo, $digest, $password, true);
            $block ^= $digest;
        }
        $derivedKey .= $block;
    }

    if (!$rawOutput) {
        $derivedKey = bin2hex($derivedKey);
    }

    if ($length > 0) {
        return substr($derivedKey, 0, $length);
    }

    return $derivedKey;
    }
 }

}

/**
 * A Compatibility library with PHP 5.5's simplified password hashing API.
 *
 * @author Anthony Ferrara <ircmaxell@php.net>
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2012 The Authors
 */

namespace {

if (!defined('PASSWORD_DEFAULT')) {

    define('PASSWORD_BCRYPT', 1);
    define('PASSWORD_DEFAULT', PASSWORD_BCRYPT);

    /**
     * Hash the password using the specified algorithm
     *
     * @param string $password The password to hash
     * @param int    $algo     The algorithm to use (Defined by PASSWORD_* constants)
     * @param array  $options  The options for the algorithm to use
     *
     * @return string|false The hashed password, or false on error.
     */
    function password_hash($password, $algo, array $options = array()) {
        if (!function_exists('crypt')) {
            trigger_error("Crypt must be loaded for password_hash to function", E_USER_WARNING);
            return null;
        }
        if (!is_string($password)) {
            trigger_error("password_hash(): Password must be a string", E_USER_WARNING);
            return null;
        }
        if (!is_int($algo)) {
            trigger_error("password_hash() expects parameter 2 to be long, " . gettype($algo) . " given", E_USER_WARNING);
            return null;
        }
        $resultLength = 0;
        switch ($algo) {
            case PASSWORD_BCRYPT:
                // Note that this is a C constant, but not exposed to PHP, so we don't define it here.
                $cost = 10;
                if (isset($options['cost'])) {
                    $cost = $options['cost'];
                    if ($cost < 4 || $cost > 31) {
                        trigger_error(sprintf("password_hash(): Invalid bcrypt cost parameter specified: %d", $cost), E_USER_WARNING);
                        return null;
                    }
                }
                // The length of salt to generate
                $raw_salt_len = 16;
                // The length required in the final serialization
                $required_salt_len = 22;
                $hash_format = sprintf("$2y$%02d$", $cost);
                // The expected length of the final crypt() output
                $resultLength = 60;
                break;
            default:
                trigger_error(sprintf("password_hash(): Unknown password hashing algorithm: %s", $algo), E_USER_WARNING);
                return null;
        }
        $salt_requires_encoding = false;
        if (isset($options['salt'])) {
            switch (gettype($options['salt'])) {
                case 'NULL':
                case 'boolean':
                case 'integer':
                case 'double':
                case 'string':
                    $salt = (string) $options['salt'];
                    break;
                case 'object':
                    if (method_exists($options['salt'], '__tostring')) {
                        $salt = (string) $options['salt'];
                        break;
                    }
                case 'array':
                case 'resource':
                default:
                    trigger_error('password_hash(): Non-string salt parameter supplied', E_USER_WARNING);
                    return null;
            }
            if (PasswordCompat\binary\_strlen($salt) < $required_salt_len) {
                trigger_error(sprintf("password_hash(): Provided salt is too short: %d expecting %d", PasswordCompat\binary\_strlen($salt), $required_salt_len), E_USER_WARNING);
                return null;
            } elseif (0 == preg_match('#^[a-zA-Z0-9./]+$#D', $salt)) {
                $salt_requires_encoding = true;
            }
        } else {
            $buffer = '';
            $buffer_valid = false;
            if (function_exists('mcrypt_create_iv') && !defined('PHALANGER')) {
                $buffer = mcrypt_create_iv($raw_salt_len, MCRYPT_DEV_URANDOM);
                if ($buffer) {
                    $buffer_valid = true;
                }
            }
            if (!$buffer_valid && function_exists('openssl_random_pseudo_bytes')) {
                $buffer = openssl_random_pseudo_bytes($raw_salt_len);
                if ($buffer) {
                    $buffer_valid = true;
                }
            }
            if (!$buffer_valid && @is_readable('/dev/urandom')) {
                $f = fopen('/dev/urandom', 'r');
                $read = PasswordCompat\binary\_strlen($buffer);
                while ($read < $raw_salt_len) {
                    $buffer .= fread($f, $raw_salt_len - $read);
                    $read = PasswordCompat\binary\_strlen($buffer);
                }
                fclose($f);
                if ($read >= $raw_salt_len) {
                    $buffer_valid = true;
                }
            }
            if (!$buffer_valid || PasswordCompat\binary\_strlen($buffer) < $raw_salt_len) {
                $bl = PasswordCompat\binary\_strlen($buffer);
                for ($i = 0; $i < $raw_salt_len; $i++) {
                    if ($i < $bl) {
                        $buffer[$i] = $buffer[$i] ^ chr(mt_rand(0, 255));
                    } else {
                        $buffer .= chr(mt_rand(0, 255));
                    }
                }
            }
            $salt = $buffer;
            $salt_requires_encoding = true;
        }
        if ($salt_requires_encoding) {
            // encode string with the Base64 variant used by crypt
            $base64_digits =
                'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
            $bcrypt64_digits =
                './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

            $base64_string = base64_encode($salt);
            $salt = strtr(rtrim($base64_string, '='), $base64_digits, $bcrypt64_digits);
        }
        $salt = PasswordCompat\binary\_substr($salt, 0, $required_salt_len);

        $hash = $hash_format . $salt;

        $ret = crypt($password, $hash);

        if (!is_string($ret) || PasswordCompat\binary\_strlen($ret) != $resultLength) {
            return false;
        }

        return $ret;
    }

    /**
     * Get information about the password hash. Returns an array of the information
     * that was used to generate the password hash.
     *
     * array(
     *    'algo' => 1,
     *    'algoName' => 'bcrypt',
     *    'options' => array(
     *        'cost' => 10,
     *    ),
     * )
     *
     * @param string $hash The password hash to extract info from
     *
     * @return array The array of information about the hash.
     */
    function password_get_info($hash) {
        $return = array(
            'algo' => 0,
            'algoName' => 'unknown',
            'options' => array(),
        );
        if (PasswordCompat\binary\_substr($hash, 0, 4) == '$2y$' && PasswordCompat\binary\_strlen($hash) == 60) {
            $return['algo'] = PASSWORD_BCRYPT;
            $return['algoName'] = 'bcrypt';
            list($cost) = sscanf($hash, "$2y$%d$");
            $return['options']['cost'] = $cost;
        }
        return $return;
    }

    /**
     * Determine if the password hash needs to be rehashed according to the options provided
     *
     * If the answer is true, after validating the password using password_verify, rehash it.
     *
     * @param string $hash    The hash to test
     * @param int    $algo    The algorithm used for new password hashes
     * @param array  $options The options array passed to password_hash
     *
     * @return boolean True if the password needs to be rehashed.
     */
    function password_needs_rehash($hash, $algo, array $options = array()) {
        $info = password_get_info($hash);
        if ($info['algo'] != $algo) {
            return true;
        }
        switch ($algo) {
            case PASSWORD_BCRYPT:
                $cost = isset($options['cost']) ? $options['cost'] : 10;
                if ($cost != $info['options']['cost']) {
                    return true;
                }
                break;
        }
        return false;
    }

    /**
     * Verify a password against a hash using a timing attack resistant approach
     *
     * @param string $password The password to verify
     * @param string $hash     The hash to verify against
     *
     * @return boolean If the password matches the hash
     */
    function password_verify($password, $hash) {
        if (!function_exists('crypt')) {
            trigger_error("Crypt must be loaded for password_verify to function", E_USER_WARNING);
            return false;
        }
        $ret = crypt($password, $hash);
        if (!is_string($ret) || PasswordCompat\binary\_strlen($ret) != PasswordCompat\binary\_strlen($hash) || PasswordCompat\binary\_strlen($ret) <= 13) {
            return false;
        }

        $status = 0;
        for ($i = 0; $i < PasswordCompat\binary\_strlen($ret); $i++) {
            $status |= (ord($ret[$i]) ^ ord($hash[$i]));
        }

        return $status === 0;
    }
}

}

namespace PasswordCompat\binary {
    /**
     * Count the number of bytes in a string
     *
     * We cannot simply use strlen() for this, because it might be overwritten by the mbstring extension.
     * In this case, strlen() will count the number of *characters* based on the internal encoding. A
     * sequence of bytes might be regarded as a single multibyte character.
     *
     * @param string $binary_string The input string
     *
     * @internal
     * @return int The number of bytes
     */
    function _strlen($binary_string) {
           if (function_exists('mb_strlen')) {
               return mb_strlen($binary_string, '8bit');
           }
           return strlen($binary_string);
    }

    /**
     * Get a substring based on byte limits
     *
     * @see _strlen()
     *
     * @param string $binary_string The input string
     * @param int    $start
     * @param int    $length
     *
     * @internal
     * @return string The substring
     */
    function _substr($binary_string, $start, $length) {
       if (function_exists('mb_substr')) {
           return mb_substr($binary_string, $start, $length, '8bit');
       }
       return substr($binary_string, $start, $length);
   }

}
