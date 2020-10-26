SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

DROP DATABASE IF EXISTS `%DB_NAME%`;
CREATE DATABASE `%DB_NAME%`;
USE `%DB_NAME%`;

CREATE TABLE `channels` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `description` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `channels_id` smallint(5) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `summary` varchar(500) NOT NULL,
  `description` text NOT NULL,
  `datetime` datetime NOT NULL,
  `link` varchar(255) NOT NULL,
  `shortlink` varchar(50) NOT NULL,
  `image` varchar(255) NOT NULL,
  `checksum` varchar(40) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx-checksum` (`checksum`),
  KEY `idx-channels_id` (`channels_id`),
  KEY `idx-shortlink` (`shortlink`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `sorting` (
  `channels_id` smallint(5) unsigned NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`channels_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `deviceid` varchar(100) NOT NULL,
  `devicetype` char(2) NOT NULL,
  `deviceversion` varchar(10) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx-deviceid-devicetype` (`deviceid`,`devicetype`),
  KEY `idx-devicetype` (`devicetype`),
  KEY `idx-deviceid` (`deviceid`),
  KEY `idx-timestamp` (`timestamp`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `users_settings` (
  `users_id` mediumint(8) unsigned NOT NULL,
  `key` varchar(20) NOT NULL,
  `value` text NOT NULL,
  UNIQUE KEY `idx-users_id-key` (`users_id`,`key`),
  KEY `idx-users_id` (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `news`
  ADD CONSTRAINT `fk-news-channels_id` FOREIGN KEY (`channels_id`) REFERENCES `channels` (`id`);

ALTER TABLE `sorting`
  ADD CONSTRAINT `fk-sorting-channels_id` FOREIGN KEY (`channels_id`) REFERENCES `channels` (`id`);

ALTER TABLE `users_settings`
  ADD CONSTRAINT `fk-users_settings-users_id` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
