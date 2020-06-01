<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Image\Image;
use PH7\Framework\Layout\Gzip\Gzip;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Navigation\Browser;

class SettingFormProcess extends Form
{
    const LOGO_FILENAME = 'logo.png';
    const MIN_CSRF_TOKEN_LIFETIME = 10;
    const LOGO_WIDTH = 47;
    const LOGO_HEIGHT = 45;
    const MAX_WATERMARK_SIZE = 5;

    /** @var boolean */
    private $bIsErr = false;

    /** @var array */
    private static $aSettingFields = [
        // General Settings
        'site_name' => 'siteName',
        'default_template' => 'defaultTemplate',
        'default_sys_module' => 'defaultSysModule',
        'default_language' => 'defaultLanguage',
        'map_type' => 'mapType',
        'profile_with_avatars' => 'profileWithAvatarSet',
        'splash_page' => 'splashPage',
        'bg_splash_vid' => 'bgSplashVideo',
        'users_block' => 'usersBlock',
        'number_profile_splash_page' => 'numberProfileSplashPage',
        'wysiwyg_editor_forum' => 'wysiwygEditorForum',
        'social_media_widgets' => 'socialMediaWidgets',
        'disclaimer' => 'disclaimer',
        'cookie_consent_bar' => 'cookieConsentBar',
        'site_status' => 'siteStatus',
        'display_powered_by_link' => 'displayPoweredByLink',
        'is_software_news_feed' => 'isSoftwareNewsFeed',

        // Design
        'navbar_type' => 'navbarType',
        'background_color' => 'backgroundColor',
        'text_color' => 'textColor',
        'heading1_color' => 'heading1Color',
        'heading2_color' => 'heading2Color',
        'heading3_color' => 'heading3Color',
        'link_color' => 'linkColor',
        'footer_link_color' => 'footerLinkColor',
        'link_hover_color' => 'linkHoverColor',

        // Registration
        'user_activation_type' => 'userActivationType',
        'aff_activation_type' => 'affActivationType',
        'min_username_length' => 'minUsernameLength',
        'max_username_length' => 'maxUsernameLength',
        'min_age_registration' => 'minAgeRegistration',
        'max_age_registration' => 'maxAgeRegistration',
        'is_user_age_range_field' => 'isUserAgeRangeField',
        'require_registration_avatar' => 'requireRegistrationAvatar',
        'default_membership_group_id' => 'defaultMembershipGroupId',

        // Picture and Video
        'watermark_text_image' => 'watermarkTextImage',
        'size_watermark_text_image' => 'sizeWatermarkTextImage',
        'default_video' => 'defaultVideo',
        'autoplay_video' => 'autoplayVideo',

        // Moderation
        'avatar_manual_approval' => 'avatarManualApproval',
        'bg_profile_manual_approval' => 'bgProfileManualApproval',
        'note_manual_approval' => 'noteManualApproval',
        'picture_manual_approval' => 'pictureManualApproval',
        'nudity_filter' => 'nudityFilter',
        'video_manual_approval' => 'videoManualApproval',
        'webcam_picture_manual_approval' => 'webcamPictureManualApproval',

        // Email
        'email_name' => 'emailName',
        'admin_email' => 'adminEmail',
        'feedback_email' => 'feedbackEmail',
        'return_email' => 'returnEmail',

        // Security
        'min_password_length' => 'minPasswordLength',
        'max_password_length' => 'maxPasswordLength',
        'is_user_login_attempt' => 'isUserLoginAttempt',
        'is_affiliate_login_attempt' => 'isAffiliateLoginAttempt',
        'is_admin_login_attempt' => 'isAdminLoginAttempt',
        'max_user_login_attempts' => 'maxUserLoginAttempts',
        'max_affiliate_login_attempts' => 'maxAffiliateLoginAttempts',
        'max_admin_login_attempts' => 'maxAdminLoginAttempts',
        'login_user_attempt_time' => 'loginUserAttemptTime',
        'login_affiliate_attempt_time' => 'loginAffiliateAttemptTime',
        'login_admin_attempt_time' => 'loginAdminAttemptTime',
        'send_report_mail' => 'sendReportMail',
        'ip_login' => 'ipLogin',
        'ban_word_replace' => 'banWordReplace',

        // CSRF
        'security_token_forms' => 'securityToken',
        'security_token_lifetime' => 'securityTokenLifetime',

        // Session hijacking protection
        'is_user_session_ip_check' => 'isUserSessionIpCheck',
        'is_affiliate_session_ip_check' => 'isAffiliateSessionIpCheck',
        'is_admin_session_ip_check' => 'isAdminSessionIpCheck',

        'stop_DDoS' => 'DDoS',

        // Spam
        'time_delay_user_registration' => 'timeDelayUserRegistration',
        'time_delay_aff_registration' => 'timeDelayAffRegistration',
        'time_delay_send_note' => 'timeDelaySendNote',
        'time_delay_send_mail' => 'timeDelaySendMail',
        'time_delay_send_comment' => 'timeDelaySendComment',
        'time_delay_send_forum_topic' => 'timeDelaySendForumTopic',
        'time_delay_send_forum_msg' => 'timeDelaySendForumMsg',

        // Captcha
        'captcha_complexity' => 'captchaComplexity',
        'captcha_case_sensitive' => 'captchaCaseSensitive',
        'is_captcha_user_signup' => 'isCaptchaUserSignup',
        'is_captcha_affiliate_signup' => 'isCaptchaAffiliateSignup',
        'is_captcha_mail' => 'isCaptchaMail',
        'is_captcha_comment' => 'isCaptchaComment',
        'is_captcha_forum' => 'isCaptchaForum',
        'is_captcha_note' => 'isCaptchaNote',
        'clean_msg' => 'cleanMsg',
        'clean_comment' => 'cleanComment',
        'clean_messenger' => 'cleanMessenger',

        // API
        'google_api_key' => 'googleApiKey',
        'ip_api' => 'ipApi',
        'chat_api' => 'chatApi',
        'chatroulette_api' => 'chatrouletteApi',

        // Automation
        'cron_security_hash' => 'cronSecurityHash',
        'user_timeout' => 'userTimeout'
    ];

