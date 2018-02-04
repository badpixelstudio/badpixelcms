<?php
require_once(sitepath . "include/core/core.class.php");
require_once(sitepath . "include/gallery/gallery.config.php");
require_once(sitepath . "include/extras/images.class.php");
require_once(sitepath . "lib/images/thumbs.php");	

class MasterGallery extends Core{
	var $title = 'Galerias';
	var $class="gallery";
	var $module = 'gallery';
	var $view = 'all';
	var $table = 'gallery';
	var $typemodule='contents';
	var $InstallAdminMenu=array(array('Block' => 'contents', 'Icon' => 'fa-archive'));
	var $tables_required=array('gallery','gallery_images');
	var $version="3.0.0.0";
	var $FieldsOfImages=array("Image"=>"ImageOptions");
	var $xtraimages_options="ImagesOptions";
	
	//constructor
	function __construct($values) {
		parent::__construct($values);  
		$title=$this->GetModuleName($this->class);
		if ($title!=$this->class) { $this->title=$title; }
		$this->BreadCrumb[$this->title] = $this->module;
		$this->conf = new ConfigGallery();
	}

	function LoadActualConfig($categoria) {
		//Si no esta cargada la configuración, la leemos...
		if(!isset($this->conf)) { $this->conf = new ConfigGallery($this->businessID); }
		//Obtenemos los datos de la categoria...
		$Categoria=parent::$db->GetDataRecord($this->table,$categoria);
		if (! $this->Check('UseConfigDefault')){
			//Recorremos los ajustes actuales y veo cuales hay que actualizar...
			foreach ($this->conf->columns as $config) {
				$parametro=$config->param;
				//Buscamos el valor en la categoria...
				if (isset($Categoria[$parametro])) {
					$this->conf->columns[$parametro]->value=$Categoria[$parametro];
				}
			}
		}
	}

	function ListAdmItems() {
		$select="SELECT " . $this->table .".*, 'General' as BusinessName, users.UserName as UserName FROM " . $this->table . " LEFT JOIN users ON " . $this->table . ".IDAuthor=users.ID WHERE " .  $this->table . ".ID IS NOT NULL";
		if (siteMulti) {
			$select="SELECT " . $this->table .".*, business.Name as BusinessName, users.UserName as UserName FROM " . $this->table . " LEFT JOIN business ON " . $this->table . ".IDBusiness=business.ID LEFT JOIN users ON " . $this->table . ".IDAuthor=users.ID WHERE " .  $this->table . ".ID IS NOT NULL";
			if (($this->businessID!=0) and (!defined('InFrontEnd'))) {  $select.=" AND IDBusiness= " . $this->businessID . " OR (IDBusiness=0 AND MultiBusiness=1)"; }
		}
		$cond="";
		if ($this->view=="active") {$cond=$this->table . ".Active=1"; }
		if ($this->view=="noactive") {$cond=$this->table . ".Active=0"; }
		$this->GetItems($cond,false,"DatePublish DESC, Orden, ID",$this->search,false,$select);
		$this->PrepareTableList();
		if ($this->paged) {
			$this->LoadTemplate('list_paged.tpl.php');
		} else {
			$this->LoadTemplate('list.tpl.php');
		}
	}

		
	function NewAdmItem() {
		$_SESSION['Back_' . $this->module]=$_SERVER['HTTP_REFERER'];
		$values['AutoGenThumb']=intval($this->Check('GalleryAutoGenThumb'));
		$values['DatePublish']=date('d/m/Y'); 
		$values['IDAuthor']=$this->userID;
		$values['Active']=1;
		$this->NewItem($values);
		$this->TotalLanguages=0;	
		$this->PrepareForm();
		$this->LoadTemplate('edit.tpl.php');
	}

	function EditAdmItem($id="") {
		$_SESSION['Back_' . $this->module]=$_SERVER['HTTP_REFERER'];
		$valid=$this->EditItem($id);
		if (! $valid) { 
			$return=siteprotocol . sitedomain . sitePanelFolder ."/home";
			if(isset($_SERVER['HTTP_REFERER'])){ $return=$_SERVER['HTTP_REFERER'];}
			$return.="/error/" . urlencode(base64_encode("El elemento especificado no existe"));
			header("Location: " . $return); exit;
		}
		$this->CheckItemBusinessPermission($this->Data);
		$this->PrepareLangMenu(true);
		$this->PrepareForm();
		$this->LoadTemplate('edit.tpl.php');
	}
	
