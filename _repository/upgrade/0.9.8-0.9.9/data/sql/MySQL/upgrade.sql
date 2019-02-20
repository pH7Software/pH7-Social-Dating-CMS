--
-- Author:        Pierre-Henry Soria <ph7software@gmail.com>
-- Copyright:     (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
--

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
  fax varchar(100) DEFAULT NULL,
  website varchar(200) DEFAULT NULL,
  socialNetworkSite varchar(200) DEFAULT NULL,
  height tinyint(3) unsigned DEFAULT NULL,
  weight tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (profileId),
  KEY country (country),
  FOREIGN KEY (profileId) REFERENCES pH7_Members(profileId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS pH7_AffiliateInfo (
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
  description text DEFAULT NULL,
  website varchar(200) DEFAULT NULL,
  fax varchar(100) DEFAULT NULL,
  PRIMARY KEY (profileId),
  KEY country (country),
  FOREIGN KEY (profileId) REFERENCES pH7_Affiliate(profileId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


ALTER TABLE pH7_Members DROP address;
ALTER TABLE pH7_Members DROP street;
ALTER TABLE pH7_Members DROP city;
ALTER TABLE pH7_Members DROP state;
ALTER TABLE pH7_Members DROP zipCode;
ALTER TABLE pH7_Members DROP country;
ALTER TABLE pH7_Members DROP phone;
ALTER TABLE pH7_Members DROP fax;
ALTER TABLE pH7_Members DROP website;
ALTER TABLE pH7_Members DROP socialNetworkSite;
ALTER TABLE pH7_Members DROP description;
ALTER TABLE pH7_Members DROP height;
ALTER TABLE pH7_Members DROP weight;
ALTER TABLE pH7_Members DROP KEY country;


ALTER TABLE pH7_Affiliate DROP businessName;
ALTER TABLE pH7_Affiliate DROP address;
ALTER TABLE pH7_Affiliate DROP street;
ALTER TABLE pH7_Affiliate DROP country;
ALTER TABLE pH7_Affiliate DROP city;
ALTER TABLE pH7_Affiliate DROP state;
ALTER TABLE pH7_Affiliate DROP zipCode;
ALTER TABLE pH7_Affiliate DROP phone;
ALTER TABLE pH7_Affiliate DROP description;
ALTER TABLE pH7_Affiliate DROP website;
ALTER TABLE pH7_Affiliate DROP fax;
ALTER TABLE pH7_Affiliate DROP KEY country;



ALTER TABLE pH7_MembersNotifications ADD COLUMN newMsg tinyint(1) unsigned NOT NULL DEFAULT 1;
ALTER TABLE pH7_MembersNotifications ADD COLUMN friendRequest tinyint(1) unsigned NOT NULL DEFAULT 1;


ALTER TABLE pH7_Admins ADD COLUMN timeZone varchar(3) NOT NULL DEFAULT '-6';
