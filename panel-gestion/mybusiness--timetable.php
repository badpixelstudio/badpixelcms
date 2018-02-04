<?php 
if (is_file(sitepath . "include/business/timetable.class.php")) { require_once(sitepath . "include/business/timetable.class.php"); }
$Core=new Core($params);
if ($Core->businessID==0) { header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/home/error/" . urlencode(base64_encode("La entidad principal no puede gestionarse desde esta opción"))); exit; }
$params['idparent']=$Core->businessID;
$Dispatcher=new bTimeTable($params);
$Dispatcher->RunDispatcher();
?>