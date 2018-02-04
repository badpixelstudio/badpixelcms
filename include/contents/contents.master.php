<?php
require_once(sitepath . "include/core/core.class.php");
require_once(sitepath . "include/contents/contents.config.php");
require_once(sitepath . "include/extras/images.class.php");
require_once(sitepath . "include/extras/attachments.class.php");
require_once(sitepath . "include/extras/links.class.php");
require_once(sitepath . "include/extras/videos.class.php");
require_once(sitepath . "include/extras/comments.class.php");
require_once(sitepath . "lib/images/thumbs.php");	

class MasterContents extends Core{
	var $title = 'Contenidos';
	var $module = 'contents';
	var $class = 'contents';
	var $table = 'contents';
	var $typemodule='contents';
	var $InstallAdminMenu=array(array('Block' => 'contents', 'Icon' => 'fa-archive'));
	var $tables_required=array('contents','contents_attachments','contents_comments','contents_images','contents_links','contents_translations','contents_videos');
	var $FieldsOfImages=array("Image"=>"ImageOptions");
	var $version="3.1.0.0";

	function __construct($values) {
		parent::__construct($values);
		$this->conf = new ConfigContents($this->businessID);
		$this->BreadCrumb[$this->title]=$this->module;
	}
	
	function CheckBusiness($emp=0,$redirigir=false) {
		if (($this->businessID==0) or ($this->conf->check('MultiBusiness'))) { 
			$valido=true; 
		} elseif ($this->businessID==$emp) {
			$valido=true;
		} 
		if ((! $valido) and ($redirigir)) {
			header("Location: " . $this->module . "/error/" . urlencode(base64_encode("Acceso no autorizado")));	
		}
		return $valido;	
	}

	function PrepareDataForSocialMedia($id=0) {
		if ($id==0) { $id=$this->id; }
		parent::$db->LoadFormData($this,$id,1); 
		$this->Data['Permalink']=$this->GetPermalink();		
		$this->SocialMedia['Twitter']=$this->Data['Title'] . " " . siteprotocol . sitedomain . $this->Data['Permalink'];
		$this->SocialMedia['FBUrl']="http://" . sitedomain . $this->Data['Permalink'];
		$this->SocialMedia['FBTitle']=stripslashes($this->Data['Title']);
		$this->SocialMedia['FBDescription']="";
		if (isset($this->Data['ShortDescription'])) { $this->SocialMedia['FBDescription']=strip_tags(stripslashes($this->Data['ShortDescription'])); }
		if (($this->SocialMedia['FBDescription']=="") and (isset($this->Data['LongDescription']))) { $this->SocialMedia['FBDescription']==strip_tags(stripslashes($this->Data['LongDescription'])); }
		$this->SocialMedia['FBImage']="";
		if ((file_exists('../public/thumbnails/' . $this->Data['Image'])) and (is_file('../public/thumbnails/' . $this->Data['Image']))) { $this->SocialMedia['FBImage']=siteprotocol . sitedomain . "public/thumbnails/" . $this->Data['Image']; }
		$this->SocialMedia['Return']=$this->module . "/list";
		$_SESSION['SocialMedia']=$this->SocialMedia;
		header("Location: socialmedia");
	}

	function PrepareTableList() {
		$this->AddMainMenu('Crear',$this->module . '/new');
		$this->AddMainMenu();
		$this->AddMainMenu('Ordenar',$this->module . '/listorder/id/' . $this->id);
		$this->AddTableContent('','data','','==(99999999999999999999-{{Orden}})');
		$this->AddTableContent('Título','data','{{Title}}','',$this->module . '/edit/id/{{ID}}');
		$in_block=$this->AddTableContent('Operaciones','menu');
		// if(((siteTwitterEnabled) or (siteFacebookEnabled))){
		// 	$this->AddTableOperations($in_block,'Compartir RRSS',$this->module . '/socialmedia');
		// 	$this->AddTableOperations($in_block);
		// }
		$this->AddTableOperations($in_block,'Editar',$this->module . '/edit/id/{{ID}}');
		$this->AddTableOperations($in_block,'Eliminar',$this->module . '/delete/id/{{ID}}');
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('General');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Título","FieldName":"Form_Title","Value":"' . addcslashes($this->Data['Title'],'\\"') . '","Required": true}');
		if($this->Check('UseImage')){ $this->AddFormContent($in_block,'{"Type":"upload","Text":"Imagen","FieldName":"Form_Image","Value":"' . $this->Data['Image'] . '","UploadType": "image", "UploadItem":"first", "Extensions": "jpg,jpeg,gif,png", "PreviewFolder": "public/thumbnails", "External":"' . $this->Data['RenameImage'] . '"}'); }
		if($this->Check('UseShortDescription')){ $this->AddFormContent($in_block,'{"Type":"textarea","Text":"Resumen","FieldName":"Form_ShortDescription","Value":"' . addcslashes($this->Data['ShortDescription'],'\\"') . '"}'); }
		if($this->Check('UseLongDescription')){ $this->AddFormContent($in_block,'{"Type":"html","Text":"Contenido","FieldName":"Form_LongDescription","Value":"' . addcslashes($this->Data['LongDescription'],'\\"') . '"}'); }
		if($this->Check('UseLink')){ $this->AddFormContent($in_block,'{"Type":"url","Text":"Enlace URL","FieldName":"Form_Link","Value":"' . addcslashes($this->Data['Link'],'\\"') . '"}'); }
		if($this->Check('UseGeolocation')){ $this->AddFormContent($in_block,'{"Type":"geo","Text":"Geolocalización basada en Google Maps","FieldName":"Form_Geolocation","Value":"' . $this->Data['Geolocation'] . '"}'); }
		if(($this->Check('UseImages')) and (isset($this->XtraImages))) {
			$in_block=$this->AddFormBlock('Imágenes');
			$this->XtraImages->PutTemplate($this,$in_block);
		}
		if(($this->Check('UseAttachments')) and (isset($this->XtraAttachments))) {
			$in_block=$this->AddFormBlock('Archivos');
			$this->XtraAttachments->PutTemplate($this,$in_block);
		}
		if(($this->Check('UseLinks')) and (isset($this->XtraLinks))) {
			$in_block=$this->AddFormBlock('Enlaces');
			$this->XtraLinks->PutTemplate($this,$in_block);
		}
		if(($this->Check('UseVideos')) and (isset($this->XtraVideos))){
			$in_block=$this->AddFormBlock('Videos');
			$this->XtraVideos->PutTemplate($this,$in_block);
		}
		$in_block=$this->AddFormBlock('Avanzado');
		if ((($this->Check('EnableMultiBusiness')) and (siteMulti) and ($this->Category['MultiBusiness']==1) and ($this->businessID==0))) { $this->AddFormContent($in_block,'{"Type":"combo","Text":"Empresa","FieldName":"Form_IDBusiness","Value":"' . $this->Data['IDBusiness'] . '", "ListTable": "business", "ListValue": "ID", "ListOption": "Name", "ListOrder":"Name", "NullValue": "0"}'); }
		if (((siteTwitterEnabled) or (siteFacebookEnabled)) and ($this->Check('PageUseSocial'))){ $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Publicar en Redes Sociales después de guardar","FieldName":"Public_SocialMedia","Value":""}'); }
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Enlace permanente","FieldName":"Permalink","Value":"' . $this->Data['Permalink'] . '", "Help": "En blanco genera la dirección URL de forma automática"}');
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
	}

	function __destruct(){}

}
?>