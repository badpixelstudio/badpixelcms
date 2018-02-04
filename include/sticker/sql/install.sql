CREATE TABLE IF NOT EXISTS `sticker` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Name` varchar(250) DEFAULT NULL,
  `Description` mediumtext,
  `Type` varchar(20) DEFAULT NULL,
  `Active` int(1) DEFAULT '1',
  `DatePublish` date DEFAULT NULL,
  `DateExpire` date DEFAULT NULL,
  `URL` varchar(250) DEFAULT NULL,
  `Orden` bigint(20) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `DatePublish` (`DatePublish`),
  KEY `DateExpire` (`DateExpire`),
  KEY `Orden` (`Orden`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `sticker_translations` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDOriginal` bigint(20) NOT NULL,
  `LangCode` varchar(5) DEFAULT NULL,
  `Name` varchar(250) DEFAULT NULL,
  `Description` mediumtext,
  PRIMARY KEY (`ID`),
  KEY `IDOriginal` (`IDOriginal`),
  KEY `LangCode` (`LangCode`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;