<?php

//cargamos el resto de clases
require_once(sitepath . "include/core/config.class.php");

class ConfigGeography extends ConfigModule{
	
	var $columns = array();
	var $name= 'geography';
	var $idbusiness = '';
	
   function __construct(){
	   
	   	$this->idbusiness=0;
		
		//Datos a almacenar de la empresa...
		$this->columns['GeographyUseCountries']= new Config('GeographyUseCountries','BOOLEAN',true);
		$this->columns['GeographyDefaultCountry']= new Config('GeographyDefaultCountry','INTEGER',1);
		
		$this->columns['GeographyUseStates']= new Config('GeographyUseStates','BOOLEAN',true);
		$this->columns['GeographyDefaultState']= new Config('GeographyDefaultState','INTEGER',1);
	   	
		$this->columns['GeographyUseCities']= new Config('GeographyUseCities','BOOLEAN',true);
		$this->columns['GeographyDefaultCity']= new Config('GeographyDefaultCity','INTEGER',1);
		
	   	$this->columns['GeographyUseZones']= new Config('GeographyUseZones','BOOLEAN',true);
		
		parent::__construct($this->idbusiness);
   }

}

?>