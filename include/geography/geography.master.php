<?php
require_once(sitepath . "include/core/core.class.php");
require_once(sitepath . "include/geography/geography.config.php");

class MasterGeography extends Core{
	var $title = 'Catálogo Geográfico';
	var $class = 'geography';
	var $module = 'geography';
	var $mod='';
	var $table = 'aux_states';
	var $typemodule='tools';
	var $InstallAdminMenu=array(array('Block' => 'tools', 'Icon' => 'fa-french'));
	var $tables_required=array('aux_states','aux_countries','aux_cities','aux_zones');
	var $version="3.0.0.1";
	
	//constructor
	function __construct($values) {
		parent::__construct($values);  
		$title=$this->GetModuleName($this->class);
		if ($title!=$this->class) { $this->title=$title; }
		if (isset($values['field'])) {$this->campo=$values['field']; }
		if (isset($values['mod'])) { $this->mod=$values['mod']; } 
		
		$this->conf = new ConfigGeography();
		//Parcheamos el módulo...
		if (($this->mod=="") and ($this->Check('GeographyUseCountries'))) {
			$this->mod="countries";
		}
		if (($this->mod=="") and ($this->Check('GeographyUseStates'))) {
			$this->mod="states";
			if ($this->idparent==0) { $this->idparent=$this->conf->Export('GeographyDefaultCountry'); }
		}
		if (($this->mod=="") and ($this->Check('GeographyUseCities'))) {
			$this->mod="cities";
			if ($this->idparent==0) { $this->idparent=$this->conf->Export('GeographyDefaultState'); }
		}	
		if (($this->mod=="") and ($this->Check('GeographyUseZones'))) {
			$this->mod="zones";
			if ($this->idparent==0) { $this->idparent=$this->conf->Export('GeographyDefaultCity'); }
		}			
		$this->BreadCrumb[$this->title] = $this->module;
		if ($this->idparent!=0) { $this->AddBreadCrumb($this->idparent); }
	}
	
	function ListAdmItems() {
		$this->table="aux_" . $this->mod;
		$cond="";
		if ($this->idparent!=0) { $cond="IDFather=" . $this->idparent; }
		$this->GetItems($cond,false,"Name",$this->search,false);
		$this->PrepareTableList();
		if ($this->paged) {
			$this->LoadTemplate('list_paged.tpl.php');
		} else {
			$this->LoadTemplate('list.tpl.php');
		}
	}

	function NewAdmItem() {
		$this->table='aux_' . $this->mod;
		$values=false;
		if ($this->idparent!=0) {
			$values['IDFather']=$this->idparent;
		}
		$this->NewItem($values);
		$this->TotalLanguages=0;	
		$this->PrepareForm();
		$this->LoadTemplate('edit.tpl.php');
	}
	
	function EditAdmItem($id="") {
		$this->table='aux_' . $this->mod;
		$valid=$this->EditItem($id);
		if (! $valid) { 
			$return=siteprotocol . sitedomain . sitePanelFolder ."/home";
			if(isset($_SERVER['HTTP_REFERER'])){ $return=$_SERVER['HTTP_REFERER'];}
			$return.="/error/" . urlencode(base64_encode("El elemento especificado no existe"));
			header("Location: " . $return); exit;
		}
		$this->PrepareLangMenu(true);
		$this->PrepareForm();
		$this->LoadTemplate('edit.tpl.php');
	}

	
	function PostAdmItem($redirect=true) {
		$this->table='aux_' . $this->mod;
		$this->PostItem(false);
		$volver="";
		if (isset($_POST['Form_IDState'])) { $volver="/idparent/" . $_POST['Form_IDState']; }
		if (isset($_POST['Form_IDCity'])) { $volver="/idparent/" . $_POST['Form_IDCity']; }
		if (isset($_POST['Form_IDFather'])) { $volver="/idparent/" . $_POST['Form_IDFather']; }
		header("Location: "  . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "/list/mod/" . $this->mod . $volver);
	}
	
	function DeleteAdmItem($id=0) {
		$this->table='aux_' . $this->mod;
		echo intval($this->DeleteItem($id));
	}
	
