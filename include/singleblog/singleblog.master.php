<?php
require_once(sitepath . "include/core/core.class.php");
require_once(sitepath . "include/singleblog/singleblog.config.php");
require_once(sitepath . "include/singleblog/cats.class.php");
require_once(sitepath . "include/singleblog/related.class.php");
require_once(sitepath . "include/extras/images.class.php");
require_once(sitepath . "include/extras/attachments.class.php");
require_once(sitepath . "include/extras/links.class.php");
require_once(sitepath . "include/extras/videos.class.php");
require_once(sitepath . "include/extras/comments.class.php");
require_once(sitepath . "lib/images/thumbs.php");	

class MasterSingleBlog extends Core{
	var $title = 'Blog';
	var $class = 'singleblog';
	var $module = 'singleblog';
	var $table = 'singleblog';	
	var $typemodule='contents';
	var $InstallAdminMenu=array(array('Block' => 'contents', 'Icon' => 'fa-archive'));
	var $tables_required=array('singleblog','singleblog_cats','singleblog_attachments','singleblog_comments','singleblog_images','singleblog_links','singleblog_translations','singleblog_videos');	
	var $version="3.0.0.4";
	var $FieldsOfImages=array("Image"=>"ImageOptions", "Image2"=>"Image2Options");

	function __construct($values) {
		parent::__construct($values); 
		$title=$this->GetModuleName($this->class);
		if ($title!=$this->class) { $this->title=$title; } 
		$this->conf = new ConfigSingleBlog($this->businessID);
		$this->BreadCrumb[$this->title]=$this->module;
		$this->Father=parent::$db->GetDataRecord($this->table . "_cats",$this->idparent);
		if ($this->Father!==false) {
			$this->BreadCrumb[$this->Father['Title']]=$this->module . "/list/idparent/" . $this->idparent;
			$this->title.=" " . $this->Father['Title'];
		}
	}
	
	function CheckBusiness($emp=0,$redirigir=false) {
		if (($this->businessID==0) or ($this->conf->check('MultiBusiness'))) { 
			$valido=true; 
		} elseif ($this->businessID==$emp) {
			$valido=true;
		} 
		if ((! $valido) and ($redirigir)) {
			header("Location: " . $this->module . "?error=" . urlencode("Acceso no autorizado"));	
		}
		return $valido;	
	}

	function ListAdmItems() {
		$select="";
		$cond="";
		if ($this->idparent!=0) { $cond.="IDCategory=" . $this->idparent; }
		if ((siteMulti) and ($this->businessID!=0)) { $select="SELECT " . $this->table . ".*, business.Name as BusinessName FROM " . $this->table . " LEFT JOIN business ON " . $this->table . ".IDBusiness=" . "business.ID AND IDBusiness= " . $this->businessID . " WHERE " . $this->table . ".ID IS NOT NULL"; }
		$this->GetItems($cond,false,"Orden",$this->search,false,false,$select);
		$this->PrepareTableList();
		if ($this->paged) {
			$this->LoadTemplate('list_paged.tpl.php');
		} else {
			$this->LoadTemplate('list.tpl.php');
		}
	}
	
	function NewAdmItem() {
		$values['DatePublish']=date('d/m/Y');
		$values['Active']=1;
		$this->NewItem($values);
		$this->XtraRelated = new XtraRelated($this,0);
		$this->XtraRelated->GetItems();
		$this->TotalLanguages=0;	
		$this->PrepareForm();
		$this->LoadTemplate('edit.tpl.php');
	}

	function EditAdmItem($id="") {
		$valid=$this->EditItem($id);
		if (! $valid) { 
			$return=siteprotocol . sitedomain . sitePanelFolder ."/home";
			if(isset($_SERVER['HTTP_REFERER'])){ $return=$_SERVER['HTTP_REFERER'];}
			$return.="/error/" . urlencode(base64_encode("El elemento especificado no existe"));
			header("Location: " . $return); exit;
		}
		$this->XtraRelated = new XtraRelated($this,$this->id);
		$this->XtraRelated->GetItems();
		$this->PrepareLangMenu(true);
		$this->PrepareForm();
		$this->LoadTemplate('edit.tpl.php');
	}

