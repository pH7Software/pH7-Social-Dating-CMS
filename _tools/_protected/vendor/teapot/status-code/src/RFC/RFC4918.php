<?php
/**
 * Interface representing extended HTTP status codes for RFC4918. These codes
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
 * Interface representing extended HTTP status codes for RFC4918. These codes
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
interface RFC4918 extends ProposedStandard, IETFStream
{
    /**
     * The message body that follows is an XML message and can contain a
     * number of separate response codes, depending on how many sub-requests
     * were made.
     *
     * Multiple resources were to be affected by the COPY, but errors on some of
     * them prevented the operation from taking place.  Specific error messages,
     * together with the most appropriate of the source and destination URLs,
     * appear in the body of the multi-status response.  For example, if a
     * destination resource was locked and could not be overwritten, then the
     * destination resource URL appears with the 423 (Locked) status.
     *
     * @link http://www.ietf.org/rfc/rfc4918.txt
     *
     * @var int
     */
    const MULTI_STATUS = 207;

    /**
     * The 422 (Unprocessable Entity) status code means the server understands
     * the content type of the request entity (hence a
     * 415[Unsupported Media Type] status code is inappropriate), and the
     * syntax of the request entity is correct (thus a 400 (Bad Request)
     * status code is inappropriate) but was unable to process the contained
     * instructions. For example, this error condition may occur if an XML
     * request body contains well-formed (i.e., syntactically correct), but
     * semantically erroneous, XML instructions.
     *
     * @link http://www.ietf.org/rfc/rfc4918.txt
     *
     * @var int
     */
    const UNPROCESSABLE_ENTITY = 422;

    /**
     * The 423 (Locked) status code means the source or destination resource
     * of a method is locked.  This response SHOULD contain an appropriate
     * precondition or post-condition code, such as 'lock-token-submitted' or
     * 'no-conflicting-lock'.
     *
     * @link http://www.ietf.org/rfc/rfc4918.txt
     *
     * @var int
     */
    const ENTITY_LOCKED = 423;

    /**
     * Indicates the method was not executed on a particular resource within
     * its scope because some part of the method's execution failed causing the
     * entire method to be aborted.
     *
     * @link http://www.ietf.org/rfc/rfc4918.txt
     *
     * @var int
     */
    const METHOD_FAILURE = 424;

    /**
     * The 424 (Failed Dependency) status code means that the method could not
     * be performed on the resource because the requested action
     * depended on another action and that action failed. For example, if a
     * command in a PROPPATCH method fails then, at minimum, the rest
     * of the commands will also fail with 424 (Failed Dependency).
     *
     * @link http://www.ietf.org/rfc/rfc4918.txt
     *
     * @var int
     */
    const FAILED_DEPENDENCY = 424;

    /**
     * The server is unable to store the representation needed to complete the
     * request.
     *
     * @link http://www.ietf.org/rfc/rfc4918.txt
     *
     * @var int
     */
    const INSUFFICIENT_STORAGE = 507;
}
