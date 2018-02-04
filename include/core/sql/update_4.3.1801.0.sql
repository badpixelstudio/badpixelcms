ALTER TABLE `users` ADD `StreetNum` VARCHAR(30) NULL AFTER `Street`, ADD `StreetOtherData` VARCHAR(80) NULL AFTER `StreetNum`;
UPDATE modules_installed SET Version="4.3.1802.0" WHERE Module="core";