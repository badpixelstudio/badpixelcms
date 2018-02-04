<?php 
require_once(sitepath . "include/users/users.class.php");

//Cargamos las variables que nos devuelve la url.
$Core = new User($params);

$login_username="";
$login_userpass="";
$login_recuerda="";

if(isset($_POST['username'])){ $login_username=$_POST['username'];}
if(isset($_POST['password'])){ $login_userpass=$_POST['password']; }
if(isset($_POST['remember'])){ $login_recuerda=$_POST['remember']; }
if(isset($_POST['urlrefer'])){ $Core->action="login"; }

if ($Core->action=="login") {
	$result=$Core->Login($login_username,$login_userpass,$login_recuerda);
	if (isset($_POST['urlrefer'])) { 
		if ($result==1) {
			$redirect=$_POST['urlrefer'];
			if($redirect=="") { $redirect=siteprotocol . sitedomain . sitePanelFolder; }
			header("Location: " . $redirect); exit; 
		} else {
			$_GET['text']=_("Usuario o contraseña inválida");
			$Core->action="";
		}
	} else {
		die($result);
	}
}

if (($Core->action=="") or ($Core->action=="start") or ($Core->action=="list")) {
	$Core->Data['Form_Redirect']="";
	if (isset($_SESSION[siteCookie . 'Return'])) { 
		$url=$_SESSION[siteCookie . 'Return'];
		$pos=strpos($url, sitefolder);
		if ($pos!==false) { $url=substr($url, $pos); }
		$url=str_replace(sitefolder, "", $url);
		$url=siteprotocol . sitedomain . $url;
		$Core->Data['Form_Redirect']=$url;}
	$Core->loadtemplate('security_login.tpl.php');		
}
 
if ($Core->action=="logout") {
	$close_all=false;
	if ((isset($_GET['closeall'])) or (isset($Core->_values['closeall']))) { $close_all=true; }
	$Core->Logout($close_all);
	header("Location: " . siteprotocol . sitedomain . sitePanelFolder);
	exit(0);
}

if ($Core->action=="send_password") {
	$Core->loadtemplate('security_password_retrieve.tpl.php');	
}

if ($Core->action=="do_sendpwd") {
	echo $Core->SendPassword($_POST);
}

if ($Core->action=="create_password") {
	$Datos=$Core->ChangePasswordGetProfile();	
	//if ($Datos===false) { header("Location: security.php?action=start&text=" . urlencode('El token facilitado no es válido')); exit(); }	
	$Core->LoadTemplate('security_password_create.tpl.php');	
}

if ($Core->action=="do_changepwd") {	
	echo intval($Core->SetNewPassword($Core->userID, $_POST));
}

if ($Core->action=="send_activation") {	
	$estado=$Core->SendActivation($_POST);
	if ($estado) {
		header("Location: security.php?action=start&text=" . $mensaje);
	} else {
		if ($mensaje!="") { $estado=$Core->error=$mensaje; }
		$Core->LoadTemplate('security_activation_retrieve.tpl.php');	
	}		
}

if ($Core->action=="activate") {
	$activar=$Core->ActivateAccount($_GET['token']);
	if ($activar==-1) { $text= "El token no ha sido aceptado"; }
	if ($activar==0) { $text= "La cuenta ya estaba activada"; }
	if ($activar==1) { $text= "La cuenta ha sido activada."; }
	header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/security?text=". _($text));
}

if ($Core->action=="edit") {
	
	if ($Core->userID!=0) {
		$Core->conf = new ConfigUsers();
		$Core->id=$Core->userID;
		$Core->EditItem($Core->userID);
		$Core->PrepareForm(false);
		$Core->TemplatePostScript="security/post";
		$Core->LoadTemplate('user_my_edit.tpl.php');
		
	} else {
		$Core->CheckPermission('admusers.php');
	}
}

if ($Core->action=="post") {
	if ($Core->userID!=0) {
		$Core->conf = new ConfigUsers();
		$_POST['System_ID']=$Core->userID;
		$_POST['System_Action']="edit";
		$_POST['Form_Active']=1;
		$Core->PostItem(false);
		header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/home/text/" . urlencode(base64_encode("Se han guardado los datos de tu perfil de usuario")));
		exit;
	} else {
		$Core->CheckPermission('admusers.php');
	}
}

if ($Core->action=="business_change") {
	$business=0;
	if (isset($_POST['IDBusiness'])) { $business=$_POST['IDBusiness']; }
	if (isset($_GET['IDBusiness'])) { $business=$_GET['IDBusiness']; }
	if (isset($Core->_values['IDBusiness'])) { $business=$Core->_values['IDBusiness']; }
	if ($business!=0) {
		$_SESSION['Business']=$business; 
	} else {
		$_SESSION['Business']=0;
		unset($_SESSION['IDBusiness']);
	}
	header("Location: " . $_SERVER['HTTP_REFERER']);
}

if ($Core->action=="twitter") { 
	ini_set("display_errors", 1);
	//Tweet("Prueba http://" . sitedomain "http://" . sitedomain . "public/images/auto-galleries111-leyendas-panoramica.jpg"); 
	echo "deshabilitado del administrador";
}

if ($Core->action=="fbphoto") { 
	ini_set("display_errors", 1);
	//FBUploadImage('Prueba',sitepath . '/public/images/verpagina.jpg','Titulo'); 
	echo "deshabilitado del administrador";
}

if ($Core->action=="refresh") {
	ini_set("display_errors" , 1);
	echo "Refresco de Cache<hr>";
	echo shell_exec('sudo touch /var/cache/mod_pagespeed/cache.flush');
}

if ($Core->action=="searchtw") {
	ini_set("display_errors" , 1);
	echo "Consulta Twitter<hr>";

	$datos=SearchStatus($_GET['q'], $Core->userData['twitter_access_token'],$Core->userData['twitter_access_token_secret']);
	print_r($datos);
}

?>