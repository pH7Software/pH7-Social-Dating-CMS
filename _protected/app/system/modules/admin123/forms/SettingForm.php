<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */

namespace PH7;

use PH7\Framework\File\File;
use PH7\Framework\Ip\Ip;
use PH7\Framework\Module\Various as SysMod;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Router\Uri;

class SettingForm
{
    const CHANGE_CHAT_DOC_URL = 'http://ph7cms.com/how-to-change-chat/';
    const I18N_DOC_URL = 'http://ph7cms.com/doc/en/how-to-translate-to-another-language';
    const GOOGLE_API_KEY_URL = 'https://console.developers.google.com/flows/enableapi?apiid=maps_backend,geocoding_backend,directions_backend,distance_matrix_backend,elevation_backend,places_backend&amp;keyType=CLIENT_SIDE&amp;reusekey=true';

    public static function display()
    {
        if (isset($_POST['submit_setting'])) {
            if (\PFBC\Form::isValid($_POST['submit_setting'])) {
                new SettingFormProcess;
            }

            Framework\Url\Header::redirect();
        }

        $oForm = new \PFBC\Form('form_setting');
        $oForm->configure(array('action' => ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_setting', 'form_setting'));
        $oForm->addElement(new \PFBC\Element\Token('setting'));


        /********** General Settings **********/
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<div class="content" id="general"><div class="col-md-10"><h2 class="underline">' . t('Global Settings') . '</h2>'));

        $oFile = new File;

        $oForm->addElement(new \PFBC\Element\Textbox(t('Site Name:'), 'site_name', array('value' => DbConfig::getSetting('siteName'), 'validation' => new \PFBC\Validation\Str(2, 50), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Default Theme:'), 'default_template', self::getTpls($oFile), array('value' => DbConfig::getSetting('defaultTemplate'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Default Module:'), 'default_sys_module', self::getDefMods(), array('description' => t('The default module is the one running by default on the homepage (recommended to keep the "user" module).'), 'value' => DbConfig::getSetting('defaultSysModule'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Default Language:'), 'default_language', self::getLangs($oFile), array('description' => t('Documentation: <a href="%0%">Translate your site to another language</a>.', self::I18N_DOC_URL), 'value' => DbConfig::getSetting('defaultLanguage'), 'validation' => new \PFBC\Validation\Str(5, 5), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Map Type:'), 'map_type', array('roadmap' => t('Roadmap (default)'), 'hybrid' => t('Hybrid'), 'terrain' => t('Terrain'), 'satellite' => t('Satellite')), array('value' => DbConfig::getSetting('mapType'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Profiles with Photo Only:'), 'profile_with_avatars', array('1' => t('Yes'), '0' => t('No')), array('description' => t('Display only the profiles with a profile photo on profile blocks (such as the homepage).'), 'value' => DbConfig::getSetting('profileWithAvatarSet'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Display Profiles on Guest Homepage:'), 'users_block', array('1' => t('Enable'), '0' => t('Disable')), array('description' => t('Display or not the newest users on the homepage for visitors. <br /><em>Available only if "User" is the Default Module.</em>'), 'value' => DbConfig::getSetting('usersBlock'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Number(t('Number of Profiles on Splash Page:'), 'number_profile_splash_page', array('description' => t('The number of profiles to display on the Splash Homepage. <br /><em>Available only if "Profiles on Guest Homepage" is enabled and if "User" is the Default Module.</em>'), 'value' => DbConfig::getSetting('numberProfileSplashPage'), 'validation' => new \PFBC\Validation\Str(1, 2), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Splash Homepage:'), 'splash_page', array('1' => t('Enable'), '0' => t('Disable')), array('description' => t('Use the Splash Page (recommended) for visitors (not logged), otherwise the classic page will be used. <br /><em>Available only if "User" is the Default Module.</em>'), 'value' => DbConfig::getSetting('splashPage'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Background Splash Video:'), 'bg_splash_vid', array('1' => t('Enable'), '0' => t('Disable')), array('description' => t('Enable/Disable the "Animated Video" on the Splash Homepage. <strong>If you hold <a href="%0%">pH7CMSPro</a>, we can provide professional splash videos for your specific niche and setting-up the video for you</strong>. <br /><em>Available only if "User" is the Default Module.</em>', Core::SOFTWARE_LICENSE_KEY_URL), 'value' => DbConfig::getSetting('bgSplashVideo'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Ajax Site with AjPH:'), 'full_ajax_site', array('1' => t('Enable'), '0' => t('Disable')), array('description' => t("Be careful! 'Full Ajax Navigation' feature is still in <strong>Beta version</strong> and may not be working properly on all pages."), 'value' => DbConfig::getSetting('fullAjaxSite'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Site Status:'), 'site_status', array(DbConfig::ENABLE_SITE => t('Enable'), DbConfig::MAINTENANCE_SITE => t('Maintenance')), array('value' => DbConfig::getSetting('siteStatus'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Social Media Widgets:'), 'social_media_widgets', array(1 => t('Enable'), 0 => t('Disable')), array('description' => t('Enable the Social Media Sharing such as Like and Sharing buttons.'), 'value' => DbConfig::getSetting('socialMediaWidgets'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Adult Disclaimer:'), 'disclaimer', array(1 => t('Enable'), 0 => t('Disable')), array('description' => t('Show an Adult Warning to enter to your website. This is useful for websites with adult content. <br /><strong>Note: this disclaimer offered by a third-party provider may sometimes open a new tab promoting a third-party adult website.</strong>'), 'value' => DbConfig::getSetting('disclaimer'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Cookie Consent Bar:'), 'cookie_consent_bar', array(1 => t('Enable'), 0 => t('Disable')), array('description' => t('Enable a Cookie Consent Bar to prevent your users that your website uses cookies. This is required for EU Law (if you have visitors from EU countries). The Cookie Bar will only be displayed if the visitor is in the EU.'), 'value' => DbConfig::getSetting('cookieConsentBar'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Show the News Feed:'), 'is_software_news_feed', array(1 => t('Enable'), 0 => t('Disable')), array('description' => t('Show the Latest News about the software in the admin dashboard (recommend).'), 'value' => DbConfig::getSetting('isSoftwareNewsFeed'), 'required' => 1)));

        unset($oFile);


        /********** Logo Settings **********/
        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div></div><div class="content" id="logotype"><div class="col-md-10"><h2 class="underline">' . t('Logo') . '</h2>'));

        $oForm->addElement(new \PFBC\Element\File('', 'logo', array('description' => t('Add your small logo/icon that represents/distinguishes your site/concept/brand the best.'), 'accept' => 'image/*')));

        $oForm->addElement(new \PFBC\Element\HTMLExternal('<div class="vs_marg"><img src="' . PH7_URL_TPL . PH7_TPL_NAME . PH7_SH . PH7_IMG . 'logo.png?v=' . File::version(PH7_PATH_TPL . PH7_TPL_NAME . PH7_DS . PH7_IMG . 'logo.png') . '" alt="' . t('Logo') . '" title="' . t('The current logo of your website.') . '" /></div>'));


        /********** Registration **********/
        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div></div><div class="content" id="registration"><div class="col-md-10"><h2 class="underline">' . t('Registration') . '</h2>'));

        $oForm->addElement(new \PFBC\Element\Select(t('Account activation type for Members:'), 'user_activation_type', array('1' => t('No activation required'), '2' => t('Self activation via email'), '3' => t('Manual activation by administrator')), array('value' => DbConfig::getSetting('userActivationType'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Account activation type for Affiliates:'), 'aff_activation_type', array('1' => t('No activation required'), '2' => t('Self activation via email'), '3' => t('Manual activation by administrator')), array('value' => DbConfig::getSetting('affActivationType'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Number(t('Minimum username length:'), 'min_username_length', array('value' => DbConfig::getSetting('minUsernameLength'), 'max' => DbConfig::getSetting('maxUsernameLength') - 1, 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Number(t('Maximum username length:'), 'max_username_length', array('value' => DbConfig::getSetting('maxUsernameLength'), 'min' => DbConfig::getSetting('minUsernameLength') + 1, 'max' => PH7_MAX_USERNAME_LENGTH, 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Number(t('Minimum age for registration:'), 'min_age_registration', array('value' => DbConfig::getSetting('minAgeRegistration'), 'max' => DbConfig::getSetting('maxAgeRegistration') - 1, 'validation' => new \PFBC\Validation\Str(1, 2), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Number(t('Maximum age for registration:'), 'max_age_registration', array('value' => DbConfig::getSetting('maxAgeRegistration'), 'min' => DbConfig::getSetting('minAgeRegistration') + 1, 'validation' => new \PFBC\Validation\Str(1, 3), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Require photo to be uploaded:'), 'require_registration_avatar', array('1' => t('Yes'), '0' => t('No')), array('description' => t('Require Members to upload a profile photo during sign up.') . '<br /><small>' . t("Doesn't guarantee that all users will have a profile photo because users can still close the page and not finish the registration process.") . '</small>', 'value' => DbConfig::getSetting('requireRegistrationAvatar'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Default Membership Group:'), 'default_membership_group_id', self::getMembershipGroups(), array('value' => DbConfig::getSetting('defaultMembershipGroupId'), 'required' => 1)));


        /********** Picture and Video **********/
        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div></div><div class="content" id="pic_vid"><div class="col-md-10"><h2 class="underline">' . t('Picture and Video') . '</h2>'));

        $oForm->addElement(new \PFBC\Element\HTMLExternal('<br /><h3 class="underline">' . t('Image') . '</h3>'));

        $oForm->addElement(new \PFBC\Element\Textbox(t('Watermark Text:'), 'watermark_text_image', array('value' => DbConfig::getSetting('watermarkTextImage'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Number(t('Watermark Size:'), 'size_watermark_text_image', array('description' => t('Between 0 to 5.'), 'min' => 0, 'max' => 5, 'value' => DbConfig::getSetting('sizeWatermarkTextImage'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\HTMLExternal('<br /><h3 class="underline">' . t('Video') . '</h3>'));

        $oForm->addElement(new \PFBC\Element\Url(t('Default Video:'), 'default_video', array('description' => t('Video by default if no video is found.'), 'value' => DbConfig::getSetting('defaultVideo'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Autoplay Video:'), 'autoplay_video', array('1' => t('Enable'), '0' => t('Disable')), array('value' => DbConfig::getSetting('autoplayVideo'), 'required' => 1)));


        /********** Moderation **********/
        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div></div><div class="content" id="moderation"><div class="col-md-10"><h2 class="underline">' . t('Moderation') . '</h2>'));

        $oForm->addElement(new \PFBC\Element\Select(t('Nudity Filter:'), 'nudity_filter', array('1' => t('Enable'), '0' => t('Disable')), array('description' => t('Photos will be automatically pending approval if there are detected as "Nude/Adult Photos"'), 'value' => DbConfig::getSetting('nudityFilter'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Profile Photo Manual Approval:'), 'avatar_manual_approval', array('1' => t('Enable'), '0' => t('Disable')), array('value' => DbConfig::getSetting('avatarManualApproval'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Background Profile Manual Approval:'), 'bg_profile_manual_approval', array('1' => t('Enable'), '0' => t('Disable')), array('value' => DbConfig::getSetting('bgProfileManualApproval'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Note Post Manual Approval:'), 'note_manual_approval', array('1' => t('Enable'), '0' => t('Disable')), array('value' => DbConfig::getSetting('noteManualApproval'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Photos Manual Approval:'), 'picture_manual_approval', array('1' => t('Enable'), '0' => t('Disable')), array('value' => DbConfig::getSetting('pictureManualApproval'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Videos Manual Approval:'), 'video_manual_approval', array('1' => t('Enable'), '0' => t('Disable')), array('value' => DbConfig::getSetting('videoManualApproval'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Webcam Pictures Manual Approval:'), 'webcam_picture_manual_approval', array('1' => t('Enable'), '0' => t('Disable')), array('description' => t('This approval mode is experimental, do not use it in production.'), 'value' => DbConfig::getSetting('webcamPictureManualApproval'), 'required' => 1)));


        /********** Email **********/
        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div></div><div class="content" id="email"><div class="col-md-10"><h2 class="underline">' . t('Email Parameters') . '</h2>'));

        $oForm->addElement(new \PFBC\Element\Textbox(t('Email Name:'), 'email_name', array('value' => DbConfig::getSetting('emailName'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Email(t('Admin Email:'), 'admin_email', array('value' => DbConfig::getSetting('adminEmail'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Email(t('Feedback Email:'), 'feedback_email', array('value' => DbConfig::getSetting('feedbackEmail'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Email(t('Return Email:'), 'return_email', array('description' => 'Usually noreply@yoursite.com', 'value' => DbConfig::getSetting('returnEmail'), 'required' => 1)));


        /********** Security **********/
        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div></div><div class="content" id="security"><div class="col-md-10"><h2 class="underline">' . t('Security') . '</h2>'));

        $oForm->addElement(new \PFBC\Element\HTMLExternal('<br /><h3 class="underline">' . t('Password') . '</h3>'));

        $oForm->addElement(new \PFBC\Element\Number(t('Minimum password length:'), 'min_password_length', array('value' => DbConfig::getSetting('minPasswordLength'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Number(t('Maximum password length:'), 'max_password_length', array('value' => DbConfig::getSetting('maxPasswordLength'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\HTMLExternal('<br /><h3 class="underline">' . t('Login Attempt Protection') . '</h3>'));

        $oForm->addElement(new \PFBC\Element\Select(t('Blocking login attempts exceeded for Users:'), 'is_user_login_attempt', array('1' => t('Enable'), '0' => t('Disable')), array('value' => DbConfig::getSetting('isUserLoginAttempt'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Blocking login attempts exceeded for Affiliates:'), 'is_affiliate_login_attempt', array('1' => t('Enable'), '0' => t('Disable')), array('value' => DbConfig::getSetting('isAffiliateLoginAttempt'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Blocking login attempts exceeded for Admins:'), 'is_admin_login_attempt', array('1' => t('Enable'), '0' => t('Disable')), array('value' => DbConfig::getSetting('isAdminLoginAttempt'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Number(t('Max number of login attempts before blocking for Users:'), 'max_user_login_attempts', array('value' => DbConfig::getSetting('maxUserLoginAttempts'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Number(t('Max number of login attempts before blocking for Affiliates:'), 'max_affiliate_login_attempts', array('value' => DbConfig::getSetting('maxAffiliateLoginAttempts'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Number(t('Max number of login attempts before blocking for Admins:'), 'max_admin_login_attempts', array('value' => DbConfig::getSetting('maxAdminLoginAttempts'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Number(t('Time interval blocking for Users:'), 'login_user_attempt_time', array('description' => t('Time in minutes.'), 'value' => DbConfig::getSetting('loginUserAttemptTime'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Number(t('Time interval blocking for Affiliates:'), 'login_affiliate_attempt_time', array('description' => t('Time in minutes.'), 'value' => DbConfig::getSetting('loginAffiliateAttemptTime'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Number(t('Time interval blocking for Admins:'), 'login_admin_attempt_time', array('description' => t('Time in minutes.'), 'value' => DbConfig::getSetting('loginAdminAttemptTime'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\HTMLExternal('<br /><h3 class="underline">' . t('Various') . '</h3>'));

        $oForm->addElement(new \PFBC\Element\Select(t('Send Abuse Reports by email:'), 'send_report_mail', array('1' => t('Yes'), '0' => t('No')), array('value' => DbConfig::getSetting('sendReportMail'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Textbox(t('IP Restriction for Admin Panel Access:'), 'ip_login', array('description' => t('By entering <a href="%0%" title="Get your current IP address">your IP</a>, you will get a higher security and exclude all other people and bots that tried to login with another IP address even if the login is correct! Leave blank to disable this feature. Be careful, for using this feature you need to have a static IP (not a dynamic one). If you are not sure, please contact your ISP.', Ip::api()), 'value' => DbConfig::getSetting('ipLogin'))));

        $oForm->addElement(new \PFBC\Element\Textbox(t('Indicate a word that will replace the banned word in the <a href="%0%">list</a>.', Uri::get(PH7_ADMIN_MOD, 'file', 'protectededit', 'app/configs/bans/word.txt', false)), 'ban_word_replace', array('value' => DbConfig::getSetting('banWordReplace'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Enable/Disable the CSRF security tokens for forms:'), 'security_token_forms', array('1' => t('Enable'), '0' => t('Disable')), array('description' => t('Sometimes this protection can be annoying for users if there are not fast enough to fulfill the forms. However, if disabled, your website can be vulnerable on CSRF attacks in forms.'), 'value' => DbConfig::getSetting('securityToken'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Number(t('CSRF token lifetime:'), 'security_token_lifetime', array('description' => t('Time in seconds.'), 'value' => DbConfig::getSetting('securityTokenLifetime'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('System against the DDoS attacks:'), 'stop_DDoS', array('1' => t('Activate'), '0' => t('Deactivate')), array('value' => DbConfig::getSetting('DDoS'), 'required' => 1)));


        /********** Spam **********/
        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div></div><div class="content" id="spam"><div class="col-md-10"><h2 class="underline">' . t('Spam') . '</h2>'));

        $oForm->addElement(new \PFBC\Element\HTMLExternal('<br /><h3 class="underline">' . t('Time Delay') . '</h3>'));

        $oForm->addElement(new \PFBC\Element\Number(t('Registration delay for Users:'), 'time_delay_user_registration', array('description' => t('Number of minutes for a new registration with the same IP address.'), 'value' => DbConfig::getSetting('timeDelayUserRegistration'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Number(t('Registration delay for Affiliates:'), 'time_delay_aff_registration', array('description' => t('Number of minutes for a new registration with the same IP address.'), 'value' => DbConfig::getSetting('timeDelayAffRegistration'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Number(t('Send Note delay:'), 'time_delay_send_note', array('description' => t('Number of minutes for the same user to post a new note.'), 'value' => DbConfig::getSetting('timeDelaySendNote'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Number(t('Send Mail delay:'), 'time_delay_send_mail', array('description' => t('Number of minutes for the same user can send a new email.'), 'value' => DbConfig::getSetting('timeDelaySendMail'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Number(t('Send Comment delay:'), 'time_delay_send_comment', array('description' => t('Number of minutes for the same user can send a new comment.'), 'value' => DbConfig::getSetting('timeDelaySendComment'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Number(t('Send Forum Topic delay:'), 'time_delay_send_forum_topic', array('description' => t('Number of minutes for the same user can send a new topic in the forum.'), 'value' => DbConfig::getSetting('timeDelaySendForumTopic'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Number(t('Send Forum Message delay:'), 'time_delay_send_forum_msg', array('description' => t('Number of minutes for the same user can send a reply message in the same topic.'), 'value' => DbConfig::getSetting('timeDelaySendForumMsg'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\HTMLExternal('<br /><h3 class="underline">' . t('Captcha') . '</h3>'));

        $oForm->addElement(new \PFBC\Element\Select(t('Captcha for User Signup Form:'), 'is_captcha_user_signup', array('1' => t('Activate'), '0' => t('Deactivate')), array('value' => DbConfig::getSetting('isCaptchaUserSignup'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Captcha for Affiliate Signup Form:'), 'is_captcha_affiliate_signup', array('1' => t('Activate'), '0' => t('Deactivate')), array('value' => DbConfig::getSetting('isCaptchaAffiliateSignup'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Captcha for sending Messages between users:'), 'is_captcha_mail', array('1' => t('Activate'), '0' => t('Deactivate')), array('value' => DbConfig::getSetting('isCaptchaMail'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Captcha for adding a Comment:'), 'is_captcha_comment', array('1' => t('Activate'), '0' => t('Deactivate')), array('value' => DbConfig::getSetting('isCaptchaComment'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Captcha for adding or reply a message in the Forum:'), 'is_captcha_forum', array('1' => t('Activate'), '0' => t('Deactivate')), array('value' => DbConfig::getSetting('isCaptchaForum'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Captcha for adding a User Post Note:'), 'is_captcha_note', array('1' => t('Activate'), '0' => t('Deactivate')), array('value' => DbConfig::getSetting('isCaptchaNote'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\HTMLExternal('<br /><h3 class="underline">' . t('Pruning') . '</h3>'));

        $oForm->addElement(new \PFBC\Element\Number(t('Delete old Messages:'), 'clean_msg', array('description' => t('Delete messages older than X days. 0 to disable.'), 'value' => DbConfig::getSetting('cleanMsg'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Number(t('Delete old Comments:'), 'clean_comment', array('description' => t('Delete comments older than X days. 0 to disable.'), 'value' => DbConfig::getSetting('cleanComment'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Number(t('Delete old IM Messages:'), 'clean_messenger', array('description' => t('Delete IM messages older than X days. 0 to disable.'), 'value' => DbConfig::getSetting('cleanMessenger'), 'required' => 1)));


        /********** API **********/
        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div></div><div class="content" id="api"><div class="col-md-10"><h2 class="underline">' . t('API') . '</h2>'));

        $sGoogleApiKeyDesc = t('You can get your key <a href="%0%">here</a>. Then, select "<strong>Google Maps JavaScript API</strong>" for "<em>Which API are you using</em>" and "<strong>Web browser (Javascript)</strong>" for "<em>Where will you be calling the API from</em>", then you will get your API key to paste here. ', self::GOOGLE_API_KEY_URL);

        $oForm->addElement(new \PFBC\Element\Textbox(t('Google Maps API Key:'), 'google_api_key', array('description' => $sGoogleApiKeyDesc, 'value' => DbConfig::getSetting('googleApiKey'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Url(t('IP API:'), 'ip_api', array('description' => t('The URL must end with a slash.'), 'value' => DbConfig::getSetting('ipApi'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Url(t('Chat API:'), 'chat_api', array('description' => t('Documentation: <a href="%0%">Change the default chat service by your real one</a>.<br /> <small>Parsing tags are permitted (e.g. #!http://api.your-service-chat.com/?url=%0%&name=%1%!#).</small>', self::CHANGE_CHAT_DOC_URL, '<strong>%site_url%</strong>', '<strong>%site_name%</strong>'), 'value' => DbConfig::getSetting('chatApi'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\Url(t('Chatroulette API:'), 'chatroulette_api', array('description' => t('Documentation: <a href="%0%">Change the default chatroulette provider by yours</a>.<br /> <small>Parsing tags are permitted (e.g. #!http://api.your-service-chat.com/?url=%0%&name=%1%!#).</small>', self::CHANGE_CHAT_DOC_URL, '<strong>%site_url%</strong>', '<strong>%site_name%</strong>'), 'value' => DbConfig::getSetting('chatrouletteApi'), 'required' => 1)));


        /********** Automation **********/
        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div></div><div class="content" id="automation"><div class="col-md-10"><h2 class="underline">' . t('Automation') . '</h2>'));

        $oForm->addElement(new \PFBC\Element\Textbox(t('Secret word for the cron URL:'), 'cron_security_hash', array('description' => t('Your very secret word for the cron URL. It will be used for running automated cron jobs.'), 'value' => DbConfig::getSetting('cronSecurityHash'), 'required' => 1, 'validation' => new \PFBC\Validation\Str(1, 64))));

        $oForm->addElement(new \PFBC\Element\Number(t('User inactivity timeout:'), 'user_timeout', array('description' => t('The number of minutes that a member becomes inactive (offline).'), 'value' => DbConfig::getSetting('userTimeout'), 'required' => 1)));

        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div></div><script src="' . PH7_URL_STATIC . PH7_JS . 'tabs.js"></script><script>tabs(\'p\', [\'general\',\'logotype\',\'registration\',\'pic_vid\',\'moderation\',\'email\',\'security\',\'spam\',\'api\',\'automation\']);</script>'));


        $oForm->addElement(new \PFBC\Element\Button);

        $oForm->render();
    }

    /**
     * @param File $oFile
     * @return array
     */
    private static function getTpls(File $oFile)
    {
        $aTpls = array();

        $aTplIds = $oFile->getDirList(PH7_PATH_TPL);
        foreach ($aTplIds as $sTpl) {
            $aTpls[$sTpl] = ucfirst($sTpl);
        }

        return $aTpls;
    }

    /**
     * @param File $oFile
     * @return array
     */
    private static function getLangs(File $oFile)
    {
        $aLangs = array();

        $aLangIds = $oFile->getDirList(PH7_PATH_APP_LANG);
        foreach ($aLangIds as $sLang) {
            $sAbbrLang = substr($sLang, 0, 2);
            $aLangs[$sLang] = t($sAbbrLang) . ' (' . $sLang . ')';
        }

        return $aLangs;
    }

    /**
     * @return array
     */
    private static function getDefMods()
    {
        $aMods = array();

        foreach (self::getActivatableDefMods() as $sMod) {
            // Skip the disable module (would be impossible to set a disabled module as the default one)
            if (!SysMod::isEnabled($sMod)) {
                continue;
            }

            $aMods[$sMod] = ucfirst($sMod);
        }

        return $aMods;
    }

    /**
     * @return array
     */
    private static function getMembershipGroups()
    {
        $aGroupNames = array();

        $oGroupIds = (new AdminCoreModel)->getMemberships();
        foreach ($oGroupIds as $iId) {
            $aGroupNames[$iId->groupId] = $iId->name;
        }

        return $aGroupNames;
    }

    /**
     * Get the list of modules that are possible to enable as the default system module.
     *
     * @return array
     */
    private static function getActivatableDefMods()
    {
        return [
            'user',
            'affiliate',
            'blog',
            'note',
            'chat',
            'chatroulette',
            'forum',
            'game',
            'hotornot',
            'picture',
            'video'
        ];
    }
}
