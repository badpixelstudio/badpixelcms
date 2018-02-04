<?php
//cargamos el resto de clases
require_once(sitepath . "include/core/config.class.php");

class ConfigApps extends ConfigModule{
	
	var $columns = array();
	var $name= 'apps';
	var $idbusiness = '';
	
   function __construct($empresa=0){
	   	$this->idbusiness=0;
		$this->columns['PermalinkFolder']= new Config('PermalinkFolder','STRING','apps');
		$this->columns['UseCallbackURL']= new Config('UseCallbackURL','BOOLEAN',true);
		$this->columns['UseAppImage']= new Config('UseAppImage','BOOLEAN',true);
		$this->columns['ImageOptions']= new Config('ImageOptions','STRING','(images,1200,0);(thumbnails,200,200,crop)');
		$this->columns['UseOrganizationData']= new Config('UseOrganizationData','BOOLEAN',true);
		$this->columns['UsePermissions']= new Config('UsePermissions','BOOLEAN',true);
		$this->columns['EnableUserLink']= new Config('EnableUserLink','BOOLEAN',true);
		$this->columns['EnableBusinessLink']= new Config('EnableBusinessLink','BOOLEAN',true);
		
		$this->columns['EnableOAuthUsers']= new Config('EnableOAuthUsers','BOOLEAN',true);
		
		parent::__construct($this->idbusiness);
   }

}

?>