<?php
require_once(sitepath . "include/core/core.class.php");
require_once(sitepath . "include/users/users.config.php");
require_once(sitepath . "include/users/devices.class.php");
require_once(sitepath . "lib/images/thumbs.php");	

class MasterUser extends Core{
	var $title = 'Usuarios';
	var $class = 'users';
	var $siteUserPassMinLength = '4';
	var $module = 'users';
	var $table = 'users';
	var $encrypt = true;
	var $version=false;	
	var $FieldsOfImages=array("Image"=>"UserAvatarOptions");
	
	function __construct($values) {
		parent::__construct($values); 
		$title=$this->GetModuleName($this->class);
		if ($title!=$this->class) { $this->title=$title; }
		$this->conf = new ConfigUsers($this->businessID); 
		if (isset($values['field'])) {$this->campo=$values['field']; }
		$this->BreadCrumb['Usuarios']=$this->module;
	}
	
	function Login($username, $password, $recuerdame='', $useragent=''){
		if (siteLogLogins) { error_log(date('d/m/Y H:i:s') . " Login: " . $username . ", PassW: " . $password .  "\r\n", 3, sitepath . "public/loginlog.txt"); }
		$username=addslashes(trim($username));
		if($this->encrypt == true){
			$password = md5($password);	
		}
		$result = parent::$db->GetDataRecordFromSQL("SELECT * FROM ".$this->table." WHERE ((UserName = '".$username."') or (Email = '" . $username . "'))  AND (PassW = '".$password."')");	
		if($result){
			if ($result['UserDisallowed']==1) { return "-9"; }
			if ($result['Active']==0) { return "-1";}
			if (($this->conf->Export('UseDateExpire')) and ($result['DateExpire']<date('Y-m-d H:i:s'))) { return "-1"; }
			if ($result['UserName'] !="" && $result['PassW'] !="") {
				if ($result['RegCode']=="") { 
					//Regeneramos el regcode
					$result['RegCode']=$result['UserName'] . KeyGen(30);
					$sql="UPDATE " . $this->table . " SET RegCode='" . $result['RegCode'] ."' WHERE ID=" . $result['ID'];
					parent::$db->Qry($sql);
				}
				$this->userID=$result['ID'];
				$_SESSION['userid'] = $result['ID'];
				$_SESSION['username'] = $result['UserName'];
				$_SESSION['userlevel'] = $result['Rol'];
				$_SESSION['regcode']= $result['RegCode'];
				try {
					$geo="";
					$ip=$_SERVER['REMOTE_ADDR'];
					if (($ip!="localhost") and ($ip!="127.0.0.1") and ($ip!="::1")) {
						$c=@file_get_contents("http://ip-api.com/json/" . $ip);
						$c=json_decode($c,true);
					}
					if (isset($c['city'])) { $geo=$c['city']; }
					if (isset($c['country'])) { $geo.=" (" . $c['country'] . ")"; }
				} catch (Exception $e) { }
				if ($useragent=="") {
					$useragent=getBrowser();
				}

				if ($recuerdame!="") {	setcookie(siteCookie . "_regcode",$result['RegCode'],time() + 31536000,"/"); }
				if ((siteNotifyEmailLogins) and ($result['NotifyLoginEmail']==1)) { 
					$cuerpo="<p>" . _("Hola") . " <strong>" . $result['UserName'] . "</strong>!" . "</p>";		
					$cuerpo.="<p>" ._("Nos hemos dado cuenta de tu nuevo inicio de sesión en ") . " <strong>" . siteTitle . "</strong>";
					if ($useragent!="") { $cuerpo.=" " . _("a través de") . " " . $useragent; }
					if ($ip!="") { $cuerpo.=", " . _("desde la dirección IP") . " " . $ip; }
					if ($geo!="") { $cuerpo.=" " . _("en un lugar cercano a") . " " . $geo; }
					$cuerpo.=".<p/>";
					$cuerpo.="<p>Nos preocupamos por la seguridad de tus datos, por eso:</p>";
					$cuerpo.="<ul>";
					$cuerpo.="<li><strong>Si has sido tú</strong>: ¡Genial! No hace falta que hagas nada más.</li>";
					$cuerpo.="<li><strong>Si no has sido tú</strong>: Puede que tu cuenta esté siendo usada por otra persona. Para evitar problemas, trata de cambiar la contraseña y si no te es posible contacta con nosotros.</li>";
					$cuerpo.="</ul>";
					$cuerpo.="<p>Si no quieres que te notifiquemos cada vez que inicies sesión, cambia las preferencias de envío de emails en tu perfil de usuario</p>";
					@SendMail(siteTitle, $result['Email'], _("Nuevo inicio de sesión en ") . siteTitle, $cuerpo, sitePasswordsMail, 1);
				}
				return "1";	
			}
			session_destroy();
			return "0";
		}
		return "0";
	}

