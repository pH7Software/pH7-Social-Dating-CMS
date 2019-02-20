--
-- Author:        Pierre-Henry Soria <hi@ph7.me>
-- Copyright:     (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License
--

-- Remove unnecessary table
DROP TABLE ph7_license;


-- Update pH7CMS's SQL schema version
UPDATE ph7_modules SET version = '1.4.6' WHERE vendorName = 'pH7CMS';
