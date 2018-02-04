<?php 
if (is_file(sitepath . "include/business/business.class.php")) { require_once(sitepath . "include/business/business.class.php"); }

$Core=new Business($params);
if ($Core->businessID==0) { header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/home/error/" . urlencode(base64_encode("La entidad principal no puede gestionarse desde esta opción"))); exit; }

if ($Core->action=="list") { 
	$Core->EditActualBusiness(); 
}

if ($Core->action=="post") {
	$Core->PostMyBusiness();
}


die;
if (is_file(sitepath . "include/extras/comments.class.php")) { 
	$Core->XtraComments= new ExtraComments('','',0);
	$Core->XtraComments->offset=10;
	$Core->XtraComments->GetAllComments();
}

//Obtenemos los últimos eventos...
$Core->TotalEvents=0;
if ($Core->ModuleInstalledAndEnabled('calendar')) {
	if (is_file(sitepath . "include/calendar/events.class.php")) { 
		$Events=new Events(0);
		$sql_paginas = "SELECT calendar_events.*, '' as BusinessName FROM calendar_events WHERE calendar_events.ID>0 ORDER BY ID DESC";
		if ($Core->EnableBusiness) {
			$sql_paginas = "SELECT calendar_events.*, business.Name as BusinessName FROM calendar_events LEFT JOIN business ON calendar_events.IDBusiness=business.ID WHERE calendar_events.ID>0 ORDER BY ID DESC";	
		}
		$ign="";
		$Core->TotalEvents=$objData->GetDataListPagedFromSQL($sql_paginas,1,10,$Core->DataEvents,$ign);
	}
}

$Core->loadtemplate('index.tpl.php');
?>