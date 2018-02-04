ALTER TABLE `users` ADD `UserDisallowed` TINYINT(1) NULL DEFAULT '0' AFTER `Active`;
UPDATE modules_installed SET Version="4.3.1801.0" WHERE Module="core";