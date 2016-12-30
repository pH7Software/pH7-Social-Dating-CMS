<?php
/**
 * @title            Rest Class
 * @desc             Rest (REpresentational State Transfer) Class.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Http / Rest
 * @version          1.2
 */

namespace PH7\Framework\Http\Rest;
defined('PH7') or exit('Restricted access');

use PH7\Framework\File\Stream, PH7\Framework\Mvc\Request\Http as HttpRequest;

class Rest extends \PH7\Framework\Http\Http
{

    private
    $_sContentType,
    $_iCode,
    $_sData,
    $_aRequest;


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
     * @return void
     */
    public function response($sData, $iStatus = 200)
    {
        $this->_sData = $sData;

        /**
         * @internal \PH7\Framework\Http\Http::getStatusCodes() returns FLASE when it doesn't find a GTTP status code.
         */
        $this->_iCode = (false !== static::getStatusCodes($iStatus)) ? $iStatus : 500; // If it finds nothing, then we put the 500 HTTP Status Code.
        $this->_output();
    }

    /**
     * Get the request data.
     *
     * @return array
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
        switch ($this->getRequestMethod())
        {
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
     * Clean Inputs.
     *
     * @param mixed (array | string)
     * @return array
     */
    private function _cleanInputs($mData)
    {
        $aCleanInput = array();

        if (is_array($mData))
        {
            foreach($mData as $sKey => $sValue)
                $aCleanInput[$sKey] = $this->_cleanInputs($sValue); // Recursive method
        }
        else
        {
            $mData = (new \PH7\Framework\Str\Str)->escape($mData);
            $aCleanInput = trim($mData);
        }

        return $aCleanInput;
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
