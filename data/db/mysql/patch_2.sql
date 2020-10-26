CREATE TABLE IF NOT EXISTS `stats` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `service` varchar(25) NOT NULL,
    `type` varchar(50) NOT NULL,
    `data` text NOT NULL,
    `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx-service-type` (`service`,`type`),
    KEY `idx-timestamp` (`timestamp`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
