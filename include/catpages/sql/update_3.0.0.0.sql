ALTER TABLE `cats` ADD `Orden` BIGINT NULL DEFAULT '0';
UPDATE modules_installed SET Version="3.0.0.1" WHERE Module="catpages";
UPDATE modules_installed SET Version="3.0.0.1" WHERE Module="cats";