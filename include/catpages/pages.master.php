<?php
require_once(sitepath . "include/catpages/catpages.config.php");

class MasterPages extends Core{
	var $title="Páginas";
	var $module = 'catpages';
	var $class= 'pages';
	var $EnableAppend = true;
	var $table = "catpages_pages";
	var $tablefather = "catpages";
	//var $prefix_extras="catpages_pages";
	var $xtras_RunSubClass=true;
	var $FieldsOfImages= array("Image"=>"PageFirstImageOptions");
	var $xtraimages_options="PageImagesOptions";
	var $version="4.0.0.1";

	function __construct($values) {
		parent::__construct($values); 
		$title=$this->GetModuleName($this->class);
		if ($title!=$this->class) { $this->title=$title; }
		$this->conf = new ConfigCatPages($this->businessID);
		$this->Father=parent::$db->GetDataRecord($this->tablefather,$this->idparent);
		if ($this->Father!==false) {
			$this->title=$this->Father['Title'];
			$this->Father['Permalink']=$this->GetPermalink($this->tablefather,$this->idparent);
			if (! $this->Check('UseConfigDefault')) { $this->conf->LoadActualconfig($this->tablefather,$this->idparent); }
		} else {
			$this->Father['ID']=0;
			$this->Father['Title']="Todos los elementos";
		}
		$this->BreadCrumb[$this->GetModuleName($this->module)] = $this->module;
		$this->GetTreeBreadCrumb($this->Father['ID']);

		//$this->BreadCrumb[$this->Father['Title']] = $this->module . "/pages_list/idparent/" . $this->idparent;	
	}

	function GetTreeBreadCrumb($father,$with_permalinks=false) {
		if ($father!=0) {
			$tmp=parent::$db->GetDataRecordFromSQL("SELECT ID,Title,IDFather FROM " . $this->tablefather . " WHERE ID=" .$father);
			$prior=$tmp['IDFather'];
			$this->GetTreeBreadCrumb($prior,$with_permalinks);
			$link=$this->module . "/pages_list/idparent/" . $tmp['ID'];
			if($with_permalinks) { $link=$this->GetPermalink($this->tablefather,$tmp['ID']); }
			if ($tmp['ID']!=0) { $this->BreadCrumb[$tmp['Title']] = $link; }
		}
	}

	function ListAdmItems() {
		$select="SELECT " . $this->table . ".*, " . $this->tablefather . ".Title as ViewCategory, users.UserName as ViewAuthor, '' as BusinessName FROM " . $this->tablefather . "_pages INNER JOIN " . $this->tablefather . " ON " . $this->table . ".IDFather=" . $this->tablefather . ".ID INNER JOIN users ON " . $this->table . ".IDAuthor=users.ID WHERE " . $this->table . ".ID IS NOT NULL";
		if ($this->EnableBusiness) {
			$select="SELECT " . $this->table . ".*, " . $this->tablefather . ".Title as ViewCategory, users.UserName as ViewAuthor, business.Name as BusinessName FROM " . $this->table . " INNER JOIN " . $this->tablefather . " ON " . $this->table . ".IDFather=" . $this->tablefather . ".ID INNER JOIN users ON " . $this->table . ".IDAuthor=users.ID LEFT JOIN business ON " . $this->table . ".IDBusiness=business.ID WHERE " . $this->table . ".ID IS NOT NULL";			
		}
		$cond=$this->table . ".IDFather=" . $this->idparent;
		if (($this->EnableBusiness) and ($this->businessID!=0) and (!defined('InFrontEnd'))) {  $cond.=" AND " . $this->table . ".IDBusiness= " . $this->businessID; }
		if ($this->view=="active") { 
			if($this->conf->Check("PageUseActivation")) {$cond.=" AND " . $this->table . ".Active=1 "; }
			if($this->conf->Check('PageUseDates')){ $cond.=" AND DatePublish<=now() AND DateExpire>=now()"; }
		}
		if (($this->view=="old") and ($this->Check('PageUseDates'))) { $cond.=" AND DateExpire< '" . date('Y-m-d') . "'"; }
		if (($this->view=="noactive") and ($this->conf->Check("PageUseActivation"))) { $cond.=" AND " . $this->table . ".Active=0"; }
		$this->GetItems($cond,false,"Orden DESC",$this->search,false,$select);
		$this->PrepareTableList();
		if ($this->paged) {
			$this->LoadTemplate('list_paged.tpl.php');
		} else {
			$this->LoadTemplate('list.tpl.php');
		}
	}
			
