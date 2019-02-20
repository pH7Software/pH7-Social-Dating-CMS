--
-- Author:        Pierre-Henry Soria <ph7software@gmail.com>
-- Copyright:     (c) 2014-2019, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
--

ALTER TABLE pH7_Admins MODIFY password varchar(120) NOT NULL;
ALTER TABLE pH7_Members MODIFY password varchar(120) NOT NULL;
ALTER TABLE pH7_Affiliates MODIFY password varchar(120) NOT NULL;
ALTER TABLE pH7_Admins DROP prefixSal;
ALTER TABLE pH7_Admins DROP suffixSalt;
ALTER TABLE pH7_Members DROP prefixSal;
ALTER TABLE pH7_Members DROP suffixSalt;
ALTER TABLE pH7_Affiliates DROP prefixSal;
ALTER TABLE pH7_Affiliates DROP suffixSalt;


CREATE TABLE IF NOT EXISTS pH7_License (
  licenseId smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  licenseKey text,
  PRIMARY KEY (licenseId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO pH7_License VALUES (1, '');

ALTER TABLE pH7_Members ADD COLUMN affiliatedId int(10) unsigned NOT NULL DEFAULT 0;
ALTER TABLE pH7_Affiliates ADD COLUMN affiliatedId int(10) unsigned NOT NULL DEFAULT 0;
ALTER TABLE pH7_Subscribers ADD COLUMN affiliatedId int(10) unsigned NOT NULL DEFAULT 0;


ALTER TABLE pH7_Affiliates CHANGE paymentLast lastPayment decimal(8,2) NOT NULL DEFAULT '0.00';
ALTER TABLE pH7_Affiliates CHANGE paymentLastDate lastPaymentDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE pH7_Affiliates CHANGE payment totalPayment decimal(8,2) NOT NULL DEFAULT '0.00';
ALTER TABLE pH7_Affiliates CHANGE summary amount decimal(8,2) NOT NULL DEFAULT '0.00';
ALTER TABLE pH7_AffiliatesInfo ADD COLUMN taxId varchar(40) DEFAULT NULL;
ALTER TABLE pH7_Affiliates DROP COLUMN credits;
