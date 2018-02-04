ALTER TABLE `cats_pages` CHANGE `DatePublish` `DatePublish` DATE NULL DEFAULT NULL ,
CHANGE `DateExpire` `DateExpire` DATE NULL DEFAULT NULL ;
ALTER TABLE `cats_pages` CHANGE `IDCategory` `IDFather` BIGINT( 20 ) NULL DEFAULT NULL ;
ALTER TABLE `cats_pages` ADD `TotalReadings` BIGINT NULL DEFAULT '0' AFTER `Readings`;
UPDATE modules_installed SET Version="3.0.0.2" WHERE Module="catpages";
UPDATE modules_installed SET Version="3.0.0.2" WHERE Module="cats";