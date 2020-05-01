<?php
/**
 * @title            Design Class
 * @desc             File containing HTML for display management.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          CC-BY License - http://creativecommons.org/licenses/by/3.0/
 * @package          PH7 / Framework / Layout / Html
 */

namespace PH7\Framework\Layout\Html;

defined('PH7') or exit('Restricted access');

use PH7\AdminCore;
use PH7\AffiliateCore;
use PH7\DbTableName;
use PH7\Framework\Benchmark\Benchmark;
use PH7\Framework\Cache\Cache;
use PH7\Framework\Core\Kernel;
use PH7\Framework\File\File;
use PH7\Framework\Geo\Ip\Geo;
use PH7\Framework\Geo\Misc\Country;
use PH7\Framework\Http\Http;
use PH7\Framework\Ip\Ip;
use PH7\Framework\Module\Various as SysMod;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Navigation\Browser;
use PH7\Framework\Navigation\Page;
use PH7\Framework\Navigation\Pagination;
use PH7\Framework\Parse\Url as UrlParser;
use PH7\Framework\Registry\Registry;
use PH7\Framework\Security\Validate\Validate;
use PH7\Framework\Session\Session;
use PH7\Framework\Str\Str;
use PH7\Framework\Translate\Lang;
use PH7\Framework\Url\Url;
use PH7\GenderTypeUserCore;
use PH7\UserCore;
use PH7\UserCoreModel;

class Design
{
    const CACHE_GROUP = 'str/design';
    const CACHE_AVATAR_GROUP = 'str/design/avatar/'; // We put a slash for after creating a directory for each username
    const CACHE_AVATAR_LIFETIME = 3600;

    const NONE_FLAG_FILENAME = 'none.gif';
    const FLAG_ICON_EXT = '.gif';
    const AVATAR_IMG_EXT = '.svg';

    const SUCCESS_TYPE = 'success';
    const ERROR_TYPE = 'error';
    const WARNING_TYPE = 'warning';
    const INFO_TYPE = 'info';

    const MESSAGE_TYPES = [
        self::SUCCESS_TYPE,
        self::ERROR_TYPE,
        self::WARNING_TYPE,
        self::INFO_TYPE
    ];

    const FLASH_MSG = 'flash_msg';
    const FLASH_TYPE = 'flash_type';

    const DEFAULT_REDIRECTION_DELAY = 3; // In secs
    const MAX_MESSAGE_LENGTH_SHOWN = 300;
    const MAX_IP_LENGTH_SHOWN = 15;

    /** @var bool */
    protected $bIsDiv = false;

    /** @var Str */
    protected $oStr;

    /** @var Session */
    protected $oSession;

    /** @var HttpRequest */
    protected $oHttpRequest;

    /** @var array */
    protected $aCssDir = [];

    /** @var array */
    protected $aCssFiles = [];

    /** @var array */
    protected $aCssMedia = [];

    /** @var array */
    protected $aJsDir = [];

    /** @var array */
    protected $aJsFiles = [];

    /** @var array */
    protected $aMessages = [];

    /** @var array */
    protected $aErrors = [];

    public function __construct()
    {
        /** Instance objects for the class **/
        $this->oStr = new Str;
        $this->oSession = new Session;
        $this->oHttpRequest = new HttpRequest;
    }

    public function langList()
    {
        $sCurrentPage = Page::cleanDynamicUrl('l');
        //$aLangs = (new File)->getDirList(Registry::getInstance()->path_module_lang);
        $aLangs = (new File)->getDirList(PH7_PATH_APP_LANG);

        foreach ($aLangs as $sLang) {
            if ($sLang === PH7_LANG_NAME) {
                // Skip the current lang
                continue;
            }

            // Get the first|last two-letter country code
            $sAbbrLang = Lang::getIsoCode($sLang, Lang::FIRST_ISO_CODE);
            $sFlagCountryCode = Lang::getIsoCode($sLang, Lang::LAST_ISO_CODE);

            echo '<a href="', $sCurrentPage, $sLang, '" hreflang="', $sAbbrLang, '"><img src="', PH7_URL_STATIC, PH7_IMG, 'flag/s/', $sFlagCountryCode, self::FLAG_ICON_EXT, '" alt="', t($sAbbrLang), '" title="', t($sAbbrLang), '" /></a>&nbsp;';
        }

        unset($aLangs);
    }

    /**
     * For better SEO optimization for multilingual sites. Ref: https://support.google.com/webmasters/answer/189077
     *
     * @return void
     */
    public function regionalUrls()
    {
        $sCurrentPage = Page::cleanDynamicUrl('l');
        $aLangs = (new File)->getDirList(PH7_PATH_APP_LANG);

        echo '<link rel="alternate" hreflang="x-default" href="', PH7_URL_ROOT, '">'; // For pages that aren't specifically targeted
        foreach ($aLangs as $sLang) {
            // Get only the two-letter country code
            $sAbbrLang = Lang::getIsoCode($sLang);
            echo '<link rel="alternate" hreflang="', $sAbbrLang, '" href="', $sCurrentPage, $sLang, '" />';
        }

        unset($aLangs, $sCurrentPage);
    }

