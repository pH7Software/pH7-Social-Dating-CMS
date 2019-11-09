<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */

namespace PH7;

use PH7\Framework\Cache\Cache;
use PH7\Framework\Mvc\Model\Engine\Util\Various;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Pattern\Statik;
use PH7\Framework\Session\Session;

class CommentCore
{
    /** @var array */
    private static $aLowercaseTableNames = [
        'profile',
        'picture',
        'video',
        'blog',
        'note',
        'game'
    ];

    /**
     * Import the trait to set the class static.
     *
     * The trait sets constructor & cloning private to prevent instantiation.
     */
    use Statik;

    /**
     * Check table names.
     *
     * @param string $sTable
     *
     * @return string|void Returns the table name if it is correct.
     *
     * @see Various::launchErr()
     *
     * @throws \PH7\Framework\Error\CException\PH7InvalidArgumentException If the table is not valid.
     */
    public static function checkTable($sTable)
    {
        $sTable = strtolower($sTable); // Case insensitivity

        if (static::doesTableNameExist($sTable)) {
            return $sTable;
        }

        Various::launchErr($sTable);
    }

    /**
     * @param HttpRequest $oHttpRequest
     * @param Session $oSession
     *
     * @return bool
     *
     * @internal Since the ID digits might be string or integer, it won't work if we use the identity operator (===)
     */
    public static function isRemovalEligible(HttpRequest $oHttpRequest, Session $oSession)
    {
        return ($oSession->get('member_id') == $oHttpRequest->post('recipient_id') ||
                $oSession->get('member_id') == $oHttpRequest->post('sender_id')) || AdminCore::auth();
    }

    /**
     * @return void
     */
    public static function clearCache()
    {
        (new Cache)->start(
            CommentCoreModel::CACHE_GROUP,
            null,
            null
        )->clear();
    }

    /**
     * @param string $sTable
     *
     * @return bool
     */
    private static function doesTableNameExist($sTable)
    {
        return in_array($sTable, self::$aLowercaseTableNames, true);
    }
}
