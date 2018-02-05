--
--
-- Title:         Sample Data CMS File
--
-- Author:        Pierre-Henry Soria <hello@ph7cms.com>
-- Copyright:     (c) 2017-2018, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
-- Package:       PH7 / Install / Data / Sql / PostgreSQL
--
--

@iTotalMembers := 16;
@iGroupId := 2; -- 1 = Visitor, 9 = Pending, 2 = Regular (Free), 4 = Platinum, 5 = Silver, 6 = Gold
@iUserStatus := 1; -- 0 = Offline, 1 = Online, 2 = Busy, 3 = Away
@sCurrentDate := CURRENT_TIMESTAMP;
@sPassword := SHA1(RAND() + UNIX_TIMESTAMP());
@sDefIp := '37.205.56.35';


-- Sample Members --

INSERT INTO ph7_members (email, username, password, firstName, lastName, birthDate, sex, matchSex, ip, lastActivity, featured, active, userStatus, groupId, joinDate) VALUES
('demo2@demo.cow', 'garcia', @sPassword, 'Grace', 'Park', '1992-11-21', 'female', 'male', @sDefIp, @sCurrentDate, 0, 1, @iUserStatus, @iGroupId, @sCurrentDate),
('demo3@demo.cow', 'peter22', @sPassword, 'Peter', 'Backhard', '1977-12-21', 'male', 'female', @sDefIp, @sCurrentDate, 0, 1, @iUserStatus, @iGroupId, @sCurrentDate),
('demo4@demo.cow', 'katin', @sPassword, 'Katin', 'Layjyr', '1988-12-21', 'female', 'male', @sDefIp, @sCurrentDate, 0, 1, @iUserStatus, @iGroupId, @sCurrentDate),
('demo5@demo.cow', 'trinityI', @sPassword, 'Trinity', 'Rivic', '1988-12-21', 'female', 'male,female', @sDefIp, @sCurrentDate, 0, 1, @iUserStatus, @iGroupId, @sCurrentDate),
('demo6@demo.cow', 'JohnH', @sPassword, 'John', 'Pittsburgh', '1988-12-21', 'male', 'female', @sDefIp, @sCurrentDate, 0, 1, @iUserStatus, @iGroupId, @sCurrentDate),
('demo7@demo.cow', 'AntonR', @sPassword, 'Anton', 'Storn', '1968-12-21', 'male', 'female,couple', @sDefIp, @sCurrentDate, 0, 1, @iUserStatus, @iGroupId, @sCurrentDate),
('demo8@demo.cow', 'kate62', @sPassword, 'Kate', 'Slater', '1988-12-21', 'female', 'male,female', @sDefIp, @sCurrentDate, 0, 1, @iUserStatus, @iGroupId, @sCurrentDate),
('demo9@demo.cow', 'MarkO', @sPassword, 'Mark', 'Yohir', '1978-01-21', 'male', 'male,female', @sDefIp, @sCurrentDate, 0, 1, @iUserStatus, @iGroupId, @sCurrentDate),
('demo10@demo.cow', 'Tom4', @sPassword, 'Tomy', 'Pittsburgh', '1992-12-21', 'male', 'female', @sDefIp, @sCurrentDate, 0, 1, @iUserStatus, @iGroupId, @sCurrentDate),
('demo11@demo.cow', 'rachO0O', @sPassword, 'Rachel', 'Žarko', '1968-02-10', 'female', 'male', @sDefIp, @sCurrentDate, 0, 1, @iUserStatus, @iGroupId, @sCurrentDate),
('demo12@demo.cow', 'Stive', @sPassword, 'Stive', 'Upton', '1988-12-21', 'male', 'male,female', @sDefIp, @sCurrentDate, 0, 1, @iUserStatus, @iGroupId, @sCurrentDate),
('demo13@demo.cow', 'EmmaR', @sPassword, 'Emma', 'Solon', '1992-06-16', 'female', 'male,female,couple', @sDefIp, @sCurrentDate, 0, 1, @iUserStatus, @iGroupId, @sCurrentDate),
('demo14@demo.cow', 'scarlaaa', @sPassword, 'Scarlett', 'Stewart', '1990-05-26', 'female', 'male', @sDefIp, @sCurrentDate, 0, 1, @iUserStatus, @iGroupId, @sCurrentDate),
('demo15@demo.cow', 'lolo22', @sPassword, 'Lola', 'Weisz', '1991-06-09', 'female', 'male,female', @sDefIp, @sCurrentDate, 0, 1, @iUserStatus, @iGroupId, @sCurrentDate),
('demo16@demo.cow', 'bartys', @sPassword, 'Bart', 'San', '1978-11-01', 'male', 'female', @sDefIp, @sCurrentDate, 0, 1, @iUserStatus, @iGroupId, @sCurrentDate),
('demo17@demo.cow', 'wenwen', @sPassword, 'Wendy', 'Beaumnt', '1965-06-10', 'female', 'male,couple', @sDefIp, @sCurrentDate, 0, 1, @iUserStatus, @iGroupId, @sCurrentDate);
@iProfileId := LAST_INSERT_ID();


