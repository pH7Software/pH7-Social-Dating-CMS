<?php
/**
 * @title            Design Model Class
 * @desc             Design Model for the HTML contents.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Model
 */

namespace PH7\Framework\Mvc\Model;

defined('PH7') or exit('Restricted access');

use PH7\AdminCore;
use PH7\DbTableName;
use PH7\Framework\Ads\Ads as Banner;
use PH7\Framework\Cache\Cache;
use PH7\Framework\Layout\Html\Design as HtmlDesign;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Lang as LangModel;
use PH7\Framework\Navigation\Page;
use PH7\Framework\Parse\SysVar;
use PH7\Framework\Translate\Lang;

class Design extends HtmlDesign
{
    const CACHE_STATIC_GROUP = 'db/design/static';
    const CACHE_TIME = 172800;

    /** @var Cache */
    private $oCache;

    public function __construct()
    {
        parent::__construct();
        $this->oCache = new Cache;
    }

    public function langList()
    {
        $sCurrentPage = Page::cleanDynamicUrl('l');

        $aLangs = (new LangModel)->getInfos();
        foreach ($aLangs as $sLang) {
            if ($sLang->langId === PH7_LANG_NAME) {
                // Skip the current lang
                continue;
            }

            // Get the first|last two-letter country code
            $sAbbrLang = Lang::getIsoCode($sLang->langId, Lang::FIRST_ISO_CODE);
            $sFlagCountryCode = Lang::getIsoCode($sLang, Lang::LAST_ISO_CODE);

            echo '<a href="', $sCurrentPage, $sLang->langId, '" hreflang="', $sAbbrLang, '"><img src="', PH7_URL_STATIC, PH7_IMG, 'flag/s/', $sFlagCountryCode, Design::FLAG_ICON_EXT, '" alt="', t($sAbbrLang), '" title="', t($sAbbrLang), '" /></a>&nbsp;';
        }
        unset($aLangs);
    }

    /**
     * Gets Ads with ORDER BY RAND() SQL aggregate function.
     * With caching, advertising changes every hour.
     *
     * @param int $iWidth
     * @param int $iHeight
     * @param bool $bOnlyActive
     *
     * @return bool|void
     */
    public function ad($iWidth, $iHeight, $bOnlyActive = true)
    {
        $this->oCache->start(self::CACHE_STATIC_GROUP, 'ads' . $iWidth . $iHeight . $bOnlyActive, static::CACHE_TIME);

        if (!$oData = $this->oCache->get()) {
            $sSqlActive = $bOnlyActive ? ' AND (active = \'1\') ' : ' ';
            $rStmt = Db::getInstance()->prepare('SELECT * FROM ' . Db::prefix(DbTableName::AD) . 'WHERE (width=:width) AND (height=:height)' . $sSqlActive . 'ORDER BY RAND() LIMIT 1');
            $rStmt->bindValue(':width', $iWidth, \PDO::PARAM_INT);
            $rStmt->bindValue(':height', $iHeight, \PDO::PARAM_INT);
            $rStmt->execute();
            $oData = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->oCache->put($oData);
        }

        /**
         * Don't display ads on the admin panel.
         */
        if ($oData && !AdminCore::isAdminPanel()) {
            echo '<div class="inline" onclick="$(\'#ad_' . $oData->adsId . '\').attr(\'src\',\'' . PH7_URL_ROOT . '?' . Banner::PARAM_URL . '=' . $oData->adsId . '\');return true;">';
            echo Banner::output($oData, $this->oHttpRequest);
            echo '<img src="' . PH7_URL_STATIC . PH7_IMG . 'useful/blank.gif" style="border:0;width:0px;height:0px;" alt="" id="ad_' . $oData->adsId . '" /></div>';
        }
        unset($oData);
    }

    /**
     * Analytics API code.
     *
     * @param bool $bPrint Print the analytics HTML code.
     * @param bool $bOnlyActive Only active code.
     *
     * @return string|void
     */
    public function analyticsApi($bPrint = true, $bOnlyActive = true)
    {
        $this->oCache->start(self::CACHE_STATIC_GROUP, 'analyticsApi' . $bOnlyActive, static::CACHE_TIME);

        if (!$sData = $this->oCache->get()) {
            $sSqlWhere = $bOnlyActive ? 'WHERE active = \'1\'' : '';
            $rStmt = Db::getInstance()->prepare('SELECT code FROM ' . Db::prefix(DbTableName::ANALYTIC_API) . $sSqlWhere . ' LIMIT 1');
            $rStmt->execute();
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $sData = $oRow->code;
            unset($oRow);
            $this->oCache->put($sData);
        }

        if (!$bPrint) {
            return $sData;
        }

        echo $sData;
    }

    /**
     * Get the custom code.
     *
     * @param string $sType Choose between 'css' and 'js'.
     *
     * @return string
     */
    public function customCode($sType)
    {
        $this->oCache->start(self::CACHE_STATIC_GROUP, 'customCode' . $sType, static::CACHE_TIME);

        if (!$sData = $this->oCache->get()) {
            $rStmt = Db::getInstance()->prepare('SELECT code FROM ' . Db::prefix(DbTableName::CUSTOM_CODE) . 'WHERE codeType = :type LIMIT 1');
            $rStmt->bindValue(':type', $sType, \PDO::PARAM_STR);
            $rStmt->execute();
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $sData = !empty($oRow->code) ? $oRow->code : null;
            unset($oRow);
            $this->oCache->put($sData);
        }

        return $sData;
    }

    /**
     * Get CSS/JS files.
     *
     * @param string $sType Choose between 'css' and 'js'.
     * @param bool $bOnlyActive If TRUE, it will get only the files activated.
     *
     * @return void HTML output.
     */
    public function files($sType, $bOnlyActive = true)
    {
        $this->oCache->start(self::CACHE_STATIC_GROUP, 'files' . $sType . $bOnlyActive, static::CACHE_TIME);

        if (!$aData = $this->oCache->get()) {
            $sSqlWhere = $bOnlyActive ? ' AND active = \'1\'' : '';
            $rStmt = Db::getInstance()->prepare('SELECT file FROM ' . Db::prefix(DbTableName::STATIC_FILE) . 'WHERE fileType = :type' . $sSqlWhere);
            $rStmt->bindValue(':type', $sType, \PDO::PARAM_STR);
            $rStmt->execute();
            $aData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->oCache->put($aData);
        }

        if (!empty($aData)) {
            foreach ($aData as $oFile) {
                $sFullPath = (new SysVar)->parse($oFile->file);
                $sMethodName = 'external' . ($sType === 'js' ? 'Js' : 'Css') . 'File';
                $this->$sMethodName($sFullPath);
            }
        }
        unset($aData);
    }
}
