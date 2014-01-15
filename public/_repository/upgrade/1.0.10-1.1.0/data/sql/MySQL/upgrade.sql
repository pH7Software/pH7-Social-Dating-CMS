--
-- Author:        Pierre-Henry Soria <ph7software@gmail.com>
-- Copyright:     (c) 2014, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
--

ALTER TABLE pH7_Admins MODIFY password varchar(120) NOT NULL;
ALTER TABLE pH7_Admins DROP prefixSal;
ALTER TABLE pH7_Admins DROP suffixSalt;
ALTER TABLE pH7_Members DROP prefixSal;
ALTER TABLE pH7_Members DROP suffixSalt;
ALTER TABLE pH7_Affiliates DROP prefixSal;
ALTER TABLE pH7_Affiliates DROP suffixSalt;
