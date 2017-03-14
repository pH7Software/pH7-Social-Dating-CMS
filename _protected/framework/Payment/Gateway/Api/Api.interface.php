<?php
/**
 * @title            API Interface
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Payment / Gateway / Api
 * @version          1.0
 */

namespace PH7\Framework\Payment\Gateway\Api;

defined('PH7') or exit('Restricted access');

interface Api
{

    /**
     * Get Checkout URL.
     *
     * @param string $sOptionalParam Default ''
     * @return string
     */
    public function getUrl($sOptionalParam = '');

    /**
     * Get message status.
     *
     * @return string
     */
    public function getMsg();

    /**
     * Check if the transaction is valid.
     *
     * @param string $sOptionalParam1 Default ''
     * @param string $sOptionalParam2 Default ''
     * @return boolean
     */
    public function valid($sOptionalParam1 = '', $sOptionalParam2 = '');

}
