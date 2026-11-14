<?php
/**
 * @title            Various Class.
 * @desc             Useful methods for the management of the Models.
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2023, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Mvc / Model / Engine / Util
 */

declare(strict_types=1);

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
    public static function execQueryFile(string $sSqlFile): bool|array
    {
        if (!is_file($sSqlFile)) {
            return false;
        }

        $oDb = Db::getInstance();
        $sSqlContent = file_get_contents($sSqlFile);
        if ($sSqlContent === false) {
            return false;
        }

        $sSqlContent = static::renameTablePrefix($sSqlContent);
        $rStmt = $oDb->exec($sSqlContent);
        unset($sSqlContent);

        return $rStmt === false ? $oDb->errorInfo() : true;
    }

    /**
     * Convert mod to table.
     *
     * @param string $sMod
     *
     * @return string The table name if the specified module was valid.
     *
     * @throws PH7InvalidArgumentException If the table is not valid.
     */
    public static function convertModToTable(string $sMod): string
    {
        return match ($sMod) {
            'user' => DbTableName::MEMBER,
            'affiliate' => DbTableName::AFFILIATE,
            'newsletter' => DbTableName::SUBSCRIBER,
            PH7_ADMIN_MOD => DbTableName::ADMIN,
            default => static::launchErr($sMod),
        };
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
    public static function convertTableToMod(string $sTable): string
    {
        return match ($sTable) {
            DbTableName::MEMBER => 'user',
            DbTableName::AFFILIATE => 'affiliate',
            DbTableName::SUBSCRIBER => 'newsletter',
            DbTableName::ADMIN => PH7_ADMIN_MOD,
            default => static::launchErr($sTable),
        };
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
    public static function convertTableToId(string $sTable): string
    {
        return match ($sTable) {
            DbTableName::MEMBER => 'profileId',
            DbTableName::PICTURE => 'pictureId',
            DbTableName::ALBUM_PICTURE, DbTableName::ALBUM_VIDEO => 'albumId',
            DbTableName::VIDEO => 'videoId',
            DbTableName::BLOG => 'blogId',
            DbTableName::NOTE => 'noteId',
            DbTableName::FORUM_TOPIC => 'topicId',
            default => AdsCore::convertTableToId($sTable),
        };
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
    public static function checkTable(string $sTable): string
    {
        return match ($sTable) {
            DbTableName::MEMBER,
            DbTableName::ALBUM_PICTURE,
            DbTableName::ALBUM_VIDEO,
            DbTableName::PICTURE,
            DbTableName::VIDEO,
            DbTableName::BLOG,
            DbTableName::NOTE => $sTable,
            default => AdsCore::checkTable($sTable),
        };
    }

    /**
     * Check the model table.
     *
     * @param string $sTable
     *
     * @return string Returns the table if it is correct.
     *
     * @throws PH7InvalidArgumentException If the table is not valid.
     * @see self::launchErr()
     *
     */
    public static function checkModelTable(string $sTable): string
    {
        return match ($sTable) {
            DbTableName::MEMBER,
            DbTableName::MEMBER_INFO,
            DbTableName::MEMBER_COUNTRY,
            DbTableName::MEMBER_ATTEMPT_LOGIN,
            DbTableName::MEMBER_LOG_LOGIN,
            DbTableName::MEMBER_LOG_SESS,
            DbTableName::AFFILIATE,
            DbTableName::AFFILIATE_INFO,
            DbTableName::AFFILIATE_COUNTRY,
            DbTableName::AFFILIATE_ATTEMPT_LOGIN,
            DbTableName::AFFILIATE_LOG_LOGIN,
            DbTableName::AFFILIATE_LOG_SESS,
            DbTableName::ADMIN,
            DbTableName::ADMIN_ATTEMPT_LOGIN,
            DbTableName::ADMIN_LOG_LOGIN,
            DbTableName::ADMIN_LOG_SESS,
            DbTableName::SUBSCRIBER => $sTable,
            default => static::launchErr($sTable),
        };
    }

    /**
     * Throw an exception with an informative message.
     *
     * @param string $sTable The table value.
     *
     * @throws PH7InvalidArgumentException Explanatory message.
     */
    public static function launchErr(string $sTable): void
    {
        throw new PH7InvalidArgumentException(sprintf('Invalid data table: "%s"!', $sTable));
    }

    public static function renameTablePrefix(string $sSqlContent): string
    {
        return str_replace(PH7_TABLE_PREFIX, Db::prefix(), $sSqlContent);
    }
}
