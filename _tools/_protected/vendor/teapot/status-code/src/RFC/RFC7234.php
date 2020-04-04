<?php
/**
 * Interface representing extended HTTP status codes for RFC7234. These codes
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
 * @copyright 2016 B Hanlon. All rights reserved.
 * @license   MIT http://opensource.org/licenses/MIT
 *
 * @link https://shrikeh.github.com/teapot
 */
namespace Teapot\StatusCode\RFC;

use Teapot\StatusCode\RFC\Status\ProposedStandard;
use Teapot\StatusCode\RFC\Stream\IETF as IETFStream;

/**
 * Interface representing extended HTTP status codes for RFC7234. These codes
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

interface RFC7234 extends ProposedStandard, IETFStream
{
    /**
     * A cache SHOULD generate this whenever the sent response is stale.
     *
     * @link https://tools.ietf.org/html/rfc7234#section-5.5.1
     * @var  int
     */
    const RESPONSE_IS_STALE = 110;

    /**
     * A cache SHOULD generate this when sending a stale response because an
     * attempt to validate the response failed, due to an inability to reach
     * the server.
     *
     * @link https://tools.ietf.org/html/rfc7234#section-5.5.2
     * @var  int
     */
    const REVALIDATION_FAILED = 111;

    /**
     * A cache SHOULD generate this if it is intentionally disconnected from
     * the rest of the network for a period of time.
     *
     * @link https://tools.ietf.org/html/rfc7234#section-5.5.3
     * @var  int
     */
    const DISCONNECTED_OPERATION = 112;

    /**
     * A cache SHOULD generate this if it heuristically chose a freshness
     * lifetime greater than 24 hours and the response's age is greater than
     * 24 hours.
     *
     * @link https://tools.ietf.org/html/rfc7234#section-5.5.4
     * @var  int
     */
    const HEURISTIC_EXPIRATION = 113;

    /**
     * The warning text can include arbitrary information to be presented to
     * a human user or logged.  A system receiving this warning MUST NOT
     * take any automated action, besides presenting the warning to the
     * user.
     *
     * @link https://tools.ietf.org/html/rfc7234#section-5.5.5
     * @var  int
     */
    const MISCELLANEOUS_WARNING = 199;

    /**
     * This Warning code MUST be added by a proxy if it applies any
     * transformation to the representation, such as changing the
     * content-coding, media-type, or modifying the representation data,
     * unless this Warning code already appears in the response.
     *
     * @link https://tools.ietf.org/html/rfc7234#section-5.5.6
     * @var  int
     */
    const TRANSFORMATION_APPLIED = 214;

    /**
     * The warning text can include arbitrary information to be presented to
     * a human user or logged.  A system receiving this warning MUST NOT
     * take any automated action.
     *
     * @link https://tools.ietf.org/html/rfc7234#section-5.5.7
     * @var  int
     */
    const MISCELLANEOUS_PERSISTENT_WARNING = 299;
}
