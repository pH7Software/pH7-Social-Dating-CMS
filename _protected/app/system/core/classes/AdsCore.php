<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Core / Class
 */

declare(strict_types=1);

namespace PH7;

use PH7\Framework\Ads\Ads;
use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PH7\Framework\Mvc\Model\Engine\Util\Various;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Pattern\Statik;

class AdsCore extends Ads
{
    const ID_COLUMN_NAME = 'adsId';

    const AD_TABLE_NAME = DbTableName::AD;
    const AFFILIATE_AD_TABLE_NAME = DbTableName::AD_AFFILIATE;

    const TABLE_NAMES = [
        self::AD_TABLE_NAME,
        self::AFFILIATE_AD_TABLE_NAME
    ];

    /**
     * Import the trait to set the class static.
     * The trait sets constructor/clone private to prevent instantiation.
     */
    use Statik;

    /**
     * Gets Ads Table.
     *
     * @return string The Table.
     */
    public static function getTable(): string
    {
        $oHttpRequest = new Http;
        if ($oHttpRequest->getExists('ads_type') &&
            $oHttpRequest->get('ads_type') === 'affiliate'
        ) {
            $sTable = self::AFFILIATE_AD_TABLE_NAME;
        } else {
            $sTable = self::AD_TABLE_NAME;
        }
        unset($oHttpRequest);

        return $sTable;
    }

    /**
     * Checks Ads Table.
     *
     * @param string $sTable
     *
     * @return string|void Returns the table name if it is correct, nothing otherwise.
     *
     * @throws PH7InvalidArgumentException If the table is not valid.
     */
    public static function checkTable(string $sTable)
    {
        if (self::doesTableExist($sTable)) {
            return $sTable;
        }

        Various::launchErr($sTable);
    }

    /**
     * Convert table to Ads' ID.
     *
     * @param string $sTable
     *
     * @return string|void Returns the table if it is correct, nothing otherwise.
     *
     * @throws PH7InvalidArgumentException If the table is not valid.
     */
    public static function convertTableToId(string $sTable)
    {
        if (self::doesTableExist($sTable)) {
            return static::ID_COLUMN_NAME;
        }

        Various::launchErr($sTable);
    }

    private static function doesTableExist(string $sTable): bool
    {
        return in_array($sTable, self::TABLE_NAMES, true);
    }
}
