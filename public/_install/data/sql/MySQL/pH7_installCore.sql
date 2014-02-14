--
--
-- Title:         SQL Core (base) Install File
--
-- Author:        Pierre-Henry Soria <ph7software@gmail.com>
-- Copyright:     (c) 2012-2014, Pierre-Henry Soria. All Rights Reserved.
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
SET @sDefaultVideoUrl = 'http://www.youtube.com/watch?v=pHWeb';
SET @sChatApiUrl = 'http://addons.hizup.com/chat/?name=%site_name%&url=%site_url%&skin=4';
SET @sChatrouletteApiUrl = 'http://addons.hizup.com/chatroulette/?name=%site_name%&url=%site_url%&skin=1';

SET @sCurrentDate = CURRENT_TIMESTAMP;
SET @sPassword = SHA1(RAND() + UNIX_TIMESTAMP());


CREATE TABLE IF NOT EXISTS pH7_Admins (
  profileId tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  username varchar(40) NOT NULL,
  password char(120) NOT NULL,
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
  ip varchar(20) NOT NULL DEFAULT '127.0.0.1',
  hashValidation varchar(40) DEFAULT NULL,
  PRIMARY KEY (profileId),
  UNIQUE KEY username (username),
  UNIQUE KEY email (email)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_Memberships (
  groupId tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(64) NOT NULL DEFAULT '',
  description varchar(255) NOT NULL,
  permissions text NOT NULL,
  price tinyint(4) unsigned NOT NULL,
  expirationDays tinyint(2) unsigned NOT NULL,
  enable enum('1','0') DEFAULT '1',
  orderId tinyint(2) unsigned NOT NULL,
  PRIMARY KEY (groupId)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO pH7_Memberships (groupId, name, description, permissions, price, expirationDays, enable, orderId) VALUES
(1, 'Visitor', 'This subscription is offered to all visitors who visit our site.', 'a:2:{s:21:"quick_search_profiles";i:0;s:24:"advanced_search_profiles";i:0;}', 0, 0, 1, 0),
(9, 'Pending', 'Pending subscription provisional migration to a different subscription.', 'a:2:{s:21:"quick_search_profiles";i:0;s:24:"advanced_search_profiles";i:0;}', 0, 15, 0, 0),
(2, 'Regular (Free)', 'Free Membership.', 'a:2:{s:21:"quick_search_profiles";i:1;s:24:"advanced_search_profiles";i:1;}', 0, 0, 1, 0),
(4, 'Platinum', 'The membership for the small budget.', 'a:2:{s:21:"quick_search_profiles";i:1;s:24:"advanced_search_profiles";i:1;}', 9.99, 5, 1, 0),
(5, 'Silver', 'The premium membership!', 'a:2:{s:21:"quick_search_profiles";i:1;s:24:"advanced_search_profiles";i:1;}', 19.99, 10, 1, 0),
(6, 'Gold', 'The must membership! The Gold!!!', 'a:2:{s:21:"quick_search_profiles";i:1;s:24:"advanced_search_profiles";i:1;}', 29.99, 30, 1, 0);


CREATE TABLE IF NOT EXISTS pH7_Members (
  profileId int(10) unsigned NOT NULL AUTO_INCREMENT,
  email varchar(120) NOT NULL,
  username varchar(40) NOT NULL,
  password char(120) NOT NULL,
  firstName varchar(50) DEFAULT NULL,
  lastName varchar(50) DEFAULT NULL,
  birthDate date NOT NULL DEFAULT '0000-00-00',
  sex enum('male','female','couple') NOT NULL DEFAULT 'female',
  matchSex set('male','female','couple') NOT NULL DEFAULT 'male',
  ip varchar(20) NOT NULL DEFAULT '127.0.0.1',
  bankAccount varchar(150) DEFAULT NULL,
  groupId tinyint(2) unsigned NOT NULL DEFAULT 2,
  membershipExpiration timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  userStatus tinyint(1) unsigned NOT NULL DEFAULT 1, -- 0 = Offline, 1 = Online, 2 = Busy, 3 = Away
  joinDate datetime DEFAULT NULL,
  lastActivity datetime DEFAULT NULL,
  lastEdit datetime DEFAULT NULL,
  avatar char(5) DEFAULT NULL,
  approvedAvatar tinyint(1) unsigned NOT NULL DEFAULT 1,
  featured tinyint(1) unsigned NOT NULL DEFAULT 0,
  lang varchar(5) NOT NULL DEFAULT 'en_US',
  hashValidation varchar(40) DEFAULT NULL,
  views int(11) NOT NULL DEFAULT 0,
  reference varchar(255) DEFAULT NULL,
  votes int(11) DEFAULT 0,
  score float DEFAULT 0,
  credits int(6) unsigned NOT NULL DEFAULT 0,
  active tinyint(1) unsigned NOT NULL DEFAULT 1,
  ban tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (profileId),
  FOREIGN KEY (groupId) REFERENCES pH7_Memberships(groupId),
  UNIQUE KEY (username),
  UNIQUE KEY (email),
  KEY birthDate (birthDate)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


-- Begin 1.0 Version
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


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
INSERT INTO pH7_Members (profileId, email, username, password, firstName, lastName, birthDate, sex, matchSex, ip, lastActivity, featured, active, userStatus, groupId, joinDate) VALUES
(1, 'ghost@ghost', 'ghost', @sPassword, 'Ghost', 'The Ghost', '1001-01-01', '', '', '127.0.0.1', @sCurrentDate, 0, 1, 1, 2, @sCurrentDate);
INSERT INTO pH7_MembersInfo (profileId, description, address, street, city, state, zipCode, country) VALUES
(1, 'This profile no longer exists, so I''m a ghost who replaces him during this time', 'The Ghost City', 'Ghost street', 'Ghost town', 'Ghost state', '000000', 'US');
-- Privacy settings
INSERT INTO pH7_MembersPrivacy (profileId, privacyProfile, searchProfile, userSaveViews) VALUES (1, 'all', 'yes', 'yes');
-- Notifications
INSERT INTO pH7_MembersNotifications (profileId, enableNewsletters, newMsg, friendRequest) VALUES (1, 0, 0, 0);


CREATE TABLE IF NOT EXISTS pH7_Affiliates (
  profileId int(10) unsigned NOT NULL AUTO_INCREMENT,
  username varchar(40) NOT NULL,
  firstName varchar(50) NOT NULL,
  lastName varchar(50) NOT NULL,
  password char(120) NOT NULL,
  email varchar(120) NOT NULL,
  sex enum('male','female') NOT NULL DEFAULT 'male',
  birthDate date NOT NULL DEFAULT '0000-00-00',
  ip varchar(20) NOT NULL DEFAULT '127.0.0.1',
  bankAccount varchar(150) DEFAULT NULL,
  credits int(6) unsigned NOT NULL DEFAULT 0,
  summary decimal(8,2) NOT NULL DEFAULT '0.00',
  payment decimal(8,2) NOT NULL DEFAULT '0.00',
  paymentLast decimal(8,2) NOT NULL DEFAULT '0.00',
  paymentLastDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  lang varchar(5) NOT NULL DEFAULT 'en_US',
  hashValidation varchar(40) DEFAULT NULL,
  refer int(10) unsigned DEFAULT 0,
  joinDate datetime DEFAULT NULL,
  lastActivity datetime DEFAULT NULL,
  lastEdit datetime DEFAULT NULL,
  active tinyint(1) unsigned NOT NULL DEFAULT 1,
  ban tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (profileId),
  UNIQUE KEY bankAccount (bankAccount), -- For the Security Bank Account --
  UNIQUE KEY username (username),
  UNIQUE KEY email (email),
  KEY birthDate (birthDate)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


-- Begin 1.0 Version
CREATE TABLE IF NOT EXISTS pH7_AffiliatesInfo (
  profileId int(10) unsigned NOT NULL AUTO_INCREMENT,
  middleName varchar(50) DEFAULT NULL,
  businessName varchar(100) DEFAULT NULL,
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
  ip int(10) unsigned NOT NULL DEFAULT '0',
  expires int(10) unsigned NOT NULL,
  PRIMARY KEY (ip)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS pH7_Ads (
  adsId smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(40) DEFAULT NULL,
  code text,
  active enum('1','0') DEFAULT '1',
  width smallint(4) DEFAULT NULL,
  height smallint(4) DEFAULT NULL,
  views int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (adsId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO pH7_Ads (adsId, name, code, active, width, height, views) VALUES
(1, 'Sponsor pH7 Dating CMS 1 (728x90)', '<a href="%software_website%"><img src="%software_website%/static/img/logo1-728x90.gif" alt="%software_name%" title="%software_name% by %software_company%" /></a>', '0', 728, 90, 0),
(2, 'Sponsor pH7 Dating CMS 2 (728x90)', '<a href="%software_website%"><img src="%software_website%/static/img/logo2-728x90.gif" alt="%software_name%" title="%software_name% by %software_company%" /></a>', '0', 728, 90, 0),
(3, 'Sponsor pH7 Dating CMS 3 (468x60)', '<a href="%software_website%"><img src="%software_website%/static/img/logo1-468x60.gif" alt="%software_name%" title="%software_name% by %software_company%" /></a>', '0', 468, 60, 0),
(4, 'Sponsor pH7 Dating CMS 4 (468x60)', '<a href="%software_website%"><img src="%software_website%/static/img/logo2-468x60.gif" alt="%software_name%" title="%software_name% by %software_company%" /></a>', '0', 468, 60, 0),
(5, 'Sponsor pH7 Dating CMS 5  (120x600)', '<a href="%software_website%"><img src="%software_website%/static/img/logo1-120x600.gif" alt="%software_name%" title="%software_name% by %software_company%" /></a>', '0', 120, 600, 0),
(6, 'Sponsor pH7 Dating CMS 6  (120x600)', '<a href="%software_website%"><img src="%software_website%/static/img/logo2-120x600.gif" alt="%software_name%" title="%software_name% by %software_company%" /></a>', '0', 120, 600, 0);


CREATE TABLE IF NOT EXISTS pH7_AdsClicks (
  adsId smallint(4) unsigned NOT NULL,
  dateTime datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  url varchar(255) DEFAULT NULL,
  ip int(11) DEFAULT NULL,
  PRIMARY KEY (adsId),
  FOREIGN KEY (adsId) REFERENCES pH7_Ads(adsId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS pH7_AdsAffiliates (
  adsId smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(40) DEFAULT NULL,
  code text,
  active enum('1','0') DEFAULT '1',
  width smallint(4) DEFAULT NULL,
  height smallint(4) DEFAULT NULL,
  views int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (adsId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO pH7_AdsAffiliates (adsId, name, code, active, width, height, views) VALUES
(1, 'Sponsor pH7 Dating CMS 1 (728x90)', '<a href="%affiliate_url%"><img src="%software_website%/static/img/logo1-728x90.gif" alt="%software_name%" title="%software_name% by %software_company%" /></a>', '0', 728, 90, 0),
(2, 'Sponsor pH7 Dating CMS 2 (728x90)', '<a href="%affiliate_url%/signup"><img src="%software_website%/static/img/logo2-728x90.gif" alt="%software_name%" title="%software_name% by %software_company%" /></a>', '0', 728, 90, 0),
(3, 'Sponsor pH7 Dating CMS 3 (468x60)', '<a href="%affiliate_url%/signup"><img src="%software_website%/static/img/logo1-468x60.gif" alt="%software_name%" title="%software_name% by %software_company%" /></a>', '0', 468, 60, 0),
(4, 'Sponsor pH7 Dating CMS 4 (468x60)', '<a href="%affiliate_url%"><img src="%software_website%/static/img/logo2-468x60.gif" alt="%software_name%" title="%software_name% by %software_company%" /></a>', '0', 468, 60, 0),
(5, 'Sponsor pH7 Dating CMS 5  (120x600)', '<a href="%affiliate_url%"><img src="%software_website%/static/img/logo1-120x600.gif" alt="%software_name%" title="%software_name% by %software_company%" /></a>', '0', 120, 600, 0),
(6, 'Sponsor pH7 Dating CMS 6  (120x600)', '<a href="%affiliate_url%"><img src="%software_website%/static/img/logo2-120x600.gif" alt="%software_name%" title="%software_name% by %software_company%" /></a>', '0', 120, 600, 0);


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
  file varchar(40) NOT NULL,
  approved enum('1','0') DEFAULT '1',
  votes int(9) unsigned DEFAULT '0',
  score float(9) unsigned DEFAULT '0',
  views int(10) unsigned DEFAULT '0',
  description varchar(255) DEFAULT NULL,
  createdDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  updatedDate datetime DEFAULT NULL,
  PRIMARY KEY (pictureId),
  FOREIGN KEY (albumId) REFERENCES pH7_AlbumsPictures(albumId),
  FOREIGN KEY (profileId) REFERENCES pH7_Members(profileId)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_Videos (
  videoId int(10) unsigned NOT NULL AUTO_INCREMENT,
  profileId int(10) unsigned NOT NULL,
  albumId int(10) unsigned NOT NULL,
  file varchar(255) DEFAULT NULL, -- e.g. http://youtu.be/4fplAZfO9KY or local file server.
  thumb varchar(255) DEFAULT NULL, -- e.g. http://img.youtube.com/vi/4fplAZfO9KY/default.jpg or local file server.
  approved enum('1','0') NOT NULL DEFAULT '1',
  votes int(9) unsigned DEFAULT '0',
  score float(9) unsigned DEFAULT '0',
  views int(10) unsigned DEFAULT '0',
  description varchar(255) DEFAULT NULL,
  title varchar(80) DEFAULT NULL,
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
(1, 'Google Analytics', '<script>\r\nvar _gaq = _gaq || [];\r\n  _gaq.push([''_setAccount'', ''YOUR_ID_API'']);\r\n  _gaq.push([''_trackPageview'']);\r\n  (function() {\r\n    var ga = document.createElement(''script''); ga.type = ''text/javascript''; ga.async = true;\r\n    ga.src = (''https:'' == document.location.protocol ? ''https://ssl'' : ''http://www'') + ''.google-analytics.com/ga.js'';\r\n    var s = document.getElementsByTagName(''script'')[0]; s.parentNode.insertBefore(ga, s);\r\n  })();</script>', '1');


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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS pH7_BlogsCategories (
  categoryId smallint(4) unsigned NOT NULL,
  blogId mediumint(10) unsigned NOT NULL,
   INDEX (categoryId),
   INDEX (blogId),
   FOREIGN KEY (blogId) REFERENCES pH7_Blogs(blogId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_ForumsCategories (
  categoryId tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
  title varchar(60) DEFAULT NULL,
  PRIMARY KEY (categoryId),
  UNIQUE KEY (title)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO pH7_ForumsCategories (categoryId, title) VALUES
(1, 'General'),
(2, 'Free Online Dating Site'),
(3, 'Business');


CREATE TABLE IF NOT EXISTS pH7_Forums (
  forumId mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'New forum',
  description varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  categoryId tinyint(4) unsigned DEFAULT NULL,
  createdDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  updatedDate datetime DEFAULT NULL,
  PRIMARY KEY (forumId),
  FOREIGN KEY (categoryId) REFERENCES pH7_ForumsCategories(categoryId)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

INSERT INTO pH7_Forums (forumId, name, description, categoryId) VALUES
(1, 'Hello', 'Free dating site', 1),
(2, 'Online Dating', 'The discussion for the website online dating', 2),
(3, 'The Best Dating Site', 'The best dating site', 1);


CREATE TABLE IF NOT EXISTS pH7_ForumsTopics (
  topicId int(10) unsigned NOT NULL AUTO_INCREMENT,
  forumId mediumint(10) unsigned DEFAULT NULL,
  profileId int(10) unsigned NOT NULL,
  title varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  message text COLLATE utf8_unicode_ci,
  approved enum('1','0') COLLATE utf8_unicode_ci DEFAULT '1',
  createdDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  updatedDate datetime DEFAULT NULL,
  views int(11) NOT NULL DEFAULT '0',
  -- Maybe we'll let the topic of member even if the member is deleted
  -- FOREIGN KEY (profileId) pH7_Members(profileId),
  FOREIGN KEY (forumId) REFERENCES pH7_Forums(forumId),
  PRIMARY KEY (topicId)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_ForumsMessages (
  messageId int(10) unsigned NOT NULL AUTO_INCREMENT,
  topicId int(10) unsigned NOT NULL,
  profileId int(10) unsigned NOT NULL,
  message text COLLATE utf8_unicode_ci,
  approved enum('1','0') COLLATE utf8_unicode_ci DEFAULT '1',
  createdDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  updatedDate datetime DEFAULT NULL,
  -- Maybe we'll let the topic of member even if the member is deleted
  -- FOREIGN KEY (profileId) pH7_Members(profileId),
  FOREIGN KEY (topicId) REFERENCES pH7_ForumsTopics(topicId),
  PRIMARY KEY (messageId)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_GroupsLevels (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(64) CHARACTER SET armscii8 NOT NULL,
  icon varchar(64) CHARACTER SET armscii8 NOT NULL,
  slog varchar(64) CHARACTER SET armscii8 DEFAULT NULL,
  level tinyint(2) DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_Language (
  langId varchar(5) NOT NULL DEFAULT '',
  name varchar(60) NOT NULL,
  lang_code varchar(5) NOT NULL,
  charset varchar(15) NOT NULL,
  active enum('0','1') NOT NULL DEFAULT '0',
  direction enum('ltr','rtl') NOT NULL DEFAULT 'ltr',
  author varchar(60) NOT NULL,
  website varchar(120) DEFAULT NULL,
  email varchar(120) DEFAULT NULL,
  PRIMARY KEY (langId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO pH7_Language (langId, name, lang_code, charset, active, direction, author, website, email) VALUES
('en_US', 'English', 'en_US', 'UTF-8', '1', 'ltr', 'Pierre-Henry', 'http://hizup.com', 'ph7software@gmail.com'),
('fr_FR', 'Français', 'fr_FR', 'UTF-8', '1', 'ltr', 'Pierre-Henry', 'http://hizup.com', 'ph7software@gmail.com');


CREATE TABLE IF NOT EXISTS pH7_Likes (
  keyId varchar(255) NOT NULL,
  votes int(10) unsigned NOT NULL,
  lastVote datetime NOT NULL,
  lastIp varchar(20) NOT NULL,
  UNIQUE KEY keyId (keyId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS pH7_LogError (
  logId mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  logError longtext,
  PRIMARY KEY (logId),
  FULLTEXT KEY logError (logError) -- FULLTEXT is not supported by InnoDB
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS pH7_AdminsAttemptsLogin (
  ip varchar(20) NOT NULL DEFAULT '',
  attempts smallint(5) unsigned NOT NULL ,
  lastLogin DATETIME NOT NULL,
  UNIQUE KEY (ip)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_MembersAttemptsLogin (
  ip varchar(20) NOT NULL DEFAULT '',
  attempts smallint(5) unsigned NOT NULL ,
  lastLogin DATETIME NOT NULL,
  UNIQUE KEY (ip)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_AffiliatesAttemptsLogin (
  ip varchar(20) NOT NULL DEFAULT '',
  attempts smallint(5) unsigned NOT NULL ,
  lastLogin DATETIME NOT NULL,
  UNIQUE KEY (ip)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_AdminsLogLogin (
  logId mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  email varchar(120) NOT NULL DEFAULT '',
  username varchar(64) NOT NULL DEFAULT '',
  password varchar(40) DEFAULT NULL,
  status varchar(60) NOT NULL DEFAULT '',
  ip varchar(20) NOT NULL DEFAULT '',
  dateTime timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (logId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_MembersLogLogin (
  logId mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  email varchar(120) NOT NULL DEFAULT '',
  username varchar(64) NOT NULL DEFAULT '',
  password varchar(40) DEFAULT NULL,
  status varchar(60) NOT NULL DEFAULT '',
  ip varchar(20) NOT NULL DEFAULT '',
  dateTime timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (logId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_AffiliatesLogLogin (
  logId mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  email varchar(120) NOT NULL DEFAULT '',
  username varchar(64) NOT NULL DEFAULT '',
  password varchar(40) DEFAULT NULL,
  status varchar(60) NOT NULL DEFAULT '',
  ip varchar(20) NOT NULL DEFAULT '',
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
  ip varchar(20) NOT NULL DEFAULT '127.0.0.1',
  userAgent varchar(100) NOT NULL,
  guest tinyint(4) unsigned NOT NULL DEFAULT 1,
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
  ip varchar(20) NOT NULL DEFAULT '127.0.0.1',
  userAgent varchar(100) NOT NULL,
  guest tinyint(4) unsigned NOT NULL DEFAULT 1,
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
  ip varchar(20) NOT NULL DEFAULT '127.0.0.1',
  userAgent varchar(100) NOT NULL,
  guest tinyint(4) unsigned NOT NULL DEFAULT 1,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


CREATE TABLE IF NOT EXISTS pH7_MembersWhoViews (
  profileId int(10) unsigned NOT NULL,
  visitorId int(10) unsigned NOT NULL,
  lastVisit datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  INDEX profileId (profileId),
  INDEX visitorId (visitorId),
  FOREIGN KEY (profileId) REFERENCES pH7_Members(profileId),
  FOREIGN KEY (visitorId) REFERENCES pH7_Members(profileId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


CREATE TABLE IF NOT EXISTS pH7_MembersFriends (
  profileId int(10) unsigned NOT NULL,
  friendId int(10) unsigned NOT NULL,
  requestDate datetime DEFAULT NULL,
  pending tinyint(1) unsigned NOT NULL DEFAULT '0',
  INDEX profileId (profileId),
  INDEX friendId (friendId),
  FOREIGN KEY (profileId) REFERENCES pH7_Members(profileId),
  FOREIGN KEY (friendId) REFERENCES pH7_Members(profileId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



CREATE TABLE IF NOT EXISTS pH7_MembersWall (
  wallId int(10) unsigned NOT NULL AUTO_INCREMENT,
  profileId int(10) unsigned NOT NULL DEFAULT '0',
  post text CHARACTER SET armscii8,
  createdDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  updatedDate datetime DEFAULT NULL,
  PRIMARY KEY (wallId),
  FOREIGN KEY (profileId) REFERENCES pH7_Members(profileId)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


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
  slogan varchar(200) NOT NULL,
  metaRobots varchar(50) NOT NULL DEFAULT '',
  metaAuthor varchar(50) NOT NULL DEFAULT '',
  metaCopyright varchar(50) NOT NULL DEFAULT '',
  metaRating varchar(50) NOT NULL DEFAULT '',
  metaDistribution varchar(50) NOT NULL DEFAULT '',
  metaCategory varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (langId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO pH7_MetaMain (langId, pageTitle, metaDescription, metaKeywords, slogan, metaRobots, metaAuthor, metaCopyright, metaRating, metaDistribution, metaCategory) VALUES
('fr_FR', 'Accueil', 'Le CMS pour la création de site de rencontre en ligne', 'script, CMS, clone rencontre, PHP, script rencontre, logiciel rencontre site, reseau social, cms communautaire', 'Le CMS-Dating, Le premier CMS spécialisé dans la rencontre en ligne !', 'index, follow, all', 'pH7 Company !', 'Copyright Pierre-Henry Soria. Tous droits réservés.', 'general', 'global', 'rencontre'),
('en_US', 'Home', 'The Dating software for creating online dating site or online community, social network,', 'script, CMS, PHP, dating script, dating software, social networking software, social networking script, social network script, free, open source, match clone, friend finder clone, adult friend finder clone', 'Dating CMS Script is the leading CMS specializes in online dating software open source!', 'index, follow, all', 'Dating CMS Company!', 'Copyright Pierre-Henry Soria. All Rights Reserved.', 'general', 'global', 'dating');


CREATE TABLE IF NOT EXISTS pH7_Modules (
  id smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  packageName varchar(120) NOT NULL,
  title varchar(120) NOT NULL,
  version tinyint(4) NOT NULL,
  uri varchar(32) DEFAULT NULL,
  path varchar(255) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS pH7_Report (
  reportId smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  reporterId int(10) unsigned DEFAULT NULL,
  spammerId int(10) unsigned DEFAULT NULL,
  dateTime datetime DEFAULT NULL,
  contentType enum('profile','avatar','mail','comment','photo','video','forum','note') NOT NULL DEFAULT 'profile',
  description varchar(255) DEFAULT NULL,
  url varchar(255) DEFAULT NULL,
  PRIMARY KEY (reportId),
  FOREIGN KEY (reporterId) REFERENCES pH7_Members(profileId),
  FOREIGN KEY (spammerId) REFERENCES pH7_Members(profileId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_Settings (
  `name` varchar(64) NOT NULL DEFAULT '',
  value varchar(80) NOT NULL,
  `desc` varchar(150) NOT NULL DEFAULT '',
  `group` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO pH7_Settings (`name`, value, `desc`, `group`) VALUES
('siteName', '¡pH7! Social Dating CMS', '', 'general'),
('adminEmail', @sAdminEmail, '', 'email'),
('defaultLanguage', 'en_US', '', 'language'),
('defaultTemplate', 'base', '', 'design'),
('emailName', '¡pH7! Social Dating CMS', '', 'email'),
('feedbackEmail', @sFeedbackEmail, '', 'email'),
('splashPage', '0', 'Use Splash Page | enable = 1 or disable = 0', 'general'),
('fullAjaxSite', '0', 'enable = 1 or disable = 0', 'general'),
('ipLogin', '', '', 'security'),
('timeDelayUserRegistration', '1440', '1440 minutes = 24 hours (in minutes!)', 'spam'),
('timeDelayAffRegistration', '2880', '2880 minutes = 2 days (in minutes!)', 'spam'),
('timeDelaySendNote', '20', 'Waiting time to add a new note post, in minutes!', 'spam'),
('timeDelaySendMail', '3', 'Waiting time to send a new message, in minutes!', 'spam'),
('timeDelaySendComment', '5', 'Waiting time to send a new comment, in minutes!', 'spam'),
('timeDelaySendForumTopic', '5', 'Waiting time to send a new topic in the forum, in minutes!', 'spam'),
('timeDelaySendForumMsg', '10', 'Waiting time to send a reply message in the same topic, in minutes!', 'spam'),
('isCaptchaUserSignup', '0', '0 for disable or 1 for enable', 'spam'),
('isCaptchaAffiliateSignup', '0', '0 for disable or 1 for enable', 'spam'),
('isCaptchaMail', '0', '0 for disable or 1 for enable', 'spam'),
('isCaptchaComment', '0', '0 for disable or 1 for enable', 'spam'),
('isCaptchaForum', '0', '0 for disable or 1 for enable', 'spam'),
('isCaptchaNote', '0', '0 for disable or 1 for enable', 'spam'),
('mailType', 'mail', '', 'email'),
('mapType', 'roadmap', 'Choose between: ''roadmap'', ''hybrid'', ''terrain'', ''satellite''', 'map'),
('maxAgeRegistration', '99', '', 'registration'),
('minAgeRegistration', '18', '', 'registration'),
('minUsernameLength', '3', '', 'registration'),
('maxUsernameLength', '30', '', 'registration'),
('userActivationType', '1', '1 = no activation, 2 = email activation, 3 = Manual activation by the administrator', 'registration'),
('affActivationType', '1', '1 = no activation, 2 = email activation, 3 = Manual activation by the administrator', 'registration'),
('isUniversalLogin', '0', '0 for disable or 1 for enable', 'registration'),
('defaultMembershipGroupId', 2, 'Default Membership Group', 'registration'),
('minPasswordLength', '6', '', 'security'),
('maxPasswordLength', '60', '', 'security'),
('isUserLoginAttempt', 1, 'Enable blocking connection attempts abusive. Enable = 1 or disable = 0', 'security'),
('isAdminLoginAttempt', 1, 'Enable blocking connection attempts abusive. Enable = 1 or disable = 0', 'security'),
('isAffiliateLoginAttempt', 1, 'Enable blocking connection attempts abusive. Enable = 1 or disable = 0', 'security'),
('maxUserLoginAttempts', 30, 'Maximum login attempts before blocking', 'security'),
('maxAffiliateLoginAttempts', 20, 'Maximum login attempts before blocking', 'security'),
('maxAdminLoginAttempts', 10, 'Maximum login attempts before blocking', 'security'),
('loginUserAttemptTime', 60, 'Time before a new connection attempt, in minutes!', 'security'),
('loginAffiliateAttemptTime', 60, 'Time before a new connection attempt, in minutes!', 'security'),
('loginAdminAttemptTime', 120, 'Time before a new connection attempt, in minutes!', 'security'),
('avatarManualApproval', '0', '0 for disable or 1 for enable ', 'moderation'),
('profileBackgroundManualApproval', '0', '0 for disable or 1 for enable ', 'moderation'),
('noteManualApproval', '0', '0 for disable or 1 for enable ', 'moderation'),
('pictureManualApproval', '0', '0 for disable or 1 for enable ', 'moderation'),
('videoManualApproval', '0', '0 for disable or 1 for enable ', 'moderation'),
('webcamPictureManualApproval', '0', '0 for disable or 1 for enable', 'moderation'),
('defaultVideo', @sDefaultVideoUrl, 'Video by default if no video found', 'video'),
('autoplayVideo', '1', '1 = Autoplay is enabled, 0 = Autoplay is disabled', 'video'),
('returnEmail', @sNoReplyEmail, 'Generally noreply@yoursite.com', 'email'),
('sendReportMail', '1', 'Send the Report by eMail (1 = enable, 0 = disable)', 'security'),
('siteStatus', 'enable', 'enable or maintenance', 'general'),
('smtpHostName', 'mail.example.com', '', 'email'),
('smtpPassword', '123456', '', 'email'),
('smtpPort', '25', '', 'email'),
('watermarkTextImage', 'HiZup.com', 'Watermark text', 'image'),
('sizeWatermarkTextImage', 2, 'Between 0 to 5', 'image'),
('banWordReplace', '[removed]',  '',  'security'),
('securityTokenLifetime', '480', 'Time in seconds. Default 480 seconds (8 min)', 'security'),
('DDoS', '0',  '0 for disabled or 1 for enabled',  'security'),
('cleanMsg', '0', '0 Delete messages older than days. 0 to disable', 'pruning'),
('cleanComment', '0', 'Delete comments older than days. 0 to disable', 'pruning'),
('cronSecurityHash', 'change_secret_cron_word_by_your', 'The secret word for the URL of the cron', 'automation'),
('userTimeout', '1', 'User inactivity timeout. The number of minutes that a member becomes inactive (offline).', 'automation'),
('ipApi', @sIpApiUrl, 'IP Api URL', 'api'),
('chatApi', @sChatApiUrl, 'Chat Api URL', 'api'),
('chatrouletteApi', @sChatrouletteApiUrl, 'Chatroulette Api URL', 'api'),
('isSoftwareNewsFeed', '1', 'Enable the news feed. 0 = Disable | 1 = Enable', 'general');


CREATE TABLE IF NOT EXISTS pH7_Subscribers (
  profileId int(10) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(200) NOT NULL,
  email varchar(120) NOT NULL,
  joinDate datetime DEFAULT NULL,
  active tinyint(1) unsigned NOT NULL DEFAULT 2, -- 1 = Active Account, 2 = Pending Account
  ip varchar(20) NOT NULL DEFAULT '127.0.0.1',
  hashValidation varchar(40) DEFAULT NULL,
  INDEX (profileId),
  PRIMARY KEY (profileId),
  UNIQUE KEY (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS pH7_StaticFiles (
  staticId smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  file varchar(255) NOT NULL,
  fileType enum('css', 'js') NOT NULL,
  active enum('1','0') DEFAULT '1',
  PRIMARY KEY (staticId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_License (
  licenseId smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  licenseKey text,
  PRIMARY KEY (licenseId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO pH7_License VALUES (1, '');


CREATE TABLE IF NOT EXISTS pH7_CustomCode (
  code text,
  codeType enum('css', 'js') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO pH7_CustomCode VALUES ('/* Your custom CSS code here */', 'css'), ('/* Your custom JS code here */\r\n\r\n// Don''t remove the code below. Inclusion of the JS file for Social Bookmark.\r\ndocument.write(''<script src="http://s7.addthis.com/js/250/addthis_widget.js"></script>'');', 'js');
