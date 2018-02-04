<?php
	require_once("../../../include/core/common.php");
	require_once("../../../include/users/users.class.php");
	$Core = new User($_GET);
	
	$app_id=siteFacebookAppID;
	$app_secret=siteFacebookAppSecret;
	

	function get_token(){
		if (isset($_GET['appid'])) { $app_id=$_GET['appid']; }
		if (isset($_GET['appsecret'])) { $app_secret=$_GET['appsecret']; }
		$redirect_url = urlencode(siteprotocol . sitedomain);
		$string = "Location: https://www.facebook.com/dialog/oauth?client_id="
		. $app_id . "&redirect_uri=" . urlencode(siteprotocol . $_SERVER['HTTP_HOST']) .
		"&scope=manage_pages&response_type=token";
		header($string);
	}
	
	//No se usa
	function get_pages_token($code) {
		$url2 = "https://graph.facebook.com/me/accounts?access_token=".$code;
		$result = json_decode(file_get_contents($url2));
		print_r($url2);
 	}
 
	if (isset($_GET['code'])) {
		get_pages_token($_GET['code']); //Obsoleto
	} else {
		get_token();
	}
 

?>