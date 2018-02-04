CREATE TABLE IF NOT EXISTS `singleblog_cats` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Title` varchar(250) DEFAULT NULL,
  `Orden` bigint(20) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `Orden` (`Orden`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
ALTER TABLE `singleblog` ADD `IDCategory` BIGINT NULL DEFAULT '0' AFTER `IDBusiness`;
UPDATE modules_installed SET Version="3.0.0.2" WHERE Module="singleblog";