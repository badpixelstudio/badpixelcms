CREATE TABLE IF NOT EXISTS `api_apps` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Name` varchar(120) DEFAULT NULL,
  `Description` varchar(250) DEFAULT NULL,
  `Web` varchar(250) DEFAULT NULL,
  `CallbackURL` varchar(250) DEFAULT NULL,
  `Image` varchar(250) DEFAULT NULL,
  `PermitGuest` TINYINT( 1 ) NULL DEFAULT '1',
  `AllowOAuthSign` int(1) DEFAULT '0',
  `PermitGuest` int(1) DEFAULT '0',
  `OrganizationName` varchar(150) DEFAULT NULL,
  `OrganizationWeb` varchar(250) DEFAULT NULL,
  `Permissions` mediumtext,
  `ConsumerKey` varchar(250) DEFAULT NULL,
  `ConsumerToken` varchar(250) DEFAULT NULL,
  `Enabled` tinyint(1) DEFAULT '1',
  `IDUser` bigint(20) DEFAULT '0',
  `IDBusiness` bigint(20) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `ConsumerKey` (`ConsumerKey`),
  KEY `ConsumerToken` (`ConsumerToken`),
  KEY `IDUser` (`IDUser`),
  KEY `IDBusiness` (`IDBusiness`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
ALTER TABLE `api_apps` ADD INDEX ( `ConsumerKey` ) IF NOT EXISTS;
INSERT INTO `permalinks` (`ID`, `Permalink`, `TableName`, `TableID`, `ModuleName`, `Options`, `LastMod`, `Priority`, `ChangeFreq`, `IDBusiness`) VALUES 
(NULL, 'apps', 'apps', '0', 'apps', 'action=list', NULL, NULL, NULL, '0');