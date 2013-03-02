<?php
/**
 * @title            Rest Class
 * @desc             Rest (REpresentational State Transfer) Class.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Http / Rest
 * @version          1.0
 */

namespace PH7\Framework\Http\Rest;
defined('PH7') or exit('Restricted access');

class Rest extends \PH7\Framework\Http\Http
{

    private
    $_sContentType,
    $_iCode,
    $_aData,
    $_aRequest;


    /**
     * Sets the default values.
     */
    public function __construct()
    {
        $this->_sContentType = 'application/json'; // Output format
        $this->_inputs();
    }

    /**
     * @param array $aData
     * @param integer $iStatus Status Code. Default 200
     * @return void
     */
    public function response($aData, $iStatus = 200)
    {
        $this->_aData = $aData;

        /**
         * @internal \PH7\Framework\Http\Http::getStatusCodes() returns FLASE when it does not find status code.
         */
        $sStatusCode = $this->getStatusCodes();

        $this->_iCode = (false !== $sStatusCode) ? $iStatus : 500; // If it finds nothing, then we put the 500 HTTP Status Code.
        $this->_output();
    }

    /**
     * @return void
     */
    private function _inputs()
    {
        switch($this->get_request_method())
        {
            case 'POST':
                $this->_aRequest = $this->_cleanInputs($_POST);
            break;

            case 'GET':
            case 'DELETE':
                $this->_aRequest = $this->_cleanInputs($_GET);
            break;

            case 'PUT':
                parse_str($this->getInput(), $this->_aRequest);
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

        if(is_array($mData))
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
        $this->setHeadersByCode($this->_iCode);
        $this->setContentType($this->_sContentType);
        echo $this->_aData;
        exit; // Stop Script
    }

}
