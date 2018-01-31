--
-- Author:        Pierre-Henry Soria <hi@ph7.me>
-- Copyright:     (c) 2018, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License
--

-- Add new module name into SysModsEnabled
INSERT INTO pH7_SysModsEnabled (moduleTitle, folderName, premiumMod, enabled) VALUES
('Cool Profile Page', 'cool-profile-page', '0', '0');

-- Update pH7CMS's SQL schema version
UPDATE pH7_Modules SET version = '1.4.2' WHERE vendorName = 'pH7CMS';
