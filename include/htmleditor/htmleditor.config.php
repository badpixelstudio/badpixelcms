<?php

//cargamos el resto de clases
require_once(sitepath . "include/core/config.class.php");

class ConfigHTMLEditor extends ConfigModule{
	
	var $columns = array();
	var $name= 'files';
	var $idbusiness = '';
	
   function __construct(){
	   
	   	$this->idbusiness=0;
		
		//Datos a almacenar de la empresa...
		$this->columns['DefaultFolder']= new Config('DefaultFolder','STRING','public/html');
							
		parent::__construct($this->idbusiness);
   }

}

?>