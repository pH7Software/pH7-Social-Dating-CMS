--
-- Author:        Pierre-Henry Soria <ph7software@gmail.com>
-- Copyright:     (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
-- License:       MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
--


UPDATE pH7_Settings SET `name` = 'bgProfileManualApproval' WHERE `name` = 'profileBackgroundManualApproval';

INSERT INTO pH7_Settings (`name`, value, `desc`, `group`) VALUES
('socialMediaWidgets', 0, 'Enable the Social Media Widgets such as Like and Sharing buttons. 0 = Disable | 1 = Enable', 'general');
