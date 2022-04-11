
-- ------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- memoir implementation : ©  Timothée Pecatte <tim.pecatte@gmail.com>, Vincent Toper <vincent.toper@gmail.com>
--
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-- -----

-- dbmodel.sql

CREATE TABLE IF NOT EXISTS `global_variables` (
  `name` varchar(255) NOT NULL,
  `value` JSON,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `user_preferences` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `player_id` int(10) NOT NULL,
  `pref_id` int(10) NOT NULL,
  `pref_value` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cards` (
  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `card_location` varchar(32) NOT NULL,
  `card_state` int(10) DEFAULT 0,
  `type` int(10) NOT NULL,
  `value` int(10) NOT NULL,
  `extra_datas` JSON NULL,
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `terrains` (
  `tile_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tile_location` varchar(32) NOT NULL,
  `tile_state` int(10) DEFAULT 0,
  `type` VARCHAR(255) NOT NULL,
  `tile` VARCHAR(255) NOT NULL,
  `orientation` int(10) NOT NULL,
  `owner` VARCHAR(255) NULL,
  `extra_datas` JSON NULL,
  PRIMARY KEY (`tile_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `units` (
  `unit_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `unit_location` varchar(32) NOT NULL,
  `unit_state` int(10) DEFAULT 0,
  `x` int(10) NULL,
  `y` int(10) NULL,
  `type` VARCHAR(255) NOT NULL,
  `nation` VARCHAR(255) NOT NULL,
  `figures` int(10) NOT NULL,
  `badge` varchar(255),
  `activation_card` int(10) DEFAULT 0,
  `moves` int(1) DEFAULT 0,
  `fights` int(1) DEFAULT 0,
  `retreats` int(1) DEFAULT 0,
  `grounds` int(1) DEFAULT 0,
  `extra_datas` JSON NULL,
  PRIMARY KEY (`unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `teams` (
  `team` varchar(255) NOT NULL,
  `position` int(10) NOT NULL,
  `country` varchar(255) NOT NULL,
  `cards` int(10) NOT NULL,
  `victory` int(10) NOT NULL,
  `left_pId` int(10) NOT NULL,
  `central_pId` int(10) NOT NULL,
  `right_pId` int(10) NOT NULL,
  `commander_pId` int(10) NULL,
  PRIMARY KEY (`team`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `player` ADD `player_team` varchar(255) NOT NULL DEFAULT '';

CREATE TABLE IF NOT EXISTS `medals` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `team` varchar(32) NOT NULL,
  `type` int(10) DEFAULT 0,
  `foreign_id` int(10) DEFAULT 0,
  `sprite` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tokens` (
  `token_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `token_location` varchar(32) NOT NULL,
  `token_state` int(10) DEFAULT 0,
  `type` int(10) DEFAULT 0,
  `team` varchar(32) NULL,
  `x` int(10) NOT NULL,
  `y` int(10) NOT NULL,
  `sprite` varchar(32) NOT NULL,
  `datas` JSON NULL,
  PRIMARY KEY (`token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `move_id` int(10) NOT NULL,
  `table` varchar(32) NOT NULL,
  `primary` varchar(32) NOT NULL,
  `type` varchar(32) NOT NULL,
  `affected` JSON,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `gamelog` ADD `cancel` TINYINT(1) NOT NULL DEFAULT 0;
