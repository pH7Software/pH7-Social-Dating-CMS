--
-- Author:        Pierre-Henry Soria <hi@ph7.me>
-- Copyright:     (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License
--

CREATE TABLE IF NOT EXISTS ph7_block_countries (
  countryId tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  countryCode char(2) NOT NULL,
  PRIMARY KEY (countryId),
  UNIQUE KEY (countryCode)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- Update pH7CMS's SQL schema version
UPDATE ph7_modules SET version = '1.4.4' WHERE vendorName = 'pH7CMS';
