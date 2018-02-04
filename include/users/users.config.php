<?php

require_once(sitepath . "include/core/config.class.php");

class ConfigUsers extends ConfigModule{
	
	var $columns = array();
	var $name= 'users';
	var $idbusiness = '';
	
   function __construct($empresa=0){
	   
	   	$this->idbusiness=$empresa;

		//Datos a almacenar del usuario...
	   	$this->columns['UserLanguage']= new Config('UserLanguage','BOOLEAN',true);
	   	$this->columns['Username']= new Config('Username','BOOLEAN',false);
	   	$this->columns['UserNIF']= new Config('UserNIF','BOOLEAN',false);
	   	$this->columns['UserStreet']= new Config('UserStreet','BOOLEAN',false);
	   	$this->columns['UserStreetNum']= new Config('UserStreetNum','BOOLEAN',false);
	   	$this->columns['UserStreetOtherData']= new Config('UserStreetOtherData','BOOLEAN',false);
	   	$this->columns['UserZipCode']= new Config('UserZipCode','BOOLEAN',false);
	   	$this->columns['UserCity']= new Config('UserCity','BOOLEAN',false);
	   	$this->columns['UserState']= new Config('UserState','BOOLEAN',false);
	   	$this->columns['UserCountry']= new Config('UserCountry','BOOLEAN',false);
	   	$this->columns['UserPhone']= new Config('UserPhone','BOOLEAN',false);
	   	$this->columns['UserFax']= new Config('UserFax','BOOLEAN',false);
	   	$this->columns['UserPublicEmail']= new Config('UserPublicEmail','BOOLEAN',false);
	   	$this->columns['UserWeb']= new Config('UserWeb','BOOLEAN',false);
	   	$this->columns['UserSignature']= new Config('UserSignature','BOOLEAN',false);
	   	$this->columns['UserBirthdate']= new Config('UserBirthdate','BOOLEAN',false);
		
		$this->columns['BlockConfig']= new Config('BlockConfig','SEPARATOR','Config'); //Añade un separador entre bloques.
		//URL Base a la que se redirigirán los correos...
	   	$this->columns['UserURLLinkMails']= new Config('UserURLLinkMails','STRING','web');
		//Configuración general...
	   	$this->columns['UserCreate']= new Config('UserCreate','BOOLEAN',false);
	   	$this->columns['UserDelete']= new Config('UserDelete','BOOLEAN',true);
	   	$this->columns['UserAutoActive']= new Config('UserAutoActive','BOOLEAN',false);
	   	$this->columns['UserPassMinLength']= new Config('UserPassMinLength','INTEGER',8);
	   	$this->columns['UserRetrievePass']= new Config('UserRetrievePass','BOOLEAN',true);
	   	$this->columns['UserNotifyPM']= new Config('UserNotifyPM','BOOLEAN',true);
	   	$this->columns['UserDaysToAutoDeletePM']= new Config('UserDaysToAutoDeletePM','INTEGER',90);
		
		//Permisos de SECURITY (aplicables sólo si están habilitadas las funciones generales)
	   	$this->columns['UserSecurityCreate']= new Config('UserSecurityCreate','BOOLEAN',false);
	   	$this->columns['UserSecurityEdit']= new Config('UserSecurityEdit','BOOLEAN',true);
		
		//Permisos de USER - Parte Pública (aplicables sólo si están habilitadas las funciones generales)
	   	$this->columns['UserFrontEndCreate']= new Config('UserFrontEndCreate','BOOLEAN',true);
	   	$this->columns['UserFrontEndEdit']= new Config('UserFrontEndEdit','BOOLEAN',true);
		
		$this->columns['BlockView']= new Config('BlockView','SEPARATOR','Vista'); //Añade un separador entre bloques.
		$this->columns['UserTemplate']= new Config('UserTemplate','BOOLEAN',true);
	   	$this->columns['UseDateExpire']= new Config('UseDateExpire','BOOLEAN',false);
	   	$this->columns['UserViewExtended']= new Config('UserViewExtended','BOOLEAN',true);
	   	$this->columns['UserViewFBData']= new Config('UserViewFBData','BOOLEAN',true);
	   	$this->columns['UserViewDevices']= new Config('UserViewDevices','BOOLEAN',true);
		//Captchas
	   	$this->columns['UserCreateCaptcha']= new Config('UserCreateCaptcha','BOOLEAN',false);
	   	$this->columns['UserEditCaptcha']= new Config('UserEditCaptcha','BOOLEAN',false);
	   	$this->columns['UserActivateCaptcha']= new Config('UserActivateCaptcha','BOOLEAN',false);
	   	$this->columns['UserChangePassCaptcha']= new Config('UserChangePassCaptcha','BOOLEAN',false);
	   	$this->columns['UserLoginCaptcha']= new Config('UserLoginCaptcha','BOOLEAN',false);
	   	$this->columns['UserContactCaptcha']= new Config('UserContactCaptcha','BOOLEAN',false);
		
		
		//Valores para generar Thumbnails
		$this->columns['BlockAvatar']= new Config('BlockAvatar','SEPARATOR','Avatar'); //Añade un separador entre bloques.
		$this->columns['UserAvatar']= new Config('UserAvatar','BOOLEAN',true);
		$this->columns['UserAvatarOptions']= new Config('UserAvatarOptions','STRING','(avatar_original,600,0);(avatar,170,170,crop)');

	   	$this->columns['BlockInvoice']= new Config('BlockInvoice','SEPARATOR','Facturación'); //Añade un separador entre bloques.
	   	$this->columns['UseInvoiceNIF']= new Config('UseInvoiceNIF','BOOLEAN',false);
	   	$this->columns['UseInvoiceName']= new Config('UseInvoiceName','BOOLEAN',false);
	   	$this->columns['UseInvoiceStreet']= new Config('UseInvoiceStreet','BOOLEAN',false);
	   	$this->columns['UseInvoiceZipCode']= new Config('UseInvoiceZipCode','BOOLEAN',false);
	   	$this->columns['UseInvoiceCity']= new Config('UseInvoiceCity','BOOLEAN',false);
	   	$this->columns['UseInvoiceState']= new Config('UseInvoiceState','BOOLEAN',false);
	   	$this->columns['UseInvoiceCountry']= new Config('UseInvoiceCountry','BOOLEAN',false);
	   	$this->columns['UseInvoicePhone']= new Config('UseInvoicePhone','BOOLEAN',false);
	   	$this->columns['UseInvoiceEmail']= new Config('UseInvoiceEmail','BOOLEAN',false);
	   	$this->columns['UseInvoiceBankName']= new Config('UseInvoiceBankName','BOOLEAN',false);
	   	$this->columns['UseInvoiceBankSwiftCode']= new Config('UseInvoiceBankSwiftCode','BOOLEAN',false);
	   	$this->columns['UseInvoiceBankAccount']= new Config('UseInvoiceBankAccount','BOOLEAN',false);
	   	$this->columns['UseInvoiceBankOwner']= new Config('UseInvoiceBankOwner','BOOLEAN',false);
		
		parent::__construct($empresa);
   }

}

?>