	function LoginOAuth($Datos){
		$resultado['Success']=0;
		$resultado['Result']['ID']=-1;
		$valid=false;
		if (isset($Datos['email'])) {
			$email=$Datos['email'];
			$result = parent::$db->GetDataRecordFromSQL("SELECT * FROM ".$this->table." WHERE (Email = '" . $email . "')");
			$valid=true;
		}
		if ((isset($Datos['fb_uid'])) and (! $valid)) {
			$result = parent::$db->GetDataRecordFromSQL("SELECT * FROM ".$this->table." WHERE (fb_uid = '" . $Datos['fb_uid'] . "')");
			$valid=true;
		}
		if ((isset($Datos['twitter_uid'])) and (! $valid)) {
			$result = parent::$db->GetDataRecordFromSQL("SELECT * FROM ".$this->table." WHERE (twitter_uid = '" . $Datos['twitter_uid'] . "')");
			$valid=true;
		}
		if ($valid) {
			if ($result===false) {
				$Datos['System_Action']="new";
				$Datos['Form_UserName']=stripslashes($email);
				$Datos['Form_Email']=stripslashes($email);
				$Datos['Form_RegCode']=$email . KeyGen(30);
				$Datos['Form_DateInscribe']=date('Y-m-d');	
				$Datos['Form_Rol']=1;
				$Datos['Form_Active']=1;
				$Datos['Form_LastLogin']=date('Y-m-d H:i:s');
				$Datos['Form_PassW']=md5(KeyGen(8));
				$password=$Datos['Form_PassW'];
			} else {
				if ($Datos['UserDisallowed']==1) { 
					$resultado['Success']=-9;
					$resultado['Result']=_("Acceso al usuario bloqueado por el administrador");
				}
				$Datos['System_Action']="edit";
				$Datos['System_ID']=$result['ID'];
				$Datos['Form_RegCode']=$result['RegCode'];
				$password=$result['PassW'];
			}
			//Datos generales
			if (isset($Datos['Name'])) { $Datos['Form_Name']=$Datos['Name']; }
			if (isset($Datos['NIF'])) { $Datos['Form_NIF']=$Datos['NIF']; }
			if (isset($Datos['BirthDate'])) { $Datos['Form_BirthDate']=$Datos['BirthDate']; }
			//Avatar
			if (isset($Datos['DownloadImage'])) {
				$url_origen=$Datos['DownloadImage'];
				$extension = preg_split("/\./", strtolower($url_origen)) ;
				$n = count($extension)-1;
				$extension = "." . $extension[$n];
				//clean file...
				$p=strpos($extension, "?");
				if ($p!==false) { $extension=substr($extension, 0,$p); }
				if ($extension=="") { $extension=".jpg"; }
				$tmp_file="rs_" . KeyGen(6) . $extension;
				CopyExternalResource($Datos['DownloadImage'],sitepath . "public/temp/" . $tmp_file);
				if (is_file(sitepath . "public/temp/" . $tmp_file)) {
					$Datos['Change_Image'][0]=$tmp_file;
				}
			}
			//Datos de la red social
			if (isset($Datos['fb_uid'])) { $Datos['Form_fb_uid']=$Datos['fb_uid']; }
			if (isset($Datos['fb_gender'])) { $Datos['Form_fb_gender']=$Datos['fb_gender']; }
			if (isset($Datos['fb_link'])) { $Datos['Form_fb_link']=$Datos['fb_link']; }
			if (isset($Datos['twitter_uid'])) { $Datos['Form_twitter_uid']=$Datos['twitter_uid']; }
			if (isset($Datos['twitter_link'])) { $Datos['Form_twitter_link']=$Datos['twitter_link']; }
			//Actualizamos algunos datos...
			$Datos['Form_LastLogin']=date('Y-m-d H:i:s');
			//Guardamos...
			$ActualID=parent::$db->PostToDatabase($this->table,$Datos);
			$_SESSION['userid'] = $ActualID;
			$_SESSION['regcode']= $Datos['Form_RegCode'];
			$this->EditItem($ActualID);
			unset($this->Data['Action']);
			unset($this->Data['Permalink']);
			unset($this->Data['RenameImage']);
			$resultado['Success']=1;
			$resultado['Result']=$this->Data;
			//$resultado['Result']['UserName']=$email;
			//$resultado['Result']['PassW']=$password;
		}
		return $resultado;		
	}

	function Logout($close_all=false){
		if ($close_all) {
			$sql="UPDATE " . $this->table . " SET RegCode='" . $this->username . KeyGen(30) ."' WHERE ID=" . $this->userID;
			parent::$db->Qry($sql);
		}
		$_SESSION['regcode']="";
   		unset($_SESSION['regcode']);
   		setcookie(siteCookie . "_regcode","",time() + 31536000,"/");
		foreach ($_COOKIE as $elemento=>$valor) {
			if (strpos($elemento,'fb_')!==false) {
				setcookie($elemento,"",time(),"/");
				unset ($_COOKIE[$elemento]);
			}
		}
		foreach ($_SESSION as $elemento=>$valor) {
			if (strpos($elemento,'fb_')!==false) {
				unset ($_SESSION[$elemento]);
			}
		}
		session_destroy();
	}
	
	static function Loged(){
		if(isset($_SESSION['regcode'])){
			if($_SESSION['regcode']!="") 
			{ 
				$query_user = "SELECT * FROM users WHERE RegCode='".$_SESSION['regcode']."'";
				return parent::$db->GetDataFieldFromSQL($query_user,'UserName'); 
			} 
			else { return false; }
		}
	}
	
	function CheckField($field,$arg){
		$query_emails = "SELECT * FROM users WHERE " . $field . " = '" . mysqli_real_escape_string(parent::$db->conexion,$arg) . "'";
		$CuentaUsuario=parent::$db->GetDataRecordFromSQL($query_emails);	
		if ($CuentaUsuario===false) { 
			return true; 
		} else { 
			if ($CuentaUsuario['ID']===$this->id){  
				return true; 
			} else { 
				return false; 
			}
		}
	}
	
	function PatchPass(&$formulario) {
		if (isset($formulario['Temp_Password'])) {
			if (strlen($formulario['Temp_Password'])>=$this->siteUserPassMinLength) { $formulario['Form_PassW']=md5($formulario['Temp_Password']); }
		}
	}

