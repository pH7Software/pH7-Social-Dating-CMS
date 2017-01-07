<?php
/**
 * @title            Various Class.
 * @desc             Useful methods for the management of the Models.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Model / Engine / Util
 */

namespace PH7\Framework\Mvc\Model\Engine\Util;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Model\Engine\Db, PH7\Framework\Pattern\Statik;

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
     * @return mixed (boolean | array) Returns TRUE if there are no errors, otherwise returns an ARRAY of error information.
     */
    public static function execQueryFile($sSqlFile)
    {
        if (!is_file($sSqlFile)) return false;

        $sSqlContent = file_get_contents($sSqlFile);
        $sSqlContent = str_replace(PH7_TABLE_PREFIX,  Db::prefix(), $sSqlContent);
        $rStmt = Db::getInstance()->exec($sSqlContent);
        unset($sSqlContent);

        return ($rStmt === false) ? $rStmt->errorInfo() : true;
    }

    /**
     * Convert mod to table.
     *
     * @param string $Mod
     * @return mixed (string | void) Returns the table if it is correct.
     * @throws \PH7\Framework\Mvc\Model\Engine\Util\Various::launchErr() If the table is not valid.
     */
    public static function convertModToTable($Mod)
    {
        switch ($Mod)
        {
            case 'user':
                $sTable = 'Members';
            break;

            case 'affiliate':
                $sTable = 'Affiliates';
            break;

            case 'newsletter':
                $sTable = 'Subscribers';
            break;

            case PH7_ADMIN_MOD:
                $sTable = 'Admins';
            break;

            default:
               static::launchErr($Mod);
        }

        return $sTable;
    }

    /**
     * Convert table to module name.
     *
     * @see \PH7\Framework\Mvc\Model\Engine\Util\Various::launchErr()
     *
     * @param string $sTable
     * @return string The correct module name.
     * @throws \PH7\Framework\Mvc\Model\Engine\Util\Various::launchErr() If the table is not valid.
     */
     public static function convertTableToMod($sTable)
     {
         switch ($sTable)
         {
             case 'Members':
                 $sMod = 'user';
             break;

             case 'Affiliates':
                 $sMod = 'affiliate';
             break;

             case 'Subscribers':
                 $sMod = 'newsletter';
             break;

             case 'Admins':
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
     * @see \PH7\Framework\Mvc\Model\Engine\Util\Various::launchErr()
     *
     * @param string $sTable
     * @return mixed (string | void) Returns the table if it is correct.
     * @throws \PH7\Framework\Mvc\Model\Engine\Util\Various::launchErr() If the table is not valid.
     */
    public static function convertTableToId($sTable)
    {
        switch ($sTable)
        {
            case 'Members':
                $sId = 'profileId';
            break;

            case 'Pictures':
                $sId = 'pictureId';
            break;

            case 'AlbumsPictures':
                $sId = 'albumId';
            break;

            case 'Videos':
                $sId = 'videoId';
            break;

            case 'AlbumsVideos':
                $sId = 'albumId';
            break;

            case 'Blogs':
                $sId = 'blogId';
            break;

            case 'Notes':
                $sId = 'noteId';
            break;

            case 'Games':
                $sId = 'GameId';
            break;

            case 'ForumsTopics':
                $sId = 'topicId';
            break;

            /** Check Ads Tables **/
            case \PH7\AdsCore::checkTable($sTable):
                $sId = \PH7\AdsCore::convertTableToId($sTable);
            break;

            default:
                static::launchErr($sTable);
        }

        return $sId;
    }

    /**
     * Check table.
     *
     * @see \PH7\Framework\Mvc\Model\Engine\Util\Various::launchErr()
     *
     * @param string $sTable
     * @return mixed (string | void) Returns the table if it is correct.
     * @throws \PH7\Framework\Mvc\Model\Engine\Util\Various::launchErr() If the table is not valid.
     */
    public static function checkTable($sTable)
    {
        switch ($sTable)
        {
            case 'Members':
            case 'AlbumsPictures':
            case 'AlbumsVideos':
            case 'Pictures':
            case 'Videos':
            case 'Games':
            case 'Blogs':
            case 'Notes':
                return $sTable;
            break;

            /** Check Ads Tables **/
            case \PH7\AdsCore::checkTable($sTable):
                return $sTable;
            break;

            default:
                static::launchErr($sTable);
        }
    }

    /**
     * Check the model table.
     *
     * @see \PH7\Framework\Mvc\Model\Engine\Util\Various::launchErr()
     *
     * @param string $sTable
     * @return mixed (string | void) Returns the table if it is correct.
     * @throws \PH7\Framework\Mvc\Model\Engine\Util\Various::launchErr() If the table is not valid.
     */
    public static function checkModelTable($sTable)
    {
        switch ($sTable)
        {
            case 'Members':
            case 'Affiliates':
            case 'MembersInfo':
            case 'AffiliatesInfo':
            case 'Subscribers':
            case 'Admins':
                return $sTable;
            break;

            default:
                static::launchErr($sTable);
        }
    }

    /**
     * Set an Error Message with an Exception then exit() function.
     *
     * @param string $sTable The table value.
     * @return integer 1 (with exit function).
     * @throws \PH7\Framework\Error\CException\PH7InvalidArgumentException Explanatory message.
     */
    public static function launchErr($sTable)
    {
        throw new \PH7\Framework\Error\CException\PH7InvalidArgumentException('Bad data table: "' . $sTable . '"!');
        exit(1);
    }
}
