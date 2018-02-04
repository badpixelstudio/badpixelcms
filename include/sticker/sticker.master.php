<?php
require_once(sitepath . "include/core/core.class.php");
require_once(sitepath . "include/sticker/sticker.config.php");

class MasterSticker extends Core{
	var $title = 'Alertas';
	var $class = "sticker";
	var $module = 'sticker';
	var $table = 'sticker';	
	var $typemodule='appearance';
	var $InstallAdminMenu=array(array('Block' => 'appearance', 'Icon' => 'fa-dashboard'));
	var $tables_required=array('sticker', 'sticker_translations');
	var $version="3.0.0.1";

	function __construct($values) {
		parent::__construct($values);
		$title=$this->GetModuleName($this->class);
		if ($title!=$this->class) { $this->title=$title; }  
		$this->conf = new ConfigSticker($this->businessID);
		$this->BreadCrumb[$this->title]=$this->module;
	}
		
	function ListAdmItems() {
		$cond="";
		switch($this->view){
			case 'expired'	: $cond= "DateExpire< '" . date('Y-m-d') . "'";
							break;
			case 'active'	: $cond= "Active=1 AND DatePublish<='" . date('Y-m-d') . "' AND DateExpire>='". date('Y-m-d') . "'";
							break;	
			case 'noactive'	: $cond= "Active=0";
							break;							
		}	
		$this->GetItems($cond,false,"DatePublish DESC, Orden DESC",$this->search,false);
		$this->PrepareTableList();
		if ($this->paged) {
			$this->LoadTemplate('list_paged.tpl.php');
		} else {
			$this->LoadTemplate('list.tpl.php');
		}
	}

	function NewAdmItem() {
		$values['DatePublish']=date('d/m/Y');
		$values['DateExpire']=date('d/m/Y',time()+86400);
		$this->NewItem($values);
		$this->TotalLanguages=0;	
		$this->PrepareForm();
		$this->LoadTemplate('edit.tpl.php');
	}
	
	function GetSticker() {
		$sql="SELECT * FROM " . $this->table . " WHERE Active=1 AND DatePublish<= '" . date('Y-m-d') . "' AND DateExpire>= '". date('Y-m-d') . "' ORDER BY DatePublish, Orden DESC LIMIT 1";
		$this->Data=parent::$db->GetDataRecordFromSQL($sql);
		if ($this->Data!==false) {
			return true;
		} else {
			return false;
		}
	}

	function OrderAdmItems($viewfield="Title",$order="Orden",$prefix_action="") {
		$this->OrderItems($viewfield="Name",$order);
		$this->script=$this->module . '/' . $prefix_action . 'saveorderjson/o/' . $order;
		$this->LoadTemplate('order.tpl.php');
	}

	function PrepareTableList() {
		$this->AddMainMenu('Crear',$this->module . '/new');
		$this->AddMainMenu('Ordenar',$this->module . '/listorder');
		$this->AddTableRowClass('danger','("{{DatePublish}}">"' . date("Y-m-d") . '") or ("{{DateExpire}}"<"' . date("Y-m-d") . '")');
		$this->AddTableRowClass('warning','{{Active}}==0');
		$this->AddTableContent('Alerta','data','{{Name}}','',$this->module . '/edit/id/{{ID}}');
		$this->AddTableContent('Fechas','data','De {{DatePublish}} a {{DateExpire}}','{{DatePublish');
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block,'Editar',$this->module . '/edit/id/{{ID}}');
		$this->AddTableOperations($in_block,'Eliminar',$this->module . '/delete/id/{{ID}}');
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('General');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Nombre","FieldName":"Form_Name","Value":"' . addcslashes($this->Data['Name'],'\\"') . '","Required": true}');
		$this->AddFormContent($in_block,'{"Type":"combo-json","Text":"Tipo de Alerta","FieldName":"Form_Type","Value":"' . $this->Data['Type'] . '", "JsonValues": {"":"Normal","warning":"Advertencia", "information": "Informativo", "urgent": "Urgente"}}');
		$this->AddFormContent($in_block,'{"Type":"doubledate","Text":"Fechas","FieldName":"Form_DatePublish","Value":"' . $this->Data['DatePublish'] . '","FieldName2":"Form_DateExpire","Value2":"' . $this->Data['DateExpire'] . '","Required": true}');
		if($this->Check('EnableLink')){ $this->AddFormContent($in_block,'{"Type":"url","Text":"Enlace","FieldName":"Form_URL","Value":"' . $this->Data['URL'] . '"}'); }
		if($this->Check('UseActivation')){ $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Activo","FieldName":"Form_Active","Value":"' . $this->Data['Active'] . '"}'); }
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
	}
}
?>