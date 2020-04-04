<?php
/**
 * Interface representing extended HTTP status codes for PEP
 * (Protocol Extension Protocol: http://www.w3.org/TR/WD-http-pep). These codes
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

use Teapot\StatusCode\RFC\Status\Expired as ExpiredStatus;
use Teapot\StatusCode\RFC\Stream\IETF as IETFStream;

/**
 * Interface representing extended HTTP status codes for PEP
 * (Protocol Extension Protocol: http://www.w3.org/TR/WD-http-pep). These codes
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
interface PEP extends ExpiredStatus, IETFStream
{
    /**
     * The policy for accessing the resource has not been met in the request.
     * The response MUST include a PEP-Info or a C-PEP-Info header field
     * specifying the extensions required by the publishing party for accessing
     * the resource. The server MAY use the for attribute bag to indicate
     * whether the policy applies to other resources.
     *
     * The client MAY repeat the request using the appropriate extensions). If
     * the initial request already included the extensions requested in the 420
     * response, then the response indicates that access has been refused for
     * those extension declarations.
     * If the 420 response contains the same set of extension policies as the
     * prior response, then the client MAY present any entity included in the
     * response to the user, since that entity may include relevant diagnostic
     * information.
     * Implementers may note the similarity to the way authentication
     * challenges are issued with the 401 (Unauthorized) status-code.
     *
     * @link http://www.w3.org/TR/WD-http-pep-971121.html#_Toc404743960
     * @deprecated
     * @var int
     */
    const POLICY_NOT_FULFILLED = 420;

    /**
     * The mappings indicated by one or more map attribute bags in the request
     * were not unique and mapped the same header field more than once.
     * The client MAY repeat the request using a new set of mappings if it
     * believes that it can find a unique set of header fields for which the
     * transaction will succeed.
     *
     * @link http://www.w3.org/TR/WD-http-pep-971121.html#_Toc404743961
     * @deprecated
     * @var int
     */
    const BAD_MAPPING = 421;
}
