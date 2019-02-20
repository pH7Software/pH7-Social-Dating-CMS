--
-- Author:        Pierre-Henry Soria <ph7software@gmail.com>
-- Copyright:     (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
--

ALTER TABLE pH7_Messages ADD COLUMN toDelete set('sender','recipient') NOT NULL DEFAULT '';

DROP TABLE pH7_StaticCss;
DROP TABLE pH7_StaticJs;


CREATE TABLE IF NOT EXISTS pH7_StaticFiles (
  staticId smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  file varchar(255) NOT NULL,
  fileType enum('css', 'js') NOT NULL,
  active enum('1','0') DEFAULT '1',
  PRIMARY KEY (staticId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pH7_CustomCode (
  code text,
  codeType enum('css', 'js') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
INSERT INTO pH7_CustomCode VALUES ('/* Your custom CSS code here */', 'css'), ('/* Your custom JS code here */\r\n\r\n// Don''t remove the code below. Inclusion of the JS file for Social Bookmark.\r\ndocument.write(''<script src="http://s7.addthis.com/js/250/addthis_widget.js"></script>'');', 'js');


ALTER TABLE pH7_Subscribers ADD COLUMN joinDate datetime DEFAULT NULL;
ALTER TABLE pH7_Subscribers ADD COLUMN ip varchar(20) NOT NULL DEFAULT '127.0.0.1';


--
-- For the convention table ... --
--
RENAME TABLE pH7_AdminsLogSession TO pH7_AdminsLogSess;
RENAME TABLE pH7_MembersLogSession TO pH7_MembersLogSess;

-- For Affiliate table --
RENAME TABLE pH7_AffiliateLogSession TO pH7_AffiliatesLogSess;
ALTER TABLE pH7_AffiliatesLogSess DROP FOREIGN KEY profileId;

RENAME TABLE pH7_AffiliateLogLogin TO pH7_AffiliatesLogLogin;
RENAME TABLE pH7_AffiliateAttemptsLogin TO pH7_AffiliatesAttemptsLogin;
RENAME TABLE pH7_AdsAffiliate TO pH7_AdsAffiliates;

RENAME TABLE pH7_AffiliateInfo TO pH7_AffiliatesInfo;
ALTER TABLE pH7_AffiliatesInfo DROP FOREIGN KEY profileId;

RENAME TABLE pH7_Affiliate TO pH7_Affiliates;

ADD CONSTRAINT pH7_AffiliatesInfo FOREIGN KEY (profileId) REFERENCES pH7_Affiliates (profileId);
ADD CONSTRAINT pH7_AffiliatesLogSess FOREIGN KEY (profileId) REFERENCES pH7_Affiliates (profileId);