	function RewriteParam(&$formulario) {	
		if ($formulario['System_Action']=="new") {
			$formulario['Form_RegCode']=$formulario['Form_UserName'] . KeyGen(30); 
			$formulario['Form_DateInscribe']=date("Y-m-d");
			$formulario['Form_LastLogin']=date("Y-m-d");
			$formulario['Form_LastIP']=getenv("REMOTE_ADDR") . "(" . getenv("HTTP_X_FORWARDED_FOR") . ")";	
			if ($this->conf->Export("UserAutoActive")==1) { $formulario['Active']=1; }
			$formulario['Active']=1;
		}
	}
	
	function SendPassword($email,$frontend=false) {
		//Devuelve:
		// -9=Cuenta deshabilitada por el administrador
		// -1=Error interno
		// 0=Email no valido
		// 1=Envio realizado
		// 2=Cuenta no activada, reenviamos la activación
		$resultado=0;
		//Parcheamos si recibimos un array
		if (is_array($email)) {
			$salida="";
			if (isset($email['email'])) { $salida=$email['email']; }
			if (isset($email['Email'])) { $salida=$email['Email']; }
			if (isset($email['Form_Email'])) { $salida=$email['Form_Email']; }
			$email=$salida;
		}
		if ($email!="") {
			$query_login = "SELECT * FROM users WHERE (email = '" . $email . "' OR UserName='" . $email . "')";
			$CuentaUsuario=parent::$db->GetDataRecordFromSQL($query_login);
			if ($CuentaUsuario!==false) { 
				if ($CuentaUsuario['UserDisallowed']==1) { return -9; }
				if ($CuentaUsuario['Active']==0) { 
					//Cuenta no activada, por tanto, reenviamos mail de activación
					$this->SendActivation($CuentaUsuario['Email'],$frontend);
					$resultado=2;
				} else {
					//Cuenta valida y activada, generamos el email.
					$tokenpassword=KeyGen(40);
					if ($frontend) {
						$enlace=siteprotocol . sitedomain . "/index.php?module=users&action=create_password&token=" .  $tokenpassword . "&email=" . urlencode($CuentaUsuario['Email']) . "&lang=" . $this->userlang;
					} else {
						$enlace=siteprotocol . sitedomain . sitePanelFolder . "/security/create_password/token/" . $tokenpassword . "/email/" . urlencode($CuentaUsuario['Email']);
					}
					$annadir="UPDATE users SET tokenpassword='" . $tokenpassword . "' WHERE Email = '" . $CuentaUsuario['Email'] . "'";
					$annadirexecute = $this->Qry($annadir);
					$cuerpo=_("Hola") . " <strong>" . $CuentaUsuario['UserName'] . "</strong>!" . "<br /><br />";		
					$cuerpo.=_("Para crear una nueva contraseña en") . " <strong>" . siteTitle . "</strong> " . _("haga clic en el siguiente enlace:") . "<br /><br />";
					$cuerpo.="<a href='" . $enlace . "'>" . $enlace . "</a><br /><br />";
					$cuerpo.=_("Si no puede hacer clic en el enlace, péguelo en su navegador web.") . "<br /><br />";
					$cuerpo.=_("En el caso de no haber solicitado el cambio de contraseña, ignore este mensaje.") ."<br />";
					$realizarenvio=SendMail(siteTitle, $CuentaUsuario['Email'], _("Instrucciones para crear una nueva contraseña"), $cuerpo, sitePasswordsMail, 1);				
					$resultado=-1;
					if ($realizarenvio) { $resultado=1; }
				}		
			}
		}
		return $resultado;
	}
	
	function SetNewPassword($id, $Formulario) {	
		$devolver=false;
		$Formulario['Form_PassW']=md5($Formulario['Form_PassW']); 
		$annadir="UPDATE users SET ";
		$annadir.="PassW='" . mysqli_real_escape_string(parent::$db->conexion,$Formulario['Form_PassW']) . "',";
		$annadir.="RegCode='" . mysqli_real_escape_string(parent::$db->conexion,$Formulario['Form_UserName'] . KeyGen(30)) . "', ";
		$annadir.="TokenPassword=''";
		if ($id!=0) { 
			$annadir.=" WHERE ID= " . mysqli_real_escape_string(parent::$db->conexion,$id); 
		} else {
			$annadir.=" WHERE email= '" . mysqli_real_escape_string(parent::$db->conexion,$Formulario['Form_Email']) . "' AND TokenPassword='" . mysqli_real_escape_string(parent::$db->conexion,$Formulario['Form_TokenPassword']) . "'";
		}	
		$annadirexecute = parent::$db->Qry($annadir);
		if (parent::$db->last_affected_rows>0) {
			return true;
		} else {
			return false;
		}
	}

	function ChangePasswordGetProfile($id=0, $email="", $token="") {
		if (($email=="") and (isset($_GET['email']))) { $email=$_GET['email']; }
		if (( $token=="") and (isset($_GET['token']))) { $token=$_GET['token']; }	
		if ($id!==0) { 
			$sql="SELECT * FROM users WHERE ID= '" . mysqli_real_escape_string(parent::$db->conexion,$id) . "'"; 
		} else {
			$sql="SELECT * FROM users WHERE email= '" . mysqli_real_escape_string(parent::$db->conexion,$email) . "' AND TokenPassword='" . mysqli_real_escape_string(parent::$db->conexion,$token) . "'"; 	
		}
		$this->Data=parent::$db->GetDataRecordFromSQL($sql);
		return $this->Data;
	}

