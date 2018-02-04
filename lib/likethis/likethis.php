<?php
ini_set("display_errors", "0");
require_once('../../include/core/common.php');	
require_once(sitepath . "/include/core/common.php");
require_once(sitepath . "/include/core/core.class.php");

$Core=new Core();

$permalink="";
$voto='+';
$accion="votar";
if (isset($_GET['permalink'])) { $permalink=$_GET['permalink']; }
if (isset($_GET['vote'])) { 
	if ($_GET['vote']=='sub') {$voto='-'; } 
} else { 
	$action="ver"; 
}

$Devolver['Status']='Error';

//Desgranamos el permalink...
$Devolver['Status']=$estado=$Core->LikeThis($voto,$permalink,true);
$Devolver['yes']=$Core->GetLikes('+',$permalink);
$Devolver['no']=$Core->GetLikes('-',$permalink);

echo json_encode($Devolver);
?>