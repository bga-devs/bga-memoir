
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

CREATE TABLE IF NOT EXISTS `teams` (
  `side` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `cards` int(10) NOT NULL,
  `medals` int(10) NOT NULL,
  `victory` int(10) NOT NULL,
  PRIMARY KEY (`side`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `player` ADD `team_side` varchar(255) NOT NULL DEFAULT '';


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
  `orientation` int(10) NOT NULL,
  `extra_datas` JSON NULL,
  PRIMARY KEY (`tile_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `troops` (
  `troop_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `troop_location` varchar(32) NOT NULL,
  `troop_state` int(10) DEFAULT 0,
  `x` int(10) NULL,
  `y` int(10) NULL,
  `type` VARCHAR(255) NOT NULL,
  `nation` VARCHAR(255) NOT NULL,
  `figures` int(10) NOT NULL,
  `badge` varchar(255),
  `activation_card` int(10) DEFAULT 0,
  `moves` int(1) DEFAULT 0,
  `fights` int(1) DEFAULT 0,
  `extra_datas` JSON NULL,
  PRIMARY KEY (`troop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
