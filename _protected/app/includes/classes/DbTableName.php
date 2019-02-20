<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / Include / Class
 */

namespace PH7;

final class DbTableName
{
    const ADMIN = 'admins';
    const MEMBER = 'members';
    const MEMBERSHIP = 'memberships';
    const AFFILIATE = 'affiliates';
    const SUBSCRIBER = 'subscribers';
    const MEMBER_INFO = 'members_info';
    const AFFILIATE_INFO = 'affiliates_info';
    const MEMBER_PRIVACY = 'members_privacy';
    const MEMBER_NOTIFICATION = 'members_notifications';
    const MEMBER_COUNTRY = 'members_countries';
    const AFFILIATE_COUNTRY = 'affiliates_countries';
    const BLOCK_IP = 'block_ip';
    const BLOCK_COUNTRY = 'block_countries';
    const AD = 'ads';
    const AD_AFFILIATE = 'ads_affiliates';
    const PICTURE = 'pictures';
    const VIDEO = 'videos';
    const ALBUM_PICTURE = 'albums_pictures';
    const ALBUM_VIDEO = 'albums_videos';
    const ANALYTIC_API = 'analytics_api';
    const BLOG = 'blogs';
    const BLOG_CATEGORY = 'blogs_categories';
    const BLOG_DATA_CATEGORY = 'blogs_data_categories';
    const NOTE = 'notes';
    const NOTE_CATEGORY = 'notes_categories';
    const NOTE_DATA_CATEGORY = 'notes_data_categories';
    const COMMENT_BLOG = 'comments_blog';
    const COMMENT_NOTE = 'comments_note';
    const COMMENT_PICTURE = 'comments_picture';
    const COMMENT_VIDEO = 'comments_video';
    const COMMENT_GAME = 'comments_game';
    const COMMENT_PROFILE = 'comments_profile';
    const FORUM = 'forums';
    const FORUM_CATEGORY = 'forums_categories';
    const FORUM_TOPIC = 'forums_topics';
    const FORUM_MESSAGE = 'forums_messages';
    const LANGUAGE_INFO = 'languages_info';
    const LIKE = 'likes';
    const LOG_ERROR = 'log_error';
    const ADMIN_ATTEMPT_LOGIN = 'admins_attempts_login';
    const MEMBER_ATTEMPT_LOGIN = 'members_attempts_login';
    const AFFILIATE_ATTEMPT_LOGIN = 'affiliates_attempts_login';
    const ADMIN_LOG_LOGIN = 'admins_log_login';
    const MEMBER_LOG_LOGIN = 'members_log_login';
    const AFFILIATE_LOG_LOGIN = 'affiliates_log_login';
    const ADMIN_LOG_SESS = 'admins_log_sess';
    const MEMBER_LOG_SESS = 'members_log_sess';
    const AFFILIATE_LOG_SESS = 'affiliates_log_sess';
    const MEMBER_BACKGROUND = 'members_background';
    const MEMBER_WHO_VIEW = 'members_who_views';
    const MEMBER_FRIEND = 'members_friends';
    const MEMBER_WALL = 'members_wall';
    const MESSAGE = 'messages';
    const MESSENGER = 'messenger';
    const META_MAIN = 'meta_main';
    const SYS_MOD_ENABLED = 'sys_mods_enabled';
    const MODULE = 'modules';
    const REPORT = 'report';
    const SETTING = 'settings';
    const STATIC_FILE = 'static_files';
    const CUSTOM_CODE = 'custom_code';
    const GAME = 'games';
    const GAME_CATEGORY = 'games_categories';

    const USER_TABLES = [
        self::ADMIN,
        self::MEMBER,
        self::AFFILIATE
    ];
}
