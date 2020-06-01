--
--
-- Title:         SQL Core (base) Install File
--
-- Author:        Pierre-Henry Soria <hello@ph7cms.com>
-- Copyright:     (c) 2017-2020, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
-- Package:       PH7 / Install / Data / Sql / PostgreSQL
--
-- Upgrade:       Convert the MySQL file version to PostgreSQL
--                https://en.wikibooks.org/wiki/Converting_MySQL_to_PostgreSQL
--
--

--
-- Set the variables --
--
@sDefaultSiteName := 'My Dating WebApp';
@sAdminEmail := 'admin@yoursite.com';
@sFeedbackEmail := 'feedback@yoursite.com';
@sNoReplyEmail := 'noreply@yoursite.com';
@sIpApiUrl := 'https://whatismyipaddress.com/ip/';
@sDefaultVideoUrl := 'https://www.youtube.com/watch?v=q-1eHnBOg4A';
@sChatApiUrl := 'https://ph7cms.com/addons/chat/?name=%site_name%&url=%site_url%&skin=4';
@sChatrouletteApiUrl := 'https://ph7cms.com/addons/chatroulette/?name=%site_name%&url=%site_url%&skin=1';

@sCurrentDate := CURRENT_TIMESTAMP;
@sPassword := SHA1(RAND() + UNIX_TIMESTAMP());


CREATE SEQUENCE ph7_admins_seq;

