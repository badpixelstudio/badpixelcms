<?php
require_once(sitepath . "include/core/core.class.php");

class MasterMainMenu extends Core{
	
	var $ModuleVersion='1.0.0.1';
	
	//Inicializamos valores por defecto
	var $title = 'Menú del Panel de Gestión';
	var $class = 'mainmenu';
	var $module = 'core';
	var $table = 'modules_adminmenu';	

	
	//constructor
	function __construct($values) {
		parent::__construct($values);  
		$title=$this->GetModuleName($this->class);
		if ($title!=$this->class) { $this->title=$title; }
		//breadcrumb
		$this->BreadCrumb[$this->title]=$this->module . "--" . $this->class;
	}

	function GenerateMainMenu() {
		$this->GetItems("",false,"Orden",$this->search,false);
		if ($this->ItemsCount>0) {
			$Items=$this->Items;
			$this->Items=array();
			foreach($Items as $item) {
				//Obtenemos los items del bloque...
				$sql="SELECT * FROM modules_installed WHERE BlockMenu='" . $item['Block'] . "' ORDER BY Orden, ID";
				$ItemsCount=parent::$db->GetDataListFromSQL($sql,$SubItems);
				$total=0;
				$subs=array();
				if ($ItemsCount>0) {
					foreach($SubItems as $subitem) {
						if ($this->ModuleInstalledAndEnabled($subitem['Module'])) {
							$incl=true;
							if (($subitem['Module']=="business") and ($this->businessID!=0)) { $incl=false; }
							if ($incl) {
								$total++;
								$link=$subitem['Module'];
								$view=$this->GetModuleName($link);
								if ($subitem['ModuleName']!="") { $view=$subitem['ModuleName']; } 
								$subs[$link]=$view;
							}
						}
					}
				}
				if ($total>0) {
					$item['Items']=$subs;
					array_push($this->Items,$item);
				}
			}
		}
		$this->ItemsCount=count($this->Items);
	}
	
	function ListAdmItems() {
		$this->GetItems("",false,"Orden",$this->search,false);
		$this->PrepareTableList();
		$this->MaxDepth=1;
		$this->LoadTemplate('nestable.tpl.php');
	}

	function ListAdmModules() {
		$this->EditItem();
		$this->BreadCrumb[$this->Data['Title']]=$this->module . "--" . $this->class . '/modules/block/' . $this->_values['block'] . '/id/' . $this->id;
		$this->title.=": " . $this->Data['Title'];
		$this->table="modules_installed";
		$this->GetItems("BlockMenu='" . $this->_values['block'] . "'",false,"Orden");
		$this->PrepareTableModulesList();
		$this->MaxDepth=1;
		$this->LoadTemplate('nestable.tpl.php');
	}

	function SaveOrderModules() {
		$this->table="modules_installed";
		$this->SaveOrderJSON();

	}

	function PrepareTableList() {
		$this->AddMainMenu('Crear',$this->module . '/new');
		$this->AddTableContent('Nombre','data','{{Title}}',$this->module . '--' . $this->class . '/edit/id/{{ID}}');
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block,'Ver módulos',$this->module . '--' . $this->class . '/modules/block/{{Block}}/id/{{ID}}');
		$this->AddTableOperations($in_block,'Editar',$this->module . '--' . $this->class . '/edit/id/{{ID}}');
		$this->AddTableOperations($in_block,'Eliminar',$this->module . '--' . $this->class . '/delete/id/{{ID}}');
	}
	
	function PrepareForm() {
		$in_block=$this->AddFormBlock('Bloque de menú');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Block","FieldName":"Form_Block","Value":"' . addslashes($this->Data['Block']) . '","Required":true}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Nombre del bloque","FieldName":"Form_Title","Value":"' . addslashes($this->Data['Title']) . '", "Help":"Nombre del bloque que se mostrará","Required":true}');
		$this->AddFormContent($in_block,'{"Type":"combo-json","Text":"Icono FontAwesome","FieldName":"Form_Icon","Value":"' . $this->Data['Icon'] . '","JsonValues":' . $this->GetFontAwesomeList() . '}');
		$this->AddFormContent($in_block,'{"Type":"textarea","Text":"Condiciones","FieldName":"Form_Conditions","Value":"' . addslashes($this->Data['Conditions']) . '", "Help":"Permite establecer las condiciones que deben cumplirse para mostrar el bloque"}');
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->TemplatePostScript=$this->module . '--' . $this->class . '/post';
	}

	function PrepareTableModulesList() {
		$this->AddTableContent('Nombre','data','{{ModuleName}} ({{Module}})');
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block,'');
		$this->TemplatePostScript=$this->module . "--" . $this->class . "/saveorderjson_modules";
	}

	function GetFontAwesomeList($encode_json=true) {
		$pattern = '/\.(fa-(?:\w+(?:-)?)+):before\s+{\s*content:\s*"(.+)";\s+}/';
		$subject = file_get_contents('http://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.css');
		preg_match_all($pattern, $subject, $matches, PREG_SET_ORDER);
		$icons = array();
		$salida = array(""=>"[Ninguno]");
		$toOrder = array();
		foreach($matches as $match) {
		               $icons[$match[1]] = $match[2];
		               array_push($toOrder, $match[1]);
		}
		sort($toOrder);
		foreach($toOrder as $match){
		   $salida[$match] = $match;
		}
		$icons = var_export($icons, TRUE);
		$icons = stripslashes($icons);
		if ($encode_json) { return json_encode($salida,true);}
		return $icons;
	}


	function RunAction() {
		if($this->action=="modules") { $this->ListAdmModules(); exit; }
		if($this->action=="saveorderjson_modules") { $this->SaveOrderModules(); exit; }
		parent::RunAction();
	}
}
?>