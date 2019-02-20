<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

// Default contents value
$sHtml = '';

/*** Begin Contents ***/

$sHtml .= '<p>' . t('Done!') . '</p>';
$sHtml .= '<p class="red">' . t('Now you need to delete the following files via FTP or SSH:') . '</p>';
$sHtml .= '<pre>';
$sHtml .= PH7_PATH_FRAMEWORK . 'Mvc' . PH7_DS . 'Request' . PH7_DS . 'HttpRequest.class.php' . "\n";
$sHtml .= PH7_PATH_FRAMEWORK . 'Mvc' . PH7_DS . 'Router' . PH7_DS . 'UriRoute.class.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'core' . PH7_DS . 'class' . PH7_DS . 'AdminCore.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'core' . PH7_DS . 'class' . PH7_DS . 'AdsCore.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'core' . PH7_DS . 'class' . PH7_DS . 'AffiliateCore.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'core' . PH7_DS . 'class' . PH7_DS . 'BirthdayCore.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'core' . PH7_DS . 'class' . PH7_DS . 'CommentCore.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'core' . PH7_DS . 'class' . PH7_DS . 'NewsFeedCore.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'core' . PH7_DS . 'class' . PH7_DS . 'PermissionCore.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'core' . PH7_DS . 'class' . PH7_DS . 'PictureCore.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'core' . PH7_DS . 'class' . PH7_DS . 'RegistrationCore.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'core' . PH7_DS . 'class' . PH7_DS . 'Security.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'core' . PH7_DS . 'class' . PH7_DS . 'UserCore.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'core' . PH7_DS . 'class' . PH7_DS . 'VideoCore.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'core' . PH7_DS . 'class' . PH7_DS . 'WriteCore.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'core' . PH7_DS . 'class' . PH7_DS . 'design' . PH7_DS . 'AvatarDesignCore.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'core' . PH7_DS . 'class' . PH7_DS . 'design' . PH7_DS . 'CommentDesignCore.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'core' . PH7_DS . 'class' . PH7_DS . 'design' . PH7_DS . 'LostPwdDesignCore.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'core' . PH7_DS . 'class' . PH7_DS . 'design' . PH7_DS . 'RatingDesignCore.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'core' . PH7_DS . 'class' . PH7_DS . 'design' . PH7_DS . 'UserDesignCore.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'core' . PH7_DS . 'class' . PH7_DS . 'design' . PH7_DS . 'VideoDesignCore.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'core' . PH7_DS . 'class' . PH7_DS . 'design' . PH7_DS . 'XmlDesignCore.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'core' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'ChangePasswordCoreFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'core' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'ConfigFileCoreFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'core' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'DeleteUserCoreFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'core' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'ResendActivationCoreFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'globals' . PH7_DS . PH7_VIEWS . PH7_DEFAULT_THEME . PH7_DS . 'errors' . PH7_DS . 'error-500.html.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'globals' . PH7_DS . PH7_VIEWS . PH7_DEFAULT_THEME . PH7_DS . 'errors' . PH7_DS . 'exception.html.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'globals' . PH7_DS . PH7_VIEWS . PH7_DEFAULT_THEME . PH7_DS . 'mails' . PH7_DS . 'sys' . PH7_DS . 'core' . PH7_DS . 'alert_login_attempt.tpl' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'globals' . PH7_DS . PH7_VIEWS . PH7_DEFAULT_THEME . PH7_DS . 'mails' . PH7_DS . 'sys' . PH7_DS . 'core' . PH7_DS . 'delete_account.tpl' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'globals' . PH7_DS . PH7_VIEWS . PH7_DEFAULT_THEME . PH7_DS . 'mails' . PH7_DS . 'sys' . PH7_DS . 'core' . PH7_DS . 'moderate_registration.tpl' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'globals' . PH7_DS . PH7_VIEWS . PH7_DEFAULT_THEME . PH7_DS . 'mails' . PH7_DS . 'sys' . PH7_DS . 'core' . PH7_DS . 'resend_activation.tpl' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'globals' . PH7_DS . PH7_VIEWS . PH7_DEFAULT_THEME . PH7_DS . 'mails' . PH7_DS . 'sys' . PH7_DS . 'mod' . PH7_DS . 'affiliate' . PH7_DS . 'registration.tpl' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'globals' . PH7_DS . PH7_VIEWS . PH7_DEFAULT_THEME . PH7_DS . 'mails' . PH7_DS . 'sys' . PH7_DS . 'mod' . PH7_DS . 'contact' . PH7_DS . 'contact_form.tpl' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'globals' . PH7_DS . PH7_VIEWS . PH7_DEFAULT_THEME . PH7_DS . 'mails' . PH7_DS . 'sys' . PH7_DS . 'mod' . PH7_DS . 'invite' . PH7_DS . 'invitation.tpl' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'globals' . PH7_DS . PH7_VIEWS . PH7_DEFAULT_THEME . PH7_DS . 'mails' . PH7_DS . 'sys' . PH7_DS . 'mod' . PH7_DS . 'lost-password' . PH7_DS . 'confirm-lost-password.tpl' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'globals' . PH7_DS . PH7_VIEWS . PH7_DEFAULT_THEME . PH7_DS . 'mails' . PH7_DS . 'sys' . PH7_DS . 'mod' . PH7_DS . 'lost-password' . PH7_DS . 'recover_password.tpl' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'globals' . PH7_DS . PH7_VIEWS . PH7_DEFAULT_THEME . PH7_DS . 'mails' . PH7_DS . 'sys' . PH7_DS . 'mod' . PH7_DS . 'mail' . PH7_DS . 'new_msg.tpl' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'globals' . PH7_DS . PH7_VIEWS . PH7_DEFAULT_THEME . PH7_DS . 'mails' . PH7_DS . 'sys' . PH7_DS . 'mod' . PH7_DS . 'newsletter' . PH7_DS . 'msg.tpl' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'globals' . PH7_DS . PH7_VIEWS . PH7_DEFAULT_THEME . PH7_DS . 'mails' . PH7_DS . 'sys' . PH7_DS . 'mod' . PH7_DS . 'newsletter' . PH7_DS . 'registration.tpl' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'globals' . PH7_DS . PH7_VIEWS . PH7_DEFAULT_THEME . PH7_DS . 'mails' . PH7_DS . 'sys' . PH7_DS . 'mod' . PH7_DS . 'report' . PH7_DS . 'abuse.tpl' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'globals' . PH7_DS . PH7_VIEWS . PH7_DEFAULT_THEME . PH7_DS . 'mails' . PH7_DS . 'sys' . PH7_DS . 'mod' . PH7_DS . 'user' . PH7_DS . 'account_registration.tpl' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'globals' . PH7_DS . PH7_VIEWS . PH7_DEFAULT_THEME . PH7_DS . 'mails' . PH7_DS . 'sys' . PH7_DS . 'mod' . PH7_DS . 'user' . PH7_DS . 'birthday.tpl' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'globals' . PH7_DS . PH7_VIEWS . PH7_DEFAULT_THEME . PH7_DS . 'mails' . PH7_DS . 'sys' . PH7_DS . 'mod' . PH7_DS . 'user' . PH7_DS . 'friend_request.tpl' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'globals' . PH7_DS . PH7_VIEWS . PH7_DEFAULT_THEME . PH7_DS . 'others' . PH7_DS . 'banned.html.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . 'globals' . PH7_DS . PH7_VIEWS . PH7_DEFAULT_THEME . PH7_DS . 'others' . PH7_DS . 'maintenance.html.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'admin123' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'AddAdminFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'admin123' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'AddUserFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'admin123' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'AdsFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'admin123' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'AnalyticsApiFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'admin123' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'EditFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'admin123' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'ImportUserFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'admin123' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'LoginFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'admin123' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'MetaMainFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'admin123' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'ProtectedFileFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'admin123' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'PublicFileFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'admin123' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'ScriptFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'admin123' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'SettingFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'admin123' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'StyleFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'admin123' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'UpdateAdsFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'affiliate' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'AddAffiliateFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'affiliate' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'AdsAdminFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'affiliate' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'BankFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'affiliate' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'EditFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'affiliate' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'JoinFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'affiliate' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'LoginFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'blog' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'AdminBlogFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'blog' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'EditAdminBlogFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'comment' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'CommentFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'comment' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'EditCommentFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'contact' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'ContactFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'field' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'AddFieldFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'field' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'EditFieldFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'forum' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'CategoryFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'forum' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'EditCategoryFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'forum' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'EditForumFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'forum' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'EditMsgFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'forum' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'EditReplyMsgFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'forum' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'ForumFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'forum' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'MsgFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'forum' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'ReplyMsgFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'game' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'AdminEditFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'game' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'AdminFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'invite' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'InviteFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'lost-password' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'ForgotPasswordFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'mail' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'MailFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'newsletter' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'MsgFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'newsletter' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'SubscriptionFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'note' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'EditNoteFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'note' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'NoteFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'payment' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'EditMembershipFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'payment' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'MembershipFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'picture' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'AlbumFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'picture' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'EditAlbumFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'picture' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'EditPictureFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'picture' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'PictureFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'report' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'ReportFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'user' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'AvatarFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'user' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'DesignFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'user' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'EditFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'user' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'EditWallFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'user' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'JoinFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'user' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'LoginFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'user' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'NotificationFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'user' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'PrivacyFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'user' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'WallFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'video' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'AlbumFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'video' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'EditAlbumFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'video' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'EditVideoFormProcessing.php' . "\n";
$sHtml .= PH7_PATH_APP . PH7_SYS . PH7_MOD . 'video' . PH7_DS . PH7_FORMS . 'processing' . PH7_DS . 'VideoFormProcessing.php';
$sHtml .= '</pre>';
$sHtml .= '<p>' . t('Good luck :-)') . '</p>';

/*** End Contents ***/

// Output!
return $sHtml;
