<?php
require_once(sitepath . "include/core/core.class.php");
require_once(sitepath . "include/business/business.config.php");

class MasterbHolidays extends Core{
	var $title = 'Festivos';
	var $class = 'holidays';
	var $module = 'business';
	var $table = 'business_holidays';
	var $typemodule='business';
	var $InstallAdminMenu=array(array('Block' => 'business', 'Icon' => 'fa-building'));
	var $version = false;
	var $permalink_conf='none';
	
	function __construct($values) {
		parent::__construct($values);  
		$this->conf = new ConfigBusiness();
		$this->BreadCrumb['Empresas']=$this->module;
		$Empresa=parent::$db->GetDataRecord("business",$this->idparent);
		$this->BreadCrumb[$Empresa['Name']]=$this->module . "/edit/id/" . $this->idparent;	
		$this->BreadCrumb['Horario']=$this->module . "--" . $this->class . "/list/id/" . $this->idparent;	
		if ($this->idparent!=0) { $this->CheckItemBusinessPermission($this->idparent, true);}
	}

	function ListAdmItems() {
		$this->GetItems("IDFather=" . $this->idparent,false,"",$this->search,false);
		$this->PrepareTableList();
		if ($this->paged) {
			$this->LoadTemplate('list_paged.tpl.php');
		} else {
			$this->LoadTemplate('list.tpl.php');
		}
	}

	function ListMyBusinessItems() {
		$this->GetItems("IDFather=" . $this->idparent,false,"",$this->search,false);
		$this->PrepareTableMyBusinessList();
		if ($this->paged) {
			$this->LoadTemplate('list_paged.tpl.php');
		} else {
			$this->LoadTemplate('list.tpl.php');
		}
	}

	function NewAdmItem() {
		$values['IDFather']=$this->idparent;
		$values['DateHoliday']=date("d/m/Y",strtotime("+1 day"));
		$this->NewItem($values);
		$this->TotalLanguages=0;	
		$this->PrepareForm();
		$this->LoadTemplate('edit.tpl.php');
	}

	function NewMyBusinessItem() {
		$values['IDFather']=$this->idparent;
		$values['DateHoliday']=date("d/m/Y",strtotime("+1 day"));
		$this->NewItem($values);
		$this->TotalLanguages=0;	
		$this->PrepareForm();
		$this->TemplatePostScript="my" . $this->module . "--" . $this->class . "/post";
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
		$this->PrepareLangMenu(true);
		$this->PrepareForm();
		$this->LoadTemplate('edit.tpl.php');
	}

	function EditMyBusinessItem($id="") {
		$this->EditItem($id);
		$this->PrepareLangMenu(true);
		$this->PrepareForm();
		$this->TemplatePostScript="my" . $this->module . "--" . $this->class . "/post";
		$this->LoadTemplate('edit.tpl.php');
	}

	function PostAdmItem($redirect=true) {
		$sql="SELECT COUNT(ID) AS TOTAL FROM " . $this->table . " WHERE IDFather=" . $_POST['Form_IDFather'] . " AND DateHoliday='" . $_POST['Form_DateHoliday'] . "' AND ID<>" . $_POST['System_ID'];
		if (parent::$db->GetDataFieldFromSQL($sql,"TOTAL")>0) { 
			if ($redirect) { header("Location: ". siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "--" . $this->class . "/list/idparent/" . $_POST['Form_IDFather']); }
		} else {
			$this->PostItem(siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "--" . $this->class . "/list/idparent/" . $_POST['Form_IDFather']);
		}
	}

	function PostMyBusinessItem($redirect=true) {
		$sql="SELECT COUNT(ID) AS TOTAL FROM " . $this->table . " WHERE IDFather=" . $_POST['Form_IDFather'] . " AND DateHoliday='" . $_POST['Form_DateHoliday'] . "' AND ID<>" . $_POST['System_ID'];
		if (parent::$db->GetDataFieldFromSQL($sql,"TOTAL")>0) { 
			if ($redirect) { header("Location: ". siteprotocol . sitedomain . sitePanelFolder . "/my" . $this->module . "--" . $this->class); }
		} else {
			$this->PostItem(siteprotocol . sitedomain . sitePanelFolder . "/my" . $this->module . "--" . $this->class);
		}
	}
	
