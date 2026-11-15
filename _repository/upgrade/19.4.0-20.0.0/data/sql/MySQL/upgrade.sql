--
-- Author:        Pierre-Henry Soria <hello@ph7builder.com>
-- Copyright:     (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
-- License:       MIT License
--

-- Let existing installations control modules that previously ran without explicit feature gates.
ALTER TABLE ph7_sys_mods_enabled MODIFY folderName varchar(64) NOT NULL;

INSERT INTO ph7_sys_mods_enabled (moduleTitle, folderName, premiumMod, enabled)
SELECT 'Milestone Celebration', 'milestone-celebration', '0', '1'
WHERE NOT EXISTS (
  SELECT 1 FROM ph7_sys_mods_enabled WHERE folderName = 'milestone-celebration'
);

INSERT INTO ph7_sys_mods_enabled (moduleTitle, folderName, premiumMod, enabled)
SELECT 'Profile Generator', 'profile-faker', '0', '1'
WHERE NOT EXISTS (
  SELECT 1 FROM ph7_sys_mods_enabled WHERE folderName = 'profile-faker'
);

-- Update pH7Builder's SQL schema version.
UPDATE ph7_modules SET version = '1.6.7' WHERE vendorName = 'pH7Builder';
