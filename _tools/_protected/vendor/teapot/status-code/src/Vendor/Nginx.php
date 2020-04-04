<?php
/**
 * Interface representing extended HTTP status codes for Nginx. These codes are
 * represented as an interface so that developers may implement it and then use
 * parent::[CODE] to gain a code, or to extend the codes using static::[CODE]
 * and override their default description.
 *
 * This allows for codes to be repurposed in a natural way where the core,
 * traditional use would not be meaningful.
 *
 * PHP version 5.3
 *
 * @category StatusCode
 *
 * @package Teapot\StatusCode\Vendor
 *
 * @author    Barney Hanlon <barney@shrikeh.net>
 * @copyright 2013-2016 B Hanlon. All rights reserved.
 * @license   MIT http://opensource.org/licenses/MIT
 *
 * @link https://shrikeh.github.com/teapot
 */
namespace Teapot\StatusCode\Vendor;

/**
 * Interface representing extended HTTP status codes for Nginx. These codes are
 * represented as an interface so that developers may implement it and then use
 * parent::[CODE] to gain a code, or to extend the codes using static::[CODE]
 * and override their default description.
 *
 * This allows for codes to be repurposed in a natural way where the core,
 * traditional use would not be meaningful.
 *
 * @category StatusCode
 *
 * @package Teapot\StatusCode\Vendor
 *
 * @author    Barney Hanlon <barney@shrikeh.net>
 * @copyright 2013-2016 B Hanlon. All rights reserved.
 * @license   MIT http://opensource.org/licenses/MIT
 *
 * @link https://shrikeh.github.com/teapot
 */
interface Nginx
{
    /**
     * Used in Nginx logs to indicate that the server has returned no
     * information to the client and closed the connection (useful as
     * a deterrent for malware).
     *
     * @var int
     */
    const NO_RESPONSE = 444;

    /**
     * Nginx internal code similar to 431 but it was introduced earlier.
     *
     * @var int
     */
    const REQUEST_HEADER_TOO_LARGE = 494;

    /**
     * Nginx internal code used when SSL client certificate error occurred to
     * distinguish it from 4XX in a log and an error page redirection.
     *
     * @var int
     */
    const CERT_ERROR = 495;

    /**
     * Nginx internal code used when client didn't provide certificate to
     * distinguish it from 4XX in a log and an error page redirection.
     *
     * @var int
     */
    const NO_CERT = 496;

    /**
     * Nginx internal code used for the plain HTTP requests that are sent to
     * HTTPS port to distinguish it from 4XX in a log and an error page
     * redirection.
     *
     * @var int
     */
    const HTTP_TO_HTTPS = 497;

    /**
     * Used in Nginx logs to indicate when the connection has been closed by
     * client while the server is still processing its request, making server
     * unable to send a status code back.
     *
     * @var int
     */
    const CLIENT_CLOSED_REQUEST = 499;
}
