--
-- Author:        Pierre-Henry Soria <hi@ph7.me>
-- Copyright:     (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License
--

-- Add field to give option for date-picker calendar or just age range
INSERT INTO ph7_settings (settingName, settingValue, description, settingGroup) VALUES
('isUserAgeRangeField', 1, '0 to disable or 1 to enable', 'registration');


-- Update pH7CMS's SQL schema version
UPDATE ph7_modules SET version = '1.4.7' WHERE vendorName = 'pH7CMS';
