--
-- Author:        Pierre-Henry Soria <hi@ph7.me>
-- Copyright:     (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License
--

-- Update Social Media Widgets provider (from AddThis to AddToAny)
UPDATE ph7_static_files SET file = '//static.addtoany.com/menu/page.js' WHERE staticId = 1;


-- Update pH7CMS's SQL schema version
UPDATE ph7_modules SET version = '1.4.9' WHERE vendorName = 'pH7CMS';