    public function __construct()
    {
        parent::__construct();

        $this->updateLogo();
        $this->updateGenericFields();

        DbConfig::clearCache();

        if ($this->noErrors()) {
            \PFBC\Form::setSuccess('form_setting', t('Configurations successfully updated!'));
        }
    }

    /**
     * Update the other "generic" fields (if modified only).
     */
    private function updateGenericFields()
    {
        foreach (self::$aSettingFields as $sKey => $sVal) {
            if ($sKey === 'security_token_lifetime') {
                $iSecTokenLifetime = (int)$this->httpRequest->post('security_token_lifetime');

                if (!$this->str->equals($iSecTokenLifetime, DbConfig::getSetting('securityTokenLifetime'))) {
                    if ($iSecTokenLifetime < self::MIN_CSRF_TOKEN_LIFETIME) {
                        \PFBC\Form::setError('form_setting', t('The token lifetime cannot be below %0% seconds.', self::MIN_CSRF_TOKEN_LIFETIME));
                        $this->bIsErr = true;
                    } else {
                        DbConfig::setSetting($iSecTokenLifetime, 'securityTokenLifetime');
                    }
                }
            } elseif ($this->hasDataChanged($sKey, $sVal)) {
                switch ($sKey) {
                    case 'min_username_length': {
                        $iMaxUsernameLength = $this->httpRequest->post('max_username_length')-1;
                        if ($this->httpRequest->post('min_username_length') > $iMaxUsernameLength) {
                            \PFBC\Form::setError('form_setting', t('The minimum length of the username cannot exceed %0% characters.', $iMaxUsernameLength));
                             $this->bIsErr = true;
                         } else {
                            DbConfig::setSetting($this->httpRequest->post('min_username_length'), 'minUsernameLength');
                        }
                    } break;

                    case 'max_username_length': {
                        if ($this->httpRequest->post('max_username_length') > PH7_MAX_USERNAME_LENGTH) {
                            \PFBC\Form::setError('form_setting', t('The maximum length of the username cannot exceed %0% characters.', PH7_MAX_USERNAME_LENGTH));
                            $this->bIsErr = true;
                        } else {
                            DbConfig::setSetting($this->httpRequest->post('max_username_length'), 'maxUsernameLength');
                        }
                    } break;

                    case 'min_age_registration': {
                        if ($this->httpRequest->post('min_age_registration') >= $this->httpRequest->post('max_age_registration')) {
                            \PFBC\Form::setError('form_setting', t('You cannot specify a minimum age higher than the maximum age.'));
                            $this->bIsErr = true;
                        } else {
                            DbConfig::setSetting($this->httpRequest->post('min_age_registration'), 'minAgeRegistration');
                        }
                    } break;

                    case 'size_watermark_text_image': {
                        if ($this->httpRequest->post('size_watermark_text_image') >= 0 &&
                            $this->httpRequest->post('size_watermark_text_image') <= self::MAX_WATERMARK_SIZE) {
                            DbConfig::setSetting($this->httpRequest->post('size_watermark_text_image'), 'sizeWatermarkTextImage');
                        }
                    } break;

                    case 'background_color':
                    case 'text_color':
                    case 'heading1_color':
                    case 'heading2_color':
                    case 'heading3_color':
                    case 'link_color':
                    case 'footer_link_color':
                    case 'link_hover_color': {
                        // Prevent to override color style if the value isn't changed by user but set by the Web browser due to empty field values
                        if (!Browser::isDefaultBrowserHexCodeFound($this->httpRequest->post($sKey))) {
                            DbConfig::setSetting($this->httpRequest->post($sKey), $sVal);
                        }
                    } break;

                    default: {
                        $sMethod = ($sKey === 'site_status' ? 'setSiteMode' : ($sKey === 'social_media_widgets' ? 'setSocialWidgets' : 'setSetting'));
                        DbConfig::$sMethod($this->httpRequest->post($sKey, null, true), $sVal);
                    }
                }
            }
        }
    }

