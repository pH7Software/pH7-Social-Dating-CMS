<?php
/**
 * @title            Core Controller Class
 * @desc             Base class for controllers.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2011-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Controller
 * @version          2.0
 * @link             http://pierrehenry.be
 */

namespace PH7\Framework\Mvc\Controller;

defined('PH7') or exit('Restricted access');

use PH7\AdminCore;
use PH7\AffiliateCore;
use PH7\Framework\Core\Core;
use PH7\Framework\Geo\Ip\Geo;
use PH7\Framework\Geo\Misc\Country;
use PH7\Framework\Http\Http;
use PH7\Framework\Ip\Ip;
use PH7\Framework\Mobile\MobApp;
use PH7\Framework\Module\Various as SysMod;
use PH7\Framework\Mvc\Model as M;
use PH7\Framework\Mvc\Model\BlockCountry as BlockCountryModel;
use PH7\Framework\Mvc\Router\FrontController;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Page\Page;
use PH7\Framework\Security\Ban\Ban;
use PH7\Framework\Security\DDoS\Stop as DDoSStoper;
use PH7\FriendCoreModel;
use PH7\MailCoreModel;
use PH7\UserCore;
use Teapot\StatusCode;

abstract class Controller extends Core implements Controllable
{
    const CORE_MAIN_MODULE = 'user';
    const MAINTENANCE_DURATION_SECONDS = 3600;

    public function __construct()
    {
        parent::__construct();

        $this->ddosProtection();

        $this->assignSiteInfoRegistryVars();
        $this->assignGlobalTplVars();
        $this->view->setTemplateDir($this->registry->path_module_views . PH7_TPL_MOD_NAME);

        $this->checkPerms();
        $this->checkModStatus();
        $this->checkBanStatus();
        $this->checkCountryBlacklist();
        $this->checkSiteStatus();
    }

    /**
     * {@inheritdoc}
     */
    final public function output($sFile = null)
    {
        /**
         * Remove database information for the tpl files in order to prevent any attack attempt.
         **/
        FrontController::getInstance()->_unsetDatabaseInfo();

        /**
         * Destroy all object instances of PDO and close the connection to the database before the display and the start of the template and free memory
         */
        M\Engine\Db::free();

        /**
         * Output our template and encoding.
         */

        $sFile = !empty($sFile) ? $sFile : $this->view->getMainPage();

        // header('Content-type: text/html; charset=' . PH7_ENCODING);
        $this->view->display($sFile, PH7_PATH_TPL . PH7_TPL_NAME . PH7_DS);
        unset($this->view); // Clean Template Data
    }

    /**
     * Includes a template file in the main layout.
     * Note: For viewing you need to use self::output() method.
     *
     * @param string $sFile
     *
     * @return void
     */
    final public function manualTplInclude($sFile)
    {
        $this->view->manual_include = $sFile;
    }

    /**
     * {@inheritdoc}
     */
    public function displayPageNotFound($sMsg = '', $b404Status = true)
    {
        if ($b404Status) {
            Http::setHeadersByCode(StatusCode::NOT_FOUND);
        }

        $this->view->page_title = !empty($sMsg) ? t('%0% - Page Not Found', $sMsg) : t('Page Not Found');
        $this->view->h1_title = !empty($sMsg) ? $sMsg : t('Whoops! The page you requested was not found.');

        $sErrorDesc = t('You may have clicked an expired link or mistyped the address. Some web addresses are case sensitive.') .
            '<br /><strong><em>' . t('Suggestions:') .
            '</em></strong><br /><a href="' . $this->registry->site_url . '">' . t('Return home') . '</a><br />';

        if (!UserCore::auth()) {
            $sErrorDesc .=
                '<a href="' . Uri::get('user', 'signup', 'step1') . '">' . t('Join Now') . '</a><br />
                <a href="' . Uri::get('user', 'main', 'login') . '">' . t('Login') . '</a><br />';
        }

        $sErrorDesc .= '<a href="javascript:history.back();">' . t('Go back to the previous page') . '</a><br />';

        $this->view->error_desc = $sErrorDesc;

        $this->view->pOH_not_found = 1;
        $this->output();
        exit;
    }

