<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */
namespace PH7;

use PH7\Framework\Mvc\Router\UriRoute;

class CommentDesignCore
{

    /**
     * Private constructor to prevent instantiation of class since it's a static class.
     *
     * @access private
     */
    private function __construct() {}

    /**
     * Get the link to comments.
     *
     * @param integer $iId
     * @param string $sTable
     * @return void
     */
    public static function link($iId, $sTable)
    {
        $oCommentModel = new CommentCoreModel();
        $iCommentNumber = $oCommentModel->total($iId, $sTable);
        unset($oCommentModel);

        echo '<p><a href="' . UriRoute::get('comment','comment','add',"$sTable,$iId") . '">' . t('Add a comment') . '</a>';
        if($iCommentNumber > 0) {
            $sCommentTxt = nt('Read Comment', 'Read the Comments', $iCommentNumber);
            echo ' - ' . t('OR') . ' -  <a href="' . UriRoute::get('comment','comment','read',$sTable.','.$iId) . '">' . $sCommentTxt . ' (' . $iCommentNumber . ')</a> <a href="' . UriRoute::get('xml','rss','xmlrouter','comment-'.$sTable.','.$iId) . '"><img src="' . PH7_URL_STATIC . PH7_IMG . 'icon/small-feed.png" alt="RSS Feed" /></a>';
        }
           echo '</p>';
    }

}
