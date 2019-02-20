--
-- Author:        Pierre-Henry Soria <hello@ph7cms.com>
-- Copyright:     (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License; https://www.gnu.org/licenses/gpl-3.0.en.html
--

INSERT INTO pH7_Settings (`name`, value, `desc`, `group`) VALUES
('profileWithAvatarSet', 0, '1 to display only the profiles with a profile photo.', 'homepage');

-- Update pH7CMS's SQL schema version
UPDATE pH7_Modules SET version = '1.3.7' WHERE vendorName = 'pH7CMS';
