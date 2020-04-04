<?php
/**
 * Interface representing standard and extended HTTP status codes. These codes
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
 * @package Teapot\StatusCode
 *
 * @author    Barney Hanlon <barney@shrikeh.net>
 * @copyright 2013-2016 B Hanlon. All rights reserved.
 * @license   MIT http://opensource.org/licenses/MIT
 *
 * @codingStandardsIgnoreStart
 *
 * @link https://shrikeh.github.com/teapot
 * @link http://lists.w3.org/Archives/Public/public-web-perf/2013Apr/att-0007/WebRequestStatusCodes4.html
 * @codingStandardsIgnoreEnd
 */
namespace Teapot\StatusCode;

use Teapot\StatusCode\RFC\RFC7168 as HTCPCPTea;
use Teapot\StatusCode\RFC\RFC7231 as SemanticsAndContent;
use Teapot\StatusCode\RFC\RFC7232 as ConditionalRequests;
use Teapot\StatusCode\RFC\RFC7233 as RangeRequests;
use Teapot\StatusCode\RFC\RFC7234 as Caching;
use Teapot\StatusCode\RFC\RFC7235 as Authentication;
use Teapot\StatusCode\RFC\RFC7538 as PermanentRedirect;
use Teapot\StatusCode\RFC\RFC7540 as HTTP2;
use Teapot\StatusCode\RFC\RFC7725 as LegalObstacles;

/**
 * Interface representing standard and extended HTTP status codes. These codes
 * are represented as an interface so that developers may implement it and then
 * use parent::[CODE] to gain a code, or to extend the codes using
 * static::[CODE] and override their default description.
 *
 * This allows for codes to be repurposed in a natural way where the core,
 * traditional use would not be meaningful.
 *
 * @category StatusCode
 *
 * @package Teapot\StatusCode
 *
 * @author    Barney Hanlon <barney@shrikeh.net>
 * @copyright 2013-2016 B Hanlon. All rights reserved.
 * @license   MIT http://opensource.org/licenses/MIT
 *
 * @codingStandardsIgnoreStart
 *
 * @link https://shrikeh.github.com/teapot
 * @link http://lists.w3.org/Archives/Public/public-web-perf/2013Apr/att-0007/WebRequestStatusCodes4.html
 * @codingStandardsIgnoreEnd
 */
interface Http extends
    HTCPCPTea,
    SemanticsAndContent,
    ConditionalRequests,
    RangeRequests,
    Caching,
    Authentication,
    PermanentRedirect,
    HTTP2,
    LegalObstacles
{
}
