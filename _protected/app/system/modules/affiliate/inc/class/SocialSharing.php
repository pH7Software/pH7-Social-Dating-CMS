<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2021, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Inc / Class
 */

namespace PH7;

use PH7\Framework\Url\Url;

final class SocialSharing
{
    const X_POST_URL = 'https://x.com/intent/post?text=';

    public static function getXLink(string $sMessage): string
    {
        return self::X_POST_URL . Url::encode($sMessage);
    }
}
