<?php
/**
 * Interface representing extended HTTP status codes for Cloudflare. These codes
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
 * Interface representing extended HTTP status codes for Cloudflare. These codes
 * are represented as an interface so that developers may implement it and then
 * use parent::[CODE] to gain a code, or to extend the codes using
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
interface CloudFlare
{
    /**
     * This status code is not specified in any RFCs, but is used by
     * Cloudflare's reverse proxies to signal an "unknown connection issue
     * between CloudFlare  and the origin web server" to a client in front of
     * the proxy.
     *
     * One potential cause could be that your web server is sending a response
     * header that exceeds CloudFlare's maximum response header size. This could
     * be the case if you're sending an abnormally high number of cookies for
     * example.
     *
     * @codingStandardsIgnoreStart
     *
     * @link https://support.cloudflare.com/hc/en-us/articles/200171936-Error-520
     * @codingStandardsIgnoreEnd
     *
     * @var int
     */
    const ORIGIN_ERROR = 520;

    /**
     * An Error 521 means that the origin web server refused the connection from
     * CloudFlare.
     *
     * There are two main reasons why this would occur. In both cases, work with
     * your hosting provider to help resolve the issue.
     *
     * 1) The origin web server is not turned on
     * 2) Something on the web server or hosting provider's network is blocking
     * CloudFlare's requests. Since CloudFlare acts as a reverse proxy, all
     * connections to your server come from a CloudFlare IP. Since the same
     * amount of traffic now comes from a smaller number of IPs, server-side
     * security solutions can mistake the increase in connections from this
     * smaller set of IPs as an attack, when they are legitimate.
     *
     * @codingStandardsIgnoreStart
     *
     * @link https://support.cloudflare.com/hc/en-us/articles/200171916-Error-521
     * @codingStandardsIgnoreEnd
     *
     * @var int
     */
    const ORIGIN_DECLINED_REQUEST = 521;

    /**
     * An Error 522 means that the connection started on the origin web server,
     * but that the request was not completed. The most common reason why this
     * would occur is that either a program, cron job or resource is taking up
     * more resources than it should causing the server not to be able to
     * respond to all requests properly. The origin web server is not
     * functioning consistently for each request. Contact your hosting provider
     * to identify and resolve the issue.
     *
     * The difference between a 522 and 524 error is that with a 522 error, the
     * connection times out before the request is completed. This means that
     * the server is overloaded.
     *
     * With a 524 error, the connection is made but then the request times out.
     * The likely cause is that a background task is timing out. The most common
     * cause would be the database or a slow application.
     *
     * @codingStandardsIgnoreStart
     *
     * @link https://support.cloudflare.com/hc/en-us/articles/200171906-Error-522
     * @codingStandardsIgnoreEnd
     *
     * @var int
     */
    const CONNECTION_TIMED_OUT = 522;

    /**
     * This status code is not specified in any RFCs, but is used by
     * Cloudflare's reverse proxies to signal a resource that has been blocked
     * by the administrator of the website or proxy itself.
     *
     * The most common cause is that the DNS setting has changed. Sometimes,
     * hosting providers update the origin IP information for their customers.
     * If this is the case, you need to make sure the new origin IP address for
     * your A record is reflected in your CloudFlare DNS Settings page.
     *
     * As soon as you make the change in your CloudFlare DNS Settings page, wait
     * up to 5 minutes for it to take effect.
     *
     * @codingStandardsIgnoreStart
     *
     * @link https://support.cloudflare.com/hc/en-us/articles/200171946-Error-523
     * @codingStandardsIgnoreEnd
     *
     * @var int
     */
    const PROXY_DECLINED_REQUEST = 523;

    /**
     * This status code is not specified in any RFCs, but is used by
     * Cloudflare's reverse proxies to signal a network read timeout behind the
     * proxy to a client in front of the proxy.
     *
     * An Error 524 means that the connection to the origin web server was made,
     * but the origin web server timed out before responding to the request. The
     * likely cause is either an overloaded background task, database or
     * application (i.e. such as Wordpress), over stressing the resources on
     * your machine. The database is often the cause.
     *
     * The difference between a 522 and 524 error is that with a 522 error, the
     * connection times out before the request is completed. This means that the
     * server is overloaded. With a 524 error, the connection is made but then
     * the request times out.
     *
     * @codingStandardsIgnoreStart
     *
     * @link https://support.cloudflare.com/hc/en-us/articles/200171926-Error-524
     * @codingStandardsIgnoreEnd
     *
     * @var int
     */
    const TIMEOUT_OCCURRED = 524;
}