	function AdminSendActivation() {
		$this->EditItem();
		$result=$this->SendActivation($this->Data['Email'],false);
		switch ($result) {
			case -2: $text=_("La cuenta ya se encontraba activada"); break;
			case -1: $text=_("Se ha producido un error interno"); break;
			case  0: $text=_("El email no es válido"); break;
			case  1: $text=_("Se ha enviado un email con las instrucciones"); break;
			default: $text=_("Error desconocido");
		}
		header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "/list/text/" . urlencode(base64_encode($text)));
	}

	function SendActivation($email,$frontend=false) {
		//Devuelve:
		// -9=Cuenta desactivada por el administrador
		// -2=Cuenta activada anteriormente.
		// -1=Error interno
		// 0=Email no valido
		// 1=Envio realizado
		$resultado=0;
		//Parcheamos si recibimos un array
		if (is_array($email)) {
			$salida="";
			if (isset($email['email'])) { $salida=$email['email']; }
			if (isset($email['Email'])) { $salida=$email['Email']; }
			if (isset($email['Form_Email'])) { $salida=$email['Form_Email']; }
			$email=$salida;
		}
		if ($email!="") {
			$query_login = "SELECT * FROM users WHERE (email = '" . mysqli_real_escape_string(parent::$db->conexion,$email) . "' OR UserName='" . mysqli_real_escape_string(parent::$db->conexion,$email) . "')";
			$CuentaUsuario=parent::$db->GetDataRecordFromSQL($query_login);
			if ($CuentaUsuario!==false) { 
				if ($CuentaUsuario['UserDisallowed']==1) { return -9; }
				if ($CuentaUsuario['Active']==1) { 
					//La cuenta ya está activada
					$resultado=-2;
				} else {
					//Generamos el email 
					$realizarenvio=$this->SendActivationMail('ID',$CuentaUsuario['ID'],$frontend);
					$resultado=-1;
					if ($realizarenvio) { $resultado=1; }
				}	
			}
		}
		return $resultado;
	}

	function SendActivationMail($campo,$valor,$frontend=false) {
		$tokenpassword=KeyGen(40);
		$annadir="UPDATE users SET tokenpassword='" . mysqli_real_escape_string(parent::$db->conexion,$tokenpassword) . "' WHERE " . $campo . " = '" . mysqli_real_escape_string(parent::$db->conexion,$valor) . "'";
		$annadirexecute = parent::$db->Qry($annadir);
		$query_login = "SELECT * FROM users WHERE " . $campo . " = '" . $valor . "'";
		$CuentaUsuario=parent::$db->GetDataRecordFromSQL($query_login);
		if ($frontend) {
			$enlace=siteprotocol . sitedomain . "/index.php?module=users&action=activate&token=" .  $tokenpassword . "&lang=" . $this->userlang;
		} else {
			$enlace=siteprotocol . sitedomain . sitePanelFolder . "/security/activate?token=" . $tokenpassword;
		}
		$cuerpo=_("Hola") . " <strong>" . $CuentaUsuario['UserName'] . "</strong>!" . "<br /><br />";
		$cuerpo.=_("Acaba de registrar una cuenta de usuario en") . " <strong>" . siteTitle . "</strong> " . _("y es preciso que confirme su cuenta.") . "<br /><br />";
		$cuerpo.=_("Haga clic en el siguiente enlace para validar sus datos") . "<br /><br />";
		$cuerpo.="<a href='" . $enlace . "'>" . $enlace . "</a><br /><br />";
		$cuerpo.=_("Si no puede hacer clic en el enlace, péguelo en su navegador web.") . "<br /><br />";
		$cuerpo.=_("En caso de que no active su cuenta en los próximos 4 días sus datos serán eliminados del sistema.") . "<br /><br />";
		$cuerpo.=_("Si no ha solicitado el ningún registro de cuenta de usuario simplemente ignore este mensaje.") ."<br />";
		$realizarenvio=SendMail(siteTitle, $CuentaUsuario['Email'], _("Instrucciones para activar la cuenta de usuario"), $cuerpo, sitePasswordsMail, 1);	
		return $realizarenvio;
	}

	function ActivateAccount($token) {
		//Devuelve:
		//-9 - Cuenta deshabilitada por el administrador
		//-1 - Error en el token
		// 0 - La cuenta ya estaba activada
		// 1 - Cuenta activada
		$devolver=-1;
		if ($token!='') {
			$query_login = "SELECT ID,Active FROM users WHERE TokenPassword LIKE '" . mysqli_real_escape_string(parent::$db->conexion,$token) . "%'";
			$CuentaUsuario=parent::$db->GetDataRecordFromSQL($query_login);
			if ($CuentaUsuario!==false) {
				$devolver=0;
				if ($CuentaUsuario['UserDisallowed']==1) { return -9; }
				if ($CuentaUsuario['Active']==0) {
					$error="";
					$annadir="UPDATE users SET tokenpassword='', Active=1 WHERE ID = '" . $CuentaUsuario['ID'] . "'";
					$annadirexecute = parent::$db->Qry($annadir);
					$devolver=1;
				}
			} else {
				$devolver=0;
			}
		}
		return $devolver;
	}

	function BeforePostItem() {
		if (isset($_POST['Temp_Password'])) {
			if ($_POST['Temp_Password']!="") { $_POST['Form_RegCode']=$_POST['Form_UserName'] . KeyGen(30); }
		}
		$_POST['Form_LastLogin']=date('Y-m-d H:i:s');
		if ($_POST['System_Action']=="new") { $_POST['Form_DateInscribe']=date('Y-m-d H:i:s'); }
		$this->PatchPass($_POST);
	}
	
	function PostCreateUser($Datos='',$from_social=false) {
		if ($Datos=="") { $Datos=$_POST; }
		$email="";
		$username="";
		$valido=true;
		if (isset($Datos['Form_Email'])) { $email=$Datos['Form_Email']; }
		if (isset($Datos['Form_UserName'])) { 
			$username=$Datos['Form_UserName']; 
		} else {
			$Datos['Form_UserName']=$_POST['Form_Email'];
			$username=$_POST['Form_Email'];
		}	
		if (($email=="") or ($username=="")) { $valido=false; }
		$sql="SELECT COUNT(ID) as Total FROM users WHERE UserName='" . $username . "'";
		if (parent::$db->GetDataFieldFromSQL($sql,'Total')!=0) { $valido=-1; }
		$sql="SELECT COUNT(ID) as Total FROM users WHERE Email='" . $email . "'";
		if (parent::$db->GetDataFieldFromSQL($sql,'Total')!=0) { 
			if ($valido===true) {
				$valido=-2; 
			} else {
				$valido=-3;
			}
		}
		if ($valido) {
			if (! isset($Datos['Form_PassW'])) { 
				$Datos['Form_PassW']=KeyGen(8); 
			} else {
				if ($Datos['Form_PassW']=="") { $Datos['Form_PassW']=KeyGen(8); }
			}
			$Datos['System_Action']="new";
			$Datos['Form_RegCode']=$Datos['Form_UserName'] . KeyGen(30);
			$Datos['Form_DateInscribe']=date('Y-m-d');	
			$Datos['Form_Rol']=1;
			$Datos['Form_LastLogin']=date('Y-m-d H:i:s');
			if ($Datos['System_Action']=="new") { $Datos['Form_LastLogin']=""; }
			$Datos['Form_Active']=0;
			if ($this->conf->Check("UserAutoActive")) { 
				$Datos['Form_Active']=1; 
				$this->Login($Datos['Form_Email'], $Datos['Form_PassW']);
			}
			$pwd=$Datos['Form_PassW'];
			$Datos['Form_PassW']=md5($Datos['Form_PassW']);
			//$Datos['Form_LastUpdate']=date('Y-m-d');
			$ActualID=parent::$db->PostToDatabase('users',$Datos);
			$this->id=$ActualID;
			if ((! $from_social) and ($Datos['Form_Active']==0)) { 
				$this->SendActivationMail('ID',$ActualID,true); 
				$Datos['Form_PassW']=$pwd;
			}
			$valido=$ActualID;
		}
		return $valido;
	}	
	
	function PopulateComboTemplates($TemplateSelection) {
		if ($TemplateSelection=="") { $TemplateSelection=template; }
		$salida='';
		$path=sitepath . "templates/";
		$directorio=dir($path);
		while ($archivo = $directorio->read()) {
			if (is_dir($path . $archivo)) {
				if (($archivo!=".") && ($archivo!="..") && ($archivo!="root") && ($archivo!="admin")) {
					$is_selected="";
					if ($archivo==$TemplateSelection) { $is_selected=" selected "; }
					$salida.='<option value="' . $archivo . '"' . $is_selected . '>' . $archivo . '</option>';
				}
			}	
		}
		return $salida;
	}
	
	function OAuthLogin($Datos,$retorno='') {
		$email="";
		if (isset($Datos['Form_Email'])) { $email=$Datos['Form_Email']; }
		$IDUser=false;
		if (isset($Datos['Form_fb_uid'])) { 
			$sql="SELECT ID FROM users WHERE fb_uid='" . $Datos['Form_fb_uid'] . "'";
			$IDUser=parent::$db->GetDataFieldFromSQL($sql,'ID'); 
		}
		if (($IDUser===false) and (isset($Datos['Form_twitter_uid']))) { 
			$sql="SELECT ID FROM users WHERE twitter_uid='" . $Datos['Form_twitter_uid'] . "'";
			$IDUser=parent::$db->GetDataFieldFromSQL($sql,'ID'); 
		}
		if (($IDUser===false) and ($email=="")) { return false; exit; }
		
		if (($IDUser===false) and (isset($email))) { 
			$sql="SELECT ID FROM users WHERE email='" . $email . "'";
			$IDUser=parent::$db->GetDataFieldFromSQL($sql,'ID'); 
		}
		if ($IDUser!==false) {
			$Datos['System_ID']=$IDUser;
			$Datos['System_Action']="edit";
			unset($Datos['Form_UserName']);
		} else {
			$aleatorio="";
			while (! $this->CheckField('UserName',$Datos['Form_UserName'] . $aleatorio)) {
				if ($aleatorio=="") {
					$aleatorio=1;
				} else {
					$aleatorio++;
				}
			}
			$Datos['Form_UserName']=$Datos['Form_UserName'].$aleatorio;
			$Datos['System_Action']="new";
			$Datos['Form_RegCode']=$Datos['Form_UserName'] .$aleatorio . KeyGen(30);
			$Datos['Form_DateInscribe']=date('Y-m-d');	
			$Datos['Form_Rol']=1;
			$Datos['Form_LastLogin']=date('Y-m-d H:i:s');
			$Datos['Form_Active']=1;
		}
		if (isset($Datos['Form_Image'])) {
			if (is_file(sitepath . 'public/temp/' . $Datos['Form_Image'])) {
				$this->conf = new ConfigUsers();
				$parametros=$this->conf->GetActualConfig();			
				$parametros['UserAvatarImageFolder']='public/avatar_original/';
				$parametros['UserAvatarThumbFolder']='public/avatar/';
				UploadImage($_POST['Form_Image'],$this->conf->Export('AvatarOptions'));
			} else {
				unset($Datos['Form_Image']);
			}
		}		
		$ActualID=parent::$db->PostToDatabase($this->table,$Datos);
		if (($ActualID!=-1) and (isset($_POST['Form_Image']))) { 
			$nombre_imagen=$this->RenameUpload($this->table,'Image',$ActualID,$_POST['Form_Image'],$ActualID . '-avatar-' . $_POST['Form_Username'],array(sitepath . 'public/avatar_original/',sitepath . 'public/avatar/'));
		}			
		
		$sql="SELECT * FROM users WHERE ID='" . $ActualID . "'";
		$usuario = parent::$db->GetDataRecordFromSQL($sql);
		$_SESSION['userid'] = $usuario['ID'];
		$_SESSION['username'] = $usuario['UserName'];
		$_SESSION['userlevel'] = $usuario['Rol'];
		$_SESSION['regcode']= $usuario['RegCode'];
		if ($retorno=="") { $retorno='/'; }
		$z=strpos($retorno,'security');
		if ($z!==false) { $retorno=substr($retorno,1,$z-1); }
		header('Location: ' . siteprotocol . sitedomain);
		return true;

	}
	
	function OAuthUpdateEmail($Datos,$retorno='') {
		if (! $this->CheckField('Email',$Datos['Form_Email'])) {
			header('Location: ../lib/oauth_email.php?oauthcode=' . md5($this->userID) . '&message=' . urlencode("La dirección de correo electrónico facilitada está vinculada a otra cuenta. Escribe otra") . '&urlreferer=' . $retorno);
			exit;
		} else {		
			$Datos['System_Action']="edit";
			$Datos['System_ID']=$this->userID;
			$IDUser=parent::$db->PostToDatabase('users',$Datos);
			if ($retorno=="") { $retorno='/'; }
			$z=strpos($retorno,'security');
			if ($z!==false) { $retorno=substr($retorno,1,$z-1); }
			header('Location: http://' . sitedomain);
		}
	}

	function PrepareTableList() {
		$this->AddMainMenu('Crear',$this->module . '/new');
		$this->AddTableRowClass('warning','{{Active}}==0');
		$this->AddTableRowClass('danger','{{UserDisallowed}}==1');
		$this->AddTableContent('Usuario','data','{{UserName}}','',$this->module . '/edit/id/{{ID}}');
		$this->AddTableContent('Email','data','{{Email}}');
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block,'Reenviar activación',$this->module . '/sendactivation/prior/{{ID}}','{{Active}}==0');
		if($this->conf->Check("UserViewDevices")) { $this->AddTableOperations($in_block,'Dispositivos',$this->module . '/devices_list/idparent/{{ID}}'); }
		$this->AddTableOperations($in_block,'Editar',$this->module . '/edit/id/{{ID}}');
		$this->AddTableOperations($in_block,'Eliminar',$this->module . '/delete/id/{{ID}}','{{ID}}!=' . $this->userID);
	}

	function PrepareForm($admin=false) {
		$in_block=$this->AddFormBlock('Cuenta');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Nombre de usuario","FieldName":"Form_UserName","Value":"' . $this->Data['UserName'] . '","Required": true, "CheckScript":"' . $this->module . '?action=nickcheck"}');
		$this->AddFormContent($in_block,'{"Type":"email","Text":"Correo electrónico","FieldName":"Form_Email","Value":"' . $this->Data['Email'] . '","Required": true, "CheckScript":"' . $this->module . '?action=emailcheck"}');
		$required=',"Help":"Dejar en blanco para mantener la contraseña actual"';
		if($this->Data['Action']!="edit") { $required=',"Required":true,"Help":"Obligatorio para crear una cuenta nueva"'; }
		$this->AddFormContent($in_block,'{"Type":"password-retype","Text":"Contraseña","FieldName":"Temp_Password","Value":"","MinLength":"' . $this->conf->Export("UserPassMinLength") . '"' . $required . '}');
		if ($admin) {
			$this->AddFormContent($in_block,'{"Type":"combo","Text":"Rol de usuario","FieldName":"Form_Rol","Value":"' . $this->Data['Rol'] . '", "ListTable": "users_roles", "ListValue": "IDRol", "ListOption": "RolName", "ListOrder":"IDRol"}');		
			if(! $this->Check('UserAutoActive')){ $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Cuenta activa","FieldName":"Form_Active","Value":"' . $this->Data['Active'] . '"}'); }
		}
		$this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Bloquear el acceso a este usuario","FieldName":"Form_UserDisallowed","Value":"' . $this->Data['UserDisallowed'] . '"}');
		$this->AddFormContent($in_block,'{"Type":"combo","Text":"Idioma","FieldName":"Form_Language","Value":"' . $this->Data['Language'] . '", "ListTable": "languages", "ListValue": "id", "ListOption": "language", "ListOrder":"language"}');	
		if($this->Check('UserAvatar')){ $this->AddFormContent($in_block,'{"Type":"upload","Text":"Imagen","FieldName":"Form_Image","Value":"' . $this->Data['Image'] . '","UploadType": "image", "UploadItem":"first", "Extensions": "jpg,jpeg,gif,png", "PreviewFolder": "public/avatar", "External":"' . $this->Data['RenameImage'] . '"}'); }
		if(siteNotifyEmailLogins) { $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Notificar por email cada vez que inicia sesión","FieldName":"Form_NotifyLoginEmail","Value":"' . $this->Data['NotifyLoginEmail'] . '"}'); }
		if($this->Check('UserBirthdate')){ $this->AddFormContent($in_block,'{"Type":"date","Text":"Fecha de nacimiento","FieldName":"Form_BirthDate","Value":"' . $this->Data['BirthDate'] . '","MinValue": "0", "MaxValue": "9999"}'); }
		if(($this->Check('Username')) or ($this->Check('UserNIF')) or ($this->Check('UserStreet')) or ($this->Check('UserCity')) or ($this->Check('UserState')) or ($this->Check('UserCountry'))
			or ($this->Check('UserZipCode')) or ($this->Check('UserPhone')) or ($this->Check('UserFax')) or ($this->Check('UserPublicEmail')) or ($this->Check('UserWeb')) or ($this->Check('UserSignature'))){
			$in_block=$this->AddFormBlock('Datos Personales');
			if($this->Check('Username')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Nombre y apellidos","FieldName":"Form_Name","Value":"' . addcslashes($this->Data['Name'],'\\"') . '"}'); }
			if($this->Check('UserNIF')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"NIF","FieldName":"Form_NIF","Value":"' . addcslashes($this->Data['NIF'],'\\"') . '"}'); }
			if($this->Check('UserStreet')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Dirección","FieldName":"Form_Street","Value":"' . addcslashes($this->Data['Street'],'\\"') . '"}'); }
			if($this->Check('UserStreetNum')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Dirección (número)","FieldName":"Form_StreetNum","Value":"' . addcslashes($this->Data['StreetNum'],'\\"') . '"}'); }
			if($this->Check('UserStreetOtherData')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Dirección (portal, escalera, piso y puerta)","FieldName":"Form_StreetOtherData","Value":"' . addcslashes($this->Data['StreetOtherData'],'\\"') . '"}'); }
			if($this->Check('UserCity')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Localidad","FieldName":"Form_City","Value":"' . addcslashes($this->Data['City'],'\\"') . '"}'); }
			if($this->Check('UserState')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Provincia","FieldName":"Form_State","Value":"' . addcslashes($this->Data['State'],'\\"') . '"}'); }
			if($this->Check('UserCountry')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"País","FieldName":"Form_Country","Value":"' . addcslashes($this->Data['Country'],'\\"') . '"}'); }
			if($this->Check('UserZipCode')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Código Postal","FieldName":"Form_ZipCode","Value":"' . addcslashes($this->Data['ZipCode'],'\\"') . '"}'); }
			if($this->Check('UserPhone')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Teléfono","FieldName":"Form_Telephone","Value":"' . addcslashes($this->Data['Telephone'],'\\"') . '"}'); }
			if($this->Check('UserFax')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Fax","FieldName":"Form_Fax","Value":"' . addcslashes($this->Data['Fax'],'\\"') . '"}'); }
			if($this->Check('UserPublicEmail')){ $this->AddFormContent($in_block,'{"Type":"email","Text":"Correo electrónico público","FieldName":"Form_EmailPublic","Value":"' . addcslashes($this->Data['EmailPublic'],'\\"') . '"}'); }
			if($this->Check('UserWeb')){ $this->AddFormContent($in_block,'{"Type":"url","Text":"Web Personal","FieldName":"Form_Web","Value":"' . addcslashes($this->Data['Web'],'\\"') . '"}'); }
			if($this->Check('UserSignature')){ $this->AddFormContent($in_block,'{"Type":"html","Text":"Firma en mensajes","FieldName":"Form_Signature","Value":"' . addcslashes($this->Data['Signature'],'\\"') . '"}'); }
		}
		if(($this->Check('UseInvoiceNIF')) or ($this->Check('UseInvoiceName')) or ($this->Check('UseInvoiceStreet')) or ($this->Check('UseInvoiceZipCode')) or ($this->Check('UseInvoiceCity')) or ($this->Check('UseInvoiceState'))
			or ($this->Check('UseInvoiceCountry')) or ($this->Check('UseInvoicePhone')) or ($this->Check('UseInvoiceEmail')) or ($this->Check('UseInvoiceBankName')) or ($this->Check('UseInvoiceBankSwiftCode')) or 
			($this->Check('UseInvoiceBankAccount')) or ($this->Check('UseInvoiceBankOwner'))) { 
			$in_block=$this->AddFormBlock('Facturación');
			if($this->Check('UseInvoiceNIF')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"NIF","FieldName":"Form_InvoiceNIF","Value":"' . addcslashes($this->Data['InvoiceNIF'],'\\"') . '"}'); }
			if($this->Check('UseInvoiceName')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Nombre completo","FieldName":"Form_InvoiceName","Value":"' . addcslashes($this->Data['InvoiceName'],'\\"') . '"}'); }
			if($this->Check('UseInvoiceStreet')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Dirección","FieldName":"Form_InvoiceStreet","Value":"' . addcslashes($this->Data['InvoiceStreet'],'\\"') . '"}'); }
			if($this->Check('UseInvoiceCity')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Localidad","FieldName":"Form_InvoiceCity","Value":"' . addcslashes($this->Data['InvoiceCity'],'\\"') . '"}'); }
			if($this->Check('UseInvoiceState')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Provincia","FieldName":"Form_InvoiceState","Value":"' . addcslashes($this->Data['InvoiceState'],'\\"') . '"}'); }
			if($this->Check('UseInvoiceCountry')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"País","FieldName":"Form_InvoiceCountry","Value":"' . addcslashes($this->Data['InvoiceCountry'],'\\"') . '"}'); }
			if($this->Check('UseInvoiceZipCode')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Código Postal","FieldName":"Form_InvoiceZipCode","Value":"' . addcslashes($this->Data['InvoiceZipCode'],'\\"') . '"}'); }
			if($this->Check('UseInvoicePhone')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Teléfono","FieldName":"Form_InvoicePhone","Value":"' . addcslashes($this->Data['InvoicePhone'],'\\"') . '"}'); }
			if($this->Check('UseInvoiceEmail')){ $this->AddFormContent($in_block,'{"Type":"email","Text":"Correo electrónico","FieldName":"Form_InvoiceEmail","Value":"' . addcslashes($this->Data['InvoiceEmail'],'\\"') . '"}'); }
			if($this->Check('UseInvoiceBankName')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Entidad Bancaria","FieldName":"Form_InvoiceBankName","Value":"' . addcslashes($this->Data['InvoiceBankName'],'\\"') . '"}'); }
			if($this->Check('UseInvoiceBankSwiftCode')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Código Swift","FieldName":"Form_InvoiceBankSwiftCode","Value":"' . addcslashes($this->Data['InvoiceBankSwiftCode'],'\\"') . '"}'); }
			if($this->Check('UseInvoiceBankAccount')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Número de cuenta","FieldName":"Form_InvoiceBankAccount","Value":"' . addcslashes($this->Data['InvoiceBankAccount'],'\\"') . '"}'); }
			if($this->Check('UseInvoiceBankOwner')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Titular de la cuenta","FieldName":"Form_InvoiceBankOwner","Value":"' . addcslashes($this->Data['InvoiceBankOwner'],'\\"') . '"}'); }
		}
		if(($admin) and ($this->Check('UserViewFBData'))){ 
			$in_block=$this->AddFormBlock('Facebook');
			$this->AddFormContent($in_block,'{"Type":"text","Text":"UID de Facebook","FieldName":"View_fb_uid","Value":"' . $this->Data['fb_uid'] . '", "Readonly": true}');
			$this->AddFormContent($in_block,'{"Type":"url","Text":"URL Perfil de Facebook","FieldName":"View_fb_link","Value":"' . $this->Data['fb_link'] . '", "Readonly": true}');
			$this->AddFormContent($in_block,'{"Type":"combo-json","Text":"Sexo","FieldName":"Form_fb_gender","Value":"' . $this->Data['fb_gender'] . '", "JsonValues": {"male":"Hombre","female":"Mujer"}}');
			$this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Permitir actualizar datos desde Facebook","FieldName":"Form_fb_updateenable","Value":"' . $this->Data['fb_updateenable'] . '"}');
			$this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Permitir publicar en el muro de Facebook","FieldName":"Form_fb_feedenable","Value":"' . $this->Data['fb_feedenable'] . '"}');
		}
		if ($admin) {
			$in_block=$this->AddFormBlock('Estadísticas');
			$this->AddFormContent($in_block,'{"Type":"date","Text":"Fecha de alta","FieldName":"Form_DateInscribe","Value":"' . $this->Data['DateInscribe'] . '"}');
			if($this->Check('UseDateExpire')){  $this->AddFormContent($in_block,'{"Type":"datetime","Text":"Fecha de finalización acceso","FieldName":"Form_DateExpire","Value":"' . $this->Data['DateExpire'] . '"}'); }
			$this->AddFormContent($in_block,'{"Type":"datetime","Text":"Último acceso","FieldName":"Form_LastLogin","Value":"' . $this->Data['LastLogin'] . '"}');
			$this->AddFormContent($in_block,'{"Type":"number","Text":"Número de publicaciones","FieldName":"Form_CountPages","Value":"' . $this->Data['CountPages'] . '"}');
			$this->AddFormContent($in_block,'{"Type":"number","Text":"Número de publicaciones en foro","FieldName":"Form_CountPost","Value":"' . $this->Data['CountPost'] . '"}');
			$this->AddFormContent($in_block,'{"Type":"number","Text":"Número de comentarios publicados","FieldName":"Form_CountComments","Value":"' . $this->Data['CountComments'] . '"}');
		}
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
	}

	function ListAdmItems() {
		$this->GetItems("Rol<=" . $this->userLevel,false,"",$this->search,false);
		$this->PrepareTableList();
		if ($this->paged) {
			$this->LoadTemplate('list_paged.tpl.php');
		} else {
			$this->LoadTemplate('list.tpl.php');
		}
	}

	function NewAdmItem() {
		$values['DateInscribe']=date('d/m/Y',time()+315360000) . " 23:59:00";
		$values['Language']=$this->userlang;
		$this->NewItem($values);
		$this->TotalLanguages=0;	
		$this->PrepareForm(true);
		$this->LoadTemplate('edit.tpl.php');
	}

	function EditAdmItem($id="") {
		$this->EditItem($id);
		$this->CheckLevel($this->Data['Rol']);
		$this->PrepareLangMenu(true);
		$this->PrepareForm(true);
		$this->LoadTemplate('edit.tpl.php');
	}

	function UserDataJson($id) {
		$this->EditItem($id);
		return json_encode($this->Data);
	}

	function RunXtraDevices($action) {
		$action=str_replace("devices_", "", $this->action);
		$this->Xtra= new UserDevices($this->_values);
		$this->Xtra->action=$action;
		$this->Xtra->RunAction();
	}
	
	function RunAction() {
		if ($this->action=="sendactivation") {
			$error=$this->SendActivate();
			header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "/list/text/" . urlencode(base64_encode($error)));
		}
		if ($this->action=="emailcheck") { echo intval($this->CheckField('Email',$_POST['value'])); exit; }
		if ($this->action=="nickcheck") { echo intval($this->CheckField('UserName',$_POST['value'])); exit; }
		if ($this->action=="jsondata") { echo $this->UserDataJson($this->id); exit; }
		if (strpos($this->action, "devices_")!==false) { $this->RunXtraDevices($this->action); exit; }
		parent::RunAction();
	}
}
?>