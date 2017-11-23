--
--
-- Title:         SQL Schema Game Install File
--
-- Author:        Pierre-Henry Soria <hello@ph7cms.com>
-- Copyright:     (c) 2017-2018, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
-- Package:       PH7 / Install / Data / Sql / PostgreSQL
--
--

CREATE SEQUENCE pH7_Games_seq;

CREATE TABLE IF NOT EXISTS pH7_Games (
  gameId int check (gameId > 0) NOT NULL DEFAULT NEXTVAL ('pH7_Games_seq'),
  name varchar(120) DEFAULT '',
  title varchar(120) NOT NULL,
  description varchar(255) NOT NULL,
  keywords varchar(255) DEFAULT '',
  thumb varchar(200) NOT NULL,
  file varchar(200) NOT NULL,
  categoryId smallint check (categoryId > 0) NOT NULL DEFAULT '0',
  addedDate timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  downloads int check (downloads > 0) DEFAULT '0',
  votes int check (votes > 0) DEFAULT '0',
  score double precision check (score > 0) DEFAULT '0',
  views int check (views > 0) DEFAULT '0',
  PRIMARY KEY (gameId)
)  ;

ALTER SEQUENCE pH7_Games_seq RESTART WITH 1;


CREATE SEQUENCE pH7_GamesCategories_seq;

CREATE TABLE IF NOT EXISTS pH7_GamesCategories (
  categoryId smallint check (categoryId > 0) NOT NULL DEFAULT NEXTVAL ('pH7_GamesCategories_seq'),
  name varchar(40) NOT NULL DEFAULT '',
  PRIMARY KEY (categoryId),
  UNIQUE (name)
)  ;

ALTER SEQUENCE pH7_GamesCategories_seq RESTART WITH 1;
