<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class / Design
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Pattern\Statik;

class LostPwdDesignCore
{
    /**
     * Import the trait to set the class static.
     * The trait sets constructor/clone private to prevent instantiation.
     */
    use Statik;

    /**
     * Get the "forgot password" link.
     *
     * @param string $sMod
     * @param bool $bPrint Print or Return the HTML code.
     *
     * @return void
     */
    public static function link($sMod, $bPrint = true)
    {
        $sHtml = '<a rel="nofollow" href="' . Uri::get('lost-password', 'main', 'forgot', $sMod) . '">' . t('Forgot your password?') . '</a>';

        if (!$bPrint) {
            return $sHtml;
        }

        echo $sHtml;
    }
}
