<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Inc / Class
 */

namespace PH7;

use PH7\Framework\Core\Kernel;
use PH7\Framework\Url\Url;

final class TweetSharing
{
    const TWITTER_TWEET_URL = 'https://twitter.com/intent/tweet?text=';
    const TWITTER_TWEET_MSG = "I built my Social #DatingWebApp with #pH7CMS 😍\n#DatingSoftware -> %0% => %1% 🚀";

    /**
     * @return string
     */
    public static function getMessage()
    {
        $sMsg = t(self::TWITTER_TWEET_MSG, Kernel::SOFTWARE_TWITTER, Kernel::SOFTWARE_GIT_REPO_URL);

        return self::TWITTER_TWEET_URL . Url::encode($sMsg);
    }
}
