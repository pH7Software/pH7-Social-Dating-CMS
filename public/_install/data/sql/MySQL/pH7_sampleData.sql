--
--
-- Title:         Sample Data CMS File
--
-- Author:        Pierre-Henry Soria <ph7software@gmail.com>
-- Copyright:     (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
-- Package:       PH7 / Install / Data / Sql
-- Version:       1.0
--
--

-- Sample Members --

SET @iTotalMembers = 12;
SET @iGroupId = 2; -- 1 = Visitor, 9 = Pending, 2 = Regular (Free), 4 = Platinum, 5 = Silver, 6 = Gold
SET @iUserStatus = 1; -- 0 = Offline, 1 = Online, 2 = Busy, 3 = Away
SET @sCurrentDate = CURRENT_TIMESTAMP;
SET @sPassword = SHA1(RAND() + UNIX_TIMESTAMP());


INSERT INTO pH7_Members (email, username, password, firstName, lastName, birthDate, sex, matchSex, hashValidation, ip, lastActivity, businessName, address, street, city, state, zipCode, country, phone, fax, lang, featured, website, socialNetworkSite, description, bankAccount, active, userStatus, groupId, joinDate, avatar, prefixSalt, suffixSalt, views, reference, votes, score, credits, ban, approvedAvatar) VALUES

('demo2@demo.cow', 'garcia', @sPassword, 'Grace', 'Park', '1992-11-21', 'female', 'male', NULL, '127.0.0.1', @sCurrentDate, NULL, '', '', 'Manhattan', 'Manhattan', '10002', 'US', '00000000', '00', 'en_US', 0, NULL, NULL, 'Hello to all', NULL, 1, @iUserStatus, @iGroupId, @sCurrentDate, NULL, NULL, NULL, 0, NULL, 0, 0, 0, 0, 1),

('demo3@demo.cow', 'peter22', @sPassword, 'Peter', 'Backhard', '1977-12-21', 'male', '', NULL, '127.0.0.1', @sCurrentDate, NULL, '', '', 'New York', 'New York', '11226', 'US', '00000000', '00', 'en_US', 0, NULL, NULL, 'Hello to all', NULL, 1, @iUserStatus, @iGroupId, @sCurrentDate, NULL, NULL, NULL, 0, NULL, 0, 0, 0, 0, 1),

('demo4@demo.cow', 'katin', @sPassword, 'Katin', 'Layjyr', '1988-12-21', 'female', 'male', NULL, '127.0.0.1', @sCurrentDate, NULL, '', '', 'Manhattan', 'Manhattan', '10002', 'US', '00000000', '00', 'en_US', 0, NULL, NULL, 'Hello to all', NULL, 1, @iUserStatus, @iGroupId, @sCurrentDate, NULL, NULL, NULL, 0, NULL, 0, 0, 0, 0, 1),

('demo5@demo.cow', 'trinityI', @sPassword, 'Trinity', 'Rivic', '1988-12-21', 'female', 'male,female', NULL, '127.0.0.1', @sCurrentDate, NULL, '', '', 'Manhattan', 'Manhattan', '10002', 'US', '00000000', '00', 'en_US', 0, NULL, NULL, 'Hello to all', NULL, 1, @iUserStatus, @iGroupId, @sCurrentDate, NULL, NULL, NULL, 0, NULL, 0, 0, 0, 0, 1),

('demo6@demo.cow', 'JohnH', @sPassword, 'John', 'Pittsburgh', '1988-12-21', 'male', 'female', NULL, '127.0.0.1', @sCurrentDate, NULL, '', '', 'Manhattan', 'Manhattan', '10002', 'US', '00000000', '00', 'en_US', 0, NULL, NULL, 'Hello to all', NULL, 1, @iUserStatus, @iGroupId, @sCurrentDate, NULL, NULL, NULL, 0, NULL, 0, 0, 0, 0, 1),

('demo7@demo.cow', 'AntonR', @sPassword, 'Anton', 'Storn', '1968-12-21', 'male', '', NULL, '127.0.0.1', @sCurrentDate, NULL, '', '', 'Abinsk', 'Abinsk', '353320', 'RU', '00000000', '00', 'en_US', 0, NULL, NULL, 'Hi all, my name is Anton.', NULL, 1, @iUserStatus, @iGroupId, @sCurrentDate, NULL, NULL, NULL, 0, NULL, 0, 0, 0, 0, 1),

('demo8@demo.cow', 'kate62', @sPassword, 'Kate', 'Slater', '1988-12-21', 'female', 'male,female', NULL, '127.0.0.1', @sCurrentDate, NULL, '', '', 'Manhattan', 'Manhattan', '10002', 'US', '00000000', '00', 'en_US', 0, NULL, NULL, 'Hello to all', NULL, 1, @iUserStatus, @iGroupId, @sCurrentDate, NULL, NULL, NULL, 0, NULL, 0, 0, 0, 0, 1),

('demo9@demo.cow', 'MarkO', @sPassword, 'Mark', 'Yohir', '1978-01-21', 'male', 'male,female', NULL, '127.0.0.1', @sCurrentDate, NULL, '', '', 'Manhattan', 'Manhattan', '10002', 'US', '00000000', '00', 'en_US', 0, NULL, NULL, 'Hi baby!<br /> I am a handsome man tall and dark as women love.<br /> Come talk to me because you will not regret it!', NULL, 1, @iUserStatus, @iGroupId, @sCurrentDate, NULL, NULL, NULL, 0, NULL, 0, 0, 0, 0, 1),

('demo10@demo.cow', 'Tom4', @sPassword, 'Tomy', 'Pittsburgh', '1992-12-21', 'male', 'female', NULL, '127.0.0.1', @sCurrentDate, NULL, '', '', 'Manhattan', 'Manhattan', '10002', 'US', '00000000', '00', 'en_US', 0, NULL, NULL, 'Hello to all', NULL, 1, @iUserStatus, @iGroupId, @sCurrentDate, NULL, NULL, NULL, 0, NULL, 0, 0, 0, 0, 1),

('demo11@demo.cow', 'rachO0O', @sPassword, 'Rachel', 'Žarko', '1968-02-10', 'female', 'male', NULL, '127.0.0.1', @sCurrentDate, NULL, '', '', 'Dolton', 'Illinois', '60419', 'US', '00000000', '00', 'en_US', 0, NULL, NULL, 'Hello to all', NULL, 1, @iUserStatus, @iGroupId, @sCurrentDate, NULL, NULL, NULL, 0, NULL, 0, 0, 0, 0, 1),

('demo12@demo.cow', 'Stive', @sPassword, 'Stive', 'Upton', '1988-12-21', 'male', 'male,female', NULL, '127.0.0.1', @sCurrentDate, NULL, '', '', 'Manhattan', 'Manhattan', '10002', 'US', '00000000', '00', 'en_US', 0, NULL, NULL, 'Hello to all', NULL, 1, @iUserStatus, @iGroupId, @sCurrentDate, NULL, NULL, NULL, 0, NULL, 0, 0, 0, 0, 1),

('demo13@demo.cow', 'EmmaROK', @sPassword, 'Emma', 'Solon', '1992-06-16', 'female', 'male,female,couple', NULL, '127.0.0.1', @sCurrentDate, NULL, '', 'Soho', 'Westminster London', 'London', '139 L8', 'UK', '00000000', '00', 'en_US', 0, NULL, NULL, 'Hmmm, you\'ll see. I like to spend my free time on the Internet or traveling in different cities, places, mountain or sea...<br /> Listening good music of course.<br />See you soon! ;)', NULL, 1, @iUserStatus, @iGroupId, @sCurrentDate, NULL, NULL, NULL, 0, NULL, 0, 0, 0, 0, 1);
SET @iProfileId = LAST_INSERT_ID();

INSERT INTO pH7_MembersPrivacy (profileId, privacyProfile, searchProfile, userSaveViews) VALUES (@iProfileId, 'all', 'yes', 'yes');
INSERT INTO pH7_MembersPrivacy (profileId, privacyProfile, searchProfile, userSaveViews) VALUES (@iProfileId+1, 'all', 'yes', 'yes');
INSERT INTO pH7_MembersPrivacy (profileId, privacyProfile, searchProfile, userSaveViews) VALUES (@iProfileId+2, 'all', 'yes', 'yes');
INSERT INTO pH7_MembersPrivacy (profileId, privacyProfile, searchProfile, userSaveViews) VALUES (@iProfileId+3, 'all', 'yes', 'yes');
INSERT INTO pH7_MembersPrivacy (profileId, privacyProfile, searchProfile, userSaveViews) VALUES (@iProfileId+4, 'all', 'yes', 'yes');
INSERT INTO pH7_MembersPrivacy (profileId, privacyProfile, searchProfile, userSaveViews) VALUES (@iProfileId+5, 'all', 'yes', 'yes');
INSERT INTO pH7_MembersPrivacy (profileId, privacyProfile, searchProfile, userSaveViews) VALUES (@iProfileId+6, 'all', 'yes', 'yes');
INSERT INTO pH7_MembersPrivacy (profileId, privacyProfile, searchProfile, userSaveViews) VALUES (@iProfileId+7, 'all', 'yes', 'yes');
INSERT INTO pH7_MembersPrivacy (profileId, privacyProfile, searchProfile, userSaveViews) VALUES (@iProfileId+8, 'all', 'yes', 'yes');
INSERT INTO pH7_MembersPrivacy (profileId, privacyProfile, searchProfile, userSaveViews) VALUES (@iProfileId+9, 'all', 'yes', 'yes');
INSERT INTO pH7_MembersPrivacy (profileId, privacyProfile, searchProfile, userSaveViews) VALUES (@iProfileId+10, 'all', 'yes', 'yes');
INSERT INTO pH7_MembersPrivacy (profileId, privacyProfile, searchProfile, userSaveViews) VALUES (@iProfileId+11, 'all', 'yes', 'yes');

INSERT INTO pH7_MembersNotifications (profileId, enableNewsletters) VALUES (@iProfileId, 0);
INSERT INTO pH7_MembersNotifications (profileId, enableNewsletters) VALUES (@iProfileId+1, 0);
INSERT INTO pH7_MembersNotifications (profileId, enableNewsletters) VALUES (@iProfileId+2, 0);
INSERT INTO pH7_MembersNotifications (profileId, enableNewsletters) VALUES (@iProfileId+3, 0);
INSERT INTO pH7_MembersNotifications (profileId, enableNewsletters) VALUES (@iProfileId+4, 0);
INSERT INTO pH7_MembersNotifications (profileId, enableNewsletters) VALUES (@iProfileId+5, 0);
INSERT INTO pH7_MembersNotifications (profileId, enableNewsletters) VALUES (@iProfileId+6, 0);
INSERT INTO pH7_MembersNotifications (profileId, enableNewsletters) VALUES (@iProfileId+7, 0);
INSERT INTO pH7_MembersNotifications (profileId, enableNewsletters) VALUES (@iProfileId+8, 0);
INSERT INTO pH7_MembersNotifications (profileId, enableNewsletters) VALUES (@iProfileId+9, 0);
INSERT INTO pH7_MembersNotifications (profileId, enableNewsletters) VALUES (@iProfileId+10, 0);
INSERT INTO pH7_MembersNotifications (profileId, enableNewsletters) VALUES (@iProfileId+11, 0);




/*
DELIMITER |

WHILE (@iTotalMembers > 0) DO
   SET iTotalMembers = @iTotalMembers-1;
   INSERT INTO pH7_MembersPrivacy (profileId, privacyProfile, searchProfile, userSaveViews) VALUES
   (@iProfileId-@iTotalMembers, 'all', 'yes', 'yes');
END WHILE;

DELIMITER ;
*/
