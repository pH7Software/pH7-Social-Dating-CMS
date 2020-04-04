<?php
/**
 * Created by Florian Pradines
 */

namespace Skeerel\Util;


use Skeerel\Exception\DecodingException;

class Request
{
    /**
     * @param $url
     * @param $parameters
     * @return mixed
     * @throws DecodingException
     */
    public static function getJson($url, $parameters) {
        $url .= '?' . http_build_query($parameters);

        $requestOptions = array('http' =>
            array(
                'method' => 'GET',
                'ignore_errors' => true
            )
        );

        $json = json_decode(@file_get_contents($url, false, stream_context_create($requestOptions)), true);
        if ($json === null) {
            throw new DecodingException("result from API request does not seem to be a valid json");
        }

        return $json;
    }
}