	function PostAdmItem($redirect=true) {
		if (! $this->Check('EnableActivation')) { $_POST['Form_Active']=1; }
		if (! $this->Check('EnableAutoGenThumb')) { $_POST['Form_AutoGenThumb']=intval($this->Check('AutoGenThumb')); }
		PatchCheckBox($_POST,'Public_SocialMedia');
		$_SESSION['ReturnToSocialMedia']=0;
		if (((siteFacebookEnabled) or (siteTwitterEnabled)) and ($_POST['Public_SocialMedia'])) { $_SESSION['ReturnToSocialMedia']=1; }
		print_r($_POST);
		$autogenerar=true;
		if (isset($_POST['Change_Image'])) {
			if(is_array($_POST['Change_Image'])) {
				$autogenerar=false;
			}
		}
		if (($redirect=="") or ($redirect==false)) { $redirect=$this->module . "/post_finish/id/" . $ActualID; }
		if (! $autogenerar) { $redirect.="/auto/no"; }
		$this->PostItem($redirect);
		$this->PostItemFinish($autogenerar,$redirect);	
	}


	function PostItemFinish($auto=true) {
		if (isset($_GET['auto'])) { $auto=false; }
		parent::$db->LoadFormData($this,$this->id); 
		$autogenerar=$this->Data['AutoGenThumb'];
		if (! $this->Check('GalleryEnableAutoGenThumb')) { $autogenerar=intval($this->Check('GalleryAutoGenThumb'));  }
		if (($auto) and ($autogenerar==1)) { $this->UpdateGallery($this->id); }
		$redirigir=$this->module . "/list";
		if (isset($_SESSION['ReturnToSocialMedia'])) {
			if ($_SESSION['ReturnToSocialMedia']==1) { $redirigir=$this->module . "/socialmedia/id/" . $this->id; }
		} else {
			if (isset($_SESSION['Back_' . $this->module])) {
				if (strpos($_SESSION['Back_' . $this->module], $redirigir)!==false) {
					$redirigir=$_SESSION['Back_' . $this->module];
				}
			}
		}
		header("Location: " . $redirigir);
	}

	function DeleteOldItems($fecha) {
		$TotalRecorrido=parent::$db->GetDataListFromSQL("SELECT ID FROM " . $this->table . " WHERE DatePublish<='" . $fecha . "'", $Recorrido);
		if ($TotalRecorrido>0) {
			foreach($Recorrido as $evento) {
				$this->DeleteItem($evento['ID']);
			}
		}
		return $TotalRecorrido;
	}

	function UpdateGallery($ActualID) {
		$actualizar="UPDATE " . $this->table. " SET LastUpdate='" . date('Y-m-d') . "'";
		$sql="SELECT * FROM " . $this->table . "_images WHERE IDFather=" . $ActualID . " ORDER BY Orden DESC LIMIT 1";
		$Ultima=parent::$db->GetDataRecordFromSQL($sql);		
		if (($this->Check('GalleryAutoGenThumb')) and ($Ultima!==false)) {
			//Copiamos la última imagen subida a la principal de la galeria	
			$nuevo=$Ultima['Image'];
			copy(sitepath . "public/images/" . $nuevo, sitepath . "public/temp/auto-" . $nuevo);
			$nuevo="auto-" . $nuevo;
			UploadImage($nuevo,$this->conf->Export('ImageOptions'));
			$actualizar.=", Image='" . $nuevo . "'";

		}
		$actualizar.=" WHERE ID=" . $ActualID;
		$ejecutamos = parent::$db->Qry($actualizar);
	}
	
	function ZipGallery() {
		parent::$db->LoadFormData($this,$this->id); 
		require_once(sitepath . "lib/zip/zipfile.php");
		$zipfile = new zipfile();
		$this->XtraImages= new ExtraImages($this->table,'','IDFather',$this->id,$this->conf->GetActualConfig());			
		$this->XtraImages->GetItems();
		if ($this->XtraImages->Total>0) {
			foreach($this->XtraImages->Data as $id=>$image) {
				$ext=substr(strrchr($image['Image'], '.'), 1);
				$zipfile->add_file(implode("",file(sitepath . "public/images/" . $image['Image'])), ($id+1) . "." . $ext);
			}
			$salida=$zipfile->file();
			$archivo=sitepath. "public/files/" . stripfilename('Gallery-' . $this->Data['ID'] . "-" . $this->Data['Title']).'.zip';
			$fp = fopen($archivo, "w");
			fwrite($fp, $salida);
			fclose($fp);  
		}
	}
	
