<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Video / Inc / Class
 */

namespace PH7;

use PH7\Framework\Cache\Cache;

class Video extends VideoCore
{
    public static function clearCache()
    {
        (new Cache)->start(VideoModel::CACHE_GROUP, null, null)->clear();
    }
}
