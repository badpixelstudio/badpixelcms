CREATE TABLE IF NOT EXISTS `contact` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Name` varchar(250) DEFAULT NULL,
  `Email` varchar(200) DEFAULT NULL,
  `Phone` varchar(200) DEFAULT NULL,
  `Subject` varchar(200) DEFAULT NULL,
  `Message` longtext,
  `BodyEmail` longtext,
  `DatePublish` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
UPDATE modules_installed SET Version="4.3.0.0" WHERE Module="contact";