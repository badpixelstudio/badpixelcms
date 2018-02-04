<?php 
if(! $Core->ModuleInstalledAndEnabled('config')) { header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/home/error/" . urlencode(base64_encode("No dispones de permiso para configurar la aplicación"))); die; }
if(isset($params['mod'])){ 
	$module=$params['mod']; 
} else {
 	if(isset($_POST['module'])) {	
 		$module=$_POST['module'];
 	} else { 
 		$module='users';
 	}
}

if(!isset($params['action'])){ $params['action']="list"; }

//Reiniciamos los permisos del usuario ROOT
require_once(sitepath . "include/levels/levels.class.php");
$tmp=new Level($params);

$Core->BreadCrumb['Configuración']='';


//Cargamos la clase seleccionada
$clase="";
$claseconf="";
if (is_file(sitepath . "include/".$module."/".$module.".class.php")) {
	require_once(sitepath . "include/".$module."/".$module.".class.php");
	$php_code = file_get_contents(sitepath . "include/".$module."/".$module.".class.php");
	$classes = get_php_classes($php_code);
	if (count($classes)>0) {
		$clase=$classes[0];
		$php_code = file_get_contents(sitepath . "include/".$module."/".$module.".config.php");
		$classes = get_php_classes($php_code);
		$claseconf=$classes[0];
	}
} else {
	header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $Core->module ."/error/" . urlencode(base64_encode("No se encuentra el modulo de configuración")));
	exit;	
}


if ($clase!="") {
	$Core=new $clase($params);
	$Core->id=$Core->businessID;
	if (isset($_GET['id'])) { $Core->id=$_GET['id']; }
	$Core->conf = new $claseconf($Core->id);
} else {
	$url_referer=$_SERVER['HTTP_REFERER'];
	header("Location: " . $salida ."/error/" . urlencode(base64_encode(_("El modulo no admite configuración"))));	
}


//Cargamos las variables que nos devuelve la url.
if ($module!="core") { $Core->CheckPermission($module); }
if ($Core->action=="list") {
	$Core->title=_("Configuración de módulos");
	$Core->Items=array();
	$Core->ItemsCount=1;
	unset($item);
	$item['ID']=0;
	$item['Module']="core";
	$item['ModuleName']=_("Núcleo del sistema") . ' (core)';
	$item['Version']="[" . _("Desconocido") . "]";
	$data=$Core::$db->GetDataRecordFromSQL("SELECT * FROM modules_installed WHERE Module='core'");
	if ($data!==false) {$item['Version']=$data['Version'];}
	array_push($Core->Items,$item);
	foreach($Core->Permissions as $module=>$datos) { 
		if ((strpos($module, "--")===false) and ($datos==1)) {
			unset($item);
			$Core->ItemsCount++;
	   		$item['ID']=$Core->ItemsCount;
	   		$item['Module']=$module;
	   		$item['ModuleName']=$module;
	   		$item['Version']="[" . _("Desconocido") . "]";
			$data=$Core::$db->GetDataRecordFromSQL("SELECT * FROM modules_installed WHERE Module='" . $module . "'");
			if ($data!==false) {
				$item['ModuleName']=$data['ModuleName'] . ' (' . $module . ')';
	   			$item['Version']=$data['Version'];
			}
			array_push($Core->Items,$item);
		}
	}
	$Core->AddTableContent('','data','','{{ID}})','config/editconfig/mod/{{Module}}/id/' . $Core->businessID);
	$Core->AddTableContent('Módulo','data','{{ModuleName}}','','config/editconfig/mod/{{Module}}/id/' . $Core->businessID);
	$Core->AddTableContent('Versión Actual','data','{{Version}}');
	$in_block=$Core->AddTableContent('Operaciones','menu');
	$Core->AddTableOperations($in_block,'Editar','config/editconfig/mod/{{Module}}/id/' . $Core->businessID);
	$Core->AddTableOperations($in_block,'Volver a configuración de fábrica','config/reloadconfig/mod/{{Module}}/id/' . $Core->businessID);
	$Core->BreadCrumb=array();
	$Core->BreadCrumb['Inicio']=siteprotocol . sitedomain . sitePanelFolder;
	$Core->BreadCrumb[$Core->title]='config';
	$Core->LoadTemplate('list.tpl.php');	
}

if ($Core->action=="editconfig") {
	$tmp_title=$Core->title;
	$Core->title=_("Configuración");
	$Core->BreadCrumb=array();
	$Core->BreadCrumb['Inicio']=siteprotocol . sitedomain . sitePanelFolder;
	$Core->BreadCrumb[$Core->title]='config';
	$Core->BreadCrumb[$tmp_title]='config';
	$Core->title.=' ' . $tmp_title;
	$Core->BreadCrumb['Editar']='';
	$Core->conf->PrepareForm($Core);
	$Core->showtitle= $Core->title;
	$Core->LoadTemplate('edit.tpl.php');	
}

if ($Core->action=="reloadconfig") {
	$Core->conf->ReloadConfig($Core->businessID,$module);
	header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/config/list/text/" . urlencode(base64_encode(_("Configuración del módulo actualizada correctamente"))));
}

if ($Core->action=="postconfig") {
	$Core->conf->PostConfig($_POST);
	if ($_POST['id']==$Core->businessID) {
		header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/config/list/text/" . urlencode(base64_encode(_("Configuración guardada correctamente"))));
	} else {
		header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . "business/modules_list/id/" . $_POST['id'] . "/text/" . urlencode(base64_encode(_("Configuración guardada correctamente"))));
	}
}

?>