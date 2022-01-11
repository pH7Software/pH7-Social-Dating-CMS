--
-- Author:        Pierre-Henry Soria <hello@ph7cms.com>
-- Copyright:     (c) 2019, Pierre-Henry Soria. All Rights Reserved.
-- License:       MIT License
--

-- Add heading color overwriter fields
INSERT INTO ph7_settings (settingName, settingValue, description, settingGroup) VALUES
('heading1Color', '', 'Override H1 color. Leave empty to disable', 'design'),
('heading2Color', '', 'Override H2 color. Leave empty to disable', 'design'),
('heading3Color', '', 'Override H3 color. Leave empty to disable', 'design');


-- Update pH7Builder's SQL schema version
UPDATE ph7_modules SET version = '1.5.7' WHERE vendorName = 'pH7Builder';
