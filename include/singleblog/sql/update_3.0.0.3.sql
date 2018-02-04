ALTER TABLE `singleblog` ADD `Active` TINYINT(1) NULL DEFAULT '1' AFTER `DatePublish`;
UPDATE modules_installed SET Version="3.0.0.4" WHERE Module="singleblog";