--
-- Author:        Pierre-Henry Soria <hi@ph7.me>
-- Copyright:     (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License
--

-- Add Progressive Web App support
INSERT INTO ph7_sys_mods_enabled (moduleTitle, folderName, premiumMod, enabled) VALUES
('Progressive Web App (https required)', 'pwa', '0', '0');


-- Update pH7CMS's SQL schema version
UPDATE ph7_modules SET version = '1.5.1' WHERE vendorName = 'pH7CMS';
