ALTER TABLE `business` ADD `Wifi` TINYINT(1) NULL DEFAULT '0' AFTER `TimeTable`, ADD `AccessHandicapped` TINYINT(1) NULL DEFAULT '0' AFTER `Wifi`, ADD `AdmitCreditCard` TINYINT(1) NULL DEFAULT '0' AFTER `AccessHandicapped`, ADD `PriceMedium` VARCHAR(30) NULL AFTER `AdmitCreditCard`, ADD `LinkReserve` VARCHAR(250) NULL AFTER `PriceMedium`, ADD `LinkBuy` VARCHAR(250) NULL AFTER `LinkReserve`;
UPDATE modules_installed SET Version="3.0.0.1" WHERE Module="business";