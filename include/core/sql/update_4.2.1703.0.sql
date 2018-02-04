UPDATE `modules_installed` SET BlockMenu="appearance" WHERE `BlockMenu` LIKE 'appareance';
INSERT INTO modules_installed (Module, BlockMenu, ModuleName, Version) VALUES
("core--mainmenu","system","Menú del panel", null);
UPDATE modules_installed SET Version="4.3.1704.0" WHERE Module="core";