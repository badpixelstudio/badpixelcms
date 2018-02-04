CREATE TABLE IF NOT EXISTS `menu` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `IDBusiness` bigint(20) DEFAULT '0',
  `IDFather` bigint(20) DEFAULT '0',
  `Title` char(100) DEFAULT NULL,
  `Link` char(255) DEFAULT NULL,
  `Image` char(255) DEFAULT NULL,
  `Icon` char(255) DEFAULT NULL,
  `Orden` bigint(20) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `IDBusiness` (`IDBusiness`),
  KEY `IDFather` (`IDFather`),
  KEY `Orden` (`Orden`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
CREATE TABLE IF NOT EXISTS `menu_translations` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `IDOriginal` bigint(20) DEFAULT '0',
  `LangCode` varchar(5) DEFAULT NULL,
  `Title` char(100) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `IDOriginal` (`IDOriginal`),
  KEY `LangCode` (`LangCode`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;