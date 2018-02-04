CREATE TABLE IF NOT EXISTS `contents` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDBusiness` bigint(20) DEFAULT '0',
  `Title` varchar(250) DEFAULT NULL,
  `Image` varchar(200) DEFAULT NULL,
  `Image2` varchar(200) DEFAULT NULL,
  `ShortDescription` mediumtext,
  `LongDescription` longtext,
  `Link` varchar(250) DEFAULT NULL,
  `Geolocation` varchar(200) DEFAULT NULL,
  `Orden` bigint(20) DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `contents_attachments` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDFather` bigint(20) DEFAULT NULL,
  `File` varchar(250) DEFAULT NULL,
  `Description` varchar(250) DEFAULT NULL,
  `CounterClick` int(11) DEFAULT NULL,
  `Level` int(3) NOT NULL DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `IDFather` (`IDFather`),
  KEY `Orden` (`Orden`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `contents_comments` (
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
  KEY `IDAuthor` (`IDAuthor`),
  KEY `Orden` (`Orden`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `contents_images` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDFather` bigint(20) DEFAULT NULL,
  `Image` varchar(250) DEFAULT NULL,
  `Description` varchar(250) DEFAULT NULL,
  `Level` int(3) NOT NULL DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0',
  `Link` varchar(250) DEFAULT NULL,
  `Download` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `IDFather` (`IDFather`),
  KEY `Orden` (`Orden`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `contents_links` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDFather` bigint(20) DEFAULT NULL,
  `Link` varchar(250) DEFAULT NULL,
  `Description` varchar(250) DEFAULT NULL,
  `CounterClick` int(11) DEFAULT NULL,
  `Level` int(3) DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `IDFather` (`IDFather`),
  KEY `Orden` (`Orden`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=FIXED AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `contents_translations` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDOriginal` bigint(20) DEFAULT NULL,
  `LangCode` varchar(5) DEFAULT NULL,
  `Title` varchar(250) DEFAULT NULL,
  `ShortDescription` mediumtext,
  `LongDescription` longtext,
  PRIMARY KEY (`ID`),
  KEY `IDOriginal` (`IDOriginal`),
  KEY `LangCode` (`LangCode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `contents_videos` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDFather` bigint(20) DEFAULT NULL,
  `Embed` mediumtext,
  `Description` varchar(250) DEFAULT NULL,
  `Level` int(3) DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `IDFather` (`IDFather`),
  KEY `Orden` (`Orden`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=FIXED AUTO_INCREMENT=1 ;
INSERT INTO permalinks (`ID`, `Permalink`, `TableName`, `TableID`, `ModuleName`, `Options`, `LastMod`, `Priority`, `ChangeFreq`, `IDBusiness`) VALUES (NULL, 'contenidos', NULL, '0', 'contents', 'action=list', NULL, NULL, NULL, '0');