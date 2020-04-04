<?php
/**
 * @title            Nudity Filter class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2016-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Security / Moderation
 */

namespace PH7\Framework\Security\Moderation;

defined('PH7') or exit('Restricted access');

use Image_FleshSkinQuantifier;
use PH7\Framework\File\Import;
use PH7\Framework\Pattern\Statik;

class Filter
{
    /**
     * The trait sets private constructor & cloning since it's a static class
     */
    use Statik;

    /**
     * @param string $sPath File path (e.g. $_FILES['file']['tmp_name'] ).
     *
     * @return bool TRUE if it is a nude photo, FALSE otherwise.
     */
    public static function isNudity($sPath)
    {
        self::importLibrary();

        $oNudityFilter = new Image_FleshSkinQuantifier($sPath);

        return $oNudityFilter->isPorn();
    }

    protected static function importLibrary()
    {
        Import::lib('FreebieStock.NudityDetector.Autoloader');
    }
}
