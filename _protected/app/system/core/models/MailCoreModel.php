<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Model;

// Abstract Class
class MailCoreModel extends Model
{
    /**
     * Get the number of unread messages.
     *
     * @param int $iProfileId
     *
     * @return int
     */
    public static function countUnreadMsg($iProfileId)
    {
        $rStmt = Db::getInstance()->prepare('SELECT COUNT(status) AS unread FROM' . Db::prefix(DbTableName::MESSAGE) .
            'WHERE recipient = :recipient AND status = \'1\' AND NOT FIND_IN_SET(\'recipient\', toDelete)');

        $rStmt->bindValue(':recipient', $iProfileId, \PDO::PARAM_INT);
        $rStmt->execute();
        $iUnread = (int)$rStmt->fetchColumn();
        Db::free($rStmt);

        return $iUnread;
    }
}
