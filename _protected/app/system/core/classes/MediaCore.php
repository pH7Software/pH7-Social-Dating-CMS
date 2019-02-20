<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */

namespace PH7;

class MediaCore
{
    /**
     * Clean picture/video title, since it cannot have blank space before the beginning and ending,
     * otherwise the URL pattern won't work.
     *
     * @param string $sTitle
     *
     * @return string
     */
    public static function cleanTitle($sTitle)
    {
        return trim($sTitle);
    }
}
