UPDATE `users_roles_permissions` SET OptionFile=REPLACE(SUBSTR(OptionFile,4),'.php','');
ALTER TABLE `modules_installed` ADD `ModuleName` VARCHAR(120) NULL AFTER `Module`;
UPDATE `modules_installed` SET ModuleName=(SELECT OptionName FROM users_modules WHERE users_modules.OptionFile=modules_installed.Module LIMIT 1);
DROP TABLE users_modules;
UPDATE modules_installed SET Version="4.0.0.0" WHERE Module="core";
UPDATE modules_installed SET Version=NULL WHERE Module="users";
UPDATE modules_installed SET Version=NULL WHERE Module="levels";
UPDATE modules_installed SET Version=NULL WHERE Module="permalinks";
UPDATE modules_installed SET Version=NULL, Module="core--locale" WHERE Module="locale";
UPDATE modules_installed SET Version=NULL, Module="core--socialmedia" WHERE Module="socialmedia";
UPDATE modules_installed SET Version=NULL, Module="core--minimizer" WHERE Module="minimizer";
UPDATE modules_installed SET Version=NULL WHERE Module="comments";
UPDATE modules_installed SET Version=NULL WHERE Module="permalinks";
INSERT INTO modules_installed ("Module", "ModuleName", "Version") VALUES ("modules","Gesti�n M�dulos", null);