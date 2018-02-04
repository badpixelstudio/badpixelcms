<?php
require_once(sitepath . "include/core/core.class.php");
require_once(sitepath . "include/business/business.config.php");
require_once(sitepath . "lib/images/thumbs.php");	

class MasterbModules extends Core{
	var $title = 'Módulos de Empresa';
	var $class = 'business';
	var $module = 'business';
	var $typemodule='business';
	var $InstallAdminMenu=array(array('Block' => 'business', 'Icon' => 'fa-building'));
	var $table = 'business_modules';
	var $version = false;
	
	function __construct($values) {
		parent::__construct($values);  
		$this->conf = new ConfigBusiness();
		$this->BreadCrumb['Empresas']=$_SERVER['PHP_SELF'];
		$Empresa=parent::$db->GetDataRecord("business",$this->id);
		$this->BreadCrumb[$Empresa['Name']]=$_SERVER['PHP_SELF'] . "&action=edit&id=" . $this->id;	
		$this->BreadCrumb['Módulos']=$_SERVER['PHP_SELF'] . "&action=modules_list&id=" . $this->id;	
		if ($this->id!=0) { $this->CheckItemBusinessPermission($this->id, true); }
	}
	
	function GetItems($conditions="",$paged=false,$order="",$search=false,$translate=true,$SQLselect="") {
		//Cargamos todos los módulos existentes en la carpeta de administración...	
		$modulos=NULL;
		//Agregamos los módulos básicos...
		// $otrasopciones=array('_data','_images','_attachments','_links','_videos');
		// foreach($otrasopciones as $archivo) {
		// 	$query_roles = "SELECT COUNT(ID) as Total FROM business_modules WHERE IDBusiness=" . $this->id . " AND OptionFile='" . $archivo . "'";
		// 	$Contador=parent::$db->GetDataFieldFromSQL($query_roles,'Total');	
		// 	$volcar['OptionFile']=$archivo;
		// 	if ($Contador==0) {
		// 		$volcar['Active']=0;	
		// 	} else {
		// 		$volcar['Active']=1;	
		// 	}
		// 	$modulos[]=$volcar;			
		// }
		foreach($this->Permissions as $module=>$datos) { 
			if (($module!=="business")&&($module!=="config")&&($module!=="levels")&&($module!=="users")&&($module!=="permalinks")
				&&(strpos($module, "core")===false)&&($module!=="modules")&&($module!=="comments")&&($module!=="apps")&&($module!=="backup")) {
				$texto=$this->GetModuleName($module);
				if ($texto=="") { $texto=$module; }
				$query_roles = "SELECT COUNT(ID) as Total FROM business_modules WHERE IDBusiness=" . $this->id . " AND OptionFile='" . $module . "'";
				$Contador=parent::$db->GetDataFieldFromSQL($query_roles,'Total');
				$volcar['OptionFile']=$module;
				$volcar['Name']=$this->GetModuleName($module);
				if ($Contador==0) {
					$volcar['Active']=0;	
				} else {
					$volcar['Active']=1;	
				}
				$modulos[]=$volcar;
			}
		}
		$this->Items=$modulos; 
		$this->ItemsCount=count($modulos);
		return true;
	}
	
	function AdminList() {
		$this->GetItems();
		$this->LoadTemplate('business_modules.tpl.php'); 
		
	}

	function ChangeActivation() {
		$query_roles="SELECT COUNT(ID) as Total FROM business_modules WHERE IDBusiness=" . $this->id . " AND OptionFile='" . $this->_values['mod'] . "'";
		$Contador=parent::$db->GetDataFieldFromSQL($query_roles,'Total');
		if ($Contador==0) {
			$Datos['System_Action']="new";
			$Datos['System_ID']=-1;
			$Datos['Form_IDBusiness']=$this->id;
			$Datos['Form_OptionFile']=$this->_values['mod'];	
			$this->PostToDatabase($this->table,$Datos);
		} else {
			$sql="DELETE FROM " . $this->table . " WHERE IDBusiness=" . $this->id . " AND OptionFile='" . $this->_values['mod'] . "'";
			parent::$db->Qry($sql);
		}
		echo "1";			
	}

