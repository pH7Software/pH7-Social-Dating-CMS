ALTER TABLE pH7_AdminsAttemptsLogin ADD UNIQUE KEY (ip);
ALTER TABLE pH7_MembersAttemptsLogin ADD UNIQUE KEY (ip);
ALTER TABLE pH7_AffiliatesLogLogin ADD UNIQUE KEY (ip);
