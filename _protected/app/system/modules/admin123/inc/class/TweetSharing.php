<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Admin / Inc / Class
 */

namespace PH7;

use PH7\Framework\Core\Kernel;
use PH7\Framework\Url\Url;

final class TweetSharing
{
    const X_POST_URL = 'https://x.com/intent/post?text=';
    const X_POST_MSG = "I built my #Social #DatingWebApp with #pH7Builder 😍\n#DatingSoftware -> %0% => %1% 🚀";

    public static function getMessage(): string
    {
        $sMsg = t(self::X_POST_MSG, Kernel::SOFTWARE_TWITTER, Kernel::SOFTWARE_GIT_REPO_URL);

        return self::X_POST_URL . Url::encode($sMsg);
    }
}
