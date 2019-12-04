<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class / Design
 */

namespace PH7;

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Pattern\Statik;

class RatingDesignCore
{
    /**
     * Import the trait to set the class static.
     * The trait sets constructor/clone private to prevent instantiation.
     */
    use Statik;

    /**
     * Generates design the voting system.
     *
     * @param int $iId Unique ID of the column of the table. EX: ID of 'profileId' column for the 'members' table.
     * @param string $sTable See the list of data tables available in the class: PH7\Framework\Mvc\Model\Engine\Util\Various::checkTable().
     * @param string $sCssClass Default value is empty. You can add the name of a CSS class (attention, only its name) e.g. 'center'.
     *
     * @return void
     */
    public static function voting($iId, $sTable, $sCssClass = '')
    {
        $aRating = self::getRatingData($iId, $sTable);

        // Note: The rating.css style file is included by default in the CMS
        (new Design)->staticFiles('js', PH7_STATIC . PH7_JS, 'jquery/rating.js');


        $fRate = ($aRating['votes'] > 0) ? number_format($aRating['score'] / $aRating['votes'], 1) : 0;

        $sPHSClass = 'pHS' . $iId . $sTable;

        echo '<div itemscope="itemscope" itemtype="http://schema.org/AggregateRating">';

        echo '<div class="', $sCssClass, ' ', $sPHSClass, '" id="', $fRate, '_', $iId, '_', $sTable, '"></div>';
        echo '<p itemprop="ratingValue" content="', $fRate, '" itemprop="ratingCount" content="', $aRating['votes'], '" class="', $sPHSClass, '_txt">', t('Score: %0% - Votes: %1%', $fRate, $aRating['votes']), '</p>';
        echo '<script>$(".', $sPHSClass, '").pHRating({length:5,decimalLength:1,rateMax:5});</script>';

        /**
         * Redirect the member to the registration page if not logged in.
         * For security reason, a check on the server-side is already present, because this JS code allows users to login easily by modifying it.
         */
        if (!UserCore::auth()) {
            $sUrl = Uri::get('user', 'signup', 'step1', '?msg=' . t('You need to be a member for voting.'), false);
            echo '<script>$(".', $sPHSClass, '").click(function(){window.location=\'', $sUrl, '\'});</script>';
        }

        echo '</div>';
    }

    /**
     * @param int $iId
     * @param string $sTable
     *
     * @return array
     */
    private static function getRatingData($iId, $sTable)
    {
        $oRatingModel = new RatingCoreModel;

        $aRatingData = [
            'votes' => $oRatingModel->getVote($iId, $sTable),
            'score' => $oRatingModel->getScore($iId, $sTable)
        ];
        unset($oRatingModel);

        return $aRatingData;
    }
}