	function NewAdmItem() {
		if ($this->Father['MultiBusiness']==0) {
			$this->CheckItemBusinessPermission($this->Father);	
		}
		$values['IDFather']=$this->idparent;
		$values['IDAuthor']=$this->userID;
		$values['DatePublish']=date('d/m/Y');
		$values['DateExpire']=date('d/m/Y',time()+315360000);
		$values['Readings']=0;
		$values['TotalReadings']=0;
		$values['Active']=1;
		$this->NewItem($values);
		$this->TotalLanguages=0;	
		$this->PrepareForm();
		$this->LoadTemplate('edit.tpl.php');
	}

	
	function CopyFromEvent() {
		parent::$db->InitFormData($this); 
		$this->Data['IDFather']=$this->father;
		if ($this->Data['IDFather']==0) {
			$this->Father=new Cats(NULL);
			//Solicitamos la elección de la categoría a la que se vinculará la página
			$this->LoadTemplate('cats_page_selectcat.tpl.php');
			die();
		}
		require_once(sitepath . "include/calendar/calendar.config.php");
		require_once(sitepath . "include/calendar/events.class.php");
		$Evento=new Events(0);
		$Evento->id=$this->id;
		parent::$db->LoadFormData($Evento,$Evento->id);
		$configuracion=GetOptionsImages($Evento->conf->Export('EventFirstImageOptions'));
		$valido=false;
		foreach($configuracion as $itm) {
			if (is_file(sitepath . "public/" . $itm['folder'] . "/" . $Evento->Data['Image'])) {
				$valido=true;
				copy(sitepath . "public/" . $itm['folder'] . "/" . $Evento->Data['Image'],sitepath . "public/" . $itm['folder'] . "/_copy_" . $Evento->Data['Image']);
			}
		}
		if ($valido) {
			$Evento->Data['Image']="_copy_" . $Evento->Data['Image'];
		} else {
			$Evento->Data['Image']="";
			unset($configuracion);
			$configuracion=GetOptionsImages($this->conf->Export('EventSecondImageOptions'));
			foreach($configuracion as $itm) {
				if (is_file(sitepath . "public/" . $itm['folder'] . "/" . $Evento->Data['SecondImage'])) {
					$valido=true;
					copy(sitepath . "public/" . $itm['folder'] . "/" . $Evento->Data['SecondImage'],sitepath . "public/" . $itm['folder'] . "/_copy_" . $Evento->Data['SecondImage']);
				}
			}
			if ($valido) { $Evento->Data['Image']="_copy_" . $Evento->Data['SecondImage']; }
		}
		unset($configuracion);
		//Inicializamos la página....
		$this->BreadCrumb['Crear'] = "";
		parent::$db->InitFormData($this); 
		$this->Data['PreTitle']=$Evento->Data['TopTitle'];
		$this->Data['Title']=$Evento->Data['Title'];
		$this->Data['PostTitle']=$Evento->Data['PostTitle'];
		$this->Data['Summary']=$Evento->Data['Summary'];
		$this->Data['Page']=$Evento->Data['Page'];
		$this->Data['Image']=$Evento->Data['Image'];
		$this->Data['IDAuthor']=$Evento->Data['IDAuthor'];
		if ($this->Data['IDAuthor']==0) { $this->Data['IDAuthor']=$this->userID; }
		$this->Data['IDFather']=$this->father;
		$this->Data['DatePublish']=date('d/m/Y');
		$this->Data['DateExpire']=date('d/m/Y',time()+315360000);
		$this->Data['Readings']=0;
		$this->Data['Active']=1;
		$this->Data['RenameImage']="";
		$this->XtraImages= new ExtraImages($this->tablefather,'','IDFather',0,$this->conf->GetActualConfig());
		$this->XtraImages->GetItems();
		$this->XtraAttachments= new ExtraAttachments($this->tablefather,'','IDFather',0,$this->conf->GetActualConfig());
		$this->XtraAttachments->GetItems();	
		$this->XtraLinks= new ExtraLinks($this->tablefather,'','IDFather',0,$this->conf->GetActualConfig());	
		$this->XtraLinks->GetItems();
		$this->XtraVideos= new ExtraVideos($this->tablefather,'','IDFather',0,$this->conf->GetActualConfig());	
		$this->XtraVideos->GetItems();	
		$this->Data['Permalink']='';
		//$this->TotalLanguages=0;
		$this->PrepareForm();	
		$this->LoadTemplate('edit.tpl.php');			
		
	}

