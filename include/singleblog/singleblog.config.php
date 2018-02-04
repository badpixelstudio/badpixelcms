<?php
require_once(sitepath . "include/core/config.class.php");

class ConfigSingleBlog extends ConfigModule{
	
	var $columns = array();
	var $name= 'singleblog';
	var $idbusiness = '';
	
   function __construct($empresa=0){
	   	$this->idbusiness=$empresa;

	   	$this->columns['UseImage']= new Config('UseImage','BOOLEAN',true);
	   	$this->columns['ImageOptions']= new Config('ImageOptions','STRING','(images,1200,0);(thumbnails,300,200,crop)');
		$this->columns['UseImage2']= new Config('UseImage2','BOOLEAN',true);
		$this->columns['Image2Options']= new Config('Image2Options','STRING','(images,1200,0);(thumbnails,300,200,crop)');
		$this->columns['UseShortDescription']= new Config('UseShortDescription','BOOLEAN',true);
		$this->columns['UseLongDescription']= new Config('UseLongDescription','BOOLEAN',true);
		$this->columns['UseLink']= new Config('UseLink','BOOLEAN',true);
		$this->columns['UseGeolocation']= new Config('UseGeolocation','BOOLEAN',true);
		$this->columns['UseDates']= new Config('UseDates','BOOLEAN',true);
		$this->columns['UseActivation']= new Config('UseActivation','BOOLEAN',false);

		$this->columns['UseImages']= new Config('UseImages','BOOLEAN',true);
		$this->columns['UseAttachments']= new Config('UseAttachments','BOOLEAN',true);
		$this->columns['UseLinks']= new Config('UseLinks','BOOLEAN',true);
		$this->columns['UseVideos']= new Config('UseVideos','BOOLEAN',true);
		$this->columns['UseComments']= new Config('UseComments','BOOLEAN',true);
		$this->columns['ImagesOptions']= new Config('ImagesOptions','STRING','(images,1200,0);(thumbnails,300,200,crop)');

		$this->columns['UseCats']= new Config('UseCats','BOOLEAN',false);

		$this->columns['PermalinkFolder']= new Config('PermalinkFolder','STRING','blog');
		
		parent::__construct($empresa);
   }
   
}

?>