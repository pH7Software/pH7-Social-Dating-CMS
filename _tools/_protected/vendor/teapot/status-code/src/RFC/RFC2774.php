<?php
/**
 * Interface representing extended HTTP status codes for RFC2774. These codes
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

use Teapot\StatusCode\RFC\Status\Experimental as ExperimentalStatus;
use Teapot\StatusCode\RFC\Stream\Legacy as LegacyStream;

/**
 * Interface representing extended HTTP status codes for RFC2774. These codes
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
interface RFC2774 extends ExperimentalStatus, LegacyStream
{
    /**
     * The policy for accessing the resource has not been met in the
     * request.  The server should send back all the information necessary
     * for the client to issue an extended request. It is outside the scope
     * of this specification to specify how the extensions inform the client.
     *
     * If the 510 response contains information about extensions that were
     * not present in the initial request then the client MAY repeat the
     * request if it has reason to believe it can fulfill the extension
     * policy by modifying the request according to the information provided
     * in the 510 response. Otherwise the client MAY present any entity
     * included in the 510 response to the user, since that entity may
     * include relevant diagnostic information.
     *
     * @link http://tools.ietf.org/search/rfc2774#section-7
     *
     * @var int
     */
    const NOT_EXTENDED = 510;
}
