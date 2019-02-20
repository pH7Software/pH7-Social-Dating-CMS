--
-- Author:        Pierre-Henry Soria <hi@ph7.me>
-- Copyright:     (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License
--

-- Change reserved MySQL column names
-- Using backticks around reserved MySQL words is ugly and not working well with all environments
ALTER TABLE pH7_Settings CHANGE `name` settingName varchar(64) NOT NULL;
ALTER TABLE pH7_Settings CHANGE `value` settingValue varchar(150) DEFAULT '';
ALTER TABLE pH7_Settings CHANGE `desc` description varchar(120) DEFAULT '' COMMENT 'Informative desc about the setting';
ALTER TABLE pH7_Settings CHANGE `group` settingGroup varchar(12) NOT NULL;

-- Increase length of "metaCopyright" column for French copyright (since it is longer)
ALTER TABLE pH7_MetaMain MODIFY metaCopyright varchar(55);

-- Change "photo" enum value to "picture". Since it must be the module name here.
ALTER TABLE pH7_Report MODIFY contentType enum('user', 'avatar', 'mail', 'comment', 'picture', 'video', 'forum', 'note') NOT NULL DEFAULT 'user';

-- Change pH7_Settings's primary key
ALTER TABLE pH7_Settings DROP PRIMARY KEY, ADD PRIMARY KEY (settingName);

-- Add new setting field
INSERT INTO pH7_Settings (settingName, settingValue, description, settingGroup) VALUES
('displayPoweredByLink', 1, 'Show or not the Branding link in the footer.', 'general');

-- Update pH7CMS's SQL schema version
UPDATE pH7_Modules SET version = '1.4.1' WHERE vendorName = 'pH7CMS';
