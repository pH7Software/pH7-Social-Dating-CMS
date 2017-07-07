<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class / Design
 */

namespace PH7;

use PH7\Framework\Layout\Html\Design;
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
     * @param integer $iId Unique ID of the column of the table. EX: ID of 'profileId' column for the 'Members' table.
     * @param string $sTable See the list of data tables available in the class: PH7\Framework\Mvc\Model\Engine\Util\Various::checkTable().
     * @param string $sCssClass Default value is empty. You can add the name of a CSS class (attention, only its name) e.g. 'center'.
     * @return void
     */
    public static function voting($iId, $sTable, $sCssClass = '')
    {
        $oRatingModel = new RatingCoreModel;
        $iVotes = $oRatingModel->getVote($iId, $sTable);
        $fScore = $oRatingModel->getScore($iId, $sTable);
        unset($oRatingModel);

        // Note: The rating.css style file is included by default in the CMS
        (new Design)->staticFiles('js', PH7_STATIC . PH7_JS, 'jquery/rating.js');


        $fRate = ($iVotes > 0) ? number_format($fScore / $iVotes, 1) : 0;

        $sPHSClass = 'pHS' . $iId . $sTable;

        echo '<div class="', $sCssClass, ' ', $sPHSClass, '" id="', $fRate, '_', $iId, '_', $sTable, '"></div><p class="', $sPHSClass, '_txt">', t('Score: %0% - Votes: %1%', $fRate, $iVotes), '</p>
              <script>$(".', $sPHSClass, '").pHRating({length:5,decimalLength:1,rateMax:5});</script>';

        /**
         * Redirectionne the member to the registration page if not logged in.
         * For security, a check on to ajax server side is already present, but this JS code allows the visitor to login easier.
         */
        if (!UserCore::auth()) {
            $sUrl = Framework\Mvc\Router\Uri::get('user', 'signup', 'step1', '?msg=' . t('Please join for free to vote that'), false);
            echo '<script>$(".', $sPHSClass, '").click(function(){window.location=\'', $sUrl, '\'});</script>';
        }
    }
}