INSERT INTO ph7_members_info (profileId, description, city, state, zipCode, country) VALUES
(@iProfileId, 'Hello to all', 'Manhattan', 'Manhattan', '10002', 'US'),
(@iProfileId+1, 'Hello to all', 'New York', 'New York', '11226', 'US'),
(@iProfileId+2, 'Hello to all',  'Manhattan', 'Manhattan', '10002', 'US'),
(@iProfileId+3, 'Hello to all',  'Manhattan', 'Manhattan', '10002', 'US'),
(@iProfileId+4, 'Hello to all',  'Manhattan', 'Manhattan', '10002', 'US'),
(@iProfileId+5, 'Hi all, my name is Anton.',  'Abinsk', 'Abinsk', '353320', 'RU'),
(@iProfileId+6, 'Hello to all',  'Manhattan', 'Manhattan', '10002', 'US'),
(@iProfileId+7, 'Hi baby!<br /> I am a handsome man tall and dark as women love.<br /> Come talk to me because you will not regret it!',  'Manhattan', 'Manhattan', '10002', 'US'),
(@iProfileId+8, 'Hello to all',  'Manhattan', 'Manhattan', '10002', 'US'),
(@iProfileId+9, 'Hello to all',  'Dolton', 'Illinois', '60419', 'US'),
(@iProfileId+10, 'Hello to all',  'Manhattan', 'Manhattan', '10002', 'US'),
(@iProfileId+11, 'Hi :D', 'Soho', 'Westminster London', '139 L8', 'UK'),
(@iProfileId+12, 'Hi you y''all, what''s up? :-)',  'Manhattan', 'Manhattan', '10002', 'US'),
(@iProfileId+13, 'Hello to YOU. How are you?',  'Dolton', 'Illinois', '60419', 'US'),
(@iProfileId+14, 'Hello to all',  'Manhattan', 'Manhattan', '10002', 'US'),
(@iProfileId+15,  'Want to see new people!! :)', 'Soho', 'Westminster London', '139 L8', 'UK');



INSERT INTO ph7_members_privacy (profileId, privacyProfile, searchProfile, userSaveViews) VALUES
(@iProfileId, 'all', 'yes', 'yes'),
(@iProfileId+1, 'all', 'yes', 'yes'),
(@iProfileId+2, 'all', 'yes', 'yes'),
(@iProfileId+3, 'all', 'yes', 'yes'),
(@iProfileId+4, 'all', 'yes', 'yes'),
(@iProfileId+5, 'all', 'yes', 'yes'),
(@iProfileId+6, 'all', 'yes', 'yes'),
(@iProfileId+7, 'all', 'yes', 'yes'),
(@iProfileId+8, 'all', 'yes', 'yes'),
(@iProfileId+9, 'all', 'yes', 'yes'),
(@iProfileId+10, 'all', 'yes', 'yes'),
(@iProfileId+11, 'all', 'yes', 'yes'),
(@iProfileId+12, 'all', 'yes', 'yes'),
(@iProfileId+13, 'all', 'yes', 'yes'),
(@iProfileId+14, 'all', 'yes', 'yes'),
(@iProfileId+15, 'all', 'yes', 'yes');


INSERT INTO ph7_members_notifications (profileId, enableNewsletters, newMsg, friendRequest) VALUES
(@iProfileId, 0, 0, 0),
(@iProfileId+1, 0, 0, 0),
(@iProfileId+2, 0, 0, 0),
(@iProfileId+3, 0, 0, 0),
(@iProfileId+4, 0, 0, 0),
(@iProfileId+5, 0, 0, 0),
(@iProfileId+6, 0, 0, 0),
(@iProfileId+7, 0, 0, 0),
(@iProfileId+8, 0, 0, 0),
(@iProfileId+9, 0, 0, 0),
(@iProfileId+10, 0, 0, 0),
(@iProfileId+11, 0, 0, 0),
(@iProfileId+12, 0, 0, 0),
(@iProfileId+13, 0, 0, 0),
(@iProfileId+14, 0, 0, 0),
(@iProfileId+15, 0, 0, 0);


-- Sample Affiliates --

INSERT INTO ph7_affiliates (email, username, password, firstName, lastName, bankAccount, birthDate, sex, ip, lastActivity, joinDate) VALUES
('aff@affiliate.cow', 'aff1', @sPassword, 'Matthew', 'Rayen', 'bank_account@demo.cow', '1986-10-13', 'male', @sDefIp, @sCurrentDate, @sCurrentDate);
@iProfileId := LAST_INSERT_ID();


INSERT INTO ph7_affiliates_info (profileId, description, website, city, state, zipCode, country) VALUES
(@iProfileId, 'My Website is very nice!', 'http://hizup.com', 'New York', 'NYC', '10001', 'US');
