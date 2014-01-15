<?php
/**
 * @title          User Model
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2014, Pierre-Henry Soria. All Rights Reserved.
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
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    public function join(array $aData)
    {
        $rStmt = Db::getInstance()->prepare( $this->getQuery('join', $this->_sQueryPath) );
        $rStmt->bindValue(':email', $aData['email'], \PDO::PARAM_STR);
        $rStmt->bindValue(':username', $aData['username'], \PDO::PARAM_STR);
        $rStmt->bindParam(':password', $aData['password'], \PDO::PARAM_STR, Framework\Security\Security::PASSWORD_LENGTH);
        $rStmt->bindValue(':first_name', $aData['first_name'], \PDO::PARAM_STR);
        $rStmt->bindValue(':reference', $aData['reference'], \PDO::PARAM_STR);
        $rStmt->bindValue(':is_active', $aData['is_active'], \PDO::PARAM_INT);
        $rStmt->bindValue(':ip', $aData['ip'], \PDO::PARAM_STR);
        $rStmt->bindParam(':hash_validation', $aData['hash_validation'], \PDO::PARAM_STR, 40);
        $rStmt->bindValue(':current_date', $aData['current_date'], \PDO::PARAM_STR);
        $rStmt->bindValue(':group_id', $aData['group_id'], \PDO::PARAM_INT);
        $rStmt->execute();
        $this->setKeyId( Db::getInstance()->lastInsertId() ); // Set the user's ID
        Db::free($rStmt);
        $this->setDefaultPrivacySetting();
        return $this->setDefaultNotification();
    }

    /**
     * Join Step 2
     *
     * @param array $aData
     * @return boolean
     */
    public function join2(array $aData)
    {
        return $this->exec('join2.1', $this->_sQueryPath, $aData);
    }

    /**
     * Join Step 2 part 2
     *
     * @param array $aData
     * @return boolean
     */
    public function join2_2(array $aData)
    {
        return $this->exec('join2_2', $this->_sQueryPath, $aData);
    }


    /**
     * Join Step 3
     *
     * @param array $aData
     * @return boolean
     */
    public function join3(array $aData)
    {
        return $this->exec('join3', $this->_sQueryPath, $aData);
    }

}
