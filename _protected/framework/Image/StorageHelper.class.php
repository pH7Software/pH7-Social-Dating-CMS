<?php

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
