<?php

//cargamos el resto de clases
require_once(sitepath . "include/core/config.class.php");

class ConfigMenu extends ConfigModule{
	
	var $columns = array();
	var $name= 'menu';
	var $idbusiness = '';
	
   function __construct(){
	   
	   	$this->idbusiness=0;
		
		//Datos a almacenar de la empresa...
		$this->columns['EnableMultiBusiness']= new Config('EnableMultiBusiness','BOOLEAN',true);
		$this->columns['MaxLevels']= new Config('MaxLevels','INTEGER',3);
		$this->columns['UseImage']= new Config('UseImage','BOOLEAN',false);
		$this->columns['ImageOptions']= new Config('ImageOptions','STRING','(images,800,600);(thumbnails,133,208,crop)');
		$this->columns['UseIcon']= new Config('UseIcon','BOOLEAN',false);
		
		
		parent::__construct($this->idbusiness);
   }

}

?>