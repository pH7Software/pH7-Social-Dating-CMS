<?php
/**
 * @title            Rest Class
 * @desc             Rest (REpresentational State Transfer) Class.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
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

class Rest extends Http
{
    /** @var string */
    private $_sContentType;

    /** @var integer */
    private $_iCode;

    /** @var string */
    private $_sData;

    /** @var array */
    private $_aRequest;

    /**
     * Calls Rest::_inputs() method and sets default values.
     */
    public function __construct()
    {
        $this->_sContentType = 'application/json'; // Output format
        $this->_inputs();
    }

    /**
     * @param string $sData The data from a request
     * @param integer $iStatus Status Code. Default 200
     *
     * @return void
     */
    public function response($sData, $iStatus = 200)
    {
        $this->_sData = $sData;

        /**
         * @internal Http::getStatusCodes() returns FLASE when it doesn't find a GTTP status code.
         */
        $this->_iCode = (false !== static::getStatusCodes($iStatus)) ? $iStatus : 500; // If it finds nothing, then we put the 500 HTTP Status Code.
        $this->_output();
    }

    /**
     * @return array|string
     */
    public function getRequest()
    {
        return $this->_aRequest;
    }

    /**
     * @return void
     */
    private function _inputs()
    {
        switch ($this->getRequestMethod()) {
            case HttpRequest::METHOD_POST:
                $this->_aRequest = $this->_cleanInputs($_POST);
                break;

            case HttpRequest::METHOD_GET:
            case HttpRequest::METHOD_DELETE:
                $this->_aRequest = $this->_cleanInputs($_GET);
                break;

            case HttpRequest::METHOD_PUT:
                parse_str(Stream::getInput(), $this->_aRequest);
                $this->_aRequest = $this->_cleanInputs($this->_aRequest);
                break;

            default:
                $this->response('', 406);
                break;
        }
    }

    /**
     * @param array|string
     *
     * @return array|string
     */
    private function _cleanInputs($mData)
    {
        if (is_array($mData)) {
            $aCleanInput = array();

            foreach ($mData as $sKey => $sValue) {
                $aCleanInput[$sKey] = $this->_cleanInputs($sValue);
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
    private function _output()
    {
        static::setHeadersByCode($this->_iCode);
        static::setContentType($this->_sContentType);
        echo $this->_sData;
        exit; // Stop the Script
    }
}
