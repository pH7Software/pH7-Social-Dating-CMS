<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Comment / Inc / Class
 */
namespace PH7;

class Comment extends CommentCore
{

    /**
     * @param string $sTable
     * @return mixed (string or void) Returns the table if it is correct.
     * @throws \PH7\Framework\Mvc\Model\Engine\Util\Various::launchErr() If the table is not valid.
     */
    public static function getTable($sTable)
    {
        switch ($sTable)
        {
            case 'Profile':
                $sNewTable = 'Members';
            break;

            case 'Picture':
                $sNewTable = 'Pictures';
            break;

            case 'Video':
                $sNewTable = 'Videos';
            break;

            case 'Blog':
                $sNewTable = 'Blogs';
            break;

            case 'Note':
                $sNewTable = 'Notes';
            break;

            case 'Game':
                $sNewTable = 'Games';
            break;

            default:
                Framework\Mvc\Model\Engine\Util\Various::launchErr($sTable);
        }

        return $sNewTable;
    }

}
