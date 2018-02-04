<?php
//Base de Datos
define ("dbserver", "{{dbserver}}"); //Dirección del servidor MySQL, por defecto "localhost"
define ("dbname", "{{dbname}}"); //Base de datos utilizada, por defecto "badpixelcms"
define ("dbuser", "{{dbuser}}"); //Usuario de la base de datos, por defecto "root"
define ("dbpsw", "{{dbpsw}}"); //Contraseña de acceso a la base de datos, escriba la contraseña del usuario especificado.

define ("sitealiasfolder", ""); //En blanco si los docs no estan alojados en un dir. virtual
define ("sitePanelFolder", "{{backend}}"); //Carpeta del panel de administrador, por defecto "admin"

date_default_timezone_set('Europe/Madrid'); //Selecciona la zona horaria, obligatorio en últimas versiones de PHP

// ***** I M P O R T A N T E ****
// NO CAMBIAR NINGUN PARAMETRO A PARTIR DE ESTE PUNTO

//AUTODETECCIÓN DE LA INSTALACIÓN DEL CMS
$fld=$_SERVER['DOCUMENT_ROOT'];
if (isset($_SERVER['CONTEXT_DOCUMENT_ROOT'])) { $fld=$_SERVER['CONTEXT_DOCUMENT_ROOT']; }
$this_file=str_replace("\\", "/", __DIR__);
$route=str_replace($fld, "", $this_file);
$route=str_replace("include/core","",$route);
$protocol="http://";
if (isset($_SERVER['HTTPS'])) { $protocol="https://"; }
define ("sitefolder", $route);
define ("siteprotocol", $protocol);
//Si no se desea utilizar el autodetector de ruta de instalación, habilitar estas líneas
//define ("sitefolder", "{{folder}}");
//define ("siteprotocol", "{{protocol}}");
define ("sitedomain", $_SERVER['HTTP_HOST'] . sitealiasfolder . sitefolder);
define ("sitepath", $fld . sitefolder);

?>