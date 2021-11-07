<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2021, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Inc / Class
 */

namespace PH7;

use PH7\Framework\Url\Url;

final class SocialSharing
{
    const TWITTER_TWEET_URL = 'https://twitter.com/intent/tweet?text=';

    /**
     * @param string $sTweetText
     *
     * @return string
     */
    public static function getTwitterLink($sTweetText)
    {
        return self::TWITTER_TWEET_URL . Url::encode($sTweetText);
    }
}
