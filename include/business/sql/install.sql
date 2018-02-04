CREATE TABLE IF NOT EXISTS `business` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDTypeBusiness` bigint(20) DEFAULT '0',
  `Package` varchar(20) DEFAULT 'basic',
  `Name` varchar(150) DEFAULT NULL,
  `Street` varchar(100) DEFAULT NULL,
  `IDState` bigint(20) DEFAULT NULL,
  `IDCity` bigint(20) DEFAULT NULL,
  `IDZone` bigint(20) DEFAULT NULL,
  `State` varchar(50) DEFAULT NULL,
  `City` varchar(50) DEFAULT NULL,
  `Zone` varchar(50) DEFAULT NULL,
  `ZipCode` varchar(5) DEFAULT NULL,
  `Phone` varchar(30) DEFAULT NULL,
  `Fax` varchar(30) DEFAULT NULL,
  `Email` varchar(150) DEFAULT NULL,
  `Web` varchar(150) DEFAULT NULL,
  `Facebook` varchar(200) DEFAULT NULL,
  `Twitter` varchar(100) DEFAULT NULL,
  `Geolocation` varchar(250) DEFAULT NULL,
  `Image` varchar(250) DEFAULT NULL,
  `Logo` varchar(200) DEFAULT NULL,
  `Slogan` varchar(200) DEFAULT NULL,
  `Description` mediumtext,
  `TimeTable` varchar(150) DEFAULT NULL,
  `BillingCIF` varchar(12) DEFAULT NULL,
  `BillingName` varchar(100) DEFAULT NULL,
  `BillingStreet` varchar(100) DEFAULT NULL,
  `BillingState` varchar(50) DEFAULT NULL,
  `BillingCity` varchar(50) DEFAULT NULL,
  `BillingZipCode` varchar(5) DEFAULT NULL,
  `BillingPhone` varchar(15) DEFAULT NULL,
  `BillingFax` varchar(15) DEFAULT NULL,
  `BillingEmail` varchar(150) DEFAULT NULL,
  `BillingIBAN` varchar(30) DEFAULT NULL,
  `CloudFiles` varchar(250) DEFAULT NULL,
  `Active` int(1) DEFAULT '0',
  `Drafted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `Name` (`Name`),
  KEY `IDState` (`IDState`),
  KEY `IDCity` (`IDCity`),
  KEY `IDZone` (`IDZone`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `business_attachments` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDFather` bigint(20) DEFAULT NULL,
  `File` varchar(250) DEFAULT NULL,
  `Description` varchar(250) DEFAULT NULL,
  `CounterClick` int(11) DEFAULT NULL,
  `Level` int(3) NOT NULL DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `IDFather` (`IDFather`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `business_attributes` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDGroup` bigint(20) DEFAULT '0',
  `Title` varchar(150) DEFAULT NULL,
  `AttributeType` varchar(50) DEFAULT NULL,
  `Required` tinyint(1) DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `IDGroup` (`IDGroup`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `business_attributes_groups` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDFather` bigint(20) DEFAULT '0',
  `Title` varchar(150) DEFAULT NULL,
  `Orden` bigint(20) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `IDFather` (`IDFather`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `business_attributes_options` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDAttribute` bigint(20) DEFAULT '0',
  `Title` varchar(150) DEFAULT NULL,
  `Value` varchar(150) DEFAULT NULL,
  `Orden` bigint(20) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `IDAttribute` (`IDAttribute`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `business_attributes_sets` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDBusiness` bigint(20) DEFAULT '0',
  `Title` varchar(150) DEFAULT NULL,
  `Orden` bigint(20) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `IDBusiness` (`IDBusiness`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `business_attributes_values` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDFather` bigint(20) DEFAULT '0',
  `IDAttribute` bigint(20) DEFAULT '0',
  `Value` longtext,
  PRIMARY KEY (`ID`),
  KEY `IDFather` (`IDFather`),
  KEY `IDAttribute` (`IDAttribute`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `business_comments` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDFather` bigint(20) DEFAULT NULL,
  `IDAuthor` int(11) DEFAULT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `Email` varchar(150) DEFAULT NULL,
  `Comment` mediumtext,
  `DatePublish` datetime DEFAULT NULL,
  `Points` int(1) NOT NULL DEFAULT '3',
  `Orden` bigint(20) DEFAULT '0',
  `Active` int(1) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `IDFather` (`IDFather`),
  KEY `IDAuthor` (`IDAuthor`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `business_images` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDFather` bigint(20) DEFAULT NULL,
  `Image` varchar(250) DEFAULT NULL,
  `Description` varchar(250) DEFAULT NULL,
  `Level` int(3) NOT NULL DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0',
  `Link` varchar(250) DEFAULT NULL,
  `Download` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `IDFather` (`IDFather`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `business_links` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDFather` bigint(20) DEFAULT NULL,
  `Link` varchar(250) DEFAULT NULL,
  `Description` varchar(250) DEFAULT NULL,
  `CounterClick` int(11) DEFAULT NULL,
  `Level` int(3) NOT NULL DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `IDFather` (`IDFather`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `business_lnk_attributes_sets` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDFather` bigint(20) DEFAULT '0',
  `IDLink` bigint(20) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `IDFather` (`IDFather`),
  KEY `IDLink` (`IDLink`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `business_mailing` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Subject` varchar(200) DEFAULT NULL,
  `Body` longtext,
  `DatePublish` date DEFAULT NULL,
  `Sended` int(1) DEFAULT '0',
  `DateSend` date DEFAULT NULL,
  `IncludeBusinessTypes` varchar(250) DEFAULT NULL,
  `IncludeWithName` varchar(250) DEFAULT NULL,
  `IncludeInIDState` int(11) DEFAULT '0',
  `IncludeInState` varchar(150) DEFAULT NULL,
  `IncludeInIDCity` int(11) DEFAULT '0',
  `IncludeInCity` varchar(150) DEFAULT NULL,
  `IncludeInIDZone` int(11) DEFAULT '0',
  `IncludeInZone` varchar(150) DEFAULT NULL,
  `IncludeWithPhone` varchar(1) DEFAULT NULL,
  `IncludeWithFacebook` varchar(1) DEFAULT NULL,
  `IncludeWithTwitter` varchar(1) DEFAULT NULL,
  `IncludeWithGoogleP` varchar(1) DEFAULT NULL,
  `IncludeActive` varchar(1) DEFAULT NULL,
  `IncludeDrafted` varchar(1) DEFAULT NULL,
  `IncludePublicEmail` int(1) DEFAULT '1',
  `IncludeBillingEmail` int(1) DEFAULT '1',
  `IncludeAdminsEmails` int(1) DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
CREATE TABLE IF NOT EXISTS `business_mailing_mails` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDMailing` bigint(20) DEFAULT '0',
  `Email` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `IDMailing` (`IDMailing`),
  KEY `Email` (`Email`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
CREATE TABLE IF NOT EXISTS `business_modules` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `IDBusiness` int(11) DEFAULT '0',
  `OptionFile` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `IDBusiness` (`IDBusiness`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `business_users` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDBusiness` bigint(20) DEFAULT '0',
  `IDUser` bigint(20) DEFAULT '0',
  `Rol` int(11) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `IDBusiness` (`IDBusiness`),
  KEY `IDUser` (`IDUSer`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `business_videos` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDFather` bigint(20) DEFAULT NULL,
  `Embed` mediumtext,
  `Description` varchar(250) DEFAULT NULL,
  `Level` int(3) DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `IDFather` (`IDFather`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=FIXED AUTO_INCREMENT=1 ;
ALTER TABLE `business` ADD INDEX(`IDTypeBusiness`);
ALTER TABLE `business` ADD INDEX(`IDState`);
ALTER TABLE `business` ADD INDEX(`IDCity`);
ALTER TABLE `business` ADD INDEX(`IDZone`);
ALTER TABLE `business` ADD INDEX(`Active`);
ALTER TABLE `business` ADD INDEX(`Drafted`);
ALTER TABLE `business_lnk_attributes_sets` ADD INDEX(`IDFather`);
ALTER TABLE `business_lnk_attributes_sets` ADD INDEX(`IDLink`);
CREATE TABLE `business_timetable` ( `ID` BIGINT NULL AUTO_INCREMENT , `IDFather` BIGINT NULL DEFAULT '0' , `Day` INT(2) NULL DEFAULT '0' , `Hour1Open` TIME NULL DEFAULT '00:00:00' , `Hour1Close` TIME NULL DEFAULT '00:00:00' , `Hour2Open` TIME NULL DEFAULT '00:00:00' , `Hour2Close` TIME NULL DEFAULT '00:00:00' , PRIMARY KEY (`ID`)) ENGINE = MyISAM;
CREATE TABLE `business_holidays` ( `ID` BIGINT NULL AUTO_INCREMENT , `IDFather` BIGINT NULL DEFAULT '0' , `DateHoliday` DATE NULL , PRIMARY KEY (`ID`)) ENGINE = MyISAM;
INSERT INTO `permalinks` (`ID`, `Permalink`, `TableName`, `TableID`, `ModuleName`, `Options`, `LastMod`, `Priority`, `ChangeFreq`, `IDBusiness`) VALUES 
(NULL, 'business', 'business', '0', 'empresas', 'action=list', NULL, NULL, NULL, '0');