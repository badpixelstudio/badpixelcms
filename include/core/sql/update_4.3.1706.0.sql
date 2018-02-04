CREATE TABLE `users_devices` (
  `ID` bigint(20) NOT NULL,
  `IDUser` bigint(20) DEFAULT '0',
  `DeviceType` varchar(10) DEFAULT NULL,
  `DeviceID` varchar(200) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
ALTER TABLE `users_devices` ADD PRIMARY KEY (`ID`), ADD KEY `IDUser` (`IDUser`),  ADD KEY `DeviceType` (`DeviceType`),   ADD KEY `DeviceID` (`DeviceID`);
ALTER TABLE `users_devices` MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
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
INSERT INTO modules_installed (Module, BlockMenu, ModuleName, Version) VALUES ("core--cleanftp","tools","Limpiar FTP", null);
INSERT INTO modules_installed (Module, BlockMenu, ModuleName, Version) VALUES ("core--fcmpush","tools","Notificaciones Push", null);
UPDATE modules_installed SET Version="4.3.1711.0" WHERE Module="core";