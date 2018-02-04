ALTER TABLE `services` ADD `Image2` VARCHAR( 250 ) NULL DEFAULT '' AFTER `Image`;
ALTER TABLE `services` ADD `Icon` VARCHAR( 250 ) NULL DEFAULT '' AFTER `Image2`;
UPDATE modules_installed SET Version="3.0.0.2" WHERE Module="services";