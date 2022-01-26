
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
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `side` int(10) NOT NULL, --AXIS / ALLIES
  `country` int(10) NOT NULL,
  `cards` int(10) NOT NULL, -- NBR OF CARDS IN HAND
  `medals` int(10) NOT NULL, -- NBR OF MEDALS WON
  `victory` int(10) NOT NULL, -- NBR OF MEDALS NEEDED TO WIN
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
  `orientation` int(10) NOT NULL,
  `extra_datas` JSON NULL,
  PRIMARY KEY (`tile_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
