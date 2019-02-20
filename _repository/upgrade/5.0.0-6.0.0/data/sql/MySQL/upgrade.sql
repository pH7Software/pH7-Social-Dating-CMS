--
-- Author:        Pierre-Henry Soria <hello@ph7cms.com>
-- Copyright:     (c) 2016-2019, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License; https://www.gnu.org/licenses/gpl-3.0.en.html
--

INSERT INTO pH7_Settings (`name`, value, `desc`, `group`) VALUES
('usersBlock', 1, '0 to disable or 1 to enable the profile photos on the homepage', 'homepage'),
('requireRegistrationAvatar', 0, '', 'registration');

INSERT INTO pH7_SysModsEnabled (moduleTitle, folderName, premiumMod, enabled) VALUES
('Related Profiles', 'related-profile', '0', '1'),
('Friends', 'friend', '0', '1');


-- Update pH7CMS's SQL schema version
UPDATE pH7_Modules SET version = '1.3.4' WHERE vendorName = 'pH7CMS';
