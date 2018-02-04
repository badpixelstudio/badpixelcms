<?php
require_once(sitepath . "templates/" . $this->template . "/adapter.php");
require_once(sitepath . "include/apps/apps.class.php");
require_once(sitepath . "include/users/oauth.class.php");

$this->Apps= new Apps($this->params);
$this->OAuth= new OAuth($this->params);

$consumer_key="";
$consumer_token="";
if (isset($_GET['consumer_key'])) { $consumer_key=$_GET['consumer_key']; }
if (isset($_GET['consumer_token'])) { $consumer_secret=$_GET['consumer_token']; }
if (isset($_GET['client_id'])) {
	$datos=$this->OAuth->GetConsumerDataFromClientID($_GET['client_id']);
	if ($datos!==false) {
		$consumer_key=$datos['ConsumerKey'];
		$consumer_token=$datos['ConsumerToken'];
	}
}

if (($consumer_key=="") or ($consumer_token=="")) {
	$this->HeadTitle=("Acceso mediante OAuth");	
	$this->HeadDescription=('Se produjo un error al intentar iniciar sesión mediante OAuth');
	$this->loadtemplatepublic('oauth_error.tpl.php');
	exit;
}

$resultado=$this->OAuth->GetStatusApp($consumer_key,$consumer_token);

if ($resultado===-2) { 
	$this->HeadTitle=("Acceso mediante OAuth");	
	$this->HeadDescription=('La aplicación no permite iniciar sesión mediante OAuth');
	$this->loadtemplatepublic('oauth_error_nologin.tpl.php');
	exit;
}
if ($resultado===-1) { header("Location: " . siteprotocol . sitedomain . "iniciar-sesion"); exit; }
if ($resultado===0) { 
	$this->HeadTitle=("Acceso mediante OAuth");	
	$this->HeadDescription=('Se produjo un error al intentar iniciar sesión mediante OAuth');
	$this->params['text']='Se produjo un error al intentar iniciar sesión mediante OAuth';
	$this->loadtemplatepublic('message.tpl.php');
	exit;
}
if ($resultado===1) {
	if (isset($_GET['authorize'])) {
		//Autorizamos...
		$access_token=$this->OAuth->AuthorizeApp($this->OAuth->AppData['ID']);
		$redirigir=$this->OAuth->LoginRedirect($access_token);
		header("Location: " . $redirigir);
		exit;
	}
	if (isset($_GET['cancel'])) {
		$redirigir=$this->OAuth->NotAuthorizedRedirect();
		header("Location: " . $redirigir);
		exit;
	}
	$this->OAuth->confirm=siteprotocol . sitedomain . $_SERVER['REQUEST_URI'] . '&authorize=yes';
	$this->OAuth->cancel=siteprotocol . sitedomain . $_SERVER['REQUEST_URI'] . '&cancel=yes';
	$this->OAuth->Image='templates/' . $this->template . '/images/app_img.jpg';
	if ($this->OAuth->AppData['Image']!="") { $this->OAuth->Image=siteprotocol . sitedomain . "/public/thumbnails/" . $this->OAuth->AppData['Image']; } 
	$this->HeadTitle=("Acceso mediante OAuth");	
	$this->HeadDescription=('Autorizar el acceso de la aplicación');
	$this->loadtemplatepublic('oauth_authorize.tpl.php');
}
?>