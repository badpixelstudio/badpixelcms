<?php

//cargamos el resto de clases
require_once(sitepath . "include/core/config.class.php");

class ConfigSticker extends ConfigModule {
	
	var $columns = array();
	var $name= 'stickers';
	var $idbusiness = '';
	
   function __construct($empresa=0){
	   	$this->idbusiness=$empresa;
		
		$this->columns['EnableLink']= new Config('EnableLink','BOOLEAN',true);
		$this->columns['EnableType']= new Config('EnableType','BOOLEAN',true);
		$this->columns['UseActivation']= new Config('UseActivation','BOOLEAN',true);
		
		parent::__construct($empresa);
   }
   
}

?>