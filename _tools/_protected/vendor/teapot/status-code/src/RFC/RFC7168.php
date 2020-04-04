<?php
/**
 * Interface representing extended HTTP status codes for RFC7168. These codes
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

use Teapot\StatusCode\RFC\Status\Informational as InformationalStatus;
use Teapot\StatusCode\RFC\Stream\IETF as IETFStream;

/**
 * Interface representing extended HTTP status codes for RFC7168. These codes
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
interface RFC7168 extends InformationalStatus, IETFStream
{
    /**
     * A BREW request to the "/" URI, as defined in Section 2.1.1, will
     * return an Alternates header indicating the URIs of the available
     * varieties of tea to brew.  It is RECOMMENDED that this response be
     * served with a status code of 300, to indicate that brewing has not
     * commenced and further options must be chosen by the client.
     *
     * @link https://www.rfc-editor.org/rfc/rfc7168.txt
     * @var int
     */
    const MULTIPLE_OPTIONS = 300;

    /**
     * Services that implement the Accept-Additions header field MAY return
     * a 403 status code for a BREW request of a given variety of tea, if
     * the service deems the combination of additions requested to be
     * contrary to the sensibilities of a consensus of drinkers regarding
     * the variety in question.
     *
     * A method of garnering and collating consensus indicators of the most
     * viable combinations of additions for each variety to be served is
     * outside the scope of this document.
     *
     * @link https://www.rfc-editor.org/rfc/rfc7168.txt
     * @var int
     */
    const BREW_FORBIDDEN = 403;

    /**
     * TEA-capable pots that are not provisioned to brew coffee may return
     * either a status code of 503, indicating temporary unavailability of
     * coffee, or a code of 418 as defined in the base HTCPCP specification
     * to denote a more permanent indication that the pot is a teapot.
     *
     * @link https://www.rfc-editor.org/rfc/rfc7168.txt
     * @var int
     */
    const IM_A_TEAPOT = 418;
}
