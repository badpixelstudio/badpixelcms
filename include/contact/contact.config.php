<?php
require_once(sitepath . "include/core/config.class.php");

class ConfigContact extends ConfigModule{
	
	var $columns = array();
	var $name= 'contact';
	var $idbusiness = '';
	
   function __construct($empresa=0){
	   	$this->idbusiness=$empresa;
	   	$this->columns['UseReCaptcha']= new Config('UseReCaptcha','BOOLEAN',false);
	   	$this->columns['ReCaptchaKey']= new Config('ReCaptchaKey','STRING','');
	   	$this->columns['ReCaptchaSecret']= new Config('ReCaptchaSecret','STRING','');
	   	$this->columns['SaveInDatabase']= new Config('SaveInDatabase','BOOLEAN',false);
		$this->columns['PermalinkFolder']= new Config('PermalinkFolder','STRING','contactar');
		
		parent::__construct($empresa);
   }
   
}

?>