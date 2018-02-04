<?php
require_once('../images/thumbs.php');
$width=100;
$height=100;
$file="";
if (isset($_GET['w'])) { $width=$_GET['w']; }
if (isset($_GET['h'])) { $height=$_GET['h']; }
if (isset($_GET['f'])) { $file=$_GET['f']; }
$width=preg_replace("[^0-9]", "", $width);
$height=preg_replace("[^0-9]", "", $height);


$partes=explode('.',$file);
$extension=strtolower($partes[count($partes)-1]);
if (in_array($extension,array('jpg','jpeg','gif','png'))) {
	$src=strtolower(dirname(__FILE__) . '/../../public/temp/' . $file); 
	$lastmod=filemtime($src);
	//header("Last-Modified: " . gmdate('D, d M Y H:i:s \G\M\T',$lastmod));
	//header("Expires: " . gmdate('D, d M Y H:i:s \G\M\T', time() + 604800));
	thumbnail ($src, intval($width)+15, intval($height)+15, null, 'crop');
}

?>