ALTER TABLE `slider` ADD `ShowButton` TINYINT(1) NULL DEFAULT '1' AFTER `DateExpire`, ADD `TextButton` VARCHAR(200) NULL AFTER `ShowButton`;
ALTER TABLE `slider` ADD `ShowTitle` TINYINT(1) NULL DEFAULT '1' AFTER `URL`, ADD `ShowDescription` TINYINT(1) NULL DEFAULT '1' AFTER `ShowTitle`;
UPDATE modules_installed SET Version="3.0.0.2" WHERE Module="slider";