<?php
require_once(sitepath . "include/core/core.class.php");
require_once(sitepath . "include/users/users.config.php");

class MasterUserDevices extends Core{
	var $title = 'Dispositivos de usuario';
	var $class = 'devices';
	var $module = 'users';
	var $table = 'users_devices';	
	var $tablefather = "users";
	var $version=false;

	function __construct($values) {
		parent::__construct($values); 
		$this->conf = new ConfigUsers($this->businessID); 
		$this->Father=parent::$db->GetDataRecord($this->tablefather,$this->idparent);
		if ($this->Father!==false) {
			$this->title.=" " . $this->Father['UserName'] . " (" . $this->Father['Email'] . ")";
		}
		$this->BreadCrumb[$this->GetModuleName($this->module)] = $this->module;
		$this->BreadCrumb[$this->GetModuleName($this->title)] = $this->module . "/files_list/idparent/" . $this->idparent;
	}
	
	function ListAdmItems() {
		$this->GetItems("IDUser=" . $this->idparent,false,"ID DESC",$this->search,false);
		$this->PrepareTableList();
		if ($this->paged) {
			$this->LoadTemplate('list_paged.tpl.php');
		} else {
			$this->LoadTemplate('list.tpl.php');
		}
	}

	function PrepareTableList() {
		$this->AddTableContent('Tipo','data','{{DeviceType}}');
		$this->AddTableContent('ID','data','{{DeviceID}}');
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block,'Eliminar',$this->module . '/devices_delete/id/{{ID}}/idparent/' . $this->idparent);
	}
}
?>