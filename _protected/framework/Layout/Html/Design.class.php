<?php
/**
 * @title            Design Class
 * @desc             File containing HTML for display management.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          CC-BY License - http://creativecommons.org/licenses/by/3.0/
 * @package          PH7 / Framework / Layout / Html
 */

namespace PH7\Framework\Layout\Html;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Core\Kernel,
PH7\Framework\Registry\Registry,
PH7\Framework\Mvc\Model\Engine\Db,
PH7\Framework\Mvc\Model\DbConfig,
PH7\UserCore,
PH7\Framework\Url\Url,
PH7\Framework\Ip\Ip,
PH7\Framework\Geo\Ip\Geo,
PH7\Framework\Str\Str,
PH7\Framework\File\File,
PH7\Framework\Session\Session,
PH7\Framework\Navigation\Page,
PH7\Framework\Geo\Misc\Country,
PH7\Framework\Benchmark\Benchmark,
PH7\Framework\Layout\Tpl\Engine\PH7Tpl\PH7Tpl,
PH7\Framework\Module\Various as SysMod,
PH7\Framework\Mvc\Request\Http,
PH7\Framework\Mvc\Router\Uri;

class Design
{
    const
    CACHE_GROUP = 'str/design',
    CACHE_AVATAR_GROUP = 'str/design/avatar/'; // We put a slash for after creating a directory for each username

    const AVATAR_IMG_EXT = '.png';

    const
    SUCCESS_TYPE = 'success',
    ERROR_TYPE = 'error',
    WARNING_TYPE = 'warning',
    INFO_TYPE = 'info';

    const MESSAGE_TYPES = [
        self::SUCCESS_TYPE,
        self::ERROR_TYPE,
        self::WARNING_TYPE,
        self::INFO_TYPE
    ];

    const
    FLASH_MSG = 'flash_msg',
    FLASH_TYPE = 'flash_type';

    protected
    $bIsDiv = false,
    $oStr,
    $oSession,
    $oHttpRequest,
    $aCssDir = array(),
    $aCssFiles = array(),
    $aCssMedia = array(),
    $aJsDir = array(),
    $aJsFiles = array(),
    $aMessages = array(),
    $aErrors = array();

    public function __construct()
    {
        /** Instance objects for the class **/
        $this->oStr = new Str;
        $this->oSession = new Session;
        $this->oHttpRequest = new Http;
    }

    public function langList()
    {
        $sCurrentPage = Page::cleanDynamicUrl('l');
        //$aLangs = (new File)->getDirList(Registry::getInstance()->path_module_lang);
        $aLangs = (new File)->getDirList(PH7_PATH_APP_LANG);

        foreach ($aLangs as $sLang)
        {
            if ($sLang === PH7_LANG_NAME) {
                // Skip the current lang
                continue;
            }

            // Retrieve only the first two characters
            $sAbbrLang = substr($sLang,0,2);

            echo '<a href="', $sCurrentPage, $sLang, '" hreflang="', $sAbbrLang, '"><img src="', PH7_URL_STATIC, PH7_IMG, 'flag/s/', $sAbbrLang, '.gif" alt="', t($sAbbrLang), '" title="', t($sAbbrLang), '" /></a>&nbsp;';
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

        echo '<link rel="alternate" hreflang="x-default" href="', PH7_URL_ROOT, '">'; // For pages that are not specifically targeted
        foreach ($aLangs as $sLang)
        {
            // Retrieve only the first two characters
            $sAbbrLang = substr($sLang,0,2);
            echo '<link rel="alternate" hreflang="', $sAbbrLang, '" href="', $sCurrentPage, $sLang, '" />';
        }

        unset($aLangs, $sCurrentPage);
    }

    /**
     * Set an information message.
     *
     * @param string $sMsg
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
        if ($this->oHttpRequest->getExists('msg'))
            $this->aMessages[] = substr($this->oHttpRequest->get('msg'),0,300);

        $iMsgNum = count($this->aMessages);
        /*** Check if there are any messages in the aMessages array ***/
        if ($iMsgNum > 0)
        {
            $this->staticFiles('js', PH7_STATIC . PH7_JS, 'jquery/apprise.js');

            echo '<script>$(function(){Apprise(\'';

            if ($iMsgNum > 1)
                echo '<strong>', t('You have'), ' <em>', $iMsgNum, '</em> ', nt('message:', 'messages:', $iMsgNum), '</strong><br />';

            for ($i=0; $i < $iMsgNum; $i++)
                echo $this->oStr->upperFirst(str_replace('-', ' ', $this->aMessages[$i])), '<br />';

            echo '\')});</script>';
        }

        unset($this->aMessages);
    }

