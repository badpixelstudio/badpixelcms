DROP TABLE IF EXISTS `cats`;
CREATE TABLE `cats` (
  `ID` bigint(20) NOT NULL,
  `IDBusiness` bigint(20) DEFAULT '0',
  `MultiBusiness` int(1) DEFAULT '0',
  `IDFather` bigint(20) NOT NULL DEFAULT '0',
  `CategoryType` int(1) NOT NULL DEFAULT '0',
  `Title` varchar(200) NOT NULL,
  `Description` longtext NOT NULL,
  `Image` varchar(250) DEFAULT NULL,
  `ImageAlign` varchar(15) DEFAULT NULL,
  `AccessURL` varchar(250) NOT NULL,
  `IDAuthor` bigint(20) NOT NULL DEFAULT '0',
  `CatLevelCreateSub` int(11) DEFAULT '-1',
  `CatLevelAdmin` int(11) DEFAULT '-1',
  `PageLevelAccess` int(11) DEFAULT '-1',
  `PageLevelAdmin` int(11) NOT NULL DEFAULT '-1',
  `PageUsePreTitle` int(1) NOT NULL DEFAULT '-1',
  `PageUsePostTitle` int(1) NOT NULL DEFAULT '-1',
  `PageUseSummary` int(1) NOT NULL DEFAULT '-1',
  `PageUseFirstImage` varchar(10) NOT NULL DEFAULT 'aligned',
  `PageUseFirstImageAlign` int(1) DEFAULT '0',
  `PageFirstImageOptions` text,
  `PageFirstImageWidth` int(11) NOT NULL,
  `PageFirstImageHeight` int(11) NOT NULL,
  `PageFirstImageHoldSize` int(1) NOT NULL DEFAULT '0',
  `PageFirstImageTrimExcess` int(1) NOT NULL DEFAULT '0',
  `PageFirstImageThumbWidth` int(11) NOT NULL,
  `PageFirstImageThumbHeight` int(11) NOT NULL,
  `PageFirstImageThumbHoldSize` int(1) NOT NULL DEFAULT '0',
  `PageFirstImageThumbTrimExcess` int(1) NOT NULL DEFAULT '0',
  `PageUseDates` int(1) NOT NULL DEFAULT '-1',
  `PageUseAuthorInfo` int(1) NOT NULL DEFAULT '-1',
  `PageUseTags` int(1) NOT NULL DEFAULT '-1',
  `PageUseActivation` int(1) NOT NULL DEFAULT '-1',
  `PageUseSocial` int(1) NOT NULL DEFAULT '-1',
  `PageUseGeolocation` int(1) NOT NULL DEFAULT '-1',
  `PageUseImages` int(1) DEFAULT '0',
  `PageImagesOptions` text,
  `PageImagesWidth` int(11) NOT NULL,
  `PageImagesHeight` int(11) NOT NULL,
  `PageImagesHoldSize` int(1) NOT NULL DEFAULT '0',
  `PageImagesTrimExcess` int(1) NOT NULL DEFAULT '0',
  `PageImagesThumbWidth` int(11) NOT NULL,
  `PageImagesThumbHeight` int(11) NOT NULL,
  `PageImagesThumbHoldSize` int(1) NOT NULL DEFAULT '0',
  `PageImagesThumbTrimExcess` int(1) NOT NULL DEFAULT '0',
  `PageUseAttachments` int(1) DEFAULT '0',
  `PageUseLinks` int(1) DEFAULT '0',
  `PageUseVideos` int(1) DEFAULT '0',
  `PageUseComments` int(1) DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
INSERT INTO `cats` (`ID`, `IDBusiness`, `MultiBusiness`, `IDFather`, `CategoryType`, `Title`, `Description`, `Image`, `ImageAlign`, `AccessURL`, `IDAuthor`, `CatLevelCreateSub`, `CatLevelAdmin`, `PageLevelAccess`, `PageLevelAdmin`, `PageUsePreTitle`, `PageUsePostTitle`, `PageUseSummary`, `PageUseFirstImage`, `PageUseFirstImageAlign`, `PageFirstImageOptions`, `PageFirstImageWidth`, `PageFirstImageHeight`, `PageFirstImageHoldSize`, `PageFirstImageTrimExcess`, `PageFirstImageThumbWidth`, `PageFirstImageThumbHeight`, `PageFirstImageThumbHoldSize`, `PageFirstImageThumbTrimExcess`, `PageUseDates`, `PageUseAuthorInfo`, `PageUseTags`, `PageUseActivation`, `PageUseSocial`, `PageUseGeolocation`, `PageUseImages`, `PageImagesOptions`, `PageImagesWidth`, `PageImagesHeight`, `PageImagesHoldSize`, `PageImagesTrimExcess`, `PageImagesThumbWidth`, `PageImagesThumbHeight`, `PageImagesThumbHoldSize`, `PageImagesThumbTrimExcess`, `PageUseAttachments`, `PageUseLinks`, `PageUseVideos`, `PageUseComments`, `Orden`) VALUES
(1, 0, 0, 0, 0, 'Noticias', '', NULL, NULL, '', 0, 2, 2, -1, -1, 1, 0, 1, '1', 0, '(images,800,600);(thumbnails,133,208,crop)', 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 0, 1, '(images,800,600);(thumbnails,87,87,crop)', 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1);
DROP TABLE IF EXISTS `cats_attachments`;
CREATE TABLE `cats_attachments` (
  `ID` bigint(20) NOT NULL,
  `IDFather` bigint(20) DEFAULT NULL,
  `File` varchar(250) DEFAULT NULL,
  `Description` varchar(250) DEFAULT NULL,
  `CounterClick` int(11) DEFAULT NULL,
  `Level` int(3) NOT NULL DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS `cats_comments`;
CREATE TABLE `cats_comments` (
  `ID` bigint(20) NOT NULL,
  `IDFather` bigint(20) DEFAULT NULL,
  `IDAuthor` int(11) DEFAULT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `Email` varchar(150) DEFAULT NULL,
  `Comment` mediumtext,
  `DatePublish` datetime DEFAULT NULL,
  `Points` int(1) NOT NULL DEFAULT '3',
  `Orden` bigint(20) DEFAULT '0',
  `Active` int(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS `cats_images`;
CREATE TABLE `cats_images` (
  `ID` bigint(20) NOT NULL,
  `IDFather` bigint(20) DEFAULT NULL,
  `Image` varchar(250) DEFAULT NULL,
  `Description` varchar(250) DEFAULT NULL,
  `Level` int(3) NOT NULL DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0',
  `Link` varchar(250) DEFAULT NULL,
  `Download` varchar(250) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS `cats_links`;
CREATE TABLE `cats_links` (
  `ID` bigint(20) NOT NULL,
  `IDFather` bigint(20) DEFAULT NULL,
  `Link` varchar(250) DEFAULT NULL,
  `Description` varchar(250) DEFAULT NULL,
  `CounterClick` int(11) DEFAULT NULL,
  `Level` int(3) DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=FIXED;
DROP TABLE IF EXISTS `cats_pages`;
CREATE TABLE `cats_pages` (
  `ID` bigint(20) NOT NULL,
  `IDBusiness` bigint(20) DEFAULT '0',
  `IDFather` bigint(20) DEFAULT NULL,
  `PreTitle` varchar(250) DEFAULT NULL,
  `Title` varchar(250) DEFAULT NULL,
  `PostTitle` varchar(250) DEFAULT NULL,
  `Summary` text,
  `Page` mediumtext,
  `Image` varchar(200) DEFAULT NULL,
  `ImageAlign` varchar(15) DEFAULT NULL,
  `DatePublish` date DEFAULT NULL,
  `DateExpire` date DEFAULT NULL,
  `Active` tinyint(1) DEFAULT NULL,
  `IDAuthor` int(11) DEFAULT NULL,
  `Readings` bigint(20) DEFAULT NULL,
  `TotalReadings` bigint(20) DEFAULT NULL,
  `Orden` bigint(20) DEFAULT '0',
  `EnableComments` int(1) NOT NULL DEFAULT '-1',
  `Geolocation` varchar(200) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS `cats_pages_translations`;
CREATE TABLE `cats_pages_translations` (
  `ID` bigint(20) NOT NULL,
  `IDOriginal` bigint(20) DEFAULT NULL,
  `LangCode` varchar(3) DEFAULT NULL,
  `PreTitle` varchar(250) DEFAULT NULL,
  `Title` varchar(250) DEFAULT NULL,
  `PostTitle` varchar(250) DEFAULT NULL,
  `Summary` text,
  `Page` text,
  `Field1` varchar(250) DEFAULT NULL,
  `Field2` varchar(250) DEFAULT NULL,
  `Field3` varchar(250) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
DROP TABLE IF EXISTS `cats_related`;
CREATE TABLE `cats_related` (
  `ID` bigint(20) NOT NULL,
  `IDFather` bigint(20) DEFAULT '0',
  `IDRelated` bigint(20) DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=FIXED;
DROP TABLE IF EXISTS `cats_translations`;
CREATE TABLE `cats_translations` (
  `ID` bigint(20) NOT NULL,
  `IDOriginal` bigint(20) DEFAULT NULL,
  `LangCode` varchar(5) DEFAULT NULL,
  `PreTitle` varchar(250) DEFAULT NULL,
  `Title` varchar(250) DEFAULT NULL,
  `PostTitle` varchar(250) DEFAULT NULL,
  `Summary` text,
  `Page` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
DROP TABLE IF EXISTS `cats_videos`;
CREATE TABLE `cats_videos` (
  `ID` bigint(20) NOT NULL,
  `IDFather` bigint(20) DEFAULT NULL,
  `Embed` mediumtext,
  `Description` varchar(250) DEFAULT NULL,
  `Level` int(3) DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=FIXED;
DROP TABLE IF EXISTS `contents`;
CREATE TABLE `contents` (
  `ID` bigint(20) NOT NULL,
  `IDBusiness` bigint(20) DEFAULT '0',
  `Title` varchar(250) DEFAULT NULL,
  `Image` varchar(200) DEFAULT NULL,
  `Image2` varchar(200) DEFAULT NULL,
  `ShortDescription` mediumtext,
  `LongDescription` longtext,
  `Link` varchar(250) DEFAULT NULL,
  `Geolocation` varchar(200) DEFAULT NULL,
  `Orden` bigint(20) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
INSERT INTO `contents` (`ID`, `IDBusiness`, `Title`, `Image`, `Image2`, `ShortDescription`, `LongDescription`, `Link`, `Geolocation`, `Orden`) VALUES
(1, 0, 'Funcionó', NULL, NULL, NULL, '<p>Ya tienes instalada la &uacute;ltima versi&oacute;n de BadPixelCMS, con la base de datos de prueba.</p>\r\n', NULL, NULL, 1);
DROP TABLE IF EXISTS `contents_attachments`;
CREATE TABLE `contents_attachments` (
  `ID` bigint(20) NOT NULL,
  `IDFather` bigint(20) DEFAULT NULL,
  `File` varchar(250) DEFAULT NULL,
  `Description` varchar(250) DEFAULT NULL,
  `CounterClick` int(11) DEFAULT NULL,
  `Level` int(3) NOT NULL DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS `contents_comments`;
CREATE TABLE `contents_comments` (
  `ID` bigint(20) NOT NULL,
  `IDFather` bigint(20) DEFAULT NULL,
  `IDAuthor` int(11) DEFAULT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `Email` varchar(150) DEFAULT NULL,
  `Comment` mediumtext,
  `DatePublish` datetime DEFAULT NULL,
  `Points` int(1) NOT NULL DEFAULT '3',
  `Orden` bigint(20) DEFAULT '0',
  `Active` int(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS `contents_images`;
CREATE TABLE `contents_images` (
  `ID` bigint(20) NOT NULL,
  `IDFather` bigint(20) DEFAULT NULL,
  `Image` varchar(250) DEFAULT NULL,
  `Description` varchar(250) DEFAULT NULL,
  `Level` int(3) NOT NULL DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0',
  `Link` varchar(250) DEFAULT NULL,
  `Download` varchar(250) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS `contents_links`;
CREATE TABLE `contents_links` (
  `ID` bigint(20) NOT NULL,
  `IDFather` bigint(20) DEFAULT NULL,
  `Link` varchar(250) DEFAULT NULL,
  `Description` varchar(250) DEFAULT NULL,
  `CounterClick` int(11) DEFAULT NULL,
  `Level` int(3) DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=FIXED;
DROP TABLE IF EXISTS `contents_translations`;
CREATE TABLE `contents_translations` (
  `ID` bigint(20) NOT NULL,
  `IDOriginal` bigint(20) DEFAULT NULL,
  `LangCode` varchar(5) DEFAULT NULL,
  `Title` varchar(250) DEFAULT NULL,
  `ShortDescription` mediumtext,
  `LongDescription` longtext
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
DROP TABLE IF EXISTS `contents_videos`;
CREATE TABLE `contents_videos` (
  `ID` bigint(20) NOT NULL,
  `IDFather` bigint(20) DEFAULT NULL,
  `Embed` mediumtext,
  `Description` varchar(250) DEFAULT NULL,
  `Level` int(3) DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=FIXED;
DROP TABLE IF EXISTS `gallery`;
CREATE TABLE `gallery` (
  `ID` bigint(20) NOT NULL,
  `IDBusiness` bigint(20) DEFAULT '0',
  `MultiBusiness` int(1) DEFAULT '0',
  `Title` varchar(200) CHARACTER SET latin1 NOT NULL,
  `DatePublish` date NOT NULL,
  `Image` varchar(100) CHARACTER SET latin1 NOT NULL,
  `Description` mediumtext CHARACTER SET latin1 NOT NULL,
  `AutoGenThumb` int(1) NOT NULL DEFAULT '0',
  `ImageOptions` text,
  `IDAuthor` bigint(20) DEFAULT '0',
  `LastUpdate` datetime DEFAULT NULL,
  `Orden` bigint(20) DEFAULT '0',
  `Active` int(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `gallery_images`;
CREATE TABLE `gallery_images` (
  `ID` bigint(20) NOT NULL,
  `IDFather` bigint(20) DEFAULT NULL,
  `IDBusiness` int(11) DEFAULT '0',
  `Image` varchar(250) DEFAULT NULL,
  `Description` varchar(250) DEFAULT NULL,
  `Level` int(3) NOT NULL DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0',
  `Link` varchar(250) DEFAULT NULL,
  `Download` varchar(250) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS `gallery_translations`;
CREATE TABLE `gallery_translations` (
  `ID` bigint(20) NOT NULL,
  `IDOriginal` bigint(20) DEFAULT '0',
  `LangCode` varchar(5) DEFAULT '',
  `Title` varchar(200) CHARACTER SET latin1 NOT NULL,
  `Description` mediumtext CHARACTER SET latin1 NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `ID` int(11) NOT NULL,
  `IDBusiness` bigint(20) DEFAULT '0',
  `IDFather` bigint(20) DEFAULT '0',
  `Title` char(100) DEFAULT NULL,
  `Link` char(255) DEFAULT NULL,
  `Image` char(255) DEFAULT NULL,
  `Icon` char(255) DEFAULT NULL,
  `Orden` bigint(20) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
INSERT INTO `menu` (`ID`, `IDBusiness`, `IDFather`, `Title`, `Link`, `Image`, `Icon`, `Orden`) VALUES
(1, 0, 0, 'Servicios', 'servicios', NULL, NULL, 1),
(2, 0, 0, 'Galería', 'galeria', NULL, NULL, 2),
(3, 0, 0, 'Noticias', 'noticias', NULL, NULL, 3),
(4, 0, 0, 'Blog', 'blog', NULL, NULL, 4),
(5, 0, 0, 'Contacto', 'contactar', NULL, NULL, 5);
DROP TABLE IF EXISTS `menu_translations`;
CREATE TABLE `menu_translations` (
  `ID` int(11) NOT NULL,
  `IDOriginal` bigint(20) DEFAULT '0',
  `LangCode` varchar(5) DEFAULT NULL,
  `Title` char(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
DROP TABLE IF EXISTS `modules_config`;
CREATE TABLE `modules_config` (
  `ID` int(3) UNSIGNED NOT NULL,
  `UserID` int(20) DEFAULT NULL,
  `Module` varchar(50) DEFAULT NULL,
  `ParamName` varchar(50) DEFAULT NULL,
  `ParamValue` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
INSERT INTO `modules_config` (`ID`, `UserID`, `Module`, `ParamName`, `ParamValue`) VALUES
(1, 0, 'core', 'MaintenanceActive', '0'),
(2, 0, 'core', 'Title', 'Nombre Del Sitio'),
(3, 0, 'core', 'Cookie', 'CookieTitle'),
(4, 0, 'core', 'HeadDescription', 'Descripción del sitio'),
(5, 0, 'core', 'HeadTags', 'MetaTags del sitio'),
(6, 0, 'core', 'HeadImage', ''),
(7, 0, 'core', 'Copyright', '© Todos los derechos reservados'),
(8, 0, 'core', 'Template', 'basic'),
(9, 0, 'core', 'TemplateMinResources', '0'),
(10, 0, 'core', 'Lang', 'Spanish'),
(11, 0, 'core', 'PanelMinResources', '0'),
(12, 0, 'core', 'EnablePrivateMessages', '1'),
(13, 0, 'core', 'WYSIWYGParseToClean', '1'),
(14, 0, 'core', 'WYSIWYGTagsEnabled', '<b><strong><i><u><strike><sup><sub><a><embed><iframe><p><q><br><img><div><span><li><ul><ol><h1><h2><h3><h4><h5><h6>'),
(15, 0, 'core', 'WYSIWYGCleanAttributes', 'class|lang|style|size|face'),
(16, 0, 'core', 'DebugActive', '0'),
(17, 0, 'core', 'LogAllErrorMessages', '0'),
(18, 0, 'core', 'DisplayErrors', '1'),
(19, 0, 'core', 'NotifyEmailLogins', '0'),
(20, 0, 'core', 'LogLogins', '0'),
(21, 0, 'core', 'BlockOwner', 'Propiedad'),
(22, 0, 'core', 'OwnerName', ''),
(23, 0, 'core', 'OwnerStreet', ''),
(24, 0, 'core', 'OwnerZipCode', ''),
(25, 0, 'core', 'OwnerCity', ''),
(26, 0, 'core', 'OwnerState', ''),
(27, 0, 'core', 'OwnerCountry', ''),
(28, 0, 'core', 'OwnerPhone', ''),
(29, 0, 'core', 'OwnerFax', ''),
(30, 0, 'core', 'OwnerPublicEmail', ''),
(31, 0, 'core', 'OwnerTaxNumber', ''),
(32, 0, 'core', 'OwnerGeolocation', ''),
(33, 0, 'core', 'StateDefault', '5'),
(34, 0, 'core', 'StateTextDefault', 'Ávila'),
(35, 0, 'core', 'CityDefault', '1'),
(36, 0, 'core', 'CityTextDefault', 'Ávila'),
(37, 0, 'core', 'BlockMails', 'Correos'),
(38, 0, 'core', 'MainMail', 'info@dominio.com'),
(39, 0, 'core', 'PasswordsMail', 'no-reply@dominio.com'),
(40, 0, 'core', 'NewsletterMail', 'newsletter@dominio.com'),
(41, 0, 'core', 'FromEmail', 'Nombre Del Sitio'),
(42, 0, 'core', 'SMTPHost', 'smtp.dominio.com'),
(43, 0, 'core', 'SMTPPort', '25'),
(44, 0, 'core', 'SMTPUserName', 'email@dominio.com'),
(45, 0, 'core', 'SMTPPassword', '12345'),
(46, 0, 'core', 'BlockSocial', 'RRSS'),
(47, 0, 'core', 'GoogleAnalyticsID', ''),
(48, 0, 'core', 'DefaultCommentsEnable', '1'),
(49, 0, 'core', 'EnableCommentsAnonymousUsers', '1'),
(50, 0, 'core', 'RequireActivationCommentsAnonymousUsers', '1'),
(51, 0, 'core', 'RequireActivationCommentsLoggedUsers', '0'),
(52, 0, 'core', 'EnableFacebookComments', '0'),
(53, 0, 'core', 'FacebookURL', ''),
(54, 0, 'core', 'TwitterURL', ''),
(55, 0, 'core', 'GooglePlusURL', ''),
(56, 0, 'core', 'YouTubeURL', ''),
(57, 0, 'core', 'LinkedInURL', ''),
(58, 0, 'core', 'PinterestURL', ''),
(59, 0, 'core', 'InstagramURL', ''),
(60, 0, 'core', 'SoundCloudURL', ''),
(61, 0, 'core', 'FlickrURL', ''),
(62, 0, 'core', 'FacebookEnabled', '0'),
(63, 0, 'core', 'FacebookAppID', ''),
(64, 0, 'core', 'FacebookAppSecret', ''),
(65, 0, 'core', 'FacebookScope', 'email,user_birthday,user_location,user_about_me,publish_stream,manage_pages'),
(66, 0, 'core', 'FacebookAccessToken', ''),
(67, 0, 'core', 'FacebookTokenProfilePublish', ''),
(68, 0, 'core', 'TwitterEnabled', '0'),
(69, 0, 'core', 'TwitterConsumerKey', ''),
(70, 0, 'core', 'TwitterConsumerSecret', ''),
(71, 0, 'core', 'TwitterAccessToken', ''),
(72, 0, 'core', 'TwitterAccessTokenSecret', ''),
(73, 0, 'core', 'TwitterPreTweet', ''),
(74, 0, 'core', 'TwitterPostTweet', ''),
(75, 0, 'core', 'GoogleAPIEnabled', '0'),
(76, 0, 'core', 'GoogleAPIClientID', ''),
(77, 0, 'core', 'GoogleAPIClientSecret', ''),
(78, 0, 'core', 'GoogleAPIDeveloperKey', ''),
(79, 0, 'core', 'GoogleAccount', ''),
(80, 0, 'core', 'GooglePassword', ''),
(81, 0, 'core', 'GoogleAPIProfile', ''),
(82, 0, 'core', 'GoogleMapsAPIKey', ''),
(83, 0, 'core', 'SocialMediaCreateAccount', '0'),
(84, 0, 'core', 'BlockAutoPublish', 'Auto publicación'),
(85, 0, 'core', 'AutoSocialMediaParameters', '/all'),
(86, 0, 'core', 'AutoSocialMediaHourFirstPublish', '08:30'),
(87, 0, 'core', 'AutoSocialMediaIntervalMinutes', '30'),
(88, 0, 'core', 'AutoSocialMediaMaxIntervals', '15'),
(89, 0, 'core', 'BlockPageSpeed', 'PageSpeed'),
(90, 0, 'core', 'ImageTag', ''),
(91, 0, 'core', 'ImageLengthKeyCache', '3'),
(92, 0, 'core', 'BlockAPI', 'API'),
(93, 0, 'core', 'APIRequiresOAuthLogin', '0'),
(94, 0, 'core', 'OAuthAccessTokenExpires', '7200'),
(95, 0, 'core', 'OAuthExtendedAccessTokenExpires', '5184000'),
(96, 0, 'core', 'BlockMobile', 'Móviles'),
(97, 0, 'core', 'AndroidGSM_APIKey', ''),
(98, 0, 'core', 'iOSPush_Passphrase', ''),
(99, 0, 'core', 'BlockUpdates', 'Actualizaciones'),
(100, 0, 'core', 'CheckUpdates', '0'),
(101, 0, 'core', 'EnableModuleInstall', '0'),
(102, 0, 'core', 'URLUpdatesAPI', 'http://localhost/badpixelcms3/api/v1/modulerepository'),
(103, 0, 'core', 'BlockMulti', 'Entidades'),
(104, 0, 'core', 'Multi', '0'),
(105, 0, 'core', 'LevelRootMulti', '99'),
(106, 0, 'core', 'MultiConfig', '1'),
(107, 0, 'users', 'UserLanguage', '1'),
(108, 0, 'users', 'Username', '1'),
(109, 0, 'users', 'UserNIF', '1'),
(110, 0, 'users', 'UserStreet', '1'),
(111, 0, 'users', 'UserZipCode', '1'),
(112, 0, 'users', 'UserCity', '1'),
(113, 0, 'users', 'UserState', '1'),
(114, 0, 'users', 'UserCountry', '1'),
(115, 0, 'users', 'UserPhone', '1'),
(116, 0, 'users', 'UserFax', '1'),
(117, 0, 'users', 'UserPublicEmail', '1'),
(118, 0, 'users', 'UserWeb', '1'),
(119, 0, 'users', 'UserSignature', '1'),
(120, 0, 'users', 'UserBirthdate', '1'),
(121, 0, 'users', 'BlockConfig', 'Config'),
(122, 0, 'users', 'UserURLLinkMails', 'web'),
(123, 0, 'users', 'UserCreate', ''),
(124, 0, 'users', 'UserDelete', '1'),
(125, 0, 'users', 'UserAutoActive', ''),
(126, 0, 'users', 'UserPassMinLength', '8'),
(127, 0, 'users', 'UserRetrievePass', '1'),
(128, 0, 'users', 'UserNotifyPM', '1'),
(129, 0, 'users', 'UserDaysToAutoDeletePM', '90'),
(130, 0, 'users', 'UserSecurityCreate', ''),
(131, 0, 'users', 'UserSecurityEdit', '1'),
(132, 0, 'users', 'UserFrontEndCreate', '1'),
(133, 0, 'users', 'UserFrontEndEdit', '1'),
(134, 0, 'users', 'BlockView', 'Vista'),
(135, 0, 'users', 'UserTemplate', '1'),
(136, 0, 'users', 'UseDateExpire', ''),
(137, 0, 'users', 'UserViewExtended', '1'),
(138, 0, 'users', 'UserViewFBData', '1'),
(139, 0, 'users', 'UserCreateCaptcha', ''),
(140, 0, 'users', 'UserEditCaptcha', ''),
(141, 0, 'users', 'UserActivateCaptcha', ''),
(142, 0, 'users', 'UserChangePassCaptcha', ''),
(143, 0, 'users', 'UserLoginCaptcha', ''),
(144, 0, 'users', 'UserContactCaptcha', ''),
(145, 0, 'users', 'BlockAvatar', 'Avatar'),
(146, 0, 'users', 'UserAvatar', '1'),
(147, 0, 'users', 'UserAvatarOptions', '(avatar_original,600,0);(avatar,170,170,crop)'),
(148, 0, 'users', 'UserAvatarWidth', '300'),
(149, 0, 'users', 'UserAvatarHeight', '300'),
(150, 0, 'users', 'UserAvatarMin', ''),
(151, 0, 'users', 'UserAvatarCut', ''),
(152, 0, 'users', 'UserAvatarThumbWidth', '170'),
(153, 0, 'users', 'UserAvatarThumbHeight', '170'),
(154, 0, 'users', 'UserAvatarThumbMin', '1'),
(155, 0, 'users', 'UserAvatarThumbCut', '1'),
(156, 0, 'users', 'BlockInvoice', 'Facturación'),
(157, 0, 'users', 'UseInvoiceNIF', '1'),
(158, 0, 'users', 'UseInvoiceName', '1'),
(159, 0, 'users', 'UseInvoiceStreet', '1'),
(160, 0, 'users', 'UseInvoiceZipCode', '1'),
(161, 0, 'users', 'UseInvoiceCity', '1'),
(162, 0, 'users', 'UseInvoiceState', '1'),
(163, 0, 'users', 'UseInvoiceCountry', '1'),
(164, 0, 'users', 'UseInvoicePhone', '1'),
(165, 0, 'users', 'UseInvoiceEmail', '1'),
(166, 0, 'users', 'UseInvoiceBankName', '1'),
(167, 0, 'users', 'UseInvoiceBankSwiftCode', '1'),
(168, 0, 'users', 'UseInvoiceBankAccount', '1'),
(169, 0, 'users', 'UseInvoiceBankOwner', '1'),
(170, 0, 'backup', 'DefaultFolder', 'backup'),
(171, 0, 'contact', 'UseReCaptcha', ''),
(172, 0, 'contact', 'ReCaptchaKey', ''),
(173, 0, 'contact', 'ReCaptchaSecret', ''),
(174, 0, 'contact', 'PermalinkFolder', 'contactar'),
(175, 0, 'menu', 'EnableMultiBusiness', '1'),
(176, 0, 'menu', 'MaxLevels', '3'),
(177, 0, 'menu', 'UseImage', ''),
(178, 0, 'menu', 'ImageOptions', '(images,800,600);(thumbnails,133,208,crop)'),
(179, 0, 'menu', 'UseIcon', ''),
(180, 0, 'stickers', 'EnableLink', '1'),
(181, 0, 'stickers', 'EnableType', '1'),
(182, 0, 'stickers', 'UseActivation', '1'),
(183, 0, 'slider', 'Width', '943'),
(184, 0, 'slider', 'Height', '300'),
(185, 0, 'slider', 'EnableText', ''),
(186, 0, 'slider', 'EnableLink', '1'),
(187, 0, 'slider', 'EnableShowButton', ''),
(188, 0, 'slider', 'EnableTextButton', ''),
(189, 0, 'slider', 'EnableShowText', '1'),
(190, 0, 'slider', 'EnableShowDescription', '1'),
(191, 0, 'slider', 'MultiBusiness', ''),
(192, 0, 'contents', 'UseImage', '1'),
(193, 0, 'contents', 'ImageOptions', '(images,800,600);(thumbnails,133,208,crop)'),
(194, 0, 'contents', 'UseImage2', '0'),
(195, 0, 'contents', 'Image2Options', '(images,800,600);(thumbnails,133,208,crop)'),
(196, 0, 'contents', 'UseShortDescription', '0'),
(197, 0, 'contents', 'UseLongDescription', '1'),
(198, 0, 'contents', 'UseLink', '0'),
(199, 0, 'contents', 'UseGeolocation', '0'),
(200, 0, 'contents', 'UseImages', '1'),
(201, 0, 'contents', 'UseAttachments', '1'),
(202, 0, 'contents', 'UseLinks', '1'),
(203, 0, 'contents', 'UseVideos', '1'),
(204, 0, 'contents', 'UseComments', '1'),
(205, 0, 'contents', 'ImagesOptions', '(images,1200,0);(thumbnails,300,200,crop)'),
(206, 0, 'contents', 'PermalinkFolder', 'contenidos'),
(207, 0, 'singleblog', 'UseImage', '1'),
(208, 0, 'singleblog', 'ImageOptions', '(images,1200,0);(thumbnails,300,200,crop)'),
(209, 0, 'singleblog', 'UseImage2', '1'),
(210, 0, 'singleblog', 'Image2Options', '(images,1200,0);(thumbnails,300,200,crop)'),
(211, 0, 'singleblog', 'UseShortDescription', '1'),
(212, 0, 'singleblog', 'UseLongDescription', '1'),
(213, 0, 'singleblog', 'UseLink', '1'),
(214, 0, 'singleblog', 'UseGeolocation', '1'),
(215, 0, 'singleblog', 'UseDates', '1'),
(216, 0, 'singleblog', 'UseImages', '1'),
(217, 0, 'singleblog', 'UseAttachments', '1'),
(218, 0, 'singleblog', 'UseLinks', '1'),
(219, 0, 'singleblog', 'UseVideos', '1'),
(220, 0, 'singleblog', 'UseComments', '1'),
(221, 0, 'singleblog', 'ImagesOptions', '(images,1200,0);(thumbnails,300,200,crop)'),
(222, 0, 'singleblog', 'PermalinkFolder', 'blog'),
(223, 0, 'gallery', 'EnableMultiBusiness', '1'),
(224, 0, 'gallery', 'UseConfigDefault', ''),
(225, 0, 'gallery', 'EnableDeleteOldGalleries', '1'),
(226, 0, 'gallery', 'GalleryEnableLevelFolderCreate', '2'),
(227, 0, 'gallery', 'GalleryEnableFolderLimit', '99999'),
(228, 0, 'gallery', 'GalleryEnableDescription', '1'),
(229, 0, 'gallery', 'GalleryEnableAuthor', '1'),
(230, 0, 'gallery', 'GalleryEnableDate', '1'),
(231, 0, 'gallery', 'GalleryEnableGenImage', '1'),
(232, 0, 'gallery', 'GalleryEnableLastUpdate', '1'),
(233, 0, 'gallery', 'GalleryEnableDefineImage', '1'),
(234, 0, 'gallery', 'GalleryEnableAutoGenThumb', ''),
(235, 0, 'gallery', 'GalleryAutoGenThumb', '1'),
(236, 0, 'gallery', 'ExtrasImagesEnableLink', '1'),
(237, 0, 'gallery', 'ExtrasImagesEnableDownload', '1'),
(238, 0, 'gallery', 'GalleryEnableActivation', '1'),
(239, 0, 'gallery', 'ImageOptions', '(images,800,600);(thumbnails,200,150,crop)'),
(240, 0, 'gallery', 'ImagesOptions', '(images,800,600);(thumbnails,200,80-300,crop)'),
(241, 0, 'gallery', 'PermalinkFolder', 'multigalerias'),
(242, 0, 'catpages', 'EnableMultiBusiness', '1'),
(243, 0, 'catpages', 'UseConfigDefault', '1'),
(244, 0, 'catpages', 'CatUseType', '1'),
(245, 0, 'catpages', 'CatEnableGeneralCreate', '1'),
(246, 0, 'catpages', 'CatLevelCreateSub', '2'),
(247, 0, 'catpages', 'CatLevelAdmin', '2'),
(248, 0, 'catpages', 'CatMaxChildren', '99'),
(249, 0, 'catpages', 'CatEnableDescription', '1'),
(250, 0, 'catpages', 'CatEnableAuthor', '1'),
(251, 0, 'catpages', 'CatEnableImage', '1'),
(252, 0, 'catpages', 'CatEnableImageAlign', '1'),
(253, 0, 'catpages', 'BlockPages', 'P?ginas'),
(254, 0, 'catpages', 'PagesCatImageOptions', '(images,800,600);(thumbnails,133,208,crop)'),
(255, 0, 'catpages', 'PageLevelAccess', '0'),
(256, 0, 'catpages', 'PageLevelAdmin', '2'),
(257, 0, 'catpages', 'PageUsePreTitle', '1'),
(258, 0, 'catpages', 'PageUsePostTitle', ''),
(259, 0, 'catpages', 'PageUseSummary', '1'),
(260, 0, 'catpages', 'PageUseFirstImage', '1'),
(261, 0, 'catpages', 'PageUseFirstImageAlign', ''),
(262, 0, 'catpages', 'PageUseDates', '1'),
(263, 0, 'catpages', 'PageUseAuthorInfo', '1'),
(264, 0, 'catpages', 'PageUseTags', '1'),
(265, 0, 'catpages', 'PageUseActivation', '1'),
(266, 0, 'catpages', 'PageUseReadings', '1'),
(267, 0, 'catpages', 'PageUseSocial', '1'),
(268, 0, 'catpages', 'PageUseGeolocation', ''),
(269, 0, 'catpages', 'PageUseImages', '1'),
(270, 0, 'catpages', 'PageUseAttachments', '1'),
(271, 0, 'catpages', 'PageUseLinks', '1'),
(272, 0, 'catpages', 'PageUseVideos', '1'),
(273, 0, 'catpages', 'PageUseComments', '1'),
(274, 0, 'catpages', 'PageFirstImageOptions', '(images,800,600);(thumbnails,133,208,crop)'),
(275, 0, 'catpages', 'PageImagesOptions', '(images,800,600);(thumbnails,87,87,crop)'),
(276, 0, 'catpages', 'PermalinkFolder', ''),
(277, 0, 'services', 'UseImage', '1'),
(278, 0, 'services', 'ImageOptions', '(images,800,600);(thumbnails,133,208,crop)'),
(279, 0, 'services', 'UseImage2', '1'),
(280, 0, 'services', 'Image2Options', '(images,800,600);(thumbnails,133,208,crop)'),
(281, 0, 'services', 'UseIcon', ''),
(282, 0, 'services', 'UseShortDescription', '1'),
(283, 0, 'services', 'UseLongDescription', '1'),
(284, 0, 'services', 'UseLink', '1'),
(285, 0, 'services', 'UseGeolocation', '1'),
(286, 0, 'services', 'UseImages', '1'),
(287, 0, 'services', 'UseAttachments', '1'),
(288, 0, 'services', 'UseLinks', '1'),
(289, 0, 'services', 'UseVideos', '1'),
(290, 0, 'services', 'UseComments', '1'),
(291, 0, 'services', 'ImagesOptions', '(images,1200,0);(thumbnails,300,200,crop)'),
(292, 0, 'services', 'PermalinkFolder', 'servicios');
DROP TABLE IF EXISTS `modules_installed`;
CREATE TABLE `modules_installed` (
  `ID` bigint(20) NOT NULL,
  `Module` varchar(150) DEFAULT NULL,
  `ModuleName` varchar(150) DEFAULT NULL,
  `Version` varchar(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
INSERT INTO `modules_installed` (`ID`, `Module`, `ModuleName`, `Version`) VALUES
(1, 'config', 'Configuración', NULL),
(2, 'levels', 'Roles y Permisos', NULL),
(3, 'users', 'Usuarios', NULL),
(4, 'modules', 'Gestión Módulos', NULL),
(5, 'permalinks', 'Permalinks', NULL),
(6, 'core--locale', 'Traducciones', NULL),
(7, 'core--socialmedia', 'SocialMedia', NULL),
(8, 'core--minimizer', 'Minificador JS/CSS', NULL),
(9, 'comments', 'Todos los comentarios', NULL),
(10, 'core', NULL, '4.0.1612.0'),
(11, 'backup', 'Copias de Seguridad', '3.0.0.2'),
(12, 'catpages', 'Categorías y Páginas', '3.0.0.2'),
(13, 'contact', 'Contactar', '4.0.0.0'),
(14, 'contents', 'Contenidos', '3.1.0.0'),
(15, 'gallery', 'Galería', '3.0.0.0'),
(16, 'menu', 'Menú de la Web', '3.0.0.1'),
(17, 'services', 'Servicios', '3.0.0.2'),
(18, 'singleblog', 'Blog', '3.0.0.1'),
(19, 'slider', 'Slider', '3.0.0.2'),
(20, 'sticker', 'Alertas', '3.0.0.1');
DROP TABLE IF EXISTS `permalinks`;
CREATE TABLE `permalinks` (
  `ID` bigint(20) NOT NULL,
  `Permalink` varchar(200) DEFAULT NULL,
  `TableName` varchar(50) DEFAULT NULL,
  `TableID` bigint(20) DEFAULT '0',
  `ModuleName` varchar(30) DEFAULT NULL,
  `Options` varchar(100) DEFAULT NULL,
  `LastMod` date DEFAULT NULL,
  `Priority` varchar(3) DEFAULT NULL,
  `ChangeFreq` varchar(15) DEFAULT NULL,
  `IDBusiness` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
INSERT INTO `permalinks` (`ID`, `Permalink`, `TableName`, `TableID`, `ModuleName`, `Options`, `LastMod`, `Priority`, `ChangeFreq`, `IDBusiness`) VALUES
(1, 'contenidos', NULL, 0, 'contents', 'action=list', NULL, NULL, NULL, 0),
(2, 'galerias', 'gallery', 0, 'gallery', 'action=list', NULL, NULL, NULL, 0),
(3, 'servicios', '', 0, 'services', 'action=list', NULL, NULL, NULL, 0),
(4, 'blog', '', 0, 'singleblog', 'action=list', NULL, NULL, NULL, 0),
(5, 'contenidos/funciono', 'contents', 1, 'contents', 'action=show', '2016-12-29', NULL, NULL, 0),
(6, 'contactar', '', 0, 'contact', 'action=contact', '2016-12-29', '0.5', 'daily', 0),
(7, 'galeria', '', 0, 'gallery', 'action=list', '2016-12-29', '0.5', 'daily', 0),
(8, 'noticias', 'cats', 1, 'catpages', 'action=list', '2016-12-29', NULL, NULL, 0);
DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
  `ID` bigint(20) NOT NULL,
  `IDBusiness` bigint(20) DEFAULT '0',
  `Title` varchar(250) DEFAULT NULL,
  `Image` varchar(200) DEFAULT NULL,
  `Image2` varchar(200) DEFAULT NULL,
  `Icon` varchar(200) DEFAULT NULL,
  `ShortDescription` mediumtext,
  `LongDescription` longtext,
  `Link` varchar(250) DEFAULT NULL,
  `Geolocation` varchar(200) DEFAULT NULL,
  `Orden` bigint(20) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
DROP TABLE IF EXISTS `services_attachments`;
CREATE TABLE `services_attachments` (
  `ID` bigint(20) NOT NULL,
  `IDFather` bigint(20) DEFAULT NULL,
  `File` varchar(250) DEFAULT NULL,
  `Description` varchar(250) DEFAULT NULL,
  `CounterClick` int(11) DEFAULT NULL,
  `Level` int(3) NOT NULL DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS `services_comments`;
CREATE TABLE `services_comments` (
  `ID` bigint(20) NOT NULL,
  `IDFather` bigint(20) DEFAULT NULL,
  `IDAuthor` int(11) DEFAULT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `Email` varchar(150) DEFAULT NULL,
  `Comment` mediumtext,
  `DatePublish` datetime DEFAULT NULL,
  `Points` int(1) NOT NULL DEFAULT '3',
  `Orden` bigint(20) DEFAULT '0',
  `Active` int(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS `services_images`;
CREATE TABLE `services_images` (
  `ID` bigint(20) NOT NULL,
  `IDFather` bigint(20) DEFAULT NULL,
  `Image` varchar(250) DEFAULT NULL,
  `Description` varchar(250) DEFAULT NULL,
  `Level` int(3) NOT NULL DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0',
  `Link` varchar(250) DEFAULT NULL,
  `Download` varchar(250) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS `services_links`;
CREATE TABLE `services_links` (
  `ID` bigint(20) NOT NULL,
  `IDFather` bigint(20) DEFAULT NULL,
  `Link` varchar(250) DEFAULT NULL,
  `Description` varchar(250) DEFAULT NULL,
  `CounterClick` int(11) DEFAULT NULL,
  `Level` int(3) DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=FIXED;
DROP TABLE IF EXISTS `services_translations`;
CREATE TABLE `services_translations` (
  `ID` bigint(20) NOT NULL,
  `IDOriginal` bigint(20) DEFAULT NULL,
  `LangCode` varchar(3) DEFAULT NULL,
  `Title` varchar(250) DEFAULT NULL,
  `ShortDescription` mediumtext,
  `LongDescription` longtext
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
DROP TABLE IF EXISTS `services_videos`;
CREATE TABLE `services_videos` (
  `ID` bigint(20) NOT NULL,
  `IDFather` bigint(20) DEFAULT NULL,
  `Embed` mediumtext,
  `Description` varchar(250) DEFAULT NULL,
  `Level` int(3) DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=FIXED;
DROP TABLE IF EXISTS `singleblog`;
CREATE TABLE `singleblog` (
  `ID` bigint(20) NOT NULL,
  `IDBusiness` bigint(20) DEFAULT '0',
  `Title` varchar(250) DEFAULT NULL,
  `Image` varchar(200) DEFAULT NULL,
  `Image2` varchar(200) DEFAULT NULL,
  `ShortDescription` mediumtext,
  `LongDescription` longtext,
  `Link` varchar(250) DEFAULT NULL,
  `Geolocation` varchar(200) DEFAULT NULL,
  `DatePublish` date DEFAULT NULL,
  `Orden` bigint(20) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
DROP TABLE IF EXISTS `singleblog_attachments`;
CREATE TABLE `singleblog_attachments` (
  `ID` bigint(20) NOT NULL,
  `IDFather` bigint(20) DEFAULT NULL,
  `File` varchar(250) DEFAULT NULL,
  `Description` varchar(250) DEFAULT NULL,
  `CounterClick` int(11) DEFAULT NULL,
  `Level` int(3) NOT NULL DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS `singleblog_comments`;
CREATE TABLE `singleblog_comments` (
  `ID` bigint(20) NOT NULL,
  `IDFather` bigint(20) DEFAULT NULL,
  `IDAuthor` int(11) DEFAULT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `Email` varchar(150) DEFAULT NULL,
  `Comment` mediumtext,
  `DatePublish` datetime DEFAULT NULL,
  `Points` int(1) NOT NULL DEFAULT '3',
  `Orden` bigint(20) DEFAULT '0',
  `Active` int(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS `singleblog_images`;
CREATE TABLE `singleblog_images` (
  `ID` bigint(20) NOT NULL,
  `IDFather` bigint(20) DEFAULT NULL,
  `Image` varchar(250) DEFAULT NULL,
  `Description` varchar(250) DEFAULT NULL,
  `Level` int(3) NOT NULL DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0',
  `Link` varchar(250) DEFAULT NULL,
  `Download` varchar(250) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=DYNAMIC;
DROP TABLE IF EXISTS `singleblog_links`;
CREATE TABLE `singleblog_links` (
  `ID` bigint(20) NOT NULL,
  `IDFather` bigint(20) DEFAULT NULL,
  `Link` varchar(250) DEFAULT NULL,
  `Description` varchar(250) DEFAULT NULL,
  `CounterClick` int(11) DEFAULT NULL,
  `Level` int(3) DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=FIXED;
DROP TABLE IF EXISTS `singleblog_translations`;
CREATE TABLE `singleblog_translations` (
  `ID` bigint(20) NOT NULL,
  `IDOriginal` bigint(20) DEFAULT NULL,
  `LangCode` varchar(5) DEFAULT NULL,
  `Title` varchar(250) DEFAULT NULL,
  `ShortDescription` mediumtext,
  `LongDescription` longtext
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
DROP TABLE IF EXISTS `singleblog_videos`;
CREATE TABLE `singleblog_videos` (
  `ID` bigint(20) NOT NULL,
  `IDFather` bigint(20) DEFAULT NULL,
  `Embed` mediumtext,
  `Description` varchar(250) DEFAULT NULL,
  `Level` int(3) DEFAULT '0',
  `Orden` bigint(20) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 ROW_FORMAT=FIXED;
DROP TABLE IF EXISTS `slider`;
CREATE TABLE `slider` (
  `ID` bigint(20) NOT NULL,
  `IDBusiness` bigint(20) DEFAULT '0',
  `Name` varchar(100) DEFAULT NULL,
  `Description` mediumtext,
  `Image` varchar(100) DEFAULT NULL,
  `DatePublish` date DEFAULT NULL,
  `DateExpire` date DEFAULT NULL,
  `ShowButton` tinyint(1) DEFAULT '1',
  `TextButton` varchar(200) DEFAULT NULL,
  `URL` varchar(250) DEFAULT NULL,
  `ShowTitle` tinyint(1) DEFAULT '1',
  `ShowDescription` tinyint(1) DEFAULT '1',
  `Orden` bigint(20) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
INSERT INTO `slider` (`ID`, `IDBusiness`, `Name`, `Description`, `Image`, `DatePublish`, `DateExpire`, `ShowButton`, `TextButton`, `URL`, `ShowTitle`, `ShowDescription`, `Orden`) VALUES
(1, 0, 'Borra o edita esta diapositiva', NULL, 'slider-1-image-borra-o-edita-esta-diapositiva.QVW.png', '2016-12-01', '2100-01-01', 1, NULL, '', 1, 0, 1);
DROP TABLE IF EXISTS `sticker`;
CREATE TABLE `sticker` (
  `ID` bigint(20) NOT NULL,
  `Name` varchar(250) DEFAULT NULL,
  `Description` mediumtext,
  `Type` varchar(20) DEFAULT NULL,
  `Active` int(1) DEFAULT '1',
  `DatePublish` date DEFAULT NULL,
  `DateExpire` date DEFAULT NULL,
  `URL` varchar(250) DEFAULT NULL,
  `Orden` bigint(20) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
DROP TABLE IF EXISTS `sticker_translations`;
CREATE TABLE `sticker_translations` (
  `ID` bigint(20) NOT NULL,
  `IDOriginal` bigint(20) NOT NULL,
  `LangCode` varchar(5) DEFAULT NULL,
  `Name` varchar(250) DEFAULT NULL,
  `Description` mediumtext
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
ALTER TABLE `cats`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDBusiness` (`IDBusiness`),
  ADD KEY `IDFather` (`IDFather`),
  ADD KEY `IDAuthor` (`IDAuthor`),
  ADD KEY `Orden` (`Orden`);
ALTER TABLE `cats_attachments`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDFather` (`IDFather`),
  ADD KEY `Orden` (`Orden`);
ALTER TABLE `cats_comments`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDFather` (`IDFather`),
  ADD KEY `IDAuthor` (`IDAuthor`),
  ADD KEY `Orden` (`Orden`);
ALTER TABLE `cats_images`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDFather` (`IDFather`),
  ADD KEY `Orden` (`Orden`);
ALTER TABLE `cats_links`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDFather` (`IDFather`),
  ADD KEY `Orden` (`Orden`);
ALTER TABLE `cats_pages`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDBusiness` (`IDBusiness`),
  ADD KEY `IDFather` (`IDFather`),
  ADD KEY `Title` (`Title`),
  ADD KEY `DatePublish` (`DatePublish`),
  ADD KEY `DateExpire` (`DateExpire`),
  ADD KEY `IDAuthor` (`IDAuthor`),
  ADD KEY `Orden` (`Orden`);
ALTER TABLE `cats_pages_translations`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDOriginal` (`IDOriginal`),
  ADD KEY `LangCode` (`LangCode`);
ALTER TABLE `cats_related`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDFather` (`IDFather`),
  ADD KEY `Orden` (`Orden`);
ALTER TABLE `cats_translations`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDOriginal` (`IDOriginal`),
  ADD KEY `LangCode` (`LangCode`);
ALTER TABLE `cats_videos`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDFather` (`IDFather`),
  ADD KEY `Orden` (`Orden`);
ALTER TABLE `contents`
  ADD PRIMARY KEY (`ID`);
ALTER TABLE `contents_attachments`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDFather` (`IDFather`),
  ADD KEY `Orden` (`Orden`);
ALTER TABLE `contents_comments`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDFather` (`IDFather`),
  ADD KEY `IDAuthor` (`IDAuthor`),
  ADD KEY `Orden` (`Orden`);
ALTER TABLE `contents_images`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDFather` (`IDFather`),
  ADD KEY `Orden` (`Orden`);
ALTER TABLE `contents_links`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDFather` (`IDFather`),
  ADD KEY `Orden` (`Orden`);
ALTER TABLE `contents_translations`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDOriginal` (`IDOriginal`),
  ADD KEY `LangCode` (`LangCode`);
ALTER TABLE `contents_videos`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDFather` (`IDFather`),
  ADD KEY `Orden` (`Orden`);
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDBusiness` (`IDBusiness`),
  ADD KEY `Title` (`Title`),
  ADD KEY `DatePublish` (`DatePublish`),
  ADD KEY `Orden` (`Orden`);
ALTER TABLE `gallery_images`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDFather` (`IDFather`),
  ADD KEY `IDBusiness` (`IDBusiness`),
  ADD KEY `Orden` (`Orden`);
ALTER TABLE `gallery_translations`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDOriginal` (`IDOriginal`),
  ADD KEY `LangCode` (`LangCode`);
ALTER TABLE `menu`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDBusiness` (`IDBusiness`),
  ADD KEY `IDFather` (`IDFather`),
  ADD KEY `Orden` (`Orden`);
ALTER TABLE `menu_translations`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDOriginal` (`IDOriginal`),
  ADD KEY `LangCode` (`LangCode`);
ALTER TABLE `modules_config`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `userID` (`UserID`),
  ADD KEY `Module` (`Module`),
  ADD KEY `ParamName` (`ParamName`);
ALTER TABLE `modules_installed`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Module` (`Module`);
ALTER TABLE `permalinks`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Permalink` (`Permalink`),
  ADD KEY `TableName` (`TableName`),
  ADD KEY `TableID` (`TableID`),
  ADD KEY `ModuleName` (`ModuleName`),
  ADD KEY `IDBusiness` (`IDBusiness`);
ALTER TABLE `services`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDBusiness` (`IDBusiness`),
  ADD KEY `Orden` (`Orden`);
ALTER TABLE `services_attachments`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDFather` (`IDFather`),
  ADD KEY `Orden` (`Orden`);
ALTER TABLE `services_comments`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDFather` (`IDFather`),
  ADD KEY `IDAuthor` (`IDAuthor`),
  ADD KEY `Orden` (`Orden`);
ALTER TABLE `services_images`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDFather` (`IDFather`),
  ADD KEY `Orden` (`Orden`);
ALTER TABLE `services_links`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDFather` (`IDFather`),
  ADD KEY `Orden` (`Orden`);
ALTER TABLE `services_translations`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDOriginal` (`IDOriginal`),
  ADD KEY `LangCode` (`LangCode`);
ALTER TABLE `services_videos`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDFather` (`IDFather`),
  ADD KEY `Orden` (`Orden`);
ALTER TABLE `singleblog`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDBusiness` (`IDBusiness`),
  ADD KEY `Orden` (`Orden`);
ALTER TABLE `singleblog_attachments`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDFather` (`IDFather`),
  ADD KEY `Orden` (`Orden`);
ALTER TABLE `singleblog_comments`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDFather` (`IDFather`),
  ADD KEY `IDAuthor` (`IDAuthor`),
  ADD KEY `Orden` (`Orden`);
ALTER TABLE `singleblog_images`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDFather` (`IDFather`),
  ADD KEY `Orden` (`Orden`);
ALTER TABLE `singleblog_links`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDFather` (`IDFather`),
  ADD KEY `Orden` (`Orden`);
ALTER TABLE `singleblog_translations`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDOriginal` (`IDOriginal`),
  ADD KEY `LangCode` (`LangCode`);
ALTER TABLE `singleblog_videos`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDFather` (`IDFather`),
  ADD KEY `Orden` (`Orden`);
ALTER TABLE `slider`
  ADD PRIMARY KEY (`ID`);
ALTER TABLE `sticker`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `DatePublish` (`DatePublish`),
  ADD KEY `DateExpire` (`DateExpire`),
  ADD KEY `Orden` (`Orden`);
ALTER TABLE `sticker_translations`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `IDOriginal` (`IDOriginal`),
  ADD KEY `LangCode` (`LangCode`);
ALTER TABLE `cats`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `cats_attachments`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `cats_comments`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `cats_images`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `cats_links`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `cats_pages`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `cats_pages_translations`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `cats_related`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `cats_translations`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `cats_videos`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `contents`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `contents_attachments`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `contents_comments`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `contents_images`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `contents_links`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `contents_translations`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `contents_videos`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `gallery`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `gallery_images`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `gallery_translations`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `menu`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `menu_translations`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `modules_config`
  MODIFY `ID` int(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=293;
ALTER TABLE `modules_installed`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
ALTER TABLE `permalinks`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
ALTER TABLE `services`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `services_attachments`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `services_comments`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `services_images`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `services_links`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `services_translations`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `services_videos`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `singleblog`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `singleblog_attachments`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `singleblog_comments`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `singleblog_images`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `singleblog_links`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `singleblog_translations`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `singleblog_videos`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `slider`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `sticker`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `sticker_translations`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT;
DROP TABLE IF EXISTS `users_roles_permissions`;
CREATE TABLE `users_roles_permissions` (
  `ID` bigint(20) NOT NULL,
  `RolID` int(11) DEFAULT '0',
  `OptionFile` varchar(40) DEFAULT NULL,
  `OptionStatus` int(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
INSERT INTO `users_roles_permissions` (`ID`, `RolID`, `OptionFile`, `OptionStatus`) VALUES
(1, 99, 'users', 1),
(2, 99, 'levels', 1),
(3, 99, 'modules', 1),
(4, 99, 'backup', 1),
(5, 99, 'catpages', 1),
(6, 99, 'config', 1),
(7, 99, 'contact', 1),
(8, 99, 'contents', 1),
(9, 99, 'gallery', 1),
(10, 99, 'menu', 1),
(11, 99, 'core--minimizer', 1),
(12, 99, 'permalinks', 1),
(13, 99, 'services', 1),
(14, 99, 'singleblog', 1),
(15, 99, 'slider', 1),
(16, 99, 'core--socialmedia', 1),
(17, 99, 'sticker', 1),
(18, 99, 'comments', 1),
(19, 99, 'core--locale', 1);
ALTER TABLE `users_roles_permissions`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `RolID` (`RolID`),
  ADD KEY `OptionFile` (`OptionFile`);
ALTER TABLE `users_roles_permissions`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;