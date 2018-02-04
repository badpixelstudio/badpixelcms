<?php
// Configuración de Empresas 
// Creado por Israel García Sáez para BadPixel.
// Revisión: 1.0 de 1 de Junio de 2012, por Israel Garcia.
// Revisión: 1.0 de 14 de Agosto de 2012, por Israel Garcia
//		Añadida configuración de extras

//cargamos el resto de clases
require_once(sitepath . "include/core/config.class.php");

class ConfigBusiness extends ConfigModule{
	
	var $columns = array();
	var $name= 'business';
	var $idbusiness = '';
	
   function __construct(){
	   
	   	$this->idbusiness=0;

	   	$this->columns['BusinessUsePackage']= new Config('BusinessUsePackage','BOOLEAN',true);
	   	$this->columns['BusinessUseAttributes']= new Config('BusinessUseAttributes','BOOLEAN',true);
	   	$this->columns['BusinessUseStandarizedLocationData']= new Config('BusinessUseStandarizedLocationData','BOOLEAN',true);
		$this->columns['BusinessUseState']= new Config('BusinessUseState','BOOLEAN',true);
		$this->columns['BusinessUseCity']= new Config('BusinessUseCity','BOOLEAN',true);
		$this->columns['BusinessUseZone']= new Config('BusinessUseZone','BOOLEAN',true);
		$this->columns['BusinessUseEmailContact']= new Config('BusinessUseEmailContact','BOOLEAN',true);
		$this->columns['BusinessUseWeb']= new Config('BusinessUseWeb','BOOLEAN',true);
		$this->columns['BusinessUseFacebook']= new Config('BusinessUseFacebook','BOOLEAN',true);
		$this->columns['BusinessUseTwitter']= new Config('BusinessUseTwitter','BOOLEAN',true);
		$this->columns['BusinessUseGooglePlus']= new Config('BusinessUseGooglePlus','BOOLEAN',false);
		$this->columns['BusinessUseGoogleMaps']= new Config('BusinessUseGoogleMaps','BOOLEAN',true);
		$this->columns['BusinessUseImage']= new Config('BusinessUseImage','BOOLEAN',true);
		$this->columns['BusinessUseLogo']= new Config('BusinessUseLogo','BOOLEAN',true);
		$this->columns['BusinessUseSlogan']= new Config('BusinessUseSlogan','BOOLEAN',true);
		$this->columns['BusinessUseTimeTable']= new Config('BusinessUseTimeTable','BOOLEAN',true);
		$this->columns['BusinessUseDescription']= new Config('BusinessUseDescription','BOOLEAN',true);
		$this->columns['BusinessUseTags']= new Config('BusinessUseTags','BOOLEAN',true);
		$this->columns['BusinessUseAccessHandicapped']= new Config('BusinessUseAccessHandicapped','BOOLEAN',true);
		$this->columns['BusinessUseWifi']= new Config('BusinessUseWifi','BOOLEAN',true);
		$this->columns['BusinessUseAdmitCreditCard']= new Config('BusinessUseAdmitCreditCard','BOOLEAN',true);
		$this->columns['BusinessUsePriceMedium']= new Config('BusinessUsePriceMedium','BOOLEAN',true);
	   	$this->columns['BusinessImageOptions']= new Config('BusinessImageOptions','STRING','(images,1024,0);(medium,175,260,crop);(thumbnails,90,90,crop)');
		$this->columns['BusinessLogoOptions']= new Config('BusinessLogoOptions','STRING','(images,800,600);(thumbnails,200,150,crop)');
		$this->columns['BusinessUseBillingData']= new Config('BusinessUseBillingData','BOOLEAN',true);
		$this->columns['BusinessUseCloudFiles']= new Config('BusinessUseCloudFiles','BOOLEAN',true);
		$this->columns['BusinessUseActivation']= new Config('BusinessUseActivation','BOOLEAN',true);
		$this->columns['BusinessUseDrafted']= new Config('BusinessUseDrafted','BOOLEAN',true);
		$this->columns['BusinessNewUserDefaultRol']= new Config('BusinessNewUserDefaultRol','INTEGER',1);
		$this->columns['UseRoles']= new Config('UseRoles','BOOLEAN',false);
		$this->columns['EnableConfigModules']= new Config('EnableConfigModules','BOOLEAN',true);
		$this->columns['EnableTimeTable']= new Config('EnableTimeTable','BOOLEAN',false);
		
		$this->columns['BlockXtras']= new Config('BlockXtras','SEPARATOR','Extras'); //Añade un separador entre bloques.
		$this->columns['UseImages']= new Config('UseImages','BOOLEAN',true);
		$this->columns['UseAttachments']= new Config('UseAttachments','BOOLEAN',true);
		$this->columns['UseLinks']= new Config('UseLinks','BOOLEAN',true);
		$this->columns['UseVideos']= new Config('UseVideos','BOOLEAN',true);
		$this->columns['ImagesOptions']= new Config('ImagesOptions','STRING','(images,800,600);(thumbnails,86,86,crop)');
		$this->columns['AttachmentsUseExternalLinks']= new Config('AttachmentsUseExternalLinks','BOOLEAN',true);	
		$this->columns['AttachmentsViewCounter']= new Config('AttachmentsViewCounter','BOOLEAN',true);

		$this->columns['BlockCRM']= new Config('BlockCRM','SEPARATOR','CRM'); //Añade un separador entre bloques.
		$this->columns['CRMUseAttachments']= new Config('CRMUseAttachments','BOOLEAN',true);
		$this->columns['UseBasicSMTP']= new Config('UseBasicSMTP','BOOLEAN',true);
		$this->columns['UseMailRelay']= new Config('UseMailRelay','BOOLEAN',false);
		$this->columns['MailRelayDomain']= new Config('MailRelayDomain','STRING',"");
		$this->columns['MailRelayApiKey']= new Config('MailRelayApiKey','STRING',"");
		$this->columns['MailRelayFromID']= new Config('MailRelayFromID','STRING',"");
		$this->columns['MailRelayReplyID']= new Config('MailRelayReplyID','STRING',"");
		$this->columns['MailRelayReportID']= new Config('MailRelayReportID','STRING',"");
		$this->columns['MailRelayPackageID']= new Config('MailRelayPackageID','STRING',"6");
		$this->columns['MailRelayGroupID']= new Config('MailRelayGroupID','STRING',"1");
		$this->columns['MailRelayCampaingFolderID']= new Config('MailRelayCampaingFolderID','STRING',"1");

		$this->columns['BlockAdv']= new Config('BlockAdv','SEPARATOR','Gestión'); //Añade un separador entre bloques.
		$this->columns['DefaultActiveModules']= new Config('DefaultActiveModules','STRING','_data,_images,_attachments,_links,_videos');
		$this->columns['PermalinkFolder']= new Config('PermalinkFolder','STRING','empresas');
		
		parent::__construct($this->idbusiness);
   }

}

?>