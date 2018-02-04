<?php
if (! is_file('../include/core/common.php')) { die("Please, run install first"); }
require_once('../include/core/common.php');	
require_once(sitepath . 'include/core/core.class.php');
require_once(sitepath . 'include/core/functions.php');

$url="";
if (isset($_GET['url'])) { $url=$_GET['url']; }
$module=GetParamsAdminLink($url,$params);
//Patch Core Modules...
$check_module_permissions=$module;
if ($module=="socialmedia") { $module="core--socialmedia"; $check_module_permissions="users"; }
if ($module=="permalinks") { $module="core--permalinks"; $check_module_permissions="users";}
if ($module=="modules") { $module="core--modules"; $check_module_permissions="users";}
if (strpos($module, "core--")!==false) { $check_module_permissions="config";}
if ($module=="users--pm") { $check_module_permissions=false; }
if ($module=="tickets--messages") { $check_module_permissions="tickets";}

if ($module=="security") {
	require_once("security.php");
} else {
	//Comprobamos que estemos logeados...
	$Core=new Core($params);
	if ($Core->userID==0) { 
		if (isset($_SERVER['REQUEST_URI'])) {
			$_SESSION[siteCookie . 'Return']=$_SERVER['REQUEST_URI'];
		}
		header("Location: " .siteprotocol . sitedomain . sitePanelFolder . "/security"); 
		exit(); 
	}
	if (is_file(sitepath . sitePanelFolder . "/" . $module . ".php")) {
		require_once(sitepath . sitePanelFolder . "/" . $module . ".php");
	} else {
		$folder=$module;
		$class=$module;
		$pos=strpos($module, "--");
		if ($pos!==false) {
			$folder=substr($module, 0,$pos);
			$class=substr($module, $pos+2);
			//echo $folder . " " . $class; die;
		}
		if (is_file(sitepath . "include/" . $folder . "/" . $class . ".class.php")) {
			require_once(sitepath . "include/" . $folder . "/" . $class . ".class.php");
			//Cargamos la primera clase que nos encontremos en el documento...
			$php_code = file_get_contents(sitepath . "include/" . $folder . "/" . $class . ".class.php");
			$classes = get_php_classes($php_code);
			if (count($classes)>0) {
				$exec=true;
				if ($check_module_permissions!==false) { $exec=$Core->ModuleInstalledAndEnabled($check_module_permissions); }
				if ($exec) {
					$loadClass=$classes[0];
					$Core->Run=new $loadClass($params);
					$Core->Run->RunAction();
				} else {
					header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . GetAdminLink("home","error=Acceso denegado"));
				}
			} else {
				header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . GetAdminLink("home","error=El módulo no está disponible"));
			}
		} else {
			header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . GetAdminLink("home","error=El módulo " . $module . " no está disponible"));
		}
	}

	

}


?>