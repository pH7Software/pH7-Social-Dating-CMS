--
-- Author:        Pierre-Henry Soria <hi@ph7.me>
-- Copyright:     (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License
--


-- Change pH7_Settings's primary key
ALTER TABLE pH7_Settings DROP PRIMARY KEY, ADD PRIMARY KEY (settingName);

-- Add new setting field
INSERT INTO pH7_Settings (settingName, settingValue, description, settingGroup) VALUES
('displayPoweredByLink', 1, 'Show or not the Branding link in the footer.', 'general');

-- Update pH7CMS's SQL schema version
UPDATE pH7_Modules SET version = '1.4.1' WHERE vendorName = 'pH7CMS';