    /**
     * Set an information message.
     *
     * @param string $sMsg
     *
     * @return void
     */
    public function setMessage($sMsg)
    {
        $this->aMessages[] = $sMsg;
    }

    /**
     * Display the information message.
     *
     * @return void
     */
    public function message()
    {
        if ($this->oHttpRequest->getExists('msg')) {
            $this->aMessages[] = substr($this->oHttpRequest->get('msg'), 0, self::MAX_MESSAGE_LENGTH_SHOWN);
        }

        $iMsgNum = count($this->aMessages);
        /** Check if there are any messages in $aMessages array **/
        if ($iMsgNum > 0) {
            $this->staticFiles('js', PH7_STATIC . PH7_JS, 'jquery/apprise.js');

            echo '<script>$(function(){Apprise(\'';

            if ($iMsgNum > 1) {
                echo '<strong>', t('You have'), ' <em>', $iMsgNum, '</em> ', nt('message:', 'messages:', $iMsgNum), '</strong><br />';
            }

            for ($iKey = 0; $iKey < $iMsgNum; $iKey++) {
                echo $this->oStr->upperFirst(str_replace('-', ' ', $this->aMessages[$iKey])), '<br />';
            }

            echo '\')});</script>';
        }

        unset($this->aMessages);
    }

    /**
     * Set an error message.
     *
     * @param string $sErr
     *
     * @return void
     */
    public function setError($sErr)
    {
        $this->aErrors[] = $sErr;
    }

    /**
     * Display the error message.
     *
     * @return void
     */
    public function error()
    {
        if ($this->oHttpRequest->getExists('err')) {
            $this->aErrors[] = substr($this->oHttpRequest->get('err'), 0, self::MAX_MESSAGE_LENGTH_SHOWN);
        }

        $iErrNum = count($this->aErrors);
        /** Check if there are any errors in $aErrors array **/
        if ($iErrNum > 0) {
            $this->staticFiles('js', PH7_STATIC . PH7_JS, 'jquery/apprise.js');

            echo '<script>$(function(){Apprise(\'';
            echo '<strong>', t('You have'), ' <em>', $iErrNum, '</em> ', nt('error:', 'errors:', $iErrNum), '</strong><br />';

            for ($iKey = 0; $iKey < $iErrNum; $iKey++) {
                echo $this->oStr->upperFirst(str_replace('-', ' ', $this->aErrors[$iKey])), '<br />';
            }

            echo '\')});</script>';
        }

        unset($this->aErrors);
    }

    /**
     * Redirect Page using Refresh with Header.
     *
     * @param string $sUrl If NULL, the URL will be the current page. Default NULL
     * @param string $sMsg , Optional, display a message after redirect of the page.
     * @param string $sType Type of message: "success", "info", "warning" or "error". Default: "success".
     * @param int $iTime Optional, a time. Default: "3" seconds.
     *
     * @return void
     */
    public function setRedirect($sUrl = null, $sMsg = null, $sType = self::SUCCESS_TYPE, $iTime = self::DEFAULT_REDIRECTION_DELAY)
    {
        if ($sMsg !== null) {
            $this->setFlashMsg($sMsg, $sType);
        }

        $sUrl = $sUrl !== null ? $sUrl : $this->oHttpRequest->currentUrl();

        header('Refresh: ' . $iTime . '; URL=' . $this->oHttpRequest->pH7Url($sUrl));
    }

    /**
     * Get stats from the benchmark.
     *
     * @return void HTML output.
     */
    public function stat()
    {
        $iCountQueries = Db::queryCount();
        $sRequest = nt('Query', 'Queries', $iCountQueries);

        $sMicrotime = microtime(true) - Registry::getInstance()->start_time;
        $sTime = Benchmark::readableElapsedTime($sMicrotime);
        $iMemory = Benchmark::readableSize(memory_get_usage(true));

        echo t('Queries time %0% | %1% %2% | Generated in %3% | Memory allocated %4%', Db::time(), $iCountQueries, $sRequest, $sTime, $iMemory);
    }

    /**
     * Display accurate homepage URL.
     *
     * @return void The homepage URL output.
     */
    public function homePageUrl()
    {
        if (UserCore::auth()) {
            if (SysMod::isEnabled('user-dashboard')) {
                $this->url('user-dashboard', 'main', 'index');
            } else {
                $this->url('user', 'browse', 'index');
            }
        } elseif (AdminCore::auth()) {
            $this->url(PH7_ADMIN_MOD, 'main', 'index');
        } elseif (AffiliateCore::auth()) {
            $this->url('affiliate', 'account', 'index');
        } else {
            echo PH7_URL_ROOT;
        }
    }

