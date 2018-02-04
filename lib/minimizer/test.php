<?php 
require_once("../../include/core/common.php");
require_once("../../include/core/core.class.php");
require_once("functions.php"); 
$Core= new Core($_GET);
echo GetCSSMin("gestion","assets/css");


die();
// $texto='y si no existe que pasa background:UrL(../images/img.jpg); bg2:url("../images/img.jpg"); b3:url(\'../fonts/font.ttf\'); b4:url("http://hola.com");';

// $partes=explode("\n",$texto);
// $pattern='/url\(["\']?(?!http)([^"\']+)["\']?\)/i';
// $replace='url(ss$1)';
// foreach ($partes as $id=>$parte) {
// 	$salida=preg_replace_callback(
// 		$pattern, 
// 		function ($coincidencias) {
// 			$valor=$coincidencias[1];
// 			if (strpos($valor,"http")!==false) {
// 				return $coincidencias[0];
// 			} else {
// 				return "url('../../templates/css/" . $valor . "')";
// 			}
//         },
//         $parte);
// 	echo $id . " " . $salida . "<hr>";
// }

// $pattern='#url\(["\']?([^"\']+)+["\']?\)#';
// $pattern='#url\(["\']?([^"\']+)["\']?\)#'; //Reducida
// $pattern='url\(["\']?(http[^"\']+)["\']?\)' //Selecciona solo los http://
// $pattern='/url\(["\']?(?!http)([^"\']+)["\']?\)/i'; // **** DEFINITIVA ****
?>