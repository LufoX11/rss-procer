SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS rssprocer;
USE rssprocer;

CREATE TABLE IF NOT EXISTS `users` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `username` varchar(25) NOT NULL,
  `password` varchar(40) NOT NULL,
  `status` enum('enabled','disabled') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx-username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `users` (`id`, `name`, `username`, `password`, `status`) VALUES
(1, 'Luciano G. Fantuzzi', 'luciano.fantuzzi', 'f402c69afca70272648045646801a2ae67c99986', 'enabled');

CREATE TABLE IF NOT EXISTS `contact` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `devicedata` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx-timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