	function PrepareTableList() {
		if ($this->mod=='countries') {
			$this->AddMainMenu('Crear',$this->module . 'new/mod/countries');
			$this->AddTableContent('Pais','data','{{Name}}','',$this->module . '/list/mod/states/idparent/{{ID}}');
			$in_block=$this->AddTableContent('Operaciones','menu');
			if($this->Check('GeographyUseStates')){
				$this->AddTableOperations($in_block,'Provincias',$this->module . '/list/mod/states/idparent/{{ID}}');
				$this->AddTableOperations($in_block);
			}
		}
		if ($this->mod=='states') {
			$this->AddMainMenu('Crear',$this->module . '/new/mod/states/idparent/' . $this->idparent);
			$this->AddTableContent('Provincia','data','{{Name}}','',$this->module . '/list/mod/cities/idparent/{{ID}}');
			$in_block=$this->AddTableContent('Operaciones','menu');
			if($this->Check('GeographyUseCitys')){
				$this->AddTableOperations($in_block,'Localidades',$this->module . '/list/mod/cities/idparent/{{ID}}');
				$this->AddTableOperations($in_block);
			}
		}
		if ($this->mod=='cities') {
			$this->AddMainMenu('Crear',$this->module . '/new/mod/cities/idparent/' . $this->idparent);
			$this->AddTableContent('Localidad','data','{{Name}}','',$this->module . '/list/mod/zones/idparent/{{ID}}');
			$in_block=$this->AddTableContent('Operaciones','menu');
			if($this->Check('GeographyUseZones')){
				$this->AddTableOperations($in_block,'Zonas',$this->module . '/list/mod/zones/idparent/{{ID}}');
				$this->AddTableOperations($in_block);
			}
		}
		if ($this->mod=='zones') {
			$this->AddMainMenu('Crear',$this->module . '/new/mod/zones/idparent/' . $this->idparent);
			$this->AddTableContent('Zona','data','{{Name}}');
			$in_block=$this->AddTableContent('Operaciones','menu');
		}
		$this->AddTableOperations($in_block,'Editar',$this->module . '/edit/mod/'. $this->mod . '/idparent/' . $this->idparent . '/id/{{ID}}');
		$this->AddTableOperations($in_block,'Eliminar',$this->module . '/delete/mod/' . $this->mod . '/idparent/' . $this->idparent . '/id/{{ID}}');
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('General');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Nombre","FieldName":"Form_Name","Value":"' . $this->Data['Name'] . '","Required": true}');
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->TemplatePostScript=$this->module . "/post/mod/" . $this->mod;
		if (($this->mod=='states') or ($this->mod=='cities') or ($this->mod=='zones')) { 
			$this->AddFormHiddenContent("Form_IDFather",$this->Data['IDFather']); 
			$this->TemplatePostScript.="/idparent/" . $this->Data['IDFather'];
		}
	}

	function AddBreadCrumb($id){
		if ($this->mod=='states') {
			$titulo=parent::$db->GetDataFieldFromSQL("SELECT Name FROM aux_countries WHERE ID='" . $id ."'",'Name');
			$this->BreadCrumb[$titulo] = $this->module . '/list/module/cities/id/' . $id;				
		}
		if ($this->mod=='cities') {
			$titulo_country="";
			$titulo_state="";
			if ($this->Check('GeographyUseStates')) {
				$State=parent::$db->GetDataRecordFromSQL("SELECT * FROM aux_states WHERE ID='" . $id ."'");
				$titulo_state=$State['Name'];
			}
			if (($this->Check('GeographyUseCountries')) and ($titulo_state!="")) {
				$Country=parent::$db->GetDataRecordFromSQL("SELECT * FROM aux_countries WHERE ID='" . $State['IDFather'] ."'");
				$titulo_country=$Country['Name'];
			}
			if ($titulo_country!="") { $this->BreadCrumb[$titulo_country] = $this->mod; }
			if ($titulo_state!="") { $this->BreadCrumb[$titulo_state] = $this->module . "/list/module/states/id/" . $id; }
		}
		if ($this->mod=='zones') {
			$titulo_country="";
			$titulo_state="";
			$titulo_city="";
			if ($this->Check('GeographyUseCitys')) {
				$City=parent::$db->GetDataRecordFromSQL("SELECT * FROM aux_cities WHERE ID='" . $id ."'");
				$titulo_city=$City['Name'];
			}
			if (($this->Check('GeographyUseStates')) and ($titulo_city!="")) {
				$State=parent::$db->GetDataRecordFromSQL("SELECT * FROM aux_states WHERE ID='" . $City['IDFather'] ."'");
				$titulo_state=$State['Name'];
			}			
			
			if (($this->Check('GeographyUseCountries')) and ($titulo_state!="")) {
				$Country=parent::$db->GetDataRecordFromSQL("SELECT * FROM aux_countries WHERE ID='" . $State['IDFather'] ."'");
				$titulo_country=$Country['Name'];
			}
			if ($titulo_country!="") { $this->BreadCrumb[$titulo_country] = $this->mod; }
			if ($titulo_state!="") { $this->BreadCrumb[$titulo_state] = $this->module . "/list/module/states/id/" . $City['IDFather']; }
			if ($titulo_city!="") { $this->BreadCrumb[$titulo_city] = $this->module . "/list/module/cities/id/" . $id; }			
			
			
			$City=parent::$db->GetDataRecordFromSQL("SELECT * FROM aux_cities WHERE ID='" . $id ."'");
			$titulo_city=$City['Name'];
			$titulo_state=' ' . parent::$db->GetDataFieldFromSQL("SELECT Name FROM aux_states WHERE ID='" . $City['IDFather'] ."'",'Name');
			$this->BreadCrumb[$titulo_state] = $this->module . '/list/module/cities/id/' . $City['IDFather'];
			$this->BreadCrumb[$titulo_city] = $this->module . '/list/module/zones/id/' . $id;			
		}
	}
}
?>