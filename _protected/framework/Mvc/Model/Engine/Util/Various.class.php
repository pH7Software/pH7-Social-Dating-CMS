<?php
/**
 * @title            Various Class.
 * @desc             Useful methods for the management of the Models.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Model / Engine / Util
 */

namespace PH7\Framework\Mvc\Model\Engine\Util;

defined('PH7') or exit('Restricted access');

use PH7\AdsCore;
use PH7\DbTableName;
use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Pattern\Statik;

class Various
{
    /**
     * Import the trait to set the class static.
     *
     * The trait sets constructor & cloning private to prevent instantiation.
     */
    use Statik;


    /**
     * Executes SQL queries.
     *
     * @param string $sSqlFile File SQL.
     *
     * @return bool|array Returns TRUE if there are no errors, otherwise returns an ARRAY of error information.
     */
    public static function execQueryFile($sSqlFile)
    {
        if (!is_file($sSqlFile)) {
            return false;
        }

        $oDb = Db::getInstance();
        $sSqlContent = file_get_contents($sSqlFile);
        $sSqlContent = static::renameTablePrefix($sSqlContent);
        $rStmt = $oDb->exec($sSqlContent);
        unset($sSqlContent);

        return $rStmt === false ? $oDb->errorInfo() : true;
    }

    /**
     * Convert mod to table.
     *
     * @param string $Mod
     *
     * @return string The table name if the specified module was valid.
     *
     * @throws PH7InvalidArgumentException If the table is not valid.
     */
    public static function convertModToTable($Mod)
    {
        switch ($Mod) {
            case 'user':
                $sTable = DbTableName::MEMBER;
                break;

            case 'affiliate':
                $sTable = DbTableName::AFFILIATE;
                break;

            case 'newsletter':
                $sTable = DbTableName::SUBSCRIBER;
                break;

            case PH7_ADMIN_MOD:
                $sTable = DbTableName::ADMIN;
                break;

            default:
                static::launchErr($Mod);
        }

        return $sTable;
    }

    /**
     * Convert table to module name.
     *
     * @see self::launchErr()
     *
     * @param string $sTable
     *
     * @return string The correct module name.
     *
     * @throws PH7InvalidArgumentException If the table is not valid.
     */
    public static function convertTableToMod($sTable)
    {
        switch ($sTable) {
            case DbTableName::MEMBER:
                $sMod = 'user';
                break;

            case DbTableName::AFFILIATE:
                $sMod = 'affiliate';
                break;

            case DbTableName::SUBSCRIBER:
                $sMod = 'newsletter';
                break;

            case DbTableName::ADMIN:
                $sMod = PH7_ADMIN_MOD;
                break;

            default:
                static::launchErr($sTable);
        }

        return $sMod;
    }

    /**
     * Convert table to ID.
     *
     * @see self::launchErr()
     *
     * @param string $sTable
     *
     * @return string Returns the DB ID column name.
     *
     * @throws PH7InvalidArgumentException If the table is not valid.
     */
    public static function convertTableToId($sTable)
    {
        switch ($sTable) {
            case DbTableName::MEMBER:
                $sId = 'profileId';
                break;

            case DbTableName::PICTURE:
                $sId = 'pictureId';
                break;

            case DbTableName::ALBUM_PICTURE:
                $sId = 'albumId';
                break;

            case DbTableName::VIDEO:
                $sId = 'videoId';
                break;

            case DbTableName::ALBUM_VIDEO:
                $sId = 'albumId';
                break;

            case DbTableName::BLOG:
                $sId = 'blogId';
                break;

            case DbTableName::NOTE:
                $sId = 'noteId';
                break;

            case DbTableName::GAME:
                $sId = 'gameId';
                break;

            case DbTableName::FORUM_TOPIC:
                $sId = 'topicId';
                break;

            /** Check Ads Tables **/
            case AdsCore::checkTable($sTable):
                $sId = AdsCore::convertTableToId($sTable);
                break;

            default:
                static::launchErr($sTable);
        }

        return $sId;
    }

    /**
     * Check table.
     *
     * @see self::launchErr()
     *
     * @param string $sTable
     *
     * @return string Returns the table if it is correct.
     *
     * @throws PH7InvalidArgumentException If the table is not valid.
     */
    public static function checkTable($sTable)
    {
        switch ($sTable) {
            case DbTableName::MEMBER:
            case DbTableName::ALBUM_PICTURE:
            case DbTableName::ALBUM_VIDEO:
            case DbTableName::PICTURE:
            case DbTableName::VIDEO:
            case DbTableName::GAME:
            case DbTableName::BLOG:
            case DbTableName::NOTE:
                return $sTable;

            /** Check Ads Tables **/
            case AdsCore::checkTable($sTable):
                return $sTable;

            default:
                static::launchErr($sTable);
        }
    }

    /**
     * Check the model table.
     *
     * @see self::launchErr()
     *
     * @param string $sTable
     *
     * @return string Returns the table if it is correct.
     *
     * @throws PH7InvalidArgumentException If the table is not valid.
     */
    public static function checkModelTable($sTable)
    {
        switch ($sTable) {
            case DbTableName::MEMBER:
            case DbTableName::AFFILIATE:
            case DbTableName::MEMBER_INFO:
            case DbTableName::AFFILIATE_INFO:
            case DbTableName::MEMBER_COUNTRY:
            case DbTableName::AFFILIATE_COUNTRY:
            case DbTableName::SUBSCRIBER:
            case DbTableName::ADMIN:
                return $sTable;

            default:
                static::launchErr($sTable);
        }
    }

    /**
     * Throw an exception with an informative message.
     *
     * @param string $sTable The table value.
     *
     * @throws PH7InvalidArgumentException Explanatory message.
     */
    public static function launchErr($sTable)
    {
        throw new PH7InvalidArgumentException(sprintf('Invalid data table: "%s"!', $sTable));
    }

    /**
     * @param string $sSqlContent
     *
     * @return string
     */
    public static function renameTablePrefix($sSqlContent)
    {
        return str_replace(PH7_TABLE_PREFIX, Db::prefix(), $sSqlContent);
    }
}
