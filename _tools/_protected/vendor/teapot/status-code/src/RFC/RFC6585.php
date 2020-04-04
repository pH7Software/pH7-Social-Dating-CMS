<?php
/**
 * Interface representing extended HTTP status codes for RFC6585. These codes
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
 * @package Teapot\StatusCode\RFC
 *
 * @author    Barney Hanlon <barney@shrikeh.net>
 * @copyright 2013-2016 B Hanlon. All rights reserved.
 * @license   MIT http://opensource.org/licenses/MIT
 *
 * @link https://shrikeh.github.com/teapot
 */
namespace Teapot\StatusCode\RFC;

use Teapot\StatusCode\RFC\Status\ProposedStandard;
use Teapot\StatusCode\RFC\Stream\IETF as IETFStream;

/**
 * Interface representing extended HTTP status codes for RFC6585. These codes
 * are represented as an interface so that developers may implement it and then
 * use parent::[CODE] to gain a code, or to extend the codes using
 * static::[CODE] and override their default description.
 *
 * This allows for codes to be repurposed in a natural way where the core,
 * traditional use would not be meaningful.
 *
 * @category StatusCode
 *
 * @package Teapot\StatusCode\RFC
 *
 * @author    Barney Hanlon <barney@shrikeh.net>
 * @copyright 2013-2016 B Hanlon. All rights reserved.
 * @license   MIT http://opensource.org/licenses/MIT
 *
 * @link https://shrikeh.github.com/teapot
 */
interface RFC6585 extends ProposedStandard, IETFStream
{
    /**
     * The origin server requires the request to be conditional. Its typical
     * use is to avoid the "lost update" problem, where a client GETs a
     * resource's state, modifies it, and PUTs it back to the server, when
     * meanwhile a third party has modified the state on the server, leading to
     * a conflict.  By requiring requests to be conditional, the server can
     * assure that clients are working with the correct copies.
     *
     * Responses using this status code SHOULD explain how to resubmit the
     * request successfully.
     *
     * Responses with the 428 status code MUST NOT be stored by a cache.
     *
     * @link http://tools.ietf.org/html/rfc6585
     *
     * @var int
     */
    const PRECONDITION_REQUIRED = 428;

    /**
     * The 429 status code indicates that the user has sent too many requests
     * in a given amount of time ("rate limiting").
     *
     * The response representations SHOULD include details explaining the
     * condition, and MAY include a Retry-After header indicating how long
     * to wait before making a new request.
     *
     * For example:
     *
     * HTTP/1.1 429 Too Many Requests
     * Content-Type: text/html
     * Retry-After: 3600
     * <html>
     *   <head>
     *     <title>Too Many Requests</title>
     *   </head>
     *   <body>
     *     <h1>Too Many Requests</h1>
     *     <p>I only allow 50 requests per hour to this Web site per
     *     logged in user.  Try again soon.</p>
     *   </body>
     * </html>
     *
     * Note that this specification does not define how the origin server
     * identifies the user, nor how it counts requests.  For example, an
     * origin server that is limiting request rates can do so based upon
     * counts of requests on a per-resource basis, across the entire server,
     * or even among a set of servers.  Likewise, it might identify the user
     * by its authentication credentials, or a stateful cookie.
     *
     * Responses with the 429 status code MUST NOT be stored by a cache.
     *
     * @link http://tools.ietf.org/html/rfc6585
     *
     * @var int
     */
    const TOO_MANY_REQUESTS = 429;

    /**
     * The 431 status code indicates that the server is unwilling to process
     * the request because its header fields are too large.  The request MAY
     * be resubmitted after reducing the size of the request header fields.
     *
     * It can be used both when the set of request header fields in total is
     * too large, and when a single header field is at fault.  In the latter
     * case, the response representation SHOULD specify which header field
     * was too large.
     *
     * @link http://tools.ietf.org/html/rfc6585
     *
     * @var int
     */
    const REQUEST_HEADER_FIELDS_TOO_LARGE = 431;

    /**
     * The 511 status code is designed to mitigate problems caused by
     * "captive portals" to software (especially non-browser agents) that is
     * expecting a response from the server that a request was made to, not
     * the intervening network infrastructure.  It is not intended to
     * encourage deployment of captive portals -- only to limit the damage
     * caused by them.
     *
     * A network operator wishing to require some authentication, acceptance
     * of terms, or other user interaction before granting access usually
     * does so by identifying clients who have not done so ("unknown
     * clients") using their Media Access Control (MAC) addresses.
     *
     * Unknown clients then have all traffic blocked, except for that on TCP
     * port 80, which is sent to an HTTP server (the "login server")
     * dedicated to "logging in" unknown clients, and of course traffic to
     * the login server itself.
     *
     * @link http://tools.ietf.org/html/rfc6585
     *
     * @var int
     */
    const NETWORK_AUTHENTICATION_REQUIRED = 511;
}
