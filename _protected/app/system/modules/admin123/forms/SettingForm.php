<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Color;
use PFBC\Element\Email;
use PFBC\Element\Hidden;
use PFBC\Element\HTMLExternal;
use PFBC\Element\Number;
use PFBC\Element\Select;
use PFBC\Element\Textbox;
use PFBC\Element\Token;
use PFBC\Element\Url;
use PFBC\Validation\Str;
use PH7\Framework\File\File;
use PH7\Framework\Ip\Ip;
use PH7\Framework\Module\Various as SysMod;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Security\CSRF\Token as SecurityToken;
use PH7\Framework\Security\Spam\Captcha\Captcha;
use PH7\Framework\Translate\Lang;
use PH7\Framework\Url\Header;

class SettingForm
{
    const CHANGE_CHAT_DOC_URL = 'https://ph7cms.com/how-to-change-chat/';
    const I18N_DOC_URL = 'https://ph7cms.com/doc/en/how-to-translate-to-another-language';
    const GOOGLE_API_KEY_URL = 'https://console.developers.google.com/flows/enableapi?apiid=maps_backend,geocoding_backend,directions_backend,distance_matrix_backend,elevation_backend,places_backend&amp;keyType=CLIENT_SIDE&amp;reusekey=true';

    public static function display()
    {
        if (isset($_POST['submit_setting'])) {
            if (\PFBC\Form::isValid($_POST['submit_setting'])) {
                new SettingFormProcess;
            }

            Header::redirect();
        }

        $bIsAffiliateEnabled = SysMod::isEnabled('affiliate');
        $bIsMailEnabled = SysMod::isEnabled('mail');
        $bIsNoteEnabled = SysMod::isEnabled('note');
        $bIsForumEnabled = SysMod::isEnabled('forum');
        $bIsPictureEnabled = SysMod::isEnabled('picture');
        $bIsVideoEnabled = SysMod::isEnabled('video');

        $oForm = new \PFBC\Form('form_setting');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_setting', 'form_setting'));
        $oForm->addElement(new Token('setting'));


        /********** General Settings **********/
        $oForm->addElement(new HTMLExternal('<div class="content" id="general"><div class="col-md-10"><h2 class="underline">' . t('Global Settings') . '</h2>'));

        $oFile = new File;

        $oForm->addElement(new Textbox(t('Site Name:'), 'site_name', ['value' => DbConfig::getSetting('siteName'), 'validation' => new Str(2, 50), 'required' => 1]));

        $oForm->addElement(new Select(t('Default Theme:'), 'default_template', self::getTpls($oFile), ['value' => DbConfig::getSetting('defaultTemplate'), 'required' => 1]));

        $oForm->addElement(new Select(t('Default Module:'), 'default_sys_module', self::getDefMods(), ['description' => t('The default module is the one running by default on the homepage.'), 'value' => DbConfig::getSetting('defaultSysModule'), 'required' => 1]));

        $oForm->addElement(new Select(t('Default Language:'), 'default_language', self::getLangs($oFile), ['description' => t('Documentation: <a href="%0%">Translate your site to another language</a>.', self::I18N_DOC_URL), 'value' => DbConfig::getSetting('defaultLanguage'), 'validation' => new Str(5, 5), 'required' => 1]));

        $oForm->addElement(new Select(t('Map Type:'), 'map_type', ['roadmap' => t('Roadmap (default)'), 'hybrid' => t('Hybrid'), 'terrain' => t('Terrain'), 'satellite' => t('Satellite')], ['value' => DbConfig::getSetting('mapType'), 'required' => 1]));

        $oForm->addElement(new Select(t('Profiles with Photo Only:'), 'profile_with_avatars', ['1' => t('Yes'), '0' => t('No')], ['description' => t('Display only the profiles with a profile photo on profile blocks (such as the homepage).'), 'value' => DbConfig::getSetting('profileWithAvatarSet'), 'required' => 1]));

        $oForm->addElement(new Select(t('Splash Homepage:'), 'splash_page', ['1' => t('Enable (recommended)'), '0' => t('Disable')], ['description' => t('Use the Splash Page for visitors (not logged), otherwise the classic page will be used. <br /><em>Available only if "User" is the Default Module.</em>'), 'value' => DbConfig::getSetting('splashPage'), 'required' => 1]));

        $oForm->addElement(new Select(t('Background Splash Video:'), 'bg_splash_vid', ['1' => t('Enable'), '0' => t('Disable')], ['description' => t('Enable/Disable the "Animated Video" on the Splash Homepage. <br /><em>Available only if "User" is the Default Module.</em>'), 'value' => DbConfig::getSetting('bgSplashVideo'), 'required' => 1]));

        $oForm->addElement(new Select(t('Display Profiles on Guest Homepage:'), 'users_block', ['1' => t('Enable'), '0' => t('Disable')], ['description' => t('Display or not the newest users on the homepage for visitors. <br /><em>Available only if "User" is the Default Module.</em>'), 'value' => DbConfig::getSetting('usersBlock'), 'required' => 1]));

        $oForm->addElement(new Number(t('Number of Profiles on Splash Page:'), 'number_profile_splash_page', ['description' => t('The number of profile photos to display on the Splash Homepage. <br /><em>Available only if "Profiles on Guest Homepage" is enabled and if "User" is the Default Module.</em>'), 'value' => DbConfig::getSetting('numberProfileSplashPage'), 'validation' => new Str(1, 2), 'required' => 1]));

        if ($bIsForumEnabled) {
            $oForm->addElement(
                new Select(
                    t('WYSIWYG editor for Forum:'),
                    'wysiwyg_editor_forum',
                    [
                        '1' => t('Enable'),
                        '0' => t('Disable')
                    ],
                    [
                        'description' => t('Enable WYSIWYG editor (CKEditor) for the forum posts. If disabled, the simple textarea field will be used.'),
                        'value' => DbConfig::getSetting('wysiwygEditorForum'),
                        'required' => 1
                    ]
                )
            );
        }

        $oForm->addElement(
            new Select(
                t('Social Media Widgets:'),
                'social_media_widgets',
                [
                    1 => t('Enable'),
                    0 => t('Disable')
                ],
                [
                    'description' => t('Enable the Social Media Sharing such as Like and Sharing buttons.'),
                    'value' => DbConfig::getSetting('socialMediaWidgets'),
                    'required' => 1
                ]
            )
        );

        $oForm->addElement(
            new Select(
                t('Adult Disclaimer:'),
                'disclaimer',
                [
                    1 => t('Enable'),
                    0 => t('Disable')
                ],
                [
                    'description' => t('Show an Adult Warning to enter to your website. Useful for websites that contain adult materials.'),
                    'value' => DbConfig::getSetting('disclaimer'),
                    'required' => 1
                ]
            )
        );

        $oForm->addElement(
            new Select(
                t('Cookie Consent Bar:'),
                'cookie_consent_bar',
                [
                    1 => t('Enable'),
                    0 => t('Disable')
                ],
                [
                    'description' => t('Enable a Cookie Consent Bar to prevent your users that your website uses cookies. This is required by EU Law (if you have visitors from EU countries). The Cookie Bar will only be displayed if the visitor is in the EU.'),
                    'value' => DbConfig::getSetting('cookieConsentBar'),
                    'required' => 1
                ]
            )
        );

        $oForm->addElement(
            new Select(
                t('Site Status:'),
                'site_status',
                [
                    DbConfig::ENABLED_SITE => t('Online'),
                    DbConfig::MAINTENANCE_SITE => t('Maintenance (offline)')
                ],
                [
                    'description' => t("Maintenance mode is useful if you are working on your website or update it. Logged admins and admin panel won't be affected by the maintenance page."),
                    'value' => DbConfig::getSetting('siteStatus'),
                    'required' => 1
                ]
            )
        );

        $oForm->addElement(
            new Select(
                t('Show "Powered By" link in footer:'),
                'display_powered_by_link',
                [
                    1 => t('Enable'),
                    0 => t('Disable (NOT recommended)')
                ],
                [
                    'description' => t('Are you proud of using <a href="%software_website%">pH7CMS</a> brand? Are you proud to say your dating app has been made by the Leading Dating Software provider?'),
                    'value' => DbConfig::getSetting('displayPoweredByLink'),
                    'required' => 1
                ]
            )
        );

        $oForm->addElement(
            new Select(
                t('Show the News Feed:'),
                'is_software_news_feed',
                [
                    1 => t('Enable'),
                    0 => t('Disable')
                ],
                [
                    'description' => t('Show the latest news about the software in the admin dashboard (recommend).'),
                    'value' => DbConfig::getSetting('isSoftwareNewsFeed'),
                    'required' => 1
                ]
            )
        );

        unset($oFile);


        /********** Logo Settings **********/
        $oForm->addElement(new HTMLExternal('</div></div><div class="content" id="icon"><div class="col-md-10"><h2 class="underline">' . t('Icon Logo') . '</h2>'));

        $oForm->addElement(new \PFBC\Element\File('', 'logo', ['description' => t('Add your small logo/icon that represents/distinguishes the best your site/concept/brand.'), 'accept' => 'image/*']));

        $oForm->addElement(new HTMLExternal('<div class="s_marg"><img src="' . PH7_URL_TPL . PH7_TPL_NAME . PH7_SH . PH7_IMG . 'logo.png?v=' . File::version(PH7_PATH_TPL . PH7_TPL_NAME . PH7_DS . PH7_IMG . 'logo.png') . '" alt="' . t('Icon Logo') . '" title="' . t('The current logo of your website.') . '" /></div>'));


        /********** Design (Color) **********/
        $oForm->addElement(new HTMLExternal('</div></div><div class="content" id="design"><div class="col-md-10"><h2 class="underline">' . t('Override Website Colors') . '</h2>'));

        $oForm->addElement(new Select(t('Top Navigation Bar Style'), 'navbar_type', ['default' => t('White & Blue (default)'), 'inverse' => t('Sober Dark')], ['value' => DbConfig::getSetting('navbarType'), 'required' => 1]));

        $oForm->addElement(new Color(t('Website Background:'), 'background_color', ['value' => DbConfig::getSetting('backgroundColor')]));

        $oForm->addElement(new Color(t('Text:'), 'text_color', ['value' => DbConfig::getSetting('textColor')]));

        $oForm->addElement(new Color(t('First Heading (H1):'), 'heading1_color', ['value' => DbConfig::getSetting('heading1Color')]));

        $oForm->addElement(new Color(t('Second Heading (H2):'), 'heading2_color', ['value' => DbConfig::getSetting('heading2Color')]));

        $oForm->addElement(new Color(t('Third Heading (H3):'), 'heading3_color', ['value' => DbConfig::getSetting('heading3Color')]));

        $oForm->addElement(new Color(t('Links:'), 'link_color', ['value' => DbConfig::getSetting('linkColor')]));

        $oForm->addElement(new Color(t('Footer Links:'), 'footer_link_color', ['value' => DbConfig::getSetting('footerLinkColor')]));

        $oForm->addElement(new Color(t('Links Hover:'), 'link_hover_color', ['value' => DbConfig::getSetting('linkHoverColor')]));

        $oForm->addElement(new HTMLExternal(
            '<div class="right"><a href="' . Uri::get(PH7_ADMIN_MOD, 'setting', 'resetcolor', (new SecurityToken)->url(), false) . '">' . t('Reset Colors') . '</a></div>'
        ));


        /********** Registration **********/
        $oForm->addElement(new HTMLExternal('</div></div><div class="content" id="registration"><div class="col-md-10"><h2 class="underline">' . t('Registration') . '</h2>'));

        $aUserActivationTypes = [
            RegistrationCore::NO_ACTIVATION => t('No activation required'),
            RegistrationCore::EMAIL_ACTIVATION => t('Self-activation via email'),
            RegistrationCore::MANUAL_ACTIVATION => t('Manual activation by administrator')
        ];
        if (SysMod::isEnabled('sms-verification')) {
            $aUserActivationTypes[RegistrationCore::SMS_ACTIVATION] = t('Self-activation via SMS');
        }

        $oForm->addElement(
            new Select(
                t('Account activation type for Members:'),
                'user_activation_type',
                $aUserActivationTypes,
                [
                    'value' => DbConfig::getSetting('userActivationType'),
                    'required' => 1
                ]
            )
        );

        if ($bIsAffiliateEnabled) {
            $oForm->addElement(
                new Select(
                    t('Account activation type for Affiliates:'),
                    'aff_activation_type',
                    [
                        RegistrationCore::NO_ACTIVATION => t('No activation required'),
                        RegistrationCore::EMAIL_ACTIVATION => t('Self-activation via email'),
                        RegistrationCore::MANUAL_ACTIVATION => t('Manual activation by administrator')
                    ],
                    [
                        'value' => DbConfig::getSetting('affActivationType'),
                        'required' => 1
                    ]
                )
            );
        }

        $oForm->addElement(new Number(t('Minimum username length:'), 'min_username_length', ['value' => DbConfig::getSetting('minUsernameLength'), 'max' => DbConfig::getSetting('maxUsernameLength') - 1, 'required' => 1]));

        $oForm->addElement(new Number(t('Maximum username length:'), 'max_username_length', ['value' => DbConfig::getSetting('maxUsernameLength'), 'min' => DbConfig::getSetting('minUsernameLength') + 1, 'max' => PH7_MAX_USERNAME_LENGTH, 'required' => 1]));

        $oForm->addElement(new Number(t('Minimum age for registration:'), 'min_age_registration', ['value' => DbConfig::getSetting('minAgeRegistration'), 'max' => DbConfig::getSetting('maxAgeRegistration') - 1, 'validation' => new Str(1, 2), 'required' => 1]));

        $oForm->addElement(new Number(t('Maximum age for registration:'), 'max_age_registration', ['value' => DbConfig::getSetting('maxAgeRegistration'), 'min' => DbConfig::getSetting('minAgeRegistration') + 1, 'validation' => new Str(1, 3), 'required' => 1]));

        $oForm->addElement(new Select(t('Date of Birth field type:'), 'is_user_age_range_field', ['1' => t('Age Range (without month and day of birth)'), '0' => t('Date-Picker calendar (full date of birth)')], ['value' => DbConfig::getSetting('isUserAgeRangeField'), 'required' => 1]));

        $oForm->addElement(new Select(t('Require photo to be uploaded:'), 'require_registration_avatar', ['1' => t('Yes'), '0' => t('No')], ['description' => t('Require Members to upload a profile photo during sign up.') . '<br /><small>' . t("Doesn't guarantee that all users will have a profile photo, because users can still close the tab without completely finishing the registration process.") . '</small>', 'value' => DbConfig::getSetting('requireRegistrationAvatar'), 'required' => 1]));

        $oForm->addElement(new Select(t('Default Membership Group:'), 'default_membership_group_id', self::getMembershipGroups(), ['value' => DbConfig::getSetting('defaultMembershipGroupId'), 'required' => 1]));


        /********** Picture and Video **********/
        $oForm->addElement(new HTMLExternal('</div></div><div class="content" id="pic_vid"><div class="col-md-10"><h2 class="underline">' . t('Picture and Video') . '</h2>'));

        if ($bIsPictureEnabled || $bIsVideoEnabled) {
            $oForm->addElement(new HTMLExternal('<br /><h3 class="underline">' . t('Image') . '</h3>'));

            $oForm->addElement(new Textbox(t('Watermark Text:'), 'watermark_text_image', ['description' => t('Leave it blank to disable the watermark text on images.'), 'value' => DbConfig::getSetting('watermarkTextImage', '')]));

            $oForm->addElement(new Number(t('Watermark Size:'), 'size_watermark_text_image', ['description' => t('Between 0 to 5.'), 'min' => 0, 'max' => 5, 'value' => DbConfig::getSetting('sizeWatermarkTextImage'), 'required' => 1]));
        }

        if ($bIsVideoEnabled) {
            $oForm->addElement(new HTMLExternal('<br /><h3 class="underline">' . t('Video') . '</h3>'));

            $oForm->addElement(new Url(t('Default Video:'), 'default_video', ['description' => t('Video by default if no video is found.'), 'value' => DbConfig::getSetting('defaultVideo'), 'required' => 1]));

            $oForm->addElement(new Select(t('Autoplay Video:'), 'autoplay_video', ['1' => t('Enable'), '0' => t('Disable')], ['value' => DbConfig::getSetting('autoplayVideo'), 'required' => 1]));
        }


        /********** Moderation **********/
        $oForm->addElement(new HTMLExternal('</div></div><div class="content" id="moderation"><div class="col-md-10"><h2 class="underline">' . t('Moderation') . '</h2>'));

        $oForm->addElement(new Select(t('Nudity Filter:'), 'nudity_filter', ['1' => t('Enable'), '0' => t('Disable')], ['description' => t('Photos will be automatically pending approval if there are detected as "Nude/Adult Photos"'), 'value' => DbConfig::getSetting('nudityFilter'), 'required' => 1]));

        $oForm->addElement(new Select(t('Profile Photo Manual Approval:'), 'avatar_manual_approval', ['1' => t('Enable'), '0' => t('Disable')], ['value' => DbConfig::getSetting('avatarManualApproval'), 'required' => 1]));

        $oForm->addElement(new Select(t('Background Profile Manual Approval:'), 'bg_profile_manual_approval', ['1' => t('Enable'), '0' => t('Disable')], ['value' => DbConfig::getSetting('bgProfileManualApproval'), 'required' => 1]));

        if ($bIsNoteEnabled) {
            $oForm->addElement(new Select(t('Note Post Manual Approval:'), 'note_manual_approval', ['1' => t('Enable'), '0' => t('Disable')], ['value' => DbConfig::getSetting('noteManualApproval'), 'required' => 1]));
        }

        if ($bIsPictureEnabled) {
            $oForm->addElement(new Select(t('Photos Manual Approval:'), 'picture_manual_approval', ['1' => t('Enable'), '0' => t('Disable')], ['value' => DbConfig::getSetting('pictureManualApproval'), 'required' => 1]));
        }

        if ($bIsVideoEnabled) {
            $oForm->addElement(new Select(t('Videos Manual Approval:'), 'video_manual_approval', ['1' => t('Enable'), '0' => t('Disable')], ['value' => DbConfig::getSetting('videoManualApproval'), 'required' => 1]));
        }

        if (SysMod::isEnabled('webcam')) {
            $oForm->addElement(new Select(t('Webcam Pictures Manual Approval:'), 'webcam_picture_manual_approval', ['1' => t('Enable'), '0' => t('Disable')], ['description' => t('This approval mode is experimental, do not use it on production.'), 'value' => DbConfig::getSetting('webcamPictureManualApproval'), 'required' => 1]));
        }


        /********** Email **********/
        $oForm->addElement(new HTMLExternal('</div></div><div class="content" id="email"><div class="col-md-10"><h2 class="underline">' . t('Email Parameters') . '</h2>'));

        $oForm->addElement(new Textbox(t('Email Name:'), 'email_name', ['value' => DbConfig::getSetting('emailName'), 'required' => 1]));

        $oForm->addElement(new Email(t('Admin Email:'), 'admin_email', ['value' => DbConfig::getSetting('adminEmail'), 'required' => 1]));

        $oForm->addElement(new Email(t('Feedback Email:'), 'feedback_email', ['value' => DbConfig::getSetting('feedbackEmail'), 'required' => 1]));

        $oForm->addElement(new Email(t('Return Email:'), 'return_email', ['description' => 'Usually noreply@yoursite.com', 'value' => DbConfig::getSetting('returnEmail'), 'required' => 1]));


        /********** Security **********/
        $oForm->addElement(new HTMLExternal('</div></div><div class="content" id="security"><div class="col-md-10"><h2 class="underline">' . t('Security') . '</h2>'));

        $oForm->addElement(new HTMLExternal('<br /><h3 class="underline">' . t('Password') . '</h3>'));

        $oForm->addElement(new Number(t('Minimum password length:'), 'min_password_length', ['value' => DbConfig::getSetting('minPasswordLength'), 'required' => 1]));

        $oForm->addElement(new Number(t('Maximum password length:'), 'max_password_length', ['value' => DbConfig::getSetting('maxPasswordLength'), 'required' => 1]));

        $oForm->addElement(new HTMLExternal('<br /><h3 class="underline">' . t('Login Attempt Protection') . '</h3>'));

        $oForm->addElement(new Select(t('Blocking login attempts exceeded for Users:'), 'is_user_login_attempt', ['1' => t('Enable'), '0' => t('Disable')], ['value' => DbConfig::getSetting('isUserLoginAttempt'), 'required' => 1]));

        if ($bIsAffiliateEnabled) {
            $oForm->addElement(new Select(t('Blocking login attempts exceeded for Affiliates:'), 'is_affiliate_login_attempt', ['1' => t('Enable'), '0' => t('Disable')], ['value' => DbConfig::getSetting('isAffiliateLoginAttempt'), 'required' => 1]));
        }

        $oForm->addElement(new Select(t('Blocking login attempts exceeded for Admins:'), 'is_admin_login_attempt', ['1' => t('Enable'), '0' => t('Disable')], ['value' => DbConfig::getSetting('isAdminLoginAttempt'), 'required' => 1]));

        $oForm->addElement(new Number(t('Max number of login attempts before blocking for Users:'), 'max_user_login_attempts', ['value' => DbConfig::getSetting('maxUserLoginAttempts'), 'required' => 1]));

        if ($bIsAffiliateEnabled) {
            $oForm->addElement(new Number(t('Max number of login attempts before blocking for Affiliates:'), 'max_affiliate_login_attempts', ['value' => DbConfig::getSetting('maxAffiliateLoginAttempts'), 'required' => 1]));
        }

        $oForm->addElement(new Number(t('Max number of login attempts before blocking for Admins:'), 'max_admin_login_attempts', ['value' => DbConfig::getSetting('maxAdminLoginAttempts'), 'required' => 1]));

        $oForm->addElement(new Number(t('Time interval blocking for Users:'), 'login_user_attempt_time', ['description' => t('Time in minutes.'), 'value' => DbConfig::getSetting('loginUserAttemptTime'), 'required' => 1]));

        if ($bIsAffiliateEnabled) {
            $oForm->addElement(new Number(t('Time interval blocking for Affiliates:'), 'login_affiliate_attempt_time', ['description' => t('Time in minutes.'), 'value' => DbConfig::getSetting('loginAffiliateAttemptTime'), 'required' => 1]));
        }

        $oForm->addElement(new Number(t('Time interval blocking for Admins:'), 'login_admin_attempt_time', ['description' => t('Time in minutes.'), 'value' => DbConfig::getSetting('loginAdminAttemptTime'), 'required' => 1]));

        $oForm->addElement(new HTMLExternal('<br /><h3 class="underline">' . t('Various') . '</h3>'));

        $oForm->addElement(new Select(t('Send Abuse Reports by email:'), 'send_report_mail', ['1' => t('Yes'), '0' => t('No')], ['value' => DbConfig::getSetting('sendReportMail'), 'required' => 1]));

        $oForm->addElement(new Textbox(t('IP Restriction for Admin Panel Access:'), 'ip_login', ['description' => t('By entering <a href="%0%" title="Get your current IP address">your IP</a>, you will get a higher security and exclude all other people and bots that tried to login with another IP address even if the login is correct! Leave blank to disable this feature. Be careful, for using this feature you need to have a static IP (not a dynamic one). If you are not sure, please contact your ISP.', Ip::api()), 'value' => DbConfig::getSetting('ipLogin', '')]));

        $oForm->addElement(new Textbox(t('Indicate a word that will replace the banned word in <a href="%0%">the list</a>.', Uri::get(PH7_ADMIN_MOD, 'file', 'protectededit', 'app/configs/banned/word.txt', false)), 'ban_word_replace', ['value' => DbConfig::getSetting('banWordReplace'), 'required' => 1]));

        $oForm->addElement(new Select(t('Enable/Disable CSRF security tokens in forms:'), 'security_token_forms', ['1' => t('Enable'), '0' => t('Disable')], ['description' => t('Sometimes this protection can be annoying for users if there are not fast enough to fulfill the forms. However, if disabled, your website can be vulnerable on CSRF attacks in forms.'), 'value' => DbConfig::getSetting('securityToken'), 'required' => 1]));

        $oForm->addElement(new Number(t('CSRF token lifetime:'), 'security_token_lifetime', ['description' => t('Time in seconds.'), 'value' => DbConfig::getSetting('securityTokenLifetime'), 'required' => 1]));

        $oForm->addElement(new Select(t('Protect for Users against session cookies hijacking:'), 'is_user_session_ip_check', ['1' => t('Yes (recommended for security reasons)'), '0' => t('No')], ['description' => t('This protection can cause problems for logged in users with dynamic IPs. Please disable if their IP changes frequently during the session.'), 'value' => DbConfig::getSetting('isUserSessionIpCheck'), 'required' => 1]));

        if ($bIsAffiliateEnabled) {
            $oForm->addElement(new Select(t('Protect for Affiliates against session cookies hijacking:'), 'is_affiliate_session_ip_check', ['1' => t('Yes (recommended for security reasons)'), '0' => t('No')], ['description' => t('This protection can cause problems for affiliates with dynamic IPs. Please disable if their IP changes frequently during the session.'), 'value' => DbConfig::getSetting('isAffiliateSessionIpCheck'), 'required' => 1]));
        }

        $oForm->addElement(new Select(t('Protect for Admins against session cookies hijacking:'), 'is_admin_session_ip_check', ['1' => t('Yes (highly recommended for security reasons)'), '0' => t('No')], ['description' => t('This protection can cause problems for admins with dynamic IPs. Please disable if their IP changes frequently during the session.'), 'value' => DbConfig::getSetting('isAdminSessionIpCheck'), 'required' => 1]));

        $oForm->addElement(new Select(t('System against DDoS attacks:'), 'stop_DDoS', ['1' => t('Activate'), '0' => t('Deactivate')], ['description' => t('Enable it ONLY if you think your website has real DDoS attacks or if your server is highly overloaded.'), 'value' => DbConfig::getSetting('DDoS'), 'required' => 1]));


        /********** Spam **********/
        $oForm->addElement(new HTMLExternal('</div></div><div class="content" id="spam"><div class="col-md-10"><h2 class="underline">' . t('Spam') . '</h2>'));

        $oForm->addElement(new HTMLExternal('<br /><h3 class="underline">' . t('Time Delay') . '</h3>'));

        $oForm->addElement(new Number(t('Registration delay for Users:'), 'time_delay_user_registration', ['description' => t('Number of minutes that has to pass before a user with the same IP address can register again. Enter "0" to disable.'), 'value' => DbConfig::getSetting('timeDelayUserRegistration'), 'required' => 1]));

        if ($bIsAffiliateEnabled) {
            $oForm->addElement(new Number(t('Registration delay for Affiliates:'), 'time_delay_aff_registration', ['description' => t('Number of minutes that has to pass before an affiliate with the same IP address can register again. Enter "0" to disable.'), 'value' => DbConfig::getSetting('timeDelayAffRegistration'), 'required' => 1]));
        }

        if ($bIsNoteEnabled) {
            $oForm->addElement(new Number(t('Send Note delay:'), 'time_delay_send_note', ['description' => t('Number of minutes for the same user to post a new note.'), 'value' => DbConfig::getSetting('timeDelaySendNote'), 'required' => 1]));
        }

        if ($bIsMailEnabled) {
            $oForm->addElement(new Number(t('Send Mail delay:'), 'time_delay_send_mail', ['description' => t('Number of minutes for the same user can send a new email.'), 'value' => DbConfig::getSetting('timeDelaySendMail'), 'required' => 1]));
        }
        $oForm->addElement(new Number(t('Send Comment delay:'), 'time_delay_send_comment', ['description' => t('Number of minutes for the same user can send a new comment.'), 'value' => DbConfig::getSetting('timeDelaySendComment'), 'required' => 1]));

        if ($bIsForumEnabled) {
            $oForm->addElement(new Number(t('Send Forum Topic delay:'), 'time_delay_send_forum_topic', ['description' => t('Number of minutes for the same user can send a new topic in the forum.'), 'value' => DbConfig::getSetting('timeDelaySendForumTopic'), 'required' => 1]));

            $oForm->addElement(new Number(t('Send Forum Message delay:'), 'time_delay_send_forum_msg', ['description' => t('Number of minutes for the same user can send a reply message in the same topic.'), 'value' => DbConfig::getSetting('timeDelaySendForumMsg'), 'required' => 1]));
        }

        $oForm->addElement(new HTMLExternal('<br /><h3 class="underline">' . t('Captcha') . '</h3>'));

        $oForm->addElement(new Select(t('Captcha Complexity:'), 'captcha_complexity', [Captcha::COMPLEXITY_LOW, Captcha::COMPLEXITY_MEDIUM, Captcha::COMPLEXITY_HIGH], ['value' => DbConfig::getSetting('captchaComplexity'), 'required' => 1]));

        $oForm->addElement(new Select(t('Captcha Case Sensitive:'), 'captcha_case_sensitive', ['1' => t('Yes'), '0' => t('No')], ['value' => DbConfig::getSetting('captchaCaseSensitive'), 'required' => 1]));

        $oForm->addElement(new Select(t('Captcha for User Signup Form:'), 'is_captcha_user_signup', ['1' => t('Activate'), '0' => t('Deactivate')], ['value' => DbConfig::getSetting('isCaptchaUserSignup'), 'required' => 1]));

        if ($bIsAffiliateEnabled) {
            $oForm->addElement(new Select(t('Captcha for Affiliate Signup Form:'), 'is_captcha_affiliate_signup', ['1' => t('Activate'), '0' => t('Deactivate')], ['value' => DbConfig::getSetting('isCaptchaAffiliateSignup'), 'required' => 1]));
        }

        if ($bIsMailEnabled) {
            $oForm->addElement(new Select(t('Captcha for sending Messages between users:'), 'is_captcha_mail', ['1' => t('Activate'), '0' => t('Deactivate')], ['value' => DbConfig::getSetting('isCaptchaMail'), 'required' => 1]));
        }

        $oForm->addElement(new Select(t('Captcha for adding a Comment:'), 'is_captcha_comment', ['1' => t('Activate'), '0' => t('Deactivate')], ['value' => DbConfig::getSetting('isCaptchaComment'), 'required' => 1]));

        if ($bIsForumEnabled) {
            $oForm->addElement(new Select(t('Captcha for adding or reply a message in the Forum:'), 'is_captcha_forum', ['1' => t('Activate'), '0' => t('Deactivate')], ['value' => DbConfig::getSetting('isCaptchaForum'), 'required' => 1]));
        }

        if ($bIsNoteEnabled) {
            $oForm->addElement(new Select(t('Captcha for adding a User Post Note:'), 'is_captcha_note', ['1' => t('Activate'), '0' => t('Deactivate')], ['value' => DbConfig::getSetting('isCaptchaNote'), 'required' => 1]));
        }

        $oForm->addElement(new HTMLExternal('<br /><h3 class="underline">' . t('Pruning') . '</h3>'));

        $oForm->addElement(new Number(t('Delete old Messages:'), 'clean_msg', ['description' => t('Delete messages older than X days. 0 to disable.'), 'value' => DbConfig::getSetting('cleanMsg'), 'required' => 1]));

        $oForm->addElement(new Number(t('Delete old Comments:'), 'clean_comment', ['description' => t('Delete comments older than X days. 0 to disable.'), 'value' => DbConfig::getSetting('cleanComment'), 'required' => 1]));

        $oForm->addElement(new Number(t('Delete old IM Messages:'), 'clean_messenger', ['description' => t('Delete IM messages older than X days. 0 to disable.'), 'value' => DbConfig::getSetting('cleanMessenger'), 'required' => 1]));


        /********** API **********/
        $oForm->addElement(
            new HTMLExternal(
                '</div></div><div class="content" id="api"><div class="col-md-10"><h2 class="underline">' . t('API') . '</h2>'
            )
        );

        if (SysMod::isEnabled('map')) {
            $sGoogleApiKeyDesc = t('You can get your key <a href="%0%">here</a>. Then, select "<strong>Google Maps JavaScript API</strong>" for "<em>Which API are you using</em>" and "<strong>Web browser (Javascript)</strong>" for "<em>Where will you be calling the API from</em>", then you will get your API key to paste here. ', self::GOOGLE_API_KEY_URL);
            $oForm->addElement(new Textbox(t('Google Maps API Key:'), 'google_api_key', ['description' => $sGoogleApiKeyDesc, 'value' => DbConfig::getSetting('googleApiKey', '')]));
        }

        $oForm->addElement(new Url(t('IP API:'), 'ip_api', ['description' => t('The URL must end with a slash.'), 'value' => DbConfig::getSetting('ipApi'), 'required' => 1]));

        if (SysMod::isEnabled('chat')) {
            $oForm->addElement(new Url(t('Chat API:'), 'chat_api', ['description' => t('Documentation: <a href="%0%">Change the default chat service by your real one</a>.<br /> <small>Parsing tags are permitted (e.g. #!http://api.your-service-chat.com/?url=%0%&name=%1%!#).</small>', self::CHANGE_CHAT_DOC_URL, '<strong>%site_url%</strong>', '<strong>%site_name%</strong>'), 'value' => DbConfig::getSetting('chatApi'), 'required' => 1]));
        }

        if (SysMod::isEnabled('chatroulette')) {
            $oForm->addElement(new Url(t('Chatroulette API:'), 'chatroulette_api', ['description' => t('Documentation: <a href="%0%">Change the default chatroulette provider by yours</a>.<br /> <small>Parsing tags are permitted (e.g. #!http://api.your-service-chat.com/?url=%0%&name=%1%!#).</small>', self::CHANGE_CHAT_DOC_URL, '<strong>%site_url%</strong>', '<strong>%site_name%</strong>'), 'value' => DbConfig::getSetting('chatrouletteApi'), 'required' => 1]));
        }


        /********** Automation **********/
        $oForm->addElement(new HTMLExternal('</div></div><div class="content" id="automation"><div class="col-md-10"><h2 class="underline">' . t('Automation') . '</h2>'));

        $oForm->addElement(new Textbox(t('Secret word for the cron URL:'), 'cron_security_hash', ['description' => t('Your very secret word for the cron URL. It will be used for running automated cron jobs.'), 'value' => DbConfig::getSetting('cronSecurityHash'), 'required' => 1, 'validation' => new Str(1, 64)]));

        $oForm->addElement(new Number(t('User inactivity timeout:'), 'user_timeout', ['description' => t('The number of minutes that a member becomes inactive (offline).'), 'value' => DbConfig::getSetting('userTimeout'), 'required' => 1]));


        $oForm->addElement(new HTMLExternal('</div></div><script src="' . PH7_URL_STATIC . PH7_JS . 'tabs.js"></script><script>tabs(\'p\', [\'general\',\'icon\',\'registration\',\'pic_vid\',\'moderation\',\'email\',\'security\',\'spam\',\'design\',\'api\',\'automation\']);</script>'));
        $oForm->addElement(new Button(t('Save'), 'submit', ['icon' => 'check']));

        $oForm->render();
    }

    /**
     * @param File $oFile
     *
     * @return array
     */
    private static function getTpls(File $oFile)
    {
        $aTpls = [];

        $aTplIds = $oFile->getDirList(PH7_PATH_TPL);
        foreach ($aTplIds as $sTpl) {
            $aTpls[$sTpl] = ucfirst($sTpl);
        }

        return $aTpls;
    }

    /**
     * @param File $oFile
     *
     * @return array
     */
    private static function getLangs(File $oFile)
    {
        $aLangs = [];

        $aLangIds = $oFile->getDirList(PH7_PATH_APP_LANG);
        foreach ($aLangIds as $sLang) {
            $sAbbrLang = Lang::getIsoCode($sLang);
            $aLangs[$sLang] = t($sAbbrLang) . ' (' . $sLang . ')';
        }

        return $aLangs;
    }

    /**
     * @return array
     */
    private static function getDefMods()
    {
        $aMods = [];

        foreach (self::getActivatableDefMods() as $sMod) {
            // Skip the disabled module (would be impossible to set a disabled module as the default one)
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
        $aGroupNames = [];

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
