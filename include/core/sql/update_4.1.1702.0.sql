ALTER TABLE `modules_installed` ADD `BlockMenu` VARCHAR(50) DEFAULT NULL AFTER `Module`;
ALTER TABLE `modules_installed` ADD `Orden` BIGINT NULL DEFAULT '0' AFTER `Version`;
CREATE TABLE `modules_adminmenu` ( 
  `ID` BIGINT NULL AUTO_INCREMENT, 
  `Block` VARCHAR(50) NULL, 
  `Title` VARCHAR(100) NULL, 
  `Icon` VARCHAR(50) NULL, 
  `Conditions` MEDIUMTEXT NULL, 
  `Orden` BIGINT NULL DEFAULT '0', 
PRIMARY KEY (`ID`)) ENGINE = MyISAM;
INSERT INTO `modules_adminmenu` (`ID`, `Block`, `Title`, `Icon`, `Conditions`, `Orden`) VALUES
(1, 'system', 'Sistema', 'fa-cogs', NULL, 1),
(2, 'business', 'Empresas', 'fa-building', NULL, 2),
(3, 'appearance', 'Apariencia', 'fa-dashboard', NULL, 3),
(4, 'contents', 'Contenidos', 'fa-archive', NULL, 4),
(5, 'tools', 'Herramientas', 'fa-wrench', NULL, 5);
UPDATE modules_installed SET BlockMenu="system" WHERE Module="config";
UPDATE modules_installed SET BlockMenu="system" WHERE Module="levels";
UPDATE modules_installed SET BlockMenu="system" WHERE Module="users";
UPDATE modules_installed SET BlockMenu="system" WHERE Module="modules";
UPDATE modules_installed SET BlockMenu="system" WHERE Module="permalinks";
UPDATE modules_installed SET BlockMenu="tools" WHERE Module="core--locale";
UPDATE modules_installed SET BlockMenu="tools" WHERE Module="core--socialmedia";
UPDATE modules_installed SET BlockMenu="tools" WHERE Module="core--minimizer";
UPDATE modules_installed SET Version="4.2.1703.0" WHERE Module="core";