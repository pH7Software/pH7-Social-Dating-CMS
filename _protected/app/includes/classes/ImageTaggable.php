<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / Include / Class
 */

namespace PH7;

use stdClass;

trait ImageTaggable
{
    /**
     * Add an image to the social meta tags (for FB, Twitter, Google, ...).
     *
     * @param stdClass $oData
     *
     * @return void
     */
    abstract protected function imageToSocialMetaTags(stdClass $oData);
}
