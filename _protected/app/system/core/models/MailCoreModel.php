<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

// Abstract Class
class MailCoreModel extends Framework\Mvc\Model\Engine\Model
{

    public static function countUnreadMsg($iProfileId)
    {
        $rStmt = Db::getInstance()->prepare('SELECT COUNT(status) AS unread FROM' . Db::prefix('Messages') .
            'WHERE recipient = :recipient AND status = \'1\' AND NOT FIND_IN_SET(\'recipient\', toDelete)');

        $rStmt->bindValue(':recipient', $iProfileId, \PDO::PARAM_INT);
        $rStmt->execute();
        $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
        Db::free($rStmt);
        return (int)$oRow->unread;
    }

}
