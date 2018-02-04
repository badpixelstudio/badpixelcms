<?php
require_once(sitepath . "include/core/core.class.php");
require_once(sitepath . "include/menu/menu.config.php");
require_once(sitepath . "lib/images/thumbs.php");	

class MasterMenu extends Core{
	var $title = 'Menú';
	var $class = 'menu';
	var $module = 'menu';
	var $campo = '';
	var $table = 'menu';
	var $typemodule='appearance';
	var $InstallAdminMenu=array(array('Block' => 'appearance', 'Icon' => 'fa-dashboard'));
	var $tables_required=array('menu');
	var $version="3.0.0.1";
	var $FieldsOfImages=array("Image"=>"ImageOptions");
	var $depth=0;
	var $MaxDepth=0;
	var $Menu=array();
	var $already_selected=false;

	
	//constructor
	function __construct($values) {
		parent::__construct($values);  
		$title=$this->GetModuleName($this->class);
		if ($title!=$this->class) { $this->title=$title; }
		$this->conf = new ConfigMenu();

		$this->BreadCrumb[$this->title] = $this->module;
		if ($this->idparent!=0) {
			$this->GetMenuBreadCrumb($this->idparent);
			$this->title=self::$db->GetDataFieldFromSQL('SELECT Title FROM ' . $this->table . " WHERE ID=" . $this->idparent,'Title');
		}
	}
	
	function GetMenuBreadCrumb($father) {
		$ParentMenu=self::$db->GetDataRecordFromSQL('SELECT ID, IDFather, Title FROM ' . $this->table . " WHERE ID=" . $father);
		$this->depth++;
		if ($ParentMenu['IDFather']!=0) { 
			$this->GetMenuBreadCrumb($ParentMenu['IDFather']); 
		}
		$this->BreadCrumb[' ' . $ParentMenu['Title']] = $this->module . "?action=list&idparent=" . $ParentMenu['ID'];
	}

	function GetFontAwesomeList($encode_json=true) {
		$pattern = '/\.(fa-(?:\w+(?:-)?)+):before\s+{\s*content:\s*"(.+)";\s+}/';
		$subject = file_get_contents('http://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.css');
		preg_match_all($pattern, $subject, $matches, PREG_SET_ORDER);
		$icons = array();
		$salida = array(""=>"[Ninguno]");
		foreach($matches as $match){
		    $icons[$match[1]] = $match[2];
		    $salida[$match[1]] = $match[1];
		}
		$icons = var_export($icons, TRUE);
		$icons = stripslashes($icons);
		if ($encode_json) {	return json_encode($salida,true);}
		return $icons;
	}

	function GetTreeItems(&$level,$padre=0) {
		if ($level=="") { $level=$this->Items; }
		$query_getcats = "SELECT ID, Title, Orden FROM " . $this->table . " WHERE IDFather = " . $padre;
		$query_getcats.=" ORDER BY Orden, ID";
		$TotalListado=self::$db->GetDataListFromSQL($query_getcats, $Listado);
		if ($TotalListado>0) {
			foreach($Listado as $item) {
				$item['Items']=array();
				$this->GetTreeItems($item['Items'],$item['ID']);
				$level[]=$item;
			}
		} 
	}

	function ListAdmItems() {
		$this->GetTreeItems($this->Items,0);
		$this->PrepareTableList();
		$this->LoadTemplate('nestable.tpl.php');
	}
	
	function NewAdmItem() {
		$values['IDFather']=$this->idparent;
		$this->NewItem($values);
		$this->TotalLanguages=0;	
		$this->PrepareForm();
		$this->LoadTemplate('edit.tpl.php');
	}

	function PostAdmItem($redirect=true) {
		$this->PostItem($redirect);
	}

	function DeleteAdmItem($id=0) {
		$devolver=false;
		if ($id==0) { $id=$this->id; $devolver=true;}
		$Total=parent::$db->GetDataListFromSQL("SELECT * FROM " . $this->table . " WHERE IDFather=" . $id, $Items);
		if ($Total>0) {
			foreach($Items as $item) {
				$this->DeleteAdmItem($item['ID']);
			}
		}
		$resultado=$this->DeleteItem($id);
		if ($devolver) { echo intval($resultado); }
	}
	
