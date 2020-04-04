--
-- Author:        Pierre-Henry Soria <ph7software@gmail.com>
-- Copyright:     (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
--

ALTER TABLE pH7_Admins ADD COLUMN `groupId` smallint(4) unsigned NOT NULL DEFAULT '9';
ALTER TABLE pH7_Members MODIFY `avatar` varchar(5) DEFAULT NULL;
ALTER TABLE pH7_Members ADD `approvedAvatar` tinyint(1) unsigned NOT NULL DEFAULT '1';

ALTER TABLE pH7_Videos ADD COLUMN `createdDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE pH7_AlbumsVideos ADD COLUMN `createdDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE pH7_Pictures ADD COLUMN `createdDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE pH7_AlbumsPictures ADD COLUMN `createdDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';

ALTER TABLE pH7_Videos ADD COLUMN `updatedDate` datetime DEFAULT NULL;
ALTER TABLE pH7_AlbumsVideos ADD COLUMN `updatedDate` datetime DEFAULT NULL;
ALTER TABLE pH7_Pictures ADD COLUMN `updatedDate` datetime DEFAULT NULL;
ALTER TABLE pH7_AlbumsPictures ADD COLUMN `updatedDate` datetime DEFAULT NULL;
ALTER TABLE pH7_Admins DROP INDEX  `adminUsername` , ADD UNIQUE  `username` (  `username` );
ALTER TABLE pH7_Admins ADD UNIQUE KEY `email` (`email`);

ALTER TABLE pH7_MembersFriends DROP KEY `profileId`, ADD INDEX `profileId` (`profileId`);
ALTER TABLE pH7_MembersFriends ADD INDEX `friendId` (`friendId`);

CREATE TABLE IF NOT EXISTS `pH7_Affiliate` (
  `profileId` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(40) NOT NULL DEFAULT '',
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `password` char(120) NOT NULL DEFAULT '',
  `email` varchar(200) NOT NULL DEFAULT '',
  `sex` enum('male','female') NOT NULL DEFAULT 'male',
  `birthDate` date NOT NULL DEFAULT '0000-00-00',
  `ip` varchar(20) NOT NULL DEFAULT '127.0.0.1',
  `businessName` varchar(100) DEFAULT NULL,
  `sessionHash` varchar(40) NOT NULL DEFAULT '',
  `address` varchar(255) DEFAULT NULL,
  `street` varchar(200) DEFAULT NULL,
  `country` char(2) DEFAULT NULL,
  `city` varchar(150) DEFAULT NULL,
  `state` varchar(150) DEFAULT NULL,
  `zipCode` varchar(20) DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `description` text NOT NULL,
  `bankAccount` varchar(150) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `fax` varchar(100) DEFAULT NULL,
  `lang` varchar(2) NOT NULL DEFAULT 'en',
  `credits` int(6) unsigned NOT NULL DEFAULT '0',
  `prefixSalt` char(40) DEFAULT NULL,
  `suffixSalt` char(40) DEFAULT NULL,
  `hashValidation` char(40) DEFAULT NULL,
  `lastActivity` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `refer` int(10) unsigned DEFAULT '0',
  `joinDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `ban`  tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`profileId`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `bankAccount` (`bankAccount`), -- For Security Bank Account --
  KEY `country` (`country`),
  KEY `birthDate` (`birthDate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `pH7_AdsAffiliate` (
  `adsId` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL,
  `code` text,
  `active` enum('1','0') DEFAULT '1',
  `width` smallint(4) DEFAULT NULL,
  `height` smallint(4) DEFAULT NULL,
  `views` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`adsId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


INSERT INTO `pH7_AdsAffiliate` (`adsId`, `name`, `code`, `active`, `width`, `height`, `views`) VALUES
(1, 'Sponsor pH7 Dating CMS 1 (728x90)', '<a href="%affiliate_url%"><img src="%software_website%/static/img/logo1-728x90.gif" alt="%software_name%" title="%software_name% by %software_company%" /></a>', '0', 728, 90, 0),
(2, 'Sponsor pH7 Dating CMS 2 (728x90)', '<a href="%affiliate_url%/signup"><img src="%software_website%/static/img/logo2-728x90.gif" alt="%software_name%" title="%software_name% by %software_company%" /></a>', '0', 728, 90, 0),
(3, 'Sponsor pH7 Dating CMS 3 (468x60)', '<a href="%affiliate_url%/signup"><img src="%software_website%/static/img/logo1-468x60.gif" alt="%software_name%" title="%software_name% by %software_company%" /></a>', '0', 468, 60, 0),
(4, 'Sponsor pH7 Dating CMS 4 (468x60)', '<a href="%affiliate_url%"><img src="%software_website%/static/img/logo2-468x60.gif" alt="%software_name%" title="%software_name% by %software_company%" /></a>', '0', 468, 60, 0),
(5, 'Sponsor pH7 Dating CMS 5  (120x600)', '<a href="%affiliate_url%"><img src="%software_website%/static/img/logo1-120x600.gif" alt="%software_name%" title="%software_name% by %software_company%" /></a>', '0', 120, 600, 0),
(6, 'Sponsor pH7 Dating CMS 6  (120x600)', '<a href="%affiliate_url%"><img src="%software_website%/static/img/logo2-120x600.gif" alt="%software_name%" title="%software_name% by %software_company%" /></a>', '0', 120, 600, 0);

ALTER TABLE  `pH7_Members` CHANGE  `paymentTo`  `bankAccount` varchar(150) DEFAULT NULL;
ALTER TABLE `pH7_Members` CHANGE  `languageMember`  `lang` NOT NULL DEFAULT 'en';

ALTER TABLE `pH7_Settings` MODIFY COLUMN `desc` varchar(150) NOT NULL DEFAULT '';
ALTER TABLE `pH7_Settings` CHANGE `groupId` `group` varchar(12) DEFAULT NULL;

INSERT INTO `pH7_Settings` (`name`, `value`, `desc`, `group`) VALUES
('splashPage', '0', 'Use Splash Page | enable = 1 or disable = 0', 'general'),
('profileBackgroundManualApproval', '0', '0 for disable or 1 for enable ', 'moderation'),
('videoManualApproval', '0', '0 for disable or 1 for enable ', 'moderation'),
('defaultVideo', 'http://www.youtube.com/watch?v=pHWeb', 'Video by default if no video found', 'video'),
('autoplayVideo', '1', '1 = Autoplay is enabled, 0 = Autoplay is disabled', 'video'),
('ipApi', 'http://whatismyipaddress.com/ip/', 'IP Api URL', 'api'),
('chatApi', 'http://chat.mikochat.com/g?s=%site_name%&amp;theme=4', 'Chat Api URL', 'api'),
('chatrouletteApi', 'http://www.jabbercam.com/JabberCam.swf', 'Chatroulette Api URL', 'api'),
('isCaptchaAffiliateSignup', '0', '0 for disable or 1 for enable', 'spam'),
('isCaptchaMail', '0', '0 for disable or 1 for enable', 'spam'),
('isCaptchaComment', '0', '0 for disable or 1 for enable', 'spam'),
('isCaptchaNote', '0', '0 for disable or 1 for enable', 'spam'),
('banWordReplace', '[removed]',  '',  'security'),
('isUniversalLogin', '0', '0 for disable or 1 for enable', 'registration'),
('userActivationType', '1', '1 = no activation, 2 = email activation, 3 = Manual activation by the administrator', 'registration'),
('affActivationType', '1', '1 = no activation, 2 = email activation, 3 = Manual activation by the administrator', 'registration'),
('cronSecurityHash', 'change_secret_cron_word_by_your', 'The secret word for the URL of the cron', 'automation');

UPDATE `pH7_Settings` SET `name` = 'isCaptchaUserSignup', `group` = 'spam' WHERE `name` = 'isCaptcha';

DELETE FROM `pH7_Settings` WHERE `name` = 'recaptchaPublicKey'; -- We no longer use reCaptcha for now
DELETE FROM `pH7_Settings` WHERE `name` = 'recaptchaPrivateKey'; -- We no longer use reCaptcha for now
DELETE FROM `pH7_Settings` WHERE `name` = 'pictureMaxHeight';
DELETE FROM `pH7_Settings` WHERE `name` = 'pictureMaxWidth';

ALTER TABLE `pH7_Videos` ADD COLUMN `thumb` varchar(255) DEFAULT NULL; -- e.g. http://img.youtube.com/vi/4fplAZfO9KY/default.jpg or local file server.

ALTER TABLE `pH7_Pictures` MODIFY `file` varchar(40) DEFAULT NULL;
ALTER TABLE `pH7_Videos` MODIFY `file` varchar(255) DEFAULT NULL; -- e.g. http://youtu.be/4fplAZfO9KY or local file server.
ALTER TABLE `pH7_AlbumsPictures` MODIFY `thumb` varchar(20) DEFAULT NULL;
ALTER TABLE `pH7_AlbumsVideos` MODIFY `thumb` varchar(20) DEFAULT NULL;

ALTER TABLE `pH7_Videos` ADD COLUMN  `albumId` int(11) DEFAULT NULL;
ALTER TABLE `pH7_Games` MODIFY COLUMN `name` varchar(120) NOT NULL;

-- Change column names for better integration of search engines by avoiding code duplication. --
ALTER TABLE `pH7_Pictures` CHANGE `name` `title` varchar(100) DEFAULT NULL;
ALTER TABLE `pH7_Videos`CHANGE `name` `title` varchar(100) DEFAULT NULL;
ALTER TABLE `pH7_AlbumsPictures` CHANGE `name` `title` varchar(100) DEFAULT NULL;
ALTER TABLE `pH7_AlbumsVideos` CHANGE `name` `title` varchar(100) DEFAULT NULL;
ALTER TABLE `pH7_ForumsTopics` CHANGE `subject` `title` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE `pH7_Messages` CHANGE `subject` `title` varchar(30) NOT NULL DEFAULT '';

ALTER TABLE `pH7_ForumsTopics` ADD COLUMN `views` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `pH7_Ads` MODIFY COLUMN `name` varchar(40) DEFAULT NULL;
ALTER TABLE `pH7_AdsAffiliate` MODIFY COLUMN `name` varchar(40) DEFAULT NULL;

ALTER TABLE `pH7_Forums` ADD COLUMN `createdDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `pH7_Forums` ADD COLUMN `updatedDate` datetime DEFAULT NULL;

CREATE TABLE IF NOT EXISTS `pH7_Notes` (
  `noteId` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `profileId` int(10) unsigned DEFAULT NULL,
  `postId` varchar(60) NOT NULL,
  `langId` char(2) NOT NULL DEFAULT '',
  `title` varchar(100) DEFAULT NULL,
  `content` longtext NOT NULL,
  `pageTitle` varchar(100) NOT NULL,
  `metaDescription` varchar(255) NOT NULL,
  `metaKeywords` varchar(255) NOT NULL,
  `slogan` varchar(200) NOT NULL,
  `metaRobots` varchar(50) NOT NULL,
  `metaAuthor` varchar(50) NOT NULL,
  `metaCopyright` varchar(50) NOT NULL,
  `tags` varchar(200) DEFAULT NULL,
  `votes` int(9) unsigned DEFAULT '0',
  `score` float(9) unsigned DEFAULT '0',
  `views` int(10) unsigned DEFAULT '0',
  `enableComment` enum('1','0') DEFAULT '1',
  `createdDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updatedDate` datetime DEFAULT NULL,
  PRIMARY KEY (`noteId`),
  UNIQUE KEY `postId` (`postId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `pH7_CommentsNote` (
  `commentId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sender` int(10) unsigned NOT NULL,
  `recipient` int(10) unsigned NOT NULL,
  `comment` text NOT NULL,
  `createdDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updatedDate` datetime DEFAULT NULL,
  `approved` enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (`commentId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- SEO, Title, etc. --
ALTER TABLE `pH7_Blogs` MODIFY `title` varchar(100) DEFAULT NULL;
ALTER TABLE `pH7_Blogs` MODIFY `pageTitle` varchar(100) DEFAULT NULL;
ALTER TABLE `pH7_Blogs` MODIFY `slogan` varchar(200) NOT NULL;
ALTER TABLE `pH7_Blogs` MODIFY `metaDescription` varchar(255) NOT NULL;
ALTER TABLE `pH7_Blogs` MODIFY `metaKeywords` varchar(255) NOT NULL;
ALTER TABLE `pH7_Blogs` MODIFY `metaRobots` varchar(50) NOT NULL;
ALTER TABLE `pH7_Blogs` MODIFY `metaAuthor` varchar(50) NOT NULL;
ALTER TABLE `pH7_Blogs` MODIFY `metaCopyright` varchar(50) NOT NULL;
ALTER TABLE `pH7_Blogs` MODIFY`tags` varchar(200) DEFAULT NULL;

ALTER TABLE `pH7_MetaMain` MODIFY `siteName`  varchar(50) NOT NULL;
ALTER TABLE `pH7_MetaMain` MODIFY `pageTitle` varchar(100) NOT NULL;
ALTER TABLE `pH7_MetaMain` MODIFY `metaDescription` varchar(255) NOT NULL;
ALTER TABLE `pH7_MetaMain` MODIFY `metaKeywords` varchar(255) NOT NULL;
ALTER TABLE `pH7_MetaMain` MODIFY `slogan` varchar(200) NOT NULL;

ALTER TABLE `pH7_MetaMain` MODIFY `metaRobots` varchar(50) NOT NULL DEFAULT '';
ALTER TABLE `pH7_MetaMain` MODIFY `metaAuthor` varchar(50) NOT NULL DEFAULT '';
ALTER TABLE `pH7_MetaMain` MODIFY `metaCopyright` varchar(50) NOT NULL DEFAULT '';
ALTER TABLE `pH7_MetaMain` MODIFY `metaRating` varchar(50) NOT NULL DEFAULT '';
ALTER TABLE `pH7_MetaMain` MODIFY `metaDistribution` varchar(50) NOT NULL DEFAULT '';
ALTER TABLE `pH7_MetaMain` MODIFY `metaCategory` varchar(50) NOT NULL DEFAULT '';


ALTER TABLE `pH7_AlbumsPictures` MODIFY `title` varchar(80) DEFAULT NULL;
ALTER TABLE `pH7_AlbumsVideos` MODIFY `title` varchar(80) DEFAULT NULL;
ALTER TABLE `pH7_Pictures` MODIFY `title` varchar(80) NOT NULL;
ALTER TABLE `pH7_Videos` MODIFY `title` varchar(80) DEFAULT NULL;
ALTER TABLE `pH7_Videos` ADD COLUMN `duration` int(9) NOT NULL;

-- Optimization of SQL fields Members --
ALTER TABLE `pH7_Members` CHANGE  `status` `active` tinyint(1) unsigned NOT NULL DEFAULT '1';
ALTER TABLE `pH7_Members` MODIFY `ban` tinyint(1) unsigned NOT NULL DEFAULT '0';
UPDATE  `pH7_Members` SET  `ban` = 0 WHERE `ban` = 1; -- We unbanned all members
ALTER TABLE `pH7_MembersFriends` MODIFY `pending` tinyint(1) unsigned NOT NULL DEFAULT '0';
UPDATE  `pH7_MembersFriends` SET  `pending` = 0 WHERE `pending` = 1; -- No friends are pending
ALTER TABLE `pH7_Members` DROP KEY  `status`;

ALTER TABLE `pH7_Messages` MODIFY `status` tinyint(1) unsigned NOT NULL DEFAULT '1';

-- Adding the Categories features for Blogs --

CREATE TABLE IF NOT EXISTS `pH7_BlogsCategories` (
  `categoryId` int(6) unsigned NOT NULL,
  `blogId` int(10) unsigned NOT NULL,
   INDEX (  `categoryId` ),
   INDEX (  `blogId` )
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `pH7_BlogsDataCategories` (
  `categoryId` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`categoryId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `pH7_BlogsDataCategories` (`categoryId`, `name`) VALUES
(1, 'Affiliate'),
(2, 'Business'),
(3, 'Company'),
(4, 'Dating'),
(5, 'Education'),
(6, 'Family'),
(7, 'Food'),
(8, 'Game'),
(9, 'Health'),
(10, 'Hobby'),
(11, 'Movie'),
(12, 'Music'),
(13, 'News'),
(14, 'Programming'),
(15, 'Recreation'),
(16, 'Shopping'),
(17, 'Society'),
(18, 'Sports'),
(19, 'Technology'),
(20, 'Travel');

-- Adding the Categories features for Notes --

CREATE TABLE IF NOT EXISTS `pH7_NotesCategories` (
  `categoryId` int(6) unsigned NOT NULL,
  `noteId` int(10) unsigned NOT NULL,
   INDEX (  `categoryId` ),
   INDEX (  `noteId` )
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `pH7_NotesDataCategories` (
  `categoryId` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`categoryId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `pH7_NotesDataCategories` (`categoryId`, `name`) VALUES
(1, 'Business'),
(2, 'Companies'),
(3, 'Dating'),
(4, 'Education'),
(5, 'Family'),
(6, 'Food'),
(7, 'Game'),
(8, 'Health'),
(9, 'Hobby'),
(10, 'Movie'),
(11, 'Music'),
(12, 'News'),
(13, 'Pets'),
(14, 'Recreation'),
(15, 'Shopping'),
(16, 'Society'),
(17, 'Sports'),
(18, 'Study'),
(19, 'Technology'),
(20, 'Travel');

-- Who's Viewed Your Profile Module --
CREATE TABLE IF NOT EXISTS `pH7_MembersWhoViews` (
  `profileId` int(10) unsigned DEFAULT NULL,
  `visitorId` int(10) unsigned DEFAULT NULL,
  `lastVisit` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  INDEX `profileId` (`profileId`),
  INDEX `visitorId` (`visitorId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE `pH7_BlockIp` ADD COLUMN `expires` int(10) unsigned NOT NULL;
ALTER TABLE `pH7_BlockIp` CHANGE `blockIp` `ip` int(10) unsigned NOT NULL DEFAULT '0';

ALTER TABLE `pH7_AdsClicks` ADD `url` varchar(255) DEFAULT NULL;

-- Background Profile --
CREATE TABLE IF NOT EXISTS `pH7_MembersBackground` (
  `profileId` int(10) unsigned DEFAULT NULL,
  `file` varchar(5) DEFAULT NULL,
  `approved` tinyint(1) unsigned NOT NULL DEFAULT '1',
   INDEX `profileId` (`profileId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE `pH7_Report` CHANGE `date` `dateTime` datetime DEFAULT NULL;
ALTER TABLE `pH7_AdsClicks` CHANGE `date` `dateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
