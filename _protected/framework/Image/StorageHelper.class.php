<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2021, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Image
 */

declare(strict_types=1);

namespace PH7\Framework\Image;

use PH7\Framework\Config\Config;
use PH7\Framework\Pattern\Statik;

final class StorageHelper
{
    use Statik;

    public static function getStorageClassName(): string
    {
        switch (true) {
            case self::isAmaZonCloudStorageSetup():
                return AmazonCloudStorage::class;

            default:
                return FileStorage::class ;
        }
    }

    private static function isAmaZonCloudStorageSetup(): bool
    {
        $aRequiredAwsKeys = [
            'aws.access_key_id',
            'aws.secret_access_key',
            'aws.default_region'
        ];

        foreach ($aRequiredAwsKeys as $sKey) {
            if (empty(Config::getInstance()->values['storage'][$sKey])) {
                return false;
            }
        }

        return true;
    }
}