	function PrepareTableList() {
		$this->AddMainMenu('Añadir día',$this->module . "--" . $this->class . '/new/idparent/' . $this->idparent);
		$this->AddMainMenu('Añadir periodo',$this->module . "--" . $this->class . '/multiple/idparent/' . $this->idparent);
		$this->AddTableContent('Día','data','{{DateHoliday}}','',$this->module . "--" . $this->class . "/edit/idparent/" . $this->idparent . "/id/{{ID}}");
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block,'Editar',$this->module . "--" . $this->class . '/edit/id/{{ID}}/idparent/' . $this->idparent);
		$this->AddTableOperations($in_block,'Eliminar',$this->module . "--" . $this->class . '/delete/id/{{ID}}/idparent/' . $this->idparent);
	}

	function PrepareTableMyBusinessList() {
		$this->AddMainMenu('Añadir día',"my" . $this->module . "--" . $this->class . '/new');
		$this->AddMainMenu('Añadir periodo',"my" . $this->module . "--" . $this->class . '/multiple');
		$this->AddTableContent('Día','data','{{DateHoliday}}','',"my" . $this->module . "--" . $this->class . "/edit/id/{{ID}}");
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block,'Editar',"my" . $this->module . "--" . $this->class . '/edit/id/{{ID}}');
		$this->AddTableOperations($in_block,'Eliminar',"my" . $this->module . "--" . $this->class . '/delete/id/{{ID}}/');
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('Añadir festividad');	
		$this->AddFormContent($in_block,'{"Type":"date","Text":"Día festivo","FieldName":"Form_DateHoliday","Value":"' . $this->Data['DateHoliday'] . '","Required": true}');
		$this->AddFormHiddenContent("Form_IDFather",$this->Data['IDFather']);
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->TemplatePostScript=$this->module . "--" . $this->class . "/post";
	}

	function NewPeriod() {
		$values['FromDate']=date("d/m/Y",strtotime("+1 day"));
		$values['ToDate']=date("d/m/Y",strtotime("+2 day"));
		$in_block=$this->AddFormBlock('Añadir periodo vacacional');	
		$this->AddFormContent($in_block,'{"Type":"doubledate","Text":"Periodo festivo","FieldName":"FromDate","Value":"' . $values['FromDate'] . '","FieldName2":"ToDate","Value2":"' . $values['ToDate'] . '","Required": true}');
		$this->AddFormHiddenContent("Form_IDFather",$this->idparent);
		$this->TemplatePostScript=$this->module . "--" . $this->class . "/postmultiple";
		$this->LoadTemplate('edit.tpl.php');
	}

	function NewMyBusinessPeriod() {
		$values['FromDate']=date("d/m/Y",strtotime("+1 day"));
		$values['ToDate']=date("d/m/Y",strtotime("+2 day"));
		$in_block=$this->AddFormBlock('Añadir periodo vacacional');	
		$this->AddFormContent($in_block,'{"Type":"doubledate","Text":"Periodo festivo","FieldName":"FromDate","Value":"' . $values['FromDate'] . '","FieldName2":"ToDate","Value2":"' . $values['ToDate'] . '","Required": true}');
		$this->AddFormHiddenContent("Form_IDFather",$this->idparent);
		$this->TemplatePostScript="my".$this->module . "--" . $this->class . "/postmultiple";
		$this->LoadTemplate('edit.tpl.php');
	}

	function PostPeriod() {
		$days=(strtotime($_POST['ToDate'])-strtotime($_POST['FromDate']))/86400;
		$_POST['System_ID']=-1;
		$_POST['System_Action']="new";
		for($x=0;$x<=$days;$x++) {
			$_POST['Form_DateHoliday']=AddDays($_POST['FromDate'],$x);
			$this->PostAdmItem(false);
		}
		header("Location: ". siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "--" . $this->class . "/list/idparent/" . $_POST['Form_IDFather']);
	}

	function PostMyBusinessPeriod() {
		$days=(strtotime($_POST['ToDate'])-strtotime($_POST['FromDate']))/86400;
		$_POST['System_ID']=-1;
		$_POST['System_Action']="new";
		for($x=0;$x<=$days;$x++) {
			$_POST['Form_DateHoliday']=AddDays($_POST['FromDate'],$x);
			$this->PostAdmItem(false);
		}
		header("Location: ". siteprotocol . sitedomain . sitePanelFolder . "/my" . $this->module . "--" . $this->class);
	}

	function RunAction() {
		if ($this->action=="multiple") { $this->NewPeriod(); die; }
		if ($this->action=="postmultiple") { $this->PostPeriod(); die; }
		parent::RunAction();
	}

	function RunDispatcher() {
		unset($this->BreadCrumb);
		$this->BreadCrumb['Inicio']="";
		$this->BreadCrumb['Editar horarios']=siteprotocol . sitedomain . sitePanelFolder . "/mybusiness--timetable";
		if ($this->action=="list") { $this->ListMyBusinessItems(); }
		if ($this->action=="new") { $this->NewMyBusinessItem(); }
		if ($this->action=="edit") { $this->EditMyBusinessItem(); }
		if ($this->action=="post") { $this->PostMyBusinessItem(); }
		if ($this->action=="multiple") { $this->NewMyBusinessPeriod(); }
		if ($this->action=="postmultiple") { $this->PostMyBusinessPeriod(); }
		if ($this->action=="delete") { $this->DeleteAdmItem(); }
	}
}
?>