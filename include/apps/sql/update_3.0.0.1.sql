ALTER TABLE `api_apps` ADD `PermitGuest` TINYINT(1) NULL DEFAULT '0' AFTER `AllowOAuthSign` ;
UPDATE modules_installed SET Version="3.1.0.0" WHERE Module="apps";