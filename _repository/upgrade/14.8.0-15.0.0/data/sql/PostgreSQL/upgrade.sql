--
-- Author:        Pierre-Henry Soria <hello@ph7cms.com>
-- Copyright:     (c) 2019, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License
--

-- Add the ability to enable/disave Session IP Check
INSERT INTO ph7_sys_mods_enabled (moduleTitle, folderName, premiumMod, enabled) VALUES
('SMS Verification', 'sms-verification', '0', '0');


-- Update pH7CMS's SQL schema version
UPDATE ph7_modules SET version = '1.5.5' WHERE vendorName = 'pH7CMS';
