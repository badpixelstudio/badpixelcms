<?php
require_once('twitteroauth.php');

//Generamos 
if (!isset($_REQUEST['oauth_token'])) {
	$connection = new TwitterOAuth(siteTwitterConsumerKey, siteTwitterConsumerSecret);
	$request_token = $connection->getRequestToken(siteprotocol . sitedomain . "/lib/oauth_login.php?method=twitter&referer=" . urlencode($_SERVER['REQUEST_URI']));
	$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
	$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
	$GLOBALS['twloginUrl']= $connection->getAuthorizeURL($token);
}

if (isset($_REQUEST['oauth_token'])) {
	$conn = new TwitterOAuth(siteTwitterConsumerKey, siteTwitterConsumerSecret, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
	$access_token = $conn->getAccessToken($_REQUEST['oauth_verifier']);
	if ($conn->http_code==200) {
		//Creamos otra conexión a Twitter para obtener datos...
		$twitter = new TwitterOAuth(siteTwitterConsumerKey, siteTwitterConsumerSecret, $access_token['oauth_token'], $access_token['oauth_token_secret']);
		$params= array('include_email' => 'true', 'include_entities' => 'false', 'skip_status' => 'true');
		$twitterme = $twitter->get('account/verify_credentials',$params);	
		$twitterme->access_token=$access_token['oauth_token'];
		$twitterme->access_token_secret=$access_token['oauth_token_secret'];
		$GLOBALS['twitterme']=$twitterme;
	}
	unset($_SESSION['oauth_token']);
	unset($_SESSION['oauth_token_secret']);
}

function TwitterImage($url_origen,$id) {  
	//$url_origen='http://graph.facebook.com/' . $id . '/picture?type=large';
	//Obtener extensión...
	$archivo_destino="../public/temp/tw_" . $id . '.jpg';
	$mi_curl = curl_init ($url_origen);  
	$fs_archivo = fopen ($archivo_destino, "w");  
	curl_setopt ($mi_curl, CURLOPT_FILE, $fs_archivo);  
	curl_setopt ($mi_curl, CURLOPT_HEADER, 0);  
	curl_exec ($mi_curl);  
	$error=curl_error($mi_curl);
	curl_close ($mi_curl);
	fclose ($fs_archivo);
	if ($error=="") {
	  	chmod("../public/temp/tw_" . $id . '.jpg', 0777);
		return "tw_" . $id . ".jpg";
	} else {
		unlink($archivo_destino);
		return false;
	}
}

function Tweet($texto,$imagen="",$access_token=siteTwitterAccessToken,$acces_token_secret=siteTwitterAccessTokenSecret) {
	//Comprobamos si hay imagen
	$ruta_imagen="";
	if ($imagen!="") {
		$ruta_imagen=str_replace(siteprotocol . sitedomain, sitepath,$imagen);
		if (! is_file($ruta_imagen)) { $ruta_imagen=""; }
	}
	$twi_user = new TwitterOAuth(siteTwitterConsumerKey, siteTwitterConsumerSecret,$access_token,$acces_token_secret);
	$parameters['status']=$texto;
	if ($ruta_imagen!="") { $parameters['media']=file_get_contents($ruta_imagen); }
	if ($ruta_imagen=="") {
		$status = $twi_user->post('statuses/update', $parameters);
	} else {
		$status = $twi_user->post('statuses/update_with_media', $parameters, true);
	}
	//print_r($status);
	unset($twi_user);	
}

function SearchStatus($texto,$access_token=siteTwitterAccessToken,$acces_token_secret=siteTwitterAccessTokenSecret) {
	$twi_data = new TwitterOAuth(siteTwitterConsumerKey, siteTwitterConsumerSecret,$access_token,$acces_token_secret);
	$parameters['q']=$texto;
	$status=$twi_data->get('statuses/user_timeline');
	return $status;
}
?>