    /**
     * Set an Access Denied page.
     *
     * @param bool $b403Status Set the Forbidden status. For the Ajax blocks and others, we cannot put the HTTP 403 error code, so the attribute must be set to FALSE. Default TRUE
     *
     * @return void Quits the page with the exit() function
     */
    public function displayPageDenied($b403Status = true)
    {
        if ($b403Status) {
            Http::setHeadersByCode(StatusCode::FORBIDDEN);
        }

        $sTitle = t('Access Denied!');
        $this->view->page_title = $sTitle;
        $this->view->h1_title = $sTitle;
        $this->view->error_desc = t('Oops! You are not authorized to access this page!');

        $this->view->pOH_not_found = 1;
        $this->output();
        exit;
    }

    /**
     * Assign site URLs/site name to the Registry class.
     *
     * @return void
     */
    private function assignSiteInfoRegistryVars()
    {
        // Site URL
        $this->registry->site_url = PH7_URL_ROOT;
        $this->registry->url_relative = PH7_RELATIVE;

        // Site Name
        $this->registry->site_name = htmlspecialchars(M\DbConfig::getSetting('siteName'));
    }

    /**
     * Assign all global variables to pH7Tpl.
     *
     * @return void
     */
    private function assignGlobalTplVars()
    {
        /**
         * Set design object to the template.
         * @internal Warning: This one won't work if directly used as shortcut in pH7Tpl parser.
         */
        $this->view->design = $this->design;

        $bIsMobApp = MobApp::is($this->httpRequest, $this->session);

        $aAuthViewVars = [
            'is_admin_auth' => AdminCore::auth(),
            'is_user_auth' => UserCore::auth(),
            'is_aff_auth' => AffiliateCore::auth()
        ];
        $aGlobalViewVars = [
            'top_navbar_type' => M\DbConfig::getSetting('navbarType'),
            'is_guest_homepage' => $this->isGuestOnHomepage($aAuthViewVars['is_user_auth']),
            'is_disclaimer' => !$bIsMobApp && (bool)M\DbConfig::getSetting('disclaimer'),
            'is_cookie_consent_bar' => !$bIsMobApp && (bool)M\DbConfig::getSetting('cookieConsentBar')
        ];

        $this->view->assigns($aAuthViewVars);
        $this->view->assigns($aGlobalViewVars);

        // Set other variables
        $this->setMetaTplVars();
        $this->setModsStatusTplVars();
        $this->setUserNotifications();
    }

    /**
     * Assign Meta and Info vars to the template engine.
     *
     * @return void
     */
    final private function setMetaTplVars()
    {
        $oInfo = M\DbConfig::getMetaMain(PH7_LANG_NAME);

        $aMetaVars = [
            'site_name' => $this->registry->site_name,
            'page_title' => $oInfo->pageTitle,
            'slogan' => $oInfo->slogan,
            'headline' => $oInfo->headline,
            'meta_description' => $oInfo->metaDescription,
            'meta_keywords' => $oInfo->metaKeywords,
            'meta_author' => $oInfo->metaAuthor,
            'meta_robots' => $oInfo->metaRobots,
            'meta_copyright' => $oInfo->metaCopyright,
            'meta_rating' => $oInfo->metaRating,
            'meta_distribution' => $oInfo->metaDistribution,
            'meta_category' => $oInfo->metaCategory,
            'header' => 0 // Default value of header contents
        ];
        $this->view->assigns($aMetaVars);

        unset($oInfo, $aMetaVars);
    }

    private function setModsStatusTplVars()
    {
        $aModsEnabled = [
            'is_connect_enabled' => SysMod::isEnabled('connect'),
            'is_affiliate_enabled' => SysMod::isEnabled('affiliate'),
            'is_game_enabled' => SysMod::isEnabled('game'),
            'is_chat_enabled' => SysMod::isEnabled('chat'),
            'is_chatroulette_enabled' => SysMod::isEnabled('chatroulette'),
            'is_picture_enabled' => SysMod::isEnabled('picture'),
            'is_video_enabled' => SysMod::isEnabled('video'),
            'is_hotornot_enabled' => SysMod::isEnabled('hotornot'),
            'is_lovecalculator_enabled' => SysMod::isEnabled('love-calculator'),
            'is_forum_enabled' => SysMod::isEnabled('forum'),
            'is_note_enabled' => SysMod::isEnabled('note'),
            'is_blog_enabled' => SysMod::isEnabled('blog'),
            'is_newsletter_enabled' => SysMod::isEnabled('newsletter'),
            'is_invite_enabled' => SysMod::isEnabled('invite'),
            'is_mail_enabled' => SysMod::isEnabled('mail'),
            'is_im_enabled' => SysMod::isEnabled('im'),
            'is_relatedprofile_enabled' => SysMod::isEnabled('related-profile'),
            'is_birthday_enabled' => SysMod::isEnabled('birthday'),
            'is_map_enabled' => SysMod::isEnabled('map'),
            'is_friend_enabled' => SysMod::isEnabled('friend'),
            'is_webcam_enabled' => SysMod::isEnabled('webcam'),
            'is_pwa_enabled' => SysMod::isEnabled('pwa'),
            'is_smsverification_enabled' => SysMod::isEnabled('sms-verification')
        ];
        $this->view->assigns($aModsEnabled);

        unset($aModsEnabled);
    }

