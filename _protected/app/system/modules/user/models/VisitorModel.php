<?php
/**
 * @title          Visitor Model
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7/ App / System / Module / User / Model
 * @version        1.1
 */
namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class VisitorModel
{

    private $_iProfileId, $_iVisitorId, $_sDateVisit;

    /**
     * Assignment of attributes.
     *
     * @param integer $iProfileId Profile ID.
     * @param integer $iVisitor ID User ID (visitor). Default NULL (this attribute is null only for the get method).
     * @param string $sDateVisit The date of last visit. Default NULL (this attribute is null only for the get method).
     */
    public function __construct($iProfileId, $iVisitorId = null, $sDateVisit = null)
    {
        $this->_iProfileId = (int) $iProfileId;
        $this->_iVisitorId = (int) $iVisitorId;
        $this->_sDateVisit = $sDateVisit;
    }

    /**
     * Checks if the profile has already been visited by this user.
     *
     * @return boolean Returns TRUE if the profile has already been seen, otherwise FALSE.
     */
    public function already()
    {
        $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix('MembersWhoViews') .
            'WHERE profileId = :profileId AND visitorId = :visitorId LIMIT 1');

        $rStmt->bindValue(':profileId', $this->_iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':visitorId', $this->_iVisitorId, \PDO::PARAM_INT);
        $rStmt->execute();
        return ($rStmt->fetchColumn() > 0);
    }

    /**
     * Gets Viewed Profile.
     *
     * @param mixed (integer for visitor ID or string for a keyword) $mLooking
     * @param boolean $bCount Put 'true' for count visitors or 'false' for the result of visitors.
     * @param string $sOrderBy
     * @param string $sSort
     * @param integer $iOffset
     * @param integer $iLimit
     * @return mixed (object | integer) object for the visitors list returned or integer for the total number visitors returned.
     */
    public function get($mLooking, $bCount, $sOrderBy, $sSort, $iOffset, $iLimit)
    {
        $bCount = (bool) $bCount;
        $iOffset = (int) $iOffset;
        $iLimit = (int) $iLimit;

        $sSqlLimit = (!$bCount) ? 'LIMIT :offset, :limit' : '';
        $sSqlSelect = (!$bCount) ? '*' : 'COUNT(who.profileId) AS totalVisitors';
        $sSqlWhere = (ctype_digit($mLooking)) ? '(who.visitorId = :looking)' : '(m.username LIKE :looking OR m.firstName LIKE :looking OR m.lastName LIKE :looking OR m.email LIKE :looking)';
        $sSqlOrder = SearchCoreModel::order($sOrderBy, $sSort);

        $rStmt = Db::getInstance()->prepare('SELECT ' . $sSqlSelect . ' FROM' . Db::prefix('MembersWhoViews') . 'AS who LEFT JOIN ' . Db::prefix('Members') .
            'AS m ON who.visitorId = m.profileId WHERE (who.profileId = :profileId) AND ' . $sSqlWhere . $sSqlOrder . $sSqlLimit);
        $rStmt->bindValue(':profileId', $this->_iProfileId, \PDO::PARAM_INT);
        (ctype_digit($mLooking)) ? $rStmt->bindValue(':looking', $mLooking, \PDO::PARAM_INT) : $rStmt->bindValue(':looking', '%' . $mLooking . '%', \PDO::PARAM_STR);

        if (!$bCount)
        {
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        }

        $rStmt->execute();

        if (!$bCount)
        {
            $oRow = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            return $oRow;
        }
        else
        {
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            return (int) $oRow->totalVisitors;
        }
    }

    /**
     * Updates the Date of Viewed Profile.
     *
     * @return void
     */
    public function update()
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix('MembersWhoViews') .
            'SET lastVisit = :dateLastVisit WHERE profileId = :profileId AND visitorId = :visitorId LIMIT 1');
        $rStmt->bindValue(':profileId', $this->_iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':visitorId', $this->_iVisitorId, \PDO::PARAM_INT);
        $rStmt->bindValue(':dateLastVisit', $this->_sDateVisit, \PDO::PARAM_STR);
        $rStmt->execute();
        Db::free($rStmt);
    }

    /**
     * Sets Viewed Profile.
     *
     * @return void
     */
    public function set()
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('MembersWhoViews') .
            '(profileId, visitorId, lastVisit) VALUES(:profileId, :visitorId, :dateVisit)');
        $rStmt->bindValue(':profileId', $this->_iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':visitorId', $this->_iVisitorId, \PDO::PARAM_INT);
        $rStmt->bindValue(':dateVisit', $this->_sDateVisit, \PDO::PARAM_STR);
        $rStmt->execute();
        Db::free($rStmt);
    }

}
