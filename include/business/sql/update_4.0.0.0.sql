CREATE TABLE IF NOT EXISTS `business_mailing_mails` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IDMailing` bigint(20) DEFAULT '0',
  `Email` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `IDMailing` (`IDMailing`),
  KEY `Email` (`Email`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
UPDATE modules_installed SET Version="4.0.0.1" WHERE Module="business";