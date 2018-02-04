<?php
/********************************************************/
/*	FILE: thumbnail.php									*/
/*  Envía thumbnail al navegador						*/
/*	Author: Israel García								*/
/*	Version: 1.4										*/
/*	Last Modified: No modified							*/
/********************************************************/

require_once('../lib/images/thumbs.php');
$gd=2;
if (isset($_GET['src'])) { $src=$_GET['src']; } else { die("Error: No file found!"); }
if (isset($_GET['maxw'])) { $maxw=$_GET['maxw']; } else { $maxw=100; }
if (isset($_GET['maxh'])) { $maxh=$_GET['maxh']; } else { $maxh=$maxw; }
if (isset($_GET['saveto'])) { $salvar=$_GET['saveto']; } else { $salvar=null; }
$min=false;
$cut=false;
if (isset($_GET['min'])) { if (($_GET['min']=="true") or ($_GET['min']=="yes")) { $min=true; } }
if (isset($_GET['cut'])) { if (($_GET['cut']=="true") or ($_GET['cut']=="yes")) { $cut=true; } }
if (isset($_GET['bg'])) { 
	$bg=$_GET['bg']; 
} else { 
	$bg='';
	if (($min) or ($cut)) { $bg="crop"; } 
}

thumbnail ($src, $maxw, $maxh, $salvar, $bg);
?>
