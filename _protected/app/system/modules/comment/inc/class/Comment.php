<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Comment / Inc / Class
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Util\Various;

class Comment extends CommentCore
{
    /**
     * @param string $sTable
     *
     * @return string|void Returns the table if it is correct.
     *
     * @throws \PH7\Framework\Error\CException\PH7InvalidArgumentException
     */
    public static function getTable($sTable)
    {
        switch ($sTable) {
            case 'Profile':
                $sNewTable = DbTableName::MEMBER;
                break;

            case 'Picture':
                $sNewTable = DbTableName::PICTURE;
                break;

            case 'Video':
                $sNewTable = DbTableName::VIDEO;
                break;

            case 'Blog':
                $sNewTable = DbTableName::BLOG;
                break;

            case 'Note':
                $sNewTable = DbTableName::NOTE;
                break;

            case 'Game':
                $sNewTable = 'Games';
                break;

            default:
                Various::launchErr($sTable);
        }

        return $sNewTable;
    }
}
