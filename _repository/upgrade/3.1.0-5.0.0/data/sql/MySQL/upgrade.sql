--
-- Author:        Pierre-Henry Soria <hello@ph7cms.com>
-- Copyright:     (c) 2016-2019, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License; https://www.gnu.org/licenses/gpl-3.0.en.html
--

ALTER TABLE pH7_SysModsEnabled ADD COLUMN moduleTitle varchar(50) NOT NULL;

DROP TABLE pH7_SysModsEnabled;

TRUNCATE TABLE pH7_SysModsEnabled;


INSERT INTO pH7_SysModsEnabled (moduleTitle, folderName, premiumMod, enabled) VALUES
('Affiliate', 'affiliate', '0', '1'),
('Game', 'game', '0', '1'),
('Chat', 'chat', '1', '0'),
('Chatroulette', 'chatroulette', '1', '0'),
('Picture', 'picture', '0', '1'),
('Video', 'video', '0', '1'),
('Hot or Not', 'hotornot', '0', '1'),
('Forum', 'forum', '0', '1'),
('Note (blog system for users)', 'note', '0', '1'),
('Blog (company blog)', 'blog', '0', '1'),
('Newsletter', 'newsletter', '0', '1'),
('Invite Friends', 'invite', '0', '1'),
('Social Media Authentication (connect module)', 'connect', '0', '0'),
('Webcam', 'webcam', '0', '1'),
('Love Calculator', 'love-calculator', '0', '1'),
('Mail', 'mail', '0', '1'),
('Instant Messaging (IM)', 'im', '0', '1'),
('User Dashboard', 'user-dashboard', '0', '1');


INSERT INTO pH7_Settings (`name`, value, `desc`, `group`) VALUES
('defaultSysModule', 'user', 'The default module running by default on the index page. Recommended to keep the "user" module', 'general');


-- Update pH7CMS's SQL schema version
UPDATE pH7_Modules SET version = '1.3.3' WHERE vendorName = 'pH7CMS';
