<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / SMS Verifier / Inc / Class
 */

namespace PH7;

use PH7\Framework\Config\Config;
use PH7\Framework\Pattern\Statik;

class Verification
{
    /**
     * Sets constructor/clone private to prevent instantiation, since it's a static class.
     */
    use Statik;

    /**
     * @return string
     */
    public static function getVerificationCode()
    {
        $sUserHashValidation = (new UserCoreModel)->getHashValidation();

        return substr(
            $sUserHashValidation,
            0,
            Config::getInstance()->values['module.setting']['verification_code.length']
        );
    }
}