    private function setUserNotifications()
    {
        $aNotificationCounter = [
            'count_unread_mail' => MailCoreModel::countUnreadMsg($this->session->get('member_id')),
            'count_pen_friend_request' => FriendCoreModel::getPending($this->session->get('member_id'))
        ];
        $this->view->assigns($aNotificationCounter);

        unset($aNotificationCounter);
    }

    /**
     * Check if a "not logged in" visitor is on the website's homepage.
     *
     * @param bool $bIsUserLogged
     *
     * @return bool TRUE if visitor is on the homepage (index), FALSE otherwise.
     */
    private function isGuestOnHomepage($bIsUserLogged)
    {
        return !$bIsUserLogged && $this->registry->module === self::CORE_MAIN_MODULE &&
            $this->registry->controller === 'MainController' &&
            $this->registry->action === 'index';
    }

    /**
     * Check if the module is not disabled, otherwise we displayed a Not Found page.
     *
     * @return void If the module is disabled, displays the Not Found page and exit the script.
     */
    private function checkModStatus()
    {
        if (!SysMod::isEnabled($this->registry->module)) {
            $this->displayPageNotFound();
        }
    }

    /**
     * Add permissions if the Permission file of the module exists.
     *
     * @return void
     */
    private function checkPerms()
    {
        if (is_file($this->registry->path_module_config . 'Permission.php')) {
            require $this->registry->path_module_config . 'Permission.php';
            new \PH7\Permission;
        }
    }

    /**
     * Check if the site has been banned for the visitor.
     * Displays the banishment page if a banned IP address is found.
     *
     * @return void If banned, exit the script after displaying the ban page.
     */
    private function checkBanStatus()
    {
        if (Ban::isIp(Ip::get())) {
            Page::banned();
        }
    }

    private function checkCountryBlacklist()
    {
        if ($this->isBlockedCountryPageEligible()) {
            $sMessage = t('You are too far away from us :( Unfortunately, we are not available in your country.');
            Page::message($sMessage);
        }
    }

    /**
     * The maintenance page is not displayed for the "Admin" module and if the administrator is logged in.
     *
     * @return void If the status if maintenance, exit the script after displaying the maintenance page.
     */
    private function checkSiteStatus()
    {
        if ($this->isMaintenancePageEligible()) {
            // Set 1 hour for the duration time of the "Service Unavailable" HTTP status
            Page::maintenance(self::MAINTENANCE_DURATION_SECONDS);
        }
    }

    /**
     *  Securing the server for DDoS attack only! Not for the attacks DoS.
     *
     * @return void
     */
    private function ddosProtection()
    {
        if (!isDebug() && (bool)M\DbConfig::getSetting('DDoS')) {
            $oDDoS = new DDoSStoper;
            if ($oDDoS->cookie() || $oDDoS->session()) {
                $oDDoS->wait();
            }
            unset($oDDoS);
        }
    }

    /**
     * Determines when and where the maintenance page should be displayed.
     * e.g., Maintenance page should be displayed only when enabled
     * and shouldn't be displayed in the admin panel.
     *
     * @return bool
     */
    private function isMaintenancePageEligible()
    {
        return M\DbConfig::getSetting('siteStatus') === M\DbConfig::MAINTENANCE_SITE &&
            !AdminCore::auth() &&
            !AdminCore::isAdminPanel();
    }

    /**
     * @return bool
     */
    private function isBlockedCountryPageEligible()
    {
        $sCountryCode = Country::fixCode(Geo::getCountryCode());

        return $this->registry->module !== PH7_ADMIN_MOD &&
            (new BlockCountryModel)->isBlocked($sCountryCode) &&
            !AdminCore::auth();
    }
}
