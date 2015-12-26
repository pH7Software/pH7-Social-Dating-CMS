<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2013-2016, Pierre-Henry Soria. All Rights Reserved.
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
     * @param object $oCategories Categories queries.
     * @param string $sMod Module name. Choose between 'blog' and 'note'.
     * @return void Output the categories list.
     */
    public static function categories($oCategories, $sMod)
    {
        WriteCore::checkMod($sMod);

        $sContents = '';

        echo '<p>', t('Categories:'), '<span class="small italic">';

        foreach ($oCategories as $oCategory)
            $sContents .= '<a href="' . Uri::get($sMod, 'main', 'category', $oCategory->name, ',title,asc') . '" data-load="ajax">' . $oCategory->name . '</a> &bull; ';

        unset($oCategories);

        echo substr($sContents, 0, -8);
        echo '</span></p>';
    }

}
