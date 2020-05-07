--
-- Author:        Pierre-Henry Soria <hello@ph7cms.com>
-- Copyright:     (c) 2020, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License
--

-- Remove outdated fields
ALTER TABLE ph7_admins_log_sess
    DROP COLUMN sessionHash,
    DROP COLUMN idHash,
    DROP COLUMN userAgent,
    DROP COLUMN lastActivity,
    DROP COLUMN location,
    DROP COLUMN password,
    DROP COLUMN guest;

ALTER TABLE ph7_members_log_sess
    DROP COLUMN sessionHash,
    DROP COLUMN idHash,
    DROP COLUMN userAgent,
    DROP COLUMN lastActivity,
    DROP COLUMN location,
    DROP COLUMN password,
    DROP COLUMN guest;

ALTER TABLE ph7_affiliates_log_sess
    DROP COLUMN sessionHash,
    DROP COLUMN idHash,
    DROP COLUMN userAgent,
    DROP COLUMN lastActivity,
    DROP COLUMN location,
    DROP COLUMN password,
    DROP COLUMN guest;

-- Remove outdated keys
ALTER TABLE ph7_admins_log_sess
    DROP INDEX sessionHash,
    DROP INDEX lastActivity;

ALTER TABLE ph7_members_log_sess
    DROP INDEX sessionHash,
    DROP INDEX lastActivity;

ALTER TABLE ph7_affiliates_log_sess
    DROP INDEX sessionHash,
    DROP INDEX lastActivity;


-- Update pH7CMS's SQL schema version
UPDATE ph7_modules SET version = '1.5.9' WHERE vendorName = 'pH7CMS';
