ALTER TABLE `singleblog` ADD `DatePublish` DATE NULL AFTER `Geolocation` ;
ALTER TABLE `singleglog` ADD `Image2` VARCHAR( 200 ) NULL AFTER `Image` ;
UPDATE modules_installed SET Version="3.0.0.1" WHERE Module="singleblog";