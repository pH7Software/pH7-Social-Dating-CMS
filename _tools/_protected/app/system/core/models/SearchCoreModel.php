<?php
/**
 * @title          Search Core Model Class
 * @desc           Useful methods for the Search.
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 * @version        1.3
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class SearchCoreModel
{
    const ASC = 1;
    const DESC = 2;

    const NAME = 'name';
    const TITLE = 'title';
    const VIEWS = 'views';
    const RATING = 'votes';
    const DOWNLOADS = 'downloads';
    const LATEST = 'joinDate';
    const LAST_ACTIVITY = 'lastActivity';
    const LAST_EDIT = 'lastEdit';
    const LAST_VISIT = 'lastVisit';
    const PENDING_APPROVAL = 'active';
    const EMAIL = 'email';
    const USERNAME = 'username';
    const FIRST_NAME = 'firstName';
    const LAST_NAME = 'lastName';
    const CREATED = 'createdDate';
    const SEND_DATE = 'sendDate';
    const ADDED_DATE = 'addedDate';
    const UPDATED = 'updatedDate';
    const FEATURED = 'featured';
    const IP = 'ip';

    /**
     * Private constructor to prevent instantiation of class since it's a static class.
     */
    private function __construct()
    {
    }

    /**
     * Order By method.
     *
     * @param string $sColumn Table Column
     * @param int $iSort SearchCoreModel::ASC OR SearchCoreModel::DESC
     * @param string|null $sAsTable The Alias Table, this prevents the ambiguous clause
     *
     * @return string SQL order by query
     */
    public static function order($sColumn, $iSort = self::ASC, $sAsTable = null)
    {
        $iSort = (int)$iSort; // Make sure it's an integer and not a digit string!

        switch ($sColumn) {
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
            case static::FEATURED:
                $sAsTable = !empty($sAsTable) ? $sAsTable . '.' : '';
                $sOrderBy = $sColumn;
                break;

            default:
                $sAsTable = ''; // No Alias because it is an SQL function
                $sOrderBy = Db::RAND; // Default value is RAND()
        }

        return ' ORDER BY ' . $sAsTable . $sOrderBy . static::sort($iSort);
    }

    /**
     * @param int $iSort
     *
     * @return string
     */
    private static function sort($iSort)
    {
        return $iSort === static::DESC ? ' DESC ' : ' ASC ';
    }
}
