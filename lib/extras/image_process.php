<?php 
require_once("../../include/core/common.php");
require_once(sitepath . 'include/core/core.class.php');
$Core=new Core();

if (isset($_GET['action'])) { $action=$_GET['action']; } else { $action="";}

if ($action=="process") {
	require_once(sitepath . "include/extras/images.class.php");
	$modulo=$_SESSION['XtraImagesModule'];
	$id=$_SESSION['XtraImagesID'];
	$options=$_SESSION['XtraImagesOptions'];
	$Xtra=new ExtraImages($modulo,'','IDFather',$id);
	echo $Xtra->PostItemDelayed();
}



?>