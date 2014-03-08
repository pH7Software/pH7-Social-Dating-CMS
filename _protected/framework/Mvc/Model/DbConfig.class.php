<?php
/**
 * @title            DbConfig Class
 * @desc             Database Config Class.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2014, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Model
 * @version          1.1
 */

namespace PH7\Framework\Mvc\Model;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Cache\Cache;

final class DbConfig
{

    const
    CACHE_GROUP = 'db/config',
    CACHE_TIME = 999000,
    ENABLE_SITE = 'enable',
    MAINTENANCE_SITE = 'maintenance';

    /**
     * Private constructor to prevent instantiation of class, because it's a static class.
     */
    private function __construct() {}

    /**
     * @param string $sSetting You can specify a specific parameter. Default NULL
     * @return mixed (string | integer | object) Returns a string or an integer if you specify a specific parameter, otherwise returns an object.
     */
    public static function getSetting($sSetting = null)
    {
        $oCache = (new Cache)->start(self::CACHE_GROUP, 'setting' . $sSetting, self::CACHE_TIME);

        // @return value of config the database
        if (!empty($sSetting))
        {
            if (!$sData = $oCache->get())
            {
                $rStmt = Engine\Db::getInstance()->prepare('SELECT value FROM' . Engine\Db::prefix('Settings') . 'WHERE name=:setting');
                $rStmt->bindParam(':setting', $sSetting, \PDO::PARAM_STR);
                $rStmt->execute();
                $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
                Engine\Db::free($rStmt);
                $sData = $oRow->value;
                unset($oRow);
                $oCache->put($sData);
            }
            $mData = $sData;
        }
        else
        {
            if (!$oData = $oCache->get())
            {
                $rStmt = Engine\Db::getInstance()->prepare('SELECT * FROM' . Engine\Db::prefix('Settings'));
                $rStmt->execute();
                $oData = $rStmt->fetch(\PDO::FETCH_OBJ);
                Engine\Db::free($rStmt);
                $oCache->put($oData);
            }
            $mData = $oData;
        }

        unset($oCache);
        return (empty($mData)) ? 0 : $mData;
    }

    public static function setSetting($sValue, $sName)
    {
        Engine\Record::getInstance()->update('Settings', 'value', $sValue, 'name', $sName);
    }

    public static function getMetaMain($sLangId)
    {
        $oCache = (new Cache)->start(self::CACHE_GROUP, 'metaMain' . $sLangId, self::CACHE_TIME);

        // @return value of meta tags the database
        if (!$oData = $oCache->get())
        {
            $rStmt = Engine\Db::getInstance()->prepare('SELECT * FROM' . Engine\Db::prefix('MetaMain') . 'WHERE langId=:langId');
            $rStmt->bindParam(':langId', $sLangId, \PDO::PARAM_STR);
            $rStmt->execute();
            $oData = $rStmt->fetch(\PDO::FETCH_OBJ);
            Engine\Db::free($rStmt);
            $oCache->put($oData);
        }
        unset($oCache);

        return $oData;
    }

    /**
     * Sets the Meta Main Data.
     *
     * @param string $sSection
     * @param string $sValue
     * @param string $sLangId
     * @return void
     */
    public static function setMetaMain($sSection, $sValue, $sLangId)
    {
        Engine\Record::getInstance()->update('MetaMain', $sSection, $sValue, 'langId', $sLangId);
    }

    /**
     * @param string The constant 'DbConfig::ENABLE_SITE' or 'DbConfig::MAINTENANCE_SITE'
     * @return void
     */
    public static function setSiteMode($sStatus)
    {
        if ($sStatus != self::MAINTENANCE_SITE && $sStatus != self::ENABLE_SITE) exit('Poor maintenance mode!');

        self::setSetting($sStatus, 'siteStatus');

        /* Clean DbConfig Cache */
        (new Cache)->start(self::CACHE_GROUP, null, null)->clear();
    }

}
