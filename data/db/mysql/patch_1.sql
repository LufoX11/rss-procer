ALTER TABLE `contact` ADD  `status` ENUM(  'deleted',  'unread',  'read' ) NOT NULL DEFAULT 'unread';
ALTER TABLE `contact` ADD INDEX  `idx-status` (  `status` );
