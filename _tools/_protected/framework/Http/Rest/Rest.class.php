<?php
/**
 * @title            Rest Class
 * @desc             Rest (REpresentational State Transfer) Class.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Http / Rest
 * @version          1.3
 */

namespace PH7\Framework\Http\Rest;

defined('PH7') or exit('Restricted access');

use PH7\Framework\File\Stream;
use PH7\Framework\Http\Http;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Str\Str;
use Teapot\StatusCode;

class Rest extends Http
{
    const CONTENT_TYPE = 'application/json';

    /** @var int */
    private $iCode;

    /** @var string */
    private $sData;

    /** @var array */
    private $aRequest;

    /**
     * Calls Rest::_inputs() method and sets default values.
     */
    public function __construct()
    {
        $this->inputs();
    }

    /**
     * @param string $sData The data from a request
     * @param int $iStatus Status Code. Default 200
     *
     * @return void
     */
    public function response($sData, $iStatus = StatusCode::OK)
    {
        $this->sData = $sData;

        /**
         * @internal Http::getStatusCode() returns FALSE when it doesn't find any valid HTTP codes.
         */
        // If it finds nothing, give 500 HTTP code
        $this->iCode = false !== static::getStatusCode($iStatus) ? $iStatus : StatusCode::INTERNAL_SERVER_ERROR;
        $this->output();
    }

    /**
     * @return array|string
     */
    public function getRequest()
    {
        return $this->aRequest;
    }

    /**
     * @return string The request body content (usually, should be a JSON string).
     */
    public function getBody()
    {
        return Stream::getInput();
    }

    /**
     * @return void
     */
    private function inputs()
    {
        switch ($this->getRequestMethod()) {
            case HttpRequest::METHOD_POST:
                $this->aRequest = $this->cleanInputs($_POST);
                break;

            case HttpRequest::METHOD_GET:
            case HttpRequest::METHOD_DELETE:
                $this->aRequest = $this->cleanInputs($_GET);
                break;

            case HttpRequest::METHOD_PUT:
                parse_str(Stream::getInput(), $this->aRequest);
                $this->aRequest = $this->cleanInputs($this->aRequest);
                break;

            default:
                $this->response('', StatusCode::NOT_ACCEPTABLE);
                break;
        }
    }

    /**
     * @param array|string
     *
     * @return array|string
     */
    private function cleanInputs($mData)
    {
        if (is_array($mData)) {
            $aCleanInput = [];

            foreach ($mData as $sKey => $sValue) {
                $aCleanInput[$sKey] = $this->cleanInputs($sValue);
            }

            return $aCleanInput;
        }

        $mData = (new Str)->escape($mData);
        return trim($mData);
    }

    /**
     * Headers Output.
     *
     * @return void
     */
    private function output()
    {
        static::setHeadersByCode($this->iCode);
        static::setContentType(self::CONTENT_TYPE); //Output format
        echo $this->sData;
        exit;
    }
}
