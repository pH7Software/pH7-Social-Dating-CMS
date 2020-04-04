<?php
/**
 * Interface representing extended HTTP status codes for RFC7235. These codes
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
 * Interface representing extended HTTP status codes for RFC7235. These codes
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
interface RFC7235 extends ProposedStandard, IETFStream
{
    /**
     * The 401 (Unauthorized) status code indicates that the request has not
     * been applied because it lacks valid authentication credentials for the
     * target resource. The server generating a 401 response must send a
     * WWW-Authenticate header field (Section 4.1) containing at least one
     * challenge applicable to the target resource.
     *
     * If the request included authentication credentials, then the 401 response
     * indicates that authorization has been refused for those credentials. The
     * user agent may repeat the request with a new or replaced Authorization
     * header field (Section 4.2). If the 401 response contains the same
     * challenge as the prior response, and the user agent has already attempted
     * authentication at least once, then the user agent should present the
     * enclosed representation to the user, since it usually contains relevant
     * diagnostic information.
     *
     * @codingStandardsIgnoreStart
     *
     * @link https://svn.tools.ietf.org/svn/wg/httpbis/specs/rfc7235.html#status.401
     * @codingStandardsIgnoreEnd
     * @var int
     */
    const UNAUTHORIZED = 401;

    /**
     * The 407 (Proxy Authentication Required) status code is similar to 401
     * (Unauthorized), but it indicates that the client needs to authenticate
     * itself in order to use a proxy. The proxy must send a Proxy-Authenticate
     * header field (Section 4.3) containing a challenge applicable to that
     * proxy for the target resource. The client may repeat the request with a
     * new or replaced Proxy-Authorization header field (Section 4.4).
     *
     * @codingStandardsIgnoreStart
     *
     * @link https://svn.tools.ietf.org/svn/wg/httpbis/specs/rfc7235.html#status.407
     * @codingStandardsIgnoreEnd
     * @var int
     */
    const PROXY_AUTHENTICATION_REQUIRED = 407;
}