    /**
     * @param string $sModule
     * @param string $sController
     * @param string $sAction
     * @param null|string $sVars
     * @param bool $bClear
     *
     * @return void
     */
    public function url($sModule, $sController, $sAction, $sVars = null, $bClear = true)
    {
        $sUrl = Uri::get($sModule, $sController, $sAction, $sVars, $bClear);

        echo Url::clean($sUrl); // For the URL parameters to avoid invalid HTML code
    }

    /**
     * Create a link of to display a popup confirmation for a CRUD action (http://en.wikipedia.org/wiki/Create,_read,_update_and_delete).
     *
     * @param string $sLabel
     * @param string $sMod
     * @param string $sCtrl
     * @param string $sAct
     * @param int|string $mId Content ID
     * @param string $sClass Add a CSS class
     *
     * @return void HTML output.
     */
    public function popupLinkConfirm($sLabel, $sMod, $sCtrl, $sAct, $mId, $sClass = null)
    {
        $sClass = $sClass !== null ? ' class="' . $sClass . '" ' : ' ';

        $aHttpParams = [
            'label' => Url::encode($sLabel),
            'mod' => $sMod,
            'ctrl' => $sCtrl,
            'act' => $sAct,
            'id' => Url::encode($mId)
        ];

        echo '<a', $sClass, 'href="', PH7_URL_ROOT, 'asset/ajax/popup/confirm/?', Url::httpBuildQuery($aHttpParams), '" data-popup="classic">', $sLabel, '</a>';
    }

    /**
     * @param string $sCountryCode The Country Code (e.g., US = United States).
     *
     * @return void Output the Flag Icon Url.
     */
    public function getSmallFlagIcon($sCountryCode)
    {
        $sIcon = $this->oStr->lower($sCountryCode) . self::FLAG_ICON_EXT;
        $sDir = PH7_URL_STATIC . PH7_IMG . 'flag/s/';

        echo is_file(PH7_PATH_STATIC . PH7_IMG . 'flag/s/' . $sIcon) ? $sDir . $sIcon : $sDir . self::NONE_FLAG_FILENAME;
    }

    /**
     * @return string
     */
    final public function softwareComment()
    {
        echo PageDna::generateHtmlComment();
    }

    /**
     * Provide a "Powered By" link.
     *
     * @param bool $bLink To include a link to pH7CMS or pH7Framework.
     * @param bool $bSoftwareName
     * @param bool $bVersion To include the version being used.
     * @param bool $bComment HTML comment.
     * @param bool $bEmailContext Is it for email content or not.
     *
     * @return void
     */
    final public function link($bLink = true, $bSoftwareName = true, $bVersion = false, $bComment = true, $bEmailContext = false)
    {
        if (!$bEmailContext && (bool)DbConfig::getSetting('displayPoweredByLink')) {
            if ($bLink) {
                $bSoftwareName = true;
            }

            echo ($bSoftwareName ? '<span class="italic">' . t('Big thanks to') : ''), ' <strong>', ($bLink ? '<a class="underline" href="' . Kernel::SOFTWARE_WEBSITE . '" title="' . Kernel::SOFTWARE_DESCRIPTION . '">' : ''), ($bSoftwareName ? Kernel::SOFTWARE_NAME : ''), ($bVersion ? ' ' . Kernel::SOFTWARE_VERSION : ''), ($bLink ? '</a>' : ''), ($bSoftwareName ? '</strong><span role="img" aria-label="love">❤️</span></span>' : '');
        }

        if ($bComment) {
            echo '
                <!-- ', sprintf(Kernel::SOFTWARE_COPYRIGHT, date('Y')), ' -->
                <!-- Powered by ', Kernel::SOFTWARE_NAME, ' ', Kernel::SOFTWARE_VERSION, ', Build ', Kernel::SOFTWARE_BUILD, ' -->
                <!-- This notice cannot be removed in any case.
                This open source software is distributed for free and you must respect the thousands of days, months and several years it took to develop it.
                Think to the developer who worked hard for years coding what you use.
                All rights reserved to ', Kernel::SOFTWARE_NAME, ', ', Kernel::SOFTWARE_COMPANY, '
                You can never claim that you own the code, developed or helped the software if it is not the case -->';
        }

        echo '<!-- "Powered by ', Kernel::SOFTWARE_NAME, ', ', Kernel::SOFTWARE_VERSION_NAME, ', ', Kernel::SOFTWARE_VERSION, ', Build ', Kernel::SOFTWARE_BUILD, ' -->';
    }

    /**
     * Provide a small "Powered By" link (e.g., for sitemap.xsl.tpl).
     *
     * @return void
     */
    final public function smallLink()
    {
        echo '<strong>', t('THANKS to'), ' <a href="', Kernel::SOFTWARE_WEBSITE, '" title="', Kernel::SOFTWARE_DESCRIPTION, '">', Kernel::SOFTWARE_NAME, '</a> ', Kernel::SOFTWARE_VERSION, '!</strong> <span role="img" aria-label="love">❤️</span>';
    }

