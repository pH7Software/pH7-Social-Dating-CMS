--
-- Author:        Pierre-Henry Soria <hi@ph7.me>
-- Copyright:     (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License
--

-- Rename table names to new names
ALTER TABLE ph7_Admins RENAME TO ph7_admins;
ALTER TABLE ph7_Memberships RENAME TO ph7_memberships;
ALTER TABLE ph7_Members RENAME TO ph7_members;
ALTER TABLE ph7_MembersInfo RENAME TO ph7_members_info;
ALTER TABLE ph7_MembersPrivacy RENAME TO ph7_members_privacy;
ALTER TABLE ph7_MembersNotifications RENAME TO ph7_members_notifications;
ALTER TABLE ph7_Affiliates RENAME TO ph7_affiliates;
ALTER TABLE ph7_AffiliatesInfo RENAME TO ph7_affiliates_info;
ALTER TABLE ph7_BlockIp RENAME TO ph7_block_ip;
ALTER TABLE ph7_Ads RENAME TO ph7_ads;
ALTER TABLE ph7_AdsAffiliates RENAME TO ph7_ads_affiliates;
ALTER TABLE ph7_AlbumsPictures RENAME TO ph7_albums_pictures;
ALTER TABLE ph7_AlbumsVideos RENAME TO ph7_albums_videos;
ALTER TABLE ph7_Pictures RENAME TO ph7_pictures;
ALTER TABLE ph7_Videos RENAME TO ph7_videos;
ALTER TABLE ph7_AnalyticsApi RENAME TO ph7_analytics_api;
ALTER TABLE ph7_Blogs RENAME TO ph7_blogs;
ALTER TABLE ph7_BlogsCategories RENAME TO ph7_blogs_categories;
ALTER TABLE ph7_BlogsDataCategories RENAME TO ph7_blogs_data_categories;
ALTER TABLE ph7_Notes RENAME TO ph7_notes;
ALTER TABLE ph7_NotesCategories RENAME TO ph7_notes_categories;
ALTER TABLE ph7_NotesDataCategories RENAME TO ph7_notes_data_categories;
ALTER TABLE ph7_CommentsBlog RENAME TO ph7_comments_blog;
ALTER TABLE ph7_CommentsNote RENAME TO ph7_comments_note;
ALTER TABLE ph7_CommentsPicture RENAME TO ph7_comments_picture;
ALTER TABLE ph7_CommentsVideo RENAME TO ph7_comments_video;
ALTER TABLE ph7_CommentsGame RENAME TO ph7_comments_game;
ALTER TABLE ph7_CommentsProfile RENAME TO ph7_comments_profile;
ALTER TABLE ph7_ForumsCategories RENAME TO ph7_forums_categories;
ALTER TABLE ph7_Forums RENAME TO ph7_forums;
ALTER TABLE ph7_ForumsTopics RENAME TO ph7_forums_topics;
ALTER TABLE ph7_ForumsMessages RENAME TO ph7_forums_messages;
ALTER TABLE ph7_LanguagesInfo RENAME TO ph7_languages_info;
ALTER TABLE ph7_Likes RENAME TO ph7_likes;
ALTER TABLE ph7_LogError RENAME TO ph7_log_error;
ALTER TABLE ph7_AdminsAttemptsLogin RENAME TO ph7_admins_attempts_login;
ALTER TABLE ph7_MembersAttemptsLogin RENAME TO ph7_members_attempts_login;
ALTER TABLE ph7_AffiliatesAttemptsLogin RENAME TO ph7_affiliates_attempts_login;
ALTER TABLE ph7_AdminsLogLogin RENAME TO ph7_admins_log_login;
ALTER TABLE ph7_MembersLogLogin RENAME TO ph7_members_log_login;
ALTER TABLE ph7_AffiliatesLogLogin RENAME TO ph7_affiliates_log_login;
ALTER TABLE ph7_AdminsLogSess RENAME TO ph7_admins_log_sess;
ALTER TABLE ph7_MembersLogSess RENAME TO ph7_members_log_sess;
ALTER TABLE ph7_AffiliatesLogSess RENAME TO ph7_affiliates_log_sess;
ALTER TABLE ph7_MembersBackground RENAME TO ph7_members_background;
ALTER TABLE ph7_membersWhoViews RENAME TO ph7_members_who_views;
ALTER TABLE ph7_MembersFriends RENAME TO ph7_members_friends;
ALTER TABLE ph7_MembersWall RENAME TO ph7_members_wall;
ALTER TABLE ph7_Messages RENAME TO ph7_messages;
ALTER TABLE ph7_Messenger RENAME TO ph7_messenger;
ALTER TABLE ph7_MetaMain RENAME TO ph7_meta_main;
ALTER TABLE ph7_SysModsEnabled RENAME TO ph7_sys_mods_enabled;
ALTER TABLE ph7_Modules RENAME TO ph7_modules;
ALTER TABLE ph7_Report RENAME TO ph7_report;
ALTER TABLE ph7_Settings RENAME TO ph7_settings;
ALTER TABLE ph7_Subscribers RENAME TO ph7_subscribers;
ALTER TABLE ph7_TopMenus RENAME TO ph7_top_menus;
ALTER TABLE ph7_BottomMenus RENAME TO ph7_bottom_menus;
ALTER TABLE ph7_StaticFiles RENAME TO ph7_static_files;
ALTER TABLE ph7_License RENAME TO ph7_license;
ALTER TABLE ph7_CustomCode RENAME TO ph7_custom_code;
ALTER TABLE ph7_Games RENAME TO ph7_games;
ALTER TABLE ph7_GamesCategories RENAME TO ph7_games_categories;


-- Add new module name into ph7_sys_mods_enabled
INSERT INTO ph7_sys_mods_enabled (moduleTitle, folderName, premiumMod, enabled) VALUES
('Cool Profile Page', 'cool-profile-page', '0', '0');


-- Add new fields into settings table
INSERT INTO ph7_settings (settingName, settingValue, description, settingGroup) VALUES
('captchaComplexity', 5, 'number of captcha complexity', 'spam'),
('captchaCaseSensitive', 1, '1 to enable captcha case sensitive | 0 to enable', 'spam');


-- Update pH7CMS's SQL schema version
UPDATE ph7_modules SET version = '1.4.2' WHERE vendorName = 'pH7CMS';
