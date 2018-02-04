<?php
require_once(sitepath . "include/core/core.class.php");

class MasterLevel extends Core{
	var $showtitle = 'Roles de Usuario';
	var $class = 'levels';
	var $module='levels';
	var $typemodule='system';
	var $title = 'Gestión de Roles';
	var $num_opciones = '0';
	var $options_file = array();
	var $options_name = array();
	var $total_roles = '0';
	var $roles = array();
	var $roles_id = array();
	var $permisos = array();
	var $values="";
	var $version=false;

	//table
	var $table= 'modules_installed';
	
	//constructor
	function __construct($values) {
		//llamamos al constructor de core
		parent::__construct($values); 
		$title=$this->GetModuleName($this->class);
		if ($title!=$this->class) { $this->title=$title; } 
		if (isset($values['field'])) {$this->campo=$values['field']; }
		$this->values=$values;
		$this->BreadCrumb[$this->title]=$this->module;
		$this->GenerateDefaultLevels();
	}

	//load levels function
	function LoadLevels(){
		$level=$this->userLevel;
		//Cargamos los roles disponibles de la tabla
		$query_roles = "SELECT * FROM users_roles WHERE IDRol<=" . $level . " ORDER BY id";
		$this->RolesCount=parent::$db->GetDataListFromSQL($query_roles,$this->Roles);
		//Cargamos los módulos disponibles...
		unset($this->Items) ;
		$this->ItemsCount=0;
		$TotalModulesInstalled=self::$db->GetDataListFromSQL("SELECT * FROM modules_installed WHERE Module!='core'",$ModulesInstalled);
		if ($TotalModulesInstalled>0) {
			foreach($ModulesInstalled as $mod) {
				$nom_mod=$mod['Module'];
				$this->Items[$this->ItemsCount]['File']=$mod['Module'];
				$this->Items[$this->ItemsCount]['Name']=$mod['Module'];
				if ($mod['ModuleName']!="") { $this->Items[$this->ItemsCount]['Name']=$mod['ModuleName']; }
				foreach($this->Roles as $rol) {
					$this->Items[$this->ItemsCount]['Status'][$rol['IDRol']]=0;
					$query_status = "SELECT * FROM users_roles_permissions WHERE RolID=" . $rol['IDRol'] . " AND OptionFile='" . $nom_mod . "'";
					$status = parent::$db->GetDataRecordFromSQL($query_status);
					if (($status!="Error") and ($status!==false)) {
						$this->Items[$this->ItemsCount]['Status'][$rol['IDRol']]=$status['OptionStatus']; 
					}
				}
				$this->ItemsCount++;
			}
		}
	}
	
	//load levels function
	function ListLevels(){
		//Cargamos los roles disponibles de la tabla
		$query_roles = "SELECT * FROM users_roles WHERE IDRol<=" . $this->userLevel . " ORDER BY id";
		$this->ItemsCount=parent::$db->GetDataListFromSQL($query_roles,$this->Items);
		return $this->ItemsCount;
	}
	
	function SetFlag($optionfile,$rolid,$flag="") {
		$query_status = "SELECT * FROM users_roles_permissions WHERE OptionFile = '" . $optionfile . "' AND RolID=" . $rolid;
		$status = parent::$db->GetDataRecordFromSQL($query_status);
		if($status!==false){
			if ($flag=="") {
				if ($status['OptionStatus']==1) {$flag=0; } else {$flag=1; }
			}
			$annadir="UPDATE users_roles_permissions SET OptionStatus=" . $flag . " WHERE OptionFile = '" . $optionfile . "' AND RolID=" . $rolid;
		} else {
			if ($flag=="") { $flag=1; }
			$annadir="INSERT INTO users_roles_permissions (OptionFile, RolID, OptionStatus) VALUES ('" . $optionfile . "', " . $rolid . ", " . $flag .")";
		}
		$ejecutar = parent::$db->Qry($annadir);
		return true;
	}	
	
	function SetName($optionfile,$name) {
		if (strpos($optionfile, '.php')!==false) { $optionfile=str_replace('.php', '',$optionfile); }
		if (strpos($optionfile, 'adm')===0) { $optionfile=str_replace('adm', '', $optionfile); }
		$sql="SELECT ID FROM modules_installed WHERE Module='" . $optionfile . "'";
		$id=parent::$db->GetDataRecordFromSQL($sql);
		if ($id!==false) {
			$annadir="UPDATE modules_installed SET ModuleName='" . $name . "' WHERE Module = '" . $optionfile . "'";
			$ejecutar = parent::$db->Qry($annadir);
		}
		return true;
	}

	//change permiso function
	function ChangeFlag(){
		return $this->SetFlag($this->campo,$this->id);
	}
	
