<?php
/**
 * @title            2 Check Out Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Payment / Gateway / Api
 * @version          1.0
 */

namespace PH7\Framework\Payment\Gateway\Api;

defined('PH7') or exit('Restricted access');

class TwoCheckOut extends Provider implements Api
{
    /** @var string */
    private $sUrl = 'https://www.2checkout.com/checkout/';

    /** @var string */
    private $sMsg;

    /** @var bool */
    private $bValid = false;


    /**
     * @param bool $bSandbox
     */
    public function __construct($bSandbox = false)
    {
        if ($bSandbox) {
            $this->param('demo', '1');
        }

        $this->param('mode', '2CO');
    }

    /**
     * Get Checkout URL.
     *
     * @param bool $bSinglePage TRUE = Single page, FALSE = Standard multi page.
     *
     * @return string
     */
    public function getUrl($bSinglePage = false)
    {
        $sPurchasePage = (true === (bool)$bSinglePage) ? 'spurchase' : 'purchase';

        return $this->sUrl . $sPurchasePage;
    }

    /**
     * Get message status.
     *
     * @return string
     */
    public function getMsg()
    {
        return $this->sMsg;
    }

    /**
     * Check if the transaction is valid.
     *
     * @param string $sVendorId
     * @param string $sSecretWord
     *
     * @return bool
     */
    public function valid($sVendorId = '', $sSecretWord = '')
    {
        // Instant Notification Service Messages
        $aInsMsg = [];

        foreach ($_POST as $sKey => $sVal) {
            $aInsMsg[$sKey] = $sVal;
        }

        if (
            !empty($_POST['message_type']) &&
            $_POST['message_type'] == 'FRAUD_STATUS_CHANGED' && !empty($aInsMsg['md5_hash'])
        ) {
            $sHash = strtoupper(md5($aInsMsg['sale_id'] . $sVendorId . $aInsMsg['invoice_id'] . $sSecretWord));

            if ($sHash == $aInsMsg['md5_hash']) {
                $this->bValid = true;
                $this->sMsg = t('Refund transaction valid.');
            } else {
                $this->bValid = false;
                $this->sMsg = t('Invalid refund transaction.');
            }
        } elseif (
            !empty($_REQUEST['key']) && !empty($aInsMsg['order_number']) &&
            !empty($aInsMsg['total'])
        ) {
            $sHash = strtoupper(md5($sSecretWord . $sVendorId . $aInsMsg['order_number'] . $aInsMsg['total']));

            if ($sHash !== $_REQUEST['key']) {
                $this->bValid = true;
                $this->sMsg = t('Purchase transaction valid.');
            } else {
                $this->bValid = false;
                $this->sMsg = t('Invalid purchase transaction.');
            }
        } else {
            $this->bValid = false;
            $this->sMsg = t('Invalid connection to 2CheckOut.');
        }

        unset($aInsMsg);

        return $this->bValid;
    }
}
