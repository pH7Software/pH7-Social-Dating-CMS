<?php
/**
 * @title            Exists Core Model Class
 * @desc             Checks if a field in a table exists.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / System / Core / Model
 * @version          1.6
 */

namespace PH7;

use PDO;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Util\Various;

class ExistsCoreModel
{
    /**
     * Checks if the same email address already exists.
     *
     * @param string $sEmail
     * @param string $sTable Default is "Members"
     *
     * @return bool
     */
    public function email($sEmail, $sTable = DbTableName::MEMBER)
    {
        $sEmail = filter_var($sEmail, FILTER_SANITIZE_EMAIL);
        return $this->is('email', $sEmail, $sTable);
    }

    /**
     * Checks if the same username already exists.
     *
     * @param string $sUsername
     * @param string $sTable Default is "Members"
     *
     * @return bool
     */
    public function username($sUsername, $sTable = DbTableName::MEMBER)
    {
        return $this->is('username', $sUsername, $sTable);
    }

    /**
     * Checks if the same ID already exists. Ignore the ghost ID (1)
     *
     * @param integer $iId
     * @param string $sTable Default is "Members"
     *
     * @return bool
     */
    public function id($iId, $sTable = DbTableName::MEMBER)
    {
        return $this->is('profileId', $iId, $sTable, PDO::PARAM_INT, 'AND profileId <> ' . PH7_GHOST_ID);
    }

    /**
     * SECURITY Checks if there is not another affiliate with the same bank account.
     *
     * @param string $sAccount
     * @param string $sTable Default is "Affiliate"
     *
     * @return bool
     */
    public function bankAccount($sAccount, $sTable = DbTableName::AFFILIATE)
    {
        return $this->is('bankAccount', $sAccount, $sTable);
    }

    /**
     * Generic method to check if the field exists and with the check \PH7\Framework\Mvc\Model\Engine\Util\Various::checkModelTable() method.
     *
     * @param string $sColumn
     * @param string $sValue
     * @param string $sTable
     * @param string $sType PDO PARAM TYPE (PDO::PARAM_*). Default is PDO::PARAM_STR
     * @param string $sParam Optional WHERE parameter SQL.
     *
     * @return bool Returns TRUE if it exists, FALSE otherwise.
     */
    protected function is($sColumn, $sValue, $sTable, $sType = null, $sParam = null)
    {
        Various::checkModelTable($sTable);
        $sType = empty($sType) ? PDO::PARAM_STR : $sType;

        $rExists = Db::getInstance()->prepare('SELECT COUNT(' . $sColumn . ') FROM' . Db::prefix($sTable) . 'WHERE ' . $sColumn . ' = :column ' . $sParam . ' LIMIT 1');
        $rExists->bindValue(':column', $sValue, $sType);
        $rExists->execute();

        return $rExists->fetchColumn() == 1;
    }
}