	function EditAdmItem($id="") {
		$this->EditItem($id);
		$this->Check('PageLevelAdmin');
		if ($this->Father['MultiBusiness']==0) {
			$this->CheckItemBusinessPermission($this->Father);		
		}
		$this->PrepareLangMenu(true);
		$this->PrepareForm();
		$this->LoadTemplate('edit.tpl.php');
	}
	
	
	function PostAdmItem($redirect=true) {
		$_POST['Form_LastUpdate']=date("Y-m-d H:i:s");
		if ($_POST['System_Action']=="new") {
			if (! isset($_POST['Form_DatePublish'])) { $_POST['Form_DatePublish']=date('d/m/Y'); }
			if (! isset($_POST['Form_DateExpire'])) { $_POST['Form_DateExpire']=date('d/m/Y',time()+315360000); }
		}
		if (! isset($_POST['Form_IDAuthor'])) { $_POST['Form_IDAuthor']=$this->userID; }
		if (siteMulti) { $_POST['Form_IDBusiness']=$this->businessID; }
		if (! $this->conf->Check("PageUseActivation")) { $_POST['Form_Active']=1; }
		$carpeta_permalink=$this->GetPermalink($this->tablefather,$_POST['Form_IDFather']);
		$this->conf->CreateTempConf("PermalinkFolder","STRING",$carpeta_permalink);
		PatchCheckBox($_POST,'Public_SocialMedia');
		$redirect=$this->module . "/pages_list/idparent/" . $_POST['Form_IDFather'];
		if (((siteFacebookEnabled) or (siteTwitterEnabled)) and ($_POST['Public_SocialMedia'])) { 
			$volver=$this->module . "/pages_socialmedia/idparent/" . $_POST['Form_IDFather'] . "/id/" . $ActualID;
		}
		$this->PostItem($redirect);
	}


	function OrderAdmItems($viewfield="Title",$order="Orden DESC",$prefix_action="") {
		$this->OrderItems($viewfield="Title",$order,"IDFather=" . $this->idparent);
		$this->script=$this->module;
		if ($this->class!=$this->module) { $this->script.="--" . $this->class; }
		$this->script=$this->module . '/pages_saveorderjson/idparent/' . $this->idparent . '/o/' . $order;
		$this->LoadTemplate('order.tpl.php');
	}

	function PrepareDataForSocialMedia($id=0) {
		if ($id==0) { $id=$this->id; }
		parent::$db->LoadFormData($this,$id,1); 
		$this->Data['Permalink']=$this->GetPermalink();		
		$this->SocialMedia['Twitter']=$this->Data['Title'] . " " . "http://" . sitedomain . $this->Data['Permalink'];
		$this->SocialMedia['FBUrl']="http://" . sitedomain . $this->Data['Permalink'];
		$this->SocialMedia['FBTitle']=stripslashes($this->Data['Title']);
		$this->SocialMedia['FBDescription']="";
		if (isset($this->Data['Summary'])) { $this->SocialMedia['FBDescription']=strip_tags(stripslashes($this->Data['Summary'])); }
		if (($this->SocialMedia['FBDescription']=="") and (isset($this->Data['Page']))) { $this->SocialMedia['FBDescription']==strip_tags(stripslashes($this->Data['Page'])); }
		$this->SocialMedia['FBImage']="";
		if ((file_exists('../public/thumbnails/' . $this->Data['Image'])) and (is_file('../public/thumbnails/' . $this->Data['Image']))) { $this->SocialMedia['FBImage']="http://" . sitedomain . "public/thumbnails/" . $this->Data['Image']; }
		$this->SocialMedia['Return']=$this->module . "?action=list&id=" . $this->Data['IDFather'];
		$_SESSION['SocialMedia']=$this->SocialMedia;
		header("Location: socialmedia");
	}

