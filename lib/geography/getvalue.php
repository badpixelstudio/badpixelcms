<?php
	/*Recibe los siguientes parámetros...
	id			El ID actual del combo que acaba de cambiar
	element		El tipo de elemento a cargar (IDCity* o IDZone) *=Por defecto
	value		Valor actual seleccionado del combo a cargar
	secondary	Valor actual de la zona, sólo aplicable si el combo es IDCity.
	*/
	require_once("../../include/core/common.php");
	require_once("../../include/core/database.class.php");
	$db = DBase::getInstance();
	if (isset($_GET['id'])) { $showid=$_GET['id']; }
	if (isset($_GET['table'])) { $table=$_GET['table']; }


	$query_getlocalidad = "SELECT * FROM " . $table . " WHERE ID = '" . $showid ."'";
	$valor=$db->GetDataFieldFromSQL($query_getlocalidad,'Name');
	if ($valor!==false) { echo ucwords(stripslashes(strtolower($valor))); }
	exit;
?>
