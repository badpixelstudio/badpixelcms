CREATE TABLE IF NOT EXISTS `gallery` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDBusiness` bigint(20) DEFAULT '0',
  `MultiBusiness` int(1) DEFAULT '0',
  `Title` varchar(200) CHARACTER SET latin1 NOT NULL,
  `DatePublish` date NOT NULL,
  `Image` varchar(100) CHARACTER SET latin1 NOT NULL,
  `Description` mediumtext CHARACTER SET latin1 NOT NULL,
  `AutoGenThumb` int(1) NOT NULL DEFAULT '0',
  `ImageOptions` text,
  `IDAuthor` bigint(20) DEFAULT '0',
  `LastUpdate` datetime DEFAULT NULL,
  `Orden` bigint(20) DEFAULT '0',
  `Active` int(1) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `IDBusiness` (`IDBusiness`),
  KEY `Title` (`Title`),
  KEY `DatePublish` (`DatePublish`),
  KEY `Orden` (`Orden`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `gallery_images` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDFather` bigint(20) DEFAULT NULL,
  `IDBusiness` int(11) DEFAULT '0',
  `Image` varchar(250) DEFAULT NULL,
  `Description` varchar(250) DEFAULT NULL,
  `Level` int(3) NOT NULL DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0',
  `Link` varchar(250) DEFAULT NULL,
  `Download` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `IDFather` (`IDFather`),
  KEY `IDBusiness` (`IDBusiness`),
  KEY `Orden` (`Orden`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `gallery_translations` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDOriginal` bigint(20) DEFAULT '0',
  `LangCode` varchar(5) DEFAULT '',
  `Title` varchar(200) CHARACTER SET latin1 NOT NULL,
  `Description` mediumtext CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `IDOriginal` (`IDOriginal`),
  KEY `LangCode` (`LangCode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
INSERT INTO `permalinks` (`ID`, `Permalink`, `TableName`, `TableID`, `ModuleName`, `Options`, `LastMod`, `Priority`, `ChangeFreq`, `IDBusiness`) VALUES 
(NULL, 'galerias', 'gallery', '0', 'gallery', 'action=list', NULL, NULL, NULL, '0');