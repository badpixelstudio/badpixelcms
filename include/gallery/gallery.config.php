<?php

//cargamos el resto de clases
require_once(sitepath . "include/core/config.class.php");

class ConfigGallery extends ConfigModule{
	
	var $columns = array();
	var $name= 'gallery';
	var $idbusiness = '';
	
   function __construct($empresa=0){
	   	$this->idbusiness=$empresa;
		
		$this->columns['EnableMultiBusiness']= new Config('EnableMultiBusiness','BOOLEAN',true);
		$this->columns['UseConfigDefault']= new Config('UseConfigDefault','BOOLEAN',false);		
		
		//Datos a almacenar de la empresa...
		$this->columns['EnableDeleteOldGalleries']= new Config('EnableDeleteOldGalleries','BOOLEAN',true);
	   	$this->columns['GalleryEnableDescription']= new Config('GalleryEnableDescription','BOOLEAN',true);
		$this->columns['GalleryEnableAuthor']= new Config('GalleryEnableAuthor','BOOLEAN',true);
		$this->columns['GalleryEnableDate']= new Config('GalleryEnableDate','BOOLEAN',true);
		$this->columns['GalleryEnableGenImage']= new Config('GalleryEnableGenImage','BOOLEAN',true);
		$this->columns['GalleryEnableLastUpdate']= new Config('GalleryEnableLastUpdate','BOOLEAN',true);
		$this->columns['GalleryEnableAutoGenThumb']= new Config('GalleryEnableAutoGenThumb','BOOLEAN',false);
		$this->columns['GalleryAutoGenThumb']= new Config('GalleryAutoGenThumb','BOOLEAN',true);
		
		$this->columns['ExtrasImagesEnableLink']= new Config('ExtrasImagesEnableLink','BOOLEAN',true);
		$this->columns['ExtrasImagesEnableDownload']= new Config('ExtrasImagesEnableDownload','BOOLEAN',true);		
		$this->columns['GalleryEnableActivation']= new Config('GalleryEnableActivation','BOOLEAN',true);

		$this->columns['ImageOptions']= new Config('ImageOptions','STRING','(images,800,600);(thumbnails,200,150,crop)');	
		$this->columns['ImagesOptions']= new Config('ImagesOptions','STRING','(images,800,600);(thumbnails,200,80-300,crop)');

		$this->columns['PermalinkFolder']= new Config('PermalinkFolder','STRING','multigalerias');

		parent::__construct($empresa);
   }
   
}

?>