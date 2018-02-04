<?php
require_once(sitepath . "include/core/core.class.php");
require_once(sitepath . "include/business/business.config.php");
require_once(sitepath . "include/business/business.class.php");
require_once(sitepath . "include/business/users.class.php");
require_once(sitepath . "include/business/modules.class.php");
require_once(sitepath . "include/extras/attributes.class.php");
require_once(sitepath . "include/extras/images.class.php");
require_once(sitepath . "include/extras/attachments.class.php");
require_once(sitepath . "include/extras/links.class.php");
require_once(sitepath . "include/extras/videos.class.php");
require_once(sitepath . "include/extras/comments.class.php");
require_once(sitepath . "lib/images/thumbs.php");

class MasterBusiness extends Core{
	var $title = 'Empresas';
	var $class = 'business';
	var $module = 'business';
	var $table = 'business';
	var $typemodule='business';
	var $InstallAdminMenu=array(array('Block' => 'business', 'Icon' => 'fa-building'));
	var $tables_required=array('business', 'business_attachments', 'business_attributes', 'business_attributes_groups', 
							'business_attributes_options', 'business_attributes_sets', 'business_attributes_values', 
							'business_comments', 'business_images', 'business_links', 'business_lnk_attributes_sets', 
							'business_mailing', 'business_mailing_mails', 'business_modules', 'business_users', 
							'business_videos', 'business_timetable', 'business_holidays');
	var $FieldsOfImages=array("Image"=>"BusinessImageOptions","Logo"=>'BusinessLogoOptions');
	var $InstallActions=array('business--crm','business--timetable','business--holidays','business-timetable','business--sets');
	var $version="4.0.0.1";
	

	function __construct($values) {
		parent::__construct($values);  
		$title=$this->GetModuleName($this->class);
		if ($title!=$this->class) { $this->title=$title; }
		$this->conf = new ConfigBusiness();
		$this->BreadCrumb[$title] = $this->module;
	}
	

	function PopulateAttributes($id=0) {
		if ($id==0) { $id=$this->id; }
		return parent::$db->PopulateMultiSelect('business_lnk_attributes_sets',"IDFather",$id,"IDLink","business_attributes_sets","ID","Title");	
		
	}
	
	function SaveAttributes($Atributos, $id=0) {
		if (($id==0) and ($this->id!=0)) { $id=$this->id; }
		parent::$db->SaveMultiSelect("business_lnk_attributes_sets","IDFather",$id,"IDLink",$Atributos);
	}
		
	function GetBusinessItems($ver="",$paginado=false,$buscar="",$tipo_empresa=0,$provincia=0,$localidad=0,$zona=0,$calificador=0,$valor="",$geo="",$radio=0.003){
		$add_select='(SELECT COUNT(ID) FROM business_comments WHERE IDFather=business.ID) as Comentarios, (SELECT COUNT(ID) FROM likethis WHERE TableName="business" AND TableID=business.ID AND Vote="+") AS VotesPositives';
		if (($calificador!=0) and ($valor!="")) {
			$select = "SELECT business_attributes_values.*, business.*" . $add_select . " FROM business_attributes_values LEFT JOIN business ON business_attributes_values.IDFather=business.ID WHERE business.ID>0";
		} else {
			$select = "SELECT *, " . $add_select . " FROM " . $this->table . " WHERE ID>0";
		}
		$cond="";
		$orden="business.Drafted DESC, Name"; //entre Drafted y Name: (VotosPositivos-VotosNegativos+Comentarios) DESC,
		if ($tipo_empresa=="0") { $tipo_empresa=$this->id; }
		if (is_numeric($tipo_empresa)) {
			if ($tipo_empresa!=0) { 
				if ($cond!="") { $cond.=" AND "; }
				$cond.="(business.IDTypeBusiness=" . $tipo_empresa . " OR '" . $tipo_empresa . "' IN (SELECT IDLink FROM business_lnk_attributes_sets WHERE IDFather=business.ID))"; 
			}
		} else {
			if ($tipo_empresa!="") { //DEPRECATED
				if ($cond!="") { $cond.=" AND "; }
				$cond.="business." . $tipo_empresa . "=1"; 
			} 
		}
		if ($provincia!=0) { 
			if ($cond!="") { $cond.=" AND "; }
			$cond.="business.IDState=" . $provincia; 
		}
		if ($localidad!=0) { 
			if ($cond!="") { $cond.=" AND "; }
			$cond.="business.IDCity=" . $localidad; 
		}
		if ($zona!=0) { 
			if ($cond!="") { $cond.=" AND "; }
			$cond.="business.IDZone=" . $zona; 
		}
		if ($ver=="active") { 
			if ($cond!="") { $cond.=" AND "; }
			$cond.="business.Active=1";
		}
		if ($ver=="enhaced") { 
			if ($cond!="") { $cond.=" AND "; }
			$cond.="business.Active=1 AND business.Drafted=1";
		}
		if ($ver=="noenhaced") { 
			if ($cond!="") { $cond.=" AND "; }
			$cond.="business.Active=1 AND business.Drafted=0";
		}
		if ($ver=="noactive") { 
			if ($cond!="") { $cond.=" AND "; }
			$cond.="business.Active=0"; 
		}
		if ($geo!="") { 
			if ($cond!="") { $cond.=" AND "; }
			$cond.=GetNearThisGeo($geo,$radio,'business.Geolocation');
		}
		if (is_numeric($calificador)) {
			if (($calificador!=0) and ($valor!="")) {
				if ($cond!="") { $cond.=" AND "; }
				$cond.="business_attributes_values.IDAttribute='" . mysqli_real_escape_string(parent::$db->conexion,$calificador) . "'' AND business_attributes_values.Value='" . mysqli_real_escape_string(parent::$db->conexion,$valor) . "'";	
			}	
		} else {
			if (($calificador=="workgroup") and ($valor!="")) {
				if ($cond!="") { $cond.=" AND "; }
				$cond.="'" . $valor . "' IN (SELECT IDLink FROM business_lnk_workgroups WHERE IDFather=business.ID)";		
			}
		}
		if ($buscar=="") { $buscar=$this->search; }
		$this->GetItems($cond,$paginado,"VotesPositives DESC",$buscar,true,$select);
	}