	//edit permiso function
	function EditFormData(){
		if (strpos($this->campo, '.php')!==false) { $this->campo=str_replace('.php', '', $this->campo); }
		if (strpos($this->campo, 'adm')===0) { $this->campo=str_replace('adm', '', $this->campo); }
		$query_permiso="SELECT * FROM " . $this->table . " WHERE OptionFile='" . $this->campo . "'";
		$Permiso = parent::$db->GetDataRecordFromSQL($query_permiso);

		if ($Permiso===false) {
			$this->Data['Action']="new";
			$this->Data['ID']=-1;
			$this->Data['OptionFile']=$this->campo;
			$this->Data['OptionName']=$this->campo;
			$this->Data['OptionHelp']="";
		} else {
			$this->Data['Action']="edit";
			$this->Data['ID']=$Permiso['ID'];
			$this->Data['OptionFile']=$Permiso['OptionFile'];
			$this->Data['OptionName']=$Permiso['OptionName'];
			$this->Data['OptionHelp']=$Permiso['OptionHelp'];
		}
	}
	
	//edit level function
	function EditFormLevelData(){
		$query_permiso="SELECT * FROM users_roles WHERE ID=" . $this->id;
		$Permiso = parent::$db->GetDataRecordFromSQL($query_permiso);

		if ($Permiso===false) {
			$this->Data['Action']="new";
			$this->Data['ID']=-1;
			$this->Data['RolName']='';
		} else {
			$this->Data['Action']="edit";
			$this->Data['ID']=$Permiso['ID'];
			$this->Data['RolName']=$Permiso['RolName'];
		}
	}

	function PostLevelData() {
		parent::$db->PostToDatabase('users_roles',$_POST);
		header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "/roles_list");
	}
	
	function GenerateDefaultLevels() {
		$sql="SELECT IDRol FROM users_roles ORDER BY IDRol DESC LIMIT 1";
		$nivelmax=parent::$db->GetDataFieldFromSQL($sql,'IDRol');
		if ($nivelmax===false) {
			//No hay niveles, creamos los niveles básicos.
			$sql="INSERT INTO users_roles (IDRol,RolName) VALUES(1,'Usuario');";
			$sql="INSERT INTO users_roles (IDRol,RolName) VALUES(2,'Administrador');";	
			$sql="INSERT INTO users_roles (IDRol,RolName) VALUES(99,'Root');";
			parent::$db->Qry($sql);
			$nivelmax=99;
		}
		//Generamos permiso para admlevels.php y admusers.php;
		$this->SetFlag('admlevels.php',$nivelmax,1);
		$this->SetFlag('admusers.php',$nivelmax,1);
	}

	function PrepareLevelsList() {
		$this->AddMainMenu('Gestionar roles',$this->module . '/roles_list');
	}

	function PrepareTableList() {
		//$this->AddMainMenu('Crear',$this->module . '?action=roles_new');
		//$this->AddMainMenu();
		$this->AddMainMenu('Permisos',$this->module);
		$this->AddTableContent('Nivel','data','{{IDRol}}','',$this->module . '/roles_edit/id/{{ID}}');
		$this->AddTableContent('Rol','data','{{RolName}}','',$this->module . '/roles_edit/id/{{ID}}');
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block,'Editar',$this->module . '/roles_edit/id/{{ID}}');
		$this->AddTableOperations($in_block,'Eliminar',$this->module . '/roles_delete/id/{{ID}}');
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('General');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Nombre del Rol","FieldName":"Form_RolName","Value":"' . $this->Data['RolName'] . '","Required": true}');
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->TemplatePostScript=$this->module . "/roles_post";
	}

	function RunAction() {
		if ($this->action=="list") {
			$this->LoadLevels();
			$this->PrepareLevelsList();
			$this->LoadTemplate('user_levels.tpl.php'); 
		}
		if ($this->action=="setflag") { echo intval($this->SetFlag($this->values['module'],$this->values['idrol'],$this->values['flag'])); }
		if ($this->action=="changename") { echo intval($this->SetName($this->values['module'],$this->values['name'])); }
		if ($this->action=="roles_list") {
			$this->ListLevels();
			$this->PrepareTableList();
			$this->LoadTemplate('levels_list.tpl.php'); 
		}
		if ($this->action=="roles_edit") {
			$this->EditFormLevelData();
			$this->PrepareForm();
			$this->LoadTemplate('levels_edit.tpl.php'); 
		}
		if ($this->action=="roles_post") {
			$this->PostLevelData();
		}
		if ($this->action=="roles_delete") {
			$this->table="users_roles";
			echo ($this->Delete());
		}
	}
}
?>