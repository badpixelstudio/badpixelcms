<?php
require_once(sitepath . "include/core/config.class.php");

class ConfigCore extends ConfigModule{
	
	var $columns = array();
	var $name= 'core';
	var $idbusiness = '';
	
	function __construct($empresa=0){
		
		//Definimos los campos base...
		$this->columns['MaintenanceActive']= new Config('MaintenanceActive','BOOLEAN',false);	
		$this->columns['Title']= new Config('Title','STRING','Nombre Del Sitio');
		$this->columns['Cookie']= new Config('Cookie','STRING','CookieTitle');
		$this->columns['HeadDescription']= new Config('HeadDescription','STRING','Descripción del sitio');
		$this->columns['HeadTags']= new Config('HeadTags','STRING','MetaTags del sitio');
		$this->columns['HeadImage']= new Config('HeadImage','STRING','');
		$this->columns['Copyright']= new Config('Copyright','STRING','&copy Todos los derechos reservados');
		$this->columns['Template']= new Config('Template','STRING','basic');
		$this->columns['TemplateMinResources']= new Config('TemplateMinResources','BOOLEAN',false);
		$this->columns['Lang']= new Config('Lang','STRING','Spanish');
		$this->columns['PanelMinResources']= new Config('PanelMinResources','BOOLEAN',false);
		$this->columns['EnablePrivateMessages']= new Config('EnablePrivateMessages','BOOLEAN',true);
		$this->columns['WYSIWYGParseToClean']= new Config('WYSIWYGParseToClean','BOOLEAN',true);
		$this->columns['WYSIWYGTagsEnabled']= new Config('WYSIWYGTagsEnabled','STRING','<b><strong><i><u><strike><sup><sub><a><embed><iframe><p><q><br><img><div><span><li><ul><ol><h1><h2><h3><h4><h5><h6>');
		$this->columns['WYSIWYGCleanAttributes']= new Config('WYSIWYGCleanAttributes','STRING',"class|lang|style|size|face");
		$this->columns['DebugActive']= new Config('DebugActive','BOOLEAN',false);
		$this->columns['LogAllErrorMessages']= new Config('LogAllErrorMessages','BOOLEAN',false);
		$this->columns['DisplayErrors']= new Config('DisplayErrors','BOOLEAN',true);
		$this->columns['NotifyEmailLogins']= new Config('NotifyEmailLogins','BOOLEAN',false);
		$this->columns['LogLogins']= new Config('LogLogins','BOOLEAN',false);

		$this->columns['BlockOwner']= new Config('BlockOwner','SEPARATOR','Propiedad'); //Añade un separador entre bloques.
		$this->columns['OwnerName']= new Config('OwnerName','STRING','');
		$this->columns['OwnerStreet']= new Config('OwnerStreet','STRING','');
		$this->columns['OwnerZipCode']= new Config('OwnerZipCode','STRING','');
		$this->columns['OwnerCity']= new Config('OwnerCity','STRING','');
		$this->columns['OwnerState']= new Config('OwnerState','STRING','');
		$this->columns['OwnerCountry']= new Config('OwnerCountry','STRING','');
		$this->columns['OwnerPhone']= new Config('OwnerPhone','STRING','');
		$this->columns['OwnerFax']= new Config('OwnerFax','STRING','');
		$this->columns['OwnerPublicEmail']= new Config('OwnerPublicEmail','STRING','');
		$this->columns['OwnerTaxNumber']= new Config('OwnerTaxNumber','STRING','');
		$this->columns['OwnerGeolocation']= new Config('OwnerGeolocation','STRING','');
		$this->columns['StateDefault']= new Config('StateDefault','INTEGER','5');
		$this->columns['StateTextDefault']= new Config('StateTextDefault','STRING','Ávila');
		$this->columns['CityDefault']= new Config('CityDefault','INTEGER','1');
		$this->columns['CityTextDefault']= new Config('CityTextDefault','STRING','Ávila');
		
		$this->columns['BlockMails']= new Config('BlockMails','SEPARATOR','Correos'); //Añade un separador entre bloques.
		$this->columns['MainMail']= new Config('MainMail','STRING','info@dominio.com');
		$this->columns['PasswordsMail']= new Config('PasswordsMail','STRING','no-reply@dominio.com');
		$this->columns['UsePHPMailer']= new Config('UsePHPMailer','BOOLEAN',false);
		$this->columns['FromEmail']= new Config('FromEmail','STRING','Nombre Del Sitio');
		$this->columns['SMTPHost']= new Config('SMTPHost','STRING','smtp.dominio.com');
		$this->columns['SMTPPort']= new Config('SMTPPort','INTEGER','25');
		$this->columns['SMTPUserName']= new Config('SMTPUserName','STRING','email@dominio.com');
		$this->columns['SMTPPassword']= new Config('SMTPPassword','STRING','12345');
		$this->columns['SMTPSecure']= new Config('SMTPSecure','STRING','');
		
		$this->columns['BlockSocial']= new Config('BlockSocial','SEPARATOR','RRSS'); //Añade un separador entre bloques.
		$this->columns['GoogleAnalyticsID']= new Config('GoogleAnalyticsID','STRING','');
		$this->columns['DefaultCommentsEnable']= new Config('DefaultCommentsEnable','BOOLEAN',true);
		$this->columns['EnableCommentsAnonymousUsers']= new Config('EnableCommentsAnonymousUsers','BOOLEAN',true);
		$this->columns['RequireActivationCommentsAnonymousUsers']= new Config('RequireActivationCommentsAnonymousUsers','BOOLEAN',true);
		$this->columns['RequireActivationCommentsLoggedUsers']= new Config('RequireActivationCommentsLoggedUsers','BOOLEAN',false);
		$this->columns['EnableFacebookComments']= new Config('EnableFacebookComments','BOOLEAN',false);
		$this->columns['FacebookURL']= new Config('FacebookURL','STRING','');
		$this->columns['TwitterURL']= new Config('TwitterURL','STRING','');
		$this->columns['GooglePlusURL']= new Config('GooglePlusURL','STRING','');
		$this->columns['YouTubeURL']= new Config('YouTubeURL','STRING','');
		$this->columns['LinkedInURL']= new Config('LinkedInURL','STRING','');
		$this->columns['PinterestURL']= new Config('PinterestURL','STRING','');
		$this->columns['InstagramURL']= new Config('InstagramURL','STRING','');
		$this->columns['SoundCloudURL']= new Config('SoundCloudURL','STRING','');
		$this->columns['FlickrURL']= new Config('FlickrURL','STRING','');

		$this->columns['FacebookEnabled']= new Config('FacebookEnabled','BOOLEAN',false);
		$this->columns['FacebookAppID']= new Config('FacebookAppID','STRING','');
		$this->columns['FacebookAppSecret']= new Config('FacebookAppSecret','STRING','');
		$this->columns['FacebookScope']= new Config('FacebookScope','STRING','email,user_birthday,user_location,user_about_me,publish_stream,manage_pages');
		$this->columns['FacebookAccessToken']= new Config('FacebookAccessToken','STRING','');
		$this->columns['FacebookTokenProfilePublish']= new Config('FacebookTokenProfilePublish','STRING','');
		
		$this->columns['TwitterEnabled']= new Config('TwitterEnabled','BOOLEAN',false);
		$this->columns['TwitterConsumerKey']= new Config('TwitterConsumerKey','STRING','');
		$this->columns['TwitterConsumerSecret']= new Config('TwitterConsumerSecret','STRING','');
		$this->columns['TwitterAccessToken']= new Config('TwitterAccessToken','STRING','');
		$this->columns['TwitterAccessTokenSecret']= new Config('TwitterAccessTokenSecret','STRING','');
		$this->columns['TwitterPreTweet']= new Config('TwitterPreTweet','STRING','');
		$this->columns['TwitterPostTweet']= new Config('TwitterPostTweet','STRING','');
		
		$this->columns['GoogleAPIEnabled']= new Config('GoogleAPIEnabled','BOOLEAN',false);
		$this->columns['GoogleAPIClientID']= new Config('GoogleAPIClientID','STRING','');
		$this->columns['GoogleAPIClientSecret']= new Config('GoogleAPIClientSecret','STRING','');
		$this->columns['GoogleAPIDeveloperKey']= new Config('GoogleAPIDeveloperKey','STRING','');
		$this->columns['GoogleAccount']= new Config('GoogleAccount','STRING','');
		$this->columns['GooglePassword']= new Config('GooglePassword','STRING','');
		$this->columns['GoogleAPIProfile']= new Config('GoogleAPIProfile','STRING','');
		$this->columns['GoogleMapsAPIKey']= new Config('GoogleMapsAPIKey','STRING','');

		$this->columns['SocialMediaCreateAccount']= new Config('SocialMediaCreateAccount','BOOLEAN',false);

		$this->columns['BlockAutoPublish']= new Config('BlockAutoPublish','SEPARATOR','Auto publicación'); //Añade un separador entre bloques.
		$this->columns['AutoSocialMediaParameters']= new Config('AutoSocialMediaParameters','STRING','/all');
		$this->columns['AutoSocialMediaHourFirstPublish']= new Config('AutoSocialMediaHourFirstPublish','STRING','08:30');
		$this->columns['AutoSocialMediaIntervalMinutes']= new Config('AutoSocialMediaIntervalMinutes','INTEGER',30);
		$this->columns['AutoSocialMediaMaxIntervals']= new Config('AutoSocialMediaMaxIntervals','INTEGER',15);
		
		$this->columns['BlockPageSpeed']= new Config('BlockPageSpeed','SEPARATOR','PageSpeed'); //Añade un separador entre bloques.
		$this->columns['ImageTag']= new Config('ImageTag','STRING','');
		$this->columns['ImageLengthKeyCache']= new Config('ImageLengthKeyCache','INTEGER','3');				

		$this->columns['BlockAPI']= new Config('BlockAPI','SEPARATOR','API'); //Añade un separador entre bloques.
		$this->columns['APIRequiresOAuthLogin']= new Config('APIRequiresOAuthLogin','BOOLEAN',false);
		$this->columns['OAuthAccessTokenExpires']= new Config('OAuthAccessTokenExpires','INTEGER','7200');
		$this->columns['OAuthExtendedAccessTokenExpires']= new Config('OAuthExtendedAccessTokenExpires','INTEGER','5184000');

		$this->columns['BlockMobile']= new Config('BlockMobile','SEPARATOR','Móviles'); //Añade un separador entre bloques.
		$this->columns['AppGooglePlayLink']= new Config('AppGooglePlayLink','STRING','');
		$this->columns['AppAppleStoreLink']= new Config('AppAppleStoreLink','STRING','');
		$this->columns['AndroidGSM_APIKey']= new Config('AndroidGSM_APIKey','STRING','');
		$this->columns['FirebaseAPIKey']= new Config('FirebaseAPIKey','STRING','');
		$this->columns['iOSPush_Passphrase']= new Config('iOSPush_Passphrase','STRING','');

		$this->columns['BlockUpdates']= new Config('BlockUpdates','SEPARATOR','Actualizaciones'); //Añade un separador entre bloques.
		$this->columns['CheckUpdates']= new Config('CheckUpdates','BOOLEAN',false);
		$this->columns['EnableModuleInstall']= new Config('EnableModuleInstall','BOOLEAN',false);
		$this->columns['URLUpdatesAPI']= new Config('URLUpdatesAPI','STRING','http://localhost/badpixelcms3/api/v1/modulerepository');

		$this->columns['BlockMulti']= new Config('BlockMulti','SEPARATOR','Entidades'); //Añade un separador entre bloques.
		$this->columns['Multi']= new Config('Multi','BOOLEAN',false);
		$this->columns['LevelRootMulti']= new Config('LevelRootMulti','INTEGER','99');
		$this->columns['MultiConfig']= new Config('MultiConfig','BOOLEAN',true);	

		parent::__construct($empresa);
	}
	
}


?>