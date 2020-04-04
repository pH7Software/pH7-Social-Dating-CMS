<?php
/**
 * Interface representing a Value Object of the HTTP Status-Line, as
 * specified in RFC 2616 and RFC 7231.
 *
 * PHP version 5.3
 *
 * @category StatusLine
 *
 * @package Teapot
 *
 * @author    Barney Hanlon <barney@shrikeh.net>
 * @copyright 2013-2016 B Hanlon. All rights reserved.
 * @license   MIT http://opensource.org/licenses/MIT
 * @link      https://shrikeh.github.com/teapot
 */
namespace Teapot;

use Psr\Http\Message\ResponseInterface;

/**
 * Interface representing a Value Object of the HTTP Status-Line, as
 * specified in RFC 2616 and RFC 7231.
 *
 * PHP version 5.3
 *
 * @category StatusLine
 *
 * @package Teapot
 *
 * @author    Barney Hanlon <barney@shrikeh.net>
 * @copyright 2013-2016 B Hanlon. All rights reserved.
 * @license   MIT http://opensource.org/licenses/MIT
 * @link      https://shrikeh.github.com/teapot
 */
interface StatusLine
{
    /**
     * Return the response code.
     *
     * @return int
     */
    public function statusCode();

    /**
     * Return the reason phrase
     *
     * @return string
     */
    public function reasonPhrase();

    /**
     * Add the status code and reason phrase to a Response.
     *
     * @param ResponseInterface $response The response
     * @return ResponseInterface
     */
    public function response(ResponseInterface $response);

    /**
     * Return the status class of the response code.
     *
     * @return int
     */
    public function statusClass();

    /**
     * Helper to establish if the class of the status code
     * is informational (1xx).
     *
     * @return bool
     */
    public function isInformational();

    /**
     * Helper to establish if the class of the status code
     * is successful (2xx).
     *
     * @return bool
     */
    public function isSuccessful();

    /**
     * Helper to establish if the class of the status code
     * is redirection (3xx).
     *
     * @return bool
     */
    public function isRedirection();

    /**
     * Helper to establish if the class of the status code
     * is client error (4xx).
     *
     * @return bool
     */
    public function isClientError();

    /**
     * Helper to establish if the class of the status code
     * is server error (5xx).
     *
     * @return bool
     */
    public function isServerError();
}
