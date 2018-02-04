ALTER TABLE `modules_config` CHANGE `ParamValue` `ParamValue` LONGTEXT CHARACTER SET utf8 NULL DEFAULT NULL;
CREATE TABLE `modules_config_previous` AS SELECT * FROM `modules_config`;
UPDATE modules_installed SET Version="4.4.1802.0" WHERE Module="core";