    /**
     * @return void Output the relevant link based on the client browser's language.
     */
    final public function smartLink()
    {
        // Get Client's Language Code
        $sLangCode = (new Browser)->getLanguage(true);

        // Default links, set to English
        $aSites = [
            ['title' => 'Flirt Hot Girls', 'link' => 'https://01script.com/p/dooba'],
            ['title' => 'Speed Dating', 'link' => 'https://01script.com/p/dooba'],
            ['title' => 'Date your Friends', 'link' => 'https://01script.com/p/dooba'],
            ['title' => 'Free Dating CMS', 'link' => Kernel::SOFTWARE_GIT_REPO_URL],
            ['title' => 'Dating Software', 'link' => Kernel::SOFTWARE_GIT_REPO_URL],
            ['title' => 'Create a Tinder-Like Dating App', 'link' => Kernel::SOFTWARE_GIT_REPO_URL]
        ];

        if ($sLangCode === 'en-ie') {
            $aSites[] = ['title' => 'FREE Flirt in Dublin City', 'link' => 'https://01script.com/p/dooba'];
            $aSites[] = ['title' => 'Date Dubs in the Town!', 'link' => 'https://01script.com/p/dooba'];
        } elseif ($sLangCode === 'en-gb') {
            $aSites[] = ['title' => 'Date Brits near from YOU', 'link' => 'https://01script.com/p/dooba'];
            $aSites[] = ['title' => 'Date Londoners', 'link' => 'https://01script.com/p/dooba'];
        } elseif (strpos($sLangCode, 'fr') !== false) {
            /**
             * Reset the array since we don't want to mix it up with different languages (default one is English, not French)
             */
            $aSites = [
                ['title' => 'Rencontre d\'un soir', 'link' => 'https://01script.com/p/dooba'],
                ['title' => 'Flirt Coquin', 'link' => 'https://01script.com/p/dooba'],
                ['title' => 'Rencontre amoureuse', 'link' => 'https://01script.com/p/dooba']
            ];
        }

        $aSite = $aSites[array_rand($aSites)];
        echo '<a href="', $aSite['link'], '">', $aSite['title'], '</a>';
    }

    /**
     * @param string $sType (js or css).
     * @param string $sDir
     * @param string $sFiles
     * @param string $sCssMedia Only works for CSS files. The CSS Media type (e.g., screen,handheld,tv,projection). Default "all". Leave blank ('' or null) not to use the media attribute.
     *
     * @return void
     */
    public function staticFiles($sType, $sDir, $sFiles, $sCssMedia = 'all')
    {
        if ($sType === 'js') {
            echo $this->externalJsFile(PH7_RELATIVE . 'asset/gzip/?t=js&amp;d=' . $sDir . '&amp;f=' . $sFiles);
        } else {
            echo $this->externalCssFile(PH7_RELATIVE . 'asset/gzip/?t=css&amp;d=' . $sDir . '&amp;f=' . $sFiles, $sCssMedia);
        }
    }

    /**
     * @param string $sDir The CSS folder.
     * @param string $sFiles The CSS files.
     * @param string $sCssMedia CSS Media type (e.g., screen,handheld,tv,projection). Default "all". Leave blank ('' or null) not to use the media attribute.
     *
     * @return void
     */
    public function addCss($sDir, $sFiles, $sCssMedia = 'all')
    {
        $this->aCssDir[] = $sDir;
        $this->aCssFiles[] = $sFiles;
        $this->aCssMedia[] = $sCssMedia;
    }

    /**
     * @param string $sDir The JavaScript folder.
     * @param string $sFiles The JavaScript files.
     *
     * @return void
     */
    public function addJs($sDir, $sFiles)
    {
        $this->aJsDir[] = $sDir;
        $this->aJsFiles[] = $sFiles;
    }

    /**
     * @return void
     */
    public function css()
    {
        for ($iKey = 0, $iCount = count($this->aCssDir); $iKey < $iCount; $iKey++) {
            $this->staticFiles('css', $this->aCssDir[$iKey], $this->aCssFiles[$iKey], $this->aCssMedia[$iKey]);
        }

        unset($this->aCssDir, $this->aCssFiles, $this->aCssMedia);
    }

    /**
     * @return void
     */
    public function js()
    {
        for ($iKey = 0, $iCount = count($this->aJsDir); $iKey < $iCount; $iKey++) {
            $this->staticFiles('js', $this->aJsDir[$iKey], $this->aJsFiles[$iKey]);
        }

        unset($this->aJsDir, $this->aJsFiles);
    }

    /**
     * Set flash message.
     *
     * @param string $sMessage
     * @param string $sType Type of message: "Design::SUCCESS_TYPE", "Design::INFO_TYPE", "Design::WARNING_TYPE" or "Design::ERROR_TYPE"
     *
     * @return void
     */
    public function setFlashMsg($sMessage, $sType = self::SUCCESS_TYPE)
    {
        /** Check the type of message, otherwise it's the default one **/
        $sType = in_array($sType, self::MESSAGE_TYPES, true) ? $sType : self::SUCCESS_TYPE;
        $sType = $sType === self::ERROR_TYPE ? 'danger' : $sType; // The "error" CSS class is now "danger", so convert it to the corresponding name
        $this->oSession->set(
            [
                self::FLASH_MSG => $sMessage,
                self::FLASH_TYPE => $sType
            ]
        );
    }

