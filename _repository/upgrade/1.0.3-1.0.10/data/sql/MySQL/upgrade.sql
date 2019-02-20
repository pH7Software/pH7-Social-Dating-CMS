--
-- Author:        Pierre-Henry Soria <ph7software@gmail.com>
-- Copyright:     (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
--

ALTER TABLE pH7_Admins MODIFY email varchar(120) NOT NULL;
ALTER TABLE pH7_Members MODIFY email varchar(120) NOT NULL;
ALTER TABLE pH7_Affiliates MODIFY email varchar(120) NOT NULL;
ALTER TABLE pH7_Subscribers MODIFY email varchar(120) NOT NULL;
ALTER TABLE pH7_Language MODIFY email varchar(120) DEFAULT NULL;
ALTER TABLE pH7_AdminsLogLogin MODIFY email varchar(120) NOT NULL DEFAULT '';
ALTER TABLE pH7_MembersLogLogin MODIFY email varchar(120) NOT NULL DEFAULT '';
ALTER TABLE pH7_AffiliatesLogLogin MODIFY email varchar(120) NOT NULL DEFAULT '';
ALTER TABLE pH7_AdminsLogSess MODIFY email varchar(120) DEFAULT NULL;
ALTER TABLE pH7_MembersLogSess MODIFY email varchar(120) DEFAULT NULL;
ALTER TABLE pH7_AffiliatesLogSess MODIFY email varchar(120) DEFAULT NULL;

ALTER TABLE pH7_MembersInfo MODIFY website varchar(120) DEFAULT NULL;
ALTER TABLE pH7_MembersInfo MODIFY socialNetworkSite varchar(120) DEFAULT NULL;
ALTER TABLE pH7_AffiliatesInfo MODIFY website varchar(120) DEFAULT NULL;
ALTER TABLE pH7_Language MODIFY website varchar(120) DEFAULT NULL;

ALTER TABLE pH7_MembersInfo DROP fax;

ALTER TABLE pH7_Admins MODIFY timeZone varchar(6) NOT NULL DEFAULT '-6';

INSERT INTO pH7_Settings (`name`, value, `desc`, `group`) VALUES ('maxUsernameLength', '30', '', 'registration');
