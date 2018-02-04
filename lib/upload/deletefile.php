<?php
//Borra el archivo solicitado de la carpeta TEMP

$files="";
if (isset($_POST['files'])) { $files=$_POST['files']; }
$files=explode(',',$files);
foreach($files as $file) {
	$tmpfile=strtolower(dirname(__FILE__) . '/../../public/temp/' . $file);
	@unlink($tmpfile);
}
echo "1";

?>