    /**
     * Flash displays the message defined in the method setFlash.
     *
     * @return void The message text with CSS layout depending on the type of message.
     */
    public function flashMsg()
    {
        $aFlashData = [
            self::FLASH_MSG,
            self::FLASH_TYPE
        ];

        if ($this->oSession->exists($aFlashData)) {
            echo '<div class="center bold alert alert-', $this->oSession->get(self::FLASH_TYPE), '" role="alert">', $this->oSession->get(self::FLASH_MSG), '</div>';

            $this->oSession->remove($aFlashData);
        }
    }

    /**
     * Show the user IP address with a link to get the IP information.
     *
     * @internal If it's an IPv6, show only the beginning, otherwise it would be too long in the template.
     *
     * @param string $sIp Allows to specify another IP address than the client one.
     * @param bool $bPrint Print or Return the HTML code. Default TRUE
     *
     * @return void|string
     */
    public function ip($sIp = null, $bPrint = true)
    {
        $sIp = Ip::get($sIp);
        $sHtml = '<a href="' . Ip::api($sIp) . '" title="' . t('Get information about this IP address') . '" target="_blank" rel="noopener noreferrer">' . $this->oStr->extract($sIp, self::MAX_IP_LENGTH_SHOWN) . '</a>';

        if (!$bPrint) {
            return $sHtml;
        }

        echo $sHtml;
    }

    /**
     * Show the geolocation of the user (with link that points to the Country controller).
     *
     * @param bool $bPrint Print or Return the HTML code. Default TRUE
     *
     * @return void|string
     */
    public function geoIp($bPrint = true)
    {
        $sCountry = Geo::getCountry();
        $sCountryCode = Country::fixCode(Geo::getCountryCode());
        $sCountryLang = t($sCountryCode); // Country name translated into the user language
        $sCity = Geo::getCity();

        if (SysMod::isEnabled('map')) {
            $sHtml = '<a href="' . Uri::get('map', 'country', 'index', $sCountry . PH7_SH . $sCity) . '" title="' . t('Meet New People in %0%, %1% with %site_name%!', $sCountryLang, $sCity) . '">' . $sCity . '</a>';
        } else {
            $sHtml = '<abbr title="' . t('Meet New People in %0%, %1% thanks to %site_name%!', $sCountryLang, $sCity) . '">' . $sCity . '</abbr>';
        }

        if (!$bPrint) {
            return $sHtml;
        }

        echo $sHtml;
    }

    /**
     * Pagination.
     *
     * @param int $iTotalPages
     * @param int $iCurrentPage
     *
     * @return void The HTML pagination code.
     */
    public function pagination($iTotalPages, $iCurrentPage)
    {
        echo (new Pagination($iTotalPages, $iCurrentPage))->getHtmlCode();
    }

    /**
     * @param string $sUsername
     * @param string $sSex
     * @param int $iSize
     * @param bool $bPrint Print or Return the HTML code.
     *
     * @return void|string The default 150px avatar URL or the user avatar URL.
     */
    public function getUserAvatar($sUsername, $sSex = '', $iSize = null, $bPrint = true)
    {
        $oCache = (new Cache)->start(
            self::CACHE_AVATAR_GROUP . $sUsername,
            $sSex . $iSize,
            self::CACHE_AVATAR_LIFETIME
        );

        if (!$sUrl = $oCache->get()) {
            $oUserModel = new UserCoreModel;

            $iProfileId = $oUserModel->getId(null, $sUsername);
            $oGetAvatar = $oUserModel->getAvatar($iProfileId);

            $sSize = ($iSize == 32 || $iSize == 64 || $iSize == 100 || $iSize == 150 || $iSize == 200 || $iSize == 400) ? '-' . $iSize : '';

            $sAvatar = @$oGetAvatar->pic;
            $sExt = PH7_DOT . (new File)->getFileExt($sAvatar);

            $sDir = 'user/avatar/img/' . $sUsername . PH7_SH;
            $sPath = PH7_PATH_PUBLIC_DATA_SYS_MOD . $sDir . $sAvatar;
            $sUrl = PH7_URL_DATA_SYS_MOD . $sDir . str_replace($sExt, $sSize . $sExt, $sAvatar);

            $bIsModerationMode = AdminCore::isAdminPanel();

            if (!is_file($sPath) || $oGetAvatar->approvedAvatar == '0') {
                /* If sex is empty, it is recovered in the database using information from member */
                $sSex = !empty($sSex) ? $sSex : $oUserModel->getSex(null, $sUsername, DbTableName::MEMBER);
                $sSex = $this->oStr->lower($sSex);
                $sIcon = (GenderTypeUserCore::isGenderValid($sSex) || $sSex === PH7_ADMIN_USERNAME) ? $sSex : 'visitor';
                $sUrlTplName = defined('PH7_TPL_NAME') ? PH7_TPL_NAME : PH7_DEFAULT_THEME;

                /** If the user doesn't have an avatar **/
                if (!is_file($sPath)) {
                    /* The user has no avatar, we try to get a Gravatar */

                    // Get the User Email
                    $sEmail = $oUserModel->getEmail($iProfileId);

                    $bSecuredGravatar = Http::isSsl();
                    $sUrl = $this->getGravatarUrl($sEmail, '404', $iSize, 'g', $bSecuredGravatar);

                    if (!(new Validate)->url($sUrl, true)) {
                        // If no Gravatar set, it returns 404, and we then set the default pH7CMS's avatar
                        $sUrl = PH7_URL_TPL . $sUrlTplName . PH7_SH . PH7_IMG . 'icon/' . $sIcon . '_no_picture' . $sSize . self::AVATAR_IMG_EXT;
                    }
                } elseif (!$bIsModerationMode) { // We don't display pending approval image when admins are on the panel admin
                    $sUrl = PH7_URL_TPL . $sUrlTplName . PH7_SH . PH7_IMG . 'icon/pending' . $sSize . self::AVATAR_IMG_EXT;
                }
            }
            unset($oUserModel);

            /**
             * @internal Clean URL for parameters in Gravatar URL to make the HTML code valid.
             * If we set replace '&' by '&amp;' before checking the 404's Gravatar URL, it will always return '200 OK', that's why we need to clean the URL now.
             */
            $oCache->put(Url::clean($sUrl));
        }

        unset($oCache);

        if (!$bPrint) {
            return $sUrl;
        }

        echo $sUrl;
    }

