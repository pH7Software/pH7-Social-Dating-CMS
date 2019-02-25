--
-- Author:        Pierre-Henry Soria <hello@ph7cms.com>
-- Copyright:     (c) 2019, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License
--

-- Add user text verification module
INSERT INTO ph7_sys_mods_enabled (moduleTitle, folderName, premiumMod, enabled) VALUES
('SMS Verification', 'sms-verification', '0', '0');

-- Allow to enable WYSIWYG editor (CKEditor) for the forum posts
INSERT INTO ph7_settings (settingName, settingValue, description, settingGroup) VALUES
('wysiwygEditorForum', 0, 'Enable or not the WYSIWYG. 0 = Disable | 1 = Enable', 'general');


-- Update pH7CMS's SQL schema version
UPDATE ph7_modules SET version = '1.5.5' WHERE vendorName = 'pH7CMS';
