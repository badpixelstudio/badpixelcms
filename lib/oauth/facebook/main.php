<?php
/*
 * @author: Mahmud Ahsan (http://thinkdiff.net)
 */
     //facebook application
    $fbconfig['appid' ]     = siteFacebookAppID;
    $fbconfig['secret']     = siteFacebookAppSecret;
	$fbconfig['baseurl']    = siteprotocol . sitedomain . "/lib/oauth_login.php?method=facebook&referer=" . urlencode($_SERVER['REQUEST_URI']);
    $fbconfig['baseurl_login']    = siteprotocol . sitedomain . "/lib/oauth_login.php?method=facebook&referer=" . urlencode($_SERVER['REQUEST_URI']); 
	$fbconfig['baseurl_logout']    = siteprotocol . sitedomain . "/lib/oauth_login.php?method=logout&action=logout&referer=" . urlencode($_SERVER['REQUEST_URI']); 
	
    //
    if (isset($_GET['request_ids'])){
        //user comes from invitation
        //track them if you need
    }
    
    $user            =   null; //facebook user uid
	$fbme			 =   null;
    try{
        include_once "facebook.php";
    }
    catch(Exception $o){
        error_log($o);
    }
    // Create our Application instance.
    $facebook = new Facebook(array(
      'appId'  => $fbconfig['appid'],
      'secret' => $fbconfig['secret'],
      'cookie' => true,
    ));
	$GLOBALS['facebook']=$facebook;

    //Facebook Authentication part
    $user       = $facebook->getUser();
	if ($user) {
		$fbme	= $facebook->api('/me');
		$GLOBALS['fbme']=$fbme;
	}
    // We may or may not have this data based 
    // on whether the user is logged in.
    // If we have a $user id here, it means we know 
    // the user is logged into
    // Facebook, but we don’t know if the access token is valid. An access
    // token is invalid if the user logged out of Facebook.
    
    
    $loginUrl   = $facebook->getLoginUrl(
            array(
                'scope'         => siteFacebookScope,
                'redirect_uri'  => $fbconfig['baseurl_login']
            )
    );
    
    $logoutUrl  = $facebook->getLogoutUrl(
			array(
                'next'  => $fbconfig['baseurl_logout']
            )		
	);
	
	$GLOBALS['loginUrl']=$loginUrl;
	$GLOBALS['logoutUrl']=$logoutUrl;
   

    if ($user) {
      try {
        // Proceed knowing you have a logged in user who's authenticated.
        $user_profile = $facebook->api('/me');
      } catch (FacebookApiException $e) {
        //you should use error_log($e); instead of printing the info on browser
        d($e);  // d is a debug function defined at the end of this file
        $user = null;
      }
    }
	
	//Funciones propias....

	function FBGetImage($id) {  
		//Obtenemos el JSON con los datos de la imagen...
		$url="http://graph.facebook.com/" . $id . "/picture?width=500&height=500&redirect=false";
		$json=file_get_contents($url);
		$json=json_decode($json,true);
		if (isset($json['data']['is_silhouette'])) {
			if ($json['data']['is_silhouette']==false) {
				$url_origen=$json['data']['url'];
				$extension = preg_split("/\./", strtolower($url_origen)) ;
				$n = count($extension)-1;
				$extension = "." . $extension[$n];
				//clean file...
				$p=strpos($extension, "?");
				if ($p!==false) { $extension=substr($extension, 0,$p); }
				if ($extension=="") { $extension=".jpg"; }
				$archivo_destino="../public/temp/fb_" . $id . $extension;
				$mi_curl = curl_init ($url_origen);  
				$fs_archivo = fopen ($archivo_destino, "w");  
				curl_setopt ($mi_curl, CURLOPT_FILE, $fs_archivo);  
				curl_setopt ($mi_curl, CURLOPT_HEADER, 0);  
				curl_setopt($mi_curl,CURLOPT_SSL_VERIFYPEER, false);
				curl_exec ($mi_curl);  
				curl_close ($mi_curl);  
				fclose ($fs_archivo); 
				chmod("../public/temp/fb_" . $id . $extension, 0777);
				return "fb_" . $id . $extension;
			}
		}
		return false;
	}
	
	function FBBirthDateParse($fecha){
		$lafecha=preg_replace('#^(\d{2})/(\d{2})/(\d{4})$#', '$2-$1-$3', $fecha);
		return $lafecha;
	} 
	

	function FBPostWall($mensaje,$url_enlace='',$url_imagen='',$titulo='',$descripcion='',$user=0) {
		try {
			$Datos['access_token']=siteFacebookTokenProfilePublish;
			$Datos['message']=$mensaje; //utf8_encode($mensaje);
			if ($url_enlace!="") { $Datos['link']=$url_enlace; }
			if ($url_imagen!="") { $Datos['picture']=$url_imagen; }
			if ($titulo!="") { $Datos['name']=$titulo; } //utf8_encode($titulo); }
			if ($descripcion!="") { $Datos['description']=$descripcion; } //utf8_encode($descripcion); }
			
			$authToken=siteFacebookAccessToken;
			if ($authToken=="") {
				//Refrescamos el token...
				$refresco= fetchUrl("https:/graph.facebook.com/oauth/access_token?client_id=" . siteFacebookAppID . "&client_secret=" . siteFacebookAppSecret . "&grant_type=fb_exchange_token&fb_exchange_token=" . siteFacebookAccessToken);
			} 
			
			$fb = new Facebook(array(
			  'appId'  => siteFacebookAppID,
			  'secret' => siteFacebookAppSecret,
			  //'access_token' => $authToken, //No es necesario, pues vale con el token de publicar en la página.
			  'cookie' => true,
			));

			$publishStream = $fb->api("/me/feed", 'post', $Datos);
			//as $_GET['publish'] is set so remove it by redirecting user to the base url 
		} catch (FacebookApiException $e) {
			echo "<h1>Error al publicar en Facebook!</h1>";
			echo "<p>Compruebe la <a target='_blank' href='" . siteprotocol . sitedomain . "lib/oauth/facebook/config.php'>configuración</a> e inicie sesión con la cuenta de administrador desde la web e intentelo de nuevo</p>";
			echo "<hr>";
			echo "<p>DETALLES DEL ERROR:</p>";
			d($e); 
		}	
		return $publishStream;	
	}
	
	function FBUploadImage($mensaje,$path_imagen='',$titulo='',$album="me") {
		try {
			$Datos['access_token']=siteFacebookTokenProfilePublish;
			$Datos['message']=$mensaje; //utf8_encode($mensaje);
			$Datos['image']='@' . realpath($path_imagen);
			
			$authToken=siteFacebookAccessToken;
			if ($authToken=="") {
				//Refrescamos el token...
				$refresco= fetchUrl("https:/graph.facebook.com/oauth/access_token?client_id=" . siteFacebookAppID . "&client_secret=" . siteFacebookAppSecret . "&grant_type=fb_exchange_token&fb_exchange_token=" . siteFacebookAccessToken);
			} 
			
			$fb = new Facebook(array(
			  'appId'  => siteFacebookAppID,
			  'secret' => siteFacebookAppSecret,
			  //'access_token' => $authToken, //No es necesario, pues vale con el token de publicar en la página.
			  'cookie' => true,
			));
			$fb->setFileUploadSupport(true);
			$publishStream = $fb->api("/" . $album . "/photos", 'post', $Datos);
			//as $_GET['publish'] is set so remove it by redirecting user to the base url 
		} catch (FacebookApiException $e) {
			echo "<h1>Error al publicar en Facebook!</h1>";
			echo "<p>Compruebe la <a target='_blank' href='" . siteprotocol . sitedomain . "lib/oauth/facebook/config.php'>configuración</a> e inicie sesión con la cuenta de administrador desde la web e intentelo de nuevo</p>";
			echo "<hr>";
			echo "<p>DETALLES DEL ERROR:</p>";
			d($e); 
		}	
		return $publishStream;	
	}
	
	function FBCreateAlbum($nombre='Album',$descripcion='') {
		try {
			$Datos['access_token']=siteFacebookTokenProfilePublish;
			$Datos['name']=$nombre; //utf8_encode($mensaje);
			if ($descripcion!="") { $Datos['description']=$descripcion; }
			
			$authToken=siteFacebookAccessToken;
			if ($authToken=="") {
				//Refrescamos el token...
				$refresco= fetchUrl("https:/graph.facebook.com/oauth/access_token?client_id=" . siteFacebookAppID . "&client_secret=" . siteFacebookAppSecret . "&grant_type=fb_exchange_token&fb_exchange_token=" . siteFacebookAccessToken);
			} 
			
			$fb = new Facebook(array(
			  'appId'  => siteFacebookAppID,
			  'secret' => siteFacebookAppSecret,
			  //'access_token' => $authToken, //No es necesario, pues vale con el token de publicar en la página.
			  'cookie' => true,
			));
			$fb->setFileUploadSupport(true);
			$Album = $fb->api('/me/albums', 'post', $Datos);
			$Album_UID = $Album['id'];
			//as $_GET['publish'] is set so remove it by redirecting user to the base url 
		} catch (FacebookApiException $e) {
			echo "<h1>Error al publicar en Facebook!</h1>";
			echo "<p>Compruebe la <a target='_blank' href='" . siteprotocol . sitedomain . "lib/oauth/facebook/config.php'>configuración</a> e inicie sesión con la cuenta de administrador desde la web e intentelo de nuevo</p>";
			echo "<hr>";
			echo "<p>DETALLES DEL ERROR:</p>";
			d($e); 
		}	
		return $Album_UID;	
	}	


	function ORIGINAL_____FBPostWall($mensaje,$url_enlace='',$url_imagen='',$titulo='',$descripcion='',$user=siteFacebookUserID) {
		try {
			$Datos['message']=$mensaje;
			if ($url_enlace!="") { $Datos['link']=$url_enlace; }
			if ($url_imagen!="") { $Datos['picture']=$url_imagen; }
			if ($titulo!="") { $Datos['name']=$titulo; }
			if ($descripcion!="") { $Datos['description']=$descripcion; }
			
			$authToken=siteFacebookAccessToken;
			//if ($authToken=="") {
				$authToken = fetchUrl("https://graph.facebook.com/oauth/access_token?type=client_cred&client_id=" . siteFacebookAppID . "&client_secret=" . siteFacebookAppSecret);
			//}
			
			$fb = new Facebook(array(
			  'appId'  => siteFacebookAppID,
			  'secret' => siteFacebookAppSecret,
			  'access_token' => $authToken,
			  'cookie' => true,
			));

			$publishStream = $fb->api("/" . $user . "/feed", 'post', $Datos);
			//as $_GET['publish'] is set so remove it by redirecting user to the base url 
		} catch (FacebookApiException $e) {
			d($e);
		}	
		return $publishStream;	
	}
	
	function FBStatusUpdate($mensaje,$user=siteFacebookUserID) {
		try {
			$authToken = fetchUrl("https://graph.facebook.com/oauth/access_token?type=client_cred&client_id=" . siteFacebookAppID . "&client_secret=" . siteFacebookAppSecret);
			
			$fb = new Facebook(array(
			  'appId'  => siteFacebookAppID,
			  'secret' => siteFacebookAppSecret,
			  'access_token' => $authToken,
			  'cookie' => true,
			));			
			$statusUpdate = $fb->api("/$user/feed", 'post', array('message'=> $mensaje));
		} catch (FacebookApiException $e) {
			d($e);
		}		
		return $statusUpdate;
	}
	
	function FBGetLikes($folder,$user=siteFacebookUserID) {
		try{
			$authToken = fetchUrl("https://graph.facebook.com/oauth/access_token?type=client_cred&client_id=" . siteFacebookAppID . "&client_secret=" . siteFacebookAppSecret);
			
			$fb = new Facebook(array(
			  'appId'  => siteFacebookAppID,
			  'secret' => siteFacebookAppSecret,
			  'access_token' => $authToken,
			  'cookie' => true,
			));			

            $likes = $fb->api("/$user/" . $folder); //El folder puede ser, por ejemplo "movies"
        }
        catch(Exception $o){
            d($o);
        }		
		return $likes;
	}
	
	
   
    
    //if user is logged in and session is valid.
    if ($user){
        //get user basic description
        $userInfo = $facebook->api("/$user");
        
        //Retriving movies those are user like using graph api
        try{
            $movies = $facebook->api("/$user/movies");
        }
        catch(Exception $o){
            d($o);
        }
        
        //update user's status using graph api
        //http://developers.facebook.com/docs/reference/dialogs/feed/
        if (isset($_GET['publish'])){
            $redirectUrl     = $fbconfig['baseurl'] . '/index.php?success=1';
            header("Location: $redirectUrl");
        }

        //update user's status using graph api
        //http://developers.facebook.com/docs/reference/dialogs/feed/
        if (isset($_POST['tt'])){
            try {
                $statusUpdate = $facebook->api("/$user/feed", 'post', array('message'=> $_POST['tt']));
            } catch (FacebookApiException $e) {
                d($e);
            }
        }

        //fql query example using legacy method call and passing parameter
        // try{
        //     $fql    =   "select name, hometown_location, sex, pic_square from user where uid=" . $user;
        //     $param  =   array(
        //         'method'    => 'fql.query',
        //         'query'     => $fql,
        //         'callback'  => ''
        //     );
        //     $fqlResult   =   $facebook->api($param);
        // }
        // catch(Exception $o){
        //     d($o);
        // }
    }
    
	function FBPutLoginButton($type="normal") {
		$texto="Conectar";
		$enlace=$GLOBALS['loginUrl'];
		if ($GLOBALS['user']) {
			$texto="Desconectar";
			$enlace=$GLOBALS['logoutUrl'];			
		}
		echo ' ';
		if ($type=="normal") { echo '<a class="fb_button fb_button_medium" href="' . $enlace . '"><span class="fb_button_text">' . $texto . '</span></a>'; } 
		if ($type=="icon") { echo '<a class="fb_button fb_button_medium" href="' . $enlace . '"></a>'; } 
		
	}
	
    function d($d){
        echo '<pre>';
        print_r($d);
        echo '</pre>';
    }
?>
