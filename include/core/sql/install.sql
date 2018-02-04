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

CREATE TABLE IF NOT EXISTS `aux_cities` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDFather` int(11) DEFAULT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `CP` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `IDFather` (`IDFather`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `aux_countries` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(40) DEFAULT NULL,
  `Code` varchar(4) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Code` (`Code`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `aux_states` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `IDFather` bigint(20) DEFAULT '1',
  `Name` char(30) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `IDFather` (`IDFather`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `aux_tags` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Tag` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Tag` (`Tag`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `aux_zones` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `IDFather` int(11) DEFAULT NULL,
  `Name` varchar(40) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `IDFather` (`IDFather`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(5) DEFAULT NULL,
  `language` varchar(40) NOT NULL,
  `fileconfig` varchar(200) NOT NULL,
  `flag` varchar(200) NOT NULL DEFAULT '',
  `factorysetting` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `code` (`code`),
  KEY `language` (`language`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `likethis` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDUser` varchar(60) DEFAULT '0',
  `TableName` varchar(50) DEFAULT NULL,
  `TableID` bigint(20) DEFAULT '0',
  `ModuleName` varchar(30) DEFAULT NULL,
  `Options` varchar(100) DEFAULT NULL,
  `Vote` varchar(1) DEFAULT '+',
  PRIMARY KEY (`ID`),
  KEY `IDUser` (`IDUser`),
  KEY `TableName` (`TableName`),
  KEY `TableID` (`TableID`),
  KEY `ModuleName` (`ModuleName`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `lnk_tags` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `TableFather` varchar(60) DEFAULT NULL,
  `IDFather` bigint(20) DEFAULT '0',
  `IDLink` bigint(20) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `TableFather` (`TableFather`),
  KEY `IDFather` (`IDFather`),
  KEY `IDLink` (`IDLink`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `modules_config` (
  `ID` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `UserID` int(20) DEFAULT NULL,
  `Module` varchar(50) DEFAULT NULL,
  `ParamName` varchar(50) DEFAULT NULL,
  `ParamValue` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `userID` (`userID`),
  KEY `Module` (`Module`),
  KEY `ParamName` (`ParamName`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE `modules_adminmenu` ( 
  `ID` BIGINT NULL AUTO_INCREMENT, 
  `Block` VARCHAR(50) NULL, 
  `Title` VARCHAR(100) NULL, 
  `Icon` VARCHAR(50) NULL, 
  `Conditions` MEDIUMTEXT NULL, 
  `Orden` BIGINT NULL DEFAULT '0', 
PRIMARY KEY (`ID`)) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `modules_installed` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Module` varchar(150) DEFAULT NULL,
  `BlockMenu` varchar(50) DEFAULT NULL,
  `ModuleName` varchar(150) DEFAULT NULL,
  `Version` varchar(50) DEFAULT NULL,
  `Orden` BIGINT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `Module` (`Module`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `oauth_accesstokens` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDApp` bigint(20) DEFAULT '0',
  `IDUser` bigint(20) DEFAULT '0',
  `AccessToken` varchar(250) DEFAULT NULL,
  `Expires` varchar(30) DEFAULT NULL,
  `LongLife` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `IDApp` (`IDApp`),
  KEY `IDUser` (`IDUser`),
  KEY `AccessToken` (`AccessToken`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `oauth_logintokens` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDApp` bigint(20) DEFAULT '0',
  `LoginToken` varchar(250) DEFAULT NULL,
  `Expires` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `IDApp` (`IDApp`),
  KEY `LoginToken` (`LoginToken`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `permalinks` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Permalink` varchar(200) DEFAULT NULL,
  `TableName` varchar(50) DEFAULT NULL,
  `TableID` bigint(20) DEFAULT '0',
  `ModuleName` varchar(30) DEFAULT NULL,
  `Options` varchar(100) DEFAULT NULL,
  `LastMod` date DEFAULT NULL,
  `Priority` varchar(3) DEFAULT NULL,
  `ChangeFreq` varchar(15) DEFAULT NULL,
  `IDBusiness` int(11) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `Permalink` (`Permalink`),
  KEY `TableName` (`TableName`),
  KEY `TableID` (`TableID`),
  KEY `ModuleName` (`ModuleName`),
  KEY `IDBusiness` (`IDBusiness`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `socialmedia_publish` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDBusiness` bigint(20) DEFAULT '0',
  `DatePublish` date DEFAULT NULL,
  `HourPublish` varchar(5) DEFAULT '99:99',
  `PublishTwitter` int(1) DEFAULT '1',
  `Twitter` varchar(250) DEFAULT NULL,
  `PublishFacebook` int(1) DEFAULT '1',
  `FBUrl` varchar(250) DEFAULT NULL,
  `FBTitle` varchar(250) DEFAULT NULL,
  `FBDescription` mediumtext,
  `FBImage` varchar(250) DEFAULT NULL,
  `PublishGPlus` tinyint(1) DEFAULT '1',
  `GPlusUrl` varchar(250) DEFAULT NULL,
  `GPlusTitle` varchar(250) DEFAULT NULL,
  `GPlusDescription` mediumtext,
  `GPlusImage` varchar(250) DEFAULT NULL,
  `Orden` bigint(20) DEFAULT '0',
  `Published` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `IDBusiness` (`IDBusiness`),
  KEY `DatePublish` (`DatePublish`),
  KEY `HourPublish` (`HourPublish`),
  KEY `Orden` (`Orden`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `users` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `RegCode` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `UserName` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `Name` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `NIF` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `Street` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `ZipCode` varchar(83) CHARACTER SET utf8 DEFAULT NULL,
  `City` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `State` varchar(83) CHARACTER SET utf8 DEFAULT NULL,
  `Country` varchar(83) CHARACTER SET utf8 DEFAULT NULL,
  `Telephone` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `Fax` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `Email` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `Web` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `EmailPublic` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `BirthDate` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `DateInscribe` datetime DEFAULT NULL,
  `PassW` varchar(80) CHARACTER SET utf8 DEFAULT NULL,
  `Rol` int(11) DEFAULT NULL,
  `LastLogin` datetime DEFAULT NULL,
  `LastIP` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `Active` tinyint(1) DEFAULT NULL,
  `UserDisallowed` TINYINT(1) NULL DEFAULT '0',
  `NotifyLoginEmail` TINYINT(1) NULL DEFAULT '1',
  `CountPages` int(11) DEFAULT '0',
  `CountPost` int(11) DEFAULT '0',
  `CountComments` int(11) DEFAULT '0',
  `Language` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `Image` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `Signature` mediumtext CHARACTER SET utf8,
  `ProfilePublic` tinyint(1) DEFAULT NULL,
  `Template` varchar(40) CHARACTER SET utf8 DEFAULT NULL,
  `TokenPassword` varchar(40) CHARACTER SET utf8 DEFAULT NULL,
  `fb_uid` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `fb_link` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `fb_gender` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `fb_updateenable` int(1) DEFAULT '1',
  `fb_feedenable` int(1) DEFAULT '1',
  `twitter_uid` varchar(50) DEFAULT NULL,
  `twitter_link` varchar(200) DEFAULT NULL,
  `twitter_access_token` varchar(200) DEFAULT NULL,
  `twitter_access_token_secret` varchar(200) DEFAULT NULL,
  `InvoiceNIF` varchar(30) DEFAULT NULL,
  `InvoiceName` varchar(200) DEFAULT NULL,
  `InvoiceStreet` varchar(200) DEFAULT NULL,
  `InvoiceZipCode` varchar(20) DEFAULT NULL,
  `InvoiceCity` varchar(150) DEFAULT NULL,
  `InvoiceState` varchar(150) DEFAULT NULL,
  `InvoiceCountry` varchar(150) DEFAULT NULL,
  `InvoicePhone` varchar(50) DEFAULT NULL,
  `InvoiceEmail` varchar(200) DEFAULT NULL,
  `InvoiceBankName` varchar(100) DEFAULT NULL,
  `InvoiceBankSwiftCode` varchar(30) DEFAULT NULL,
  `InvoiceBankAccount` varchar(50) DEFAULT NULL,
  `InvoiceBankOwner` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `RegCode` (`RegCode`),
  KEY `UserName` (`UserName`),
  KEY `PassW` (`PassW`),
  KEY `TokenPassword` (`TokenPassword`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;
CREATE TABLE IF NOT EXISTS `users_favorites` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDUser` bigint(20) DEFAULT '0',
  `BlockName` varchar(100) DEFAULT NULL,
  `Orden` bigint(20) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `IDUser` (`IDUser`),
  KEY `Orden` (`Orden`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `users_modules` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `OptionFile` varchar(30) DEFAULT NULL,
  `OptionName` varchar(120) DEFAULT NULL,
  `OptionHelp` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `OptionFile` (`OptionFile`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `users_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `OptionFile` varchar(30) DEFAULT NULL,
  `OptionName` varchar(120) DEFAULT NULL,
  `OptionHelp` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `OptionFile` (`OptionFile`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `users_roles` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `IDRol` bigint(20) DEFAULT '0',
  `RolName` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `IDRol` (`IDRol`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

CREATE TABLE IF NOT EXISTS `users_roles_permissions` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `RolID` int(11) DEFAULT '0',
  `OptionFile` varchar(40) DEFAULT NULL,
  `OptionStatus` int(1) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `RolID` (`RolID`),
  KEY `OptionFile` (`OptionFile`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `users_messages`;
CREATE TABLE IF NOT EXISTS `users_messages` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `FromID` bigint(20) DEFAULT '0',
  `ToID` bigint(20) DEFAULT NULL,
  `Subject` varchar(200) DEFAULT NULL,
  `DateSend` datetime DEFAULT NULL,
  `Message` longtext,
  `ReadMsg` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `FromID` (`FromID`),
  KEY `ToID` (`ToID`),
  KEY `DateSend` (`DateSend`),
  KEY `ReadMsg` (`ReadMsg`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `users_messages_sent`;
CREATE TABLE IF NOT EXISTS `users_messages_sent` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `FromID` bigint(20) DEFAULT '0',
  `ToID` mediumtext,
  `Subject` varchar(200) DEFAULT NULL,
  `DateSend` datetime DEFAULT NULL,
  `Message` longtext,
  `ReadMsg` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `FromID` (`FromID`),
  KEY `DateSend` (`DateSend`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `users_devices` (
  `ID` bigint(20) NOT NULL,
  `IDUser` bigint(20) DEFAULT '0',
  `DeviceType` varchar(10) DEFAULT NULL,
  `DeviceID` varchar(200) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
ALTER TABLE `users_devices` ADD PRIMARY KEY (`ID`), ADD KEY `IDUser` (`IDUser`),  ADD KEY `DeviceType` (`DeviceType`),   ADD KEY `DeviceID` (`DeviceID`);
ALTER TABLE `users_devices` MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;

INSERT IGNORE INTO `aux_countries` (`ID`, `Name`, `Code`) VALUES
(1, 'España', 'ES');

INSERT IGNORE INTO `aux_states` (`ID`, `IDFather`, `Name`) VALUES
(1, 1, 'Alava'),
(2, 1, 'Albacete'),
(3, 1, 'Alicante'),
(4, 1, 'Almeria'),
(5, 1, 'Avila'),
(6, 1, 'Badajoz'),
(7, 1, 'Baleares'),
(8, 1, 'Barcelona'),
(9, 1, 'Burgos'),
(10, 1, 'Caceres'),
(11, 1, 'Cadiz'),
(12, 1, 'Castellon'),
(13, 1, 'Ciudad Real'),
(14, 1, 'Cordoba'),
(15, 1, 'A Coruña'),
(16, 1, 'Cuenca'),
(17, 1, 'Girona'),
(18, 1, 'Granada'),
(19, 1, 'Guadalajara'),
(20, 1, 'Guipuzcoa'),
(21, 1, 'Huelva'),
(22, 1, 'Huesca'),
(23, 1, 'Jaen'),
(24, 1, 'Leon'),
(25, 1, 'Lleida'),
(26, 1, 'La Rioja'),
(27, 1, 'Lugo'),
(28, 1, 'Madrid'),
(29, 1, 'Malaga'),
(30, 1, 'Murcia'),
(31, 1, 'Navarra'),
(32, 1, 'Ourense'),
(33, 1, 'Asturias'),
(34, 1, 'Palencia'),
(35, 1, 'Las Palmas G.c.'),
(36, 1, 'Pontevedra'),
(37, 1, 'Salamanca'),
(38, 1, 'Tenerife'),
(39, 1, 'Cantabria'),
(40, 1, 'Segovia'),
(41, 1, 'Sevilla'),
(43, 1, 'Tarragona'),
(44, 1, 'Teruel'),
(45, 1, 'Toledo'),
(46, 1, 'Valencia'),
(47, 1, 'Valladolid'),
(48, 1, 'Vizcaya'),
(49, 1, 'Zamora'),
(50, 1, 'Zaragoza'),
(51, 1, 'Ceuta'),
(52, 1, 'Melilla');

INSERT IGNORE INTO `languages` (`id`, `code`, `language`, `fileconfig`, `flag`, `factorysetting`) VALUES
(1, 'es_ES', 'Español', '', '', 1);

INSERT IGNORE INTO `users_roles` (`ID`, `IDRol`, `RolName`) VALUES
(1, 1, 'Usuario'),
(2, 2, 'Asociado'),
(3, 3, 'Colaborador'),
(4, 4, 'Gestor'),
(5, 5, 'Admin'),
(6, 99, 'Root');

INSERT IGNORE INTO `users_roles_permissions` (`ID`, `RolID`, `OptionFile`, `OptionStatus`) VALUES
(1, 99, 'users', 1),
(2, 99, 'levels', 1),
(3, 99, 'modules', 1);

CREATE TABLE IF NOT EXISTS `likethis` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDUser` varchar(60) DEFAULT '0',
  `TableName` varchar(50) DEFAULT NULL,
  `TableID` bigint(20) DEFAULT '0',
  `ModuleName` varchar(30) DEFAULT NULL,
  `Options` varchar(100) DEFAULT NULL,
  `Vote` varchar(1) DEFAULT '+',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
CREATE TABLE IF NOT EXISTS `lnk_tags` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `TableFather` varchar(60) DEFAULT NULL,
  `IDFather` bigint(20) DEFAULT '0',
  `IDLink` bigint(20) DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
CREATE TABLE `notifications` (
  `ID` bigint(20) NOT NULL,
  `Type` varchar(30) DEFAULT NULL,
  `IDElement` varchar(11) DEFAULT NULL,
  `DatePublish` datetime DEFAULT NULL,
  `Method` varchar(30) DEFAULT NULL,
  `Destination` varchar(250) DEFAULT NULL,
  `Title` varchar(250) DEFAULT NULL,
  `Body` longtext,
  `Data` longtext,
  `Sended` tinyint(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
ALTER TABLE `notifications` ADD PRIMARY KEY (`ID`), ADD KEY `DatePublish` (`DatePublish`);
ALTER TABLE `notifications` MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
INSERT INTO modules_installed (Module, BlockMenu, ModuleName, Version) VALUES
("config","system","Configuración", null),
("levels","system","Roles y Permisos", null),
("users","system","Usuarios", null),
("modules","system","Gestión Módulos", null),
("permalinks","system","Permalinks", null),
("core--cleanftp","tools","Limpiador FTP", null),
("core--fcmpush","tools","Notificaciones Push", null),
("core--locale","tools","Traducciones", null),
("core--socialmedia","tools","SocialMedia", null),
("core--minimizer","tools","Minificador JS/CSS", null),
("comments","tools","Todos los comentarios", null);
INSERT INTO `modules_adminmenu` (`ID`, `Block`, `Title`, `Icon`, `Conditions`, `Orden`) VALUES
(1, 'system', 'Sistema', 'fa-cogs', NULL, 1),
(2, 'business', 'Empresas', 'fa-building', NULL, 2),
(3, 'appearance', 'Apariencia', 'fa-dashboard', NULL, 3),
(4, 'contents', 'Contenidos', 'fa-archive', NULL, 4),
(5, 'tools', 'Herramientas', 'fa-wrench', NULL, 5);