<?php
if ((siteFacebookEnabled) and (file_exists(sitepath . "lib/oauth/facebook/main.php"))) { require_once(sitepath . "lib/oauth/facebook/main.php"); }
if ((siteTwitterEnabled) and (file_exists(sitepath . "lib/oauth/twitter/main.php"))) { require_once(sitepath . "lib/oauth/twitter/main.php"); }
if ((siteGoogleAPIEnabled) and (file_exists(sitepath . "lib/oauth/google/main.php"))) { require_once(sitepath . "lib/oauth/google/main.php"); }

function PutFacebookButton($type="large") {
	if (siteFacebookEnabled) {
		if ($type=="large") {echo '<a class="fb_button fb_button_medium" href="'. $GLOBALS['loginUrl'] . '"><span class="fb_button_text">' ._("Conectar") . '</span></a>'; }
		if ($type=="icon") {echo '<a class="fb_button fb_button_medium" href="'. $GLOBALS['loginUrl'] . '"></a>'; }		
	}
}

function PutTwitterButton($type="large") {
	if (siteTwitterEnabled) {
		if ($type=="large") {echo '<a class="twitter_button twitter_button_medium" href="'. $GLOBALS['twloginUrl'] . '"><span class="twitter_button_text">' ._("Conectar") . '</span></a>'; }
		if ($type=="icon") {echo '<a class="twitter_button twitter_button_medium" href="'. $GLOBALS['twloginUrl'] . '"></a>'; }
	}	
}

function PutGoogleButton($type="large") {
	if (siteGoogleAPIEnabled) {
		if ($type=="large") {echo '<a class="google_button google_button_medium" href="'. $GLOBALS['gloginUrl'] . '"><span class="twitter_button_text">' ._("Conectar") . '</span></a>'; }
		if ($type=="icon") {echo '<a class="google_button google_button_medium" href="'. $GLOBALS['gloginUrl'] . '"></a>'; }
	}		
	
}

function PutOAuthButtons($type="large") {
	PutFacebookButton($type);
	PutTwitterButton($type);
	PutGoogleButton($type);
}

function GetOAuthLink($red="facebook") {
	if ($red=="facebook") {	echo $GLOBALS['loginUrl']; }
	if ($red=="twitter") {	echo $GLOBALS['twloginUrl']; }
	if ($red=="google") {	echo $GLOBALS['gloginUrl']; }
}

?>