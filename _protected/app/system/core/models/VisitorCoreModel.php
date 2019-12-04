<?php
/**
 * @title          Visitor Model
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class VisitorCoreModel
{
    /** @var int */
    private $iProfileId;

    /** @var int */
    private $iVisitorId;

    /** @var string */
    private $sDateVisit;

    /**
     * Assignment of attributes.
     *
     * @param int $iProfileId Profile ID.
     * @param int|null $iVisitorId ID User ID (visitor). Default NULL (this attribute is null only for the get method).
     * @param string|null $sDateVisit The date of last visit. Default NULL (this attribute is null only for the get method).
     */
    public function __construct($iProfileId, $iVisitorId = null, $sDateVisit = null)
    {
        $this->iProfileId = (int)$iProfileId;
        $this->iVisitorId = (int)$iVisitorId;
        $this->sDateVisit = $sDateVisit;
    }

    /**
     * Checks if the profile has already been visited by this user.
     *
     * @return bool Returns TRUE if the profile has already been seen, otherwise FALSE.
     */
    public function already()
    {
        $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix(DbTableName::MEMBER_WHO_VIEW) .
            'WHERE profileId = :profileId AND visitorId = :visitorId LIMIT 1');

        $rStmt->bindValue(':profileId', $this->iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':visitorId', $this->iVisitorId, \PDO::PARAM_INT);
        $rStmt->execute();

        return $rStmt->fetchColumn() > 0;
    }

    /**
     * Gets Viewed Profile.
     *
     * @param int|string $mLooking Integer for visitor ID or string for a keyword
     * @param bool $bCount Put 'true' for count visitors or 'false' for the result of visitors.
     * @param string $sOrderBy
     * @param int $iSort
     * @param int $iOffset
     * @param int $iLimit
     *
     * @return int|array|\stdClass An object for the visitors list or an integer for the total number visitors returned
     */
    public function get($mLooking, $bCount, $sOrderBy, $iSort, $iOffset, $iLimit)
    {
        $bCount = (bool)$bCount;
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $mLooking = trim($mLooking);
        $bDigitSearch = ctype_digit($mLooking);

        $sSqlLimit = !$bCount ? 'LIMIT :offset, :limit' : '';
        $sSqlSelect = !$bCount ? '*' : 'COUNT(who.profileId)';

        $sSqlWhere = '(m.username LIKE :looking OR m.firstName LIKE :looking OR m.lastName LIKE :looking OR m.email LIKE :looking)';
        if ($bDigitSearch) {
            $sSqlWhere = '(who.visitorId = :looking)';
        }

        $sSqlOrder = SearchCoreModel::order($sOrderBy, $iSort);

        $rStmt = Db::getInstance()->prepare('SELECT ' . $sSqlSelect . ' FROM' . Db::prefix(DbTableName::MEMBER_WHO_VIEW) . 'AS who LEFT JOIN ' . Db::prefix(DbTableName::MEMBER) .
            'AS m ON who.visitorId = m.profileId WHERE (m.ban = 0) AND (who.profileId = :profileId) AND ' . $sSqlWhere . $sSqlOrder . $sSqlLimit);

        $rStmt->bindValue(':profileId', $this->iProfileId, \PDO::PARAM_INT);
        if ($bDigitSearch) {
            $rStmt->bindValue(':looking', $mLooking, \PDO::PARAM_INT);
        } else {
            $rStmt->bindValue(':looking', '%' . $mLooking . '%', \PDO::PARAM_STR);
        }

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

    /**
     * Updates the Date of Viewed Profile.
     *
     * @return void
     */
    public function update()
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix(DbTableName::MEMBER_WHO_VIEW) .
            'SET lastVisit = :dateLastVisit WHERE profileId = :profileId AND visitorId = :visitorId LIMIT 1');
        $rStmt->bindValue(':profileId', $this->iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':visitorId', $this->iVisitorId, \PDO::PARAM_INT);
        $rStmt->bindValue(':dateLastVisit', $this->sDateVisit, \PDO::PARAM_STR);
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
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix(DbTableName::MEMBER_WHO_VIEW) .
            '(profileId, visitorId, lastVisit) VALUES(:profileId, :visitorId, :dateVisit)');
        $rStmt->bindValue(':profileId', $this->iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':visitorId', $this->iVisitorId, \PDO::PARAM_INT);
        $rStmt->bindValue(':dateVisit', $this->sDateVisit, \PDO::PARAM_STR);
        $rStmt->execute();
        Db::free($rStmt);
    }
}
