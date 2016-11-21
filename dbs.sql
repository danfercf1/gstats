CREATE TABLE `dbs_admins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ghostid` int(10) unsigned NOT NULL,
  `ghostbotid` int(10) unsigned NOT NULL,
  `botid` varchar(32) NOT NULL,
  `name` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `server` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`,`server`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



CREATE TABLE `dbs_bans` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ghostid` int(10) unsigned NOT NULL,
  `ghostbotid` int(10) unsigned NOT NULL,
  `botid` varchar(32) NOT NULL,
  `name` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `server` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `date` int(15) unsigned NOT NULL,
  `gamename` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `admin` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `reason` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`,`server`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



CREATE TABLE `dbs_dotagameplayers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ghostid` int(10) unsigned NOT NULL,
  `ghostbotid` int(10) unsigned NOT NULL,
  `botid` varchar(32) NOT NULL,
  `player_id` int(10) unsigned NOT NULL,
  `gameid` int(10) unsigned NOT NULL,
  `name` varchar(15) CHARACTER SET latin1 NOT NULL,
  `spoofed` tinyint(1) NOT NULL,
  `reserved` tinyint(1) NOT NULL,
  `loadingtime` mediumint(6) NOT NULL,
  `left` mediumint(6) NOT NULL,
  `leftreason` varchar(100) CHARACTER SET latin1 NOT NULL,
  `team` tinyint(2) NOT NULL,
  `colour` tinyint(2) NOT NULL,
  `spoofedrealm` varchar(100) CHARACTER SET latin1 NOT NULL,
  `dota_id` int(10) unsigned NOT NULL,
  `dota_kills` smallint(4) NOT NULL,
  `dota_deaths` smallint(4) NOT NULL,
  `dota_creepkills` smallint(5) NOT NULL,
  `dota_creepdenies` smallint(5) NOT NULL,
  `dota_assists` smallint(4) NOT NULL,
  `dota_gold` mediumint(6) NOT NULL,
  `dota_neutralkills` smallint(5) NOT NULL,
  `dota_item1` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `dota_item2` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `dota_item3` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `dota_item4` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `dota_item5` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `dota_item6` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `dota_hero` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `dota_newcolour` tinyint(2) NOT NULL,
  `dota_towerkills` smallint(5) NOT NULL,
  `dota_raxkills` smallint(5) NOT NULL,
  `dota_courierkills` smallint(5) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `player_id` (`player_id`),
  KEY `name` (`name`,`spoofedrealm`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



CREATE TABLE `dbs_dotagames` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ghostid` int(10) unsigned NOT NULL,
  `ghostbotid` int(10) unsigned NOT NULL,
  `botid` varchar(32) NOT NULL,
  `map` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `datetime` int(15) NOT NULL,
  `gamename` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `duration` int(11) NOT NULL,
  `playersnum` tinyint(2) NOT NULL,
  `versus` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `dota_id` int(10) unsigned NOT NULL,
  `dota_winner` tinyint(1) NOT NULL,
  `dota_min` smallint(3) NOT NULL,
  `dota_sec` smallint(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



CREATE TABLE `dbs_lastentries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `botid` varchar(32) NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `entry` int(15) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



CREATE TABLE `dbs_lastupdates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `time` int(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



CREATE TABLE `dbs_normalgameplayers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ghostid` int(10) unsigned NOT NULL,
  `ghostbotid` int(10) unsigned NOT NULL,
  `botid` varchar(32) NOT NULL,
  `player_id` int(10) unsigned NOT NULL,
  `gameid` int(10) unsigned NOT NULL,
  `name` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `spoofed` tinyint(1) NOT NULL,
  `reserved` tinyint(1) NOT NULL,
  `loadingtime` mediumint(6) NOT NULL,
  `left` mediumint(6) NOT NULL,
  `leftreason` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `team` tinyint(2) NOT NULL,
  `colour` tinyint(2) NOT NULL,
  `spoofedrealm` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `player_id` (`player_id`),
  KEY `name` (`name`,`spoofedrealm`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



CREATE TABLE `dbs_normalgames` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ghostid` int(10) unsigned NOT NULL,
  `ghostbotid` int(10) unsigned NOT NULL,
  `botid` varchar(32) NOT NULL,
  `map` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `datetime` int(15) unsigned NOT NULL,
  `gamename` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `duration` int(11) unsigned NOT NULL,
  `playersnum` tinyint(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



CREATE TABLE `dbs_players` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `realm` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;