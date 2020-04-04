<?php
namespace Clickatell;

class Rest
{
    /**
     * API base URL
     * @var string
     */
    const API_URL = 'https://platform.clickatell.com';

    /**
     * @var string
     */
    const HTTP_GET = 'GET';

    /**
     * @var string
     */
    const HTTP_POST = 'POST';

    /**
     * The CURL agent identifier
     * @var string
     */
    const AGENT = 'ClickatellV2/1.0';

    /**
     * Excepted HTTP statuses
     * @var array
     */
    const ACCEPTED_CODES = [200, 201, 202];

    /**
     * @var string
     */
    private $apiToken = '';

    /**
     * Create a new API connection
     *
     * @param string $apiToken The token found on your integration
     */
    public function __construct($apiToken)
    {
        $this->apiToken = $apiToken;
    }

    /**
     * Handle CURL response from Clickatell APIs
     *
     * @param string $result   The API response
     * @param int    $httpCode The HTTP status code
     *
     * @throws Exception
     * @return array
     */
    protected function handle($result, $httpCode)
    {
        // Check for non-OK statuses
        if (!in_array($httpCode, static::ACCEPTED_CODES)) {
            // Decode JSON if possible, if this can't be decoded...something fatal went wrong
            // and we will just return the entire body as an exception.
            if ($error = json_decode($result, true)) {
                $error = $error['error'];
            } else {
                $error = $result;
            }

            throw new \Clickatell\ClickatellException($error);
        } else {
            return json_decode($result, true);
        }
    }

    /**
     * Abstract CURL usage.
     *
     * @param string $uri     The endpoint
     * @param string $data    Array of parameters
     *
     * @return Decoder
     */
    protected function curl($uri, $data)
    {
        // Force data object to array
        $data = $data ? (array) $data : $data;

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: ' . $this->apiToken
        ];

        // This is the clickatell endpoint. It doesn't really change so
        // it's safe for us to "hardcode" it here.
        $endpoint = static::API_URL . "/" . $uri;

        $curlInfo = curl_version();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, static::AGENT . ' curl/' . $curlInfo['version'] . ' PHP/' . phpversion());

        // Specify the raw post data
        if ($data) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return $this->handle($result, $httpCode);
    }

    /**
     * @see https://www.clickatell.com/developers/api-documentation/rest-api-send-message/
     *
     * @param array $message The message parameters
     *
     * @return array
     */
    public function sendMessage(array $message)
    {
        $response = $this->curl('messages', $message);
        return $response['messages'];
    }

    /**
     * @see https://www.clickatell.com/developers/api-documentation/rest-api-status-callback/
     *
     * @param callable $callback The function to trigger with desired parameters
     * @param string   $file     The stream or file name, default to standard input
     *
     * @return void
     */
    public static function parseStatusCallback($callback, $file = STDIN)
    {
        $body = file_get_contents($file);

        $body = json_decode($body, true);
        $keys = [
            'apiKey',
            'messageId',
            'requestId',
            'clientMessageId',
            'to',
            'from',
            'status',
            'statusDescription',
            'timestamp'
        ];

        if (!array_diff($keys, array_keys($body))) {
            $callback($body);
        }

        return;
    }

    /**
     * @see https://www.clickatell.com/developers/api-documentation/rest-api-reply-callback/
     *
     * @param callable $callback The function to trigger with desired parameters
     * @param string   $file     The stream or file name, default to standard input
     *
     * @return void
     */
    public static function parseReplyCallback($callback, $file = STDIN)
    {
        $body = file_get_contents($file);

        $body = json_decode($body, true);
        $keys = [
            'integrationId',
            'messageId',
            'replyMessageId',
            'apiKey',
            'fromNumber',
            'toNumber',
            'timestamp',
            'text',
            'charset',
            'udh',
            'network',
            'keyword'
        ];

        if (!array_diff($keys, array_keys($body))) {
            $callback($body);
        }

        return;
    }
}