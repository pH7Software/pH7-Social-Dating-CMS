<?php
/**
 * Interface representing extended HTTP status codes for RFC7232. These codes
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
 * Interface representing extended HTTP status codes for RFC7232. These codes
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
interface RFC7232 extends ProposedStandard, IETFStream
{
    /**
     * The 304 (Not Modified) status code indicates that a conditional GET or
     * HEAD request has been received and would have resulted in a 200 (OK)
     * response if it were not for the fact that the condition evaluated to
     * false. In other words, there is no need for the server to transfer a
     * representation of the target resource because the request indicates that
     * the client, which made the request conditional, already has a valid
     * representation; the server is therefore redirecting the client to make
     * use of that stored representation as if it were the payload of a 200 (OK)
     * response.
     *
     * The server generating a 304 response must generate any of the following
     * header fields that would have been sent in a 200 (OK) response to the
     * same request: Cache-Control, Content-Location, Date, ETag, Expires, and
     * Vary.
     *
     * Since the goal of a 304 response is to minimize information transfer when
     * the recipient already has one or more cached representations, a sender
     * should not generate representation metadata other than the above listed
     * fields unless said metadata exists for the purpose of guiding cache
     * updates (e.g., Last-Modified might be useful if the response does not
     * have an ETag field).
     *
     * Requirements on a cache that receives a 304 response are defined in
     * Section 4.3.4 of [RFC7234]. If the conditional request originated with an
     * outbound client, such as a user agent with its own cache sending a
     * conditional GET to a shared proxy, then the proxy should forward the 304
     * response to that client.
     *
     * A 304 response cannot contain a message-body; it is always terminated by
     * the first empty line after the header fields.
     *
     * @codingStandardsIgnoreStart
     *
     * @link https://svn.tools.ietf.org/svn/wg/httpbis/specs/rfc7232.html#status.304
     * @codingStandardsIgnoreEnd
     * @var int
     */
    const NOT_MODIFIED = 304;

    /**
     * The 412 (Precondition Failed) status code indicates that one or more
     * conditions given in the request header fields evaluated to false when
     * tested on the server. This response code allows the client to place
     * preconditions on the current resource state (its current representations
     * and metadata) and, thus, prevent the request method from being applied if
     * the target resource is in an unexpected state.
     *
     * @codingStandardsIgnoreStart
     *
     * @link https://svn.tools.ietf.org/svn/wg/httpbis/specs/rfc7232.html#status.412
     * @codingStandardsIgnoreEnd
     * @var int
     */
    const PRECONDITION_FAILED = 412;
}
