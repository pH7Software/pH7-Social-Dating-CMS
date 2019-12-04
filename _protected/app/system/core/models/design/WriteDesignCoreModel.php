<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model / Design
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;

abstract class WriteDesignCoreModel
{
    /**
     * Generate the categories links.
     *
     * @param array $aCategories Categories queries.
     * @param string $sMod Module name. Choose between 'blog' and 'note'.
     *
     * @return void Output the categories list.
     */
    public static function categories(array $aCategories, $sMod)
    {
        WriteCore::checkMod($sMod);

        $sContents = '';

        echo '<p class="small">', t('Categories:'), ' <span class="italic">';

        foreach ($aCategories as $oCategory) {
            $sContents .= '<a itemprop="keywords" href="' . Uri::get($sMod, 'main', 'category', $oCategory->name, ',title,asc') . '" data-load="ajax">' . $oCategory->name . '</a> &bull; ';
        }

        // Remove the last " &bull; " since not needed for the last category
        $sContents = rtrim($sContents, ' &bull; ');

        echo $sContents;
        echo '</span></p>';
    }
}