	function GetItemsAddData(&$data) {
		$data['Claim']=$data['Slogan'];
		$sql_comment="SELECT * FROM business_comments WHERE IDFather=" . $data['ID'] . " ORDER BY DatePublish DESC LIMIT 1";
		$LastComment=parent::$db->GetDataRecordFromSQL($sql_comment);
		if ($LastComment!="") {
			$data['Claim']=LimitString(strip_tags(stripslashes($LastComment['Comment'])),200);	
		}
		$data['ILikeThis']=$this->GetLikes('+',$data['Permalink']);
		$data['NoLikeThis']=$this->GetLikes('-',$data['Permalink']);
	}

	function NewAdmItem() {
		$this->BreadCrumb['Nuevo'] = '';
		$this->XtraAttributes= new ExtraAttributes('business','IDFather',0);
		$continuar=true;
		if (! isset($_POST['Form_IDAttributesGroup'])) {
			//Cargamos el conjunto de atributos disponibles...
			$this->XtraAttributes->GetSets(true);
			if ($this->XtraAttributes->ItemsCount==1) { $_POST['Form_IDAttributesGroup']=$this->XtraAttributes->Items[0]['ID']; }
			if ($this->XtraAttributes->ItemsCount>1) { 
				$this->Prepare1StepForm();
				$this->LoadTemplate($this->module . '_edit.tpl.php');
				$continuar=false;
			}
		}
		if ($continuar) {
			$values['IDState']=siteStateDefault;
			$values['IDCity']=siteCityDefault;
			$values['State']=siteStateTextDefault;
			$values['City']=siteCityTextDefault;
			$values['Active']=1;
			$conjunto_atributos=0;
			if (isset($_POST['Form_IDAttributesGroup'])) { $conjunto_atributos=$_POST['Form_IDAttributesGroup'];  }
			$values['IDTypeBusiness']=$conjunto_atributos;
			$this->NewItem($values);
			$this->Data['Temp_Attributes']='0';
			$this->Data['Tags']=parent::$db->GetLinkedTags($this->table,0,true);
			$this->XtraAttributes= new ExtraAttributes('business','','IDFather',0);
			$this->XtraAttributes->id=$conjunto_atributos;
			$this->XtraAttributes->GetGroups(true);
			$this->TotalLanguages=0;	
			$this->PrepareForm();
			$this->LoadTemplate('edit.tpl.php');
		}		
	}
	
	function EditAdmItem($id=0) {
		if ($id==0) { $id=$this->id; }
		$valid=$this->EditItem($id);
		if (! $valid) { 
			$return=siteprotocol . sitedomain . sitePanelFolder ."/home";
			if(isset($_SERVER['HTTP_REFERER'])){ $return=$_SERVER['HTTP_REFERER'];}
			$return.="/error/" . urlencode(base64_encode("El elemento especificado no existe"));
			header("Location: " . $return); exit;
		}
		$this->XtraAttributes= new ExtraAttributes('business','','IDFather',$this->id);	
		$this->XtraAttributes->id=$this->Data['IDTypeBusiness'];
		$this->XtraAttributes->GetGroups(true);	
		$this->Data['Temp_Attributes']=parent::$db->LoadMultiSelect('business_lnk_attributes_sets',"IDFather",$this->id,$ListLink="IDLink",true);
		$this->Data['Tags']=parent::$db->GetLinkedTags($this->table,$this->id,true);
		$this->PrepareLangMenu(true);
		$this->PrepareForm();
		$this->LoadTemplate('edit.tpl.php');
	}