CREATE TABLE IF NOT EXISTS ph7_admins (
  profileId smallint check (profileId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_admins_seq'),
  username varchar(40) NOT NULL,
  password varchar(120) NOT NULL,
  email varchar(120) NOT NULL,
  firstName varchar(50) DEFAULT NULL,
  lastName varchar(50) DEFAULT NULL,
  sex enum('male','female') NOT NULL DEFAULT 'male',
  lang varchar(5) NOT NULL DEFAULT 'en_US',
  timeZone varchar(6) NOT NULL DEFAULT '-6',
  joinDate timestamp(0) DEFAULT NULL,
  lastActivity timestamp(0) DEFAULT NULL,
  lastEdit timestamp(0) DEFAULT NULL,
  ban enum('0','1') DEFAULT '0',
  ip varchar(45) NOT NULL DEFAULT '127.0.0.1',
  isTwoFactorAuth enum('1','0') DEFAULT '0',
  twoFactorAuthSecret varchar(40) DEFAULT NULL,
  hashValidation varchar(40) DEFAULT NULL,
  PRIMARY KEY (profileId),
  CONSTRAINT username UNIQUE (username),
  CONSTRAINT email UNIQUE (email)
)  ;

ALTER SEQUENCE ph7_admins_seq RESTART WITH 1;


CREATE SEQUENCE ph7_memberships_seq;

CREATE TABLE IF NOT EXISTS ph7_memberships (
  groupId smallint check (groupId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_memberships_seq'),
  name varchar(64) NOT NULL DEFAULT '',
  description varchar(255) NOT NULL,
  permissions text NOT NULL,
  price decimal(10,2) check (price > 0) NOT NULL,
  expirationDays smallint check (expirationDays > 0) NOT NULL,
  enable enum('1','0') DEFAULT '1',
  PRIMARY KEY (groupId)
)  ;

ALTER SEQUENCE ph7_memberships_seq RESTART WITH 1;

INSERT INTO ph7_memberships (groupId, name, description, permissions, price, expirationDays, enable) VALUES
(1, 'Visitor (not visible)', 'This subscription is offered to all visitors who visit the site.', 'a:24:{s:21:"quick_search_profiles";s:1:"1";s:24:"advanced_search_profiles";s:1:"1";s:10:"read_mails";s:1:"0";s:10:"send_mails";s:1:"0";s:13:"view_pictures";s:1:"1";s:15:"upload_pictures";s:1:"0";s:11:"view_videos";s:1:"1";s:13:"upload_videos";s:1:"0";s:17:"instant_messaging";s:1:"0";s:4:"chat";s:1:"1";s:12:"chatroulette";s:1:"1";s:10:"hot_or_not";s:1:"1";s:15:"love_calculator";s:1:"0";s:10:"read_notes";s:1:"1";s:11:"write_notes";s:1:"0";s:15:"read_blog_posts";s:1:"1";s:13:"view_comments";s:1:"1";s:14:"write_comments";s:1:"0";s:12:"forum_access";s:1:"1";s:19:"create_forum_topics";s:1:"0";s:19:"answer_forum_topics";s:1:"0";s:12:"games_access";s:1:"1";s:13:"webcam_access";s:1:"1";s:18:"member_site_access";s:1:"0";}', 0.00, 0, '1'),
(9, 'Pending (not visible)', 'Pending subscription provisional migration to a different subscription.', 'a:24:{s:21:"quick_search_profiles";s:1:"1";s:24:"advanced_search_profiles";s:1:"1";s:10:"read_mails";s:1:"0";s:10:"send_mails";s:1:"0";s:13:"view_pictures";s:1:"1";s:15:"upload_pictures";s:1:"0";s:11:"view_videos";s:1:"1";s:13:"upload_videos";s:1:"0";s:17:"instant_messaging";s:1:"0";s:4:"chat";s:1:"1";s:12:"chatroulette";s:1:"1";s:10:"hot_or_not";s:1:"1";s:15:"love_calculator";s:1:"0";s:10:"read_notes";s:1:"1";s:11:"write_notes";s:1:"0";s:15:"read_blog_posts";s:1:"1";s:13:"view_comments";s:1:"1";s:14:"write_comments";s:1:"0";s:12:"forum_access";s:1:"1";s:19:"create_forum_topics";s:1:"0";s:19:"answer_forum_topics";s:1:"0";s:12:"games_access";s:1:"1";s:13:"webcam_access";s:1:"1";s:18:"member_site_access";s:1:"0";}', 0.00, 15, '0'),
(2, 'Regular (Free)', 'Free Membership.', 'a:24:{s:21:"quick_search_profiles";s:1:"1";s:24:"advanced_search_profiles";s:1:"1";s:10:"read_mails";s:1:"1";s:10:"send_mails";s:1:"1";s:13:"view_pictures";s:1:"1";s:15:"upload_pictures";s:1:"1";s:11:"view_videos";s:1:"1";s:13:"upload_videos";s:1:"1";s:17:"instant_messaging";s:1:"1";s:4:"chat";s:1:"1";s:12:"chatroulette";s:1:"1";s:10:"hot_or_not";s:1:"1";s:15:"love_calculator";s:1:"1";s:10:"read_notes";s:1:"1";s:11:"write_notes";s:1:"1";s:15:"read_blog_posts";s:1:"1";s:13:"view_comments";s:1:"1";s:14:"write_comments";s:1:"1";s:12:"forum_access";s:1:"1";s:19:"create_forum_topics";s:1:"1";s:19:"answer_forum_topics";s:1:"1";s:12:"games_access";s:1:"1";s:13:"webcam_access";s:1:"1";s:18:"member_site_access";s:1:"1";}', 0.00, 0, '1'),
(4, 'Platinum', 'The membership for the small budget.', 'a:24:{s:21:"quick_search_profiles";s:1:"1";s:24:"advanced_search_profiles";s:1:"1";s:10:"read_mails";s:1:"1";s:10:"send_mails";s:1:"1";s:13:"view_pictures";s:1:"1";s:15:"upload_pictures";s:1:"1";s:11:"view_videos";s:1:"1";s:13:"upload_videos";s:1:"1";s:17:"instant_messaging";s:1:"1";s:4:"chat";s:1:"1";s:12:"chatroulette";s:1:"1";s:10:"hot_or_not";s:1:"1";s:15:"love_calculator";s:1:"1";s:10:"read_notes";s:1:"1";s:11:"write_notes";s:1:"1";s:15:"read_blog_posts";s:1:"1";s:13:"view_comments";s:1:"1";s:14:"write_comments";s:1:"1";s:12:"forum_access";s:1:"1";s:19:"create_forum_topics";s:1:"1";s:19:"answer_forum_topics";s:1:"1";s:12:"games_access";s:1:"1";s:13:"webcam_access";s:1:"1";s:18:"member_site_access";s:1:"1";}', 9.99, 5, '1'),
(5, 'Silver', 'The premium membership!', 'a:24:{s:21:"quick_search_profiles";s:1:"1";s:24:"advanced_search_profiles";s:1:"1";s:10:"read_mails";s:1:"1";s:10:"send_mails";s:1:"1";s:13:"view_pictures";s:1:"1";s:15:"upload_pictures";s:1:"1";s:11:"view_videos";s:1:"1";s:13:"upload_videos";s:1:"1";s:17:"instant_messaging";s:1:"1";s:4:"chat";s:1:"1";s:12:"chatroulette";s:1:"1";s:10:"hot_or_not";s:1:"1";s:15:"love_calculator";s:1:"1";s:10:"read_notes";s:1:"1";s:11:"write_notes";s:1:"1";s:15:"read_blog_posts";s:1:"1";s:13:"view_comments";s:1:"1";s:14:"write_comments";s:1:"1";s:12:"forum_access";s:1:"1";s:19:"create_forum_topics";s:1:"1";s:19:"answer_forum_topics";s:1:"1";s:12:"games_access";s:1:"1";s:13:"webcam_access";s:1:"1";s:18:"member_site_access";s:1:"1";}', 19.99, 10, '1'),
(6, 'Gold', 'The must membership! The Gold!!!', 'a:24:{s:21:"quick_search_profiles";s:1:"1";s:24:"advanced_search_profiles";s:1:"1";s:10:"read_mails";s:1:"1";s:10:"send_mails";s:1:"1";s:13:"view_pictures";s:1:"1";s:15:"upload_pictures";s:1:"1";s:11:"view_videos";s:1:"1";s:13:"upload_videos";s:1:"1";s:17:"instant_messaging";s:1:"1";s:4:"chat";s:1:"1";s:12:"chatroulette";s:1:"1";s:10:"hot_or_not";s:1:"1";s:15:"love_calculator";s:1:"1";s:10:"read_notes";s:1:"1";s:11:"write_notes";s:1:"1";s:15:"read_blog_posts";s:1:"1";s:13:"view_comments";s:1:"1";s:14:"write_comments";s:1:"1";s:12:"forum_access";s:1:"1";s:19:"create_forum_topics";s:1:"1";s:19:"answer_forum_topics";s:1:"1";s:12:"games_access";s:1:"1";s:13:"webcam_access";s:1:"1";s:18:"member_site_access";s:1:"1";}', 29.99, 30, '1');


CREATE SEQUENCE ph7_members_seq;

CREATE TABLE IF NOT EXISTS ph7_members (
  profileId int check (profileId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_members_seq'),
  email varchar(120) NOT NULL,
  username varchar(40) NOT NULL,
  password varchar(120) NOT NULL,
  firstName varchar(50) DEFAULT NULL,
  lastName varchar(50) DEFAULT NULL,
  birthDate date NULL,
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
  views cast(11 as int) unsigned NOT NULL DEFAULT 0,
  reference varchar(255) DEFAULT NULL,
  votes cast(11 as int) DEFAULT 0,
  score float DEFAULT 0,
  credits cast(6 as int) unsigned NOT NULL DEFAULT 0, -- Not used for the moment (maybe in the future by the payment module)
  affiliatedId cast(10 as int) unsigned NOT NULL DEFAULT 0,
  active tinyint(1) unsigned NOT NULL DEFAULT 1,
  ban tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (profileId),
  FOREIGN KEY (groupId) REFERENCES ph7_memberships(groupId),
  UNIQUE KEY (username),
  UNIQUE KEY (email),
  KEY birthDate (birthDate)
) ;


CREATE SEQUENCE ph7_members_info_seq;

CREATE TABLE IF NOT EXISTS ph7_members_info (
  profileId int check (profileId > 0) NOT NULL,
  middleName varchar(50) DEFAULT NULL,
  description text DEFAULT NULL,
  punchline varchar(255) DEFAULT NULL,
  address varchar(255) DEFAULT NULL,
  city varchar(150) DEFAULT NULL,
  state varchar(150) DEFAULT NULL,
  zipCode varchar(20) DEFAULT NULL,
  country char(2) DEFAULT NULL,
  phone varchar(100) DEFAULT NULL,
  website varchar(120) DEFAULT NULL,
  socialNetworkSite varchar(120) DEFAULT NULL,
  height smallint check (height > 0) DEFAULT NULL,
  weight smallint check (weight > 0) DEFAULT NULL,
  PRIMARY KEY (profileId)
 ,
  FOREIGN KEY (profileId) REFERENCES ph7_members(profileId)
)  ;

ALTER SEQUENCE ph7_members_info_seq RESTART WITH 1;

CREATE INDEX country ON ph7_members_info (country);


CREATE TABLE IF NOT EXISTS ph7_members_privacy (
  profileId int check (profileId > 0) NOT NULL,
  privacyProfile enum('all','only_members','only_me') NOT NULL DEFAULT 'all',
  searchProfile enum('yes','no') NOT NULL DEFAULT 'yes',
  userSaveViews enum('yes','no') NOT NULL DEFAULT 'yes',
  PRIMARY KEY (profileId),
  FOREIGN KEY (profileId) REFERENCES ph7_members(profileId)
) ;


CREATE TABLE IF NOT EXISTS ph7_members_notifications (
  profileId int check (profileId > 0) NOT NULL,
  enableNewsletters smallint check (enableNewsletters > 0) NOT NULL DEFAULT 1,
  newMsg smallint check (newMsg > 0) NOT NULL DEFAULT 1,
  friendRequest smallint check (friendRequest > 0) NOT NULL DEFAULT 1,
  PRIMARY KEY (profileId),
  FOREIGN KEY (profileId) REFERENCES ph7_members(profileId)
) ;

-- GHOST User. Do not remove ghost default member!
INSERT INTO ph7_members (profileId, email, username, password, firstName, lastName, birthDate, ip, lastActivity, featured, active, userStatus, groupId, joinDate) VALUES
(1, 'ghost@ghost', 'ghost', @sPassword, 'Ghost', 'The Ghost', '1001-01-01', '00.000.00.00', @sCurrentDate, 0, 1, 1, 2, @sCurrentDate);
INSERT INTO ph7_members_info (profileId, description, address, city, state, zipCode, country) VALUES
(1, 'This profile doesn''t exist anymore. So I''m the ghost who replaces him/her during this time', 'The Ghost City', 'Ghost Town', 'Ghost State', '000000', 'US');
-- Privacy settings
INSERT INTO ph7_members_privacy (profileId, privacyProfile, searchProfile, userSaveViews) VALUES (1, 'all', 'yes', 'yes');
-- Notifications
INSERT INTO ph7_members_notifications (profileId, enableNewsletters, newMsg, friendRequest) VALUES (1, 0, 0, 0);


CREATE SEQUENCE ph7_affiliates_seq;

CREATE TABLE IF NOT EXISTS ph7_affiliates (
  profileId int check (profileId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_affiliates_seq'),
  username varchar(40) NOT NULL,
  firstName varchar(50) NOT NULL,
  lastName varchar(50) NOT NULL,
  password varchar(120) NOT NULL,
  email varchar(120) NOT NULL,
  sex enum('male','female') NOT NULL DEFAULT 'male',
  birthDate date NULL,
  ip varchar(45) NOT NULL DEFAULT '127.0.0.1',
  bankAccount varchar(150) DEFAULT NULL,
  amount decimal(8,2) NOT NULL DEFAULT '0.00',
  totalPayment decimal(8,2) NOT NULL DEFAULT '0.00',
  lastPayment decimal(8,2) NOT NULL DEFAULT '0.00',
  lastPaymentDate timestamp(0) NULL,
  lang varchar(5) NOT NULL DEFAULT 'en_US',
  hashValidation varchar(40) DEFAULT NULL,
  isTwoFactorAuth enum('1','0') DEFAULT '0',
  twoFactorAuthSecret varchar(40) DEFAULT NULL,
  refer int check (refer > 0) DEFAULT 0,
  joinDate timestamp(0) DEFAULT NULL,
  lastActivity timestamp(0) DEFAULT NULL,
  lastEdit timestamp(0) DEFAULT NULL,
  affiliatedId int check (affiliatedId > 0) NOT NULL DEFAULT 0,
  active smallint check (active > 0) NOT NULL DEFAULT 1,
  ban smallint check (ban > 0) NOT NULL DEFAULT 0,
  PRIMARY KEY (profileId),
  CONSTRAINT bankAccount UNIQUE (bankAccount), -- For the Security Bank Account --
  CONSTRAINT username UNIQUE (username),
  CONSTRAINT email UNIQUE (email)
)  ;

ALTER SEQUENCE ph7_affiliates_seq RESTART WITH 1;

CREATE INDEX birthDate ON ph7_affiliates (birthDate);


CREATE SEQUENCE ph7_affiliates_info_seq;

CREATE TABLE IF NOT EXISTS ph7_affiliates_info (
  profileId int check (profileId > 0) NOT NULL,
  middleName varchar(50) DEFAULT NULL,
  businessName varchar(100) DEFAULT NULL,
  taxId varchar(40) DEFAULT NULL, -- Tax ID, VAT, SSN, ...
  address varchar(255) DEFAULT NULL,
  country char(2) DEFAULT NULL,
  city varchar(150) DEFAULT NULL,
  state varchar(150) DEFAULT NULL,
  zipCode varchar(20) DEFAULT NULL,
  phone varchar(100) DEFAULT NULL,
  description text DEFAULT NULL,
  website varchar(120) DEFAULT NULL,
  PRIMARY KEY (profileId)
 ,
  FOREIGN KEY (profileId) REFERENCES ph7_affiliates(profileId)
)  ;

ALTER SEQUENCE ph7_affiliates_info_seq RESTART WITH 1;

CREATE INDEX country ON ph7_affiliates_info (country);


CREATE SEQUENCE ph7_block_ip_seq;

CREATE TABLE IF NOT EXISTS ph7_block_ip (
  ipId smallint check (ipId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_block_ip_seq'),
  ip varchar(45) NOT NULL,
  expiration smallint check (expiration > 0) NOT NULL,
  PRIMARY KEY (ip)
)  ;

CREATE INDEX ipId ON ph7_block_ip (ipId);


CREATE SEQUENCE ph7_ads_seq;

CREATE TABLE IF NOT EXISTS ph7_ads (
  adsId smallint check (adsId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_ads_seq'),
  name varchar(40) DEFAULT NULL,
  code text,
  active enum('1','0') DEFAULT '1',
  width smallint check (width > 0) DEFAULT NULL,
  height smallint check (height > 0) DEFAULT NULL,
  views int check (views > 0) NOT NULL DEFAULT 0,
  clicks int check (clicks > 0) NOT NULL DEFAULT 0,
  PRIMARY KEY (adsId)
)  ;

ALTER SEQUENCE ph7_ads_seq RESTART WITH 1;

INSERT INTO ph7_ads (adsId, name, code, active, width, height, views, clicks) VALUES
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


CREATE SEQUENCE ph7_ads_affiliates_seq;

CREATE TABLE IF NOT EXISTS ph7_ads_affiliates (
  adsId smallint check (adsId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_ads_affiliates_seq'),
  name varchar(40) DEFAULT NULL,
  code text,
  active enum('1','0') DEFAULT '1',
  width smallint check (width > 0) DEFAULT NULL,
  height smallint check (height > 0) DEFAULT NULL,
  PRIMARY KEY (adsId)
)  ;

ALTER SEQUENCE ph7_ads_affiliates_seq RESTART WITH 1;

INSERT INTO ph7_ads_affiliates (adsId, name, code, active, width, height) VALUES
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


CREATE SEQUENCE ph7_albums_pictures_seq;

CREATE TABLE IF NOT EXISTS ph7_albums_pictures (
  albumId int check (albumId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_albums_pictures_seq'),
  profileId int check (profileId > 0) NOT NULL,
  name varchar(80) NOT NULL,
  thumb char(11) NOT NULL,
  approved enum('1','0') DEFAULT '1',
  votes int check (votes > 0) DEFAULT 0,
  score double precision check (score > 0) DEFAULT 0,
  views int check (views > 0) DEFAULT 0,
  description varchar(255) DEFAULT NULL,
  createdDate timestamp(0) NULL,
  updatedDate timestamp(0) DEFAULT NULL,
  PRIMARY KEY (albumId),
  FOREIGN KEY (profileId) REFERENCES ph7_members(profileId)
)  ;

ALTER SEQUENCE ph7_albums_pictures_seq RESTART WITH 1;


CREATE SEQUENCE ph7_albums_videos_seq;

CREATE TABLE IF NOT EXISTS ph7_albums_videos (
  albumId int check (albumId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_albums_videos_seq'),
  profileId int check (profileId > 0) NOT NULL,
  name varchar(80) NOT NULL,
  thumb char(11) NOT NULL,
  approved enum('1','0') DEFAULT '1',
  votes int check (votes > 0) DEFAULT 0,
  score double precision check (score > 0) DEFAULT 0,
  views int check (views > 0) DEFAULT 0,
  description varchar(255) DEFAULT NULL,
  createdDate timestamp(0) NULL,
  updatedDate timestamp(0) DEFAULT NULL,
  PRIMARY KEY (albumId),
  FOREIGN KEY (profileId) REFERENCES ph7_members(profileId)
)  ;

ALTER SEQUENCE ph7_albums_videos_seq RESTART WITH 1;


CREATE SEQUENCE ph7_pictures_seq;

CREATE TABLE IF NOT EXISTS ph7_pictures (
  pictureId int check (pictureId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_pictures_seq'),
  profileId int check (profileId > 0) NOT NULL,
  albumId int check (albumId > 0) NOT NULL,
  title varchar(80) NOT NULL,
  description varchar(255) DEFAULT NULL,
  file varchar(40) NOT NULL,
  approved enum('1','0') DEFAULT '1',
  votes int check (votes > 0) DEFAULT 0,
  score double precision check (score > 0) DEFAULT 0,
  views int check (views > 0) DEFAULT 0,
  createdDate timestamp(0) NULL,
  updatedDate timestamp(0) DEFAULT NULL,
  PRIMARY KEY (pictureId),
  FOREIGN KEY (albumId) REFERENCES ph7_albums_pictures(albumId),
  FOREIGN KEY (profileId) REFERENCES ph7_members(profileId)
)  ;

ALTER SEQUENCE ph7_pictures_seq RESTART WITH 1;


CREATE SEQUENCE ph7_videos_seq;

CREATE TABLE IF NOT EXISTS ph7_videos (
  videoId int check (videoId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_videos_seq'),
  profileId int check (profileId > 0) NOT NULL,
  albumId int check (albumId > 0) NOT NULL,
  title varchar(80) DEFAULT NULL,
  description varchar(255) DEFAULT NULL,
  file varchar(255) DEFAULT NULL, -- e.g. http://youtu.be/4fplAZfO9KY or local file server.
  thumb varchar(255) DEFAULT NULL, -- e.g. http://img.youtube.com/vi/4fplAZfO9KY/default.jpg or local file server.
  approved enum('1','0') NOT NULL DEFAULT '1',
  votes int check (votes > 0) DEFAULT 0,
  score double precision check (score > 0) DEFAULT 0,
  views int check (views > 0) DEFAULT 0,
  createdDate timestamp(0) NULL,
  updatedDate timestamp(0) DEFAULT NULL,
  duration int NOT NULL,
  PRIMARY KEY (videoId),
  FOREIGN KEY (albumId) REFERENCES ph7_albums_videos(albumId),
  FOREIGN KEY (profileId) REFERENCES ph7_members(profileId)
)  ;

ALTER SEQUENCE ph7_videos_seq RESTART WITH 1;


CREATE SEQUENCE ph7_analytics_api_seq;

CREATE TABLE IF NOT EXISTS ph7_analytics_api (
  analyticsId smallint check (analyticsId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_analytics_api_seq'),
  name varchar(32) DEFAULT NULL,
  code text,
  active enum('1','0') DEFAULT '1',
  PRIMARY KEY (analyticsId)
)  ;

ALTER SEQUENCE ph7_analytics_api_seq RESTART WITH 1;

INSERT INTO ph7_analytics_api (analyticsId, name, code, active) VALUES
(1, 'Analytics Code', '', '1');


CREATE SEQUENCE ph7_blogs_seq;

CREATE TABLE IF NOT EXISTS ph7_blogs (
  blogId mediumint check (blogId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_blogs_seq'),
  postId varchar(60) NOT NULL,
  langId char(2) NOT NULL DEFAULT '',
  title varchar(100) DEFAULT NULL,
  content text NOT NULL,
  pageTitle varchar(100) NOT NULL,
  metaDescription varchar(255) NOT NULL,
  metaKeywords varchar(255) NOT NULL,
  slogan varchar(200) NOT NULL,
  metaRobots varchar(50) NOT NULL,
  metaAuthor varchar(50) NOT NULL,
  metaCopyright varchar(50) NOT NULL,
  tags varchar(200) DEFAULT NULL,
  votes int check (votes > 0) DEFAULT 0,
  score double precision check (score > 0) DEFAULT 0,
  views int check (views > 0) DEFAULT 0,
  enableComment enum('1','0') DEFAULT '1',
  createdDate timestamp(0) NULL,
  updatedDate timestamp(0) DEFAULT NULL,
  PRIMARY KEY (blogId),
  CONSTRAINT postId UNIQUE (postId)
)  ;

ALTER SEQUENCE ph7_blogs_seq RESTART WITH 1;


CREATE TABLE IF NOT EXISTS ph7_blogs_categories (
  categoryId smallint check (categoryId > 0) NOT NULL,
  blogId mediumint check (blogId > 0) NOT NULL
 ,
  FOREIGN KEY (blogId) REFERENCES ph7_blogs(blogId)
) ;

CREATE INDEX (categoryId);
CREATE INDEX (blogId);


CREATE SEQUENCE ph7_blogs_data_categories_seq;

CREATE TABLE IF NOT EXISTS ph7_blogs_data_categories (
  categoryId smallint check (categoryId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_blogs_data_categories_seq'),
  name varchar(40) DEFAULT NULL,
  PRIMARY KEY (categoryId),
  UNIQUE (name)
)  ;

ALTER SEQUENCE ph7_blogs_data_categories_seq RESTART WITH 1;

INSERT INTO ph7_blogs_data_categories (categoryId, name) VALUES
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


CREATE SEQUENCE ph7_notes_seq;

CREATE TABLE IF NOT EXISTS ph7_notes (
  noteId int check (noteId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_notes_seq'),
  profileId int check (profileId > 0) NOT NULL,
  postId varchar(60) NOT NULL,
  langId char(2) NOT NULL DEFAULT '',
  title varchar(100) DEFAULT NULL,
  content text NOT NULL,
  pageTitle varchar(100) NOT NULL,
  metaDescription varchar(255) NOT NULL,
  metaKeywords varchar(255) NOT NULL,
  slogan varchar(200) NOT NULL,
  metaRobots varchar(50) NOT NULL,
  metaAuthor varchar(50) NOT NULL,
  metaCopyright varchar(50) NOT NULL,
  tags varchar(200) DEFAULT NULL,
  thumb char(24) DEFAULT NULL,
  votes int check (votes > 0) DEFAULT 0,
  score double precision check (score > 0) DEFAULT 0,
  views int check (views > 0) DEFAULT 0,
  enableComment enum('1','0') DEFAULT '1',
  createdDate timestamp(0) NULL,
  updatedDate timestamp(0) DEFAULT NULL,
  approved smallint check (approved > 0) NOT NULL DEFAULT 1,
  PRIMARY KEY (noteId),
  CONSTRAINT postId UNIQUE (postId),
  FOREIGN KEY (profileId) REFERENCES ph7_members(profileId)
)  ;

ALTER SEQUENCE ph7_notes_seq RESTART WITH 1;


CREATE TABLE IF NOT EXISTS ph7_notes_categories (
  categoryId smallint check (categoryId > 0) NOT NULL,
  noteId int check (noteId > 0) NOT NULL,
  profileId int check (profileId > 0) NOT NULL
 ,
  FOREIGN KEY (noteId) REFERENCES ph7_notes(noteId),
  FOREIGN KEY (profileId) REFERENCES ph7_members(profileId)
)  ;

ALTER SEQUENCE ph7_notes_categories_seq RESTART WITH 1;

CREATE INDEX (categoryId);
CREATE INDEX (noteId);


CREATE SEQUENCE ph7_notes_data_categories_seq;

CREATE TABLE IF NOT EXISTS ph7_notes_data_categories (
  categoryId smallint check (categoryId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_notes_data_categories_seq'),
  name varchar(40) DEFAULT NULL,
  PRIMARY KEY (categoryId),
  UNIQUE (name)
)  ;

ALTER SEQUENCE ph7_notes_data_categories_seq RESTART WITH 1;

INSERT INTO ph7_notes_data_categories (categoryId, name) VALUES
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


CREATE SEQUENCE ph7_comments_blog_seq;

CREATE TABLE IF NOT EXISTS ph7_comments_blog (
  commentId int check (commentId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_comments_blog_seq'),
  sender int check (sender > 0) NOT NULL,
  recipient mediumint check (recipient > 0) NOT NULL,
  comment text NOT NULL,
  createdDate timestamp(0) NULL,
  updatedDate timestamp(0) DEFAULT NULL,
  approved enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (commentId),
  -- Maybe we'll let the comments of the members even if they are deleted or we will allow administrator to leave a comment, so we comment on this line.
  -- FOREIGN KEY (sender) REFERENCES ph7_members(profileId),
  FOREIGN KEY (recipient) REFERENCES ph7_blogs(blogId)
)  ;

ALTER SEQUENCE ph7_comments_blog_seq RESTART WITH 1;


CREATE SEQUENCE ph7_comments_note_seq;

CREATE TABLE IF NOT EXISTS ph7_comments_note (
  commentId int check (commentId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_comments_note_seq'),
  sender int check (sender > 0) NOT NULL,
  recipient int check (recipient > 0) NOT NULL,
  comment text NOT NULL,
  createdDate timestamp(0) NULL,
  updatedDate timestamp(0) DEFAULT NULL,
  approved enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (commentId),
  -- Maybe we'll let the comments of the members even if they are deleted.
  -- FOREIGN KEY (sender) ph7_members(profileId),
  FOREIGN KEY (recipient) REFERENCES ph7_notes(noteId)
)  ;

ALTER SEQUENCE ph7_comments_note_seq RESTART WITH 1;


CREATE SEQUENCE ph7_comments_picture_seq;

CREATE TABLE IF NOT EXISTS ph7_comments_picture (
  commentId int check (commentId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_comments_picture_seq'),
  sender int check (sender > 0) NOT NULL,
  recipient int check (recipient > 0) NOT NULL,
  comment text NOT NULL,
  createdDate timestamp(0) NULL,
  updatedDate timestamp(0) DEFAULT NULL,
  approved enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (commentId),
  -- Maybe we'll let the comments of the members even if they are deleted.
  -- FOREIGN KEY (sender) ph7_members(profileId),
  FOREIGN KEY (recipient) REFERENCES ph7_pictures(pictureId)
)  ;

ALTER SEQUENCE ph7_comments_picture_seq RESTART WITH 1;


CREATE SEQUENCE ph7_comments_video_seq;

CREATE TABLE IF NOT EXISTS ph7_comments_video (
  commentId int check (commentId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_comments_video_seq'),
  sender int check (sender > 0) NOT NULL,
  recipient int check (recipient > 0) NOT NULL,
  comment text NOT NULL,
  createdDate timestamp(0) NULL,
  updatedDate timestamp(0) DEFAULT NULL,
  approved enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (commentId),
  -- Maybe we'll let the comments of the members even if they are deleted.
  -- FOREIGN KEY (sender) ph7_members(profileId),
  FOREIGN KEY (recipient) REFERENCES ph7_videos(videoId)
)  ;

ALTER SEQUENCE ph7_comments_video_seq RESTART WITH 1;


CREATE SEQUENCE ph7_comments_game_seq;

CREATE TABLE IF NOT EXISTS ph7_comments_game (
  commentId int check (commentId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_comments_game_seq'),
  sender int check (sender > 0) NOT NULL,
  recipient int check (recipient > 0) NOT NULL,
  comment text NOT NULL,
  createdDate timestamp(0) NULL,
  updatedDate timestamp(0) DEFAULT NULL,
  approved enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (commentId),
  -- Maybe we'll let the comments of the members even if they are deleted.
  -- FOREIGN KEY (sender) ph7_members(profileId),
  FOREIGN KEY (recipient) REFERENCES ph7_games(gameId) -- Warning: You must first download the file "pH7_Game.sql" for this table can be inserted because it uses a foreign key.
)  ;

ALTER SEQUENCE ph7_comments_game_seq RESTART WITH 1;


CREATE SEQUENCE ph7_comments_profile_seq;

CREATE TABLE IF NOT EXISTS ph7_comments_profile (
  commentId int check (commentId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_comments_profile_seq'),
  sender int check (sender > 0) NOT NULL,
  recipient int check (recipient > 0) NOT NULL,
  comment text NOT NULL,
  createdDate timestamp(0) NULL,
  updatedDate timestamp(0) DEFAULT NULL,
  approved enum('1','0') DEFAULT '1',
  PRIMARY KEY (commentId),
  -- Maybe we'll let the comments of the members even if they are deleted.
  -- FOREIGN KEY (sender) ph7_members(profileId),
  FOREIGN KEY (recipient) REFERENCES ph7_members(profileId)
)  ;

ALTER SEQUENCE ph7_comments_profile_seq RESTART WITH 1;


CREATE SEQUENCE ph7_forums_categories_seq;

CREATE TABLE IF NOT EXISTS ph7_forums_categories (
  categoryId smallint check (categoryId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_forums_categories_seq'),
  title varchar(60) DEFAULT NULL,
  PRIMARY KEY (categoryId),
  UNIQUE (title)
)  ;

ALTER SEQUENCE ph7_forums_categories_seq RESTART WITH 1;

INSERT INTO ph7_forums_categories (categoryId, title) VALUES
(1, 'General'),
(2, 'Free Online Dating Site'),
(3, 'Business');


CREATE SEQUENCE ph7_forums_seq;

CREATE TABLE IF NOT EXISTS ph7_forums (
  forumId mediumint check (forumId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_forums_seq'),
  name varchar(80) NOT NULL DEFAULT 'New forum',
  description varchar(255) NOT NULL,
  categoryId smallint check (categoryId > 0) DEFAULT NULL,
  createdDate timestamp(0) NULL,
  updatedDate timestamp(0) DEFAULT NULL,
  PRIMARY KEY (forumId),
  FOREIGN KEY (categoryId) REFERENCES ph7_forums_categories(categoryId)
)  ;

ALTER SEQUENCE ph7_forums_seq RESTART WITH 1;

INSERT INTO ph7_forums (forumId, name, description, categoryId) VALUES
(1, 'Hello', 'Free dating site', 1),
(2, 'Online Dating', 'Discussion about the online dating websites', 2),
(3, 'The Best Dating Site', 'The best dating site', 1);


CREATE SEQUENCE ph7_forums_topics_seq;

CREATE TABLE IF NOT EXISTS ph7_forums_topics (
  topicId int check (topicId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_forums_topics_seq'),
  forumId mediumint check (forumId > 0) DEFAULT NULL,
  profileId int check (profileId > 0) NOT NULL,
  title varchar(100) NOT NULL,
  message text NOT NULL,
  approved enum('1','0') DEFAULT '1',
  createdDate timestamp(0) NULL,
  updatedDate timestamp(0) DEFAULT NULL,
  views int check (views > 0) NOT NULL DEFAULT '0',
  -- Maybe we'll let the topic of member even if the member is deleted
  -- FOREIGN KEY (profileId) ph7_members(profileId),
  FOREIGN KEY (forumId) REFERENCES ph7_forums(forumId),
  PRIMARY KEY (topicId)
)  ;

ALTER SEQUENCE ph7_forums_topics_seq RESTART WITH 1;


CREATE SEQUENCE ph7_forums_messages_seq;

CREATE TABLE IF NOT EXISTS ph7_forums_messages (
  messageId int check (messageId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_forums_messages_seq'),
  topicId int check (topicId > 0) NOT NULL,
  profileId int check (profileId > 0) NOT NULL,
  message text NOT NULL,
  approved enum('1','0') DEFAULT '1',
  createdDate timestamp(0) NULL,
  updatedDate timestamp(0) DEFAULT NULL,
  -- Maybe we'll let the topic of member even if the member is deleted
  -- FOREIGN KEY (profileId) ph7_members(profileId),
  FOREIGN KEY (topicId) REFERENCES ph7_forums_topics(topicId),
  PRIMARY KEY (messageId)
)  ;

ALTER SEQUENCE ph7_forums_messages_seq RESTART WITH 1;


CREATE TABLE IF NOT EXISTS ph7_languages_info (
  langId varchar(5) NOT NULL,
  name varchar(60) NOT NULL,
  charset varchar(15) NOT NULL,
  active enum('0','1') NOT NULL DEFAULT '0',
  direction enum('ltr','rtl') NOT NULL DEFAULT 'ltr',
  author varchar(60) NOT NULL,
  website varchar(120) DEFAULT NULL,
  email varchar(120) DEFAULT NULL,
  PRIMARY KEY (langId)
) ;

INSERT INTO ph7_languages_info (langId, name, charset, active, direction, author, website, email) VALUES
('en_US', 'English', 'UTF-8', '1', 'ltr', 'Pierre-Henry Soria', 'http://ph7.me', 'hi@ph7.me');


CREATE TABLE IF NOT EXISTS ph7_likes (
  keyId varchar(255) NOT NULL,
  votes int check (votes > 0) NOT NULL,
  lastVote timestamp(0) NOT NULL,
  lastIp varchar(45) NOT NULL,
  CONSTRAINT keyId UNIQUE (keyId)
) ;


CREATE SEQUENCE ph7_log_error_seq;

CREATE TABLE IF NOT EXISTS ph7_log_error (
  logId mediumint check (logId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_log_error_seq'),
  logError text,
  PRIMARY KEY (logId),
  KEY logError (logError)
) ;


CREATE SEQUENCE ph7_admins_attempts_login_seq;

CREATE TABLE IF NOT EXISTS ph7_admins_attempts_login (
  attemptsId int check (attemptsId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_admins_attempts_login_seq'),
  ip varchar(45) NOT NULL DEFAULT '',
  attempts smallint check (attempts > 0) NOT NULL ,
  lastLogin TIMESTAMP(0) NOT NULL,
  PRIMARY KEY (attemptsId),
  UNIQUE (ip)
) ;


CREATE SEQUENCE ph7_members_attempts_login_seq;

CREATE TABLE IF NOT EXISTS ph7_members_attempts_login (
  attemptsId int check (attemptsId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_members_attempts_login_seq'),
  ip varchar(45) NOT NULL DEFAULT '',
  attempts smallint check (attempts > 0) NOT NULL ,
  lastLogin TIMESTAMP(0) NOT NULL,
  PRIMARY KEY (attemptsId),
  UNIQUE (ip)
) ;


CREATE SEQUENCE ph7_affiliates_attempts_login_seq;

CREATE TABLE IF NOT EXISTS ph7_affiliates_attempts_login (
  attemptsId int check (attemptsId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_affiliates_attempts_login_seq'),
  ip varchar(45) NOT NULL DEFAULT '',
  attempts smallint check (attempts > 0) NOT NULL ,
  lastLogin TIMESTAMP(0) NOT NULL,
  PRIMARY KEY (attemptsId),
  UNIQUE (ip)
) ;


CREATE SEQUENCE ph7_admins_log_login_seq;

CREATE TABLE IF NOT EXISTS ph7_admins_log_login (
  logId mediumint check (logId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_admins_log_login_seq'),
  email varchar(120) NOT NULL DEFAULT '',
  username varchar(64) NOT NULL DEFAULT '',
  password varchar(40) DEFAULT NULL,
  status varchar(60) NOT NULL DEFAULT '',
  ip varchar(45) NOT NULL DEFAULT '',
  dateTime timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (logId)
)  ;

ALTER SEQUENCE ph7_admins_log_login_seq RESTART WITH 1;


CREATE SEQUENCE ph7_members_log_login_seq;

CREATE TABLE IF NOT EXISTS ph7_members_log_login (
  logId mediumint check (logId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_members_log_login_seq'),
  email varchar(120) NOT NULL DEFAULT '',
  username varchar(64) NOT NULL DEFAULT '',
  password varchar(40) DEFAULT NULL,
  status varchar(60) NOT NULL DEFAULT '',
  ip varchar(45) NOT NULL DEFAULT '',
  dateTime timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (logId)
)  ;

ALTER SEQUENCE ph7_members_log_login_seq RESTART WITH 1;


CREATE SEQUENCE ph7_affiliates_log_login_seq;

CREATE TABLE IF NOT EXISTS ph7_affiliates_log_login (
  logId mediumint check (logId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_affiliates_log_login_seq'),
  email varchar(120) NOT NULL DEFAULT '',
  username varchar(64) NOT NULL DEFAULT '',
  password varchar(40) DEFAULT NULL,
  status varchar(60) NOT NULL DEFAULT '',
  ip varchar(45) NOT NULL DEFAULT '',
  dateTime timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (logId)
)  ;

ALTER SEQUENCE ph7_affiliates_log_login_seq RESTART WITH 1;


CREATE TABLE IF NOT EXISTS ph7_admins_log_sess (
  sessionId int check (sessionId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_admins_log_sess_seq'),
  profileId smallint check (profileId > 0) DEFAULT NULL,
  username varchar(40) DEFAULT NULL,
  email varchar(120) DEFAULT NULL,
  firstName varchar(50) DEFAULT NULL,
  lastName varchar(50) DEFAULT NULL,
  ip varchar(45) NOT NULL DEFAULT '127.0.0.1',
  dateTime timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (sessionId),
  FOREIGN KEY (profileId) REFERENCES ph7_admins(profileId)
) ;


CREATE TABLE IF NOT EXISTS ph7_members_log_sess (
  sessionId int check (sessionId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_members_log_sess'),
  profileId int check (profileId > 0) DEFAULT NULL,
  username varchar(40) DEFAULT NULL,
  email varchar(120) DEFAULT NULL,
  firstName varchar(50) DEFAULT NULL,
  lastName varchar(50) DEFAULT NULL,
  ip varchar(45) NOT NULL DEFAULT '127.0.0.1',
  dateTime timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (sessionId),
  FOREIGN KEY (profileId) REFERENCES ph7_members(profileId)
) ;


CREATE TABLE IF NOT EXISTS ph7_affiliates_log_sess (
  sessionId int check (sessionId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_affiliates_log_sess'),
  profileId int check (profileId > 0) DEFAULT NULL,
  username varchar(40) DEFAULT NULL,
  email varchar(120) DEFAULT NULL,
  firstName varchar(50) DEFAULT NULL,
  lastName varchar(50) DEFAULT NULL,
  ip varchar(45) NOT NULL DEFAULT '127.0.0.1',
  dateTime timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (sessionId),
  FOREIGN KEY (profileId) REFERENCES ph7_affiliates(profileId)
) ;


CREATE TABLE IF NOT EXISTS ph7_members_background (
  profileId int check (profileId > 0) NOT NULL,
  file varchar(5) NOT NULL,
  approved smallint check (approved > 0) NOT NULL DEFAULT '1',
  PRIMARY KEY profileId (profileId),
  FOREIGN KEY (profileId) REFERENCES ph7_members(profileId)
) ;


CREATE TABLE IF NOT EXISTS ph7_members_who_views (
  profileId int check (profileId > 0) NOT NULL,
  visitorId int check (visitorId > 0) NOT NULL,
  lastVisit timestamp(0) NULL
 ,
  FOREIGN KEY (profileId) REFERENCES ph7_members(profileId),
  FOREIGN KEY (visitorId) REFERENCES ph7_members(profileId)
) ;

CREATE INDEX profileId ON ph7_members_who_views (profileId);
CREATE INDEX visitorId ON ph7_members_who_views (visitorId);


CREATE TABLE IF NOT EXISTS ph7_members_friends (
  profileId int check (profileId > 0) NOT NULL,
  friendId int check (friendId > 0) NOT NULL,
  requestDate timestamp(0) DEFAULT NULL,
  pending smallint check (pending > 0) NOT NULL DEFAULT '0'
 ,
  FOREIGN KEY (profileId) REFERENCES ph7_members(profileId),
  FOREIGN KEY (friendId) REFERENCES ph7_members(profileId)
) ;

CREATE INDEX profileId ON ph7_members_friends (profileId);
CREATE INDEX friendId ON ph7_members_friends (friendId);


CREATE SEQUENCE ph7_members_wall_seq;

CREATE TABLE IF NOT EXISTS ph7_members_wall (
  wallId int check (wallId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_members_wall_seq'),
  profileId int check (profileId > 0) NOT NULL DEFAULT '0',
  post text CHARACTER SET armscii8,
  createdDate timestamp(0) NULL,
  updatedDate timestamp(0) DEFAULT NULL,
  PRIMARY KEY (wallId),
  FOREIGN KEY (profileId) REFERENCES ph7_members(profileId)
)  ;

ALTER SEQUENCE ph7_members_wall_seq RESTART WITH 1;


CREATE SEQUENCE ph7_messages_seq;

CREATE TABLE IF NOT EXISTS ph7_messages (
  messageId int check (messageId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_messages_seq'),
  sender int check (sender > 0) NOT NULL DEFAULT '0',
  recipient int check (recipient > 0) NOT NULL DEFAULT '0',
  title varchar(30) NOT NULL DEFAULT '',
  message text NOT NULL,
  sendDate timestamp(0) NULL,
  status smallint check (status > 0) NOT NULL DEFAULT '1', -- 1 = Unread | 0 = Read
  trash set('sender','recipient') NOT NULL DEFAULT '',
  toDelete set('sender','recipient') NOT NULL DEFAULT '',
  PRIMARY KEY (messageId),
  -- This is wrong, because now administrators can also send emails.
  -- FOREIGN KEY (sender) REFERENCES ph7_members(profileId),
  FOREIGN KEY (recipient) REFERENCES ph7_members(profileId)
)  ;

ALTER SEQUENCE ph7_messages_seq RESTART WITH 1;


CREATE SEQUENCE ph7_messenger_seq;

CREATE TABLE IF NOT EXISTS ph7_messenger (
  messengerId int check (messengerId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_messenger_seq'),
  fromUser varchar(40) NOT NULL DEFAULT '',
  toUser varchar(40) NOT NULL DEFAULT '',
  message text NOT NULL,
  sent timestamp(0) NULL,
  recd int check (recd > 0) NOT NULL DEFAULT '0',
  PRIMARY KEY (messengerId),
  FOREIGN KEY (fromUser) REFERENCES ph7_members(username),
  FOREIGN KEY (toUser) REFERENCES ph7_members(username)
)  ;

ALTER SEQUENCE ph7_messenger_seq RESTART WITH 1;


CREATE TABLE IF NOT EXISTS ph7_meta_main (
  langId varchar(5) NOT NULL DEFAULT '',
  pageTitle varchar(100) NOT NULL,
  metaDescription varchar(255) NOT NULL,
  metaKeywords varchar(255) NOT NULL,
  headline varchar(50) NOT NULL,
  slogan varchar(200) NOT NULL,
  promoText text DEFAULT NULL,
  metaRobots varchar(50) NOT NULL DEFAULT '',
  metaAuthor varchar(50) NOT NULL DEFAULT '',
  metaCopyright varchar(55) NOT NULL DEFAULT '',
  metaRating varchar(50) NOT NULL DEFAULT '',
  metaDistribution varchar(50) NOT NULL DEFAULT '',
  metaCategory varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (langId)
) ;

INSERT INTO ph7_meta_main (langId, pageTitle, metaDescription, metaKeywords, headline, slogan, promoText, metaRobots, metaAuthor, metaCopyright, metaRating, metaDistribution, metaCategory) VALUES
('en_US', 'Home', 'The Best Online Social Dating Service to meet people and keep in touch with your friends', 'meet people, community, single, friends, meet singles, women, men, dating site, dating service, dating website, online dating website', 'Be on the right place!', 'The Place to meet lovely people', 'You''re on the best place for meeting new people nearby! Chat, Flirt, Socialize and have Fun!<br />Create any Social Dating Web Apps or Websites like this one with the #1 <a href="https://ph7cms.com">Dating Web App Builder</a>. It''s Professional, Modern, Open Source, and gives you the Best Way to launch a new Social/Dating Business!', 'index, follow, all', 'Pierre-Henry Soria', 'Copyright Pierre-Henry Soria. All Rights Reserved.', 'general', 'global', 'dating');


CREATE SEQUENCE ph7_sys_mods_enabled_seq;

CREATE TABLE IF NOT EXISTS ph7_sys_mods_enabled (
  moduleId smallint check (moduleId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_sys_mods_enabled_seq'),
  moduleTitle varchar(50) NOT NULL,
  folderName varchar(20) NOT NULL,
  premiumMod enum('0','1') NOT NULL DEFAULT '0',
  enabled enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (moduleId)
)  ;

ALTER SEQUENCE ph7_sys_mods_enabled_seq RESTART WITH 1;

INSERT INTO ph7_sys_mods_enabled (moduleTitle, folderName, premiumMod, enabled) VALUES
('Affiliate', 'affiliate', '0', '1'),
('Chat', 'chat', '1', '1'),
('Chatroulette', 'chatroulette', '1', '1'),
('Photo', 'picture', '0', '1'),
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
('Dating-Style Profile Page', 'cool-profile-page', '0', '1'),
('Birthday: Let''s Celebrate Birthdays', 'birthday', '0', '1'),
('Google Maps', 'map', '0', '1'),
('Game', 'game', '0', '0'),
('Newsletter', 'newsletter', '0', '1'),
('Invite Friends', 'invite', '0', '1'),
('SMS Verification', 'sms-verification', '0', '0'),
('Social Media Auth (connect)', 'connect', '0', '0'),
('Webcam', 'webcam', '0', '0'),
('Progressive Web App (HTTPS required)', 'pwa', '0', '0');


CREATE SEQUENCE ph7_modules_seq;

CREATE TABLE IF NOT EXISTS ph7_modules (
  moduleId smallint check (moduleId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_modules_seq'),
  vendorName varchar(40) NOT NULL,
  moduleName varchar(40) NOT NULL,
  version varchar(6) NOT NULL,
  uri varchar(40) DEFAULT NULL,
  path varchar(255) DEFAULT NULL,
  active enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (moduleId)
)  ;

ALTER SEQUENCE ph7_modules_seq RESTART WITH 1;

INSERT INTO ph7_modules (vendorName, moduleName, version, active) VALUES
/* Gives the current version of pH7CMS SQL schema (this helps to update and shows whether it is necessary or not to update the database as well) */
('pH7CMS', 'SQL System Schema', '1.5.9', 1);


CREATE SEQUENCE ph7_report_seq;

CREATE TABLE IF NOT EXISTS ph7_report (
  reportId smallint check (reportId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_report_seq'),
  reporterId int check (reporterId > 0) DEFAULT NULL,
  spammerId int check (spammerId > 0) DEFAULT NULL,
  dateTime timestamp(0) DEFAULT NULL,
  contentType enum('user','avatar','mail','comment','photo','video','forum','note') NOT NULL DEFAULT 'user',
  description varchar(255) DEFAULT NULL,
  url varchar(255) DEFAULT NULL,
  PRIMARY KEY (reportId),
  FOREIGN KEY (reporterId) REFERENCES ph7_members(profileId),
  FOREIGN KEY (spammerId) REFERENCES ph7_members(profileId)
)  ;

ALTER SEQUENCE ph7_report_seq RESTART WITH 1;


CREATE TABLE IF NOT EXISTS ph7_settings (
  settingName varchar(64) NOT NULL,
  settingValue varchar(150) DEFAULT '',
  description varchar(120) DEFAULT '' ,
  settingGroup varchar(12) NOT NULL,
  PRIMARY KEY (settingName)
) ;

INSERT INTO ph7_settings (settingName, settingValue, description, settingGroup) VALUES
('siteName', @sDefaultSiteName, '', 'general'),
('adminEmail', @sAdminEmail, '', 'email'),
('defaultLanguage', 'en_US', '', 'language'),
('defaultTemplate', 'base', '', 'design'),
('navbarType', 'default', 'Choose between "default" or "dark"', 'design'),
('backgroundColor', '', 'Override background color. Leave empty to disable', 'design'),
('textColor', '', 'Override text color. Leave empty to disable', 'design'),
('heading1Color', '', 'Override H1 color. Leave empty to disable', 'design'),
('heading2Color', '', 'Override H2 color. Leave empty to disable', 'design'),
('heading3Color', '', 'Override H3 color. Leave empty to disable', 'design'),
('linkColor', '', 'Override links color. Leave empty to disable', 'design'),
('footerLinkColor', '', 'Override footer links color. Leave empty to disable', 'design'),
('linkHoverColor', '', 'Override links hover color. Leave empty to disable', 'design'),
('defaultSysModule', 'user', 'The default module running by default on the index page. Recommended to keep the "user" module', 'general'),
('emailName', 'pH7CMS', '', 'email'),
('feedbackEmail', @sFeedbackEmail, '', 'email'),
('splashPage', 1, 'Use Splash Page | enable = 1 or disable = 0', 'homepage'),
('usersBlock', 1, '0 to disable | 1 to enable the profile photos on the homepage', 'homepage'),
('profileWithAvatarSet', 0, '1 to display only the profiles with a profile photo.', 'homepage'),
('bgSplashVideo', 1, '0 to disable or 1 to enable the background splash video', 'homepage'),
('numberProfileSplashPage', 44, 'Number of profiles to display on the splash homepage', 'homepage'),
('ipLogin', '', '', 'security'),
('timeDelayUserRegistration', 1440, '1440 minutes = 24 hours (in minutes!)', 'spam'),
('timeDelayAffRegistration', 2880, '2880 minutes = 2 days (in minutes!)', 'spam'),
('timeDelaySendNote', 20, 'Waiting time to add a new note post, in minutes!', 'spam'),
('timeDelaySendMail', 3, 'Waiting time to send a new message, in minutes!', 'spam'),
('timeDelaySendComment', 5, 'Waiting time to send a new comment, in minutes!', 'spam'),
('timeDelaySendForumTopic', 5, 'Waiting time to send a new topic in the forum, in minutes!', 'spam'),
('timeDelaySendForumMsg', 10, 'Waiting time to send a reply message in the same topic, in minutes!', 'spam'),
('captchaComplexity', 5, 'number of captcha complexity', 'spam'),
('captchaCaseSensitive', 1, '1 to enable captcha case sensitive | 0 to enable', 'spam'),
('isCaptchaUserSignup', 0, '0 to disable or 1 to enable', 'spam'),
('isCaptchaAffiliateSignup', 0, '0 to disable or 1 to enable', 'spam'),
('isCaptchaMail', 0, '0 to disable or 1 to enable', 'spam'),
('isCaptchaComment', 0, '0 to disable or 1 to enable', 'spam'),
('isCaptchaForum', 0, '0 to disable or 1 to enable', 'spam'),
('isCaptchaNote', 0, '0 to disable or 1 to enable', 'spam'),
('mailType', 'mail', '', 'email'),
('mapType', 'roadmap', 'Choose between: ''roadmap'', ''hybrid'', ''terrain'', ''satellite''', 'map'),
('isUserAgeRangeField', 1, '0 to disable or 1 to enable', 'registration'),
('maxAgeRegistration', 99, '', 'registration'),
('minAgeRegistration', 18, '', 'registration'),
('minUsernameLength', 3, '', 'registration'),
('maxUsernameLength', 30, '', 'registration'),
('requireRegistrationAvatar', 0, '', 'registration'),
('userActivationType', 1, '1 = no activation, 2 = email activation, 3 = manual activation by admin, 4 = SMS activation', 'registration'),
('affActivationType', 1, '1 = no activation, 2 = email activation, 3 = Manual activation by the administrator', 'registration'),
('defaultMembershipGroupId', 2, 'Default Membership Group', 'registration'),
('minPasswordLength', 6, '', 'security'),
('maxPasswordLength', 60, '', 'security'),
('isUserLoginAttempt', 1, 'Enable blocking connection attempts abusive. Enable = 1 or disable = 0', 'security'),
('isAdminLoginAttempt', 1, 'Enable blocking connection attempts abusive. Enable = 1 or disable = 0', 'security'),
('isAffiliateLoginAttempt', 1, 'Enable blocking connection attempts abusive. Enable = 1 or disable = 0', 'security'),
('maxUserLoginAttempts', 20, 'Maximum login attempts before blocking', 'security'),
('maxAffiliateLoginAttempts', 15, 'Maximum login attempts before blocking', 'security'),
('maxAdminLoginAttempts', 10, 'Maximum login attempts before blocking', 'security'),
('loginUserAttemptTime', 60, 'Time before a new connection attempt, in minutes!', 'security'),
('loginAffiliateAttemptTime', 60, 'Time before a new connection attempt, in minutes!', 'security'),
('loginAdminAttemptTime', 120, 'Time before a new connection attempt, in minutes!', 'security'),
('isUserSessionIpCheck', 0, 'Enable it to Protect against session hijacking. Disable it if use dynamic IPs', 'security'),
('isAffiliateSessionIpCheck', 1, 'Enable it to Protect against session hijacking. Disable it if use dynamic IPs', 'security'),
('isAdminSessionIpCheck', 1, 'Enable it to Protect against session hijacking. Disable it if use dynamic IPs', 'security'),
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
('DDoS', 0,  '0 to disabled or 1 to enabled DDoS attack protection',  'security'),
('isSiteValidated', 0,  '0 = site not validated | 1 = site validated',  'security'),
('cleanMsg', 0, 'Delete messages older than X days. 0 = Disable', 'pruning'),
('cleanComment', 0, 'Delete comments older than X days. 0 = Disable', 'pruning'),
('cleanMessenger', 0, 'Delete IM messages older than X days. 0 = Disable', 'pruning'),
('ipApi', @sIpApiUrl, 'IP Api URL', 'api'),
('chatApi', @sChatApiUrl, 'Chat Api URL', 'api'),
('chatrouletteApi', @sChatrouletteApiUrl, 'Chatroulette Api URL', 'api'),
('googleApiKey', '', 'Google Maps API key https://developers.google.com/maps/documentation/javascript/get-api-key', 'api'),
('cronSecurityHash', 'change_this_secret_cron_word_by_yours', 'The secret word for the URL of the cron', 'automation'),
('userTimeout', 1, 'User inactivity timeout. The number of minutes that a member becomes inactive (offline)', 'automation'),
('socialMediaWidgets', 0, 'Enable the Social Media Widgets such as Like and Sharing buttons. 0 = Disable | 1 = Enable', 'general'),
('wysiwygEditorForum', 0, 'Enable or not the WYSIWYG. 0 = Disable | 1 = Enable', 'general'),
('disclaimer', 0, 'Enable a disclaimer to enter to the site. This is useful for sites with adult content. 0 = Disable | 1 = Enable', 'general'),
('cookieConsentBar', 0, 'Enable the cookie consent bar to prevent your users that your site uses cookies. 0 = Disable | 1 = Enable', 'general'),
('displayPoweredByLink', 1, 'Show or not the branding link in the footer.', 'general'),
('isSoftwareNewsFeed', 1, 'Enable the news feed. 0 = Disable | 1 = Enable', 'general');


CREATE SEQUENCE ph7_subscribers_seq;

CREATE TABLE IF NOT EXISTS ph7_subscribers (
  profileId int check (profileId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_subscribers_seq'),
  name varchar(200) NOT NULL,
  email varchar(120) NOT NULL,
  joinDate timestamp(0) DEFAULT NULL,
  active smallint check (active > 0) NOT NULL DEFAULT 2, -- 1 = Active Account, 2 = Pending Account
  ip varchar(45) NOT NULL DEFAULT '127.0.0.1',
  hashValidation varchar(40) DEFAULT NULL,
  affiliatedId int check (affiliatedId > 0) NOT NULL DEFAULT 0,
  PRIMARY KEY (profileId),
  UNIQUE (email)
)  ;

ALTER SEQUENCE ph7_subscribers_seq RESTART WITH 1;


CREATE SEQUENCE ph7_top_menus_seq;

CREATE TABLE IF NOT EXISTS ph7_top_menus (
  menuId smallint check (menuId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_top_menus_seq'),
  vendorName varchar(40) NOT NULL,
  moduleName varchar(40) NOT NULL,
  controllerName varchar(40) NOT NULL,
  actionName varchar(40) NOT NULL,
  vars varchar(60) DEFAULT NULL,
  parentMenu smallint check (parentMenu > 0) DEFAULT NULL,
  grandParentMenu smallint check (grandParentMenu > 0) DEFAULT NULL,
  onlyForUsers enum('0','1') NOT NULL DEFAULT '0',
  active enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (menuId)
)  ;

ALTER SEQUENCE ph7_top_menus_seq RESTART WITH 1;


CREATE SEQUENCE ph7_bottom_menus_seq;

CREATE TABLE IF NOT EXISTS ph7_bottom_menus (
  menuId smallint check (menuId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_bottom_menus_seq'),
  vendorName varchar(40) NOT NULL,
  moduleName varchar(40) NOT NULL,
  controllerName varchar(40) NOT NULL,
  actionName varchar(40) NOT NULL,
  vars varchar(60) DEFAULT NULL,
  parentMenu smallint check (parentMenu > 0) DEFAULT NULL,
  grandParentMenu smallint check (grandParentMenu > 0) DEFAULT NULL,
  active enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (menuId)
)  ;

ALTER SEQUENCE ph7_bottom_menus_seq RESTART WITH 1;


CREATE SEQUENCE ph7_static_files_seq;

CREATE TABLE IF NOT EXISTS ph7_static_files (
  staticId smallint check (staticId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_static_files_seq'),
  file varchar(255) NOT NULL,
  fileType enum('css', 'js') NOT NULL,
  active enum('1','0') DEFAULT '1',
  PRIMARY KEY (staticId)
)  ;

ALTER SEQUENCE ph7_static_files_seq RESTART WITH 1;

INSERT INTO ph7_static_files VALUES (1, '//static.addtoany.com/menu/page.js', 'js', '0');


CREATE SEQUENCE ph7_license_seq;

CREATE TABLE IF NOT EXISTS ph7_license (
  licenseId smallint check (licenseId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_license_seq'),
  licenseKey varchar(40) NOT NULL,
  PRIMARY KEY (licenseId)
)  ;

ALTER SEQUENCE ph7_license_seq RESTART WITH 1;

INSERT INTO ph7_license VALUES (1, '');


CREATE TABLE IF NOT EXISTS ph7_custom_code (
  code text,
  codeType enum('css', 'js') NOT NULL
)  ;

ALTER SEQUENCE ph7_custom_code_seq RESTART WITH 1;

INSERT INTO ph7_custom_code VALUES
('/* Your custom CSS code here */rn', 'css'),
('/* Your custom JS code here */rn', 'js');


CREATE SEQUENCE ph7_block_countries_seq;

CREATE TABLE IF NOT EXISTS ph7_block_countries (
  countryId smallint check (countryId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_block_countries_seq'),
  countryCode char(2) NOT NULL,
  PRIMARY KEY (countryId),
  UNIQUE (countryCode)
);

ALTER SEQUENCE ph7_block_countries_seq RESTART WITH 1;


CREATE SEQUENCE ph7_members_countries_seq;

CREATE TABLE IF NOT EXISTS ph7_members_countries (
  countryId smallint check (countryId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_members_countries_seq'),
  countryCode char(2) NOT NULL,
  PRIMARY KEY (countryId),
  UNIQUE (countryCode)
);

ALTER SEQUENCE ph7_members_countries_seq RESTART WITH 1;

INSERT INTO ph7_members_countries (countryCode) VALUES
('AD'),
('AE'),
('AF'),
('AG'),
('AI'),
('AL'),
('AM'),
('AN'),
('AO'),
('AQ'),
('AR'),
('AS'),
('AT'),
('AU'),
('AW'),
('AX'),
('AZ'),
('BA'),
('BB'),
('BD'),
('BE'),
('BF'),
('BG'),
('BH'),
('BI'),
('BJ'),
('BM'),
('BN'),
('BO'),
('BR'),
('BS'),
('BT'),
('BV'),
('BW'),
('BY'),
('BZ'),
('CA'),
('CC'),
('CD'),
('CF'),
('CG'),
('CH'),
('CI'),
('CK'),
('CL'),
('CM'),
('CN'),
('CO'),
('CR'),
('CU'),
('CV'),
('CX'),
('CY'),
('CZ'),
('DE'),
('DJ'),
('DK'),
('DM'),
('DO'),
('DZ'),
('EC'),
('EE'),
('EG'),
('EH'),
('ER'),
('ES'),
('ET'),
('FI'),
('FJ'),
('FK'),
('FM'),
('FO'),
('FR'),
('FX'),
('GA'),
('GD'),
('GE'),
('GF'),
('GH'),
('GI'),
('GL'),
('GM'),
('GN'),
('GP'),
('GQ'),
('GR'),
('GS'),
('GT'),
('GU'),
('GW'),
('GY'),
('HK'),
('HM'),
('HN'),
('HR'),
('HT'),
('HU'),
('ID'),
('IE'),
('IL'),
('IN'),
('IO'),
('IQ'),
('IR'),
('IS'),
('IT'),
('JM'),
('JO'),
('JP'),
('KE'),
('KG'),
('KH'),
('KI'),
('KM'),
('KN'),
('KP'),
('KR'),
('KW'),
('KY'),
('KZ'),
('LA'),
('LB'),
('LC'),
('LI'),
('LK'),
('LR'),
('LS'),
('LT'),
('LU'),
('LV'),
('LY'),
('MA'),
('MC'),
('MD'),
('MG'),
('MH'),
('MK'),
('ML'),
('MM'),
('MN'),
('MO'),
('MP'),
('MQ'),
('MR'),
('MS'),
('MT'),
('MU'),
('MV'),
('MW'),
('MX'),
('MY'),
('MZ'),
('NA'),
('NC'),
('NE'),
('NF'),
('NG'),
('NI'),
('NL'),
('NO'),
('NP'),
('NR'),
('NU'),
('NZ'),
('OM'),
('PA'),
('PE'),
('PF'),
('PG'),
('PH'),
('PK'),
('PL'),
('PM'),
('PN'),
('PR'),
('PT'),
('PW'),
('PY'),
('QA'),
('RE'),
('RO'),
('RU'),
('RW'),
('SA'),
('SB'),
('SC'),
('SD'),
('SE'),
('SG'),
('SH'),
('SI'),
('SJ'),
('SK'),
('SL'),
('SM'),
('SN'),
('SO'),
('SR'),
('ST'),
('SV'),
('SY'),
('SZ'),
('TC'),
('TD'),
('TF'),
('TG'),
('TH'),
('TJ'),
('TK'),
('TM'),
('TN'),
('TO'),
('TP'),
('TR'),
('TT'),
('TV'),
('TW'),
('TZ'),
('UA'),
('UG'),
('UK'),
('UM'),
('US'),
('UY'),
('UZ'),
('VA'),
('VC'),
('VE'),
('VG'),
('VI'),
('VN'),
('VU'),
('WF'),
('WS'),
('YE'),
('YT'),
('YU'),
('ZA'),
('ZM'),
('ZW');


CREATE SEQUENCE ph7_affiliates_countries_seq;

CREATE TABLE IF NOT EXISTS ph7_affiliates_countries (
  countryId smallint check (countryId > 0) NOT NULL DEFAULT NEXTVAL ('ph7_affiliates_countries_seq'),
  countryCode char(2) NOT NULL,
  PRIMARY KEY (countryId),
  UNIQUE (countryCode)
);

ALTER SEQUENCE ph7_affiliates_countries_seq RESTART WITH 1;

INSERT INTO ph7_affiliates_countries (countryCode) VALUES
('AD'),
('AE'),
('AF'),
('AG'),
('AI'),
('AL'),
('AM'),
('AN'),
('AO'),
('AQ'),
('AR'),
('AS'),
('AT'),
('AU'),
('AW'),
('AX'),
('AZ'),
('BA'),
('BB'),
('BD'),
('BE'),
('BF'),
('BG'),
('BH'),
('BI'),
('BJ'),
('BM'),
('BN'),
('BO'),
('BR'),
('BS'),
('BT'),
('BV'),
('BW'),
('BY'),
('BZ'),
('CA'),
('CC'),
('CD'),
('CF'),
('CG'),
('CH'),
('CI'),
('CK'),
('CL'),
('CM'),
('CN'),
('CO'),
('CR'),
('CU'),
('CV'),
('CX'),
('CY'),
('CZ'),
('DE'),
('DJ'),
('DK'),
('DM'),
('DO'),
('DZ'),
('EC'),
('EE'),
('EG'),
('EH'),
('ER'),
('ES'),
('ET'),
('FI'),
('FJ'),
('FK'),
('FM'),
('FO'),
('FR'),
('FX'),
('GA'),
('GD'),
('GE'),
('GF'),
('GH'),
('GI'),
('GL'),
('GM'),
('GN'),
('GP'),
('GQ'),
('GR'),
('GS'),
('GT'),
('GU'),
('GW'),
('GY'),
('HK'),
('HM'),
('HN'),
('HR'),
('HT'),
('HU'),
('ID'),
('IE'),
('IL'),
('IN'),
('IO'),
('IQ'),
('IR'),
('IS'),
('IT'),
('JM'),
('JO'),
('JP'),
('KE'),
('KG'),
('KH'),
('KI'),
('KM'),
('KN'),
('KP'),
('KR'),
('KW'),
('KY'),
('KZ'),
('LA'),
('LB'),
('LC'),
('LI'),
('LK'),
('LR'),
('LS'),
('LT'),
('LU'),
('LV'),
('LY'),
('MA'),
('MC'),
('MD'),
('MG'),
('MH'),
('MK'),
('ML'),
('MM'),
('MN'),
('MO'),
('MP'),
('MQ'),
('MR'),
('MS'),
('MT'),
('MU'),
('MV'),
('MW'),
('MX'),
('MY'),
('MZ'),
('NA'),
('NC'),
('NE'),
('NF'),
('NG'),
('NI'),
('NL'),
('NO'),
('NP'),
('NR'),
('NU'),
('NZ'),
('OM'),
('PA'),
('PE'),
('PF'),
('PG'),
('PH'),
('PK'),
('PL'),
('PM'),
('PN'),
('PR'),
('PT'),
('PW'),
('PY'),
('QA'),
('RE'),
('RO'),
('RU'),
('RW'),
('SA'),
('SB'),
('SC'),
('SD'),
('SE'),
('SG'),
('SH'),
('SI'),
('SJ'),
('SK'),
('SL'),
('SM'),
('SN'),
('SO'),
('SR'),
('ST'),
('SV'),
('SY'),
('SZ'),
('TC'),
('TD'),
('TF'),
('TG'),
('TH'),
('TJ'),
('TK'),
('TM'),
('TN'),
('TO'),
('TP'),
('TR'),
('TT'),
('TV'),
('TW'),
('TZ'),
('UA'),
('UG'),
('UK'),
('UM'),
('US'),
('UY'),
('UZ'),
('VA'),
('VC'),
('VE'),
('VG'),
('VI'),
('VN'),
('VU'),
('WF'),
('WS'),
('YE'),
('YT'),
('YU'),
('ZA'),
('ZM'),
('ZW');
