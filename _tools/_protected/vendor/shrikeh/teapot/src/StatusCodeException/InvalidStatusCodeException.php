<?php
/**
 * Exception thrown when the Status Code is invalid.
 *
 * PHP version 5.3
 *
 * @category Exception
 *
 * @package Teapot
 *
 * @author    Barney Hanlon <barney@shrikeh.net>
 * @copyright 2013-2016 B Hanlon. All rights reserved.
 * @license   MIT http://opensource.org/licenses/MIT
 * @link      https://shrikeh.github.com/teapot
 */
namespace Teapot\StatusCodeException;

use InvalidArgumentException;

/**
 * Exception thrown when the Status Code is invalid.
 *
 * PHP version 5.3
 *
 * @category Exception
 *
 * @package Teapot
 *
 * @author    Barney Hanlon <barney@shrikeh.net>
 * @copyright 2013-2016 B Hanlon. All rights reserved.
 * @license   MIT http://opensource.org/licenses/MIT
 * @link      https://shrikeh.github.com/teapot
 */
class InvalidStatusCodeException extends InvalidArgumentException
{
    /**
     * Named constructor for non-numeric status codes.
     *
     * @param mixed $code the non-numeric code
     * @return InvalidStatusCodeException
     */
    public static function notNumeric($code)
    {
        return new self(sprintf(
            'Status code must be numeric, but received %d',
            $code
        ));
    }

    /**
     * Named constructor for numeric status codes below 100.
     *
     * @param mixed $code the status code
     * @return InvalidStatusCodeException
     */
    public static function notGreaterOrEqualTo100($code)
    {
        return new self(sprintf(
            'Status code must be 100 or greater but code was %d',
            $code
        ));
    }
}
