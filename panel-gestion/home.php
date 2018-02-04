<?php 
if (is_file(sitepath . "include/calendar/calendar.class.php")) { require_once(sitepath . "include/calendar/calendar.class.php"); }
if (is_file(sitepath . "include/extras/comments.class.php")) { require_once(sitepath . "include/extras/comments.class.php"); }
if (is_file(sitepath . "include/calendar/events.class.php")) { require_once(sitepath . "include/calendar/events.class.php"); }

$Core=new Core($params);
$Core->title="Inicio";
$objData = new DBase();

if (is_file(sitepath . "include/extras/comments.class.php")) { 
	$Core->table="users";
	$Core->conf=new ConfigCore(0);
	$Core->XtraComments= new ExtraComments($Core,0);
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