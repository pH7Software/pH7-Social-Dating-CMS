<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Util\Various;
use PH7\Framework\Pattern\Statik;

class CommentCore
{
    /**
     * Import the trait to set the class static.
     *
     * The trait sets constructor & cloning private to prevent instantiation.
     */
    use Statik;

    /**
     * Check table.
     *
     * @param string $sTable
     *
     * @return string|void Returns the table if it is correct.
     *
     * @see Various::launchErr()
     *
     * @throws \PH7\Framework\Error\CException\PH7InvalidArgumentException If the table is not valid.
     */
    public static function checkTable($sTable)
    {
        $sTable = strtolower($sTable); // Case insensitivity

        switch ($sTable) {
            case 'profile':
            case 'picture':
            case 'video':
            case 'blog':
            case 'note':
            case 'game':
                return ucfirst($sTable);

            default:
                Various::launchErr($sTable);
        }
    }

    /**
     * Count Comment with a HTML text.
     *
     * @param integer $iId
     * @param string $sTable
     *
     * @return string
     */
    public static function count($iId, $sTable)
    {
        $iCommentNumber = (new CommentCoreModel)->total($iId, $sTable);

        return nt('%n% Comment', '%n% Comments', $iCommentNumber);
    }
}
