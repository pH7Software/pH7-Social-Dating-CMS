--
-- Author:        Pierre-Henry Soria <ph7software@gmail.com>
-- Copyright:     (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
-- License:       MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
--

ALTER TABLE pH7_Members ADD COLUMN height tinyint(3) unsigned DEFAULT NULL;
ALTER TABLE pH7_Members ADD COLUMN weight tinyint(3) unsigned DEFAULT NULL;
