TRUNCATE `dbs_admins`;
TRUNCATE `dbs_bans`;
TRUNCATE `dbs_dotagameplayers`;
TRUNCATE `dbs_dotagames`;
TRUNCATE `dbs_lastentries`;
TRUNCATE `dbs_lastupdates`;
TRUNCATE `dbs_normalgameplayers`;
TRUNCATE `dbs_normalgames`;
TRUNCATE `dbs_players`;

ALTER TABLE `dbs_players`
  DROP `ghostbotid`,
  DROP `botid`;
  
ALTER TABLE  `dbs_normalgames` CHANGE  `botid`  `botid` VARCHAR( 32 ) NOT NULL;

ALTER TABLE  `dbs_normalgameplayers` CHANGE  `botid`  `botid` VARCHAR( 32 ) NOT NULL;

ALTER TABLE  `dbs_lastentries` CHANGE  `botid`  `botid` VARCHAR( 32 ) NOT NULL;

ALTER TABLE  `dbs_dotagames` CHANGE  `botid`  `botid` VARCHAR( 32 ) NOT NULL;

ALTER TABLE  `dbs_dotagameplayers` CHANGE  `botid`  `botid` VARCHAR( 32 ) NOT NULL;

ALTER TABLE  `dbs_bans` CHANGE  `botid`  `botid` VARCHAR( 32 ) NOT NULL;

ALTER TABLE  `dbs_admins` CHANGE  `botid`  `botid` VARCHAR( 32 ) NOT NULL;