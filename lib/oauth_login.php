<?php
ini_set("display_errors", 1);
require_once("../include/core/common.php");
require_once("../include/users/users.class.php");

$Core = new User($_GET);

$method="";
$referer="";

if (isset($_GET['method'])) { $method=$_GET['method']; } else { $method="cancel"; }
if (($method!="facebook") and ((isset($_GET['code'])) or (isset($_SESSION['GoogleToken'])))) { $method="google"; }
if (isset($_GET['referer'])) { $referer=$_GET['referer']; }
if ((isset($_POST['referer'])) and ($referer=="")) { $referer=$_POST['referer']; }


if ($method=="logout") {
	$Core->Logout();
	if ($referer=="") { $referer='/'; }
	header('Location: ' . $referer);
	exit;	
}
if (isset($GLOBALS['fbme'])) { $fbme=$GLOBALS['fbme']; }
if (isset($GLOBALS['twitterme'])) { $twitterme=$GLOBALS['twitterme']; }
if (isset($GLOBALS['googleme'])) { $googleme=$GLOBALS['googleme']; }


if ($method=="facebook") {
	if ($fbme) {
		$Datos['Form_fb_uid']=$fbme['id'];
		$Datos['Form_fb_gender']=$fbme['gender'];
		$Datos['Form_fb_link']=addslashes($fbme['link']);
		$Datos['Form_UserName']=stripfilename($fbme['name']);
		$Datos['Form_Name']=$fbme['name'];
		$Datos['Form_Email']=$fbme['email'];
		$Datos['Form_PassW']=md5(KeyGen(8));
		$Datos['Form_Birthdate']=FBBirthDateParse($fbme['birthday']);
		$avatar=FBGetImage($fbme['id']);
		if ($avatar!==false) { $Datos['Form_Image']=$avatar; }
		//Logueamos...
		$Core->OAuthLogin($Datos,$referer);
		exit;
	}
}

if ($method=="twitter") {
	if ($twitterme) {
		$Datos['Form_twitter_uid']=$twitterme->id_str;
		$Datos['Form_twitter_link']="https://twitter.com/#!/" . $twitterme->screen_name;
		$Datos['Form_twitter_access_token']=$twitterme->access_token;
		$Datos['Form_twitter_access_token_secret']=$twitterme->access_token_secret;
		$Datos['Form_UserName']=stripfilename($twitterme->screen_name);
		$Datos['Form_Name']=$twitterme->name;
		//$Datos['Form_Email']=$twitterme->email;
		$url_imagen=$twitterme->profile_image_url;
		$stpos=strpos($url_imagen,'_normal');
		if ($stpos!==false) { $url_imagen=substr($url_imagen,0,$stpos); }
		$url_imagen.="_reasonably_small";
		$avatar=TwitterImage($url_imagen,$Datos['Form_twitter_uid']);
		if ($avatar!==false) { $Datos['Form_Image']=$avatar; }
	} else {
		$Datos=$_POST;
	}
	if (!$Core->OAuthLogin($Datos,$referer)) {
		define('ZCMSPATH', '../');
		$Core->HeadTitle="Twitter Login"; 
		require_once( 'oauth_email.php');
		//header("Location: oauth_email.php");
	} 
	exit;	
}

if ($method=="google") {
	$Usuario=GoogleLogin();
	if ($Usuario!="") {
		$Datos['Form_UserName']=stripfilename($Usuario['name']);
		$Datos['Form_Name']=$Usuario['name'];
		$Datos['Form_Email']=$Usuario['email'];
		$Datos['Form_PassW']=md5(KeyGen(8));
		$Datos['Form_Birthdate']=FBBirthDateParse($Usuario['birthday']);
		//Traemos los datos de la foto en miniatura mediante FQL..
		if (isset($Usuario['picture'])) {
			if ($Usuario['picture']!="") {
				$Datos['Form_Image']="google_" . $Usuario['id']. ".jpg";
				CopyExternalResource($Usuario['pictute'],sitepath . "public/temp/" . $Datos['Form_Image']);
			}
		}
		//Logueamos...
		$Core->OAuthLogin($Datos,"");
		exit;		
		
	}
	exit;
}

if ($method=="email") {
	$_POST['System_Action']="edit";
	$Core->OAuthUpdateEmail($_POST,$_POST['referer']);
	header("Location: " . urldecode($referer));
}

if ($action=="cancel") {
	if ($referer=="") { $referer='/'; }
	header('Location: ' . urldecode($referer));	
}


?>