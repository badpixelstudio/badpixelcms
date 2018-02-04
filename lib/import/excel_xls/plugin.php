<?php
require_once('reader.php');

$xlsprocess = new Spreadsheet_Excel_Reader();
$actual_row=0;

function make_alpha_from_numbers($number)
{
	$numeric = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	if($number<strlen($numeric))
	{
		return $numeric[$number];
	}
	else
	{
		$dev_by = floor($number/strlen($numeric));
		return "" . make_alpha_from_numbers($dev_by-1) . make_alpha_from_numbers($number-($dev_by*strlen($numeric)));
	}
}

function SelectTable ($archivo) {
	//Comprobamos que el archivo sea leible...
	$maxcolumnas=0;
	$maxfilas=0;
	if (is_readable('../public/import/' . $archivo)) {
		$datos= new Spreadsheet_Excel_Reader();
		$datos->read('../public/import/' . $archivo);
		$maxfilas=$datos->sheets[0]['numRows'];
		$maxcolumnas=$datos->sheets[0]['numCols'];
	echo "<label for='maxfilas'><b><span class='req'>*</span>Filas</b>";
	echo "<input name='maxfilas' type='text' id='maxfilas' value='" . $maxfilas . "' readonly='1' class='formularios'><br />";
	echo "</label>";
	echo "<label for='maxcolumnas'><b><span class='req'>*</span>Columnas</b>";
	echo "<input name='maxcolumnas' type='text' id='maxcolumnas' value='" . $maxcolumnas . "' readonly='1' class='formularios'><br />";
	echo "</label>";
	echo "<br /><br />";
	echo "<label for='nombrecampos'><b><span class='req'>*</span>Fila Campos</b>";
	echo "<select name='nombrecampos' id='nombrecampos' class='formularios'>";
	echo "<option value='0'>Sin nombres de campo</option>";
	if ($maxfilas>0) {
		for ($x=1;$x<=$maxfilas;$x++) {
			echo "<option value='" . $x . "'>" . $x . "</option>";
		}
	}
	echo "</select>";
	echo "<label for='desde'><b><span class='req'>*</span>Procesar desde</b>";
	echo "<select name='desde' id='desde' class='formularios'>";
	if ($maxfilas>0) {
		for ($x=1;$x<=$maxfilas;$x++) {
			$seleccionado="";
			if ($x==1) { $seleccionado=" selected "; }
			echo "<option" . $seleccionado . " value='" . $x . "'>" . $x . "</option>";
		}
	}
	echo "</select>";	
	echo "</label>";	
	echo "<label for='hasta'><b><span class='req'>*</span>Procesar hasta</b>";
	echo "<select name='hasta' id='hasta' class='formularios'>";
	if ($maxfilas>0) {
		for ($x=1;$x<=$maxfilas;$x++) {
			$seleccionado="";
			if ($x==$maxfilas) { $seleccionado=" selected "; }
			echo "<option" . $seleccionado . " value='" . $x . "'>" . $x . "</option>";
		}
	}
	echo "</select>";	
	echo "</label>";	
	
	
	}
}

function ViewFile($archivo) {
	if (is_readable('../public/import/' . $archivo)) {
		echo "<a href='../lib/import/excel_xls/view.php?file=" . $archivo . "' target='_blank'>" . "<img src='../lib/import/excel_xls/images/view.png' border=0 alt='Ver archivo'>" . "</a>";
	}
}


function ComboRelations($archivo,$filacabeceras=0) {
	$maxcolumnas=0;
	$maxfilas=0;
	if (is_readable('../public/import/' . $archivo)) {
		$datos= new Spreadsheet_Excel_Reader();
		$datos->read('../public/import/' . $archivo);
		$maxfilas=$datos->sheets[0]['numRows'];
		$maxcolumnas=$datos->sheets[0]['numCols'];
		for ($x=1;$x<=$maxcolumnas;$x++) {
			echo "<option value='" . ($x) . "'>";
			echo make_alpha_from_numbers($x-1);
			if ($filacabeceras!=0) {
				echo ": " . nl2br(htmlentities($datos->sheets[0]['cells'][$filacabeceras][$x]));
			}
			echo "</option>";
		}
	}
}

function OpenFile($archivo,$procesardesde) {
	$GLOBALS['actual_row']=0;
	$devolver=false;
	if (is_readable('../public/import/' . $archivo)) {
		$GLOBALS['xlsprocess']->read('../public/import/' . $archivo);
		$GLOBALS['actual_row']=$procesardesde;
		$devolver=true;
	}
	return $devolver;
}

function GetData($columna) {
	$fila=$GLOBALS['actual_row'];
	$devolver="";
	if (isset($GLOBALS['xlsprocess']->sheets[0]['cells'][$fila][$columna])) {
		$devolver=nl2br(htmlentities($GLOBALS['xlsprocess']->sheets[0]['cells'][$fila][$columna]));
	}
	return $devolver;
}

function MoveNext() {
	$GLOBALS['actual_row']++;
}
	
	
	
?>