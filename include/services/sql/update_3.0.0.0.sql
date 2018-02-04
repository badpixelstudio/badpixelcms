ALTER TABLE `services` ADD `Image2` VARCHAR( 250 ) NULL DEFAULT '1' AFTER `Image`;
UPDATE modules_installed SET Version="3.0.0.1" WHERE Module="services";