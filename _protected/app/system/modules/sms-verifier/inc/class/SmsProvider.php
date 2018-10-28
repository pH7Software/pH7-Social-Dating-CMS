<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / SMS Verifier / Inc / Class
 */

namespace PH7;

class SmsProvider
{
    /** @var string */
    protected $sFromSenderName;

    /** @var string */
    protected $sTokenApi;

    /**
     * @param string $sFromSenderName
     * @param string $sTokenApi
     */
    public function __construct($sFromSenderName, $sTokenApi)
    {
        $this->sFromSenderName = $sFromSenderName;
        $this->sTokenApi = $sTokenApi;
    }
}
