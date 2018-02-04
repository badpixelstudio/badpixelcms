<?php
// Configuración del slider 
// Creado por Israel García Sáez para BadPixel.
// Revisión: 1.0 de 1 de junio de 2012, por Israel Garcia.

//cargamos el resto de clases
require_once(sitepath . "include/core/config.class.php");

class ConfigSlider extends ConfigModule{
	
	var $columns = array();
	var $name= 'slider';
	var $idbusiness = '';
	
   function __construct($empresa=0){
	   	$this->idbusiness=$empresa;

	   	$this->columns['Width']= new Config('Width','INTEGER',943);
	   	$this->columns['Height']= new Config('Height','INTEGER',300);
		$this->columns['EnableText']= new Config('EnableText','BOOLEAN',false);
		$this->columns['EnableLink']= new Config('EnableLink','BOOLEAN',true);
		$this->columns['EnableShowButton']= new Config('EnableShowButton','BOOLEAN',false);
		$this->columns['EnableTextButton']= new Config('EnableTextButton','BOOLEAN',false);
		$this->columns['EnableShowText']= new Config('EnableShowText','BOOLEAN',true);
		$this->columns['EnableShowDescription']= new Config('EnableShowDescription','BOOLEAN',true);
		$this->columns['MultiBusiness']= new Config('MultiBusiness','BOOLEAN',false);	
		
		parent::__construct($empresa);
   }
   
}

?>