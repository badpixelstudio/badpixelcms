SELECT DATABASE() INTO @db_name FROM DUAL;
SELECT COUNT(*) INTO @exists FROM information_schema.tables WHERE table_schema = @db_name AND table_name = 'cats';
SET @query = If( @exists = 1, 'RENAME TABLE cats TO catpages','SELECT \'nothing to rename\' status');
PREPARE stmt FROM @query;
EXECUTE stmt;
SELECT COUNT(*) INTO @exists FROM information_schema.tables WHERE table_schema = @db_name AND table_name = 'cats_attachments';
SET @query = If( @exists = 1, 'RENAME TABLE cats_attachments TO catpages_pages_attachments','SELECT \'nothing to rename\' status');
PREPARE stmt FROM @query;
EXECUTE stmt;
SELECT COUNT(*) INTO @exists FROM information_schema.tables WHERE table_schema = @db_name AND table_name = 'cats_comments';
SET @query = If( @exists = 1, 'RENAME TABLE cats_comments TO catpages_pages_comments','SELECT \'nothing to rename\' status');
PREPARE stmt FROM @query;
EXECUTE stmt;
SELECT COUNT(*) INTO @exists FROM information_schema.tables WHERE table_schema = @db_name AND table_name = 'cats_images';
SET @query = If( @exists = 1, 'RENAME TABLE cats_images TO catpages_pages_images','SELECT \'nothing to rename\' status');
PREPARE stmt FROM @query;
EXECUTE stmt;
SELECT COUNT(*) INTO @exists FROM information_schema.tables WHERE table_schema = @db_name AND table_name = 'cats_links';
SET @query = If( @exists = 1, 'RENAME TABLE cats_links TO catpages_pages_links','SELECT \'nothing to rename\' status');
PREPARE stmt FROM @query;
EXECUTE stmt;
SELECT COUNT(*) INTO @exists FROM information_schema.tables WHERE table_schema = @db_name AND table_name = 'cats_pages';
SET @query = If( @exists = 1, 'RENAME TABLE cats_pages TO catpages_pages_pages','SELECT \'nothing to rename\' status');
PREPARE stmt FROM @query;
EXECUTE stmt;
SELECT COUNT(*) INTO @exists FROM information_schema.tables WHERE table_schema = @db_name AND table_name = 'cats_pages_translations';
SET @query = If( @exists = 1, 'RENAME TABLE cats_pages_translations TO catpages_pages_translations','SELECT \'nothing to rename\' status');
PREPARE stmt FROM @query;
EXECUTE stmt;
SELECT COUNT(*) INTO @exists FROM information_schema.tables WHERE table_schema = @db_name AND table_name = 'cats_translations';
SET @query = If( @exists = 1, 'RENAME TABLE cats_translations TO catpages_translations','SELECT \'nothing to rename\' status');
PREPARE stmt FROM @query;
EXECUTE stmt;
SELECT COUNT(*) INTO @exists FROM information_schema.tables WHERE table_schema = @db_name AND table_name = 'cats_related';
SET @query = If( @exists = 1, 'RENAME TABLE cats_related TO catpages_pages_related','SELECT \'nothing to rename\' status');
PREPARE stmt FROM @query;
EXECUTE stmt;
SELECT COUNT(*) INTO @exists FROM information_schema.tables WHERE table_schema = @db_name AND table_name = 'cats_videos';
SET @query = If( @exists = 1, 'RENAME TABLE cats_videos TO catpages_pages_videos','SELECT \'nothing to rename\' status');
PREPARE stmt FROM @query;
EXECUTE stmt;
CREATE TABLE IF NOT EXISTS `catpages` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDBusiness` bigint(20) DEFAULT '0',
  `MultiBusiness` int(1) DEFAULT '0',
  `IDFather` bigint(20) NOT NULL DEFAULT '0',
  `CategoryType` int(1) NOT NULL DEFAULT '0',
  `Title` varchar(200) NOT NULL,
  `Description` longtext NOT NULL,
  `Image` varchar(250) DEFAULT NULL,
  `ImageAlign` varchar(15) DEFAULT NULL,
  `AccessURL` varchar(250) NOT NULL,
  `IDAuthor` bigint(20) NOT NULL DEFAULT '0',
  `CatLevelCreateSub` int(11) DEFAULT '-1',
  `CatLevelAdmin` int(11) DEFAULT '-1',
  `PageLevelAccess` int(11) DEFAULT '-1',
  `PageLevelAdmin` int(11) NOT NULL DEFAULT '-1',
  `PageUsePreTitle` int(1) NOT NULL DEFAULT '-1',
  `PageUsePostTitle` int(1) NOT NULL DEFAULT '-1',
  `PageUseSummary` int(1) NOT NULL DEFAULT '-1',
  `PageUseFirstImage` varchar(10) NOT NULL DEFAULT 'aligned',
  `PageUseFirstImageAlign` int(1) DEFAULT '0',
  `PageFirstImageOptions` text,
  `PageFirstImageWidth` int(11) NOT NULL,
  `PageFirstImageHeight` int(11) NOT NULL,
  `PageFirstImageHoldSize` int(1) NOT NULL DEFAULT '0',
  `PageFirstImageTrimExcess` int(1) NOT NULL DEFAULT '0',
  `PageFirstImageThumbWidth` int(11) NOT NULL,
  `PageFirstImageThumbHeight` int(11) NOT NULL,
  `PageFirstImageThumbHoldSize` int(1) NOT NULL DEFAULT '0',
  `PageFirstImageThumbTrimExcess` int(1) NOT NULL DEFAULT '0',
  `PageUseDates` int(1) NOT NULL DEFAULT '-1',
  `PageUseAuthorInfo` int(1) NOT NULL DEFAULT '-1',
  `PageUseTags` int(1) NOT NULL DEFAULT '-1',
  `PageUseActivation` int(1) NOT NULL DEFAULT '-1',
  `PageUseSocial` int(1) NOT NULL DEFAULT '-1',
  `PageUseGeolocation` int(1) NOT NULL DEFAULT '-1',
  `PageUseImages` int(1) DEFAULT '0',
  `PageImagesOptions` text,
  `PageImagesWidth` int(11) NOT NULL,
  `PageImagesHeight` int(11) NOT NULL,
  `PageImagesHoldSize` int(1) NOT NULL DEFAULT '0',
  `PageImagesTrimExcess` int(1) NOT NULL DEFAULT '0',
  `PageImagesThumbWidth` int(11) NOT NULL,
  `PageImagesThumbHeight` int(11) NOT NULL,
  `PageImagesThumbHoldSize` int(1) NOT NULL DEFAULT '0',
  `PageImagesThumbTrimExcess` int(1) NOT NULL DEFAULT '0',
  `PageUseAttachments` int(1) DEFAULT '0',
  `PageUseLinks` int(1) DEFAULT '0',
  `PageUseVideos` int(1) DEFAULT '0',
  `PageUseComments` int(1) DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `IDBusiness` (`IDBusiness`),
  KEY `IDFather` (`IDFather`),
  KEY `IDAuthor` (`IDAuthor`),
  KEY `Orden` (`Orden`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `catpages_pages_attachments` (
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

CREATE TABLE IF NOT EXISTS `catpages_pages_comments` (
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

CREATE TABLE IF NOT EXISTS `catpages_pages_images` (
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

CREATE TABLE IF NOT EXISTS `catpages_pages_links` (
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

CREATE TABLE IF NOT EXISTS `catpages_pages` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDBusiness` bigint(20) DEFAULT '0',
  `IDFather` bigint(20) DEFAULT NULL,
  `PreTitle` varchar(250) DEFAULT NULL,
  `Title` varchar(250) DEFAULT NULL,
  `PostTitle` varchar(250) DEFAULT NULL,
  `Summary` text,
  `Page` mediumtext,
  `Image` varchar(200) DEFAULT NULL,
  `ImageAlign` varchar(15) DEFAULT NULL,
  `DatePublish` date DEFAULT NULL,
  `DateExpire` date DEFAULT NULL,
  `Active` tinyint(1) DEFAULT NULL,
  `IDAuthor` int(11) DEFAULT NULL,
  `Readings` bigint(20) DEFAULT NULL,
  `TotalReadings` bigint(20) DEFAULT NULL,
  `Orden` bigint(20) DEFAULT '0',
  `EnableComments` int(1) NOT NULL DEFAULT '-1',
  `Geolocation` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `IDBusiness` (`IDBusiness`),
  KEY `IDFather` (`IDFather`),
  KEY `Title` (`Title`),
  KEY `DatePublish` (`DatePublish`),
  KEY `DateExpire` (`DateExpire`),
  KEY `IDAuthor` (`IDAuthor`),
  KEY `Orden` (`Orden`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `catpages_pages_translations` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDOriginal` bigint(20) DEFAULT NULL,
  `LangCode` varchar(3) DEFAULT NULL,
  `PreTitle` varchar(250) DEFAULT NULL,
  `Title` varchar(250) DEFAULT NULL,
  `PostTitle` varchar(250) DEFAULT NULL,
  `Summary` text,
  `Page` text,
  `Field1` varchar(250) DEFAULT NULL,
  `Field2` varchar(250) DEFAULT NULL,
  `Field3` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `IDOriginal` (`IDOriginal`),
  KEY `LangCode` (`LangCode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `catpages_pages_related` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDFather` bigint(20) DEFAULT '0',
  `IDRelated` bigint(20) DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `IDFather` (`IDFather`),
  KEY `Orden` (`Orden`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=FIXED AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `catpages_pages_videos` (
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

CREATE TABLE IF NOT EXISTS `catpages_translations` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDOriginal` bigint(20) DEFAULT NULL,
  `LangCode` varchar(5) DEFAULT NULL,
  `PreTitle` varchar(250) DEFAULT NULL,
  `Title` varchar(250) DEFAULT NULL,
  `PostTitle` varchar(250) DEFAULT NULL,
  `Summary` text,
  `Page` text,
  PRIMARY KEY (`ID`),
  KEY `IDOriginal` (`IDOriginal`),
  KEY `LangCode` (`LangCode`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;