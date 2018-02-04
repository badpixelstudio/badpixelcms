<?php
// Gestión de Apps de la API
// Creado por Israel García Sáez para BadPixel,
// Revisión: 1.0 de 6 de Febrero de 2013, por Israel Garcia.
//		Release original

require_once(sitepath . "include/core/core.class.php");
require_once(sitepath . "include/apps/apps.config.php");

class OAuth extends Core{
	
	//Inicializamos valores por defecto
	var $title = 'OAuth';
	var $conf = null;	
	var $table_apps = "api_apps";
	var $table = 'oauth_accesstokens';	
	var $version=false;	
	
	
	//constructor
	function __construct($values) {
		parent::__construct($values);  
		//Cargamos la configuración
		$this->conf = new ConfigApps(0);
		$sql="DELETE FROM oauth_logintokens WHERE Expires<'" . date('Y-m-d H:i:s') . "'";
		parent::$db->Qry($sql);
	}
	
	function GetStatusApp($consumerkey,$consumertoken) {
		$sql="SELECT * FROM " . $this->table_apps . " WHERE ConsumerKey='" . $consumerkey . "' AND ConsumerToken='" . $consumertoken . "' AND Enabled=1";
		$this->AppData=parent::$db->GetDataRecordFromSQL($sql);
		if ($this->AppData!==false) {
			if ($this->AppData['AllowOAuthSign']==0) { return -2; }
			if ($this->userID==0) {
				//Debe logearse para autorizar la aplicación
				return -1;
			} else {
				return 1;
			}
		} else {
			return 0;
		}
	}
	
	function AuthorizeApp($AppID) {
		$token=$this->AccessToken($AppID);
		$valido=$this->LoginFromAccessToken($token);
		if ($valido) {
			return $token;
		} else {
			return "error";
		}
	}
	
	function LoginRedirect($access_token) {
		$redirigir="";
		if (isset($_GET['redirect_uri'])) { $redirigir=urldecode($_GET['redirect_uri']); }
		if ($redirigir=="") { $redirigir=$this->AppData['CallbackURL']; }
		if ($redirigir=="") { $redirigir="/"; }
		$union="?";
		$posic=strpos($redirigir,"?");
		if ($posic!==false) { $union="&"; }
		if ($access_token!="") { 
			$redirigir.=$union . "access_token=" . $access_token; 
		} else {
			$redirigir.=$union . "error=" . urlencode($this->Result['Message']);
		}
		return $redirigir;
	}
	
	function NotAuthorizedRedirect() {
		$redirigir="";
		if (isset($_GET['redirect_uri'])) { $redirigir=urldecode($_GET['redirect_uri']); }
		if ($redirigir=="") { $redirigir=$this->OAuth->AppData['CallbackURL']; }
		if ($redirigir=="") { $redirigir="/"; }
		$union="?";
		$posic=strpos($redirigir,"?");
		if ($posic!==false) { $union="&"; }
		$redirigir.=$union . "error=" . urlencode('App not authorized by user.');
		return $redirigir;
	}
	
	function AccessToken($AppID,$UserID=0) {
		if ($UserID==0) { $UserID=$this->userID; }
		$sql="SELECT * FROM " . $this->table . " WHERE IDApp=" . $AppID . " AND IDUser=" . $UserID;
		$token=parent::$db->GetDataRecordFromSQL($sql);

		$regenerar=true;
		if ($token!==false) {
			$Datos['System_Action']="edit";
			$Datos['System_ID']=$token['ID'];
			$Datos['Form_LongLife']=$token['LongLife'];
			$Datos['Form_AccessToken']=$token['AccessToken'];
			//Comprobamos si la Sesión es aún válida.
			if ($token['Expires']>date('Y-m-d H:i:s')) {$regenerar=false; }		
		} else {
			$Datos['System_Action']="new";
			$Datos['System_ID']=-1;
			$Datos['Form_IDApp']=$AppID;
			$Datos['Form_IDUser']=$UserID;
			$Datos['Form_LongLife']=0;
		}
		//Regeneramos el AccessToken sólo si es estritamente necesario...
		if ($regenerar) {
			$valido=false;
			while (! $valido) {
				$Datos['Form_AccessToken']=KeyGen(210);	
				$sql="SELECT ID FROM " . $this->table ." WHERE AccessToken='" . $Datos['Form_AccessToken'] . "'";
				$valido=false;
				$datos=parent::$db->GetDataRecordFromSQL($sql);
				if ($datos===false) { $valido=true; }
			}
		}
		$tiempo=siteOAuthAccessTokenExpires;
		if ($Datos['Form_LongLife']==1) { $tiempo=siteOAuthExtendedAccessTokenExpires; }
		$Datos['Form_Expires']=date('Y-m-d H:i:s', time()+$tiempo);
		$ActualID = $this->PostToDatabase($this->table,$Datos);
		return $Datos['Form_AccessToken'];
	}
	
	function OAuthGetURL($consumerkey,$consumertoken,$redirect="") {
		$sql="SELECT * FROM " . $this->table_apps . " WHERE ConsumerKey='" . $consumerkey . "' AND ConsumerToken='" . $consumertoken . "' AND Enabled=1";
		$Datos=parent::$db->GetDataRecordFromSQL($sql);
		$url="";
		if ($Datos!==false) {
			//Generamos un LoginToken
			$Crear['System_ID']=-1;
			$Crear['System_Action']="new";
			$Crear['Form_IDApp']=$Datos['ID'];
			$valido=false;
			while (! $valido) {
				$Crear['Form_LoginToken']=KeyGen(22);	
				$sql="SELECT ID FROM oauth_logintokens WHERE LoginToken='" . $Crear['Form_LoginToken'] . "'";
				$consulta=parent::$db->GetDataRecordFromSQL($sql);
				if ($consulta===false) { $valido=true; }
			}
			$Crear['Form_Expires']=date('Y-m-d H:i:s',time()+1440);
			$ActualID = $this->PostToDatabase('oauth_logintokens',$Crear);
			$url=siteprotocol . sitedomain . "/oauth/authorize?client_id=" . md5($Datos['ID']) . '&auth=' . $Crear['Form_LoginToken'];
		}
		return $url;
	}
	
	function GetConsumerDataFromClientID($client_id) {
		$sql="SELECT * FROM " . $this->table_apps . " WHERE md5(ID)='" . $client_id . "' AND Enabled=1";
		$this->Datos=parent::$db->GetDataRecordFromSQL($sql);
		if ($this->Datos===false) {
			return false;
		} else {
			return $this->Datos;
		}
	}
	
	
	
}