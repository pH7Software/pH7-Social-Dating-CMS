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

    public static function getData($bCount, $iOffset = null, $iLimit = null)
    {
        $bCount = (bool)$bCount;
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;

        $sSqlLimit = !$bCount ? ' LIMIT :offset, :limit' : '';
        $sSqlSelect = !$bCount ? '*' : 'COUNT(spy.profileId)';

        $sSql = 'SELECT ' . $sSqlSelect . ' FROM' . Db::prefix(DbTableName::MEMBER_SPY) . 'AS spy LEFT JOIN ' .
            Db::prefix(DbTableName::MEMBER) .
            'AS m ON spy.profileId = m.profileId ORDER BY spy.lastActivity DESC' . $sSqlLimit;

        $rStmt = Db::getInstance()->prepare($sSql);

        if (!$bCount) {
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        }

        $rStmt->execute();

        if (!$bCount) {
            $mData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
        } else {
            $mData = (int)$rStmt->fetchColumn();
        }

        Db::free($rStmt);

        return $mData;
    }
}
