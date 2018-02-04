<?php
require_once(sitepath . "include/core/core.class.php");
require_once(sitepath . "include/slider/slider.config.php");
require_once(sitepath . "lib/images/thumbs.php");	

class MasterSlider extends Core{
	var $title = 'Slider';
	var $module = 'slider';
	var $class = 'slider';
	var $table = 'slider';
	var $typemodule='appearance';
	var $InstallAdminMenu=array(array('Block' => 'appearance', 'Icon' => 'fa-dashboard'));
	var $tables_required=array('slider');
	var $version="3.0.0.2";	
	var $FieldsOfImages=array("Image"=>"TempImageOptions");

	function __construct($values) {
		parent::__construct($values); 
		$this->conf = new ConfigSlider($this->businessID);
		$this->conf->CreateTempConf("TempImageOptions","STRING","(slider," . $this->conf->Export("Width") . "," . $this->conf->Export("Height") . ",crop)");
		$this->BreadCrumb[$this->title]=$this->module;
	}

	function CheckBusiness($emp=0,$redirigir=false) {
		if (($this->businessID==0) or ($this->conf->check('MultiBusiness'))) { 
			$valido=true; 
		} elseif ($this->businessID==$emp) {
			$valido=true;
		} 
		if ((! $valido) and ($redirigir)) {
			header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "/error/" . urlencode(base64_encode("Acceso no autorizado")));	
		}
		return $valido;	
	}

	function ListAdmItems() {
		$select="";
		if ((siteMulti) and ($this->businessID!=0)) { $select.="SELECT " . $this->table . ".*, business.Name as BusinessName FROM " . $this->table . " LEFT JOIN business ON " . $this->table . ".IDBusiness=" . "business.ID AND IDBusiness= " . $this->businessID . " WHERE " . $this->table . ".ID IS NOT NULL"; }
		$cond="";
		switch($this->view){
			case 'expired'	: $cond= "DateExpire< '" . date('Y-m-d') . "'";
							break;
			case 'active'	: $cond= "DatePublish<= '" . date('Y-m-d') . "' AND DateExpire>= '". date('Y-m-d') . "'";
							break;				
		}	
		$this->GetItems($cond,false,"Orden",$this->search,false,false,$select);
		$this->PrepareTableList();
		if ($this->paged) {
			$this->LoadTemplate('list_paged.tpl.php');
		} else {
			$this->LoadTemplate('list.tpl.php');
		}
	}

	function GetSlider() {
		$this->GetItems("",false,"Orden");
	}

	function NewAdmItem() {
		$values['DatePublish']=date('d/m/Y');
		$values['DateExpire']=date('d/m/Y',time()+315360000);
		$this->NewItem($values);
		$this->TotalLanguages=0;	
		$this->PrepareForm();
		$this->LoadTemplate('edit.tpl.php');
	}

	function PostAdmItem($redirect=true) {
		if(! $this->Check('EnableShowText')){ $_POST['Form_ShowTitle']=1; } else { PatchCheckbox($_POST,"Form_ShowTitle"); }
		if(! $this->Check('EnableShowDescription')){ $_POST['Form_ShowDescription']=1; } else { PatchCheckbox($_POST,"Form_ShowDescription"); }
		if(! $this->Check('EnableShowButton')){ $_POST['Form_ShowButton']=1; } else { PatchCheckbox($_POST,"Form_ShowButton"); }
		$this->PostItem($redirect);
	}

	function OrderAdmItems($viewfield="Name",$order="Orden DESC",$prefix_action="") {
		parent::OrderAdmItems("Name",$order,$prefix_action);
	}
	
	function PrepareTableList() {
		$this->AddMainMenu('Crear',$this->module . '/new');
		$this->AddMainMenu('Ordenar',$this->module . '/listorder');
		$this->AddMainMenu();
		if ($this->view!="expired") { $this->AddMainMenu('Ver Caducados',$this->module . '/list/view/expired'); }
		if ($this->view!="active") { $this->AddMainMenu('Ver Activos',$this->module . '/list/view/active'); }
		if ($this->view!="") {$this->AddMainMenu('Ver Todos',$this->module . '/list'); }
		$this->AddTableRowClass('danger','("{{DatePublish}}">"' . date("Y-m-d") . '") or ("{{DateExpire}}"<"' . date("Y-m-d") . '")');
		$this->AddTableContent('Nombre','data','{{Name}}','',$this->module . '/edit/id/{{ID}}');
		$this->AddTableContent('Fecha','data','{{DatePublish}}','{{DatePublish}}');
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block,'Editar',$this->module . '/edit/id/{{ID}}');
		$this->AddTableOperations($in_block,'Eliminar',$this->module . '/delete/id/{{ID}}');
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('General');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Nombre","FieldName":"Form_Name","Value":"' . addcslashes($this->Data['Name'],'\\"') . '","Required": true}');
		if($this->Check('EnableShowText')){ $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Mostrar título","FieldName":"Form_ShowTitle","Value":"' . $this->Data['ShowTitle'] . '"}'); }
		$this->AddFormContent($in_block,'{"Type":"upload","Text":"Imagen","FieldName":"Form_Image","Value":"' . $this->Data['Image'] . '","UploadType": "image", "UploadItem":"first", "Extensions": "jpg,jpeg,gif,png", "PreviewFolder": "public/slider", "External":"' . $this->Data['RenameImage'] . '"}');
		if($this->Check('EnableText')){ 
			$this->AddFormContent($in_block,'{"Type":"textarea","Text":"Texto","FieldName":"Form_Description","Value":"' . addcslashes($this->Data['Description'],'\\"') . '"}'); 
			if($this->Check('EnableShowDescription')){ $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Mostrar texto","FieldName":"Form_ShowDescription","Value":"' . $this->Data['ShowDescription'] . '"}'); }
		}
		if($this->Check('EnableLink')){ 
			$this->AddFormContent($in_block,'{"Type":"text","Text":"Enlace","FieldName":"Form_URL","Value":"' . $this->Data['URL'] . '"}');
			if($this->Check('EnableShowButton')){ $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Mostrar botón con el enlace","FieldName":"Form_ShowButton","Value":"' . $this->Data['ShowButton'] . '"}'); }
			if($this->Check('EnableTextButton')){$this->AddFormContent($in_block,'{"Type":"text","Text":"Texto del botón con el enlace","FieldName":"Form_TextButton","Value":"' . addcslashes($this->Data['TextButton'],'\\"') . '"}'); }
		}
		$this->AddFormContent($in_block,'{"Type":"doubledate","Text":"Fechas","FieldName":"Form_DatePublish","Value":"' . $this->Data['DatePublish'] . '","FieldName2":"Form_DateExpire","Value2":"' . $this->Data['DateExpire'] . '","Required": true}');
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
	}
}
?>