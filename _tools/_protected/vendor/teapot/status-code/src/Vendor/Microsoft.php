<?php
/**
 * Interface representing extended HTTP status codes for Microsoft. These codes
 * are represented as an interface so that developers may implement it and then
 * use parent::[CODE] to gain a code, or to extend the codes using
 * static::[CODE] and override their default description.
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
 * Interface representing extended HTTP status codes for Microsoft. These codes
 * are represented as an interface so that developers may implement it and then
 * use parent::[CODE] to gain a code, or to extend the codes using
 * static::[CODE] and override their default description.
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
interface Microsoft
{
    /**
     * A Microsoft extension. Indicates that your session has expired.
     *
     * @var int
     */
    const LOGIN_TIMEOUT = 440;

    /**
     * A Microsoft extension. The request should be retried after performing
     * the appropriate action. The new extension status code is defined as
     * follows (using the Augmented Backus-Naur Form (ABNF) syntax, as specified
     * in RFC2616 section 2.1).
     *
     * Often search-engines or custom applications will ignore required
     * parameters. Where no default action is appropriate, the Aviongoo website
     * sends a "HTTP/1.1 449 Retry with valid parameters: param1, param2, . . ."
     * response. The applications may choose to learn, or not.
     *
     * @link http://msdn.microsoft.com/en-us/library/dd891478.aspx
     *
     * @var int
     */
    const RETRY_WITH = 449;

    /**
     * A Microsoft extension. This error is given when Windows Parental Controls
     * are turned on and are blocking access to the given webpage.
     *
     * @var int
     */
    const BLOCKED_BY_WINDOWS_PARENTAL_CONTROLS = 450;

    /**
     * Used in Exchange ActiveSync if there either is a more efficient server
     * to use or the server can't access the users' mailbox.
     *
     * The client is supposed to re-run the HTTP Autodiscovery protocol to find
     * a better suited server.
     *
     * @var int
     */
    const REDIRECT = 451;
}
