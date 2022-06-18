<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Comment / Inc / Class
 */

namespace PH7;

use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PH7\Framework\Mvc\Model\Engine\Util\Various;

class Comment extends CommentCore
{
    /**
     * @param string $sTable
     *
     * @return string|void Returns the table if it is correct.
     *
     * @throws PH7InvalidArgumentException
     */
    public static function getTable($sTable)
    {
        switch ($sTable) {
            case 'profile':
                $sNewTable = DbTableName::MEMBER;
                break;

            case 'picture':
                $sNewTable = DbTableName::PICTURE;
                break;

            case 'video':
                $sNewTable = DbTableName::VIDEO;
                break;

            case 'blog':
                $sNewTable = DbTableName::BLOG;
                break;

            case 'note':
                $sNewTable = DbTableName::NOTE;
                break;

            default:
                Various::launchErr($sTable);
        }

        return $sNewTable;
    }
}
