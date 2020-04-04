<?php
/**
 * Interface representing extended HTTP status codes for vendor-specific codes.
 * These codes are represented as an interface so that developers may implement
 * it and then use parent::[CODE] to gain a code, or to extend the codes using
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
 * Interface representing extended HTTP status codes for vendor-specific codes.
 * These codes are represented as an interface so that developers may implement
 * it and then use parent::[CODE] to gain a code, or to extend the codes using
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
interface Twitter
{
    /**
     * Returned by the version 1 Search and Trends APIs when you are being rate
     * limited.
     *
     * @link https://dev.twitter.com/docs/rate-limiting/1
     *
     * @var int
     */
    const ENHANCE_YOUR_CALM = 420;
}
