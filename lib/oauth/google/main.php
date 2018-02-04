<?php
require_once(sitepath . '/lib/oauth/google/Google_Client.php');
require_once(sitepath . '/lib/oauth/google/contrib/Google_Oauth2Service.php');

$googleme = new Google_Client();
$oauth2 = new Google_Oauth2Service($googleme);
$GLOBALS['googleme']=$googleme;

$authUrl = $googleme->createAuthUrl();
$GLOBALS['gloginUrl']=$authUrl;


function GoogleLogin() {
	global $googleme;
	if (isset($_GET['code'])) {
		$googleme->authenticate($_GET['code']);
		$_SESSION['GoogleToken'] = $googleme->getAccessToken();
		$redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
		header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
		return;
	}
	if (isset($_SESSION['GoogleToken'])) {
 		$googleme->setAccessToken($_SESSION['GoogleToken']);
	}
	if ($googleme->getAccessToken()) {
		$oauth2 = new Google_Oauth2Service($googleme);
		$user = $oauth2->userinfo->get();
		// These fields are currently filtered through the PHP sanitize filters.
		// See http://www.php.net/manual/en/filter.filters.sanitize.php
		$email = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
		$img = filter_var($user['picture'], FILTER_VALIDATE_URL);
		$personMarkup = "$email<div><img src='$img?sz=150'></div>";
		return $user;
	} else {
		return false;
	}
}

?>