	function DownloadGallery($forzar=false) {
		parent::$db->LoadFormData($this,$this->id); 
		$archivo="public/files/" . stripfilename('Gallery-' . $this->Data['ID'] . "-" . $this->Data['Title']).'.zip';
		if ((! is_file(sitepath. $archivo)) or ($forzar)) {
			$this->ZipGallery();
		}
		return "http://" . sitedomain . $archivo;
	}

	function PrepareDataForSocialMedia($id=0) {
		if ($id==0) { $id=$this->id; }
		parent::$db->LoadFormData($this,$id,1); 
		$this->Data['Permalink']=$this->GetPermalink();		
		$this->SocialMedia['Twitter']=$this->Data['Title'] . " " . siteprotocol . sitedomain . $this->Data['Permalink'];
		$this->SocialMedia['FBUrl']=siteprotocol . sitedomain . $this->Data['Permalink'];
		$this->SocialMedia['FBTitle']=stripslashes($this->Data['Title']);
		$this->SocialMedia['FBDescription']=strip_tags(stripslashes($this->Data['Description']));
		$this->SocialMedia['FBImage']="";
		if ((file_exists('../public/thumbnails/' . $this->Data['Image'])) and (is_file('../public/thumbnails/' . $this->Data['Image']))) { $this->SocialMedia['FBImage']=siteprotocol . sitedomain . "public/thumbnails/" . $this->Data['Image']; }
		$this->SocialMedia['Return']=$this->module . "/list";
		$_SESSION['SocialMedia']=$this->SocialMedia;
		header("Location: socialmedia");
	}

	function AdminDeleteOldGalleries() {
		if (! isset($_POST['ToDate'])) {
			$this->Data['ToDate']="31/12/" . (intval(date('Y'))-2);		
			$in_block=$this->AddFormBlock('General');
			$this->AddFormContent($in_block,'{"Type":"date","Text":"Mantener galerias posteriores a","FieldName":"ToDate","Value":"' . $this->Data['ToDate'] . '","Required": true}');
			$this->TemplatePostScript=$this->module . "/deleteold";
			$this->LoadTemplate('edit.tpl.php');		
		} else {
			$borrados=$this->DeleteOldItems($_POST['ToDate']);
			header("Location: ". $this->module . "/list/error/" . urlencode(base64_encode($borrados . " " . "galerias borradas")));
		}
	}
	