	function PostAdmItem($redirect=true) {
		if (! $this->Check('BusinessUseActivation')) { $_POST['Form_Active']=1; }
		if (isset($_POST['Form_IDTypeBusiness'])) {
			if ($_POST['Form_IDTypeBusiness']!=0) {
				$carpeta_permalink=$this->GetPermalink("business_attributes_sets",$_POST['Form_IDTypeBusiness']);
				$this->conf->CreateTempConf("TempPermalinkFolder","STRING",$carpeta_permalink);
				$this->permalink_conf="TempPermalinkFolder";
			}
		}
		$_POST['Form_LastUpdated']=date("Y-m-d H:i:s");
		$ActualID=$this->PostItem(false);
		if (! isset($_POST['Temp_Attributes'])) {$_POST['Temp_Attributes']=NULL; }
		
		$this->SaveAttributes($_POST['Temp_Attributes'],$ActualID);
		//Guardamos las etiquetas...
		if (! isset($_POST['List_Tags'])) { $_POST['List_Tags']=NULL; }
		parent::$db->SetLinkedTags($this->table,$ActualID,$_POST['List_Tags'],false);
		if ($_POST['System_Action']=="new") {
			$modulos=explode(',',$this->conf->Export('DefaultActiveModules'));
			if (count($modulos)>0) {
				foreach ($modulos as $modulo) {
					unset($Datos);
					$Datos['System_Action']="new";
					$Datos['System_ID']=-1;
					$Datos['Form_IDBusiness']=$ActualID;
					$Datos['Form_OptionFile']=trim($modulo);	
					$this->PostToDatabase('business_modules',$Datos);					
				}
			}
		}	
		$this->XtraAttributes= new ExtraAttributes('business','','IDFather',$ActualID);
		$this->XtraAttributes->PostAllItems();
		if($redirect) { header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module); }
	}
	
	function EditActualBusiness() {
		$this->EditItem($this->businessID);
		$this->XtraAttributes= new ExtraAttributes('business','','IDFather',$this->businessID);	
		$this->XtraAttributes->id=$this->Data['IDTypeBusiness'];
		$this->XtraAttributes->GetGroups(true);	
		$this->Data['Temp_Attributes']=parent::$db->LoadMultiSelect('business_lnk_attributes_sets',"IDFather",$this->businessID,$ListLink="IDLink",true);
		$this->Data['Tags']=parent::$db->GetLinkedTags($this->table,$this->businessID,true);
		$this->PrepareLangMenu(true);
		$this->PrepareMyBusinessForm();
		$this->LoadTemplate($this->module . '_my_edit.tpl.php'); 		
	}

	function PostMyBusiness($redirect=true) {
		$_POST['System_ID']=$this->businessID; 
		$_POST['Form_Active']=1;
		$ActualID=$this->PostItem(false);
		if (! isset($_POST['Temp_Attributes'])) { $_POST['Temp_Attributes']=NULL; }
		$this->SaveAttributes($_POST['Temp_Attributes'],$ActualID);
		//Guardamos las etiquetas...
		if (! isset($_POST['List_Tags'])) { $_POST['List_Tags']=NULL; }
		parent::$db->SetLinkedTags($this->table,$ActualID,$_POST['List_Tags'],false);
		if ($_POST['System_Action']=="new") {
			$modulos=explode(',',$this->conf->Export('DefaultActiveModules'));
			if (count($modulos)>0) {
				foreach ($modulos as $modulo) {
					unset($Datos);
					$Datos['System_Action']="new";
					$Datos['System_ID']=-1;
					$Datos['Form_IDBusiness']=$ActualID;
					$Datos['Form_OptionFile']=trim($modulo);	
					$this->PostToDatabase('business_modules',$Datos);					
				}
			}
		}	
		$this->XtraAttributes= new ExtraAttributes('business','','IDFather',$ActualID);
		$this->XtraAttributes->PostAllItems();
		header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/mybusiness/text/" . urlencode(base64_encode("Los datos han sido guardados")));
	}
	
	function CheckAdminEmail($arg){
		$query_emails = "SELECT * FROM users WHERE Email = '" . $arg . "'";
		$CuentaUsuario=parent::$db->GetDataRecordFromSQL($query_emails);
			
		//Si no devuelve registros el email puede usarse.
		if ($CuentaUsuario===false) { //echo "No hay coincidencia en la BD"; 
			return '0'; } 
		else { 
			return $CuentaUsuario['ID'];
		}
	}

	function PrepareTableList() {
		$this->AddMainMenu('Crear',$this->module . '/new');
		$this->AddMainMenu();
		$this->AddMainMenu('Permisos masivos',$this->module . '/modules_changemassive');
		$this->AddTableRowClass('warning','{{Active}}==0');
		$this->AddTableContent('Nombre','data','{{Name}}','',$this->module . '/edit/id/{{ID}}');
		$in_block=$this->AddTableContent('Operaciones','menu');
		if($this->Check('EnableConfigModules')){ $this->AddTableOperations($in_block,'Módulos',$this->module . '/modules_list/id/{{ID}}'); }
		$this->AddTableOperations($in_block,'Usuarios',$this->module . '/users_list/id/{{ID}}');
		$this->AddTableOperations($in_block,'Horarios',$this->module . '--timetable/list/idparent/{{ID}}');
		$this->AddTableOperations($in_block,'Vacaciones',$this->module . '--holidays/list/idparent/{{ID}}');
		$this->AddTableOperations($in_block);
		if($this->Check('UseImages')){ $this->AddTableOperations($in_block,'Imágenes',$this->module . '/images_list/prior/{{ID}}'); }
		if($this->Check('UseAttachments')){ $this->AddTableOperations($in_block,'Adjuntos',$this->module . '/attachments_list/prior/{{ID}}'); }
		if($this->Check('UseLinks')){ $this->AddTableOperations($in_block,'Enlaces',$this->module . '/links_list/prior/{{ID}}'); }
		if($this->Check('UseVideos')){ $this->AddTableOperations($in_block,'Videos',$this->module . '/videos_list/prior/{{ID}}');	}
		$this->AddTableOperations($in_block);
		$this->AddTableOperations($in_block,'Editar',$this->module . '/edit/id/{{ID}}');
		$this->AddTableOperations($in_block,'Eliminar',$this->module . '/delete/id/{{ID}}');
	}

	function Prepare1StepForm() {
		$in_block=$this->AddFormBlock('Seleccionar tipo de empresa');
		$this->AddFormContent($in_block,'{"Type":"combo","Text":"Actividad Principal","FieldName":"Form_IDAttributesGroup","Value":"0", "ListTable": "business_attributes_sets", "ListValue": "ID", "ListOption": "Title", "ListOrder":"Title"}');
		$this->TemplatePostScript=$this->module . "?action=new";
	}
	
	function PrepareMyBusinessForm() {
		$in_block=$this->AddFormBlock('General');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Nombre Comercial","FieldName":"Form_Name","Value":"' . addcslashes($this->Data['Name'],'\\"') . '","Required": true}');
		if($this->Check('BusinessUseSlogan')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Slogan","FieldName":"Form_Slogan","Value":"' . addcslashes($this->Data['Slogan'],'\\"') . '"}'); }
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Dirección","FieldName":"Form_Street","Value":"' . addcslashes($this->Data['Street'],'\\"') . '", "Help": "Calle, número, etc."}');
		if($this->Check('BusinessUseStandarizedLocationData')){
			if($this->Check('BusinessUseState')){ 
				$this->AddFormContent($in_block,'{"Type":"combo","Text":"Provincia","FieldName":"Form_IDState","Value":"' . $this->Data['IDState'] . '", "ListTable": "aux_states", "ListValue": "ID", "ListOption": "Name", "ListOrder":"Name", "NullValue": "0"}');
				$this->AddFormHiddenContent("Form_State",$this->Data['State']);
			}
			if($this->Check('BusinessUseCity')){ 
				$this->AddFormContent($in_block,'{"Type":"combo","Text":"Localidad","FieldName":"Form_IDCity","Value":"' . $this->Data['IDCity'] . '", "ListTable": "aux_cities", "ListValue": "ID", "ListOption": "Name", "ListOrder":"Name", "NullValue": "0"}');
				$this->AddFormHiddenContent("Form_City",$this->Data['City']);
			}
			if($this->Check('BusinessUseZone')){ 
				$this->AddFormContent($in_block,'{"Type":"combo","Text":"Zona","FieldName":"Form_IDZone","Value":"' . $this->Data['IDZone'] . '", "ListTable": "aux_zones", "ListValue": "ID", "ListOption": "Name", "ListOrder":"Name", "NullValue": "0"}');
				$this->AddFormHiddenContent("Form_Zone",$this->Data['Zone']);
			}
		} else {
			if($this->Check('BusinessUseState')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Provincia","FieldName":"Form_State","Value":"' . addcslashes($this->Data['State'],'\\"') . '"}'); }
			if($this->Check('BusinessUseCity')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Localidad","FieldName":"Form_City","Value":"' . addcslashes($this->Data['City'],'\\"') . '"}'); }
			if($this->Check('BusinessUseZone')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Zona","FieldName":"Form_Zone","Value":"' . addcslashes($this->Data['Zone'],'\\"') . '"}'); }
		}
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Código Postal","FieldName":"Form_ZipCode","Value":"' . addcslashes($this->Data['ZipCode'],'\\"') . '"}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Teléfono","FieldName":"Form_Phone","Value":"' . addcslashes($this->Data['Phone'],'\\"') . '"}');
		if($this->Check('BusinessUseTags')){ $this->AddFormContent($in_block,'{"Type":"tags","Text":"Etiquetas","FieldName":"List_Tags","Value":' . $this->Data['Tags'] . '}'); }
		if($this->Check('BusinessUseEmailContact')){ $this->AddFormContent($in_block,'{"Type":"email","Text":"Correo Electrónico","FieldName":"Form_Email","Value":"' . addcslashes($this->Data['Email'],'\\"') . '"}'); }
		if($this->Check('BusinessUseWeb')){ $this->AddFormContent($in_block,'{"Type":"url","Text":"Web","FieldName":"Form_Web","Value":"' . addcslashes($this->Data['Web'],'\\"') . '"}'); }
		if($this->Check('BusinessUseFacebook')){ $this->AddFormContent($in_block,'{"Type":"url","Text":"Página de Facebook","FieldName":"Form_Facebook","Value":"' . addcslashes($this->Data['Facebook'],'\\"') . '"}'); }
		if($this->Check('BusinessUseTwitter')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Usuario de Twitter","FieldName":"Form_Twitter","Value":"' . addcslashes($this->Data['Twitter'],'\\"') . '", "Help": "Usuario de Twitter, comenzando con @"}'); }
		if($this->Check('BusinessUseGooglePlus')){ $this->AddFormContent($in_block,'{"Type":"url","Text":"Página de Google+","FieldName":"Form_GooglePlus","Value":"' . addcslashes($this->Data['GooglePlus'],'\\"') . '"}'); }
		if($this->Check('BusinessUseGoogleMaps')){ $this->AddFormContent($in_block,'{"Type":"geo","Text":"Geolocalización basada en Google Maps","FieldName":"Form_Geolocation","Value":"' . $this->Data['Geolocation'] . '"}'); }
		if($this->Check('BusinessUseTimeTable')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Horario","FieldName":"Form_TimeTable","Value":"' . addcslashes($this->Data['TimeTable'],'\\"') . '"}');  }
		if($this->Check('BusinessUseLogo')){ $this->AddFormContent($in_block,'{"Type":"upload","Text":"Logo","FieldName":"Form_Logo","Value":"' . $this->Data['Logo'] . '","UploadType": "image", "UploadItem":"second", "Extensions": "jpg,jpeg,gif,png", "PreviewFolder": "public/thumbnails", "External":"' . $this->Data['RenameLogo'] . '"}'); }
		if($this->Check('BusinessUseImage')){ $this->AddFormContent($in_block,'{"Type":"upload","Text":"Imagen","FieldName":"Form_Image","Value":"' . $this->Data['Image'] . '","UploadType": "image", "UploadItem":"first", "Extensions": "jpg,jpeg,gif,png", "PreviewFolder": "public/thumbnails", "External":"' . $this->Data['RenameImage'] . '"}'); }
		if($this->Check('BusinessUseDescription')){ $this->AddFormContent($in_block,'{"Type":"html","Text":"Descripción","FieldName":"Form_Description","Value":"' . $this->Data['Description'] . '"}'); }
		$this->AddFormContent($in_block,'{"Type":"url","Text":"Enlace para reservas","FieldName":"Form_LinkReserve","Value":"' . addcslashes($this->Data['LinkReserve'],'\\"') . '"}');
		$this->AddFormContent($in_block,'{"Type":"url","Text":"Enlace para pedidos","FieldName":"Form_LinkBuy","Value":"' . addcslashes($this->Data['LinkBuy'],'\\"') . '"}');
		if ($this->Data['IDTypeBusiness']!=0) {
			if ($this->XtraAttributes->ItemsCount>0) {
				$in_block=$this->AddFormBlock('Información Detallada');
				$this->XtraAttributes->PutTemplate($this,$in_block);
			}
		}
		if($this->Check('UseImages')){
			$in_block=$this->AddFormBlock('Imágenes');
			$this->XtraImages->PutTemplate($this,$in_block);
		}
		if($this->Check('UseAttachments')){
			$in_block=$this->AddFormBlock('Archivos');
			$this->XtraAttachments->PutTemplate($this,$in_block);
		}
		if($this->Check('UseLinks')){
			$in_block=$this->AddFormBlock('Enlaces');
			$this->XtraLinks->PutTemplate($this,$in_block);
		}
		if($this->Check('UseVideos')){
			$in_block=$this->AddFormBlock('Videos');
			$this->XtraVideos->PutTemplate($this,$in_block);
		}
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->AddFormHiddenContent("System_IDState",$this->Data['IDState']);
		$this->AddFormHiddenContent("System_IDCity",$this->Data['IDCity']);
		$this->AddFormHiddenContent("System_IDZone",$this->Data['IDZone']);
		if ($this->Data['Action']=="new") { $this->AddFormHiddenContent("Form_IDTypeBusiness",$this->Data['IDTypeBusiness']); }
		if(! $this->Check('BusinessUseState')){
			$this->AddFormHiddenContent("Form_IDState",$this->Data['IDState']);
			$this->AddFormHiddenContent("Form_State",$this->Data['State']);
		}
		if(! $this->Check('BusinessUseCity')){
			$this->AddFormHiddenContent("Form_IDCity",$this->Data['IDCity']);
			$this->AddFormHiddenContent("Form_City",$this->Data['City']);
		}
		if(! $this->Check('BusinessUseZone')){
			$this->AddFormHiddenContent("Form_IDZone",$this->Data['IDZone']);
			$this->AddFormHiddenContent("Form_Zone",$this->Data['Zone']);
		}
		$this->TemplatePostScript="mybusiness/post";
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('General');
		if ($this->Data['Action']=="edit") { $this->AddFormContent($in_block,'{"Type":"combo","Text":"Actividad Principal","FieldName":"Form_IDTypeBusiness","Value":"' . $this->Data['IDTypeBusiness'] . '", "ListTable": "business_attributes_sets", "ListValue": "ID", "ListOption": "Title", "ListOrder":"Title", "Help":"El cambio supone la pérdida de la información guardada y específica del conjunto de atributos"}'); }
		if($this->Check('BusinessUseAttributes')){ $this->AddFormContent($in_block,'{"Type":"combo-multiple","Text":"Actividades Secundarias","FieldName":"Temp_Attributes","Value":' . $this->Data['Temp_Attributes'] . ', "ListTable": "business_attributes_sets", "ListValue": "ID", "ListOption": "Title", "ListOrder":"Title", "NullValue": "0"}'); }
		if($this->Check('BusinessUsePackage')){ $this->AddFormContent($in_block,'{"Type":"combo-json","Text":"Paquete Comercial","FieldName":"Form_Package","Value":"' . $this->Data['Package'] . '", "JsonValues": {"basic":"Básica","premium":"Premium"}}'); }
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Nombre Comercial","FieldName":"Form_Name","Value":"' . addcslashes($this->Data['Name'],'\\"') . '","Required": true}');
		if($this->Check('BusinessUseSlogan')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Slogan","FieldName":"Form_Slogan","Value":"' . addcslashes($this->Data['Slogan'],'\\"') . '"}'); }
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Dirección","FieldName":"Form_Street","Value":"' . addcslashes($this->Data['Street'],'\\"') . '", "Help": "Calle, número, etc."}');
		if($this->Check('BusinessUseStandarizedLocationData')){
			if($this->Check('BusinessUseState')){ 
				$this->AddFormContent($in_block,'{"Type":"combo","Text":"Provincia","FieldName":"Form_IDState","Value":"' . $this->Data['IDState'] . '", "ListTable": "aux_states", "ListValue": "ID", "ListOption": "Name", "ListOrder":"Name", "NullValue": "0"}');
				$this->AddFormHiddenContent("Form_State",$this->Data['State']);
			}
			if($this->Check('BusinessUseCity')){ 
				$this->AddFormContent($in_block,'{"Type":"combo","Text":"Localidad","FieldName":"Form_IDCity","Value":"' . $this->Data['IDCity'] . '", "ListTable": "aux_cities", "ListValue": "ID", "ListOption": "Name", "ListOrder":"Name", "NullValue": "0"}');
				$this->AddFormHiddenContent("Form_City",$this->Data['City']);
			}
			if($this->Check('BusinessUseZone')){ 
				$this->AddFormContent($in_block,'{"Type":"combo","Text":"Zona","FieldName":"Form_IDZone","Value":"' . $this->Data['IDZone'] . '", "ListTable": "aux_zones", "ListValue": "ID", "ListOption": "Name", "ListOrder":"Name", "NullValue": "0"}');
				$this->AddFormHiddenContent("Form_Zone",$this->Data['Zone']);
			}
		} else {
			if($this->Check('BusinessUseState')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Provincia","FieldName":"Form_State","Value":"' . addcslashes($this->Data['State'],'\\"') . '"}'); }
			if($this->Check('BusinessUseCity')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Localidad","FieldName":"Form_City","Value":"' . addcslashes($this->Data['City'],'\\"') . '"}'); }
			if($this->Check('BusinessUseZone')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Zona","FieldName":"Form_Zone","Value":"' . addcslashes($this->Data['Zone'],'\\"') . '"}'); }
		}
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Código Postal","FieldName":"Form_ZipCode","Value":"' . addcslashes($this->Data['ZipCode'],'\\"') . '"}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Teléfono","FieldName":"Form_Phone","Value":"' . addcslashes($this->Data['Phone'],'\\"') . '"}');
		if($this->Check('BusinessUseTags')){ $this->AddFormContent($in_block,'{"Type":"tags","Text":"Etiquetas","FieldName":"List_Tags","Value":' . $this->Data['Tags'] . '}'); }
		if($this->Check('BusinessUseEmailContact')){ $this->AddFormContent($in_block,'{"Type":"email","Text":"Correo Electrónico","FieldName":"Form_Email","Value":"' . addcslashes($this->Data['Email'],'\\"') . '"}'); }
		if($this->Check('BusinessUseWeb')){ $this->AddFormContent($in_block,'{"Type":"url","Text":"Web","FieldName":"Form_Web","Value":"' . addcslashes($this->Data['Web'],'\\"') . '"}'); }
		if($this->Check('BusinessUseFacebook')){ $this->AddFormContent($in_block,'{"Type":"url","Text":"Página de Facebook","FieldName":"Form_Facebook","Value":"' . addcslashes($this->Data['Facebook'],'\\"') . '"}'); }
		if($this->Check('BusinessUseTwitter')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Usuario de Twitter","FieldName":"Form_Twitter","Value":"' . addcslashes($this->Data['Twitter'],'\\"') . '", "Help": "Usuario de Twitter, comenzando con @"}'); }
		if($this->Check('BusinessUseGooglePlus')){ $this->AddFormContent($in_block,'{"Type":"url","Text":"Página de Google+","FieldName":"Form_GooglePlus","Value":"' . addcslashes($this->Data['GooglePlus'],'\\"') . '"}'); }
		if($this->Check('BusinessUseGoogleMaps')){ $this->AddFormContent($in_block,'{"Type":"geo","Text":"Geolocalización basada en Google Maps","FieldName":"Form_Geolocation","Value":"' . $this->Data['Geolocation'] . '"}'); }
		if($this->Check('BusinessUseTimeTable')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Horario","FieldName":"Form_TimeTable","Value":"' . addcslashes($this->Data['TimeTable'],'\\"') . '"}');  }
		if($this->Check('BusinessUseLogo')){ $this->AddFormContent($in_block,'{"Type":"upload","Text":"Logo","FieldName":"Form_Logo","Value":"' . $this->Data['Logo'] . '","UploadType": "image", "UploadItem":"second", "Extensions": "jpg,jpeg,gif,png", "PreviewFolder": "public/thumbnails", "External":"' . $this->Data['RenameLogo'] . '"}'); }
		if($this->Check('BusinessUseImage')){ $this->AddFormContent($in_block,'{"Type":"upload","Text":"Imagen","FieldName":"Form_Image","Value":"' . $this->Data['Image'] . '","UploadType": "image", "UploadItem":"first", "Extensions": "jpg,jpeg,gif,png", "PreviewFolder": "public/thumbnails", "External":"' . $this->Data['RenameImage'] . '"}'); }
		if($this->Check('BusinessUseDescription')){ $this->AddFormContent($in_block,'{"Type":"html","Text":"Descripción","FieldName":"Form_Description","Value":"' . $this->Data['Description'] . '"}'); }
		if($this->Check('BusinessUseAccessHandicapped')) { $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Dispone de acceso para discapacitados","FieldName":"Form_AccessHandicapped","Value":"' . $this->Data['AccessHandicapped'] . '"}');}
		if($this->Check('BusinessUseWifi')) { $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Dispone de Wifi","FieldName":"Form_Wifi","Value":"' . $this->Data['Wifi'] . '"}');}
		if($this->Check('BusinessUseAdmitCreditCard')) { $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Admite Tarjeta bancaria","FieldName":"Form_AdminCreditCard","Value":"' . $this->Data['AdmitCreditCard'] . '"}');}
		if($this->Check('BusinessUsePriceMedium')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Orientación económica (precio medio por persona)","FieldName":"Form_PriceMedium","Value":"' . addcslashes($this->Data['PriceMedium'],'\\"') . '"}');  }
		if($this->Check('BusinessUseBillingData')){
			$in_block=$this->AddFormBlock('Datos de Facturación');
			$this->AddFormContent($in_block,'{"Type":"text","Text":"CIF","FieldName":"Form_BillingCIF","Value":"' . addcslashes($this->Data['BillingCIF'],'\\"') . '"}');
			$this->AddFormContent($in_block,'{"Type":"text","Text":"Nombre","FieldName":"Form_BillingName","Value":"' . addcslashes($this->Data['BillingName'],'\\"') . '"}');
 			$this->AddFormContent($in_block,'{"Type":"text","Text":"Domicilio","FieldName":"Form_BillingStreet","Value":"' . addcslashes($this->Data['BillingStreet'],'\\"') . '"}');
 			$this->AddFormContent($in_block,'{"Type":"text","Text":"Provincia","FieldName":"Form_BillingState","Value":"' . addcslashes($this->Data['BillingState'],'\\"') . '"}');
 			$this->AddFormContent($in_block,'{"Type":"text","Text":"Localidad","FieldName":"Form_BillingCity","Value":"' . addcslashes($this->Data['BillingCity'],'\\"') . '"}');
 			$this->AddFormContent($in_block,'{"Type":"text","Text":"Código Postal","FieldName":"Form_BillingZipCode","Value":"' . addcslashes($this->Data['BillingZipCode'],'\\"') . '"}');
 			$this->AddFormContent($in_block,'{"Type":"text","Text":"Teléfono","FieldName":"Form_BillingPhone","Value":"' . addcslashes($this->Data['BillingPhone'],'\\"') . '"}');
 			$this->AddFormContent($in_block,'{"Type":"text","Text":"Fax","FieldName":"Form_BillingFax","Value":"' . addcslashes($this->Data['BillingFax'],'\\"') . '"}');
 			$this->AddFormContent($in_block,'{"Type":"email","Text":"Correo Electrónico","FieldName":"Form_BillingEmail","Value":"' . addcslashes($this->Data['BillingEmail'],'\\"') . '"}');
 			//$this->AddFormContent($in_block,'{"Type":"text","Text":"IBAN","FieldName":"Form_BillingIBAN","Value":"' . addcslashes($this->Data['BillingIBAN'],'\\"') . '"}');
		}
		if ($this->Data['IDTypeBusiness']!=0) {
			if ($this->XtraAttributes->ItemsCount>0) {
				$in_block=$this->AddFormBlock('Información Detallada');
				$this->XtraAttributes->PutTemplate($this,$in_block);
			}
		}
		if($this->Check('UseImages')){
			$in_block=$this->AddFormBlock('Imágenes');
			$this->XtraImages->PutTemplate($this,$in_block);
		}
		if($this->Check('UseAttachments')){
			$in_block=$this->AddFormBlock('Archivos');
			$this->XtraAttachments->PutTemplate($this,$in_block);
		}
		if($this->Check('UseLinks')){
			$in_block=$this->AddFormBlock('Enlaces');
			$this->XtraLinks->PutTemplate($this,$in_block);
		}
		if($this->Check('UseVideos')){
			$in_block=$this->AddFormBlock('Videos');
			$this->XtraVideos->PutTemplate($this,$in_block);
		}
		$in_block=$this->AddFormBlock('Avanzado');
		if ($this->Data['Action']=="new"){
			$this->AddFormContent($in_block,'{"Type":"combo","Text":"Administrador","FieldName":"User_AdminID","Value":"' . $this->userID . '", "ListTable": "users", "ListValue": "ID", "ListOption": "UserName", "ListOrder":"UserName"}');
		}
		if ($this->Check('BusinessUseCloudFiles')) { $this->AddFormContent($in_block,'{"Type":"text","Text":"URL del contenedor de archivos","FieldName":"Form_CloudFiles","Value":"' . addcslashes($this->Data['CloudFiles'],'\\"') . '"}'); }
		if ($this->Check('BusinessUseActivation')) { $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Empresa activa","FieldName":"Form_Active","Value":"' . $this->Data['Active'] . '"}'); }
		if ($this->Check('BusinessUseDrafted')) { $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Destacar","FieldName":"Form_Drafted","Value":"' . $this->Data['Drafted'] . '"}');}
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Enlace permanente","FieldName":"Permalink","Value":"' . $this->Data['Permalink'] . '", "Help": "En blanco genera la dirección URL de forma automática"}');
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->AddFormHiddenContent("System_IDState",$this->Data['IDState']);
		$this->AddFormHiddenContent("System_IDCity",$this->Data['IDCity']);
		$this->AddFormHiddenContent("System_IDZone",$this->Data['IDZone']);
		if ($this->Data['Action']=="new") { $this->AddFormHiddenContent("Form_IDTypeBusiness",$this->Data['IDTypeBusiness']); }
		if(! $this->Check('BusinessUseState')){
			$this->AddFormHiddenContent("Form_IDState",$this->Data['IDState']);
			$this->AddFormHiddenContent("Form_State",$this->Data['State']);
		}
		if(! $this->Check('BusinessUseCity')){
			$this->AddFormHiddenContent("Form_IDCity",$this->Data['IDCity']);
			$this->AddFormHiddenContent("Form_City",$this->Data['City']);
		}
		if(! $this->Check('BusinessUseZone')){
			$this->AddFormHiddenContent("Form_IDZone",$this->Data['IDZone']);
			$this->AddFormHiddenContent("Form_Zone",$this->Data['Zone']);
		}
	}

	function RunModules($action) {
		$action=str_replace("modules_", "", $this->action);
		$this->Xtra= new bModules($this->_values);
		$this->Xtra->action=$action;
		$this->Xtra->RunAction();
	}

	function RunUsers($action) {
		$action=str_replace("users_", "", $this->action);
		$this->Xtra= new bUsers($this->_values);
		$this->Xtra->action=$action;
		$this->Xtra->RunAction();
	}
	
	function RunAction() {
		if ($this->action=="mybusiness") { $this->EditActualBusiness(); }
		if ($this->action=="emailcheck") { echo intval($this->CheckAdminEmail($_POST['email'])); }
		if (strpos($this->action, "modules_")!==false) { $this->RunModules($this->action); }
		if (strpos($this->action, "users_")!==false) { $this->RunUsers($this->action); }
		if ($this->action=="resampleimages") { 
			$campos['Image']=$this->conf->Export("BusinessImageOptions");
			$campos['Logo']=$this->conf->Export("BusinessLogoOptions");
			$extras=$this->conf->Export("ImagesOptions");
			$this->ResampleImages($campos,$extras);
			exit;
		}
		parent::RunAction();
	}
}
?>