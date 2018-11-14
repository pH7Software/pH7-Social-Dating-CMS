<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */

namespace PH7;

use PH7\Framework\Date\CDateTime;
use PH7\Framework\Mvc\Model\Engine\Db;

class UserSpyCoreModel
{
    /**
     * @param int $iUserId Profile user ID.
     * @param string $sUrl Use Uri::get() to get the full accurate URL.
     * @param string $sAction What the user does right now. That's the "action".
     *
     * @return mixed
     */
    public static function addUserAction($iUserId, $sUrl, $sAction)
    {
        if (!UserCore::auth()) {
            return false;
        }

        $sCurrentDate = (new CDateTime)->get()->dateTime('Y-m-d H:i:s');

        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix(DbTableName::MEMBER_SPY) . '(profileId, url, userAction, lastActivity) VALUES(:profileId, :url, :userAction, :lastActivity)');
        $rStmt->bindValue(':profileId', $iUserId, \PDO::PARAM_INT);
        $rStmt->bindValue(':url', $sUrl, \PDO::PARAM_STR);
        $rStmt->bindValue(':userAction', $sAction, \PDO::PARAM_STR);
        $rStmt->bindValue(':lastActivity', $sCurrentDate, \PDO::PARAM_STR);

        return $rStmt->execute();
    }
}
