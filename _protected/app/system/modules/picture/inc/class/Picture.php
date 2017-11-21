<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Picture / Inc / Class
 */

namespace PH7;

use PH7\Framework\Cache\Cache;

class Picture extends PictureCore
{
    /**
     * Clean picture title, since it cannot have blank space before the beginning and ending,
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

    public static function clearCache()
    {
        (new Cache)->start(PictureModel::CACHE_GROUP, null, null)->clear();
    }
}
