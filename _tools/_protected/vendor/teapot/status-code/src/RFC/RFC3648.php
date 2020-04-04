<?php
/**
 * Interface representing extended HTTP status codes for RFC3648. These codes
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
 * Interface representing extended HTTP status codes for RFC3648. These codes
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
interface RFC3648 extends ProposedStandard, IETFStream
{
    /**
     * Unordered Collection (Internet draft).
     *
     * The 425 (Unordered Collection) status code indicates that the client
     * attempted to set the position of an internal collection member in an
     * unordered collection or in a collection with a server-maintained
     * ordering.
     * Defined in drafts of "WebDAV Advanced Collections Protocol", but not
     * present in "Web Distributed Authoring and Versioning (WebDAV) Ordered
     * Collections Protocol".
     *
     * @codingStandardsIgnoreStart
     *
     * @link https://tools.ietf.org/html/draft-ietf-webdav-collection-protocol-04#section-7.2
     * @codingStandardsIgnoreEnd
     *
     * @var int
     */
    const UNORDERED_COLLECTION = 425;
}
