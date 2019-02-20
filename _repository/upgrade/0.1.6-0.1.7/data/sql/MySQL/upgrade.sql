--
-- Author:        Pierre-Henry Soria <ph7software@gmail.com>
-- Copyright:     (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
--

ALTER TABLE `pH7_MetaMain` DROP `siteName`;
INSERT INTO `pH7_Settings` (`name`, `value`, `desc`, `group`) VALUES
('siteName', 'pH7 Social Dating CMS', '', 'general'),
('noteManualApproval', '0', '0 for disable or 1 for enable ', 'moderation'),
('isCaptchaForum', '0', '0 for disable or 1 for enable', 'spam'),

('timeDelayUserRegistration', '1440', '1440 minutes = 24 hours (in minutes!)', 'spam'),
('timeDelayAffRegistration', '2880', '2880 minutes = 2 days (in minutes!)', 'spam'),
('timeDelaySendNote', '20', 'Waiting time to add a new note post, in minutes!', 'spam'),
('timeDelaySendMail', '3', 'Waiting time to send a new message, in minutes!', 'spam'),
('timeDelaySendComment', '5', 'Waiting time to send a new comment, in minutes!', 'spam'),
('timeDelaySendForumTopic', '5', 'Waiting time to send a new topic in the forum, in minutes!', 'spam'),
('timeDelaySendForumMsg', '10', 'Waiting time to send a reply message in the same topic, in minutes!', 'spam'),

('watermarkTextImage', 'HiZup.com', 'Watermark text', 'image'),
('sizeWatermarkTextImage', 2, 'Between 0 to 5', 'image'),
('defaultMembershipGroupId', 2, 'Default Membership Group', 'registration'),

('isUserLoginAttempt', 1, 'Enable blocking connection attempts abusive. Enable = 1 or disable = 0', 'security'),
('isAdminLoginAttempt', 1, 'Enable blocking connection attempts abusive. Enable = 1 or disable = 0', 'security'),
('isAffiliateLoginAttempt', 1, 'Enable blocking connection attempts abusive. Enable = 1 or disable = 0', 'security'),
('maxUserLoginAttempts', 30, 'Maximum login attempts before blocking', 'security'),
('maxAffiliateLoginAttempts', 20, 'Maximum login attempts before blocking', 'security'),
('maxAdminLoginAttempts', 10, 'Maximum login attempts before blocking', 'security'),
('loginUserAttemptTime', 60, 'Time before a new connection attempt, in minutes!', 'security'),
('loginAffiliateAttemptTime', 60, 'Time before a new connection attempt, in minutes!', 'security'),
('loginAdminAttemptTime', 120, 'Time before a new connection attempt, in minutes!', 'security'),

('cleanMsg', '0', '0 Delete messages older than days. 0 to disable', 'pruning'),
('cleanComment', '0', 'Delete comments older than days. 0 to disable', 'pruning'),


('userTimeout', '1', 'User inactivity timeout. The number of minutes that a member becomes inactive (offline).', 'automation'),


('securityTokenLifetime', '480', 'Time in seconds', 'security');


ALTER TABLE pH7_Notes ADD COLUMN thumb char(24) DEFAULT NULL;
ALTER TABLE pH7_Notes ADD COLUMN approved tinyint(1) unsigned NOT NULL DEFAULT '1';
ALTER TABLE pH7_Members MODIFY COLUMN avatar char(5) DEFAULT NULL;

ALTER TABLE pH7_AlbumsPictures MODIFY COLUMN thumb char(11) NOT NULL;
ALTER TABLE pH7_AlbumsVideos MODIFY COLUMN thumb char(11) NOT NULL;

-- Build 2 --

ALTER TABLE pH7_Ads ENGINE = InnoDB;
ALTER TABLE pH7_AdsClicks ENGINE = InnoDB;
ALTER TABLE pH7_BlogsCategories ENGINE = InnoDB;
ALTER TABLE pH7_NotesCategories ENGINE = InnoDB;

ALTER TABLE pH7_BlogsCategories MODIFY blogId mediumint(10) unsigned NOT NULL;
ALTER TABLE pH7_Notes MODIFY noteId int(10) unsigned NOT NULL AUTO_INCREMENT;


ALTER TABLE pH7_AdsClicks ADD FOREIGN KEY (adsId) REFERENCES pH7_Ads(adsId);

ALTER TABLE pH7_BlogsCategories ADD FOREIGN KEY (blogId) REFERENCES pH7_Blogs(blogId);

ALTER TABLE pH7_NotesCategories ADD FOREIGN KEY (noteId) REFERENCES pH7_Notes(noteId);

ALTER TABLE pH7_CommentsNote ADD FOREIGN KEY (recipient) REFERENCES pH7_Notes(noteId);

ALTER TABLE pH7_CommentsPicture ADD FOREIGN KEY (recipient) REFERENCES pH7_Pictures(pictureId);

ALTER TABLE pH7_CommentsVideo ADD FOREIGN KEY (recipient) REFERENCES pH7_Videos(videoId);

ALTER TABLE pH7_CommentsGame ADD FOREIGN KEY (recipient) REFERENCES pH7_Games(gameId);

ALTER TABLE pH7_CommentsProfile ADD FOREIGN KEY (recipient) REFERENCES pH7_Members(profileId);

ALTER TABLE pH7_Messenger MODIFY fromUser varchar(40) NOT NULL DEFAULT '';
ALTER TABLE pH7_Messenger MODIFY toUser varchar(40) NOT NULL DEFAULT '';
ALTER TABLE pH7_Messenger ADD FOREIGN KEY (fromUser) REFERENCES pH7_Members(username);
ALTER TABLE pH7_Messenger ADD FOREIGN KEY (toUser) REFERENCES pH7_Members(username);

ALTER TABLE pH7_Forums ADD FOREIGN KEY (categoryId) REFERENCES pH7_ForumsCategories(categoryId);
ALTER TABLE pH7_ForumsTopics ADD FOREIGN KEY (forumId) REFERENCES pH7_Forums(forumId);
ALTER TABLE pH7_ForumsMessages ADD FOREIGN KEY (topicId) REFERENCES pH7_ForumsTopics(topicId);

-- We replace "blog" by "note" because the blog posts are official and should not be reported by users contrary with the note posts that are posted by users.
ALTER TABLE  pH7_Report CHANGE contentType contentType ENUM(  'profile',  'avatar',  'mail',  'comment',  'photo',  'video',  'forum',  'note' ) NOT NULL DEFAULT 'profile';


ALTER TABLE pH7_Members MODIFY lastActivity datetime DEFAULT NULL;
ALTER TABLE pH7_Affiliate MODIFY lastActivity datetime DEFAULT NULL;


ALTER TABLE pH7_Admins MODIFY joinDate datetime DEFAULT NULL;
ALTER TABLE pH7_Members MODIFY joinDate datetime DEFAULT NULL;
ALTER TABLE pH7_Affiliate MODIFY joinDate datetime DEFAULT NULL;
ALTER TABLE pH7_MembersFriends MODIFY requestDate datetime DEFAULT NULL;
ALTER TABLE pH7_MembersWall CHANGE dateTime createdDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE pH7_MembersWall ADD updatedDate datetime DEFAULT NULL;
ALTER TABLE pH7_AdsClicks MODIFY dateTime datetime NOT NULL DEFAULT '0000-00-00 00:00:00';

ALTER TABLE pH7_Admins ADD hashValidation char(40) DEFAULT NULL; -- For the lost password module


ALTER TABLE pH7_NotesCategories ADD profileId int(10) unsigned NOT NULL;
ALTER TABLE pH7_NotesCategories ADD FOREIGN KEY (profileId) REFERENCES pH7_Members(profileId);

ALTER TABLE pH7_MembersFriends MODIFY profileId int(10) unsigned NOT NULL;
ALTER TABLE pH7_MembersFriends MODIFY friendId int(10) unsigned NOT NULL;
ALTER TABLE pH7_MembersWhoViews MODIFY profileId int(10) unsigned NOT NULL;
ALTER TABLE pH7_MembersWhoViews MODIFY visitorId int(10) unsigned NOT NULL;
ALTER TABLE pH7_MembersBackground MODIFY file varchar(5) NOT NULL;
ALTER TABLE pH7_MembersBackground MODIFY profileId int(10) unsigned NOT NULL;
ALTER TABLE pH7_ForumsMessages MODIFY profileId int(10) unsigned NOT NULL;
ALTER TABLE pH7_ForumsTopics MODIFY profileId int(10) unsigned NOT NULL;
ALTER TABLE pH7_Notes MODIFY profileId int(10) unsigned NOT NULL;

ALTER TABLE pH7_AlbumsPictures CHANGE title name varchar(80) NOT NULL;
ALTER TABLE pH7_AlbumsVideos CHANGE title name varchar(80) NOT NULL;


ALTER TABLE pH7_Admins DROP groupId;
ALTER TABLE pH7_Members MODIFY groupId tinyint(2) unsigned NOT NULL DEFAULT 2;

ALTER TABLE pH7_Members ADD membershipExpiration timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE pH7_Members ADD lastEdit datetime DEFAULT NULL;
ALTER TABLE pH7_Affiliate ADD lastEdit datetime DEFAULT NULL;
ALTER TABLE pH7_Admins ADD lastActivity datetime DEFAULT NULL;
ALTER TABLE pH7_Admins ADD lastEdit datetime DEFAULT NULL;

ALTER TABLE pH7_Members ADD FOREIGN KEY(groupId) REFERENCES pH7_Memberships(groupId);



CREATE TABLE IF NOT EXISTS pH7_AdminsAttemptsLogin (
  ip varchar(20) NOT NULL DEFAULT '',
  attempts smallint(5) unsigned NOT NULL ,
  lastLogin DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_MembersAttemptsLogin (
  ip varchar(20) NOT NULL DEFAULT '',
  attempts smallint(5) unsigned NOT NULL ,
  lastLogin DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_AffiliateAttemptsLogin (
  ip varchar(20) NOT NULL DEFAULT '',
  attempts smallint(5) unsigned NOT NULL ,
  lastLogin DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

ALTER TABLE pH7_Members CHANGE onlineStatus userStatus tinyint(1) unsigned NOT NULL DEFAULT 1;


CREATE TABLE IF NOT EXISTS pH7_MembersNotifications (
  profileId int(10) unsigned NOT NULL,
  enableNewsletters tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (profileId),
  FOREIGN KEY (profileId) REFERENCES pH7_Members(profileId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS pH7_Subscribers (
  profileId int(10) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(200) NOT NULL,
  email varchar(200) NOT NULL,
  active tinyint(1) unsigned NOT NULL DEFAULT 2, -- 1 = Active Account, 2 = Pending Account
  hashValidation char(40) DEFAULT NULL,
  INDEX (profileId),
  PRIMARY KEY (profileId),
  UNIQUE KEY (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE pH7_Settings MODIFY COLUMN value varchar(80) NOT NULL;

UPDATE pH7_Settings SET value = 'http://addons.hizup.com/chat/?name=%site_name%&url=%site_url%&skin=4' WHERE name = 'chatApi';
UPDATE pH7_Settings SET value = 'http://addons.hizup.com/chatroulette/?name=%site_name%&url=%site_url%&skin=1' WHERE name = 'chatrouletteApi';
