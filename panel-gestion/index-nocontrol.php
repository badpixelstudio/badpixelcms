<?php
require_once('../include/core/common.php');	
require_once(sitepath . 'include/core/core.class.php');
require_once(sitepath . 'include/core/functions.php');

$url="";
if (isset($_GET['url'])) { $url=$_GET['url']; }
$module=GetParamsAdminLink($url,$params);
//Patch Core Modules...
if ($module=="socialmedia") { $module="core--socialmedia"; }
if ($module=="permalinks") { $module="core--permalinks"; }
if ($module=="modules") { $module="core--modules"; }

if ($module=="security") {
	require_once("security.php");
} else {
	//Comprobamos que estemos logeados...
	$Core=new Core($params);
	if ($Core->userID==0) { header("Location: security"); exit(0); }
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
				$loadClass=$classes[0];
				$Core->Run=new $loadClass($params);
				$Core->Run->RunAction();
			} else {
				header("Location: " . GetAdminLink("home","error=El módulo no está disponible"));
			}
		} else {
			header("Location: " . GetAdminLink("home","error=El módulo " . $module . " no está disponible"));
		}
	}

	

}


?>