    /**
     * Set an error message.
     *
     * @param string $sErr
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
        if ($this->oHttpRequest->getExists('err'))
            $this->aErrors[] = substr($this->oHttpRequest->get('err'),0,300);

        $iErrNum = count($this->aErrors);
        /*** Check if there are any errors in the aErrors array ***/
        if ($iErrNum > 0)
        {
           $this->staticFiles('js', PH7_STATIC . PH7_JS, 'jquery/apprise.js');

           echo '<script>$(function(){Apprise(\'';
           echo '<strong>', t('You have'), ' <em>', $iErrNum, '</em> ', nt('error:', 'errors:', $iErrNum), '</strong><br />';

           for ($i=0; $i < $iErrNum; $i++)
             echo $this->oStr->upperFirst(str_replace('-', ' ', $this->aErrors[$i])), '<br />';

           echo '\')});</script>';
        }

        unset($this->aErrors);
    }

   /**
    * Redirect Page using Refresh with Header.
    *
    * @param string $sUrl If NULL, the URL will be the current page. Default NULL
    * @param string $sMsg, Optional, display a message after redirect of the page.
    * @param string $sType Type of message: "success", "info", "warning" or "error". Default: "success".
    * @param integer $iTime Optional, a time. Default: "3" seconds.
    * @return void
    */
    public function setRedirect($sUrl = null, $sMsg = null, $sType = self::SUCCESS_TYPE, $iTime = 3)
    {
        if (!empty($sMsg)) {
            $this->setFlashMsg($sMsg, $sType);
        }

        $sUrl = (!empty($sUrl)) ? $sUrl : $this->oHttpRequest->currentUrl();

        header('Refresh: ' . intval($iTime) . '; URL=' . $this->oHttpRequest->pH7Url($sUrl));
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

        echo t('Queries time: %0% | %1% %2% | Generated in %3% | Memory allocated: %4%', Db::time(), $iCountQueries, $sRequest, $sTime, $iMemory);
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
        } elseif (\PH7\AdminCore::auth()) {
            $this->url(PH7_ADMIN_MOD, 'main', 'index');
        } elseif (\PH7\AffiliateCore::auth()) {
            $this->url('affiliate', 'account', 'index');
        } else {
            echo PH7_URL_ROOT;
        }
    }

    public function url($sModule, $sController, $sAction, $sVars = null, $bClear = true)
    {
        $sUrl = Uri::get($sModule, $sController, $sAction, $sVars, $bClear);
        echo Url::clean($sUrl); // For the URL parameters to avoid invalid HTML code
    }

    /**
     * Create a link of to display a popup confirmation for an action CRUD (http://en.wikipedia.org/wiki/Create,_read,_update_and_delete).
     *
     * @param string $sLabel
     * @param string $sMod
     * @param string $sCtrl
     * @param string $sAct
     * @param mixed (integer | string) $mId
     * @param string $sClass Add a CSS class. Default NULL
     * @return void HTML output.
     */
    public function popupLinkConfirm($sLabel, $sMod, $sCtrl, $sAct, $mId, $sClass = null)
    {
        $sClass = (!empty($sClass)) ? ' class="' . $sClass . '" ' : ' ';

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
     * @return void Output the Flag Icon Url.
     */
    public function getSmallFlagIcon($sCountryCode)
    {
        $sIcon = $this->oStr->lower($sCountryCode) . '.gif';
        $sDir = PH7_URL_STATIC . PH7_IMG . 'flag/s/';

        echo (is_file(PH7_PATH_STATIC . PH7_IMG . 'flag/s/' . $sIcon)) ? $sDir . $sIcon : $sDir . 'none.gif';
    }

     /**
     * Provide a "Powered By" link.
     *
     * @param boolean $bLink To include a link to pH7CMS or pH7Framework. Default TRUE
     * @param boolean $bSoftwareName Default TRUE
     * @param boolean $bVersion To include the version being used. Default TRUE
     * @param boolean $bComment HTML comment. Default TRUE
     * @param boolean $bEmail Is it for email content or not. Default FALSE
     * @return void
     */
    final public function link($bLink = true, $bSoftwareName = true, $bVersion = true, $bComment = true, $bEmail = false)
    {
        if (defined('PH7_VALID_LICENSE') && PH7_VALID_LICENSE)
            return;

        ($bLink ? $bSoftwareName = true : '');

        if (!$bEmail && \PH7\AdminCore::auth())
            echo '<p class="s_bMarg underline"><strong><em><a class="red" href="', Uri::get(PH7_ADMIN_MOD, 'setting', 'license'), '">', t('Need to remove the link below?'), '</a></em></strong><br /><em class="small">' . t('(... and get rid of all other promo notices)') . '</em></p>';

        if ($bComment)
        {
            echo '
            <!-- ', Kernel::SOFTWARE_COPYRIGHT, ' -->
            <!-- Powered by ', Kernel::SOFTWARE_NAME, ' ', Kernel::SOFTWARE_VERSION, ', Build ', Kernel::SOFTWARE_BUILD, ' -->
            <!-- You must leave this comment and the back link in the footer.
            This open source software is distributed free and you must respect the thousands of days, months and several years it takes to develop it!
            All rights reserved for ', Kernel::SOFTWARE_NAME, ', ', Kernel::SOFTWARE_COMPANY, '
            You can never claim that you took, developed, or helped in any other way in this software if it is wrong! -->';
        }

        echo ($bSoftwareName ?  '<p class="italic"><strong>' . t('Powered by') : ''), ' ', ($bLink ? '<a class="underline" href="' . Kernel::SOFTWARE_WEBSITE . '" title="' . Kernel::SOFTWARE_DESCRIPTION . '">' : ''), ($bSoftwareName ? Kernel::SOFTWARE_NAME : ''), ($bVersion ? ' ' . Kernel::SOFTWARE_VERSION : ''), ($bLink ? '</a>' : ''), ($bSoftwareName ? '</strong></p>' : ''),

        '<!-- "Powered by ', Kernel::SOFTWARE_NAME, ' ', Kernel::SOFTWARE_VERSION_NAME, ' ', Kernel::SOFTWARE_VERSION, ', Build ', Kernel::SOFTWARE_BUILD, ' -->';
    }

    /**
     * Provide a small "Powered By" link (e.g., for sitemap.xsl.tpl).
     *
     * @return void
     */
    final public function smallLink()
    {
        if (defined('PH7_VALID_LICENSE') && PH7_VALID_LICENSE)
            return;

        echo '<p><strong>', t('Proudly Powered by'), ' <a href="', Kernel::SOFTWARE_WEBSITE, '" title="', Kernel::SOFTWARE_DESCRIPTION, '">', Kernel::SOFTWARE_NAME, '</a> ', Kernel::SOFTWARE_VERSION, '</strong></p>';
    }

    /**
     * The below code MUST be present if you didn't pay a pH7CMS Pro License.
     *
     * @return string Returns relevant link based on the client browser's language.
     */
    final public function smartLink()
    {
        if (defined('PH7_VALID_LICENSE') && PH7_VALID_LICENSE)
            return;

        // Get Client's Language Code
        $sLangCode = (new \PH7\Framework\Navigation\Browser)->getLanguage(true);

        if ($sLangCode == 'en-ie') {
            $aSites = [
                ['title' => 'Dublin Dating Site', 'link' => 'http://dublin.meetlovelypeople.com'],
                ['title' => 'Meet Singles in Pubs/Bars', 'link' => 'http://dublin.meetlovelypeople.com']
            ];
        } elseif ($sLangCode == 'en-gb') {
          $aSites = [
              ['title' => 'London Dating App', 'link' => 'http://london.meetlovelypeople.com'],
              ['title' => 'Meet Singles in Pubs/Bars', 'link' => 'http://london.meetlovelypeople.com'],
              ['title' => 'Date Londoners', 'link' => 'http://flirt-dating.london']
          ];
        } elseif (substr($sLangCode,0,2) == 'fr') {
            $aSites = [
                ['title' => '1er Site de Rencontre Cool!', 'link' => 'http://coolonweb.com'],
                ['title' => 'Rencontre d\'un soir', 'link' => 'http://flirt-rencontre.net'],
                ['title' => ' Flirt Coquin', 'link' => 'http://flirt-rencontre.net'],
                ['title' => 'Rencontre à Paris Gratuite', 'link' => 'http://coolonweb.com']
            ];
        } else { // Default links, set to English
            $aSites = [
                ['title' => 'Flirt Hot Girls', 'link' => 'http://meetlovelypeople.com'],
                ['title' => 'Flirt Naughty & Girls', 'link' => 'http://meetlovelypeople.com'],
                ['title' => 'The MOBILE Dating App', 'link' => 'http://flirt-dating.london'],
                ['title' => 'Kik or Not', 'link' => 'http://kikornot.com'],
                ['title' => 'Dating App', 'link' => 'http://meetlovelypeople.com'],
                ['title' => 'Date People by Mobile App', 'link' => 'http://meetlovelypeople.com'],
                ['title' => 'Meet Amazing People', 'link' => 'http://coolonweb.com/p/dooba'],
                ['title' => 'Dating App for Dating Singles', 'link' => 'http://london-dating-app.meetlovelypeople.com'],
                ['title' => 'Android London Dating App', 'link' => 'https://play.google.com/store/apps/details?id=com.MLPLondon']
            ];
        }

        $iRand = mt_rand(0,count($aSites)-1);
        echo '<a href="', $aSites[$iRand]['link'], '">', $aSites[$iRand]['title'], '</a>';
    }

    final public function smartAppBanner(PH7Tpl $oView)
    {
        if (
            (!defined('PH7_VALID_LICENSE') || !PH7_VALID_LICENSE)
            && (new \PH7\AdminCoreModel)->getRootIp() !== Ip::get()
            && !\PH7\AdminCore::auth()
        ) {
            $sIOSBanner = '<meta name="apple-itunes-app" content="app-id=1155373742" />';

            if (empty($oView->header)) {
                $oView->header = $sIOSBanner;
            } else {
                $oView->header .= $sIOSBanner;
            }
        }
    }

    /**
     * @param string $sType (js or css).
     * @param string $sDir
     * @param string $sFiles
     * @param string $sCssMedia Only works for CSS files. The CSS Media type (e.g., screen,handheld,tv,projection). Default "all". Leave blank ('' or null) not to use the media attribute.
     * @return void
     */
    public function staticFiles($sType, $sDir, $sFiles, $sCssMedia = 'all')
    {
        if ($sType == 'js') {
            echo $this->externalJsFile(PH7_RELATIVE . 'asset/gzip/?t=js&amp;d=' . $sDir . '&amp;f=' . $sFiles);
        } else {
            echo $this->externalCssFile(PH7_RELATIVE . 'asset/gzip/?t=css&amp;d=' . $sDir . '&amp;f=' . $sFiles, $sCssMedia);
        }
    }

    /**
     * @param string $sDir The CSS folder.
     * @param string $sFiles The CSS files.
     * @param string $sCssMedia CSS Media type (e.g., screen,handheld,tv,projection). Default "all". Leave blank ('' or null) not to use the media attribute.
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
        for ($i = 0, $iCount = count($this->aCssDir); $i < $iCount; $i++)
            $this->staticFiles('css', $this->aCssDir[$i], $this->aCssFiles[$i], $this->aCssMedia[$i]);

        unset($this->aCssDir, $this->aCssFiles, $this->aCssMedia);
    }

    /**
     * @return void
     */
    public function js()
    {
        for ($i = 0, $iCount = count($this->aJsDir); $i < $iCount; $i++)
            $this->staticFiles('js', $this->aJsDir[$i], $this->aJsFiles[$i]);

        unset($this->aJsDir, $this->aJsFiles);
    }

    /**
     * Set flash message.
     *
     * @param string $sMessage
     * @param string $sType Type of message: "Design::SUCCESS_TYPE", "Design::INFO_TYPE", "Design::WARNING_TYPE" or "Design::ERROR_TYPE"
     * @return void
     */
    public function setFlashMsg($sMessage, $sType = self::SUCCESS_TYPE)
    {
        /*** Check the type of message, otherwise it is the default ***/
        $sType = in_array($sType, self::MESSAGE_TYPES) ? $sType : self::SUCCESS_TYPE;
        $sType = ($sType == self::ERROR_TYPE ? 'danger' : $sType); // Now the "error" CSS class has become "danger", so we have to convert it
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
     * @var string $html The message text with CSS layout depending on the type of message.
     */
    public function flashMsg()
    {
        $aFlashData = [
            self::FLASH_MSG,
            self::FLASH_TYPE
        ];

        if ($this->oSession->exists($aFlashData))
        {
            echo '<div class="center bold alert alert-', $this->oSession->get(self::FLASH_TYPE), '" role="alert">', $this->oSession->get(self::FLASH_MSG), '</div>';

            $this->oSession->remove($aFlashData);
        }
    }

    /**
     * Show the user IP address with a link to get the IP information.
     *
     * @internal If it's an IPv6, show only the beginning, otherwise it would be too long in the template.
     * @param string $sIp Allows to speciy another IP address than the client one.
     * @param boolean $bPrint Print or Return the HTML code. Default TRUE
     * @return mixed (string | void)
     */
    public function ip($sIp = null, $bPrint = true)
    {
        $sIp = Ip::get($sIp);
        $sHtml = '<a href="' . Ip::api($sIp) . '" title="' . t('See info of this IP, %0%', $sIp) . '" target="_blank">' . $this->oStr->extract($sIp,0,15) . '</a>';

        if ($bPrint)
            echo $sHtml;
        else
            return $sHtml;
    }

    /**
     * Show the geolocation of the user (with link that points to the Country controller).
     *
     * @param boolean $bPrint Print or Return the HTML code. Default TRUE
     * @return mixed (string | void)
     */
    public function geoIp($bPrint = true)
    {
        $sCountry = Geo::getCountry();
        $sCountryCode = Country::fixCode(Geo::getCountryCode());
        $sCountryLang = t($sCountryCode); // Country name translated into the user language
        $sCity = Geo::getCity();

        $sHtml = '<a href="' . Uri::get('user', 'country', 'index', $sCountry . PH7_SH . $sCity) . '" title="' . t('Meet New People in %0%, %1% with %site_name%!', $sCountryLang, $sCity) . '">' . $sCountryLang . ', ' . $sCity . '</a>';

        if ($bPrint)
            echo $sHtml;
        else
            return $sHtml;
    }

    /**
     * Pagination.
     *
     * @param integer $iTotalPages
     * @param integer  $iCurrentPage
     * @return void The HTML pagination code.
     */
    public function pagination($iTotalPages, $iCurrentPage)
    {
        echo (new \PH7\Framework\Navigation\Pagination($iTotalPages, $iCurrentPage))->getHtmlCode();
    }

    /**
     * Get the User Avatar.
     *
     * @param string $sUername
     * @param string $sSex
     * @param integer $iSize
     * @return void Html contents. URL avatar default 150px or the user avatar.
     */
    public function getUserAvatar($sUsername, $sSex = '', $iSize = '')
    {
        $oCache = (new \PH7\Framework\Cache\Cache)->start(self::CACHE_AVATAR_GROUP . $sUsername, $sSex . $iSize, 3600);

        if (!$sUrl = $oCache->get())
        {
            $oUserModel = new \PH7\UserCoreModel;

            $iProfileId = $oUserModel->getId(null, $sUsername);
            $oGetAvatar = $oUserModel->getAvatar($iProfileId);

            $sSize = ($iSize == '32' || $iSize == '64' || $iSize == '100' || $iSize == '150' || $iSize == '200' || $iSize == '400') ? '-' . $iSize : '';

            $sAvatar = @$oGetAvatar->pic;
            $sExt = PH7_DOT . (new File)->getFileExt($sAvatar);

            $sDir = 'user/avatar/img/' . $sUsername . PH7_SH;
            $sPath = PH7_PATH_PUBLIC_DATA_SYS_MOD . $sDir . $sAvatar;
            $sUrl = PH7_URL_DATA_SYS_MOD . $sDir . str_replace($sExt, $sSize . $sExt, $sAvatar);

            $bIsModerate = (Registry::getInstance()->module === PH7_ADMIN_MOD);

            if (!is_file($sPath) || $oGetAvatar->approvedAvatar == '0')
            {
                /* If sex is empty, it is recovered in the database using information from member */
                $sSex = (!empty($sSex)) ? $sSex : $oUserModel->getSex(null, $sUsername, 'Members');
                $sSex = $this->oStr->lower($sSex);
                $sIcon = ($sSex == 'male' || $sSex == 'female' || $sSex == 'couple' || $sSex == PH7_ADMIN_USERNAME) ? $sSex : 'visitor';
                $sUrlTplName = (defined('PH7_TPL_NAME')) ? PH7_TPL_NAME : PH7_DEFAULT_THEME;

                /*** If the user doesn't have an avatar ***/
                if (!is_file($sPath))
                {
                    /* The user has no avatar, we try to get a Gravatar */

                    // Get the User Email
                    $sEmail = $oUserModel->getEmail($iProfileId);

                    $bSecuredGravatar = \PH7\Framework\Http\Http::isSsl();
                    $sUrl = $this->getGravatarUrl($sEmail, '404', $iSize, 'g', $bSecuredGravatar);

                    if (!(new \PH7\Framework\Security\Validate\Validate)->url($sUrl, true))
                    {
                        // If there is no Gravatar, we set the default pH7CMS's avatar
                        $sUrl = PH7_URL_TPL . $sUrlTplName . PH7_SH . PH7_IMG . 'icon/' . $sIcon . '_no_picture' . $sSize . self::AVATAR_IMG_EXT;
                    }

                }
                elseif (!$bIsModerate) // We do not display the pending approval image when an administrator is on the panel admin
                {
                    $sUrl = PH7_URL_TPL . $sUrlTplName . PH7_SH . PH7_IMG . 'icon/pending' . $sSize . self::AVATAR_IMG_EXT;
                }
            }

            unset($oUserModel);
            /**
             * @internal Clean URL for parameters in Gravatar URL to make the HTML code valid.
             * If we set replace '&' by '&amp;' before checking the 404's Gravatar URL, it will always return '200 OK', that's why we need to clean the URL now.
             */
            $oCache->put( Url::clean($sUrl) );
        }

        unset($oCache);
        echo $sUrl;
    }

    /**
     * Get the user profile link.
     *
     * @param string $sUsername
     * @param boolean $bPrint Print or Return the HTML code.
     *
     * @return string The absolute user profile link.
     */
    public function getProfileLink($sUsername, $bPrint = true)
    {
        $sHtml = '<a href="';
        $sHtml .= (new UserCore)->getProfileLink($sUsername);
        $sHtml .= '" title="' . t("%0%'s profile", $sUsername) . '">' . $sUsername . '</a>';

        if ($bPrint)
            echo $sHtml;
        else
            return $sHtml;
    }

    /**
     * Get the Gravatar URL.
     *
     * @param string $sEmail The user email address.
     * @param string $sType The default image type to show. Default: 'wavatar'
     * @param integer $iSize  The size of the image. Default: 80
     * @param character $cRating The max image rating allowed. Default: 'g' (for all)
     * @param boolean $bSecure Display avatar via HTTPS, for example if the site uses HTTPS, you should use this option to not get a warning with most Web browsers. Default: FALSE
     * @return string The Gravatar Link.
     */
    public function getGravatarUrl($sEmail, $sType = 'wavatar', $iSize = 80, $cRating = 'g', $bSecure = false)
    {
        $sProtocol = ($bSecure) ? 'https' : 'http';
        $bSubDomain = ($bSecure) ? 'secure' : 'www';
        return $sProtocol . '://' . $bSubDomain . '.gravatar.com/avatar/' . md5( strtolower($sEmail) ) . '?d=' . $sType . '&s=' . $iSize . '&r=' . $cRating;
    }

    /**
     * Get favicon from a URL.
     *
     * @param string $sUrl
     * @return void The HTML favicon image.
     */
    public function favicon($sUrl)
    {
        $sImg = \PH7\Framework\Navigation\Browser::favicon($sUrl);
        $sName = \PH7\Framework\Http\Http::getHostName($sUrl);

        $this->imgTag($sImg, $sName, ['width'=>16, 'height'=>16]);
    }

    /**
     * Like Link.
     *
     * @param string $sUsername Username of member.
     * @param string $sFirstName First name of member.
     * @param string $sSex Sex of member.
     * @param string $sForceUrlKey Specify a specific URL from the like. Default NULL (current URL).
     * @return void
     */
    public function like($sUsername, $sFirstName, $sSex, $sForceUrlKey = null)
    {
        $aHttpParams = [
            'msg' => t('Please join for free to vote that'),
            'ref' => $this->oHttpRequest->currentController(),
            'a' => 'like',
            'u' => $sUsername,
            'f_n' => $sFirstName,
            's' => $sSex
        ];

        $bIsLogged = UserCore::auth();
        $sLikeLink = ($bIsLogged) ? '#' : Uri::get('user', 'signup', 'step1', '?' . Url::httpBuildQuery($aHttpParams), false);
        $sLikeId = ($bIsLogged) ? ' id="like"' : '';

        $sUrlKey = (empty($sForceUrlKey)) ? $this->oHttpRequest->currentUrl() : $sForceUrlKey;
        echo '<a rel="nofollow" href="', $sLikeLink, '" data-key="', $sUrlKey, '" title="', t('Like %0%', $sFirstName), '" class="like"', $sLikeId, '>', t('Like %0%', $sFirstName), '</a>';
        $this->staticFiles('js', PH7_STATIC . PH7_JS, 'Like.js');
    }

    /**
     * Add Normal size Social Media Widgets.
     *
     * @internal AddThis JS file will be included through 'pH7_StaticFiles' table.
     * @return void HTML output.
     */
    public function likeApi()
    {
        if ((bool) DbConfig::getSetting('socialMediaWidgets'))
            echo  '<br /><br /><div class="center addthis_toolbox addthis_default_style"><a class="addthis_button_facebook_like"></a><a class="addthis_button_tweet" tw:count="horizontal"></a><a class="addthis_button_google_plusone" g:plusone:size="medium"></a><a class="addthis_counter addthis_pill_style"></a></div>';
    }

    /**
     * Add Small size Social Media Widgets.
     *
     * @internal AddThis JS file will be included through 'pH7_StaticFiles' table.
     * @return void HTML output.
     */
    public function littleLikeApi()
    {
        if ((bool) DbConfig::getSetting('socialMediaWidgets'))
            echo  '<div class="addthis_toolbox addthis_default_style"><a class="addthis_button_facebook_like"></a><a class="addthis_button_google_plusone" g:plusone:size="medium"></a><a class="addthis_button_tweet" tw:count="horizontal"></a></div>';
    }

    /**
     * Generate a Report Link.
     *
     * @param integer $iId
     * @param string $sUsername
     * @param string $sFirstName
     * @param string $sSex
     * @internal We do not use \PH7\Framework\Url\Url::httpBuildQuery() method for the first condition otherwise the URL is distorted and it doesn't work.
     * @return void
     */
    public function report($iId, $sUsername, $sFirstName, $sSex)
    {
        $sReportLink = (UserCore::auth()) ?
            Uri::get('report', 'main', 'abuse', '?spammer=' . $iId . '&amp;url=' . $this->oHttpRequest->currentUrl() . '&amp;type=' . Registry::getInstance()->module, false) . '" data-popup="block-page' :
            Uri::get('user', 'signup', 'step1', '?' . Url::httpBuildQuery(['msg' => t('You must be registered to report contents.'), 'ref' => 'profile', 'a' => 'report', 'u' => $sUsername, 'f_n' => $sFirstName, 's' => $sSex]), false);

        echo '<a rel="nofollow" href="', $sReportLink, '" title="', t('Report Abuse'), '">', t('Report'), '</a>';
    }

    /**
     * Generate a Link tag.
     *
     * @param string $sLink The link.
     * @param boolean $bNoFollow Set TRUE to set the link "nofollow", FALSE otherwise. Default TRUE
     * @return void The HTML link tag.
     */
    public function urlTag($sLink, $bNoFollow = true)
    {
        $sLinkName = \PH7\Framework\Parse\Url::name($sLink);
        $aDefAttrs = ['href' => $sLink, 'title' => $sLinkName];

        if ($bNoFollow)
            $aDefAttrs += ['rel' => 'nofollow']; // Add "nofollow" attribute if "$bNoFollow" is TURE

        $this->htmlTag('a', $aDefAttrs, true, $sLinkName);
    }

    /**
     * Generate a IMG tag.
     *
     * @param string $sImg The image.
     * @param string $sAlt Alternate text.
     * @param array $aAttrs Optional. Array containing the "name" and "value" HTML attributes. Default NULL
     * @return void The HTML image tag.
     */
    public function imgTag($sImg, $sAlt, array $aAttrs = null)
    {
        $aDefAttrs = ['src' => $sImg, 'alt' => $sAlt];

        if (!empty($aAttrs)) {
            $aDefAttrs += $aAttrs; // Update the attributes if necessary
        }

        $this->htmlTag('img',  $aDefAttrs);
    }

    /**
     * Generate any HTML tag.
     *
     * @param string $sTag
     * @param array $aAttrs Optional. Default NULL
     * @param boolean $bPair Optional. Default FALSE
     * @param string $sText Optional. Add text, available only for pair tag. Default NULL
     * @return string The custom HTML tag.
     */
    public function htmlTag($sTag, array $aAttrs = null, $bPair = false, $sText = null)
    {
        $sAttrs = '';

        if (!empty($aAttrs)) {
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
     * @param array $aMeta Default NULL
     * @param boolean $bLogo Default FALSE
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
        echo '<meta name="author" content="', Kernel::SOFTWARE_COMPANY, '" />
        <meta name="copyright" content="', Kernel::SOFTWARE_COPYRIGHT, '" />
        <meta name="creator" content="', Kernel::SOFTWARE_NAME, '" />
        <meta name="designer" content="', Kernel::SOFTWARE_NAME, '" />
        <meta name="generator" content="', Kernel::SOFTWARE_NAME, ' ', Kernel::SOFTWARE_VERSION_NAME, ' ', Kernel::SOFTWARE_VERSION, ', Build ', Kernel::SOFTWARE_BUILD, '" />';
        $this->externalCssFile(PH7_URL_STATIC. PH7_CSS . 'js/jquery/smoothness/jquery-ui.css');
        $this->staticFiles('css', PH7_LAYOUT . PH7_TPL . PH7_DEFAULT_THEME . PH7_SH . PH7_CSS, 'common.css,style.css,form.css');
        $this->externalJsFile(PH7_URL_STATIC . PH7_JS . 'jquery/jquery.js');
        $this->externalJsFile(PH7_URL_STATIC . PH7_JS . 'jquery/jquery-ui.js');
        echo '<script>var pH7Url={base:\'', PH7_URL_ROOT, '\'}</script></head><body>';
        if ($bLogo) {
            // Website's name
            $sSiteName = Registry::getInstance()->site_name;

            // Check if the website's name exists, otherwise we displayed the software's name
            $sName = (!empty($sSiteName)) ? $sSiteName : Kernel::SOFTWARE_NAME;

            echo '<header>
            <div role="banner" id="logo"><h1><a href="', PH7_URL_ROOT, '" title="', $sName, ' — ', Kernel::SOFTWARE_NAME, ', ', Kernel::SOFTWARE_COMPANY, '">', $sName, '</a></h1></div>
            </header>';
        }
        echo $this->flashMsg(),
        '<div class="msg"></div><div class="m_marg">';
    }

    public function htmlFooter()
    {
        if ($this->bIsDiv)
            echo '</div>';

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
     * @return void HTML link tag.
     */
    public function externalCssFile($sFile, $sCssMedia = null)
    {
        $sCssMedia = (!empty($sCssMedia)) ? ' media="' . $sCssMedia . '"' : '';
        echo '<link rel="stylesheet" href="', $sFile, '"', $sCssMedia, ' />';
    }

    /**
     * Get an external JS file.
     *
     * @param string $sFile JS file.
     * @return void HTML script tag.
     */
    public function externalJsFile($sFile)
    {
        echo '<script src="', $sFile, '"></script>';
    }

    public function __destruct()
    {
        unset(
          $this->bIsDiv,
          $this->oStr,
          $this->oSession,
          $this->oHttpRequest
        );
    }
}