    /**
     * Get the user profile link.
     *
     * @param string $sUsername
     * @param bool $bPrint Print or Return the HTML code.
     *
     * @return void|string The absolute user profile link.
     */
    public function getProfileLink($sUsername, $bPrint = true)
    {
        $sHtml = '<a href="';
        $sHtml .= (new UserCore)->getProfileLink($sUsername);
        $sHtml .= '" title="' . t("%0%'s profile", $sUsername) . '">' . $sUsername . '</a>';

        if (!$bPrint) {
            return $sHtml;
        }

        echo $sHtml;
    }

    /**
     * Get the Gravatar URL.
     *
     * @param string $sEmail The user email address.
     * @param string $sType Default image type to show [ 404 | mp | identicon | monsterid | wavatar ]
     * @param int $iSize The size of the image. Default: 80
     * @param string $sRating The max image rating allowed. Default: 'g' (for all)
     * @param bool $bSecure Display avatar via HTTPS, for example if the site uses HTTPS, you should use this option to not get a warning with most Web browsers. Default: FALSE
     *
     * @return string The Gravatar Link.
     */
    public function getGravatarUrl($sEmail, $sType = 'wavatar', $iSize = 80, $sRating = 'g', $bSecure = false)
    {
        $sProtocol = $bSecure ? 'https' : 'http';
        $bSubDomain = $bSecure ? 'secure' : 'www';

        return $sProtocol . '://' . $bSubDomain . '.gravatar.com/avatar/' . md5(strtolower($sEmail)) . '?d=' . $sType . '&s=' . $iSize . '&r=' . $sRating;
    }

    /**
     * Get favicon from a URL.
     *
     * @param string $sUrl
     *
     * @return void The HTML favicon image.
     */
    public function favicon($sUrl)
    {
        $iFaviconSize = 16;
        $sImg = Browser::favicon($sUrl);
        $sName = Http::getHostName($sUrl);

        $this->imgTag(
            $sImg,
            $sName,
            [
                'width' => $iFaviconSize,
                'height' => $iFaviconSize
            ]
        );
    }

    /**
     * Like Link.
     *
     * @param string $sUsername Username of member.
     * @param string $sFirstName First name of member.
     * @param string $sSex Sex of member.
     * @param string $sForceUrlKey Specify a specific URL to be liked. Default NULL (current browser URL).
     *
     * @return void
     */
    public function like($sUsername, $sFirstName, $sSex, $sForceUrlKey = null)
    {
        $aHttpParams = [
            'msg' => t('You need to be a member for liking contents.'),
            'ref' => $this->oHttpRequest->currentController(),
            'a' => 'like',
            'u' => $sUsername,
            'f_n' => $sFirstName,
            's' => $sSex
        ];

        $bIsLogged = UserCore::auth();
        $sLikeLink = $bIsLogged ? '#' : Uri::get('user', 'signup', 'step1', '?' . Url::httpBuildQuery($aHttpParams), false);
        $sLikeId = $bIsLogged ? ' id="like"' : '';

        $sUrlKey = empty($sForceUrlKey) ? $this->oHttpRequest->currentUrl() : $sForceUrlKey;
        echo '<a rel="nofollow" href="', $sLikeLink, '" data-key="', $sUrlKey, '" title="', t('Like %0%', $sFirstName), '" class="like smooth-pink"', $sLikeId, '>', t('Like %0%', $sFirstName), '</a>';
        $this->staticFiles('js', PH7_STATIC . PH7_JS, 'Like.js');
    }

