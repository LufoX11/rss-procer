RENAME TABLE  `users` TO  `admin_users` ;

CREATE TABLE IF NOT EXISTS `admin_groups` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `title` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx-name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `admin_groups` (`id`, `name`, `title`) VALUES
(1, 'admin', 'Administrador'),
(2, 'client-level1', 'Cliente (Nivel 1)');

CREATE TABLE IF NOT EXISTS `admin_permissions` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `title` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx-name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `admin_permissions` (`id`, `name`, `title`) VALUES
(1, 'home', 'Leer Novedades'),
(2, 'app-messages', 'Acceder Mensajes de la aplicación'),
(3, 'client-stats', 'Leer Estadísticas del cliente'),
(4, 'services', 'Acceder Servicios');

CREATE TABLE IF NOT EXISTS `admin_users_groups` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `users_id` smallint(5) unsigned NOT NULL,
  `groups_id` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx-users_id-groups_id` (`users_id`,`groups_id`),
  KEY `idx-groups_id` (`groups_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `admin_users_groups` (`id`, `users_id`, `groups_id`) VALUES
(1, 1, 1);

CREATE TABLE IF NOT EXISTS `admin_groups_permissions` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `groups_id` tinyint(3) unsigned NOT NULL,
  `permissions_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx-groups_id-permissions_id` (`groups_id`,`permissions_id`),
  KEY `idx-permissions_id` (`permissions_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `admin_groups_permissions` (`id`, `groups_id`, `permissions_id`) VALUES
(1, 2, 1),
(2, 2, 3);
