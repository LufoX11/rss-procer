INSERT INTO `admin_groups` (`id`, `name`, `title`) VALUES (3, 'client-level2', 'Cliente (Nivel 2)');
INSERT INTO `admin_groups_permissions` (`id`, `groups_id`, `permissions_id`) VALUES (3, 3, 3);
INSERT INTO `admin_groups_permissions` (`id`, `groups_id`, `permissions_id`) VALUES (4, 3, 1);
ALTER TABLE `admin_users` ADD `service` VARCHAR(25) NOT NULL AFTER `password`;
