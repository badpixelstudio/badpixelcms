<?php

//cargamos el resto de clases
require_once(sitepath . "include/core/config.class.php");

class ConfigCatPages extends ConfigModule{
	
	var $columns = array();
	var $name= 'catpages';
	var $idbusiness = '';
	
   function __construct($empresa=0){
	   	$this->idbusiness=$empresa;
		
	   	//Otros
		$this->columns['EnableMultiBusiness']= new Config('EnableMultiBusiness','BOOLEAN',true);
		$this->columns['UseConfigDefault']= new Config('UseConfigDefault','BOOLEAN',true);

		//CATEGORIAS
		$this->columns['CatUseType']= new Config('CatUseType','BOOLEAN',true);
	   	$this->columns['CatEnableGeneralCreate']= new Config('CatEnableGeneralCreate','BOOLEAN',true);
		$this->columns['CatLevelCreateSub']= new Config('CatLevelCreateSub','INTEGER',2);
		$this->columns['CatLevelAdmin']= new Config('CatLevelAdmin','INTEGER',2);
		$this->columns['CatMaxChildren']= new Config('CatMaxChildren','INTEGER',99);
		//Elementos a guardar de las categorias		
	   	$this->columns['CatEnableDescription']= new Config('CatEnableDescription','BOOLEAN',true);
	   	$this->columns['CatEnableAuthor']= new Config('CatEnableAuthor','BOOLEAN',true);
		$this->columns['CatEnableImage']= new Config('CatEnableImage','BOOLEAN',true);
		$this->columns['CatEnableImageAlign']= new Config('CatEnableImageAlign','BOOLEAN',true);	
		$this->columns['PagesCatImageOptions']= new Config('PagesCatImageOptions','STRING','(images,800,600);(thumbnails,133,208,crop)');
		$this->columns['BlockPages']= new Config('BlockPages','SEPARATOR','Páginas'); //Añade un separador entre bloques.
		//PAGINAS
		$this->columns['PageLevelAccess']= new Config('PageLevelAccess','INTEGER',0);
		$this->columns['PageLevelAdmin']= new Config('PageLevelAdmin','INTEGER',2);
		//Elementos a guardar de las páginas
		$this->columns['PageUsePreTitle']= new Config('PageUsePreTitle','BOOLEAN',true);
		$this->columns['PageUsePostTitle']= new Config('PageUsePostTitle','BOOLEAN',false);
		$this->columns['PageUseSummary']= new Config('PageUseSummary','BOOLEAN',true);
		$this->columns['PageUseFirstImage']= new Config('PageUseFirstImage','BOOLEAN',true);
		$this->columns['PageUseFirstImageAlign']= new Config('PageUseFirstImageAlign','BOOLEAN',false);
		$this->columns['PageUseDates']= new Config('PageUseDates','BOOLEAN',true);
		$this->columns['PageUseAuthorInfo']= new Config('PageUseAuthorInfo','BOOLEAN',true);
		$this->columns['PageUseTags']= new Config('PageUseTags','BOOLEAN',true);
		$this->columns['PageUseActivation']= new Config('PageUseActivation','BOOLEAN',true); 
		$this->columns['PageUseReadings']= new Config('PageUseReadings','BOOLEAN',true); 
		$this->columns['PageUseSocial']= new Config('PageUseSocial','BOOLEAN',true); 
		$this->columns['PageUseGeolocation']= new Config('PageUseGeolocation','BOOLEAN',false);  
		$this->columns['PageUseImages']= new Config('PageUseImages','BOOLEAN',true);
		$this->columns['PageUseAttachments']= new Config('PageUseAttachments','BOOLEAN',true);
		$this->columns['PageUseLinks']= new Config('PageUseLinks','BOOLEAN',true);
		$this->columns['PageUseVideos']= new Config('PageUseVideos','BOOLEAN',true);
		$this->columns['PageUseComments']= new Config('PageUseComments','BOOLEAN',true);
		//Tamaño estándar imagen principal		
		$this->columns['PageFirstImageOptions']= new Config('PageFirstImageOptions','STRING','(images,800,600);(thumbnails,133,208,crop)');
		$this->columns['PageImagesOptions']= new Config('PageImagesOptions','STRING','(images,800,600);(thumbnails,87,87,crop)');

		$this->columns['PermalinkFolder'] = new Config("PermalinkFolder","STRING","");

		
		parent::__construct($empresa);
   }
   
}

?>