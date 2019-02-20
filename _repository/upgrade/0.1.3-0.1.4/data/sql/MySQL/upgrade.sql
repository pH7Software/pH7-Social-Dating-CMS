--
-- Author:        Pierre-Henry Soria <ph7software@gmail.com>
-- Copyright:     (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
--

TRUNCATE pH7_StaticJs;
TRUNCATE pH7_StaticCss;
ALTER TABLE pH7_Members MODIFY `country` char(2) DEFAULT NULL;
ALTER TABLE pH7_Members MODIFY `city` varchar(150) DEFAULT NULL;
ALTER TABLE pH7_Members MODIFY `state` varchar(150) DEFAULT NULL;
ALTER TABLE pH7_Members MODIFY `phone` varchar(100) DEFAULT NULL;
ALTER TABLE pH7_Members MODIFY `fax` varchar(100) DEFAULT NULL;
ALTER TABLE pH7_Members MODIFY `languageMember` varchar(2) NOT NULL DEFAULT 'en';
ALTER TABLE pH7_Members MODIFY `paymentTo` varchar(100) DEFAULT NULL;
ALTER TABLE pH7_Admins MODIFY `email` varchar(200) NOT NULL DEFAULT '';

ALTER TABLE pH7_Members MODIFY `votes` int(9) unsigned NULL DEFAULT  '0';
ALTER TABLE pH7_Members MODIFY `score` float(9) unsigned NULL DEFAULT  '0';
ALTER TABLE pH7_Members MODIFY `views` int(10) unsigned NULL DEFAULT  '0';

ALTER TABLE pH7_Pictures CHANGE totlaViews views INT(10) unsigned NULL DEFAULT  '0';
ALTER TABLE pH7_Pictures CHANGE totalScore score float(9) unsigned NULL DEFAULT  '0';
ALTER TABLE pH7_Pictures CHANGE totalVotes votes INT(9) unsigned NULL DEFAULT  '0';

ALTER TABLE pH7_Videos  ADD COLUMN views INT(10) unsigned NULL DEFAULT  '0';
ALTER TABLE pH7_Videos ADD COLUMN score float(9) unsigned NULL DEFAULT  '0';
ALTER TABLE pH7_Videos ADD COLUMN votes INT(9) unsigned NULL DEFAULT  '0';

ALTER TABLE pH7_AlbumsPictures CHANGE totlaViews views INT(10) unsigned NULL DEFAULT  '0';
ALTER TABLE pH7_AlbumsPictures CHANGE totalScore score float(9) unsigned NULL DEFAULT  '0';
ALTER TABLE pH7_AlbumsPictures CHANGE totalVotes votes INT(9) unsigned NULL DEFAULT  '0';

ALTER TABLE pH7_AlbumsVideos CHANGE totlaViews views INT(10) unsigned NULL DEFAULT  '0';
ALTER TABLE pH7_AlbumsVideos CHANGE totalScore score float(9) unsigned NULL DEFAULT  '0';
ALTER TABLE pH7_AlbumsVideos CHANGE totalVotes votes INT(9) unsigned NULL DEFAULT  '0';

ALTER TABLE pH7_Blogs CHANGE postDate createdDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE pH7_Blogs ADD COLUMN `updatedDate` datetime DEFAULT NULL;

ALTER TABLE pH7_Games CHANGE COLUMN dateAdded addedDate timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE pH7_Games ADD COLUMN `downloads` int(9) unsigned DEFAULT '0';
ALTER TABLE pH7_Games ADD COLUMN `votes` int(9) unsigned NOT NULL DEFAULT '0';
ALTER TABLE pH7_Games ADD COLUMN `score` float(9) unsigned NOT NULL DEFAULT '0';
ALTER TABLE pH7_Games ADD COLUMN `views` int(10) unsigned NOT NULL DEFAULT '0';


ALTER TABLE pH7_Ads MODIFY COLUMN `views` int(10) unsigned NOT NULL DEFAULT '0';

ALTER TABLE pH7_Members DROP COLUMN totalVisits;

ALTER TABLE pH7_Blogs ADD COLUMN `votes` int(9) unsigned DEFAULT '0';
ALTER TABLE pH7_Blogs ADD COLUMN `score` float(9) unsigned DEFAULT '0';
ALTER TABLE pH7_Blogs ADD COLUMN `views` int(10) unsigned DEFAULT '0';

ALTER TABLE pH7_Likes MODIFY `keyId` varchar(255) NOT NULL;

INSERT INTO `pH7_Settings` (
`name` ,
`value` ,
`desc` ,
`groupId`
)
VALUES (
'DDoS',  '0',  '0 for disabled or 1 for enabled',  'security'
);