	function ConfModule() {
		if (! isset($this->_values['mod'])) { header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "--" . $this->class . "/id/" . $this->id . "/error/" . urlencode(base64_encode("Falta especificar el módulo a configurar"))); exit; }
		$module=$this->_values['mod']; 
		$clase="";
		$claseconf="";
		if (! is_file(sitepath . "include/".$module."/".$module.".class.php")) { header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "--" . $this->class . "/id/" . $this->id . "/error/" . urlencode(base64_encode("El módulo no existe"))); exit; }
		require_once(sitepath . "include/".$module."/".$module.".class.php");
		$php_code = file_get_contents(sitepath . "include/".$module."/".$module.".class.php");
		$classes = get_php_classes($php_code);
		if (count($classes)>0) {
			$clase=$classes[0];
			$php_code = file_get_contents(sitepath . "include/".$module."/".$module.".config.php");
			$classes = get_php_classes($php_code);
			$claseconf=$classes[0];
		}
		if ($clase=="") { header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "--" . $this->class . "/id/" . $this->id . "/error/" . urlencode(base64_encode("El módulo no admite configuración"))); exit; }
		$this->changeconf = new $claseconf($this->id);
		$this->changeconf->PrepareForm($this,$this->changeconf);
		$this->title= "Configurar módulo " . $module;
		$this->TemplatePostScript=$this->module . "/modules_configpost/mod/" . $this->_values['mod'] . "/id/" . $this->_values['id'];
		$this->LoadTemplate('edit.tpl.php');		
	}

	function ConfModulePost() {
		if (! isset($this->_values['mod'])) { header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "--" . $this->class . "/id/" . $this->id . "/error/" . urlencode(base64_encode("Falta especificar el módulo a configurar"))); exit; }
		$module=$this->_values['mod']; 
		$clase="";
		$claseconf="";
		if (! is_file(sitepath . "include/".$module."/".$module.".class.php")) { header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "--" . $this->class . "/id/" . $this->id . "/error/" . urlencode(base64_encode("El módulo no existe"))); exit; }
		require_once(sitepath . "include/".$module."/".$module.".class.php");
		$php_code = file_get_contents(sitepath . "include/".$module."/".$module.".class.php");
		$classes = get_php_classes($php_code);
		if (count($classes)>0) {
			$clase=$classes[0];
			$php_code = file_get_contents(sitepath . "include/".$module."/".$module.".config.php");
			$classes = get_php_classes($php_code);
			$claseconf=$classes[0];
		}
		if ($clase=="") { header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "--" . $this->class . "/id/" . $this->id . "/error/" . urlencode(base64_encode("El módulo no admite configuración"))); exit; }
		$this->changeconf = new $claseconf($this->id);
		$this->changeconf->PostConfig($_POST);
		header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . "business/modules_list/id/" . $_POST['id'] . "/text/" . urlencode(base64_encode(_("Configuración guardada correctamente"))));
	}
	
	function DefaultActivationModules() {
		$modulos=explode(',',$this->conf->Export('DefaultActiveModules'));
		$sql="SELECT * FROM business";
		$total=parent::$db->GetDataListFromSQL($sql,$empresas);
		if ($total>0) {
			foreach ($empresas as $empresa) {
				foreach($modulos as $modulo) {
					$query_roles="SELECT COUNT(ID) as Total FROM business_modules WHERE IDBusiness=" . $empresa['ID'] . " AND OptionFile='" . trim($modulo) . "'";
					$Contador=parent::$db->GetDataFieldFromSQL($query_roles,'Total');
					if ($Contador==0) {
						unset($Datos);
						$Datos['System_Action']="new";
						$Datos['System_ID']=-1;
						$Datos['Form_IDBusiness']=$empresa['ID'];
						$Datos['Form_OptionFile']=trim($modulo);	
						$this->PostToDatabase($this->table,$Datos);
					}
				}
			}
		}
		header("Location: " . $_SERVER['PHP_SELF'] . "?text=" . urlencode('Se ha cargado la configuración por defecto para todas las empresas'));
	}

	function ChangeMassive() {
		//Agregamos los módulos básicos...
		// $this->modulos=array('_data','_images','_attachments','_links','_videos');
		foreach($this->Permissions as $module=>$datos) { 
			if (($module!=="business")&&($module!=="config")&&($module!=="levels")&&($module!=="users")&&($module!=="permalinks")
				&&(strpos($module, "core")===false)&&($module!=="modules")&&($module!=="comments")&&($module!=="apps")&&($module!=="backup")) {
				$this->modulos[]=$module;
			}
		}
		
		$this->PrepareMassiveForm();
		$this->LoadTemplate('edit.tpl.php'); 
	}
	
	function ChangeMassiveSave() {
		PatchCheckBox($_POST,"Form_Value");
		if ($_POST['Form_Value']==0) {
			$sql="DELETE FROM business_modules WHERE IDBusiness<>0 AND OptionFile='" . trim($_POST['Form_OptionFile']) . "'";
			parent::$db->Qry($sql);
			$accion="eliminado";
		} else {	
			$sql="SELECT ID FROM business";
			$total=parent::$db->GetDataListFromSQL($sql,$empresas);
			if ($total>0) {
				foreach ($empresas as $empresa) {
					$query_roles="SELECT COUNT(ID) as Total FROM business_modules WHERE IDBusiness=" . $empresa['ID'] . " AND OptionFile='" . trim($_POST['Form_OptionFile']) . "'";
					$Contador=parent::$db->GetDataFieldFromSQL($query_roles,'Total');
					if ($Contador==0) {
						unset($Datos);
						$Datos['System_Action']="new";
						$Datos['System_ID']=-1;
						$Datos['Form_IDBusiness']=$empresa['ID'];
						$Datos['Form_OptionFile']=trim($_POST['Form_OptionFile']);	
						$this->PostToDatabase($this->table,$Datos);	
					}
				}
			}
			$accion="habilitado";
		}
		header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module ."/text/" . urlencode(base64_encode('Se ha ' . $accion . ' el permiso del modulo ' . trim($_POST['Form_OptionFile']) . ' para todas las empresas')));
	}

	function PrepareMassiveForm() {
		$salida=array();
		foreach($this->modulos as $modulo) { $salida[$modulo]=$modulo; }
		$opciones=json_encode($salida,true);
		$in_block=$this->AddFormBlock('Cambiar permisos a todas las empresas');
		$this->AddFormContent($in_block,'{"Type":"combo-json","Text":"Módulo","FieldName":"Form_OptionFile","Value":"", "JsonValues": ' . $opciones . '}');
		$this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Activar","FieldName":"Form_Value","Value":"0"}');
		$this->TemplatePostScript=siteprotocol . sitedomain . sitePanelFolder . "/business/modules_changemassive_post";
	}
	
	/*********************/
	/*	ACCION DEL ADMIN */
	/*********************/		
	
	function RunAction() {
		if ($this->action=="list") { $this->AdminList(); }
		if ($this->action=="switch") { $this->ChangeActivation(); }
		if ($this->action=="config") { $this->ConfModule(); }
		if ($this->action=="configpost") { $this->ConfModulePost(); }
		if ($this->action=="changemassive") { $this->ChangeMassive(); }
		if ($this->action=="changemassive_post") { $this->ChangeMassiveSave(); }
		if ($this->action=="default") { $this->DefaultActivationModules(); }
	}
}
?>