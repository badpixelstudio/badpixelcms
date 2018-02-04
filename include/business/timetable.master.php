<?php
require_once(sitepath . "include/core/core.class.php");
require_once(sitepath . "include/business/business.config.php");

class MasterbTimeTable extends Core{
	var $title = 'Horarios';
	var $class = 'timetable';
	var $module = 'business';
	var $typemodule='business';
	var $InstallAdminMenu=array(array('Block' => 'business', 'Icon' => 'fa-building'));
	var $table = 'business_timetable';
	var $version = false;
	var $permalink_conf='none';
	
	function __construct($values) {
		parent::__construct($values);  
		$this->conf = new ConfigBusiness();
		$this->BreadCrumb['Empresas']=$this->module;
		$Empresa=parent::$db->GetDataRecord("business",$this->idparent);
		$this->BreadCrumb[$Empresa['Name']]=$this->module . "/edit/id/" . $this->idparent;	
		$this->BreadCrumb['Horario']=$this->module . "--" . $this->class . "/list/id/" . $this->idparent;	
		if ($this->idparent!=0) { 
			$this->StartEmptyTimetable();
			$this->CheckItemBusinessPermission($this->idparent, true); 
		}
	}

	function DayText($i) {
		$day=_('Dia') . ' ' . $i;
		switch ($i) {
			case 0: $day=_('Domingo'); break;
			case 1: $day=_('Lunes'); break;
			case 2: $day=_('Martes'); break;
			case 3: $day=_('Miércoles'); break;
			case 4: $day=_('Jueves'); break;
			case 5: $day=_('Viernes'); break;
			case 6: $day=_('Sábado'); break;
			case 7: $day=_('Domingo'); break;
		}
		return $day;
	}

	function StartEmptyTimetable() {
		for($x=1;$x<=7;$x++) {
			$sql="SELECT ID FROM " . $this->table . " WHERE IDFather=" . $this->idparent . " AND Day=" . $x;
			if (parent::$db->GetDataRecordFromSQL($sql)===false) {
				unset($_POST);
				$_POST['System_ID']=0;
				$_POST['System_Action']="new";
				$_POST['Form_IDFather']=$this->idparent;
				$_POST['Form_Day']=$x;
				$_POST['Form_Hour1Open']="00:00";
				$_POST['Form_Hour1Close']="00:00";
				$_POST['Form_Hour2Open']="00:00";
				$_POST['Form_Hour2Close']="00:00";
				parent::$db->PostToDatabase($this->table,$_POST);
			}
		}
	}

