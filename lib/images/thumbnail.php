<?php
/********************************************************/
/*	FILE: thumbnail.php									*/
/*  Env�a thumbnail al navegador						*/
/*	Author: Israel Garc�a								*/
/*	Version: 1.4										*/
/*	Last Modified: No modified							*/
/********************************************************/

require_once('thumbs.php');
$gd=2;
if (isset($_GET['src'])) { $src="../../public/" . $_GET['src']; } else { die("Error: No file found!"); }
if (isset($_GET['maxw'])) { $maxw=$_GET['maxw']; } else { $maxw=100; }
if (isset($_GET['maxh'])) { $maxh=$_GET['maxh']; } else { $maxh=0; }
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
$lastmod=filemtime($src);
header("Last-Modified: " . gmdate('D, d M Y H:i:s \G\M\T',$lastmod));
header("Expires: " . gmdate('D, d M Y H:i:s \G\M\T', time() + 604800)); 
thumbnail ($src, $maxw, $maxh, $salvar, $bg);
?>