    /**
     * Add 'normal' size of the Social Media Widgets.
     *
     * @internal AddToAny JS file will be included through 'ph7_static_files' table.
     *
     * @return void HTML output.
     */
    public function socialMediaWidgets()
    {
        if ((bool)DbConfig::getSetting('socialMediaWidgets')) {
            $sHtml = <<<HTML
<div class="a2a_kit a2a_kit_size_32 a2a_default_style">
    <a class="a2a_dd" href="https://www.addtoany.com/share" rel="nofollow"></a>
    <a class="a2a_button_facebook"></a>
    <a class="a2a_button_twitter"></a>
    <a class="a2a_button_pinterest"></a>
    <a class="a2a_button_facebook_messenger"></a>
    <a class="a2a_button_linkedin"></a>
</div>
HTML;
            echo $sHtml;
        }
    }

    /**
     * Add 'small' size of the Social Media Widgets.
     *
     * @internal AddToAny JS file will be included through 'ph7_static_files' table.
     *
     * @return void HTML output.
     */
    public function littleSocialMediaWidgets()
    {
        if ((bool)DbConfig::getSetting('socialMediaWidgets')) {
            $sHtml = <<<HTML
<div class="a2a_kit a2a_kit_size_32 a2a_default_style">
    <a class="a2a_dd" href="https://www.addtoany.com/share" rel="nofollow"></a>
    <a class="a2a_button_facebook"></a>
    <a class="a2a_button_twitter"></a>
    <a class="a2a_button_pinterest"></a>
</div>
HTML;
            echo $sHtml;
        }
    }

    /**
     * Generate a Report Link.
     *
     * @param int $iId
     * @param string $sUsername
     * @param string $sFirstName
     * @param string $sSex
     *
     * @internal We do not use Url::httpBuildQuery() method for the first condition otherwise the URL is distorted and it doesn't work.
     *
     * @return void
     */
    public function report($iId, $sUsername, $sFirstName, $sSex)
    {
        $iId = (int)$iId;

        if ($iId > PH7_GHOST_ID) {
            if (UserCore::auth()) {
                $aUrlParams = [
                    'spammer' => $iId,
                    'type' => Registry::getInstance()->module
                ];
                $sReportLink = Uri::get(
                    'report',
                    'main',
                    'abuse',
                    '?' . Url::httpBuildQuery($aUrlParams) . '&amp;url=' . $this->oHttpRequest->currentUrl(),
                    false
                );

                $sReportLink .= '" data-popup="block-page';
            } else {
                $aUrlParams = [
                    'msg' => t('You need to be a user to report contents.'),
                    'ref' => 'profile',
                    'a' => 'report',
                    'u' => $sUsername,
                    'f_n' => $sFirstName,
                    's' => $sSex
                ];
                $sReportLink = Uri::get('user', 'signup', 'step1', '?' . Url::httpBuildQuery($aUrlParams), false);
            }

            echo '<a rel="nofollow" href="', $sReportLink, '" title="', t('Report Abuse'), '"><i class="fa fa-flag smooth-pink"></i></a>';
        } else {
            echo '<abbr title="' . t('Report feature is not available for this content since the user who posted that content has been removed.') . '""><i class="fa fa-flag smooth-pink"></i></abbr>';
        }
    }

    /**
     * Generate a Link tag.
     *
     * @param string $sLink The link.
     * @param bool $bNoFollow Set TRUE to set the link "nofollow", FALSE otherwise. Default TRUE
     *
     * @return void The HTML link tag.
     */
    public function urlTag($sLink, $bNoFollow = true)
    {
        $sLinkName = UrlParser::name($sLink);
        $aDefAttrs = ['href' => $sLink, 'title' => $sLinkName];

        if ($bNoFollow) {
            /**
             * Add "nofollow" attribute if "$bNoFollow" is TURE
             * If TRUE, this means we don't trust the link and might be opened on a new tab, so add "noopener noreferrer" to prevent Reverse Tabnabbing attacks.
             */
            $aDefAttrs += ['rel' => 'nofollow noopener noreferrer'];
        }

        $this->htmlTag('a', $aDefAttrs, true, $sLinkName);
    }

    /**
     * Generate a IMG tag.
     *
     * @param string $sImg The image.
     * @param string $sAlt Alternate text.
     * @param array $aAttrs Optional. Array containing the "name" and "value" HTML attributes. Default NULL
     *
     * @return void The HTML image tag.
     */
    public function imgTag($sImg, $sAlt, array $aAttrs = null)
    {
        $aDefAttrs = ['src' => $sImg, 'alt' => $sAlt];

        if ($aAttrs !== null) {
            $aDefAttrs += $aAttrs; // Update the attributes if necessary
        }

        $this->htmlTag('img', $aDefAttrs);
    }