	function BeforePostItem() {
		if (! $this->conf->Check("UseActivation")) {
			$_POST['Form_Active']=1;
		}
	}

	function AfterPostItem($ActualID=-1) {
		$this->XtraRelated= new XtraRelated($this,$ActualID);
		$this->XtraRelated->PostAllItems();	
		return true;
	}

	function OrderAdmItems($viewfield="Title",$order="Orden DESC",$prefix_action="") {
		$this->OrderItems($viewfield,$order);
		$this->script=$this->module;
		if ($this->class!=$this->module) { $this->script.="--" . $this->class; }
		$this->script.='/' . $prefix_action . 'saveorderjson/o/' . $order;
		$this->LoadTemplate('order.tpl.php');
	}

	function PrepareDataForSocialMedia($id=0) {
		if ($id==0) { $id=$this->id; }
		parent::$db->LoadFormData($this,$id,1); 
		$this->Data['Permalink']=$this->GetPermalink();		
		$this->SocialMedia['Twitter']=$this->Data['Title'] . " " . siteprotocol . sitedomain . $this->Data['Permalink'];
		$this->SocialMedia['FBUrl']=siteprotocol . sitedomain . $this->Data['Permalink'];
		$this->SocialMedia['FBTitle']=stripslashes($this->Data['Title']);
		$this->SocialMedia['FBDescription']="";
		if (isset($this->Data['ShortDescription'])) { $this->SocialMedia['FBDescription']=strip_tags(stripslashes($this->Data['ShortDescription'])); }
		if (($this->SocialMedia['FBDescription']=="") and (isset($this->Data['LongDescription']))) { $this->SocialMedia['FBDescription']==strip_tags(stripslashes($this->Data['LongDescription'])); }
		$this->SocialMedia['FBImage']="";
		if ((file_exists('../public/thumbnails/' . $this->Data['Image'])) and (is_file('../public/thumbnails/' . $this->Data['Image']))) { $this->SocialMedia['FBImage']="http://" . sitedomain . "public/thumbnails/" . $this->Data['Image']; }
		$this->SocialMedia['Return']=$this->module . "/list";
		$_SESSION['SocialMedia']=$this->SocialMedia;
		header("Location: ". siteprotocol . sitedomain . sitePanelFolder . "/socialmedia");
	}

