-- Synthetic UNA 14.0.0 member fixture derived from the public MIT schema.
-- It contains no real user data and only the columns needed by the exporter.

CREATE DATABASE una_migration_fixture CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE una_migration_fixture;

CREATE TABLE sys_accounts (
  id int unsigned NOT NULL AUTO_INCREMENT,
  profile_id int unsigned NOT NULL,
  name varchar(255) NOT NULL,
  email varchar(255) NOT NULL,
  email_confirmed tinyint NOT NULL DEFAULT 0,
  active int NOT NULL DEFAULT 0,
  ip varchar(40) NOT NULL DEFAULT '',
  PRIMARY KEY (id)
);

CREATE TABLE sys_profiles (
  id int unsigned NOT NULL AUTO_INCREMENT,
  account_id int unsigned NOT NULL,
  type varchar(32) NOT NULL,
  content_id int unsigned NOT NULL,
  status enum('active', 'pending', 'suspended') NOT NULL DEFAULT 'active',
  PRIMARY KEY (id)
);

CREATE TABLE bx_persons_data (
  id int unsigned NOT NULL AUTO_INCREMENT,
  fullname varchar(255) NOT NULL,
  last_name varchar(255) NOT NULL,
  description text NOT NULL,
  gender varchar(255) DEFAULT NULL,
  birthday date DEFAULT NULL,
  PRIMARY KEY (id)
);

INSERT INTO bx_persons_data (id, fullname, last_name, description, gender, birthday) VALUES
  (101, 'Alex Morgan', 'Morgan', 'Travelling between Wellington and Lisbon.', 'male', '1990-04-12'),
  (102, 'Taylor Chen', 'Chen', 'A profile with a comma, quote " and a new\nline.', 'female', '1988-09-03'),
  (103, 'Invalid Email', 'Email', 'This row must be skipped.', 'unknown', NULL);

INSERT INTO sys_profiles (id, account_id, type, content_id, status) VALUES
  (201, 1, 'bx_person', 101, 'active'),
  (202, 2, 'bx_person', 102, 'pending'),
  (203, 3, 'bx_person', 103, 'active');

INSERT INTO sys_accounts (id, profile_id, name, email, email_confirmed, active, ip) VALUES
  (1, 201, 'alex-morgan', 'alex@example.test', 1, 1, '2001:db8::1'),
  (2, 202, 'taylor-chen', 'taylor@example.test', 1, 1, '192.0.2.20'),
  (3, 203, 'invalid-email', 'not-an-email', 1, 1, '192.0.2.30');
