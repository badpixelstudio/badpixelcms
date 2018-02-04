ALTER TABLE `users` ADD `NotifyLoginEmail` TINYINT(1) NULL DEFAULT '1' AFTER `Active`;
UPDATE modules_installed SET Version="4.0.1612.0" WHERE Module="core";