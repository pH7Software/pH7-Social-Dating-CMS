<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / SMS Verification / Inc / Class
 */

namespace PH7;

use PH7\Framework\Config\Config;
use PH7\Framework\Mvc\Model\Engine\Record;
use PH7\Framework\Pattern\Statik;

class Verification
{
    /**
     * Sets constructor/clone private to prevent instantiation, since it's a static class.
     */
    use Statik;

    /**
     * @param int $iProfileId
     *
     * @return string
     */
    public static function getVerificationCode($iProfileId)
    {
        $sUserHashValidation = Record::getInstance()->getOne(
            DbTableName::MEMBER,
            'profileId',
            $iProfileId,
            'hashValidation'
        )->hashValidation;

        return substr(
            $sUserHashValidation,
            0,
            Config::getInstance()->values['module.setting']['verification_code.length']
        );
    }
}
