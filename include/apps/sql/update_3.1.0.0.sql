ALTER TABLE `api_apps` ADD `PermitGuest` TINYINT( 1 ) NULL DEFAULT '1' AFTER `Image` ;
UPDATE modules_installed SET Version="3.1.0.1" WHERE Module="apps";