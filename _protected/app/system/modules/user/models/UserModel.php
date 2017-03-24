<?php
/**
 * @title          User Model
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7/ App / System / Module / User / Model
 * @version        1.0
 */
namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class UserModel extends UserCoreModel
{

    private $_sQueryPath;

    public function __construct()
    {
        parent::__construct();
        $this->_sQueryPath = __DIR__ . PH7_DS . PH7_QUERY;
    }

    /**
     * Join Step 1
     *
     * @param array $aData
     * @return integer Returns the user's ID
     */
    public function join(array $aData)
    {
        $rStmt = Db::getInstance()->prepare( $this->getQuery('join', $this->_sQueryPath) );
        $rStmt->bindValue(':email', $aData['email'], \PDO::PARAM_STR);
        $rStmt->bindValue(':username', $aData['username'], \PDO::PARAM_STR);
        $rStmt->bindValue(':password', $aData['password'], \PDO::PARAM_STR);
        $rStmt->bindValue(':first_name', $aData['first_name'], \PDO::PARAM_STR);
        $rStmt->bindValue(':reference', $aData['reference'], \PDO::PARAM_STR);
        $rStmt->bindValue(':is_active', $aData['is_active'], \PDO::PARAM_INT);
        $rStmt->bindValue(':ip', $aData['ip'], \PDO::PARAM_STR);
        $rStmt->bindParam(':hash_validation', $aData['hash_validation'], \PDO::PARAM_STR, 40);
        $rStmt->bindValue(':current_date', $aData['current_date'], \PDO::PARAM_STR);
        $rStmt->bindValue(':affiliated_id', $aData['affiliated_id'], \PDO::PARAM_INT);
        $rStmt->execute();
        $this->setKeyId(Db::getInstance()->lastInsertId()); // Set the user's ID
        Db::free($rStmt);

        $this->setInfoFields(array());
        $this->setDefaultPrivacySetting();
        $this->setDefaultNotification();

        // Last thing, update the membership with the correct details
        $this->updateMembership($aData['group_id'], $this->getKeyId(), $this->sCurrentDate);

        return $this->getKeyId();
    }

    /**
     * Execute SQL Join files.
     *
     * @param array $aData
     * @param string $sJoinStep Step of the "Join" file ('2_1', '2_2' or '3').
     * @return boolean
     */
    public function exe(array $aData, $sJoinStep)
    {
        return $this->exec('join' . $sJoinStep, $this->_sQueryPath, $aData);
    }

}
