<?php
require_once(sitepath . "include/core/core.class.php");
require_once(sitepath . "include/services/services.config.php");
require_once(sitepath . "include/extras/images.class.php");
require_once(sitepath . "include/extras/attachments.class.php");
require_once(sitepath . "include/extras/links.class.php");
require_once(sitepath . "include/extras/videos.class.php");
require_once(sitepath . "include/extras/comments.class.php");
require_once(sitepath . "lib/images/thumbs.php");	

class MasterServices extends Core{
	var $title = 'Services';
	var $module = 'services';
	var $class = 'services';
	var $table = 'services';
	var $typemodule='contents';
	var $InstallAdminMenu=array(array('Block' => 'contents', 'Icon' => 'fa-archive'));
	var $tables_required=array('services', 'services_attachments', 'services_comments', 'services_images', 'services_links', 'services_translations', 'services_videos');	
	var $version="3.0.0.2";
	var $FieldsOfImages=array("Image"=>"ImageOptions", "Image2"=>"Image2Options");
	
	function __construct($values) {
		parent::__construct($values); 
		$title=$this->GetModuleName($this->class);
		if ($title!=$this->class) { $this->title=$title; }
		$this->conf = new ConfigServices($this->businessID);
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

	function ListAdmItems() {
		$select="";
		if ((siteMulti) and ($this->businessID!=0) and (!defined('InFrontEnd'))) { $select="SELECT " . $this->table . ".*, business.Name as BusinessName FROM " . $this->table . " LEFT JOIN business ON " . $this->table . ".IDBusiness=" . "business.ID AND IDBusiness= " . $this->businessID . " WHERE " . $this->table .".ID IS NOT NULL"; }
		$this->GetItems("",false,"Orden",$this->search,false,$select);
		$this->PrepareTableList();
		if ($this->paged) {
			$this->LoadTemplate('list_paged.tpl.php');
		} else {
			$this->LoadTemplate('list.tpl.php');
		}
	}

	function GetFontAwesomeList($encode_json=true) {
		$pattern = '/\.(fa-(?:\w+(?:-)?)+):before\s+{\s*content:\s*"(.+)";\s+}/';
		$subject = file_get_contents('http://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.css');
		preg_match_all($pattern, $subject, $matches, PREG_SET_ORDER);
		$icons = array();
		$salida = array(""=>"[Ninguno]");
		foreach($matches as $match){
		    $icons[$match[1]] = $match[2];
		    $salida[$match[1]] = $match[1];
		}
		$icons = var_export($icons, TRUE);
		$icons = stripslashes($icons);
		if ($encode_json) {	return json_encode($salida,true);}
		return $icons;
	}

	function PrepareTableList() {
		$this->AddMainMenu('Crear',$this->module . '/new'); 
		$this->AddMainMenu(); 
		$this->AddMainMenu('Ordenar',$this->module . '/listorder'); 
		$this->AddTableContent('','data','','==(99999999999999999999-{{Orden}})');
		$this->AddTableContent('Título','data','{{Title}}','',$this->module . '/edit/id/{{ID}}');
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block,'Editar',$this->module . '/edit/id/{{ID}}');
		$this->AddTableOperations($in_block,'Eliminar',$this->module . '/delete/id/{{ID}}');
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('General');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Título","FieldName":"Form_Title","Value":"' . addcslashes($this->Data['Title'],'\\"') . '","Required": true}');
		if($this->Check('UseImage')){ $this->AddFormContent($in_block,'{"Type":"upload","Text":"Imagen","FieldName":"Form_Image","Value":"' . addcslashes($this->Data['Image'],'\\"') . '","UploadType": "image", "UploadItem":"first", "Extensions": "jpg,jpeg,gif,png", "PreviewFolder": "public/thumbnails", "External":"' . $this->Data['RenameImage'] . '"}'); }
		if($this->Check('UseImage2')){ $this->AddFormContent($in_block,'{"Type":"upload","Text":"Imagen Secundaria","FieldName":"Form_Image2","Value":"' . addcslashes($this->Data['Image2'],'\\"') . '","UploadType": "image", "UploadItem":"second", "Extensions": "jpg,jpeg,gif,png", "PreviewFolder": "public/thumbnails", "External":"' . $this->Data['RenameImage2'] . '"}'); }
		if($this->Check('UseIcon')){ $this->AddFormContent($in_block,'{"Type":"combo-json","Text":"Icono FontAwesome","FieldName":"Form_Icon","Value":"' . $this->Data['Icon'] . '","JsonValues":' . $this->GetFontAwesomeList() . '}'); }
		if($this->Check('UseShortDescription')){ $this->AddFormContent($in_block,'{"Type":"textarea","Text":"Resumen","FieldName":"Form_ShortDescription","Value":"' . addcslashes($this->Data['ShortDescription'],'\\"') . '"}'); }
		if($this->Check('UseLongDescription')){ $this->AddFormContent($in_block,'{"Type":"html","Text":"Descripción","FieldName":"Form_LongDescription","Value":"' . addcslashes($this->Data['LongDescription'],'\\"') . '"}'); }
		if($this->Check('UseLink')){ $this->AddFormContent($in_block,'{"Type":"url","Text":"URL Enlace","FieldName":"Form_Link","Value":"' . $this->Data['Link'] . '"}'); }
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
		if(((siteTwitterEnabled) or (siteFacebookEnabled)) and ($this->Check('PageUseSocial'))){ $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Publicar en Redes Sociales después de guardar","FieldName":"Public_SocialMedia","Value":""}'); }
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Enlace permanente","FieldName":"Permalink","Value":"' . $this->Data['Permalink'] . '", "Help": "En blanco genera la dirección URL de forma automática"}');
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
	}

}
?>