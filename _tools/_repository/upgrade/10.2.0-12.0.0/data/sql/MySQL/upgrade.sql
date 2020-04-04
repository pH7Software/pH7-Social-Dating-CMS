--
-- Author:        Pierre-Henry Soria <hi@ph7.me>
-- Copyright:     (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License
--

-- Rename table names to new names
ALTER TABLE ph7_Admins RENAME ph7_admins;
ALTER TABLE ph7_Memberships RENAME ph7_memberships;
ALTER TABLE ph7_Members RENAME ph7_members;
ALTER TABLE ph7_MembersInfo RENAME ph7_members_info;
ALTER TABLE ph7_MembersPrivacy RENAME ph7_members_privacy;
ALTER TABLE ph7_MembersNotifications RENAME ph7_members_notifications;
ALTER TABLE ph7_Affiliates RENAME ph7_affiliates;
ALTER TABLE ph7_AffiliatesInfo RENAME ph7_affiliates_info;
ALTER TABLE ph7_BlockIp RENAME ph7_block_ip;
ALTER TABLE ph7_Ads RENAME ph7_ads;
ALTER TABLE ph7_AdsAffiliates RENAME ph7_ads_affiliates;
ALTER TABLE ph7_AlbumsPictures RENAME ph7_albums_pictures;
ALTER TABLE ph7_AlbumsVideos RENAME ph7_albums_videos;
ALTER TABLE ph7_Pictures RENAME ph7_pictures;
ALTER TABLE ph7_Videos RENAME ph7_videos;
ALTER TABLE ph7_AnalyticsApi RENAME ph7_analytics_api;
ALTER TABLE ph7_Blogs RENAME ph7_blogs;
ALTER TABLE ph7_BlogsCategories RENAME ph7_blogs_categories;
ALTER TABLE ph7_BlogsDataCategories RENAME ph7_blogs_data_categories;
ALTER TABLE ph7_Notes RENAME ph7_notes;
ALTER TABLE ph7_NotesCategories RENAME ph7_notes_categories;
ALTER TABLE ph7_NotesDataCategories RENAME ph7_notes_data_categories;
ALTER TABLE ph7_CommentsBlog RENAME ph7_comments_blog;
ALTER TABLE ph7_CommentsNote RENAME ph7_comments_note;
ALTER TABLE ph7_CommentsPicture RENAME ph7_comments_picture;
ALTER TABLE ph7_CommentsVideo RENAME ph7_comments_video;
ALTER TABLE ph7_CommentsGame RENAME ph7_comments_game;
ALTER TABLE ph7_CommentsProfile RENAME ph7_comments_profile;
ALTER TABLE ph7_ForumsCategories RENAME ph7_forums_categories;
ALTER TABLE ph7_Forums RENAME ph7_forums;
ALTER TABLE ph7_ForumsTopics RENAME ph7_forums_topics;
ALTER TABLE ph7_ForumsMessages RENAME ph7_forums_messages;
ALTER TABLE ph7_LanguagesInfo RENAME ph7_languages_info;
ALTER TABLE ph7_Likes RENAME ph7_likes;
ALTER TABLE ph7_LogError RENAME ph7_log_error;
ALTER TABLE ph7_AdminsAttemptsLogin RENAME ph7_admins_attempts_login;
ALTER TABLE ph7_MembersAttemptsLogin RENAME ph7_members_attempts_login;
ALTER TABLE ph7_AffiliatesAttemptsLogin RENAME ph7_affiliates_attempts_login;
ALTER TABLE ph7_AdminsLogLogin RENAME ph7_admins_log_login;
ALTER TABLE ph7_MembersLogLogin RENAME ph7_members_log_login;
ALTER TABLE ph7_AffiliatesLogLogin RENAME ph7_affiliates_log_login;
ALTER TABLE ph7_AdminsLogSess RENAME ph7_admins_log_sess;
ALTER TABLE ph7_MembersLogSess RENAME ph7_members_log_sess;
ALTER TABLE ph7_AffiliatesLogSess RENAME ph7_affiliates_log_sess;
ALTER TABLE ph7_MembersBackground RENAME ph7_members_background;
ALTER TABLE ph7_membersWhoViews RENAME ph7_members_who_views;
ALTER TABLE ph7_MembersFriends RENAME ph7_members_friends;
ALTER TABLE ph7_MembersWall RENAME ph7_members_wall;
ALTER TABLE ph7_Messages RENAME ph7_messages;
ALTER TABLE ph7_Messenger RENAME ph7_messenger;
ALTER TABLE ph7_MetaMain RENAME ph7_meta_main;
ALTER TABLE ph7_SysModsEnabled RENAME ph7_sys_mods_enabled;
ALTER TABLE ph7_Modules RENAME ph7_modules;
ALTER TABLE ph7_Report RENAME ph7_report;
ALTER TABLE ph7_Settings RENAME ph7_settings;
ALTER TABLE ph7_Subscribers RENAME ph7_subscribers;
ALTER TABLE ph7_TopMenus RENAME ph7_top_menus;
ALTER TABLE ph7_BottomMenus RENAME ph7_bottom_menus;
ALTER TABLE ph7_StaticFiles RENAME ph7_static_files;
ALTER TABLE ph7_License RENAME ph7_license;
ALTER TABLE ph7_CustomCode RENAME ph7_custom_code;
ALTER TABLE ph7_Games RENAME ph7_games;
ALTER TABLE ph7_GamesCategories RENAME ph7_games_categories;


-- Add new module name into ph7_sys_mods_enabled
INSERT INTO ph7_sys_mods_enabled (moduleTitle, folderName, premiumMod, enabled) VALUES
('Cool Profile Page', 'cool-profile-page', '0', '0');


-- Add new fields into settings table
INSERT INTO ph7_settings (settingName, settingValue, description, settingGroup) VALUES
('captchaComplexity', 5, 'number of captcha complexity', 'spam'),
('captchaCaseSensitive', 1, '1 to enable captcha case sensitive | 0 to enable', 'spam');


-- Update pH7CMS's SQL schema version
UPDATE ph7_modules SET version = '1.4.2' WHERE vendorName = 'pH7CMS';
