--
-- Author:        Pierre-Henry Soria <hello@ph7cms.com>
-- Copyright:     (c) 2020, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License
--

-- Add heading color overwriter fields
INSERT INTO ph7_settings (settingName, settingValue, description, settingGroup) VALUES
('navbarType', 'default', 'Choose between "default" or "dark"', 'design');


-- Update pH7CMS's SQL schema version
UPDATE ph7_modules SET version = '1.5.8' WHERE vendorName = 'pH7CMS';
