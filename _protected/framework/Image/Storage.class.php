<?php

namespace PH7\Framework\Image;

use PH7\Framework\Config\Config;
use PH7\Framework\Pattern\Statik;

final class Storage
{
    use Statik;

    /** @var Storageable|null */
    private static $oStorage = null;

    public static function get()
    {
        if (self::$oStorage === null) {
            switch (true) {
                case self::isAmaZonCloudStorageSetup():
                    self::$oStorage = new AmazonCloudStorage();
                    break;

                default :
                    self::$oStorage = new FileStorage();
                    break;
            }
        }

        return self::$oStorage;
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
            if(empty(Config::getInstance()->values['storage'][$sKey])) {
                return false;
            }
        }

        return true;
    }
}
