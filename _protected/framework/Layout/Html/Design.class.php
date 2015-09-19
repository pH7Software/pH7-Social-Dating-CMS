<?php
/**
 * @title            Design Class
 * @desc             File containing HTML for display management.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license          CC-BY License - http://creativecommons.org/licenses/by/3.0/
 * @package          PH7 / Framework / Layout / Html
 * @version          1.6
 */

namespace PH7\Framework\Layout\Html;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Core\Kernel,
PH7\Framework\Registry\Registry,
PH7\Framework\Mvc\Model\Engine\Db,
PH7\Framework\Url\Url,
PH7\Framework\Ip\Ip,
PH7\Framework\Geo\Ip\Geo,
PH7\Framework\Str\Str,
PH7\Framework\File\File,
PH7\Framework\Session\Session,
PH7\Framework\Navigation\Page,
PH7\Framework\Mvc\Request\Http,
PH7\Framework\Mvc\Router\Uri;

class Design
{

    const
    CACHE_GROUP = 'str/design',
    CACHE_AVATAR_GROUP = 'str/design/avatar/'; // We put a slash for after creating a directory for each username

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
            if ($sLang === PH7_LANG_NAME) continue;

                // Retrieve only the first two characters
                $sAbbrLang = substr($sLang,0,2);

                echo '<a href="', $sCurrentPage, $sLang, '" hreflang="', $sAbbrLang, '"><img src="', PH7_URL_STATIC, PH7_IMG, 'flag/s/', $sAbbrLang, '.gif" alt="', t($sAbbrLang), '" title="', t($sAbbrLang), '" /></a>&nbsp;';
        }

        unset($aLangs, $sCurrentPage);
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
    public function setRedirect($sUrl = null, $sMsg = null, $sType = 'success', $iTime = 3)
    {
        if ($sMsg)
            $this->setFlashMsg($sMsg, $sType);

        $sUrl = (!empty($sUrl)) ? $sUrl : $this->oHttpRequest->currentUrl();

        header('Refresh: ' . intval($iTime) . '; URL=' . $this->oHttpRequest->pH7Url($sUrl));
    }

    public function stat()
    {
        $iCountQueries = Db::queryCount();
        $sRequest = nt('Request', 'Requests', $iCountQueries);
        echo t('Time of the request: %0% | %1% %2% | Page executed in %3% seconds | Amount of memory allocated: %4%', Db::time(), $iCountQueries, $sRequest, Page::time(Registry::getInstance()->start_time, microtime(true)), memory_get_usage(true));
    }

    public function url($sModule, $sController, $sAction, $sVars = null, $bClear = true)
    {
        $sUrl = Uri::get($sModule, $sController, $sAction, $sVars, $bClear);
        echo Url::clean($sUrl); // For the parameters in the URL are valid in HTML
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
        if (defined('PH7_VALID_LICENSE') && PH7_VALID_LICENSE) return;

        ($bLink ? $bSoftwareName = true : '');

        if (!$bEmail && \PH7\AdminCore::auth())
            echo '<p class="underline"><strong><em><a class="red" href="', Uri::get(PH7_ADMIN_MOD, 'setting', 'license'), '">', t('Want to delete this below link?'), '</a></em></strong></p>';

        if ($bComment)
        {
            echo '
            <!-- ', Kernel::SOFTWARE_COPYRIGHT, ' -->
            <!-- Powered by ', Kernel::SOFTWARE_NAME, ' ', Kernel::SOFTWARE_VERSION, ', Build ', Kernel::SOFTWARE_BUILD, ' -->
            <!-- You must leave this comment and the back link in the footer.
            This open source software is distributed free and you must respect the thousands of days, months and years it takes to develop it!
            All rights reserved for ', Kernel::SOFTWARE_NAME, ', ', Kernel::SOFTWARE_COMPANY, '
            You can never claim that you took, developed, or helped in any other way in this software if it is wrong! -->';
        }

        echo ($bSoftwareName ?  '<p><strong>' . t('Powered By') : ''), ' ', ($bLink ? '<a href="' . Kernel::SOFTWARE_WEBSITE . '" title="' . Kernel::SOFTWARE_DESCRIPTION . '">' : ''), ($bSoftwareName ? Kernel::SOFTWARE_NAME : ''), ($bLink ? '</a> ' : ' '), ($bVersion ? Kernel::SOFTWARE_VERSION : ''), ($bSoftwareName ? '</strong></p>' : ''),

        '<!-- "Powered by ', Kernel::SOFTWARE_NAME, ' ', Kernel::SOFTWARE_VERSION_NAME, ' ', Kernel::SOFTWARE_VERSION, ', Build ', Kernel::SOFTWARE_BUILD, ' -->';
    }

    /**
     * Provide a small "Powered By" link.
     *
     * @return void
     */
    final public function smallLink()
    {
        echo '<p><strong>', t('Powered By'), ' <a href="', Kernel::SOFTWARE_WEBSITE, '" title="', Kernel::SOFTWARE_DESCRIPTION, '">', Kernel::SOFTWARE_NAME, '</a> ', Kernel::SOFTWARE_VERSION, '</strong></p>';
    }

    /**
     * The below code MUST be present if you didn't pay a pH7CMS Pro License.
     *
     * @return string Returns relevant link based on the client browser's language.
     */
    final public function smartLink()
    {
        $sLangCode = (new \PH7\Framework\Navigation\Browser)->getLanguage(true); // Get Client's Language Code

        if ($sLangCode == 'en-ie') {
            $iRand = 0;
            $aData = [
                ['title' => 'Dating Site in Dublin', 'link' => 'http://dublin.meetlovelypeople.com']
            ];
        } elseif (substr($sLangCode,0,2) == 'fr') {
            $iRand = mt_rand(0,2);
            $aData = [
                ['title' => '1er Site de Rencontre Cool', 'link' => 'http://coolonweb.com'],
                ['title' => 'Échanges Linguistiques en Ligne', 'link' => 'http://newayup.com'],
                ['title' => 'Site de Tchat 100% Gratuit', 'link' => 'http://01tchat.com'],
                ['title' => 'Rencontre &amp; Flirt', 'link' => 'http://flirt-rencontre.net']
            ];
        } else { // Default links, set to English
            $iRand = mt_rand(0,3);
            $aData = [
                ['title' => 'Date Lovely People', 'link' => 'http://meetlovelypeople.com'],
                ['title' => 'Friend New Fun Date', 'link' => 'http://sofun.co'],
                ['title' => 'Flirt Hot People', 'link' => 'http://flirtme.biz'],
                ['title' => 'Swingers Dating Site', 'link' => 'http://swinger.flirtme.biz'],
                ['title' => 'Learn Languages Online', 'link' => 'http://newayup.com']
            ];
        }

        echo '<a href="', $aData[$iRand]['link'], '">', $aData[$iRand]['title'], '</a>';
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
        if ($sType == 'js')
            echo $this->externalJsFile(PH7_RELATIVE . 'asset/gzip/?t=js&amp;d=' . $sDir . '&amp;f=' . $sFiles);
        else
            echo $this->externalCssFile(PH7_RELATIVE . 'asset/gzip/?t=css&amp;d=' . $sDir . '&amp;f=' . $sFiles, $sCssMedia);
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
     * @param string $sType Type of message: "success", "info", "warning" or "error". Default: "success".
     * @return void
     */
    public function setFlashMsg($sMessage, $sType = '')
    {
        /*** Check the type of message, otherwise it is the default ***/
        $sType = ($sType == 'success' || $sType == 'info' || $sType == 'warning' || $sType == 'error') ? $sType : 'success';
        $sType = ($sType == 'error' ? 'danger' : $sType); // Now the "error" CSS class has become "danger", so we have to convert it
        $this->oSession->set(
            [
                'flash_msg'=> $sMessage,
                'flash_type'=> $sType
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
        if ($this->oSession->exists('flash_msg'))
        {
            echo '<div class="center bold alert alert-', $this->oSession->get('flash_type'), '" role="alert">', $this->oSession->get('flash_msg'), '</div>';

            $this->oSession->remove('flash_msg'); // Remove the flash_msg session
        }
    }

    /**
     * Show the user IP address with a link to get the IP information.
     *
     * @param string $sIp IP address. Default NULL
     * @param boolean $bPrint Print or Return the HTML code. Default TRUE
     * @return mixed (string | void)
     */
    public function ip($sIp = null, $bPrint = true)
    {
        $sHtml = '<a href="' . Ip::api($sIp) . '" title="' . t('See information from this IP') . '" target="_blank">' . Ip::get($sIp) . '</a>';

        if ($bPrint)
            echo $sHtml;
        else
            return $sHtml;
    }

    /**
     * Show the geolocation of the user.
     *
     * @param boolean $bPrint Print or Return the HTML code. Default TRUE
     * @return mixed (string | void)
     */
    public function geoIp($bPrint = true)
    {
        $sCountry = Geo::getCountry();
        $sCountryLang = t(str_replace('GB', 'UK', Geo::getCountryCode())); // Country name translated into the user language
        $sCity = Geo::getCity();

        $sHtml = '<a href="' . Uri::get('user', 'country', 'index', $sCountry . PH7_SH . $sCity) . '" title="' . t('Meet New People on %0%, %1% with %site_name%!', $sCountryLang, $sCity) . '">' . $sCountryLang . ', ' . $sCity . '</a>';

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
        $oCache = (new \PH7\Framework\Cache\Cache)->start(self::CACHE_AVATAR_GROUP . $sUsername, $sSex . $iSize, 60*24*30);

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

                /*** If the user does not have an avatar ***/
                if (!is_file($sPath))
                {
                   // The user has no avatar, we try to get her Gravatar.

                    // Get the User Email
                    $sEmail = $oUserModel->getEmail($iProfileId);

                    $bSecureGravatar = \PH7\Framework\Http\Http::isSsl();
                    $sUrl = $this->getGravatarUrl($sEmail, '404', $iSize, 'g', $bSecureGravatar);

                    if (!(new \PH7\Framework\Security\Validate\Validate)->url($sUrl, true))
                       // Our Default Image
                        $sUrl = PH7_URL_TPL . $sUrlTplName . PH7_SH . PH7_IMG . 'icon/' . $sIcon . '_no_picture' . $sSize . '.jpg';

                }
                elseif (!$bIsModerate) // We do not display the pending approval image when an administrator is on the panel admin
                {
                    $sUrl = PH7_URL_TPL . $sUrlTplName . PH7_SH . PH7_IMG . 'icon/pending' . $sSize . '.jpg';
                }
            }

            unset($oUserModel);
            $oCache->put($sUrl);
        }

        unset($oCache);
        echo $sUrl;
    }


    /**
     * Get the Gravatar URL.
     *
     * @param string $sEmail The user email address.
     * @param string $sType The default image type to show. Default wavatar
     * @param integer $iSize  The size of the image. Default 80
     * @param character $cRating The max image rating allowed. Default G (for all)
     * @param boolean $bSecure Display avatar via HTTPS, for example if the site uses HTTPS, you should use this option to not get a warning with most Web browsers. Default FALSE
     * @return string The Link Avatar.
     */
    public function getGravatarUrl($sEmail, $sType = 'wavatar', $iSize = 80, $cRating = 'g', $bSecure = false)
    {
        $sProtocol = ($bSecure) ? 'https' : 'http';
        $bSubDomain = ($bSecure) ? 'secure' : 'www';
        return $sProtocol . '://' . $bSubDomain . '.gravatar.com/avatar/' . md5( strtolower($sEmail) ) . '?d=' . $sType . '&amp;s=' . $iSize . '&amp;r=' . $cRating;
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
            'msg' => t('Free registration for the feature Like.'),
            'ref' => $this->oHttpRequest->currentController(),
            'a' => 'like',
            'u' => $sUsername,
            'f_n' => $sFirstName,
            's' => $sSex
        ];

        $sLikeLink = (\PH7\UserCore::auth()) ? '#' : Uri::get('user', 'signup', 'step1', '?' . Url::httpBuildQuery($aHttpParams), false);

        $sUrlKey = (empty($sForceUrlKey)) ? $this->oHttpRequest->currentUrl() : $sForceUrlKey;
        echo '<a rel="nofollow" href="', $sLikeLink, '" data-key="', $sUrlKey, '" title="', t('Like %0%', $sFirstName), '" class="like">', t('Like %0%', $sFirstName), '</a>';
        $this->staticFiles('js', PH7_STATIC . PH7_JS, 'Like.js');
    }

    /**
     * Add Normal size Media Social Widgets.
     *
     * @param boolean $bDisable Disable or Enable it.
     * AddThis JS file makes the site very slow (and nowadays social widgets like those are a bit old fashioned). So it's by default deactivated and the JS file in the database as well -> 'pH7_StaticFiles' table.
     *
     * @return void HTML output.
     */
    public function likeApi($bDisable = true)
    {
        if ($bDisable === false)
            echo  '<br /><br /><div class="center addthis_toolbox addthis_default_style"><a class="addthis_button_facebook_like"></a><a class="addthis_button_tweet" tw:count="horizontal"></a><a class="addthis_button_google_plusone" g:plusone:size="medium"></a><a class="addthis_counter addthis_pill_style"></a></div>';
    }

    /**
     * Add Small size Media Social Widgets.
     *
     * @param boolean $bDisable Disable or Enable it.
     * AddThis JS file makes the site very slow (and nowadays social widgets like those are a bit old fashioned). So it's by default deactivated and the JS file in the database as well -> 'pH7_StaticFiles' table.
     *
     * @return void HTML output.
     */
    public function littleLikeApi($bDisable = true)
    {
        if ($bDisable === false)
            echo  '<div class="addthis_toolbox addthis_default_style"><a class="addthis_button_facebook_like"></a><a class="addthis_button_google_plusone" g:plusone:size="medium"></a><a class="addthis_button_tweet" tw:count="horizontal"></a></div>';
    }

    /**
     * Generate a Report Link.
     *
     * @param integer $iId
     * @param string $sUsername
     * @param string $sFirstName
     * @param string $sSex
     * @internal We do not use \PH7\Framework\Url\Url::httpBuildQuery() method for the first condition otherwise the URL is distorted and it does not work.
     * @return void
     */
    public function report($iId, $sUsername, $sFirstName, $sSex)
    {
        $sReportLink = (\PH7\UserCore::auth()) ?
            Uri::get('report', 'main', 'abuse', '?spammer=' . $iId . '&amp;url=' . $this->oHttpRequest->currentUrl() . '&amp;type=' . Registry::getInstance()->module, false) . '" data-popup="block-page' :
            Uri::get('user', 'signup', 'step1', '?' . Url::httpBuildQuery(['msg' => t('You must register to report this person.'), 'ref' => 'profile', 'a' => 'report', 'u' => $sUsername, 'f_n' => $sFirstName, 's' => $sSex]), false);

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

        if (!empty($aAttrs))
            $aDefAttrs += $aAttrs; // Update the attributes if necessary

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

        if (!empty($aAttrs))
        {
            foreach ($aAttrs as $sName => $sValue)
                $sAttrs .= ' ' . $sName . '="' . $sValue . '"';
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
        <title>', (!empty($aMeta['title']) ? $aMeta['title'] : ''), '</title>';
        if (!empty($aMeta['description'])) echo '<meta name="description" content="', $aMeta['description'], '" />';
        if (!empty($aMeta['keywords'])) echo '<meta name="keywords" content="', $aMeta['keywords'], '" />';
        echo '<meta name="author" content="', Kernel::SOFTWARE_COMPANY, '" />
        <meta name="copyright" content="', Kernel::SOFTWARE_COPYRIGHT, '" />
        <meta name="creator" content="', Kernel::SOFTWARE_NAME, '" />
        <meta name="designer" content="', Kernel::SOFTWARE_NAME, '" />
        <meta name="generator" content="', Kernel::SOFTWARE_NAME, ' ', Kernel::SOFTWARE_VERSION_NAME, ' ', Kernel::SOFTWARE_VERSION, ', Build ', Kernel::SOFTWARE_BUILD, '" />
        <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />',
        $this->staticFiles('css', PH7_LAYOUT . PH7_TPL . PH7_DEFAULT_THEME . PH7_SH . PH7_CSS, 'common.css,style.css,form.css'),
        '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
        <script>var pH7Url={base:\'', PH7_URL_ROOT, '\'}</script></head><body>';
        if ($bLogo)
        {
            echo '<header>
            <div id="logo"><h1><a href="', PH7_URL_ROOT, '" title="', Kernel::SOFTWARE_NAME, ', ', Kernel::SOFTWARE_COMPANY, '">', Kernel::SOFTWARE_NAME, '</a></h1></div>
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
     * The XML tag does not work in PHP files since it is the same "<?"
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
