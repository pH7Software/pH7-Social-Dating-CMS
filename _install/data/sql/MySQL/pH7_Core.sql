--
--
-- Title:         SQL Core (base) Install File
--
-- Author:        Pierre-Henry Soria <ph7software@gmail.com>
-- Copyright:     (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
-- Package:       PH7 / Install / Data / Sql
-- Version:       1.1
--
--

--
-- Set the variables --
--
SET @sAdminEmail = 'admin@yoursite.com';
SET @sFeedbackEmail = 'feedback@yoursite.com';
SET @sNoReplyEmail = 'noreply@yoursite.com';
SET @sIpApiUrl = 'http://whatismyipaddress.com/ip/';
SET @sDefaultVideoUrl = 'https://www.youtube.com/watch?v=q-1eHnBOg4A';
SET @sChatApiUrl = 'http://addons.hizup.com/chat/?name=%site_name%&url=%site_url%&skin=4';
SET @sChatrouletteApiUrl = 'http://addons.hizup.com/chatroulette/?name=%site_name%&url=%site_url%&skin=1';

SET @sCurrentDate = CURRENT_TIMESTAMP;
SET @sPassword = SHA1(RAND() + UNIX_TIMESTAMP());


CREATE TABLE IF NOT EXISTS pH7_Admins (
  profileId tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  username varchar(40) NOT NULL,
  password varchar(120) NOT NULL,
  email varchar(120) NOT NULL,
  firstName varchar(50) DEFAULT NULL,
  lastName varchar(50) DEFAULT NULL,
  sex enum('male','female') NOT NULL DEFAULT 'male',
  lang varchar(5) NOT NULL DEFAULT 'en_US',
  timeZone varchar(6) NOT NULL DEFAULT '-6',
  joinDate datetime DEFAULT NULL,
  lastActivity datetime DEFAULT NULL,
  lastEdit datetime DEFAULT NULL,
  ban enum('0','1') DEFAULT '0',
  ip varchar(45) NOT NULL DEFAULT '127.0.0.1',
  isTwoFactorAuth enum('1','0') DEFAULT '0',
  twoFactorAuthSecret varchar(40) DEFAULT NULL,
  hashValidation varchar(40) DEFAULT NULL,
  PRIMARY KEY (profileId),
  UNIQUE KEY username (username),
  UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_Memberships (
  groupId tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(64) NOT NULL DEFAULT '',
  description varchar(255) NOT NULL,
  permissions text NOT NULL,
  price decimal(10,2) unsigned NOT NULL,
  expirationDays tinyint(2) unsigned NOT NULL,
  enable enum('1','0') DEFAULT '1',
  orderId tinyint(2) unsigned NOT NULL,
  PRIMARY KEY (groupId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO pH7_Memberships (groupId, name, description, permissions, price, expirationDays, enable, orderId) VALUES
(1, 'Visitor', 'This subscription is offered to all visitors who visit the site.', 'a:24:{s:21:"quick_search_profiles";s:1:"1";s:24:"advanced_search_profiles";s:1:"1";s:10:"read_mails";s:1:"0";s:10:"send_mails";s:1:"0";s:13:"view_pictures";s:1:"1";s:15:"upload_pictures";s:1:"0";s:11:"view_videos";s:1:"1";s:13:"upload_videos";s:1:"0";s:17:"instant_messaging";s:1:"0";s:4:"chat";s:1:"1";s:12:"chatroulette";s:1:"1";s:10:"hot_or_not";s:1:"1";s:15:"love_calculator";s:1:"0";s:10:"read_notes";s:1:"1";s:11:"write_notes";s:1:"0";s:15:"read_blog_posts";s:1:"1";s:13:"view_comments";s:1:"1";s:14:"write_comments";s:1:"0";s:12:"forum_access";s:1:"1";s:19:"create_forum_topics";s:1:"0";s:19:"answer_forum_topics";s:1:"0";s:12:"games_access";s:1:"1";s:13:"webcam_access";s:1:"1";s:18:"member_site_access";s:1:"0";}', 0.00, 0, '1', 1),
(9, 'Pending', 'Pending subscription provisional migration to a different subscription.', 'a:24:{s:21:"quick_search_profiles";s:1:"1";s:24:"advanced_search_profiles";s:1:"1";s:10:"read_mails";s:1:"0";s:10:"send_mails";s:1:"0";s:13:"view_pictures";s:1:"1";s:15:"upload_pictures";s:1:"0";s:11:"view_videos";s:1:"1";s:13:"upload_videos";s:1:"0";s:17:"instant_messaging";s:1:"0";s:4:"chat";s:1:"1";s:12:"chatroulette";s:1:"1";s:10:"hot_or_not";s:1:"1";s:15:"love_calculator";s:1:"0";s:10:"read_notes";s:1:"1";s:11:"write_notes";s:1:"0";s:15:"read_blog_posts";s:1:"1";s:13:"view_comments";s:1:"1";s:14:"write_comments";s:1:"0";s:12:"forum_access";s:1:"1";s:19:"create_forum_topics";s:1:"0";s:19:"answer_forum_topics";s:1:"0";s:12:"games_access";s:1:"1";s:13:"webcam_access";s:1:"1";s:18:"member_site_access";s:1:"0";}', 0.00, 15, '0', 2),
(2, 'Regular (Free)', 'Free Membership.', 'a:24:{s:21:"quick_search_profiles";s:1:"1";s:24:"advanced_search_profiles";s:1:"1";s:10:"read_mails";s:1:"1";s:10:"send_mails";s:1:"1";s:13:"view_pictures";s:1:"1";s:15:"upload_pictures";s:1:"1";s:11:"view_videos";s:1:"1";s:13:"upload_videos";s:1:"1";s:17:"instant_messaging";s:1:"1";s:4:"chat";s:1:"1";s:12:"chatroulette";s:1:"1";s:10:"hot_or_not";s:1:"1";s:15:"love_calculator";s:1:"1";s:10:"read_notes";s:1:"1";s:11:"write_notes";s:1:"1";s:15:"read_blog_posts";s:1:"1";s:13:"view_comments";s:1:"1";s:14:"write_comments";s:1:"1";s:12:"forum_access";s:1:"1";s:19:"create_forum_topics";s:1:"1";s:19:"answer_forum_topics";s:1:"1";s:12:"games_access";s:1:"1";s:13:"webcam_access";s:1:"1";s:18:"member_site_access";s:1:"1";}', 0.00, 0, '1', 3),
(4, 'Platinum', 'The membership for the small budget.', 'a:24:{s:21:"quick_search_profiles";s:1:"1";s:24:"advanced_search_profiles";s:1:"1";s:10:"read_mails";s:1:"1";s:10:"send_mails";s:1:"1";s:13:"view_pictures";s:1:"1";s:15:"upload_pictures";s:1:"1";s:11:"view_videos";s:1:"1";s:13:"upload_videos";s:1:"1";s:17:"instant_messaging";s:1:"1";s:4:"chat";s:1:"1";s:12:"chatroulette";s:1:"1";s:10:"hot_or_not";s:1:"1";s:15:"love_calculator";s:1:"1";s:10:"read_notes";s:1:"1";s:11:"write_notes";s:1:"1";s:15:"read_blog_posts";s:1:"1";s:13:"view_comments";s:1:"1";s:14:"write_comments";s:1:"1";s:12:"forum_access";s:1:"1";s:19:"create_forum_topics";s:1:"1";s:19:"answer_forum_topics";s:1:"1";s:12:"games_access";s:1:"1";s:13:"webcam_access";s:1:"1";s:18:"member_site_access";s:1:"1";}', 9.99, 5, '1', 4),
(5, 'Silver', 'The premium membership!', 'a:24:{s:21:"quick_search_profiles";s:1:"1";s:24:"advanced_search_profiles";s:1:"1";s:10:"read_mails";s:1:"1";s:10:"send_mails";s:1:"1";s:13:"view_pictures";s:1:"1";s:15:"upload_pictures";s:1:"1";s:11:"view_videos";s:1:"1";s:13:"upload_videos";s:1:"1";s:17:"instant_messaging";s:1:"1";s:4:"chat";s:1:"1";s:12:"chatroulette";s:1:"1";s:10:"hot_or_not";s:1:"1";s:15:"love_calculator";s:1:"1";s:10:"read_notes";s:1:"1";s:11:"write_notes";s:1:"1";s:15:"read_blog_posts";s:1:"1";s:13:"view_comments";s:1:"1";s:14:"write_comments";s:1:"1";s:12:"forum_access";s:1:"1";s:19:"create_forum_topics";s:1:"1";s:19:"answer_forum_topics";s:1:"1";s:12:"games_access";s:1:"1";s:13:"webcam_access";s:1:"1";s:18:"member_site_access";s:1:"1";}', 19.99, 10, '1', 5),
(6, 'Gold', 'The must membership! The Gold!!!', 'a:24:{s:21:"quick_search_profiles";s:1:"1";s:24:"advanced_search_profiles";s:1:"1";s:10:"read_mails";s:1:"1";s:10:"send_mails";s:1:"1";s:13:"view_pictures";s:1:"1";s:15:"upload_pictures";s:1:"1";s:11:"view_videos";s:1:"1";s:13:"upload_videos";s:1:"1";s:17:"instant_messaging";s:1:"1";s:4:"chat";s:1:"1";s:12:"chatroulette";s:1:"1";s:10:"hot_or_not";s:1:"1";s:15:"love_calculator";s:1:"1";s:10:"read_notes";s:1:"1";s:11:"write_notes";s:1:"1";s:15:"read_blog_posts";s:1:"1";s:13:"view_comments";s:1:"1";s:14:"write_comments";s:1:"1";s:12:"forum_access";s:1:"1";s:19:"create_forum_topics";s:1:"1";s:19:"answer_forum_topics";s:1:"1";s:12:"games_access";s:1:"1";s:13:"webcam_access";s:1:"1";s:18:"member_site_access";s:1:"1";}', 29.99, 30, '1', 6);


CREATE TABLE IF NOT EXISTS pH7_Members (
  profileId int(10) unsigned NOT NULL AUTO_INCREMENT,
  email varchar(120) NOT NULL,
  username varchar(40) NOT NULL,
  password varchar(120) NOT NULL,
  firstName varchar(50) DEFAULT NULL,
  lastName varchar(50) DEFAULT NULL,
  birthDate date NOT NULL DEFAULT '0000-00-00',
  sex enum('male','female','couple') NOT NULL DEFAULT 'female',
  matchSex set('male','female','couple') NOT NULL DEFAULT 'male',
  ip varchar(45) NOT NULL DEFAULT '127.0.0.1',
  bankAccount varchar(150) DEFAULT NULL,
  groupId tinyint(2) unsigned NOT NULL DEFAULT 2,
  membershipDate datetime DEFAULT NULL,
  userStatus tinyint(1) unsigned NOT NULL DEFAULT 1, -- 0 = Offline, 1 = Online, 2 = Busy, 3 = Away
  joinDate datetime DEFAULT NULL,
  lastActivity datetime DEFAULT NULL,
  lastEdit datetime DEFAULT NULL,
  avatar char(5) DEFAULT NULL,
  approvedAvatar tinyint(1) unsigned NOT NULL DEFAULT 1,
  featured tinyint(1) unsigned NOT NULL DEFAULT 0,
  lang varchar(5) NOT NULL DEFAULT 'en_US',
  hashValidation varchar(40) DEFAULT NULL,
  isTwoFactorAuth enum('1','0') DEFAULT '0',
  twoFactorAuthSecret varchar(40) DEFAULT NULL,
  views int(11) NOT NULL DEFAULT 0,
  reference varchar(255) DEFAULT NULL,
  votes int(11) DEFAULT 0,
  score float DEFAULT 0,
  credits int(6) unsigned NOT NULL DEFAULT 0,
  affiliatedId int(10) unsigned NOT NULL DEFAULT 0,
  active tinyint(1) unsigned NOT NULL DEFAULT 1,
  ban tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (profileId),
  FOREIGN KEY (groupId) REFERENCES pH7_Memberships(groupId),
  UNIQUE KEY (username),
  UNIQUE KEY (email),
  KEY birthDate (birthDate)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_MembersInfo (
  profileId int(10) unsigned NOT NULL AUTO_INCREMENT,
  middleName varchar(50) DEFAULT NULL,
  description text DEFAULT NULL,
  address varchar(255) DEFAULT NULL,
  street varchar(200) DEFAULT NULL,
  city varchar(150) DEFAULT NULL,
  state varchar(150) DEFAULT NULL,
  zipCode varchar(20) DEFAULT NULL,
  country char(2) DEFAULT NULL,
  phone varchar(100) DEFAULT NULL,
  website varchar(120) DEFAULT NULL,
  socialNetworkSite varchar(120) DEFAULT NULL,
  height tinyint(3) unsigned DEFAULT NULL,
  weight tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (profileId),
  KEY country (country),
  FOREIGN KEY (profileId) REFERENCES pH7_Members(profileId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_MembersPrivacy (
  profileId int(10) unsigned NOT NULL,
  privacyProfile enum('all','only_members','only_me') NOT NULL DEFAULT 'all',
  searchProfile enum('yes','no') NOT NULL DEFAULT 'yes',
  userSaveViews enum('yes','no') NOT NULL DEFAULT 'yes',
  PRIMARY KEY (profileId),
  FOREIGN KEY (profileId) REFERENCES pH7_Members(profileId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS pH7_MembersNotifications (
  profileId int(10) unsigned NOT NULL,
  enableNewsletters tinyint(1) unsigned NOT NULL DEFAULT 1,
  newMsg tinyint(1) unsigned NOT NULL DEFAULT 1,
  friendRequest tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (profileId),
  FOREIGN KEY (profileId) REFERENCES pH7_Members(profileId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- GHOST User. Do not remove ghost default member!
INSERT INTO pH7_Members (profileId, email, username, password, firstName, lastName, birthDate, ip, lastActivity, featured, active, userStatus, groupId, joinDate) VALUES
(1, 'ghost@ghost', 'ghost', @sPassword, 'Ghost', 'The Ghost', '1001-01-01', '00.000.00.00', @sCurrentDate, 0, 1, 1, 2, @sCurrentDate);
INSERT INTO pH7_MembersInfo (profileId, description, address, street, city, state, zipCode, country) VALUES
(1, 'This profile doesn''t exist anymore. So I''m the ghost who replaces him/her during this time', 'The Ghost City', 'Ghost Street', 'Ghost Town', 'Ghost State', '000000', 'US');
-- Privacy settings
INSERT INTO pH7_MembersPrivacy (profileId, privacyProfile, searchProfile, userSaveViews) VALUES (1, 'all', 'yes', 'yes');
-- Notifications
INSERT INTO pH7_MembersNotifications (profileId, enableNewsletters, newMsg, friendRequest) VALUES (1, 0, 0, 0);


CREATE TABLE IF NOT EXISTS pH7_Affiliates (
  profileId int(10) unsigned NOT NULL AUTO_INCREMENT,
  username varchar(40) NOT NULL,
  firstName varchar(50) NOT NULL,
  lastName varchar(50) NOT NULL,
  password varchar(120) NOT NULL,
  email varchar(120) NOT NULL,
  sex enum('male','female') NOT NULL DEFAULT 'male',
  birthDate date NOT NULL DEFAULT '0000-00-00',
  ip varchar(45) NOT NULL DEFAULT '127.0.0.1',
  bankAccount varchar(150) DEFAULT NULL,
  amount decimal(8,2) NOT NULL DEFAULT '0.00',
  totalPayment decimal(8,2) NOT NULL DEFAULT '0.00',
  lastPayment decimal(8,2) NOT NULL DEFAULT '0.00',
  lastPaymentDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  lang varchar(5) NOT NULL DEFAULT 'en_US',
  hashValidation varchar(40) DEFAULT NULL,
  isTwoFactorAuth enum('1','0') DEFAULT '0',
  twoFactorAuthSecret varchar(40) DEFAULT NULL,
  refer int(10) unsigned DEFAULT 0,
  joinDate datetime DEFAULT NULL,
  lastActivity datetime DEFAULT NULL,
  lastEdit datetime DEFAULT NULL,
  affiliatedId int(10) unsigned NOT NULL DEFAULT 0,
  active tinyint(1) unsigned NOT NULL DEFAULT 1,
  ban tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (profileId),
  UNIQUE KEY bankAccount (bankAccount), -- For the Security Bank Account --
  UNIQUE KEY username (username),
  UNIQUE KEY email (email),
  KEY birthDate (birthDate)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_AffiliatesInfo (
  profileId int(10) unsigned NOT NULL AUTO_INCREMENT,
  middleName varchar(50) DEFAULT NULL,
  businessName varchar(100) DEFAULT NULL,
  taxId varchar(40) DEFAULT NULL, -- Tax ID, VAT, SSN, ...
  address varchar(255) DEFAULT NULL,
  street varchar(200) DEFAULT NULL,
  country char(2) DEFAULT NULL,
  city varchar(150) DEFAULT NULL,
  state varchar(150) DEFAULT NULL,
  zipCode varchar(20) DEFAULT NULL,
  phone varchar(100) DEFAULT NULL,
  fax varchar(100) DEFAULT NULL,
  description text DEFAULT NULL,
  website varchar(120) DEFAULT NULL,
  PRIMARY KEY (profileId),
  KEY country (country),
  FOREIGN KEY (profileId) REFERENCES pH7_Affiliates(profileId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_BlockIp (
  ipId smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  ip varchar(45) NOT NULL,
  expiration smallint(5) unsigned NOT NULL,
  PRIMARY KEY (ip),
  KEY ipId (ipId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS pH7_Ads (
  adsId smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(40) DEFAULT NULL,
  code text,
  active enum('1','0') DEFAULT '1',
  width smallint(3) DEFAULT NULL,
  height smallint(3) DEFAULT NULL,
  views int(10) unsigned NOT NULL DEFAULT 0,
  clicks int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (adsId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO pH7_Ads (adsId, name, code, active, width, height, views, clicks) VALUES
(1, 'Sponsor pH7CMS 1 (728x90)', '<a href="#0"><img data-src="holder.js/728x90" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 728, 90, 0, 0),
(2, 'Sponsor pH7CMS 2 (728x90)', '<a href="#0"><img data-src="holder.js/728x90" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 728, 90, 0, 0),
(3, 'Sponsor pH7CMS 3 (200x200)', '<a href="#0"><img data-src="holder.js/200x200" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 200, 200, 0, 0),
(4, 'Sponsor pH7CMS 4 (200x200)', '<a href="#0"><img data-src="holder.js/200x200" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 200, 200, 0, 0),
(5, 'Sponsor pH7CMS 5 (250x250)', '<a href="#0"><img data-src="holder.js/250x250" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 250, 250, 0, 0),
(6, 'Sponsor pH7CMS 6 (250x250)', '<a href="#0"><img data-src="holder.js/250x250" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 250, 250, 0, 0),
(7, 'Sponsor pH7CMS 7 (468x60)', '<a href="#0"><img data-src="holder.js/468x60" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 468, 60, 0, 0),
(8, 'Sponsor pH7CMS 8 (468x60)', '<a href="#0"><img data-src="holder.js/468x60" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 468, 60, 0, 0),
(9, 'Sponsor pH7CMS 9 (300x250)', '<a href="#0"><img data-src="holder.js/300x250" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 300, 250, 0, 0),
(10, 'Sponsor pH7CMS 10 (300x250)', '<a href="#0"><img data-src="holder.js/300x250" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 300, 250, 0, 0),
(11, 'Sponsor pH7CMS 11 (336x280)', '<a href="#0"><img data-src="holder.js/336x280" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 336, 280, 0, 0),
(12, 'Sponsor pH7CMS 12 (336x280)', '<a href="#0"><img data-src="holder.js/336x280" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 336, 280, 0, 0),
(13, 'Sponsor pH7CMS 13 (120x600)', '<a href="#0"><img data-src="holder.js/120x600" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 120, 600, 0, 0),
(14, 'Sponsor pH7CMS 14 (120x600)', '<a href="#0"><img data-src="holder.js/120x600" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 120, 600, 0, 0),
(15, 'Sponsor pH7CMS 15 (160x600)', '<a href="#0"><img data-src="holder.js/160x600" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 160, 600, 0, 0),
(16, 'Sponsor pH7CMS 16 (160x600)', '<a href="#0"><img data-src="holder.js/160x600" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 160, 600, 0, 0);


CREATE TABLE IF NOT EXISTS pH7_AdsAffiliates (
  adsId smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(40) DEFAULT NULL,
  code text,
  active enum('1','0') DEFAULT '1',
  width smallint(3) DEFAULT NULL,
  height smallint(3) DEFAULT NULL,
  PRIMARY KEY (adsId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO pH7_AdsAffiliates (adsId, name, code, active, width, height) VALUES
(1, 'Affiliate Banner 1 (728x90)', '<a href="%affiliate_url%"><img data-src="holder.js/728x90" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 728, 90),
(2, 'Affiliate Banner 2 (728x90)', '<a href="%affiliate_url%/signup"><img data-src="holder.js/728x90" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 728, 90),
(3, 'Affiliate Banner 3 (200x200)', '<a href="%affiliate_url%"><img data-src="holder.js/200x200" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 200, 200),
(4, 'Affiliate Banner 4 (200x200)', '<a href="%affiliate_url%/signup"><img data-src="holder.js/200x200" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 200, 200),
(5, 'Affiliate Banner 5 (250x250)', '<a href="%affiliate_url%"><img data-src="holder.js/250x250" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 250, 250),
(6, 'Affiliate Banner 6 (250x250)', '<a href="%affiliate_url%/signup"><img data-src="holder.js/250x250" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 250, 250),
(7, 'Affiliate Banner 7 (468x60)', '<a href="%affiliate_url%"><img data-src="holder.js/468x60" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 468, 60),
(8, 'Affiliate Banner 8 (468x60)', '<a href="%affiliate_url%/signup"><img data-src="holder.js/468x60" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 468, 60),
(9, 'Affiliate Banner 9 (300x250)', '<a href="%affiliate_url%"><img data-src="holder.js/300x250" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 300, 250),
(10, 'Affiliate Banner 10 (300x250)', '<a href="%affiliate_url%/signup"><img data-src="holder.js/300x250" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 300, 250),
(11, 'Affiliate Banner 11 (336x280)', '<a href="%affiliate_url%"><img data-src="holder.js/336x280" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 336, 280),
(12, 'Affiliate Banner 12 (336x280)', '<a href="%affiliate_url%/signup"><img data-src="holder.js/336x280" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 336, 280),
(13, 'Affiliate Banner 13 (120x600)', '<a href="%affiliate_url%"><img data-src="holder.js/120x600" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 120, 600),
(14, 'Affiliate Banner 14 (120x600)', '<a href="%affiliate_url%/signup"><img data-src="holder.js/120x600" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 120, 600),
(15, 'Affiliate Banner 15 (160x600)', '<a href="%affiliate_url%"><img data-src="holder.js/160x600" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 160, 600),
(16, 'Affiliate Banner 16 (160x600)', '<a href="%affiliate_url%/signup"><img data-src="holder.js/160x600" alt="%site_name% by %software_name%" title="%site_name% powered by %software_name%" /></a>', '0', 160, 600);


CREATE TABLE IF NOT EXISTS pH7_AlbumsPictures (
  albumId int(10) unsigned NOT NULL AUTO_INCREMENT,
  profileId int(10) unsigned NOT NULL,
  name varchar(80) NOT NULL,
  thumb char(11) NOT NULL,
  approved enum('1','0') DEFAULT '1',
  votes int(9) unsigned DEFAULT '0',
  score float(9) unsigned DEFAULT '0',
  views int(10) unsigned DEFAULT '0',
  description varchar(255) DEFAULT NULL,
  createdDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  updatedDate datetime DEFAULT NULL,
  PRIMARY KEY (albumId),
  FOREIGN KEY (profileId) REFERENCES pH7_Members(profileId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_AlbumsVideos (
  albumId int(10) unsigned NOT NULL AUTO_INCREMENT,
  profileId int(10) unsigned NOT NULL,
  name varchar(80) NOT NULL,
  thumb char(11) NOT NULL,
  approved enum('1','0') DEFAULT '1',
  votes int(9) unsigned DEFAULT '0',
  score float(9) unsigned DEFAULT '0',
  views int(10) unsigned DEFAULT '0',
  description varchar(255) DEFAULT NULL,
  createdDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  updatedDate datetime DEFAULT NULL,
  PRIMARY KEY (albumId),
  FOREIGN KEY (profileId) REFERENCES pH7_Members(profileId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_Pictures (
  pictureId int(10) unsigned NOT NULL AUTO_INCREMENT,
  profileId int(10) unsigned NOT NULL,
  albumId int(10) unsigned NOT NULL,
  title varchar(80) NOT NULL,
  description varchar(255) DEFAULT NULL,
  file varchar(40) NOT NULL,
  approved enum('1','0') DEFAULT '1',
  votes int(9) unsigned DEFAULT '0',
  score float(9) unsigned DEFAULT '0',
  views int(10) unsigned DEFAULT '0',
  createdDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  updatedDate datetime DEFAULT NULL,
  PRIMARY KEY (pictureId),
  FOREIGN KEY (albumId) REFERENCES pH7_AlbumsPictures(albumId),
  FOREIGN KEY (profileId) REFERENCES pH7_Members(profileId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_Videos (
  videoId int(10) unsigned NOT NULL AUTO_INCREMENT,
  profileId int(10) unsigned NOT NULL,
  albumId int(10) unsigned NOT NULL,
  title varchar(80) DEFAULT NULL,
  description varchar(255) DEFAULT NULL,
  file varchar(255) DEFAULT NULL, -- e.g. http://youtu.be/4fplAZfO9KY or local file server.
  thumb varchar(255) DEFAULT NULL, -- e.g. http://img.youtube.com/vi/4fplAZfO9KY/default.jpg or local file server.
  approved enum('1','0') NOT NULL DEFAULT '1',
  votes int(9) unsigned DEFAULT '0',
  score float(9) unsigned DEFAULT '0',
  views int(10) unsigned DEFAULT '0',
  createdDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  updatedDate datetime DEFAULT NULL,
  duration int(9) NOT NULL,
  PRIMARY KEY (videoId),
  FOREIGN KEY (albumId) REFERENCES pH7_AlbumsVideos(albumId),
  FOREIGN KEY (profileId) REFERENCES pH7_Members(profileId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_AnalyticsApi (
  analyticsId tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(32) DEFAULT NULL,
  code text,
  active enum('1','0') DEFAULT '1',
  PRIMARY KEY (analyticsId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO pH7_AnalyticsApi (analyticsId, name, code, active) VALUES
(1, 'Analytics Code', '', '1');


CREATE TABLE IF NOT EXISTS pH7_Blogs (
  blogId mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  postId varchar(60) NOT NULL,
  langId char(2) NOT NULL DEFAULT '',
  title varchar(100) DEFAULT NULL,
  content longtext NOT NULL,
  pageTitle varchar(100) NOT NULL,
  metaDescription varchar(255) NOT NULL,
  metaKeywords varchar(255) NOT NULL,
  slogan varchar(200) NOT NULL,
  metaRobots varchar(50) NOT NULL,
  metaAuthor varchar(50) NOT NULL,
  metaCopyright varchar(50) NOT NULL,
  tags varchar(200) DEFAULT NULL,
  votes int(9) unsigned DEFAULT '0',
  score float(9) unsigned DEFAULT '0',
  views int(10) unsigned DEFAULT '0',
  enableComment enum('1','0') DEFAULT '1',
  createdDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  updatedDate datetime DEFAULT NULL,
  PRIMARY KEY (blogId),
  UNIQUE KEY postId (postId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_BlogsCategories (
  categoryId smallint(4) unsigned NOT NULL,
  blogId mediumint(10) unsigned NOT NULL,
  INDEX (categoryId),
  INDEX (blogId),
  FOREIGN KEY (blogId) REFERENCES pH7_Blogs(blogId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS pH7_BlogsDataCategories (
  categoryId smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(40) DEFAULT NULL,
  PRIMARY KEY (categoryId),
  UNIQUE KEY (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO pH7_BlogsDataCategories (categoryId, name) VALUES
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


CREATE TABLE IF NOT EXISTS pH7_Notes (
  noteId int(10) unsigned NOT NULL AUTO_INCREMENT,
  profileId int(10) unsigned NOT NULL,
  postId varchar(60) NOT NULL,
  langId char(2) NOT NULL DEFAULT '',
  title varchar(100) DEFAULT NULL,
  content longtext NOT NULL,
  pageTitle varchar(100) NOT NULL,
  metaDescription varchar(255) NOT NULL,
  metaKeywords varchar(255) NOT NULL,
  slogan varchar(200) NOT NULL,
  metaRobots varchar(50) NOT NULL,
  metaAuthor varchar(50) NOT NULL,
  metaCopyright varchar(50) NOT NULL,
  tags varchar(200) DEFAULT NULL,
  thumb char(24) DEFAULT NULL,
  votes int(9) unsigned DEFAULT '0',
  score float(9) unsigned DEFAULT '0',
  views int(10) unsigned DEFAULT '0',
  enableComment enum('1','0') DEFAULT '1',
  createdDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  updatedDate datetime DEFAULT NULL,
  approved tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (noteId),
  UNIQUE KEY postId (postId),
  FOREIGN KEY (profileId) REFERENCES pH7_Members(profileId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_NotesCategories (
  categoryId smallint(4) unsigned NOT NULL,
  noteId int(10) unsigned NOT NULL,
  profileId int(10) unsigned NOT NULL,
  INDEX (categoryId),
  INDEX (noteId),
  FOREIGN KEY (noteId) REFERENCES pH7_Notes(noteId),
  FOREIGN KEY (profileId) REFERENCES pH7_Members(profileId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_NotesDataCategories (
  categoryId smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(40) DEFAULT NULL,
  PRIMARY KEY (categoryId),
  UNIQUE KEY (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO pH7_NotesDataCategories (categoryId, name) VALUES
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


CREATE TABLE IF NOT EXISTS pH7_CommentsBlog (
  commentId int(10) unsigned NOT NULL AUTO_INCREMENT,
  sender int(10) unsigned NOT NULL,
  recipient mediumint(10) unsigned NOT NULL,
  comment text NOT NULL,
  createdDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  updatedDate datetime DEFAULT NULL,
  approved enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (commentId),
  -- Maybe we'll let the comments of the members even if they are deleted or we will allow administrator to leave a comment, so we comment on this line.
  -- FOREIGN KEY (sender) REFERENCES pH7_Members(profileId),
  FOREIGN KEY (recipient) REFERENCES pH7_Blogs(blogId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_CommentsNote (
  commentId int(10) unsigned NOT NULL AUTO_INCREMENT,
  sender int(10) unsigned NOT NULL,
  recipient int(10) unsigned NOT NULL,
  comment text NOT NULL,
  createdDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  updatedDate datetime DEFAULT NULL,
  approved enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (commentId),
  -- Maybe we'll let the comments of the members even if they are deleted.
  -- FOREIGN KEY (sender) pH7_Members(profileId),
  FOREIGN KEY (recipient) REFERENCES pH7_Notes(noteId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_CommentsPicture (
  commentId int(10) unsigned NOT NULL AUTO_INCREMENT,
  sender int(10) unsigned NOT NULL,
  recipient int(10) unsigned NOT NULL,
  comment text NOT NULL,
  createdDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  updatedDate datetime DEFAULT NULL,
  approved enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (commentId),
  -- Maybe we'll let the comments of the members even if they are deleted.
  -- FOREIGN KEY (sender) pH7_Members(profileId),
  FOREIGN KEY (recipient) REFERENCES pH7_Pictures(pictureId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_CommentsVideo (
  commentId int(10) unsigned NOT NULL AUTO_INCREMENT,
  sender int(10) unsigned NOT NULL,
  recipient int(10) unsigned NOT NULL,
  comment text NOT NULL,
  createdDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  updatedDate datetime DEFAULT NULL,
  approved enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (commentId),
  -- Maybe we'll let the comments of the members even if they are deleted.
  -- FOREIGN KEY (sender) pH7_Members(profileId),
  FOREIGN KEY (recipient) REFERENCES pH7_Videos(videoId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_CommentsGame (
  commentId int(10) unsigned NOT NULL AUTO_INCREMENT,
  sender int(10) unsigned NOT NULL,
  recipient int(10) unsigned NOT NULL,
  comment text NOT NULL,
  createdDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  updatedDate datetime DEFAULT NULL,
  approved enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (commentId),
  -- Maybe we'll let the comments of the members even if they are deleted.
  -- FOREIGN KEY (sender) pH7_Members(profileId),
  FOREIGN KEY (recipient) REFERENCES pH7_Games(gameId) -- Warning: You must first download the file "pH7_Game.sql" for this table can be inserted because it uses a foreign key.
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_CommentsProfile (
  commentId int(10) unsigned NOT NULL AUTO_INCREMENT,
  sender int(10) unsigned NOT NULL,
  recipient int(10) unsigned NOT NULL,
  comment text NOT NULL,
  createdDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  updatedDate datetime DEFAULT NULL,
  approved enum('1','0') DEFAULT '1',
  PRIMARY KEY (commentId),
  -- Maybe we'll let the comments of the members even if they are deleted.
  -- FOREIGN KEY (sender) pH7_Members(profileId),
  FOREIGN KEY (recipient) REFERENCES pH7_Members(profileId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_ForumsCategories (
  categoryId smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  title varchar(60) DEFAULT NULL,
  PRIMARY KEY (categoryId),
  UNIQUE KEY (title)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO pH7_ForumsCategories (categoryId, title) VALUES
(1, 'General'),
(2, 'Free Online Dating Site'),
(3, 'Business');


CREATE TABLE IF NOT EXISTS pH7_Forums (
  forumId mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(80) NOT NULL DEFAULT 'New forum',
  description varchar(255) NOT NULL,
  categoryId smallint(4) unsigned DEFAULT NULL,
  createdDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  updatedDate datetime DEFAULT NULL,
  PRIMARY KEY (forumId),
  FOREIGN KEY (categoryId) REFERENCES pH7_ForumsCategories(categoryId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO pH7_Forums (forumId, name, description, categoryId) VALUES
(1, 'Hello', 'Free dating site', 1),
(2, 'Online Dating', 'Discussion about the online dating websites', 2),
(3, 'The Best Dating Site', 'The best dating site', 1);


CREATE TABLE IF NOT EXISTS pH7_ForumsTopics (
  topicId int(10) unsigned NOT NULL AUTO_INCREMENT,
  forumId mediumint(10) unsigned DEFAULT NULL,
  profileId int(10) unsigned NOT NULL,
  title varchar(100) NOT NULL,
  message text NOT NULL,
  approved enum('1','0') DEFAULT '1',
  createdDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  updatedDate datetime DEFAULT NULL,
  views int(11) NOT NULL DEFAULT '0',
  -- Maybe we'll let the topic of member even if the member is deleted
  -- FOREIGN KEY (profileId) pH7_Members(profileId),
  FOREIGN KEY (forumId) REFERENCES pH7_Forums(forumId),
  PRIMARY KEY (topicId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_ForumsMessages (
  messageId int(10) unsigned NOT NULL AUTO_INCREMENT,
  topicId int(10) unsigned NOT NULL,
  profileId int(10) unsigned NOT NULL,
  message text NOT NULL,
  approved enum('1','0') DEFAULT '1',
  createdDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  updatedDate datetime DEFAULT NULL,
  -- Maybe we'll let the topic of member even if the member is deleted
  -- FOREIGN KEY (profileId) pH7_Members(profileId),
  FOREIGN KEY (topicId) REFERENCES pH7_ForumsTopics(topicId),
  PRIMARY KEY (messageId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_LanguagesInfo (
  langId varchar(5) NOT NULL,
  name varchar(60) NOT NULL,
  charset varchar(15) NOT NULL,
  active enum('0','1') NOT NULL DEFAULT '0',
  direction enum('ltr','rtl') NOT NULL DEFAULT 'ltr',
  author varchar(60) NOT NULL,
  website varchar(120) DEFAULT NULL,
  email varchar(120) DEFAULT NULL,
  PRIMARY KEY (langId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO pH7_LanguagesInfo (langId, name, charset, active, direction, author, website, email) VALUES
('en_US', 'English', 'UTF-8', '1', 'ltr', 'Pierre-Henry Soria', 'http://hizup.com', 'phs@hizup.net');


CREATE TABLE IF NOT EXISTS pH7_Likes (
  keyId varchar(255) NOT NULL,
  votes int(10) unsigned NOT NULL,
  lastVote datetime NOT NULL,
  lastIp varchar(45) NOT NULL,
  UNIQUE KEY keyId (keyId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS pH7_LogError (
  logId mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  logError longtext,
  PRIMARY KEY (logId),
  FULLTEXT KEY logError (logError) -- FULLTEXT is not supported by InnoDB in MySQL < 5.6.4, so set MyISAM engine
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_AdminsAttemptsLogin (
  attemptsId int(10) unsigned NOT NULL AUTO_INCREMENT,
  ip varchar(45) NOT NULL DEFAULT '',
  attempts smallint(5) unsigned NOT NULL ,
  lastLogin DATETIME NOT NULL,
  PRIMARY KEY (attemptsId),
  UNIQUE KEY (ip)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS pH7_MembersAttemptsLogin (
  attemptsId int(10) unsigned NOT NULL AUTO_INCREMENT,
  ip varchar(45) NOT NULL DEFAULT '',
  attempts smallint(5) unsigned NOT NULL ,
  lastLogin DATETIME NOT NULL,
  PRIMARY KEY (attemptsId),
  UNIQUE KEY (ip)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS pH7_AffiliatesAttemptsLogin (
  attemptsId int(10) unsigned NOT NULL AUTO_INCREMENT,
  ip varchar(45) NOT NULL DEFAULT '',
  attempts smallint(5) unsigned NOT NULL ,
  lastLogin DATETIME NOT NULL,
  PRIMARY KEY (attemptsId),
  UNIQUE KEY (ip)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS pH7_AdminsLogLogin (
  logId mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  email varchar(120) NOT NULL DEFAULT '',
  username varchar(64) NOT NULL DEFAULT '',
  password varchar(40) DEFAULT NULL,
  status varchar(60) NOT NULL DEFAULT '',
  ip varchar(45) NOT NULL DEFAULT '',
  dateTime timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (logId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_MembersLogLogin (
  logId mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  email varchar(120) NOT NULL DEFAULT '',
  username varchar(64) NOT NULL DEFAULT '',
  password varchar(40) DEFAULT NULL,
  status varchar(60) NOT NULL DEFAULT '',
  ip varchar(45) NOT NULL DEFAULT '',
  dateTime timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (logId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_AffiliatesLogLogin (
  logId mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  email varchar(120) NOT NULL DEFAULT '',
  username varchar(64) NOT NULL DEFAULT '',
  password varchar(40) DEFAULT NULL,
  status varchar(60) NOT NULL DEFAULT '',
  ip varchar(45) NOT NULL DEFAULT '',
  dateTime timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (logId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_AdminsLogSess (
  profileId tinyint(3) unsigned DEFAULT NULL,
  username varchar(40) DEFAULT NULL,
  password varchar(240) DEFAULT NULL,
  email varchar(120) DEFAULT NULL,
  firstName varchar(50) DEFAULT NULL,
  lastName varchar(50) DEFAULT NULL,
  sessionHash varchar(40) NOT NULL,
  idHash char(32) NOT NULL,
  lastActivity int(10) unsigned NOT NULL,
  location varchar(255) DEFAULT NULL,
  ip varchar(45) NOT NULL DEFAULT '127.0.0.1',
  userAgent varchar(100) NOT NULL,
  guest smallint(4) unsigned NOT NULL DEFAULT 1,
  dateTime timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY profileId (profileId),
  FOREIGN KEY (profileId) REFERENCES pH7_Admins(profileId),
  KEY sessionHash (sessionHash),
  KEY lastActivity (lastActivity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS pH7_MembersLogSess (
  profileId int(10) unsigned DEFAULT NULL,
  username varchar(40) DEFAULT NULL,
  password varchar(120) DEFAULT NULL,
  email varchar(120) DEFAULT NULL,
  firstName varchar(50) DEFAULT NULL,
  lastName varchar(50) DEFAULT NULL,
  sessionHash varchar(40) NOT NULL,
  idHash char(32) NOT NULL,
  lastActivity int(10) unsigned NOT NULL,
  location varchar(255) DEFAULT NULL,
  ip varchar(45) NOT NULL DEFAULT '127.0.0.1',
  userAgent varchar(100) NOT NULL,
  guest smallint(4) unsigned NOT NULL DEFAULT 1,
  dateTime timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY profileId (profileId),
  FOREIGN KEY (profileId) REFERENCES pH7_Members(profileId),
  KEY sessionHash (sessionHash),
  KEY lastActivity (lastActivity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS pH7_AffiliatesLogSess (
  profileId int(10) unsigned DEFAULT NULL,
  username varchar(40) DEFAULT NULL,
  password varchar(120) DEFAULT NULL,
  email varchar(120) DEFAULT NULL,
  firstName varchar(50) DEFAULT NULL,
  lastName varchar(50) DEFAULT NULL,
  sessionHash varchar(40) NOT NULL,
  idHash char(32) NOT NULL,
  lastActivity int(10) unsigned NOT NULL,
  location varchar(255) DEFAULT NULL,
  ip varchar(45) NOT NULL DEFAULT '127.0.0.1',
  userAgent varchar(100) NOT NULL,
  guest smallint(4) unsigned NOT NULL DEFAULT 1,
  dateTime timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY profileId (profileId),
  FOREIGN KEY (profileId) REFERENCES pH7_Affiliates(profileId),
  KEY sessionHash (sessionHash),
  KEY lastActivity (lastActivity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS pH7_MembersBackground (
  profileId int(10) unsigned NOT NULL,
  file varchar(5) NOT NULL,
  approved tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY profileId (profileId),
  FOREIGN KEY (profileId) REFERENCES pH7_Members(profileId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS pH7_MembersWhoViews (
  profileId int(10) unsigned NOT NULL,
  visitorId int(10) unsigned NOT NULL,
  lastVisit datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  INDEX profileId (profileId),
  INDEX visitorId (visitorId),
  FOREIGN KEY (profileId) REFERENCES pH7_Members(profileId),
  FOREIGN KEY (visitorId) REFERENCES pH7_Members(profileId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS pH7_MembersFriends (
  profileId int(10) unsigned NOT NULL,
  friendId int(10) unsigned NOT NULL,
  requestDate datetime DEFAULT NULL,
  pending tinyint(1) unsigned NOT NULL DEFAULT '0',
  INDEX profileId (profileId),
  INDEX friendId (friendId),
  FOREIGN KEY (profileId) REFERENCES pH7_Members(profileId),
  FOREIGN KEY (friendId) REFERENCES pH7_Members(profileId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS pH7_MembersWall (
  wallId int(10) unsigned NOT NULL AUTO_INCREMENT,
  profileId int(10) unsigned NOT NULL DEFAULT '0',
  post text CHARACTER SET armscii8,
  createdDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  updatedDate datetime DEFAULT NULL,
  PRIMARY KEY (wallId),
  FOREIGN KEY (profileId) REFERENCES pH7_Members(profileId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_Messages (
  messageId int(10) unsigned NOT NULL AUTO_INCREMENT,
  sender int(10) unsigned NOT NULL DEFAULT '0',
  recipient int(10) unsigned NOT NULL DEFAULT '0',
  title varchar(30) NOT NULL DEFAULT '',
  message text NOT NULL,
  sendDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  status tinyint(1) unsigned NOT NULL DEFAULT '1',
  trash set('sender','recipient') NOT NULL DEFAULT '',
  toDelete set('sender','recipient') NOT NULL DEFAULT '',
  PRIMARY KEY (messageId),
  -- This is wrong, because now administrators can also send emails.
  -- FOREIGN KEY (sender) REFERENCES pH7_Members(profileId),
  FOREIGN KEY (recipient) REFERENCES pH7_Members(profileId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_Messenger (
  messengerId int(10) unsigned NOT NULL AUTO_INCREMENT,
  fromUser varchar(40) NOT NULL DEFAULT '',
  toUser varchar(40) NOT NULL DEFAULT '',
  message text NOT NULL,
  sent datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  recd int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (messengerId),
  FOREIGN KEY (fromUser) REFERENCES pH7_Members(username),
  FOREIGN KEY (toUser) REFERENCES pH7_Members(username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_MetaMain (
  langId varchar(5) NOT NULL DEFAULT '',
  pageTitle varchar(100) NOT NULL,
  metaDescription varchar(255) NOT NULL,
  metaKeywords varchar(255) NOT NULL,
  headline varchar(50) NOT NULL,
  slogan varchar(200) NOT NULL,
  promoText text DEFAULT NULL,
  metaRobots varchar(50) NOT NULL DEFAULT '',
  metaAuthor varchar(50) NOT NULL DEFAULT '',
  metaCopyright varchar(50) NOT NULL DEFAULT '',
  metaRating varchar(50) NOT NULL DEFAULT '',
  metaDistribution varchar(50) NOT NULL DEFAULT '',
  metaCategory varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (langId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO pH7_MetaMain (langId, pageTitle, metaDescription, metaKeywords, headline, slogan, promoText, metaRobots, metaAuthor, metaCopyright, metaRating, metaDistribution, metaCategory) VALUES
('en_US', 'Home', 'The Best Online Social Dating Service to meet people and keep in touch your friends', 'meet people, community, single, friends, meet singles, women, men, dating site, dating service, dating website, online dating website', 'Be on the best e-place!', 'The Best place to Meet Nice People', 'You''re on the best place for meeting new people nearby! Chat, Flirt, Socialize and have Fun!<br />Create any Social Dating Business App or Website like this one with the #1 <a href="https://ph7cms.com">Dating Web App Builder</a>. It''s Professional, Modern, Open Source, and gives you the Best Way to launch a new Social/Dating Business!', 'index, follow, all', 'Pierre-Henry Soria', 'Copyright Pierre-Henry Soria. All Rights Reserved.', 'general', 'global', 'dating');


CREATE TABLE IF NOT EXISTS pH7_SysModsEnabled (
  moduleId tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  moduleTitle varchar(50) NOT NULL,
  folderName varchar(20) NOT NULL,
  premiumMod enum('0','1') NOT NULL DEFAULT '0', -- If the module required pH7CMSPro (http://ph7cms.com/pro/)
  enabled enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (moduleId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO pH7_SysModsEnabled (moduleTitle, folderName, premiumMod, enabled) VALUES
('Affiliate', 'affiliate', '0', '1'),
('Chat', 'chat', '1', '0'),
('Chatroulette', 'chatroulette', '1', '0'),
('Picture', 'picture', '0', '1'),
('Video', 'video', '0', '1'),
('Hot or Not', 'hotornot', '0', '1'),
('Forum', 'forum', '0', '1'),
('Note (blog for users)', 'note', '0', '1'),
('Blog (company blog)', 'blog', '0', '1'),
('Love Calculator', 'love-calculator', '0', '1'),
('Mail (private message)', 'mail', '0', '1'),
('Instant Messaging (IM)', 'im', '0', '1'),
('Related Profiles', 'related-profile', '0', '1'),
('Friends', 'friend', '0', '1'),
('User Dashboard', 'user-dashboard', '0', '1'),
('Game', 'game', '0', '1'),
('Newsletter', 'newsletter', '0', '1'),
('Invite Friends', 'invite', '0', '1'),
('Social Media Auth (connect module)', 'connect', '0', '0'),
('Webcam', 'webcam', '0', '1');


CREATE TABLE IF NOT EXISTS pH7_Modules (
  moduleId smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  vendorName varchar(40) NOT NULL,
  moduleName varchar(40) NOT NULL,
  version varchar(6) NOT NULL,
  uri varchar(40) DEFAULT NULL,
  path varchar(255) DEFAULT NULL,
  active enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (moduleId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO pH7_Modules (vendorName, moduleName, version, active) VALUES
/* Gives the current version of the SQL schema of pH7CMS (this helps to update and shows whether it is necessary or not to update the database as well) */
('pH7CMS', 'SQL System Schema', '1.3.4', 1);


CREATE TABLE IF NOT EXISTS pH7_Report (
  reportId smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  reporterId int(10) unsigned DEFAULT NULL,
  spammerId int(10) unsigned DEFAULT NULL,
  dateTime datetime DEFAULT NULL,
  contentType enum('user','avatar','mail','comment','photo','video','forum','note') NOT NULL DEFAULT 'user',
  description varchar(255) DEFAULT NULL,
  url varchar(255) DEFAULT NULL,
  PRIMARY KEY (reportId),
  FOREIGN KEY (reporterId) REFERENCES pH7_Members(profileId),
  FOREIGN KEY (spammerId) REFERENCES pH7_Members(profileId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_Settings (
  `name` varchar(64) NOT NULL,
  value varchar(150) DEFAULT '',
  `desc` varchar(120) DEFAULT '' COMMENT 'Informative desc about the setting',
  `group` varchar(12) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO pH7_Settings (`name`, value, `desc`, `group`) VALUES
('siteName', 'My Social Dating App', '', 'general'),
('adminEmail', @sAdminEmail, '', 'email'),
('defaultLanguage', 'en_US', '', 'language'),
('defaultTemplate', 'base', '', 'design'),
('defaultSysModule', 'user', 'The default module running by default on the index page. Recommended to keep the "user" module', 'general'),
('emailName', 'pH7CMS', '', 'email'),
('feedbackEmail', @sFeedbackEmail, '', 'email'),
('splashPage', 1, 'Use Splash Page | enable = 1 or disable = 0', 'homepage'),
('usersBlock', 1, '0 to disable or 1 to enable the profile photos on the homepage', 'homepage'),
('bgSplashVideo', 1, '0 to disable or 1 to enable the background splash video', 'homepage'),
('numberProfileSplashPage', 44, 'Number of profiles to display on the splash homepage', 'homepage'),
('fullAjaxSite', 0, 'enable = 1 or disable = 0', 'general'),
('ipLogin', '', '', 'security'),
('timeDelayUserRegistration', 1440, '1440 minutes = 24 hours (in minutes!)', 'spam'),
('timeDelayAffRegistration', 2880, '2880 minutes = 2 days (in minutes!)', 'spam'),
('timeDelaySendNote', 20, 'Waiting time to add a new note post, in minutes!', 'spam'),
('timeDelaySendMail', 3, 'Waiting time to send a new message, in minutes!', 'spam'),
('timeDelaySendComment', 5, 'Waiting time to send a new comment, in minutes!', 'spam'),
('timeDelaySendForumTopic', 5, 'Waiting time to send a new topic in the forum, in minutes!', 'spam'),
('timeDelaySendForumMsg', 10, 'Waiting time to send a reply message in the same topic, in minutes!', 'spam'),
('isCaptchaUserSignup', 0, '0 for disable or 1 for enable', 'spam'),
('isCaptchaAffiliateSignup', 0, '0 for disable or 1 for enable', 'spam'),
('isCaptchaMail', 0, '0 for disable or 1 for enable', 'spam'),
('isCaptchaComment', 0, '0 for disable or 1 for enable', 'spam'),
('isCaptchaForum', 0, '0 for disable or 1 for enable', 'spam'),
('isCaptchaNote', 0, '0 for disable or 1 for enable', 'spam'),
('mailType', 'mail', '', 'email'),
('mapType', 'roadmap', 'Choose between: ''roadmap'', ''hybrid'', ''terrain'', ''satellite''', 'map'),
('maxAgeRegistration', 99, '', 'registration'),
('minAgeRegistration', 18, '', 'registration'),
('minUsernameLength', 3, '', 'registration'),
('maxUsernameLength', 30, '', 'registration'),
('requireRegistrationAvatar', 0, '', 'registration'),
('userActivationType', 1, '1 = no activation, 2 = email activation, 3 = Manual activation by the administrator', 'registration'),
('affActivationType', 1, '1 = no activation, 2 = email activation, 3 = Manual activation by the administrator', 'registration'),
('defaultMembershipGroupId', 2, 'Default Membership Group', 'registration'),
('minPasswordLength', 6, '', 'security'),
('maxPasswordLength', 60, '', 'security'),
('isUserLoginAttempt', 1, 'Enable blocking connection attempts abusive. Enable = 1 or disable = 0', 'security'),
('isAdminLoginAttempt', 1, 'Enable blocking connection attempts abusive. Enable = 1 or disable = 0', 'security'),
('isAffiliateLoginAttempt', 1, 'Enable blocking connection attempts abusive. Enable = 1 or disable = 0', 'security'),
('maxUserLoginAttempts', 30, 'Maximum login attempts before blocking', 'security'),
('maxAffiliateLoginAttempts', 20, 'Maximum login attempts before blocking', 'security'),
('maxAdminLoginAttempts', 10, 'Maximum login attempts before blocking', 'security'),
('loginUserAttemptTime', 60, 'Time before a new connection attempt, in minutes!', 'security'),
('loginAffiliateAttemptTime', 60, 'Time before a new connection attempt, in minutes!', 'security'),
('loginAdminAttemptTime', 120, 'Time before a new connection attempt, in minutes!', 'security'),
('avatarManualApproval', 0, '0 to disable or 1 to enable ', 'moderation'),
('bgProfileManualApproval', 0, 'Background Profile Manual Approval. 0 to disable or 1 to enable ', 'moderation'),
('noteManualApproval', 0, '0 to disable or 1 to enable ', 'moderation'),
('pictureManualApproval', 0, '0 to disable or 1 to enable ', 'moderation'),
('videoManualApproval', 0, '0 to disable or 1 to enable ', 'moderation'),
('webcamPictureManualApproval', 0, '0 to disable or 1 to enable', 'moderation'),
('nudityFilter', 0, '1 = enable | 0 = disable', 'moderation'),
('defaultVideo', @sDefaultVideoUrl, 'Video by default if no video is found', 'video'),
('autoplayVideo', 1, '1 = Autoplay is enabled, 0 = Autoplay is disabled', 'video'),
('returnEmail', @sNoReplyEmail, 'Generally noreply@yoursite.com', 'email'),
('sendReportMail', 1, 'Send the Report by eMail (1 = enable, 0 = disable)', 'security'),
('siteStatus', 'enable', 'enable or maintenance', 'general'),
('smtpHostName', 'mail.example.com', '', 'email'),
('smtpPassword', 123456, '', 'email'),
('smtpPort', 25, '', 'email'),
('watermarkTextImage', 'pH7CMS.com', 'Watermark text', 'image'),
('sizeWatermarkTextImage', 2, 'Between 0 to 5', 'image'),
('banWordReplace', '[removed]',  '',  'security'),
('securityToken', 0, '0 to disable or 1 to enable the CSRF security token in the forms', 'security'),
('securityTokenLifetime', 720, 'Time in seconds to the CSRF security token. Default 720 seconds (12 mins)', 'security'),
('DDoS', 0,  '0 to disabled or 1 to enabled the DDoS attack protection',  'security'),
('isSiteValidated', 0,  '0 = site not validated | 1 = site validated',  'security'),
('cleanMsg', 0, 'Delete messages older than X days. 0 = Disable', 'pruning'),
('cleanComment', 0, 'Delete comments older than X days. 0 = Disable', 'pruning'),
('cleanMessenger', 0, 'Delete IM messages older than X days. 0 = Disable', 'pruning'),
('cronSecurityHash', 'change_this_secret_cron_word_by_yours', 'The secret word for the URL of the cron', 'automation'),
('userTimeout', 1, 'User inactivity timeout. The number of minutes that a member becomes inactive (offline)', 'automation'),
('ipApi', @sIpApiUrl, 'IP Api URL', 'api'),
('chatApi', @sChatApiUrl, 'Chat Api URL', 'api'),
('chatrouletteApi', @sChatrouletteApiUrl, 'Chatroulette Api URL', 'api'),
('googleApiKey', '', 'Google Maps API key https://developers.google.com/maps/documentation/javascript/get-api-key', 'api'),
('socialMediaWidgets', 0, 'Enable the Social Media Widgets such as Like and Sharing buttons. 0 = Disable | 1 = Enable', 'general'),
('disclaimer', 0, 'Enable a disclaimer to enter to the site. This is useful for sites with adult content. 0 = Disable | 1 = Enable', 'general'),
('cookieConsentBar', 0, 'Enable the cookie consent bar to prevent your users that your site uses cookies. 0 = Disable | 1 = Enable', 'general'),
('isSoftwareNewsFeed', 1, 'Enable the news feed. 0 = Disable | 1 = Enable', 'general');


CREATE TABLE IF NOT EXISTS pH7_Subscribers (
  profileId int(10) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(200) NOT NULL,
  email varchar(120) NOT NULL,
  joinDate datetime DEFAULT NULL,
  active tinyint(1) unsigned NOT NULL DEFAULT 2, -- 1 = Active Account, 2 = Pending Account
  ip varchar(45) NOT NULL DEFAULT '127.0.0.1',
  hashValidation varchar(40) DEFAULT NULL,
  affiliatedId int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (profileId),
  UNIQUE KEY (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_TopMenus (
  menuId smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  vendorName varchar(40) NOT NULL,
  moduleName varchar(40) NOT NULL,
  controllerName varchar(40) NOT NULL,
  actionName varchar(40) NOT NULL,
  vars varchar(60) DEFAULT NULL,
  parentMenu smallint(4) unsigned DEFAULT NULL,
  grandParentMenu smallint(4) unsigned DEFAULT NULL,
  onlyForUsers enum('0','1') NOT NULL DEFAULT '0',
  active enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (menuId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_BottomMenus (
  menuId smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  vendorName varchar(40) NOT NULL,
  moduleName varchar(40) NOT NULL,
  controllerName varchar(40) NOT NULL,
  actionName varchar(40) NOT NULL,
  vars varchar(60) DEFAULT NULL,
  parentMenu smallint(4) unsigned DEFAULT NULL,
  grandParentMenu smallint(4) unsigned DEFAULT NULL,
  active enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (menuId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_StaticFiles (
  staticId smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  file varchar(255) NOT NULL,
  fileType enum('css', 'js') NOT NULL,
  active enum('1','0') DEFAULT '1',
  PRIMARY KEY (staticId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO pH7_StaticFiles VALUES (1, '//s7.addthis.com/js/250/addthis_widget.js', 'js', '0');


CREATE TABLE IF NOT EXISTS pH7_License (
  licenseId tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  licenseKey varchar(40) NOT NULL,
  PRIMARY KEY (licenseId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO pH7_License VALUES (1, '');


CREATE TABLE IF NOT EXISTS pH7_CustomCode (
  code text,
  codeType enum('css', 'js') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO pH7_CustomCode VALUES
('/* Your custom CSS code here */\r\n', 'css'),
('/* Your custom JS code here */\r\n', 'js');
