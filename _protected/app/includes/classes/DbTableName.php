<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2018-2021, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / App / Include / Class
 */

namespace PH7;

final class DbTableName
{
    public const ADMIN = 'admins';
    public const MEMBER = 'members';
    public const MEMBERSHIP = 'memberships';
    public const AFFILIATE = 'affiliates';
    public const SUBSCRIBER = 'subscribers';
    public const MEMBER_INFO = 'members_info';
    public const AFFILIATE_INFO = 'affiliates_info';
    public const MEMBER_PRIVACY = 'members_privacy';
    public const MEMBER_NOTIFICATION = 'members_notifications';
    public const MEMBER_COUNTRY = 'members_countries';
    public const AFFILIATE_COUNTRY = 'affiliates_countries';
    public const BLOCK_IP = 'block_ip';
    public const BLOCK_COUNTRY = 'block_countries';
    public const AD = 'ads';
    public const AD_AFFILIATE = 'ads_affiliates';
    public const PICTURE = 'pictures';
    public const VIDEO = 'videos';
    public const ALBUM_PICTURE = 'albums_pictures';
    public const ALBUM_VIDEO = 'albums_videos';
    public const ANALYTIC_API = 'analytics_api';
    public const BLOG = 'blogs';
    public const BLOG_CATEGORY = 'blogs_categories';
    public const BLOG_DATA_CATEGORY = 'blogs_data_categories';
    public const NOTE = 'notes';
    public const NOTE_CATEGORY = 'notes_categories';
    public const NOTE_DATA_CATEGORY = 'notes_data_categories';
    public const COMMENT_BLOG = 'comments_blog';
    public const COMMENT_NOTE = 'comments_note';
    public const COMMENT_PICTURE = 'comments_picture';
    public const COMMENT_VIDEO = 'comments_video';
    public const COMMENT_PROFILE = 'comments_profile';
    public const FORUM = 'forums';
    public const FORUM_CATEGORY = 'forums_categories';
    public const FORUM_TOPIC = 'forums_topics';
    public const FORUM_MESSAGE = 'forums_messages';
    public const LANGUAGE_INFO = 'languages_info';
    public const LIKE = 'likes';
    public const LOG_ERROR = 'log_error';
    public const ADMIN_ATTEMPT_LOGIN = 'admins_attempts_login';
    public const MEMBER_ATTEMPT_LOGIN = 'members_attempts_login';
    public const AFFILIATE_ATTEMPT_LOGIN = 'affiliates_attempts_login';
    public const ADMIN_LOG_LOGIN = 'admins_log_login';
    public const MEMBER_LOG_LOGIN = 'members_log_login';
    public const AFFILIATE_LOG_LOGIN = 'affiliates_log_login';
    public const ADMIN_LOG_SESS = 'admins_log_sess';
    public const MEMBER_LOG_SESS = 'members_log_sess';
    public const AFFILIATE_LOG_SESS = 'affiliates_log_sess';
    public const MEMBER_BACKGROUND = 'members_background';
    public const MEMBER_WHO_VIEW = 'members_who_views';
    public const MEMBER_FRIEND = 'members_friends';
    public const MEMBER_WALL = 'members_wall';
    public const MESSAGE = 'messages';
    public const MESSENGER = 'messenger';
    public const META_MAIN = 'meta_main';
    public const SYS_MOD_ENABLED = 'sys_mods_enabled';
    public const REPORT = 'report';
    public const SETTING = 'settings';
    public const STATIC_FILE = 'static_files';
    public const CUSTOM_CODE = 'custom_code';

    public const USER_TABLES = [
        self::ADMIN,
        self::MEMBER,
        self::AFFILIATE
    ];
}
