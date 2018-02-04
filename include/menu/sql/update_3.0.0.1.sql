ALTER TABLE `menu` ADD `Icon` VARCHAR( 255 ) NULL AFTER `Image`;
UPDATE modules_installed SET Version="3.0.0.1" WHERE Module="menu";