	function PrepareTableList() {
		$this->AddMainMenu('Crear',$this->module . '/pages_new/idparent/' . $this->idparent);
		$this->AddMainMenu();
		$this->AddMainMenu('Ordenar',$this->module . '/pages_listorder/idparent/' . $this->idparent);
		if (($this->conf->Check("PageUseActivation")) or ($this->conf->Check("PageUseDates"))) { 
			$this->AddMainMenu();
			if (($this->conf->Check("PageUseActivation")) and ($this->view!="active")) { $this->AddMainMenu('Ver Activas',$this->module . '/pages_list/view/active/idparent/' . $this->idparent); }
			if (($this->conf->Check("PageUseDates")) and ($this->view!="old")) {$this->AddMainMenu('Ver Caducadas',$this->module . '/pages_lis/view/old/idparent/' . $this->idparent); }
			if (($this->conf->Check("PageUseActivation")) and ($this->view!="noactive")) {$this->AddMainMenu('Ver Inactivas',$this->module . '/pages_list/view/noactive/idparent/' . $this->idparent); }
			if (($this->conf->Check("PageUseActivation")) and ($this->view!="all")) {$this->AddMainMenu('Ver Todas',$this->module . '/pages_list/view/all/idparent/' . $this->idparent); }
		}
		if ($this->conf->Check("PageUseDates")) { $this->AddTableRowClass('danger','("{{DatePublish}}">"' . date("Y-m-d") . '") or ("{{DateExpire}}"<"' . date("Y-m-d") . '")'); }
		if ($this->conf->Check("PageUseActivation")) { $this->AddTableRowClass('warning','{{Active}}==0'); }
		$this->AddTableContent('','data','','==(99999999999-{{Orden}})');
		$this->AddTableContent('Título','data','{{Title}}','',$this->module . '/pages_edit/idparent/' . $this->idparent . '/id/{{ID}}');
		$this->AddTableContent('Categoría','data','{{ViewCategory}}','',$this->module . '/list/id/{{ID}}');
		if ($this->conf->Check("PageUseAuthorInfo")) { $this->AddTableContent('Autor','data','{{ViewAuthor}}'); }
		if ($this->conf->Check("PageUseDates")) { $this->AddTableContent('Fecha','data','{{DatePublish}}','{{DatePublish}}'); }
		$in_block=$this->AddTableContent('Operaciones','menu');
		if(((siteTwitterEnabled) or (siteFacebookEnabled)) and ($this->Check('PageUseSocial'))){
			$this->AddTableOperations($in_block,'Compartir RRSS',$this->module . '/pages_socialmedia/id/{{ID}}','{{Active}}==1');
			$this->AddTableOperations($in_block);
		}
		if ($this->ModuleInstalledAndEnabled('admcalendar.php')) {
			$this->AddTableOperations($in_block,'Copiar a Agenda','calendar/events_page_import/id/{{ID}}');
			$this->AddTableOperations($in_block);
		}
		$this->AddTableOperations($in_block,'Editar',$this->module . '/pages_edit/idparent/' . $this->idparent . '/id/{{ID}}');
		$this->AddTableOperations($in_block,'Eliminar',$this->module . '/pages_delete/idparent/' . $this->idparent . '/id/{{ID}}');
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('General');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Título de la página","FieldName":"Form_Title","Value":"' . addcslashes($this->Data['Title'],'\\"') . '","Required": true}');
		if($this->Check('PageUsePreTitle')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Antetítulo","FieldName":"Form_PreTitle","Value":"' . addcslashes($this->Data['PreTitle'],'\\"') . '"}'); }
		if($this->Check('PageUsePostTitle')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Subtítulo","FieldName":"Form_PostTitle","Value":"' . addcslashes($this->Data['PostTitle'],'\\"') . '"}'); }
		if($this->Check('PageUseFirstImage')){ $this->AddFormContent($in_block,'{"Type":"upload","Text":"Imagen","FieldName":"Form_Image","Value":"' . $this->Data['Image'] . '","UploadType": "image", "UploadItem":"first", "Extensions": "jpg,jpeg,gif,png", "PreviewFolder": "public/thumbnails", "External":"' . $this->Data['RenameImage'] . '"}'); }
		if($this->Check('PageUseSummary')){ $this->AddFormContent($in_block,'{"Type":"textarea","Text":"Resumen","FieldName":"Form_Summary","Value":"' . addcslashes($this->Data['Summary'],'\\"') . '"}'); }
		$this->AddFormContent($in_block,'{"Type":"html","Text":"Contenido","FieldName":"Form_Page","Value":"' . addcslashes($this->Data['Page'],'\\"') . '"}');
		if($this->Check('PageUseDates')){ $this->AddFormContent($in_block,'{"Type":"doubledate","Text":"Fechas","FieldName":"Form_DatePublish","Value":"' . $this->Data['DatePublish'] . '","FieldName2":"Form_DateExpire","Value2":"' . $this->Data['DateExpire'] . '","Required": true}'); }
		if($this->Check('PageUseGeolocation')){ $this->AddFormContent($in_block,'{"Type":"geo","Text":"Geolocalización basada en Google Maps","FieldName":"Form_Geolocation","Value":"' . $this->Data['Geolocation'] . '"}'); }
		if(($this->Check('PageUseImages')) and (isset($this->XtraImages))) {
			$in_block=$this->AddFormBlock('Imágenes');
			$this->XtraImages->PutTemplate($this,$in_block);
		}
		if(($this->Check('PageUseAttachments')) and (isset($this->XtraAttachments))) {
			$in_block=$this->AddFormBlock('Archivos');
			$this->XtraAttachments->PutTemplate($this,$in_block);
		}
		if(($this->Check('PageUseLinks')) and (isset($this->XtraLinks))) {
			$in_block=$this->AddFormBlock('Enlaces');
			$this->XtraLinks->PutTemplate($this,$in_block);
		}
		if(($this->Check('PageUseVideos')) and (isset($this->XtraVideos))){
			$in_block=$this->AddFormBlock('Videos');
			$this->XtraVideos->PutTemplate($this,$in_block);
		}
		$in_block=$this->AddFormBlock('Avanzado');
		$this->AddFormContent($in_block,'{"Type":"combo","Text":"Autor","FieldName":"Form_IDAuthor","Value":"' . $this->Data['IDAuthor'] . '", "ListTable": "users", "ListValue": "ID", "ListOption": "UserName", "ListOrder":"UserName", "NullValue": "0"}');
		$this->AddFormContent($in_block,'{"Type":"number","Text":"Visitas","FieldName":"Form_TotalReadings","Value":"' . $this->Data['TotalReadings'] . '"}');
		if($this->Check('PageUseComments')){ $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Permitir comentarios","FieldName":"Form_EnableComments","Value":"' . $this->Data['EnableComments'] . '"}'); }
		if($this->Check('PageUseActivation')){ $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Activado","FieldName":"Form_Active","Value":"' . $this->Data['Active'] . '"}'); }
		if ((($this->Check('EnableMultiBusiness')) and (siteMulti) and ($this->Father['MultiBusiness']==1) and ($this->businessID==0)) or (! $this->Check('EnableMultiBusiness'))) {
			if(((siteTwitterEnabled) or (siteFacebookEnabled)) and ($this->Check('PageUseSocial'))){ $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Publicar en Redes Sociales después de guardar","FieldName":"Public_SocialMedia","Value":""}'); }
		}
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Enlace permanente","FieldName":"Permalink","Value":"' . $this->Data['Permalink'] . '", "Help": "En blanco genera la dirección URL de forma automática"}');
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->AddFormHiddenContent("Form_IDFather",$this->Data['IDFather']);
		$this->TemplatePostScript=$this->module . "/pages_post";
	}

	function RunAction() {
		if ($this->action=="event_import") { $this->CopyFromEvent(); }
		if ($this->action=="socialmedia") { $this->PrepareDataForSocialMedia(); }
		if ($this->action=="resampleimages") { 
			$campos['Image']=$this->conf->Export("PageFirstImageOptions");
			$extras=$this->conf->Export("PageImagesOptions");
			$this->ResampleImages($campos,$extras);
			exit;
		}
		parent::RunAction();
	}
	
	
	
}
?>