    /**
     * Update Logo (if a new one if uploaded only).
     */
    private function updateLogo()
    {
        if ($this->isLogoUploaded()) {
            $oLogo = new Image($_FILES['logo']['tmp_name']);
            if (!$oLogo->validate()) {
                \PFBC\Form::setError('form_setting', Form::wrongImgFileTypeMsg());
                $this->bIsErr = true;
            } else {
                /**
                 * @internal File::deleteFile() first tests if the file exists, and then deletes it.
                 */
                $sPathName = PH7_PATH_TPL . PH7_TPL_NAME . PH7_DS . PH7_IMG . self::LOGO_FILENAME;
                $this->file->deleteFile($sPathName); // It erases the old logo.
                $oLogo->dynamicResize(self::LOGO_WIDTH, self::LOGO_HEIGHT);
                $oLogo->save($sPathName);

                // Clear CSS cache, because the logo is stored with data URI in the CSS cache file
                $this->file->deleteDir(PH7_PATH_CACHE . Gzip::CACHE_DIR);

                // Clear the Web browser's cache
                $this->browser->noCache();
            }
        }
    }

    /**
     * @param string $sKey
     * @param string $sVal
     *
     * @return bool
     *
     * @throws Framework\Mvc\Request\WrongRequestMethodException
     */
    private function hasDataChanged($sKey, $sVal)
    {
        return
            isset($_POST[$sKey]) &&
            !$this->str->equals($this->httpRequest->post($sKey), DbConfig::getSetting($sVal));
    }

    /**
     * @return bool
     */
    private function isLogoUploaded()
    {
        return !empty($_FILES['logo']['tmp_name']);
    }

    /**
     * @return bool
     */
    private function noErrors()
    {
        return !$this->bIsErr;
    }
}
