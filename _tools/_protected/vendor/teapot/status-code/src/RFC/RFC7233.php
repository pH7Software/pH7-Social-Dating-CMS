<?php
/**
 * Interface representing extended HTTP status codes for RFC7233. These codes
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
 * Interface representing extended HTTP status codes for RFC7233. These codes
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
 * @copyright 2016 B Hanlon. All rights reserved.
 * @license   MIT http://opensource.org/licenses/MIT
 *
 * @link https://shrikeh.github.com/teapot
 */
interface RFC7233 extends ProposedStandard, IETFStream
{
    /**
     * The 206 (Partial Content) status code indicates that the server is
     * successfully fulfilling a range request for the target resource by
     * transferring one or more parts of the selected representation that
     * correspond to the satisfiable ranges found in the request's Range header
     * field (Section 3.1).
     *
     * If a single part is being transferred, the server generating the 206
     * response must generate a Content-Range header field, describing what
     * range of the selected representation is enclosed, and a payload
     * consisting of the range. For example:
     *
     * HTTP/1.1 206 Partial Content
     * Date: Wed, 15 Nov 1995 06:25:24 GMT
     * Last-Modified: Wed, 15 Nov 1995 04:58:08 GMT
     * Content-Range: bytes 21010-47021/47022
     * Content-Length: 26012
     * Content-Type: image/gif
     *
     * ... 26012 bytes of partial image data ...
     * If multiple parts are being transferred, the server generating the 206
     * response must generate a "multipart/byteranges" payload, as defined in
     * Appendix A, and a Content-Type header field containing the
     * multipart/byteranges media type and its required boundary parameter. To
     * avoid confusion with single-part responses, a server must not generate a
     * Content-Range header field in the HTTP header section of a multiple part
     * response (this field will be sent in each part instead).
     * Within the header area of each body part in the multipart payload, the
     * server must generate a Content-Range header field corresponding to the
     * range being enclosed in that body part. If the selected representation
     * would have had a Content-Type header field in a 200 (OK) response, the
     * server should generate that same Content-Type field in the header area of
     * each body part. For example:
     *
     * HTTP/1.1 206 Partial Content
     * Date: Wed, 15 Nov 1995 06:25:24 GMT
     * Last-Modified: Wed, 15 Nov 1995 04:58:08 GMT
     * Content-Length: 1741
     * Content-Type: multipart/byteranges; boundary=THIS_STRING_SEPARATES
     *
     * --THIS_STRING_SEPARATES
     * Content-Type: application/pdf
     * Content-Range: bytes 500-999/8000
     *
     * ...the first range...
     * --THIS_STRING_SEPARATES
     * Content-Type: application/pdf
     * Content-Range: bytes 7000-7999/8000
     *
     * ...the second range
     * --THIS_STRING_SEPARATES--
     * When multiple ranges are requested, a server may coalesce any of the
     * ranges that overlap, or that are separated by a gap that is smaller than
     * the overhead of sending multiple parts, regardless of the order in which
     * the corresponding byte-range-spec appeared in the received Range header
     * field. Since the typical overhead between parts of a multipart/byteranges
     * payload is around 80 bytes, depending on the selected representation's
     * media type and the chosen boundary parameter length, it can be less
     * efficient to transfer many small disjoint parts than it is to transfer
     * the entire selected representation.¶
     *
     * A server must not generate a multipart response to a request for a single
     * range, since a client that does not request multiple parts might not
     * support multipart responses. However, a server may generate a
     * multipart/byteranges payload with only a single body part if multiple
     * ranges were requested and only one range was found to be satisfiable or
     * only one range remained after coalescing. A client that cannot process a
     * multipart/byteranges response must not generate a request that asks for
     * multiple ranges.
     *
     * When a multipart response payload is generated, the server should send
     * the parts in the same order that the corresponding byte-range-spec
     * appeared in the received Range header field, excluding those ranges that
     * were deemed unsatisfiable or that were coalesced into other ranges. A
     * client that receives a multipart response must inspect the Content-Range
     * header field present in each body part in order to determine which range
     * is contained in that body part; a client cannot rely on receiving the
     * same ranges that it requested, nor the same order that it requested.
     *
     * When a 206 response is generated, the server must generate the following
     * header fields, in addition to those required above, if the field would
     * have been sent in a 200 (OK) response to the same request: Date,
     * Cache-Control, ETag, Expires, Content-Location, and Vary.
     *
     * If a 206 is generated in response to a request with an If-Range header
     * field, the sender should not generate other representation header fields
     * beyond those required above, because the client is understood to already
     * have a prior response containing those header fields. Otherwise, the
     * sender must generate all of the representation header fields that would
     * have been sent in a 200 (OK) response to the same request.
     *
     * A 206 response is cacheable by default; i.e., unless otherwise indicated
     * by explicit cache controls (see Section 4.2.2 of [RFC7234]).
     *
     * @codingStandardsIgnoreStart
     *
     * @link https://svn.tools.ietf.org/svn/wg/httpbis/specs/rfc7233.html#status.206
     * @codingStandardsIgnoreEnd
     *
     * @var int
     */
    const PARTIAL_CONTENT = 206;

    /**
    * The 416 (Range Not Satisfiable) status code indicates that none of the
    * ranges in the request's Range header field (Section 3.1) overlap the
    * current extent of the selected resource or that the set of ranges
    * requested has been rejected due to invalid ranges or an excessive request
    * of small or overlapping ranges.
    *
    * For byte ranges, failing to overlap the current extent means that the
    * first-byte-pos of all of the byte-range-spec values were greater than the
    * current length of the selected representation. When this status code is
    * generated in response to a byte-range request, the sender should generate
    * a Content-Range header field specifying the current length of the selected
    * representation (Section 4.2).
    *
    * Note: Because servers are free to ignore Range, many implementations will
    * simply respond with the entire selected representation in a 200 (OK)
    * response. That is partly because most clients are prepared to receive a
    * 200 (OK) to complete the task (albeit less efficiently) and partly because
    * clients might not stop making an invalid partial request until they have
    * received a complete representation. Thus, clients cannot depend on
    * receiving a 416 (Range Not Satisfiable) response even when it is most
    * appropriate.
    *
    * @codingStandardsIgnoreStart
    *
    * @link https://svn.tools.ietf.org/svn/wg/httpbis/specs/rfc7233.html#status.416
    * @codingStandardsIgnoreEnd
    *
    * @var int
    */
    const RANGE_NOT_SATISFIABLE = 416;
}