	function PrepareTableList() {
		$this->AddMainMenu('Crear',$this->module . '/new');
		$this->AddMainMenu();
		if($this->Check('GalleryEnableActivation')){
			if ($this->view!="all") { $this->AddMainMenu('Ver Todas',$this->module . '/list/view/all'); }
			if ($this->view!="active") { $this->AddMainMenu('Ver Activas',$this->module . '/list/view/active'); }
			if ($this->view!="noactive") { $this->AddMainMenu('Ver Inactivas',$this->module . '/list/view/noactive'); }
			$this->AddMainMenu();
		}
		$this->AddMainMenu('Borrar antiguas',$this->module . '/deleteold');
		$this->AddTableRowClass('warning','{{Active}}==0');
		$this->AddTableContent('','data','','==(99999999999999999999-{{Orden}})');
		$this->AddTableContent('Título','data','{{Title}}','',$this->module . '/edit/id/{{ID}}');
		if ($this->conf->Check('GalleryEnableDate')) { $this->AddTableContent('Fecha Creación','data','{{DatePublish}}','{{DatePublish}}'); }
		if ($this->conf->Check('GalleryEnableLastUpdate')) { $this->AddTableContent('Fecha Actualización','data','{{LastUpdate}}','{{LastUpdate}}'); }
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block,'Ver',$this->module . '/images_view/prior/{{ID}}');
		$this->AddTableOperations($in_block);
		if(((siteTwitterEnabled) or (siteFacebookEnabled))){
			$this->AddTableOperations($in_block,'Publicar en RRSS',$this->module . '/socialmedia/id/{{ID}}');
			$this->AddTableOperations($in_block);
		}
		$this->AddTableOperations($in_block,'Editar',$this->module . '/edit/id/{{ID}}');
		$this->AddTableOperations($in_block,'Eliminar',$this->module . '/delete/id/{{ID}}');
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('General');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Nombre de la Galería","FieldName":"Form_Title","Value":"' . addcslashes($this->Data['Title'],'\\"') . '","Required": true}');
		if($this->Check('GalleryEnableDescription')){ $this->AddFormContent($in_block,'{"Type":"html","Text":"Descripción","FieldName":"Form_Description","Value":"' . addcslashes($this->Data['Description'],'\\"') . '"}'); }
		if($this->Check('GalleryEnableDate')){ $this->AddFormContent($in_block,'{"Type":"date","Text":"Fecha de publicación","FieldName":"Form_DatePublish","Value":"' . $this->Data['DatePublish'] . '","Required": true}'); }
		if($this->Check('GalleryEnableGenImage')){ $this->AddFormContent($in_block,'{"Type":"upload","Text":"Imagen","FieldName":"Form_Image","Value":"' . $this->Data['Image'] . '","UploadType": "image", "UploadItem":"first", "Extensions": "jpg,jpeg,gif,png", "PreviewFolder": "public/thumbnails", "External":"' . $this->Data['RenameImage'] . '"}'); }
		if($this->Check('GalleryEnableAutoGenThumb')){ $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Habilitar la generación automática de la imagen principal","FieldName":"Form_AutoGenThumb","Value":"' . $this->Data['AutoGenThumb'] . '"}'); }
		if($this->Check('GalleryEnableAuthor')){ $this->AddFormContent($in_block,'{"Type":"combo","Text":"Administrador","FieldName":"Form_IDAuthor","Value":"' . $this->Data['IDAuthor'] . '", "ListTable": "users", "ListValue": "ID", "ListOption": "UserName", "ListOrder":"UserName", "NullValue": "0"}'); }
		if (isset($this->XtraImages)) {
			$in_block=$this->AddFormBlock('Imágenes');
			$this->XtraImages->PutTemplate($this,$in_block);
		}
		$in_block=$this->AddFormBlock('Avanzado');
		if(! $this->Check('UseConfigDefault')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Configuración de imágenes","FieldName":"Form_ImageOptions","Value":"' . $this->Data['ImageOptions'] . '","Required": true, "Help": "Cada opción entre paréntesis y separados por punto y coma. Para cada opción: (carpeta,ancho,alto,[color de fondo][crop],[posicion marca de agua],[repeticiones marca],[imagen marca],[margen marca])"}'); }
		if($this->Check('GalleryEnableActivation')){$this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Activada","FieldName":"Form_Active","Value":"' . $this->Data['Active'] . '"}'); }
		if((! $this->Check('UseConfigDefault')) and ((($this->Check('EnableMultiBusiness')) and ($this->businessID==0) and (siteMulti)))) { $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Compartida para todas las empresas","FieldName":"Form_MultiBusiness","Value":"' . $this->Data['MultiBusiness'] . '"}'); }
		if(((siteTwitterEnabled) or (siteFacebookEnabled)) ){$this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Publicar en Redes Sociales después de guardar","FieldName":"Public_SocialMedia","Value":""}'); }
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Enlace permanente","FieldName":"Permalink","Value":"' . $this->Data['Permalink'] . '", "Help": "En blanco genera la dirección URL de forma automática"}');
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->AddFormHiddenContent("Form_LastUpdate",$this->Data['LastUpdate']);
	}

	function RunAction() {
		parent::RunAction();
		if ($this->action=="post_finish") { $this->PostGalleryFinish(); }	
		if ($this->action=="deleteold") { $this->AdminDeleteOldGalleries(); }
		if ($this->action=="update") { 
			$this->UpdateGallery($this->id);
			header("Location: " . $this->module);
		}
		if ($this->action=="socialmedia") { $this->PrepareDataForSocialMedia(); }
		if ($this->action=="zip") { header("Location: " . $this->DownloadGallery(true)); }
	}

}

?>