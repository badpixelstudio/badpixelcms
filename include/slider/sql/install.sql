CREATE TABLE IF NOT EXISTS `slider` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDBusiness` bigint(20) DEFAULT '0',
  `Name` varchar(100) DEFAULT NULL,
  `Description` mediumtext,
  `Image` varchar(100) DEFAULT NULL,
  `DatePublish` date DEFAULT NULL,
  `DateExpire` date DEFAULT NULL,
  `ShowButton` TINYINT(1) NULL DEFAULT '1',
  `TextButton` VARCHAR(200) NULL,
  `URL` varchar(250) DEFAULT NULL,
  `ShowTitle` TINYINT(1) NULL DEFAULT '1',
  `ShowDescription` TINYINT(1) NULL DEFAULT '1',
  `Orden` bigint(20) DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;