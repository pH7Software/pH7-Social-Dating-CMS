<?php

namespace PH7\Framework\Image;

use PH7\Framework\Config\Config;
use PH7\Framework\Pattern\Statik;

final class StorageHelper
{
    use Statik;

    /**
     * @return string
     */
    public static function getStorageClassName()
    {
        switch (true) {
            case self::isAmaZonCloudStorageSetup():
                return AmazonCloudStorage::class;

            default:
                return FileStorage::class ;
        }
    }

    /**
     * @return bool
     */
    private static function isAmaZonCloudStorageSetup()
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
