<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Model\DbConfig;

class SettingFormProcess extends Form
{

    private $bIsErr = false;

    public function __construct()
    {
        parent::__construct();

        /********** General Settings **********/

        if(!$this->str->equals($this->httpRequest->post('site_name'), DbConfig::getSetting('siteName')))
            DbConfig::setSetting($this->httpRequest->post('site_name'), 'siteName');

        if(!$this->str->equals($this->httpRequest->post('default_template'), DbConfig::getSetting('defaultTemplate')))
            DbConfig::setSetting($this->httpRequest->post('default_template'), 'defaultTemplate');

        if(!$this->str->equals($this->httpRequest->post('default_language'), DbConfig::getSetting('defaultLanguage')))
            DbConfig::setSetting($this->httpRequest->post('default_language'), 'defaultLanguage');

        if(!$this->str->equals($this->httpRequest->post('map_type'), DbConfig::getSetting('mapType')))
            DbConfig::setSetting($this->httpRequest->post('map_type'), 'mapType');

        if(!$this->str->equals($this->httpRequest->post('splash_page'), DbConfig::getSetting('splashPage')))
            DbConfig::setSetting($this->httpRequest->post('splash_page'), 'splashPage');

        if(!$this->str->equals($this->httpRequest->post('bg_splash_vid'), DbConfig::getSetting('bgSplashVideo')))
            DbConfig::setSetting($this->httpRequest->post('bg_splash_vid'), 'bgSplashVideo');

        if(!$this->str->equals($this->httpRequest->post('full_ajax_site'), DbConfig::getSetting('fullAjaxSite')))
            DbConfig::setSetting($this->httpRequest->post('full_ajax_site'), 'fullAjaxSite');

        if(!$this->str->equals($this->httpRequest->post('site_status'), DbConfig::getSetting('siteStatus')))
            DbConfig::setSiteMode($this->httpRequest->post('site_status'));

        if(!$this->str->equals($this->httpRequest->post('disclaimer'), DbConfig::getSetting('disclaimer')))
            DbConfig::setSetting($this->httpRequest->post('disclaimer'), 'disclaimer');

        if(!$this->str->equals($this->httpRequest->post('cookie_consent_bar'), DbConfig::getSetting('cookieConsentBar')))
            DbConfig::setSetting($this->httpRequest->post('cookie_consent_bar'), 'cookieConsentBar');

        if(!$this->str->equals($this->httpRequest->post('is_software_news_feed'), DbConfig::getSetting('isSoftwareNewsFeed')))
            DbConfig::setSetting($this->httpRequest->post('is_software_news_feed'), 'isSoftwareNewsFeed');


        /********** Logo Settings **********/

        if(!empty($_FILES['logo']['tmp_name']))
        {
            $oLogo = new Framework\Image\Image($_FILES['logo']['tmp_name']);
            if(!$oLogo->validate())
            {
                \PFBC\Form::setError('form_setting', Form::wrongImgFileTypeMsg());
                $this->bIsErr = true;
            }
            else
            {
                /*
                 * The method deleteFile first test if the file exists, if so it delete the file.
                 */
                $sPathName = PH7_PATH_TPL . PH7_TPL_NAME . PH7_DS . PH7_IMG . 'logo.png';
                $this->file->deleteFile($sPathName); // It erases the old logo.
                $oLogo->dynamicResize(250,60);
                $oLogo->save($sPathName);

                // Clear CSS cache, because the logo is storaged with data URI in the CSS cache file
                $this->file->deleteDir(PH7_PATH_CACHE . Framework\Layout\Gzip\Gzip::CACHE_DIR);

                // Clear the Web browser cache
                (new Framework\Navigation\Browser)->noCache();
            }
        }


        /********** Email **********/

        if(!$this->str->equals($this->httpRequest->post('email_name'), DbConfig::getSetting('emailName')))
            DbConfig::setSetting($this->httpRequest->post('email_name'), 'emailName');

        if(!$this->str->equals($this->httpRequest->post('admin_email'), DbConfig::getSetting('adminEmail')))
            DbConfig::setSetting($this->httpRequest->post('admin_email'), 'adminEmail');

        if(!$this->str->equals($this->httpRequest->post('feedback_email'), DbConfig::getSetting('feedbackEmail')))
            DbConfig::setSetting($this->httpRequest->post('feedback_email'),'feedbackEmail');

        if(!$this->str->equals($this->httpRequest->post('return_email'), DbConfig::getSetting('returnEmail')))
            DbConfig::setSetting($this->httpRequest->post('return_email'), 'returnEmail');


        /********** Registration **********/

        if(!$this->str->equals($this->httpRequest->post('user_activation_type'), DbConfig::getSetting('userActivationType')))
            DbConfig::setSetting($this->httpRequest->post('user_activation_type'), 'userActivationType');

        if(!$this->str->equals($this->httpRequest->post('aff_activation_type'), DbConfig::getSetting('affActivationType')))
            DbConfig::setSetting($this->httpRequest->post('aff_activation_type'), 'affActivationType');

        if(!$this->str->equals($this->httpRequest->post('min_username_length'), DbConfig::getSetting('minUsernameLength')))
        {
            $iMaxUsernameLength = DbConfig::getSetting('maxUsernameLength')-1;

            if($this->httpRequest->post('min_username_length') > $iMaxUsernameLength)
            {
                \PFBC\Form::setError('form_setting', t('The minimum length of the username cannot exceed %0% characters.', $iMaxUsernameLength));
                $this->bIsErr = true;
            }
            else
                DbConfig::setSetting($this->httpRequest->post('min_username_length'), 'minUsernameLength');
        }

        if(!$this->str->equals($this->httpRequest->post('max_username_length'), DbConfig::getSetting('maxUsernameLength')))
        {
            if($this->httpRequest->post('max_username_length') > PH7_MAX_USERNAME_LENGTH)
            {
                \PFBC\Form::setError('form_setting', t('The maximum length of the username cannot exceed %0% characters.', PH7_MAX_USERNAME_LENGTH));
                $this->bIsErr = true;
            }
            else
                DbConfig::setSetting($this->httpRequest->post('max_username_length'), 'maxUsernameLength');
        }

        if(!$this->str->equals($this->httpRequest->post('min_age_registration'), DbConfig::getSetting('minAgeRegistration')))
        {
            if($this->httpRequest->post('min_age_registration') >= DbConfig::getSetting('maxAgeRegistration'))
            {
                \PFBC\Form::setError('form_setting', t('You cannot specify a minimum age higher than the maximum age.'));
                $this->bIsErr = true;
            }
            else
                DbConfig::setSetting($this->httpRequest->post('min_age_registration'), 'minAgeRegistration');
        }

        if(!$this->str->equals($this->httpRequest->post('max_age_registration'), DbConfig::getSetting('maxAgeRegistration')))
            DbConfig::setSetting($this->httpRequest->post('max_age_registration'), 'maxAgeRegistration');

        if(!$this->str->equals($this->httpRequest->post('default_membership_group_id'), DbConfig::getSetting('defaultMembershipGroupId')))
            DbConfig::setSetting($this->httpRequest->post('default_membership_group_id'), 'defaultMembershipGroupId');


        /********** Picture and Video **********/

        // Image
        if(!$this->str->equals($this->httpRequest->post('watermark_text_image'), DbConfig::getSetting('watermarkTextImage')))
            DbConfig::setSetting($this->httpRequest->post('watermark_text_image'), 'watermarkTextImage');

        if(!($this->str->equals($this->httpRequest->post('size_watermark_text_image'), DbConfig::getSetting('sizeWatermarkTextImage'))) && ($this->httpRequest->post('size_watermark_text_image') >= 0 && $this->httpRequest->post('size_watermark_text_image') <= 5))
            DbConfig::setSetting($this->httpRequest->post('size_watermark_text_image'), 'sizeWatermarkTextImage');

        // Video
        if(!$this->str->equals($this->httpRequest->post('default_video'), DbConfig::getSetting('defaultVideo')))
            DbConfig::setSetting($this->httpRequest->post('default_video'), 'defaultVideo');

        if(!$this->str->equals($this->httpRequest->post('autoplay_video'), DbConfig::getSetting('autoplayVideo')))
            DbConfig::setSetting($this->httpRequest->post('autoplay_video'), 'autoplayVideo');


        /********** Moderation **********/

        if(!$this->str->equals($this->httpRequest->post('avatar_manual_approval'), DbConfig::getSetting('avatarManualApproval')))
            DbConfig::setSetting($this->httpRequest->post('avatar_manual_approval'), 'avatarManualApproval');

        if(!$this->str->equals($this->httpRequest->post('profile_background_manual_approval'), DbConfig::getSetting('profileBackgroundManualApproval')))
            DbConfig::setSetting($this->httpRequest->post('profile_background_manual_approval'), 'profileBackgroundManualApproval');

        if(!$this->str->equals($this->httpRequest->post('note_manual_approval'), DbConfig::getSetting('noteManualApproval')))
            DbConfig::setSetting($this->httpRequest->post('note_manual_approval'), 'noteManualApproval');

        if(!$this->str->equals($this->httpRequest->post('picture_manual_approval'), DbConfig::getSetting('pictureManualApproval')))
            DbConfig::setSetting($this->httpRequest->post('picture_manual_approval'), 'pictureManualApproval');

        if(!$this->str->equals($this->httpRequest->post('video_manual_approval'), DbConfig::getSetting('videoManualApproval')))
            DbConfig::setSetting($this->httpRequest->post('video_manual_approval'), 'videoManualApproval');

        if(!$this->str->equals($this->httpRequest->post('webcam_picture_manual_approval'), DbConfig::getSetting('webcamPictureManualApproval')))
            DbConfig::setSetting($this->httpRequest->post('webcam_picture_manual_approval'), 'webcamPictureManualApproval');


        /********** Security **********/

        if(!$this->str->equals($this->httpRequest->post('min_password_length'), DbConfig::getSetting('minPasswordLength')))
            DbConfig::setSetting($this->httpRequest->post('min_password_length'), 'minPasswordLength');

        if(!$this->str->equals($this->httpRequest->post('max_password_length'), DbConfig::getSetting('maxPasswordLength')))
            DbConfig::setSetting($this->httpRequest->post('max_password_length'), 'maxPasswordLength');

        if(!$this->str->equals($this->httpRequest->post('is_user_login_attempt'), DbConfig::getSetting('isUserLoginAttempt')))
            DbConfig::setSetting($this->httpRequest->post('is_user_login_attempt'), 'isUserLoginAttempt');

        if(!$this->str->equals($this->httpRequest->post('is_affiliate_login_attempt'), DbConfig::getSetting('isAffiliateLoginAttempt')))
            DbConfig::setSetting($this->httpRequest->post('is_affiliate_login_attempt'), 'isAffiliateLoginAttempt');

        if(!$this->str->equals($this->httpRequest->post('is_admin_login_attempt'), DbConfig::getSetting('isAdminLoginAttempt')))
            DbConfig::setSetting($this->httpRequest->post('is_admin_login_attempt'), 'isAdminLoginAttempt');

        if(!$this->str->equals($this->httpRequest->post('max_user_login_attempts'), DbConfig::getSetting('maxUserLoginAttempts')))
            DbConfig::setSetting($this->httpRequest->post('max_user_login_attempts'), 'maxUserLoginAttempts');

        if(!$this->str->equals($this->httpRequest->post('max_affiliate_login_attempts'), DbConfig::getSetting('maxAffiliateLoginAttempts')))
            DbConfig::setSetting($this->httpRequest->post('max_affiliate_login_attempts'), 'maxAffiliateLoginAttempts');

        if(!$this->str->equals($this->httpRequest->post('max_admin_login_attempts'), DbConfig::getSetting('maxAdminLoginAttempts')))
            DbConfig::setSetting($this->httpRequest->post('max_admin_login_attempts'), 'maxAdminLoginAttempts');

        if(!$this->str->equals($this->httpRequest->post('login_user_attempt_time'), DbConfig::getSetting('loginUserAttemptTime')))
            DbConfig::setSetting($this->httpRequest->post('login_user_attempt_time'), 'loginUserAttemptTime');

        if(!$this->str->equals($this->httpRequest->post('login_affiliate_attempt_time'), DbConfig::getSetting('loginAffiliateAttemptTime')))
            DbConfig::setSetting($this->httpRequest->post('login_affiliate_attempt_time'), 'loginAffiliateAttemptTime');

        if(!$this->str->equals($this->httpRequest->post('login_admin_attempt_time'), DbConfig::getSetting('loginAdminAttemptTime')))
            DbConfig::setSetting($this->httpRequest->post('login_admin_attempt_time'), 'loginAdminAttemptTime');

        if(!$this->str->equals($this->httpRequest->post('send_report_mail'), DbConfig::getSetting('sendReportMail')))
            DbConfig::setSetting($this->httpRequest->post('send_report_mail'), 'sendReportMail');

        if(!$this->str->equals($this->httpRequest->post('ip_login'), DbConfig::getSetting('ipLogin')))
            DbConfig::setSetting($this->httpRequest->post('ip_login'), 'ipLogin');

        if(!$this->str->equals($this->httpRequest->post('ban_word_replace'), DbConfig::getSetting('banWordReplace')))
            DbConfig::setSetting($this->httpRequest->post('ban_word_replace'), 'banWordReplace');

        if(!$this->str->equals($this->httpRequest->post('security_token'), DbConfig::getSetting('securityToken')))
            DbConfig::setSetting($this->httpRequest->post('security_token'), 'securityToken');

        $iSecTokenLifetime = (int) $this->httpRequest->post('security_token_lifetime');
        if(!$this->str->equals($iSecTokenLifetime, DbConfig::getSetting('securityTokenLifetime')))
        {
            if($iSecTokenLifetime < 10)
            {
                \PFBC\Form::setError('form_setting', t('The token lifetime cannot be below 10 seconds.'));
                $this->bIsErr = true;
            }
            else
                DbConfig::setSetting($iSecTokenLifetime, 'securityTokenLifetime');
        }

        if(!$this->str->equals($this->httpRequest->post('stop_DDoS'), DbConfig::getSetting('DDoS')))
            DbConfig::setSetting($this->httpRequest->post('stop_DDoS'), 'DDoS');


        /********** Spam **********/

        // Time Delay
        if(!$this->str->equals($this->httpRequest->post('time_delay_user_registration'), DbConfig::getSetting('timeDelayUserRegistration')))
            DbConfig::setSetting($this->httpRequest->post('time_delay_user_registration'), 'timeDelayUserRegistration');

        if(!$this->str->equals($this->httpRequest->post('time_delay_aff_registration'), DbConfig::getSetting('timeDelayAffRegistration')))
            DbConfig::setSetting($this->httpRequest->post('time_delay_aff_registration'), 'timeDelayAffRegistration');

        if(!$this->str->equals($this->httpRequest->post('time_delay_send_note'), DbConfig::getSetting('timeDelaySendNote')))
            DbConfig::setSetting($this->httpRequest->post('time_delay_send_note'), 'timeDelaySendNote');

        if(!$this->str->equals($this->httpRequest->post('time_delay_send_mail'), DbConfig::getSetting('timeDelaySendMail')))
            DbConfig::setSetting($this->httpRequest->post('time_delay_send_mail'), 'timeDelaySendMail');

        if(!$this->str->equals($this->httpRequest->post('time_delay_send_comment'), DbConfig::getSetting('timeDelaySendComment')))
            DbConfig::setSetting($this->httpRequest->post('time_delay_send_comment'), 'timeDelaySendComment');

        if(!$this->str->equals($this->httpRequest->post('time_delay_send_forum_topic'), DbConfig::getSetting('timeDelaySendForumTopic')))
            DbConfig::setSetting($this->httpRequest->post('time_delay_send_forum_topic'), 'timeDelaySendForumTopic');

        if(!$this->str->equals($this->httpRequest->post('time_delay_send_forum_msg'), DbConfig::getSetting('timeDelaySendForumMsg')))
            DbConfig::setSetting($this->httpRequest->post('time_delay_send_forum_msg'), 'timeDelaySendForumMsg');

        // Captcha
        if(!$this->str->equals($this->httpRequest->post('is_captcha_user_signup'), DbConfig::getSetting('isCaptchaUserSignup')))
            DbConfig::setSetting($this->httpRequest->post('is_captcha_user_signup'), 'isCaptchaUserSignup');

        if(!$this->str->equals($this->httpRequest->post('is_captcha_affiliate_signup'), DbConfig::getSetting('isCaptchaAffiliateSignup')))
            DbConfig::setSetting($this->httpRequest->post('is_captcha_affiliate_signup'), 'isCaptchaAffiliateSignup');

        if(!$this->str->equals($this->httpRequest->post('is_captcha_mail'), DbConfig::getSetting('isCaptchaMail')))
            DbConfig::setSetting($this->httpRequest->post('is_captcha_mail'), 'isCaptchaMail');

        if(!$this->str->equals($this->httpRequest->post('is_captcha_comment'), DbConfig::getSetting('isCaptchaComment')))
            DbConfig::setSetting($this->httpRequest->post('is_captcha_comment'), 'isCaptchaComment');

        if(!$this->str->equals($this->httpRequest->post('is_captcha_forum'), DbConfig::getSetting('isCaptchaForum')))
            DbConfig::setSetting($this->httpRequest->post('is_captcha_forum'), 'isCaptchaForum');

        if(!$this->str->equals($this->httpRequest->post('is_captcha_note'), DbConfig::getSetting('isCaptchaNote')))
            DbConfig::setSetting($this->httpRequest->post('is_captcha_note'), 'isCaptchaNote');


        if(!$this->str->equals($this->httpRequest->post('clean_msg'), DbConfig::getSetting('cleanMsg')))
            DbConfig::setSetting($this->httpRequest->post('clean_msg'), 'cleanMsg');

        if(!$this->str->equals($this->httpRequest->post('clean_comment'), DbConfig::getSetting('cleanComment')))
            DbConfig::setSetting($this->httpRequest->post('clean_comment'), 'cleanComment');


        /********** Api **********/

        if(!$this->str->equals($this->httpRequest->post('ip_api'), DbConfig::getSetting('ipApi')))
            DbConfig::setSetting($this->httpRequest->post('ip_api'), 'ipApi');

        if(!$this->str->equals($this->httpRequest->post('chat_api'), DbConfig::getSetting('chatApi')))
            DbConfig::setSetting($this->httpRequest->post('chat_api'), 'chatApi');

        if(!$this->str->equals($this->httpRequest->post('chatroulette_api'), DbConfig::getSetting('chatrouletteApi')))
            DbConfig::setSetting($this->httpRequest->post('chatroulette_api'), 'chatrouletteApi');


        /********** Automation **********/

        if(!$this->str->equals($this->httpRequest->post('cron_security_hash'), DbConfig::getSetting('cronSecurityHash')))
            DbConfig::setSetting($this->httpRequest->post('cron_security_hash'), 'cronSecurityHash');

        if(!$this->str->equals($this->httpRequest->post('user_timeout'), DbConfig::getSetting('userTimeout')))
            DbConfig::setSetting($this->httpRequest->post('user_timeout'), 'userTimeout');

        /* Clean DbConfig Cache */
        (new Framework\Cache\Cache)->start(DbConfig::CACHE_GROUP, null, null)->clear();

        if(!$this->bIsErr)
            \PFBC\Form::setSuccess('form_setting', t('The configuration was saved successfully!'));
    }

}