    /**
     * Generate any HTML tag.
     *
     * @param string $sTag
     * @param array $aAttrs Optional. Default NULL
     * @param bool $bPair Optional. Default FALSE
     * @param string $sText Optional. Add text, available only for pair tag. Default NULL
     *
     * @return string The custom HTML tag.
     */
    public function htmlTag($sTag, array $aAttrs = null, $bPair = false, $sText = null)
    {
        $sAttrs = '';

        if ($aAttrs !== null) {
            foreach ($aAttrs as $sName => $sValue) {
                $sAttrs .= ' ' . $sName . '="' . $sValue . '"';
            }
        }

        echo ($bPair ? '<' . $sTag . $sAttrs . '>' . ($sText === null ? '' : $sText) . '</' . $sTag . '>' : '<' . $sTag . $sAttrs . ' />');
    }

    public function htmlHeader()
    {
        echo '<!DOCTYPE html>';
    }

    /**
     * Useful HTML Header.
     *
     * @param array $aMeta
     * @param bool $bLogo
     *
     * @return void
     */
    final public function usefulHtmlHeader(array $aMeta = null, $bLogo = false)
    {
        $this->bIsDiv = true;

        // DO NOT REMOVE THE COPYRIGHT CODE BELOW! Thank you!
        echo '<html><head><meta charset="utf-8" />
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <title>', (!empty($aMeta['title']) ? $aMeta['title'] : ''), '</title>';

        if (!empty($aMeta['description'])) {
            echo '<meta name="description" content="', $aMeta['description'], '" />';
        }

        if (!empty($aMeta['keywords'])) {
            echo '<meta name="keywords" content="', $aMeta['keywords'], '" />';
        }

        if (!empty($aMeta['noindex'])) {
            echo Meta::NOINDEX;
        }

        echo '<meta name="author" content="', Kernel::SOFTWARE_COMPANY, '" />
        <meta name="copyright" content="', sprintf(Kernel::SOFTWARE_COPYRIGHT, date('Y')), '" />
        <meta name="creator" content="', Kernel::SOFTWARE_NAME, '" />
        <meta name="designer" content="', Kernel::SOFTWARE_NAME, '" />
        <meta name="generator" content="', Kernel::SOFTWARE_NAME, ' ', Kernel::SOFTWARE_VERSION_NAME, ' ', Kernel::SOFTWARE_VERSION, ', Build ', Kernel::SOFTWARE_BUILD, '" />';
        $this->softwareComment();
        $this->externalCssFile(PH7_URL_STATIC . PH7_CSS . 'js/jquery/smoothness/jquery-ui.css');
        $this->staticFiles('css', PH7_LAYOUT . PH7_TPL . PH7_DEFAULT_THEME . PH7_SH . PH7_CSS, 'common.css,style.css,form.css');
        $this->externalJsFile(PH7_URL_STATIC . PH7_JS . 'jquery/jquery.js');
        $this->externalJsFile(PH7_URL_STATIC . PH7_JS . 'jquery/jquery-ui.js');
        echo '<script>var pH7Url={base:\'', PH7_URL_ROOT, '\'}</script></head><body>';

        if ($bLogo) {
            // Website's name
            $sSiteName = Registry::getInstance()->site_name;

            // Check if the website's name exists, otherwise we displayed the software's name
            $sName = !empty($sSiteName) ? $sSiteName : Kernel::SOFTWARE_NAME;

            echo '<header>
            <div role="banner" id="logo"><h1><a href="', PH7_URL_ROOT, '" title="', $sName, ' — ', Kernel::SOFTWARE_NAME, ', ', Kernel::SOFTWARE_COMPANY, '">', $sName, '</a></h1></div>
            </header>';
        }

        echo $this->flashMsg(), '<div class="msg"></div><div class="m_marg">';
    }

    public function htmlFooter()
    {
        if ($this->bIsDiv) {
            echo '</div>';
        }

        echo '</body></html>';
    }

    /**
     * The XML tag doesn't work in PHP files since it is the same "<?" language tag.
     * So this method can introduce the XML header without causing an error by the PHP interpreter.
     *
     * @return void
     */
    public function xmlHeader()
    {
        echo '<?xml version="1.0" encoding="utf-8"?>';
    }

    /**
     * Get an external CSS file.
     *
     * @param string $sFile CSS file.
     * @param string $sCssMedia Only works for CSS files. The CSS Media type (e.g., screen,handheld,tv,projection). Default NULL
     *
     * @return void HTML link tag.
     */
    public function externalCssFile($sFile, $sCssMedia = null)
    {
        $sCssMedia = $sCssMedia !== null ? ' media="' . $sCssMedia . '"' : '';

        echo '<link rel="stylesheet" href="', $sFile, '"', $sCssMedia, ' />';
    }

    /**
     * Get an external JS file.
     *
     * @param string $sFile JS file.
     *
     * @return void HTML script tag.
     */
    public function externalJsFile($sFile)
    {
        echo '<script src="', $sFile, '"></script>';
    }
}
