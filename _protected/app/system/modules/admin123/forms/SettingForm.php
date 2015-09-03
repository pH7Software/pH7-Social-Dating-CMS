<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */
namespace PH7;

use
PH7\Framework\Mvc\Model\DbConfig,
PH7\Framework\Mvc\Router\Uri,
PH7\Framework\File\File,
PH7\Framework\Ip\Ip;

class SettingForm
{

    public static function display()
    {
        if (isset($_POST['submit_setting']))
        {
            if (\PFBC\Form::isValid($_POST['submit_setting']))
                new SettingFormProcess;

            Framework\Url\Header::redirect();
        }

        $oForm = new \PFBC\Form('form_setting', 700);
        $oForm->configure(array('action' => ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_setting', 'form_setting'));
        $oForm->addElement(new \PFBC\Element\Token('setting'));

        /********** General Settings **********/
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<div class="content" id="general"><h2 class="underline">' . t('Global Settings:') . '</h2>'));

        $oFile = new File;
        $aTplsId = $oFile->getDirList(PH7_PATH_TPL);
        $aLangsId = $oFile->getDirList(PH7_PATH_APP_LANG);

        $aTpls = array();
        foreach ($aTplsId as $sTpl) $aTpls[$sTpl] = ucfirst($sTpl);

        $aLangs = array();
        foreach ($aLangsId as $sLang)
        {
            $sAbbrLang = substr($sLang,0,2);
            $aLangs[$sLang] = t($sAbbrLang) . ' (' . $sLang . ')';
        }

        $oForm->addElement(new \PFBC\Element\Textbox(t('Site Name:'), 'site_name', array('value' => DbConfig::getSetting('siteName'), 'validation' => new \PFBC\Validation\Str(2, 50), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Select(t('Theme by default:'), 'default_template', $aTpls, array('value' => DbConfig::getSetting('defaultTemplate'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Select(t('Language by default:'), 'default_language', $aLangs, array('value' => DbConfig::getSetting('defaultLanguage'), 'validation' => new \PFBC\Validation\Str(5, 5), 'required' => 1)));
        unset($oFile, $aTplsId, $aLangsId, $aTpls, $aLangs);

        $oForm->addElement(new \PFBC\Element\Select(t('Map Type:'), 'map_type', array('roadmap' => t('Roadmap (default)'), 'hybrid' => t('Hybrid'), 'terrain' => t('Terrain'), 'satellite' => t('Satellite')), array('value' => DbConfig::getSetting('mapType'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Select(t('Splash Page:'), 'splash_page', array('1' => t('Enable'), '0' => t('Disable')), array('description' => t('Use the Splash Page for the visitors, otherwise it will classic page that will be used.'), 'value' => DbConfig::getSetting('splashPage'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Select(t('Background Splash Video:'), 'bg_splash_vid', array('1' => t('Enable'), '0' => t('Disable')), array('description' => t('Enable or Disable the "Animated Video" on the Splash Page.'), 'value' => DbConfig::getSetting('bgSplashVideo'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Select(t('Ajax Site with AjPH:'), 'full_ajax_site', array('1' => t('Enable'), '0' => t('Disable')), array('description' => t("Be careful! 'Full Ajax Navigation' feature is still in Beta and may not be working properly on all pages."), 'value' => DbConfig::getSetting('fullAjaxSite'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Select(t('Site Status:'), 'site_status', array(DbConfig::ENABLE_SITE => t('Enable'), DbConfig::MAINTENANCE_SITE => t('Maintenance')), array('value' => DbConfig::getSetting('siteStatus'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Select(t('Adult Disclaimer:'), 'disclaimer', array(1 => t('Enable'), 0 => t('Disable')), array('description' => t('Show an Adult Warning to enter to the site. This is useful for sites with adult content.'), 'value' => DbConfig::getSetting('disclaimer'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Select(t('Cookie Consent Bar:'), 'cookie_consent_bar', array(1 => t('Enable'), 0 => t('Disable')), array('description' => t('Enable a Cookie Consent Bar to prevent your users that your site uses cookies. This is required for EU Law (if you have visitors from EU countries). The Cookie Bar will only be displayed if the visitor is in the EU.'), 'value' => DbConfig::getSetting('cookieConsentBar'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Select(t('Show the News Feed:'), 'is_software_news_feed', array(1 => t('Enable'), 0 => t('Disable')), array('description' => t('Show the Latest News on the software in the admin dashboard (recommend).'), 'value' => DbConfig::getSetting('isSoftwareNewsFeed'), 'required' => 1)));

        /********** Logo Settings **********/
        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div><div class="content" id="logotype"><h2 class="underline">' . t('Logo:') . '</h2>'));
        $oForm->addElement(new \PFBC\Element\File(t('Logo:'), 'logo', array('accept' => 'image/*')));

        $oForm->addElement(new \PFBC\Element\HTMLExternal('<p><img src="' . PH7_URL_TPL . PH7_TPL_NAME . PH7_SH . PH7_IMG . 'logo.png?v=' . File::version(PH7_PATH_TPL . PH7_TPL_NAME . PH7_DS . PH7_IMG . 'logo.png') . '" alt="' . t('Logo') . '" title="' . t('The current logo of your site.') . '" /></p>'));

        /********** Registration **********/
        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div><div class="content" id="registration"><h2 class="underline">' . t('Registration:') . '</h2>'));
        $oForm->addElement(new \PFBC\Element\Select(t('Account activation type for Members:'), 'user_activation_type', array('1' => t('No activation required'), '2' => t('Self activation via email'), '3' => t('Manual activation by administrator')), array('value' => DbConfig::getSetting('userActivationType'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Select(t('Account activation type for Affiliates:'), 'aff_activation_type', array('1' => t('No activation required'), '2' => t('Self activation via email'), '3' => t('Manual activation by administrator')), array('value' => DbConfig::getSetting('affActivationType'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Number(t('Minimum username length:'), 'min_username_length', array('value' => DbConfig::getSetting('minUsernameLength'), 'max' => DbConfig::getSetting('maxUsernameLength')-1, 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Number(t('Maximum username length:'), 'max_username_length', array('value' => DbConfig::getSetting('maxUsernameLength'), 'min' => DbConfig::getSetting('minUsernameLength')+1, 'max' => PH7_MAX_USERNAME_LENGTH, 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Number(t('Minimum age for registration:'), 'min_age_registration', array('value' => DbConfig::getSetting('minAgeRegistration'), 'max' => DbConfig::getSetting('maxAgeRegistration')-1, 'validation' => new \PFBC\Validation\Str(1, 3), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Number(t('Maximum age for registration:'), 'max_age_registration', array('value' => DbConfig::getSetting('maxAgeRegistration'), 'min' => DbConfig::getSetting('minAgeRegistration')+1, 'validation' => new \PFBC\Validation\Str(1, 3), 'required' => 1)));

        $oGroupId = (new AdminCoreModel)->getMemberships();
        $aGroupName = array();
        foreach ($oGroupId as $iId) $aGroupName[$iId->groupId] = $iId->name;
        $oForm->addElement(new \PFBC\Element\Select(t('Default Membership Group:'), 'default_membership_group_id', $aGroupName, array('value'=>DbConfig::getSetting('defaultMembershipGroupId'), 'required'=>1)));
        unset($aGroupName);

        /********** Picture and Video **********/
        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div><div class="content" id="pic_vid"><h2 class="underline">' . t('Picture and Video:') . '</h2>'));

        $oForm->addElement(new \PFBC\Element\HTMLExternal('<br /><h3 class="underline">' . t('Image:') . '</h3>'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Watermark text:'), 'watermark_text_image', array('value' => DbConfig::getSetting('watermarkTextImage'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Number(t('Size Watermark Text:'), 'size_watermark_text_image', array('description' => t('Between 0 to 5.'), 'value' => DbConfig::getSetting('sizeWatermarkTextImage'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\HTMLExternal('<br /><h3 class="underline">' . t('Video:') . '</h3>'));
        $oForm->addElement(new \PFBC\Element\Url(t('Default video:'), 'default_video', array('description' => t('Video by default if no video found.'), 'value' => DbConfig::getSetting('defaultVideo'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Select(t('Autoplay Video:'), 'autoplay_video', array('1' => t('Enable'), '0' => t('Disable')), array('value' => DbConfig::getSetting('autoplayVideo'), 'required' => 1)));

        /********** Moderation **********/
        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div><div class="content" id="moderation"><h2 class="underline">' . t('Moderation:') . '</h2>'));
        $oForm->addElement(new \PFBC\Element\Select(t('Avatar Manual Approval:'), 'avatar_manual_approval', array('1' => t('Enable'), '0' => t('Disable')), array('value' => DbConfig::getSetting('avatarManualApproval'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Select(t('Profile Background Manual Approval:'), 'profile_background_manual_approval', array('1' => t('Enable'), '0' => t('Disable')), array('value' => DbConfig::getSetting('profileBackgroundManualApproval'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Select(t('Note Post Manual Approval:'), 'note_manual_approval', array('1' => t('Enable'), '0' => t('Disable')), array('value' => DbConfig::getSetting('noteManualApproval'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Select(t('Pictures Manual Approval:'), 'picture_manual_approval', array('1' => t('Enable'), '0' => t('Disable')), array('value' => DbConfig::getSetting('pictureManualApproval'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Select(t('Videos Manual Approval:'), 'video_manual_approval', array('1' => t('Enable'), '0' => t('Disable')), array('value' => DbConfig::getSetting('videoManualApproval'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Select(t('Webcam Pictures Manual Approval:'), 'webcam_picture_manual_approval', array('1' => t('Enable'), '0' => t('Disable')), array('description' => t('This mode is experimental approval, do not use it in production.'), 'value' => DbConfig::getSetting('webcamPictureManualApproval'), 'required' => 1)));

        /********** Email **********/
        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div><div class="content" id="email"><h2 class="underline">' . t('Email Parameters:') . '</h2>'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Email Name:'), 'email_name', array('value' => DbConfig::getSetting('emailName'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Email(t('Admin Email:'), 'admin_email', array('value' => DbConfig::getSetting('adminEmail'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Email(t('Feedback Email:'), 'feedback_email', array('value' => DbConfig::getSetting('feedbackEmail'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Email(t('Return Email:'), 'return_email', array('description' => 'Generally noreply@yoursite.com', 'value' => DbConfig::getSetting('returnEmail'), 'required' => 1)));

        /********** Security **********/
        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div><div class="content" id="security"><h2 class="underline">' . t('Security:') . '</h2>'));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<br /><h3 class="underline">' . t('Password:') . '</h3>'));
        $oForm->addElement(new \PFBC\Element\Number(t('Minimum password length:'), 'min_password_length', array('value' => DbConfig::getSetting('minPasswordLength'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Number(t('Maximum password length:'), 'max_password_length', array('value' => DbConfig::getSetting('maxPasswordLength'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<br /><h3 class="underline">' . t('Blocking login attempts exceeded:') . '</h3>'));
        $oForm->addElement(new \PFBC\Element\Select(t('Enable blocking for User:'), 'is_user_login_attempt', array('1' => t('Enable'), '0' => t('Disable')), array('value' => DbConfig::getSetting('isUserLoginAttempt'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Select(t('Enable blocking for Affiliate:'), 'is_affiliate_login_attempt', array('1' => t('Enable'), '0' => t('Disable')), array('value' => DbConfig::getSetting('isAffiliateLoginAttempt'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Select(t('Enable blocking for Admin:'), 'is_admin_login_attempt', array('1' => t('Enable'), '0' => t('Disable')), array('value' => DbConfig::getSetting('isAdminLoginAttempt'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Number(t('Max login attempts before blocking for User:'), 'max_user_login_attempts', array('value' => DbConfig::getSetting('maxUserLoginAttempts'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Number(t('Max login attempts before blocking for Affiliate:'), 'max_affiliate_login_attempts', array('value' => DbConfig::getSetting('maxAffiliateLoginAttempts'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Number(t('Max login attempts before blocking for Admin:'), 'max_admin_login_attempts', array('value' => DbConfig::getSetting('maxAdminLoginAttempts'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Number(t('Time interval blocking for User:'), 'login_user_attempt_time', array('description' => t('Time in minutes.'), 'value' => DbConfig::getSetting('loginUserAttemptTime'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Number(t('Time interval blocking for Affiliate:'), 'login_affiliate_attempt_time', array('description' => t('Time in minutes.'), 'value' => DbConfig::getSetting('loginAffiliateAttemptTime'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Number(t('Time interval blocking for Admin:'), 'login_admin_attempt_time', array('description' => t('Time in minutes.'), 'value' => DbConfig::getSetting('loginAdminAttemptTime'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<br /><h3 class="underline">' . t('Various:') . '</h3>'));
        $oForm->addElement(new \PFBC\Element\Select(t('Send reports by email:'), 'send_report_mail', array('1' => t('Activate'), '0' => t('Deactivate')), array('value' => DbConfig::getSetting('sendReportMail'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Security IP connection for Admin Panel:'), 'ip_login', array('description' => t('Enter <a href="%0%" title="Get your IP here!">your IP address</a> and an even higher security and exclude all other persons and bots that tried to connect with another IP address even if the login is correct! Leave blank to disable this feature.', Ip::api()), 'value' => DbConfig::getSetting('ipLogin'))));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Indicate a word that will replace the banned word in the <a href="%0%">list</a>.', Uri::get(PH7_ADMIN_MOD, 'file', 'protectededit', 'app/configs/bans/word.txt', false)), 'ban_word_replace', array('value' => DbConfig::getSetting('banWordReplace'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Select(t('Enable or Disable the CSRF security tokens in forms:'), 'security_token', array('1' => t('Enable'), '0' => t('Disable')), array('description' => t('Sometime this protection can be annoying for users if there are not fast enough to fulfill the forms. However, if disabled, your site can be vulnerable on CSRF attacks in forms.'), 'value' => DbConfig::getSetting('securityToken'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Number(t('CSRF token lifetime:'), 'security_token_lifetime', array('description' => t('Time in seconds.'), 'value' => DbConfig::getSetting('securityTokenLifetime'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Select(t('System against the DDoS attacks:'), 'stop_DDoS', array('1' => t('Activate'), '0' => t('Deactivate')), array('value' => DbConfig::getSetting('DDoS'), 'required' => 1)));

        /********** Spam **********/
        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div><div class="content" id="spam"><h2 class="underline">' . t('Spam:') . '</h2>'));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<br /><h3 class="underline">' . t('Time Delay:') . '</h3>'));
        $oForm->addElement(new \PFBC\Element\Number(t('Registration delay for User'), 'time_delay_user_registration', array('description' => t('Number of minutes for a new registration with the same IP address.'), 'value' => DbConfig::getSetting('timeDelayUserRegistration'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Number(t('Registration delay for Affiliate'), 'time_delay_aff_registration', array('description' => t('Number of minutes for a new registration with the same IP address.'), 'value' => DbConfig::getSetting('timeDelayAffRegistration'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Number(t('Send Note delay'), 'time_delay_send_note', array('description' => t('Number of minutes for the same user to post a new note.'), 'value' => DbConfig::getSetting('timeDelaySendNote'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Number(t('Send Mail delay'), 'time_delay_send_mail', array('description' => t('Number of minutes for the same user can send a new email.'), 'value' => DbConfig::getSetting('timeDelaySendMail'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Number(t('Send Comment delay'), 'time_delay_send_comment', array('description' => t('Number of minutes for the same user can send a new comment.'), 'value' => DbConfig::getSetting('timeDelaySendComment'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Number(t('Send Forum Topic delay'), 'time_delay_send_forum_topic', array('description' => t('Number of minutes for the same user can send a new topic in the forum.'), 'value' => DbConfig::getSetting('timeDelaySendForumTopic'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Number(t('Send Forum Message delay'), 'time_delay_send_forum_msg', array('description' => t('Number of minutes for the same user can send a reply message in the same topic.'), 'value' => DbConfig::getSetting('timeDelaySendForumMsg'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\HTMLExternal('<br /><h3 class="underline">' . t('Captcha:') . '</h3>'));
        $oForm->addElement(new \PFBC\Element\Select(t('Captcha for User Signup Form:'), 'is_captcha_user_signup', array('1' => t('Activate'), '0' => t('Deactivate')), array('value' => DbConfig::getSetting('isCaptchaUserSignup'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Select(t('Captcha for Affiliate Signup Form:'), 'is_captcha_affiliate_signup', array('1' => t('Activate'), '0' => t('Deactivate')), array('value' => DbConfig::getSetting('isCaptchaAffiliateSignup'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Select(t('Captcha for send an Email:'), 'is_captcha_mail', array('1' => t('Activate'), '0' => t('Deactivate')), array('value' => DbConfig::getSetting('isCaptchaMail'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Select(t('Captcha for adding a Comment:'), 'is_captcha_comment', array('1' => t('Activate'), '0' => t('Deactivate')), array('value' => DbConfig::getSetting('isCaptchaComment'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Select(t('Captcha for adding or reply a message in the Forum:'), 'is_captcha_forum', array('1' => t('Activate'), '0' => t('Deactivate')), array('value' => DbConfig::getSetting('isCaptchaForum'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Select(t('Captcha for adding a User Post Note:'), 'is_captcha_note', array('1' => t('Activate'), '0' => t('Deactivate')), array('value' => DbConfig::getSetting('isCaptchaNote'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\HTMLExternal('<br /><h3 class="underline">' . t('Pruning:') . '</h3>'));
        $oForm->addElement(new \PFBC\Element\Number(t('Delete older messages:'), 'clean_msg', array('description' => t('Delete messages older than days. 0 to disable.'), 'value' => DbConfig::getSetting('cleanMsg'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Number(t('Delete older comments:'), 'clean_comment', array('description' => t('Delete comments older than days. 0 to disable.'), 'value' => DbConfig::getSetting('cleanComment'), 'required' => 1)));

        /********** Api **********/
        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div><div class="content" id="api"><h2 class="underline">' . t('Api:') . '</h2>'));
        $oForm->addElement(new \PFBC\Element\Url(t('IP Api:'), 'ip_api', array('description' => t('The URL must end with a slash.'), 'value' => DbConfig::getSetting('ipApi'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Url(t('Chat Api:'), 'chat_api', array('description' => t('Parsing tags are permitted (e.g. #!http://api.your-service-chat.com/?url=%0%&name=%1%!#).', '<strong>%site_url%</strong>', '<strong>%site_name%</strong>'), 'value' => DbConfig::getSetting('chatApi'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Url(t('Chatroulette Api:'), 'chatroulette_api', array('description' => t('Parsing tags are permitted (e.g. #!http://api.your-service-chat.com/?url=%0%&name=%1%!#).', '<strong>%site_url%</strong>', '<strong>%site_name%</strong>'), 'value' => DbConfig::getSetting('chatrouletteApi'), 'required' => 1)));

        /********** Automation **********/
        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div><div class="content" id="automation"><h2 class="underline">' . t('Automation:') . '</h2>'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Secret word for the URL cron:'), 'cron_security_hash', array('description' => t('Your very secret word for the cron URL. It will be used for running automated cron job.'), 'value' => DbConfig::getSetting('cronSecurityHash'), 'required' => 1, 'validation' => new \PFBC\Validation\Str(1, 64))));
        $oForm->addElement(new \PFBC\Element\Number(t('User inactivity timeout:'), 'user_timeout', array('description' => t('The number of minutes that a member becomes inactive (offline).'), 'value' => DbConfig::getSetting('userTimeout'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div><script src="' . PH7_URL_STATIC . PH7_JS . 'tabs.js"></script><script>tabs(\'p\', [\'general\',\'logotype\',\'registration\',\'pic_vid\',\'moderation\',\'email\',\'security\',\'spam\',\'api\',\'automation\']);</script>'));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }

}
