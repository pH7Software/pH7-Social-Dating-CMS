--
-- Author:        Pierre-Henry Soria <hello@ph7builder.com>
-- Copyright:     (c) 2026, Pierre-Henry Soria. All Rights Reserved.
-- License:       MIT License
--

-- Store wall posts with the same full-Unicode character set as their table.
ALTER TABLE ph7_members_wall MODIFY post TEXT CHARACTER SET utf8mb4;

-- Keep error logs transactional and compatible with consistent InnoDB backups.
ALTER TABLE ph7_log_error ENGINE=InnoDB;

-- Persist PayPal checkout context so verified asynchronous notifications do not depend on browser cookies.
CREATE TABLE IF NOT EXISTS ph7_payment_transactions (
  payment_transaction_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  checkout_reference_hash char(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  provider varchar(20) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  provider_transaction_id varchar(127) CHARACTER SET ascii COLLATE ascii_bin DEFAULT NULL,
  profile_id int(10) unsigned NOT NULL,
  membership_id tinyint(3) unsigned NOT NULL,
  membership_amount decimal(12,2) unsigned NOT NULL,
  expected_amount decimal(12,2) unsigned NOT NULL,
  expected_currency char(3) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  merchant_account varchar(190) NOT NULL,
  sandbox tinyint(1) unsigned NOT NULL DEFAULT 1,
  status enum('pending','completed') CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT 'pending',
  created_at datetime NOT NULL,
  completed_at datetime DEFAULT NULL,
  PRIMARY KEY (payment_transaction_id),
  UNIQUE KEY payment_checkout_reference (checkout_reference_hash),
  UNIQUE KEY payment_provider_transaction (provider, provider_transaction_id),
  KEY payment_profile_status (profile_id, status),
  KEY payment_status_created (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Update pH7Builder's SQL schema version.
UPDATE ph7_modules SET version = '1.6.6' WHERE vendorName = 'pH7Builder';
