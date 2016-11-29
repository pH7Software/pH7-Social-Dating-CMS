<?php
/**
 * @title          Search Core Model Class
 * @desc           Useful methods for the Search.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 * @version        1.1
 */
namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class SearchCoreModel
{

    const
    NAME = 'name',
    TITLE = 'title',
    VIEWS = 'views',
    RATING = 'votes',
    DOWNLOADS = 'downloads',
    LATEST = 'joinDate',
    LAST_ACTIVITY = 'lastActivity',
    LAST_EDIT = 'lastEdit',
    LAST_VISIT = 'lastVisit',
    PENDING_APPROVAL = 'active',
    EMAIL = 'email',
    USERNAME = 'username',
    FIRST_NAME = 'firstName',
    LAST_NAME = 'lastName',
    CREATED = 'createdDate',
    SEND_DATE = 'sendDate',
    ADDED_DATE = 'addedDate',
    UPDATED = 'updatedDate',
    IP = 'ip',
    ASC = 1,
    DESC = 2;

    /**
     * @constructor
     * Private constructor to prevent instantiation of class since it's a static class.
     *
     * @access private
     */
    private function __construct() {}

    /**
     * Order By method.
     *
     * @param string $sColumn Table Column
     * @param integer $iSort \PH7\SearchCoreModel::ASC OR \PH7\SearchCoreModel::DESC Default: \PH7\SearchCoreModel::ASC
     * @param string $sAsTable The Alias Table, this prevents the ambiguous clause. Default: NULL
     * @return string SQL order by query
     */
    public static function order($sColumn, $iSort = self::ASC, $sAsTable = null)
    {
        switch ($sColumn)
        {
            case static::NAME:
            case static::TITLE:
            case static::VIEWS:
            case static::RATING:
            case static::DOWNLOADS:
            case static::LATEST:
            case static::LAST_ACTIVITY:
            case static::LAST_EDIT:
            case static::LAST_VISIT:
            case static::PENDING_APPROVAL:
            case static::EMAIL:
            case static::USERNAME:
            case static::FIRST_NAME:
            case static::LAST_NAME:
            case static::CREATED:
            case static::SEND_DATE:
            case static::ADDED_DATE:
            case static::UPDATED:
                $sAsTable = (!empty($sAsTable)) ? $sAsTable . '.' : '';
                $sOrderBy = $sColumn;
            break;

            default:
                $sAsTable = ''; // No Alias because it is an SQL function
                $sOrderBy = Db::RAND; // Default value is RAND()
        }

        return ' ORDER BY ' . $sAsTable . $sOrderBy . static::sort($iSort);
    }

    /**
     * @access protected
     * @param integer $iSort
     * @return string
     */
    protected static function sort($iSort)
    {
        return ($iSort === static::DESC) ? ' DESC ' : ' ASC ';
    }

}
