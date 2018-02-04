ALTER TABLE users 
ADD `InvoiceNIF` varchar(30) DEFAULT NULL AFTER `twitter_access_token_secret`,
ADD `InvoiceName` varchar(200) DEFAULT NULL AFTER `InvoiceNIF`,
ADD `InvoiceStreet` varchar(200) DEFAULT NULL AFTER `InvoiceName`,
ADD `InvoiceZipCode` varchar(20) DEFAULT NULL AFTER `InvoiceStreet`,
ADD `InvoiceCity` varchar(150) DEFAULT NULL AFTER `InvoiceZipCode`,
ADD `InvoiceState` varchar(150) DEFAULT NULL AFTER `InvoiceCity`,
ADD `InvoiceCountry` varchar(150) DEFAULT NULL AFTER `InvoiceState`,
ADD `InvoicePhone` varchar(50) DEFAULT NULL AFTER `InvoiceCountry`,
ADD `InvoiceEmail` varchar(200) DEFAULT NULL AFTER `InvoicePhone`,
ADD `InvoiceBankName` varchar(100) DEFAULT NULL AFTER `InvoiceEmail`,
ADD `InvoiceBankSwiftCode` varchar(30) DEFAULT NULL AFTER `InvoiceBankName`,
ADD `InvoiceBankAccount` varchar(50) DEFAULT NULL AFTER `InvoiceBankSwiftCode`,
ADD `InvoiceBankOwner` varchar(200) DEFAULT NULL AFTER `InvoiceBankAccount`;
UPDATE modules_installed SET Version="3.0.0.1" WHERE Module="core";
UPDATE modules_installed SET Version="3.0.0.1" WHERE Module="users";