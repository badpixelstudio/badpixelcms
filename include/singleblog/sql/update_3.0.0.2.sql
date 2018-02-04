CREATE TABLE IF NOT EXISTS `singleblog_related` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDFather` bigint(20) DEFAULT NULL,
  `Link` varchar(250) DEFAULT NULL,
  `CounterClick` int(11) DEFAULT NULL,
  `Level` int(3) DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `IDFather` (`IDFather`),
  KEY `Orden` (`Orden`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=FIXED AUTO_INCREMENT=1 ;
UPDATE modules_installed SET Version="3.0.0.3" WHERE Module="singleblog";