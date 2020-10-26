ALTER TABLE `admin_users_groups`
  ADD CONSTRAINT `fk-admin_users_groups-users_id` FOREIGN KEY (`users_id`) REFERENCES `admin_users` (`id`),
  ADD CONSTRAINT `fk-admin_users_groups-groups_id` FOREIGN KEY (`groups_id`) REFERENCES `admin_groups` (`id`);

ALTER TABLE `admin_groups_permissions`
  ADD CONSTRAINT `fk-admin_groups_permissions-groups_id` FOREIGN KEY (`groups_id`) REFERENCES `admin_groups` (`id`),
  ADD CONSTRAINT `fk-admin_groups_permissions-permissions_id` FOREIGN KEY (`permissions_id`) REFERENCES `admin_permissions` (`id`);
