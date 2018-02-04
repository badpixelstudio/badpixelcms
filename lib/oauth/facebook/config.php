<?php 
require_once('../../../include/core/common.php');
require_once(sitepath . "include/core/socialmedia.class.php");
$Core = new SocialMedia($_GET);
if ($Core->userLevel<99) { die("No tiene permiso para realizar esta acción"); }

if (isset($_POST['Save'])) {
	$registro=$Core::$db->GetDataFieldFromSQL("SELECT ID FROM modules_config WHERE UserID=" . $Core->businessID . " AND Module='core' AND ParamName='FacebookAppID'",'ID');
	if (($registro!==false) and ($registro>0)) {
		$sql="UPDATE modules_config SET ParamValue='" . $_POST['FacebookAppID'] . "' WHERE ID=" . $registro;	
	} else {
		$sql="INSERT INTO modules_config (UserID, Module, ParamName, ParamValue) VALUES (" . $Core->businessID . ", 'core', 'FacebookAppID', '" . $_POST['FacebookAppID'] . "')";
	}
	$Core::$db->Qry($sql);	
	$registro=$Core::$db->GetDataFieldFromSQL("SELECT ID FROM modules_config WHERE UserID=" . $Core->businessID . " AND Module='core' AND ParamName='FacebookAppSecret'",'ID');
	if (($registro!==false) and ($registro>0)) {
		$sql="UPDATE modules_config SET ParamValue='" . $_POST['FacebookAppSecret'] . "' WHERE ID=" . $registro;	
	} else {
		$sql="INSERT INTO modules_config (UserID, Module, ParamName, ParamValue) VALUES (" . $Core->businessID . ", 'core', 'FacebookAppSecret', '" . $_POST['FacebookAppSecret'] . "')";
	}
	$Core::$db->Qry($sql);	
	$registro=$Core::$db->GetDataFieldFromSQL("SELECT ID FROM modules_config WHERE UserID=" . $Core->businessID . " AND Module='core' AND ParamName='FacebookAccessToken'",'ID');
	if (($registro!==false) and ($registro>0)) {
		$sql="UPDATE modules_config SET ParamValue='" . $_POST['FacebookAccessToken'] . "' WHERE ID=" . $registro;	
	} else {
		$sql="INSERT INTO modules_config (UserID, Module, ParamName, ParamValue) VALUES (" . $Core->businessID . ", 'core', 'FacebookAccessToken', '" . $_POST['FacebookAccessToken'] . "')";
	}
	$Core::$db->Qry($sql);
	if ($_POST['FacebookTokenProfilePublish']=="") { $_POST['FacebookTokenProfilePublish']=$_POST['FacebookAccessToken']; }	
	$registro=$Core::$db->GetDataFieldFromSQL("SELECT ID FROM modules_config WHERE UserID=" . $Core->businessID . " AND Module='core' AND ParamName='FacebookTokenProfilePublish'",'ID');
	if (($registro!==false) and ($registro>0)) {
		$sql="UPDATE modules_config SET ParamValue='" . $_POST['FacebookTokenProfilePublish'] . "' WHERE ID=" . $registro;	
	} else {
		$sql="INSERT INTO modules_config (UserID, Module, ParamName, ParamValue) VALUES (" . $Core->businessID . ", 'core', 'FacebookTokenProfilePublish', '" . $_POST['FacebookTokenProfilePublish'] . "')";
	}
	$Core::$db->Qry($sql);	
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Configurar FacebookConnect</title>
</head>

<body>
<script>
function update() {
	document.getElementById('fb_link').href="get_accesstoken.php?appid=" + document.getElementById('app_id').value + '&appsecret=' + document.getElementById('app_secret').value;
	document.getElementById('client_id').value=document.getElementById('app_id').value;
	document.getElementById('client_secret').value=document.getElementById('app_secret').value;
	document.getElementById('Conf_ID').value=document.getElementById('app_id').value;
	document.getElementById('Conf_Secret').value=document.getElementById('app_secret').value;
	document.getElementById('Conf_Token').value=document.getElementById('access_token').value;
	document.getElementById('Conf_Profile').value=document.getElementById('user_token').value;
}
</script>
<h1>Configurar acceso a Facebook</h1>
<h2>1. Registra tu aplicación</h2>
<p>Crea una aplicación en <a href="https://developers.facebook.com/apps" target="_blank">el sitio de Desarrolladores de Facebook</a>, y facilita el APP ID ó API KEY y el API SECRET:</p>
<label>AppID: <input type="text" name="app_id" id="app_id" size="60" value="<?php echo $Core->conf->Export('FacebookAppID'); ?>" onblur="update();"/></label><br />
<label>AppSecret: <input type="text" name="app_secret" id="app_secret" value="<?php echo $Core->conf->Export('FacebookAppSecret'); ?>" size="60" onblur="update();"/></label><br />
<h2>2. Autoriza tu cuenta de usuario</h2>
<p>Es necesario que tu cuenta de usuario (la que puede administrar las p&aacute;ginas), inicie sesi&oacute;n y autorice el acceso al contenido fuera de linea.</p>
<p>Haz clic <a id="fb_link" href="get_accesstoken.php" target="_blank">aqui e inicia sesi&oacute;n</a>, te devolver&aacute; a la p&aacute;gina principal de la web y en la barra de direcciones copia los datos que hay desde <strong>access_token=</strong> y hasta <strong>&expires_in</strong>, sin incluir estos parametros, sólo el código alfanumerico, al que a partir de este momento lo llamaremos <strong>ACCESS_TOKEN</strong>
<h2>3.Ampliar validez del token de acceso</h2>
<p>Pega el access_token en el campo y haz clic en Enviar para extender la vida del token por 60 dias</p>
<form action="https://graph.facebook.com/oauth/access_token" method="get" target="_blank">
	<input type="text" name="fb_exchange_token" id="fb_exchange_token" size="60" />
    <input type="submit" value="Enviar" />
    <input type="hidden" name="client_id" id="client_id" value="<?php echo $Core->conf->Export('FacebookAppID'); ?>" />
    <input type="hidden" name="client_secret" id="client_secret" value="<?php echo $Core->conf->Export('FacebookAppSecret'); ?>" />
    <input type="hidden" name="grant_type" id="grant_type" value="fb_exchange_token" />
</form>
<h2>4. Obtenemos la lista de páginas de Facebook que podemos administrar</h2>
<p>Guardamos el contenido existente entre las comillas del "access_token": correspondiente a la página a adminstrar. En el caso de que existan varios elementos con el mismo nombre, debemos coger la que NO diga que es Application</p>
<form action="https://graph.facebook.com/me/accounts" method="get" target="_blank">
	<input type="text" name="access_token" id="access_token" size="60" value="<?php echo $Core->conf->Export('FacebookAccessToken'); ?>" onblur="update();" />
    <input type="submit" value="Enviar" />
</form>
<p>Copiamos el user_token de la página a adminstrar</p>
<p>Si deseamos publicar en un perfil personal, hay que pegar el mismo token anterior. Si se deja en blanco se producirán errores.</p>
<input type="text" name="user_token" id="user_token" size="60" value="<?php echo $Core->conf->Export('FacebookTokenProfilePublish'); ?>" onblur="update();"/>
<h2>5. Configurar la aplicación</h2>
<form method="post">
    <input type="submit" value="Guardar la configuración" />
    <input type="hidden" name="FacebookAppID" id="Conf_ID" value="" />
    <input type="hidden" name="FacebookAppSecret" id="Conf_Secret" value="" />
    <input type="hidden" name="FacebookAccessToken" id="Conf_Token" value="" />
    <input type="hidden" name="FacebookTokenProfilePublish" id="Conf_Profile" value="" />
    <input type="hidden" name="Save" id="Save" value="1" />
</form>
<script>
update();
</script>