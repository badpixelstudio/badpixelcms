CREATE TABLE `business_timetable` ( `ID` BIGINT NULL AUTO_INCREMENT , `IDFather` BIGINT NULL DEFAULT '0' , `Day` INT(2) NULL DEFAULT '0' , `Hour1Open` TIME NULL DEFAULT '00:00:00' , `Hour1Close` TIME NULL DEFAULT '00:00:00' , `Hour2Open` TIME NULL DEFAULT '00:00:00' , `Hour2Close` TIME NULL DEFAULT '00:00:00' , PRIMARY KEY (`ID`)) ENGINE = MyISAM;
CREATE TABLE `business_holidays` ( `ID` BIGINT NULL AUTO_INCREMENT , `IDFather` BIGINT NULL DEFAULT '0' , `DateHoliday` DATE NULL , PRIMARY KEY (`ID`)) ENGINE = MyISAM;
ALTER TABLE `business` ADD INDEX(`IDTypeBusiness`);
ALTER TABLE `business` ADD INDEX(`IDState`);
ALTER TABLE `business` ADD INDEX(`IDCity`);
ALTER TABLE `business` ADD INDEX(`IDZone`);
ALTER TABLE `business` ADD INDEX(`Active`);
ALTER TABLE `business` ADD INDEX(`Drafted`);
ALTER TABLE `business_lnk_attributes_sets` ADD INDEX(`IDFather`);
ALTER TABLE `business_lnk_attributes_sets` ADD INDEX(`IDLink`);
ALTER TABLE `business` ADD `LastUpdated` DATETIME NULL AFTER `Drafted`, ADD `ImportedFrom` VARCHAR(50) NULL AFTER `LastUpdated`, ADD `ImportedFromID` BIGINT NULL DEFAULT '0' AFTER `ImportedFrom`;
UPDATE modules_installed SET Version="4.0.0.0" WHERE Module="business";