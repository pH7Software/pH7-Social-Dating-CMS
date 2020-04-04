--
-- Author:        Pierre-Henry Soria <hello@ph7cms.com>
-- Copyright:     (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License; https://www.gnu.org/licenses/gpl-3.0.en.html
--

INSERT INTO pH7_Settings (`name`, value, `desc`, `group`) VALUES
('allowUserToPartner', 0, 'If 1, gives the possibility to register instantly an account to a partner website', 'registration');

-- Update pH7CMS's SQL schema version
UPDATE pH7_Modules SET version = '1.3.8' WHERE vendorName = 'pH7CMS';
