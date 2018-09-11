--
-- Author:        Pierre-Henry Soria <hi@ph7.me>
-- Copyright:     (c) 2018, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License
--

-- Remove field that is no longer used
ALTER TABLE ph7_memberships DROP COLUMN orderId;


-- Update pH7CMS's SQL schema version
UPDATE ph7_modules SET version = '1.4.8' WHERE vendorName = 'pH7CMS';