	function GetItemsAddData(&$data) {
		$data['ViewDay']=$this->DayText($data['Day']);
		$view="";
		if ($data['Hour1Open']!=$data['Hour1Close']) { $view=_('De') . ' ' . substr($data['Hour1Open'], 0,5) . ' ' . _('a') . ' ' . substr($data['Hour1Close'], 0,5); }
		if ($data['Hour2Open']!=$data['Hour2Close']) { 
			if ($view!="") { $view.=' ' . _('y de') . ' '; } else {$view.=_('De') . ' '; }
			$view.=substr($data['Hour2Open'], 0,5) . ' ' . _('a') . ' ' . substr($data['Hour2Close'], 0,5); 
		}
		if ($view=="") { $view=_('Cerrado'); }
		$data['ViewHours']=$view;
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

	function EditAdmItem($id="") {
		$valid=$this->EditItem($id);
		if (! $valid) { 
			$return=siteprotocol . sitedomain . sitePanelFolder ."/home";
			if(isset($_SERVER['HTTP_REFERER'])){ $return=$_SERVER['HTTP_REFERER'];}
			$return.="/error/" . urlencode(base64_encode("El elemento especificado no existe"));
			header("Location: " . $return); exit;
		}
		$this->Data['ViewDay']=$this->DayText($this->Data['Day']);
		$this->PrepareLangMenu(true);
		$this->PrepareForm();
		$this->LoadTemplate('edit.tpl.php');
	}

	function EditMyBusinessItem($id="") {
		$this->EditItem($id);
		$this->Data['ViewDay']=$this->DayText($this->Data['Day']);
		$this->PrepareLangMenu(true);
		$this->PrepareForm();
		$this->TemplatePostScript="my" . $this->module . "--" . $this->class . "/post";
		$this->LoadTemplate('edit.tpl.php');
	}

	function PostAdmItem($redirect=true) {
		$this->PostItem(siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "--" . $this->class . "/list/idparent/" . $_POST['Form_IDFather']);
	}

	function PostMyBusinessItem($redirect=true) {
		$_POST['Form_IDFather']=$this->idparent;
		$this->PostItem(siteprotocol . sitedomain . sitePanelFolder . "/my" . $this->module . "--" . $this->class . "/list");
	}
	
	function GetListHours($idbusiness=0,$day=false,$margin_minutes=30, $hours_today=2,$check_today=true,$margin_end=60) {
		if ($idbusiness==0) { $idbusiness=$this->idparent; }
		if ($day===false) { $day=date("Y-m-d"); }
		$salida=array();
		if (($check_today) and ($day>=date("Y-m-d"))) {
			$festivo=self::$db->GetDataRecordFromSQL("SELECT * FROM business_holidays WHERE IDFather=" . $idbusiness . " AND DateHoliday='" . $day . "'");
			if ($festivo===false) {
				$horas=self::$db->GetDataRecordFromSQL("SELECT * FROM business_timetable WHERE IDFather=" . $idbusiness . " AND Day='" . date('N',strtotime($day)) . "'");
				if ($horas!==false) {
					$hora_inicio=strtotime($day . " " . $horas['Hour1Open']);
					if (date("Y-m-d")==$day) { 
						if ($hours_today<1) { 
							$hora=strtotime("+ " . $hours_today*60 . " minutes");
							if ($hora_inicio<$hora) {
								//Redondeamos la hora...
								$minutes=round(date("i",$hora) / $margin_minutes)*$margin_minutes;
								$tmp=date("Y-m-d H:",$hora) . $minutes . ":00";
								$hora=strtotime($tmp);
								$hora_inicio=$hora;
							} else {
								$hora_inicio=strtotime("+ " . $hours_today*60 . " minutes",$hora_inicio);
							}
						} else { 
							$hora_inicio=strtotime(date("H",strtotime("+" . $hours_today . " hour")) . ":00"); 
						}
					}
					$hora_fin=strtotime($day . " " . $horas['Hour1Close'])-($margin_end*60);					
					while($hora_inicio<=$hora_fin) {
						$salida[]=date('H:i',$hora_inicio);
						$hora_inicio+=$margin_minutes*60;
					}
					$hora_inicio=strtotime($day . " " . $horas['Hour2Open']);
					if (date("Y-m-d")==$day) { 
						if ($hours_today<1) { 
							$hora=strtotime("+ " . $hours_today*60 . " minutes");
							if ($hora_inicio<$hora) {
								//Redondeamos la hora...
								$minutes=round(date("i",$hora) / $margin_minutes)*$margin_minutes;
								$tmp=date("Y-m-d H:",$hora) . $minutes . ":00";
								$hora=strtotime($tmp);
								$hora_inicio=$hora;
							} else {
								$hora_inicio=strtotime("+ " . $hours_today*60 . " minutes",$hora_inicio);
							}
						} else { 
							$hora_inicio=strtotime(date("H",strtotime("+" . $hours_today . " hour")) . ":00"); 
						}
					}
					$hora_fin=strtotime($day . " " . $horas['Hour2Close'])-($margin_end*60);
					while($hora_inicio<=$hora_fin) {
						$salida[]=date('H:i',$hora_inicio);
						$hora_inicio+=$margin_minutes*60;
					}
				}
			}
		}
		return $salida;
	}

	function PrepareTableList() {
		$this->AddTableContent('Día','data','{{ViewDay}}','{{Day}}',$this->module . "--" . $this->class . "/edit/idparent/" . $this->idparent . "/id/{{ID}}");
		$this->AddTableContent('Horario','data','{{ViewHours}}','',$this->module . "--" . $this->class . "/edit/idparent/" . $this->idparent . "/id/{{ID}}");
	}

	function PrepareTableMyBusinessList() {
		$this->AddTableContent('Día','data','{{ViewDay}}','{{Day}}',"my" . $this->module . "--" . $this->class . "/edit/id/{{ID}}");
		$this->AddTableContent('Horario','data','{{ViewHours}}','',"my" . $this->module . "--" . $this->class . "/edit/id/{{ID}}");
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('Editar horario');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Día de la semana","FieldName":"View_Day","Value":"' . addcslashes($this->Data['ViewDay'],'\\"') . '","Disabled": true}');
		$this->AddFormContent($in_block,'{"Type":"doubletime","Text":"Franja de apertura 1 (si no se abre, especificar de 00:00 a 00:00)","FieldName":"Form_Hour1Open","Value":"' . substr($this->Data['Hour1Open'],0,5) . '","FieldName2":"Form_Hour1Close","Value2":"' . substr($this->Data['Hour1Close'],0,5) . '","Required": true}');
		$this->AddFormContent($in_block,'{"Type":"doubletime","Text":"Franja de apertura 2 (si no se abre, especificar de 00:00 a 00:00)","FieldName":"Form_Hour2Open","Value":"' . substr($this->Data['Hour2Open'],0,5) . '","FieldName2":"Form_Hour2Close","Value2":"' . substr($this->Data['Hour2Close'],0,5) . '","Required": true}');
		$this->AddFormHiddenContent("Form_IDFather",$this->Data['IDFather']);
		$this->AddFormHiddenContent("Form_Day",$this->Data['Day']);
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->TemplatePostScript=$this->module . "--" . $this->class . "/post";
	}

	function RunDispatcher() {
		unset($this->BreadCrumb);
		$this->BreadCrumb['Inicio']="";
		$this->BreadCrumb['Editar horarios']=siteprotocol . sitedomain . sitePanelFolder . "/mybusiness--timetable";
		if ($this->action=="list") { $this->ListMyBusinessItems(); }
		if ($this->action=="edit") { $this->EditMyBusinessItem(); }
		if ($this->action=="post") { $this->PostMyBusinessItem(); }
	}
}
?>