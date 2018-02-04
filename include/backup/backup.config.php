<?php
require_once(sitepath . "include/core/config.class.php");

class ConfigBackup extends ConfigModule{
	
	var $columns = array();
	var $name= 'backup';
	var $idbusiness = '';
	
   function __construct(){
	   
	   	$this->idbusiness=0;
		
		//Datos a almacenar de la empresa...
		$this->columns['DefaultFolder']= new Config('DefaultFolder','STRING','backup');
							
		parent::__construct($this->idbusiness);
   }

}

?>