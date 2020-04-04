<?php
/**
 * Class representing a Value Object of the HTTP Status-Line, as
 * specified in RFC 2616 and RFC 7231.
 *
 * PHP version 5.3
 *
 * @category StatusLine
 *
 * @package Teapot\StatusLine
 *
 * @author    Barney Hanlon <barney@shrikeh.net>
 * @copyright 2013-2016 B Hanlon. All rights reserved.
 * @license   MIT http://opensource.org/licenses/MIT
 * @link      https://shrikeh.github.com/teapot
 */
namespace Teapot\StatusLine;

use Teapot\StatusCode;
use Teapot\StatusLine;
use Teapot\StatusCodeException\InvalidStatusCodeException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class representing a Value Object of the HTTP Status-Line, as
 * specified in RFC 2616 and RFC 7231.
 *
 * PHP version 5.3
 *
 * @category StatusLine
 *
 * @package Teapot
 *
 * @author    Barney Hanlon <barney@shrikeh.net>
 * @copyright 2013-2016 B Hanlon. All rights reserved.
 * @license   MIT http://opensource.org/licenses/MIT
 * @link      https://shrikeh.github.com/teapot
 */
final class ResponseStatusLine implements StatusLine
{
    /**
     * The actual response code.
     *
     * @var int
     */
    private $code;

    /**
     * The reason phrase.
     *
     * @var string
     */
    private $reason;

    /**
     * ResponseStatusLine constructor.
     * @param int    $code   The HTTP response code
     * @param string $reason The reason phrase
     */
    public function __construct($code, $reason)
    {
        $this->setCode($code);
        $this->reason = $reason;
    }


    /**
     * Return the response code.
     *
     * @return int
     */
    public function statusCode()
    {
        return (int) $this->code;
    }

    /**
     * Return the reason phrase
     *
     * @return string
     */
    public function reasonPhrase()
    {
        return (string) $this->reason;
    }

    /**
     * Add the status code and reason phrase to a Response.
     *
     * @param ResponseInterface $response The response
     * @return ResponseInterface
     */
    public function response(ResponseInterface $response)
    {
        return $response->withStatus(
            $this->statusCode(),
            $this->reasonPhrase()
        );
    }

    /**
     * Return the status class of the response code.
     *
     * @return int
     */
    public function statusClass()
    {
        return (int) \floor($this->code / 100);
    }

    /**
     * Helper to establish if the class of the status code
     * is informational (1xx).
     *
     * @return bool
     */
    public function isInformational()
    {
        return $this->isStatusClass(StatusCode::INFORMATIONAL);
    }

    /**
     * Helper to establish if the class of the status code
     * is successful (2xx).
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->isStatusClass(StatusCode::SUCCESSFUL);
    }

    /**
     * Helper to establish if the class of the status code
     * is redirection (3xx).
     *
     * @return bool
     */
    public function isRedirection()
    {
        return $this->isStatusClass(StatusCode::REDIRECTION);
    }

    /**
     * Helper to establish if the class of the status code
     * is client error (4xx).
     *
     * @return bool
     */
    public function isClientError()
    {
        return $this->isStatusClass(StatusCode::CLIENT_ERROR);
    }

    /**
     * Helper to establish if the class of the status code
     * is server error (5xx).
     *
     * @return bool
     */
    public function isServerError()
    {
        return $this->isStatusClass(StatusCode::SERVER_ERROR);
    }

    /**
     * Set the code. Used in constructor to ensure the code meets the
     * requirements for a status code.
     *
     * @param int $code The status code
     * @throws InvalidStatusCodeException If the status code is invalid
     */
    private function setCode($code)
    {
        if (!\is_numeric($code)) {
            throw InvalidStatusCodeException::notNumeric($code);
        }
        $code = (int) $code;

        if ($code < 100) {
            throw InvalidStatusCodeException::notGreaterOrEqualTo100($code);
        }
        $this->code = $code;
    }

    /**
     * Test whether the response class matches the class passed to it.
     *
     * @param int $class The class of the response code
     * @return bool
     */
    private function isStatusClass($class)
    {
        return $this->statusClass() === $class;
    }
}
