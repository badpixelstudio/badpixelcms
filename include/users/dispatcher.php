<?php
require_once(sitepath . "templates/" . $this->template . "/adapter.php");
require_once(sitepath . "include/users/users.class.php");

$this->user = new User($this->params);
//ini_set("display_errors", 0);

$this->user->viewpanel="";

$login_username="";
$login_userpass="";
$login_recuerda="";


if (count($_POST)>0) {
	foreach ($_POST as $iden=>$valor) {
		$_POST[$iden]=$valor;
	}
}	

if(isset($_POST['username'])){ $login_username=$_POST['username'];}
if(isset($_POST['password'])){ $login_userpass=$_POST['password']; }
if(isset($_POST['remember'])){ $login_recuerda=$_POST['remember']; }

if (($this->user->action=="start") or ($this->user->action=="")) {
	$this->user->Redirect=siteprotocol . sitedomain;
	if (isset($_SERVER['HTTP_REFERER'])) { $this->user->Redirect=$_SERVER['HTTP_REFERER']; }
	$this->loadtemplatepublic('user_login.tpl.php');		
}

if ($this->user->action=="login") {
	echo $this->user->Login($login_username,$login_userpass,$login_recuerda);
}

if ($this->user->action=="logout") {
	$close_all=false;
	if ((isset($_GET['closeall'])) or (isset($this->_values['closeall']))) { $close_all=true; }
	$this->user->Logout($close_all);
	$url=siteprotocol . sitedomain;
	if (isset($_SERVER['HTTP_REFERER'])) {
		$url=$_SERVER['HTTP_REFERER'];
		$posic=strpos($url,"access_token=");
		if ($posic!==false) { 
			$parte2=substr($url,$posic);
			$url=substr($url,0,$posic);
			if ($parte2!="") {
				$posic=strpos($parte2,"&");
				if ($posic!==false) {
					$url.=substr($parte2,$posic+1);
				}
			}
		}
	} 
	header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
	header("Location: " . $url);
}		

if ($this->user->action=="retrieve_password") {
	$this->loadtemplatepublic('user_retrievepassword.tpl.php');
}

if ($this->user->action=="send_password") {
	echo $this->user->SendPassword($_POST,true);
}	

if ($this->user->action=="create_password") {	
	if (isset($_POST['Form_PassW'])) {
		echo intval($this->user->SetNewPassword($this->userID, $_POST));
	} else {
		$this->user->Data['Email']=$this->params['email'];
		$this->user->Data['TokenPassword']=$this->params['token'];

		$Datos=$this->user->ChangePasswordGetProfile($this->userID, $this->user->Data['Email'],$this->user->Data['TokenPassword']);	
		if ($Datos!==false) {
			$this->user->Data['UserName']=$Datos['UserName'];	
			$this->user->viewpanel="changepass";		
		} else {
			$this->user->viewpanel="error";
			$this->user->error="Error al acceder a la aplicación";
		}
		$this->loadtemplatepublic('user_createpassword.tpl.php');
	}
}

if ($this->user->action=="register") {
	$this->user->viewpanel="register";
	$this->loadtemplatepublic('user_register.tpl.php');
}

if ($this->user->action=="register_post") {
	$error="";
	$devolver=-1;
	$this->user->id=0;
	if (! $this->user->CheckField('UserName',$_POST['Form_UserName'])) {
		$error.=htmlentities("El nombre de usuario ya existe") . "<br />";
	}
	if (! $this->user->CheckField('Email',$_POST['Form_Email'])) {
		$error.=htmlentities("El correo electrónico esta vinculado a otro usuario") . "<br />";
	}		
	if ($error=="") {
		//Registramos!!!
		$devolver=$this->user->PostCreateUser();
		// //Damos de alta en el newsletter...
		// $Datos['System_Action']="new";
		// $Datos['Form_IDMajordomo']=1;
		// $Datos['Form_Email']=$_POST['Form_Email'];
		// self::$db->PostToDatabase('majordomo_subscriptors',$Datos);
		//Devolvemos el mensaje...
		if ($devolver===false) { 
			$error=htmlentities("Error al crear la cuenta de usuario"); 
			$devolver=0;
		} else {
			if ($this->user->conf->Check("UserAutoActive")) {
				$error=htmlentities("Se ha creado tu cuenta de usuario");
				$devolver=1;
			} else {
				$error=htmlentities("Se ha creado la cuenta de usuario pero es necesario que confirmes el correo electrónico.") ."<br /><br />" . htmlentities("Te hemos enviado instrucciones a tu dirección de email");
				$devolver=2;
			}
		}
	}
	$Datos['Error']=($error);
	$Datos['Status']=$devolver;
	echo json_encode($Datos);
}

if ($this->user->action=="retrieve_activation") {
	$this->loadtemplatepublic('user_retrieveactivation.tpl.php');
}

if ($this->user->action=="send_activation") {
	echo intval($this->user->SendActivation($_POST,true));
}

if ($this->user->action=="activate") {
	$activar=$this->user->ActivateAccount($_GET['token']);
	if ($activar==-1) { $text= "El token no ha sido aceptado"; }
	if ($activar==0) { $text= "La cuenta ya estaba activada"; }
	if ($activar==1) { $text= "La cuenta ha sido activada."; }
	header("Location: iniciar-sesion?text=" . $text);
}
if ($this->user->action=="profile") {
	if ($this->userID==0) { header("Location: " . siteprotocol . sitedomain); exit; }
	if (isset($_POST['System_Action'])) {
		$_POST['System_Action']="edit";
		$_POST['System_ID']=$this->userID;
		$this->user->PostItem(false);
		if (is_file(sitepath . "majordomo/majordomo.class.php")) {
			$sql_newsletter="SELECT ID FROM majordomo_subscriptors WHERE IDMajordomo=1 AND Email='" . mysqli_real_escape_string(self::$db->conexion,$_POST['Form_Email']) . "'";
			$this->user->Data['Subscribe']=self::$db->GetDataFieldFromSQL($sql_newsletter,"ID");
			if ((isset($_POST['System_Subscribe'])) and ($this->user->Data['Subscribe']===false)) {
				//Suscribimos...	
				$Datos['System_Action']="new";
				$Datos['Form_IDMajordomo']=1;
				$Datos['Form_Email']=$_POST['Form_Email'];
				self::$db->PostToDatabase('majordomo_subscriptors',$Datos);
			}
			if (! (isset($_POST['System_Subscribe'])) and ($this->user->Data['Subscribe']!==false)) {
				//Damos de baja...
				$baja="DELETE FROM majordomo_subscriptors WHERE ID=" . 	$this->user->Data['Subscribe'];
				self::$db->Qry($baja);
			}
		}
		header("Location: " . siteprotocol . sitedomain . "/mi-perfil");
		die;
	}
	$this::$db->LoadFormData($this->user,$this->userID);
	if (is_file(sitepath . "majordomo/majordomo.class.php")) {
		$sql_newsletter="SELECT COUNT(ID) as Total FROM majordomo_subscriptors WHERE IDMajordomo=1 AND Email='" . mysqli_real_escape_string(self::$db->conexion,$this->useremail) . "'";
		$this->user->Data['Subscribe']=self::$db->GetDataFieldFromSQL($sql_newsletter,"Total");
	}
	$this->HeadTitle="Editar mi cuenta de usuario" ;
	$this->HeadDescription=("Editar mi cuenta de usuario");
	$this->loadtemplatepublic('user_edit.tpl.php');
}

if ($this->user->action=="checkemail") {
	$this->user->id=$this->userID;
	echo intval($this->user->CheckField("Email",$_POST['Form_Email']));
}
?>