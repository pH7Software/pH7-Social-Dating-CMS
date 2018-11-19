--
-- Author:        Pierre-Henry Soria <hi@ph7.me>
-- Copyright:     (c) 2018, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License
--

-- Add the ability to enable/disave Session IP Check
INSERT INTO ph7_settings (settingName, settingValue, description, settingGroup) VALUES
('isUserSessionIpCheck', 1, 'Enable it to Protect against session hijacking. Disable it if use dynamic IPs', 'security'),
('isAffiliateSessionIpCheck', 1, 'Enable it to Protect against session hijacking. Disable it if use dynamic IPs', 'security'),
('isAdminSessionIpCheck', 1, 'Enable it to Protect against session hijacking. Disable it if use dynamic IPs', 'security');


-- Update pH7CMS's SQL schema version
UPDATE ph7_modules SET version = '1.5.2' WHERE vendorName = 'pH7CMS';