	function GenerateMenu(&$level,$id=0) {
		if ($level=="") { $level=$this->Menu; }
		$TotalListado=self::$db->GetDataListFromSQL('SELECT * FROM ' . $this->table . " WHERE IDFather=" . $id . " ORDER BY Orden", $Listado);
		$return=false; //Devuelve el estado del item y sus subitems
		if ($TotalListado>0) {
			$url=$_SERVER['REQUEST_URI'];
			if (sitefolder!="/") { $url=str_replace(sitefolder, '', $url); }
			$pos=strpos($url, '?');
			if ($pos!==false) { $url=substr($url,0,$pos); }
			if (substr($url, strlen($url)-1)=='/') { $url=substr($url, 0,strlen($url)-1); }
			$previousIndexSelected=false;
			foreach($Listado as $item) {
				unset($subitem);
				$subitem=array();
				if (siteLang!=$this->userlang) { parent::$db->GetTranslate($this->table,$item['ID'],$this->userlang,$item); }
				$subitem['Title']=$item['Title'];
				$subitem['Link']=$item['Link'];
				$subitem['Image']=$item['Image'];
				$subitem['Icon']=$item['Icon'];
				$subitem['Items']=array();
				$subitem['Selected']=false;
				$selected=false; //Guarda el estado del item actual
				if (($url!="") and ($item['Link']!="")) {
					if (strpos($url,$item['Link'])!==false) { $selected=true; $return=true; } // $url==$item['Link']
				}
				if ($url=="") {
					if (($item['Link']=="./") or ($item['Link']=="/")) { $selected=true; $return=true; $previousIndexSelected=true; }
					if (($item['Link']=="") and (! $previousIndexSelected)) { $selected=true; $return=true; $previousIndexSelected=true; }
				}
				if ($selected) { $subitem['Selected']=true; }
				$add=array_push($level, $subitem);
				$result=$this->GenerateMenu($level[$add-1]['Items'],$item['ID']);
				//Si no está el item actual seleccionado, pero si uno de sus subitems, cambiamos el estado.
				if ((! $selected) and ($result) and (! $this->already_selected)) { $selected=true; $this->already_selected=true; }
				//Devolvemos al array la selección, sólo si es el nivel inicial
				//if ($id==0) { 
					$level[$add-1]['Selected']=$selected;
				//}
			}
		}
		return $return;
	}

	function PrepareTableList() {
		$this->AddMainMenu('Crear',$this->module . '/new');
		$this->AddTableContent('Nombre','data','{{Title}}','',$this->module . '/edit/id/{{ID}}');
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block);
		$this->AddTableOperations($in_block,'Crear submenú',$this->module . '/new/idparent/{{ID}}','{{CheckMaxDepth}}<' . $this->conf->Export('MaxLevels'));
		$this->AddTableOperations($in_block,'Editar',$this->module . '/edit/id/{{ID}}');
		$this->AddTableOperations($in_block,'Eliminar',$this->module . '/delete/id/{{ID}}');
		$this->MaxDepth=$this->conf->Export('MaxLevels');
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('General');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Título","FieldName":"Form_Title","Value":"' . addcslashes($this->Data['Title'],'\\"') . '","Required": true}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Enlace","FieldName":"Form_Link","Value":"' . $this->Data['Link'] . '","Help": "Ruta relativa a la web actual (Permalink) o una URL completa, incluyéndose http:// o https://"}');
		if($this->Check('UseImage')){ $this->AddFormContent($in_block,'{"Type":"upload","Text":"Imagen","FieldName":"Form_Image","Value":"' . $this->Data['Image'] . '","UploadType": "image", "UploadItem":"first", "Extensions": "jpg,jpeg,gif,png", "PreviewFolder": "public/thumbnails", "External":"' . $this->Data['RenameImage'] . '"}'); }
		if($this->Check('UseIcon')){ $this->AddFormContent($in_block,'{"Type":"combo-json","Text":"Icono FontAwesome","FieldName":"Form_Icon","Value":"' . $this->Data['Icon'] . '","JsonValues":' . $this->GetFontAwesomeList() . '}'); }		

		if (($this->Check('EnableMultiBusiness')) and (siteMulti)) { $this->AddFormContent($in_block,'{"Type":"combo","Text":"Empresa","FieldName":"Form_IDBusiness","Value":"' . $this->Data['IDBusiness'] . '", "ListTable": "business", "ListValue": "ID", "ListOption": "Name", "ListOrder":"Name", "NullValue": "0"}'); }
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->AddFormHiddenContent("Form_IDFather",$this->Data['IDFather']);
	}
}
?>