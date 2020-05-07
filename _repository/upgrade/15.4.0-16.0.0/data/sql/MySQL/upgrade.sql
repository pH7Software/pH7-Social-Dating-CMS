--
-- Author:        Pierre-Henry Soria <hello@ph7cms.com>
-- Copyright:     (c) 2020, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License
--

-- Remove outdated tables
DROP TABLE ph7_admins_log_sess;
DROP TABLE ph7_members_log_sess;
DROP TABLE ph7_affiliates_log_sess;



CREATE TABLE IF NOT EXISTS ph7_admins_log_sess (
  sessionId int(10) unsigned NOT NULL AUTO_INCREMENT,
  profileId tinyint(3) unsigned NOT NULL,
  username varchar(40) DEFAULT NULL,
  email varchar(120) DEFAULT NULL,
  firstName varchar(50) DEFAULT NULL,
  lastName varchar(50) DEFAULT NULL,
  ip varchar(45) NOT NULL DEFAULT '127.0.0.1',
  dateTime timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (sessionId),
  FOREIGN KEY (profileId) REFERENCES ph7_admins(profileId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS ph7_members_log_sess (
  sessionId int(10) unsigned NOT NULL AUTO_INCREMENT,
  profileId int(10) unsigned NOT NULL,
  username varchar(40) DEFAULT NULL,
  email varchar(120) DEFAULT NULL,
  firstName varchar(50) DEFAULT NULL,
  lastName varchar(50) DEFAULT NULL,
  ip varchar(45) NOT NULL DEFAULT '127.0.0.1',
  dateTime timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (sessionId),
  FOREIGN KEY (profileId) REFERENCES ph7_members(profileId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS ph7_affiliates_log_sess (
  sessionId int(10) unsigned NOT NULL AUTO_INCREMENT,
  profileId int(10) unsigned NOT NULL,
  username varchar(40) DEFAULT NULL,
  email varchar(120) DEFAULT NULL,
  firstName varchar(50) DEFAULT NULL,
  lastName varchar(50) DEFAULT NULL,
  ip varchar(45) NOT NULL DEFAULT '127.0.0.1',
  dateTime timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (sessionId),
  FOREIGN KEY (profileId) REFERENCES ph7_affiliates(profileId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Update pH7CMS's SQL schema version
UPDATE ph7_modules SET version = '1.5.9' WHERE vendorName = 'pH7CMS';