	function PrepareTableList() {
		$this->AddMainMenu('Crear',$this->module . '/new');
		$this->AddMainMenu('Ordenar',$this->module . '/listorder');
		if ($this->conf->Check("UseCats")) { $this->AddMainMenu('Categorías...',$this->module . '/cats_list'); }
		$this->AddTableRowClass('warning','{{Active}}==0');
		$this->AddTableContent('','data','','==(99999999999999999999-{{Orden}})');
		$this->AddTableContent('Título','data','{{Title}}','',$this->module . '/edit/id/{{ID}}');
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block,'Editar',$this->module . '/edit/id/{{ID}}');
		$this->AddTableOperations($in_block,'Eliminar',$this->module . '/delete/id/{{ID}}');
		if($this->Check('UseActivation')) { 
			$this->AddTableOperations($in_block,'Previsualizar',siteprotocol . sitedomain . '{{Permalink}}','{{Active}}==0',"_blank");
		}
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('General');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Título","FieldName":"Form_Title","Value":"' . addslashes($this->Data['Title']) . '","Required": true}');
		if($this->conf->Check('UseCats')){ $this->AddFormContent($in_block,'{"Type":"combo","Text":"Categoría","FieldName":"Form_IDCategory","Value":"' . $this->Data['IDCategory'] . '", "ListTable": "' . $this->table . '_cats", "ListValue": "ID", "ListOption": "Title", "ListOrder":"Title", "NullValue": "0"}'); }
		if($this->conf->Check('UseImage')){ $this->AddFormContent($in_block,'{"Type":"upload","Text":"Imagen","FieldName":"Form_Image","Value":"' . $this->Data['Image'] . '","UploadType": "image", "UploadItem":"first", "Extensions": "jpg,jpeg,gif,png", "PreviewFolder": "public/thumbnails", "External":"' . $this->Data['RenameImage'] . '"}'); }
		if($this->conf->Check('UseImage2')){ $this->AddFormContent($in_block,'{"Type":"upload","Text":"Imagen secundaria","FieldName":"Form_Image2","Value":"' . $this->Data['Image2'] . '","UploadType": "image", "UploadItem":"first", "Extensions": "jpg,jpeg,gif,png", "PreviewFolder": "public/thumbnails", "External":"' . $this->Data['RenameImage2'] . '"}'); }
		if($this->conf->Check('UseShortDescription')){ $this->AddFormContent($in_block,'{"Type":"textarea","Text":"Resumen","FieldName":"Form_ShortDescription","Value":"' . addslashes($this->Data['ShortDescription']) . '"}'); }
		if($this->conf->Check('UseLongDescription')){ $this->AddFormContent($in_block,'{"Type":"html","Text":"Contenido","FieldName":"Form_LongDescription","Value":"' . addslashes($this->Data['LongDescription']) . '"}'); }
		if($this->conf->Check('UseLink')){ $this->AddFormContent($in_block,'{"Type":"url","Text":"Enlace URL","FieldName":"Form_Link","Value":"' . addslashes($this->Data['Link']) . '"}'); }
		if($this->conf->Check('UseGeolocation')){ $this->AddFormContent($in_block,'{"Type":"geo","Text":"Geolocalización basada en Google Maps","FieldName":"Form_Geolocation","Value":"' . $this->Data['Geolocation'] . '"}'); }
		if($this->conf->Check('UseDates')) { $this->AddFormContent($in_block,'{"Type":"date","Text":"Fecha","FieldName":"Form_DatePublish","Value":"' . $this->Data['DatePublish'] . '"}'); }
		if($this->Check('UseActivation')) { $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Activado","FieldName":"Form_Active","Value":"' . $this->Data['Active'] . '"}'); }
		if(($this->conf->Check('UseImages')) and (isset($this->XtraImages))) {
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
		$in_block=$this->AddFormBlock('Relacionados');
		$this->XtraRelated->PutTemplate($this,$in_block);
		$in_block=$this->AddFormBlock('Avanzado');
		if ((($this->Check('EnableMultiBusiness')) and (siteMulti) and ($this->Category['MultiBusiness']==1) and ($this->businessID==0))) { $this->AddFormContent($in_block,'{"Type":"combo","Text":"Empresa","FieldName":"Form_IDBusiness","Value":"' . $this->Data['IDBusiness'] . '", "ListTable": "business", "ListValue": "ID", "ListOption": "Name", "ListOrder":"Name", "NullValue": "0"}'); }
		if (((siteTwitterEnabled) or (siteFacebookEnabled)) and ($this->Check('PageUseSocial'))){ $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Publicar en Redes Sociales después de guardar","FieldName":"Public_SocialMedia","Value":""}'); }
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Enlace permanente","FieldName":"Permalink","Value":"' . $this->Data['Permalink'] . '", "Help": "En blanco genera la dirección URL de forma automática"}');
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
	}

	function RunXtraCats($action) {
		$action=str_replace("cats_", "", $this->action);
		$this->Xtra= new SingleBlogCats($this->_values);
		$this->Xtra->action=$action;
		$this->Xtra->RunAction();
	}

	function RunXtraRelated($action) {
		if ($this->xtras_prefix=="") { $this->xtras_prefix=$this->table; }
		if (self::$db->GetDataRecordFromSQL("SHOW TABLES LIKE '" . $this->xtras_prefix . "_related'")===false) { die($this->xtras_prefix . "_related not found"); }
		$action=str_replace("related_", "", $action);
		$this->XtraRelated= new XtraRelated($this,$this->linkid);
		$parametros=$this->conf->GetActualConfig();
		$this->XtraRelated->Run($action);
	}

	function RunAction() {
		if (strpos($this->action, "cats_")!==false) { $this->RunXtraCats($this->action); exit; }
		if (strpos($this->action, "related_")!==false) { $this->RunXtraRelated($this->action); exit; }
		parent::RunAction();
	}
}
?>