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
	$actual_value="";
	$element="";
	if (isset($_GET['id'])) { $showid=$_GET['id']; }
	if (isset($_GET['value'])) { $actual_value=$_GET['value']; }
	if (isset($_GET['table'])) { $table=$_GET['table']; }
	if (isset($_GET['element'])) { $elemento=$_GET['element']; }

	$elemento_name=str_replace('Form_','Form_IncludeIn',$elemento);

	$query_getlocalidades = "SELECT * FROM " . $table . " WHERE IDFather = '" . $showid ."'";
	$db->GetDataListFromSQL($query_getlocalidades,$Elementos);
	echo '<select name="' . $elemento_name . '" id="' . $elemento . '">';
	$selected="";
	$helper_zones=0;
	if (($actual_value=="") or ($actual_value=="0")) { $selected=' selected="selected" '; }	
	echo '<option value="0"' . $selected . '>No seleccionado</option>';
	foreach ($Elementos as $item) {
		$selected="";
		if ($item['ID']==$actual_value) { $selected=' selected="selected" '; $helper_zones=$item['ID']; }
		echo '<option value="' .$item['ID'] . '"' . $selected . '>' . ucwords(stripslashes(strtolower($item['Name']))) . '</option>';
	}	
	echo '</select>';

	exit;
?>
