<?php
require_once(sitepath . "/include/users/users.class.php");
require_once(sitepath . "/include/users/oauth.class.php");
require_once(sitepath . "/include/extras/comments.class.php");

class ws_user extends User {
	
	function ws_me() {
		//Comprobamos que haya un AccessToken
		if (! $this->AccessTokenValid) {
			$Resultado=$this->AccessTokenResult;
			return $Resultado;		
		}
		parent::$db->LoadFormData($this,$this->userID,1);
		if ($this->Data!==false) {
			unset($this->Data['Action']);
			unset($this->Data['RegCode']);
			unset($this->Data['PassW']);
			unset($this->Data['Rol']);
			unset($this->Data['LastLogin']);
			unset($this->Data['LastIP']);
			unset($this->Data['Active']);
			unset($this->Data['CountPages']);
			unset($this->Data['CountPost']);
			unset($this->Data['CountComments']);
			unset($this->Data['ProfilePublic']);
			unset($this->Data['Template']);
			unset($this->Data['fb_uid']);
			unset($this->Data['fb_link']);
			unset($this->Data['fb_updateenable']);
			unset($this->Data['fb_feedenable']);
			unset($this->Data['twitter_uid']);
			unset($this->Data['twitter_link']);
			unset($this->Data['twitter_access_token']);
			unset($this->Data['twitter_access_token_secret']);
			unset($this->Data['TokenPassword']);
			$Resultado['Success']=1;
			$Resultado['Result']['Data']=$this->Data;
			return $this->Data;
		} else {
			$Resultado['Success']=0;
			$Resultado['Result']='No existe el usuario';
		}
		return $Resultado;
	}

	function wsSendActivation($email) {
		//Comprobamos que haya un AccessToken
		if (! $this->AccessTokenValid) {
			$Resultado=$this->AccessTokenResult;
			return $Resultado;		
		}
		$result=$this->SendActivation($email,false);
		if ($result==0) {
			$Resultado['Success']=0;
			$Resultado['Result']="Not found";
		}
		if ($result==1) {
			$Resultado['Success']=1;
			$Resultado['Result']="Activation mail sended";
		}
		if ($result==-1) {
			$Resultado['Success']=-1;
			$Resultado['Result']="Internal error";
		}
		if ($result==-2) {
			$Resultado['Success']=-2;
			$Resultado['Result']="Account is activated";
		}
		return $Resultado;
	}

	function wsSendPassword($email) {
		//Comprobamos que haya un AccessToken
		if (! $this->AccessTokenValid) {
			$Resultado=$this->AccessTokenResult;
			return $Resultado;		
		}
		$result=$this->SendPassword($email,true);
		if ($result==0) {
			$Resultado['Success']=0;
			$Resultado['Result']="Not found";
		}
		if ($result==1) {
			$Resultado['Success']=1;
			$Resultado['Result']="Instrucions sended";
		}
		if ($result==-1) {
			$Resultado['Success']=-1;
			$Resultado['Result']="Internal error";
		}
		if ($result==-2) {
			$Resultado['Success']=-2;
			$Resultado['Result']="Account not activated, instructions sended";
		}
		return $Resultado;
	}

	function wsPostEdit($data) {
		//Comprobamos que haya un AccessToken
		if (! $this->AccessTokenValid) {
			$Resultado=$this->AccessTokenResult;
			return $Resultado;		
		}
		$_POST=$data;
		$_POST['System_Action']="edit";
		if (! isset($_POST['System_ID'])) { $_POST['System_ID']=$this->userID; }
		$_POST['Form_Active']=1;
		$this->PostAdmItem(false);
		$Resultado['Success']=1;
		$Resultado['Result']="Success!";
		return $Resultado;
	}

	function wsCreateAccount($data) {
		//Comprobamos que haya un AccessToken
		if (! $this->AccessTokenValid) {
			$Resultado=$this->AccessTokenResult;
			return $Resultado;		
		}
		$_POST['System_Action']="new";
		$_POST['System_ID']=-1;
		$result=$this->PostCreateUser($data,false);
		if ($result>0) {
			$Resultado['Success']=$result;
			$Resultado['Result']="Account created";
		} else {
			$Resultado['Success']=$result;
			$Resultado['Result']="Error";
		}
		return $Resultado;
	}

	function wsLoginSocial($Datos) {
		//Comprobamos que haya un AccessToken
		if (! $this->AccessTokenValid) {
			$Resultado=$this->AccessTokenResult;
			return $Resultado;		
		}
		$result=$this->LoginOAuth($Datos);
		return $result;
	}

	function wsCheckField($id,$field,$arg) {
		echo $arg;
		//Comprobamos que haya un AccessToken
		if (! $this->AccessTokenValid) {
			$Resultado=$this->AccessTokenResult;
			return $Resultado;		
		}
		$this->id=$id;
		$result=$this->CheckField($field,$arg);
		if ($result) {
			$Resultado['Success']=1;
			$Resultado['Result']="Valid";
		} else {
			$Resultado['Success']=0;
			$Resultado['Result']="Duplicate value";
		}
		return $Resultado;
	}

	
	function ws_alarms() {
		//Comprobamos que haya un AccessToken
		if (! $this->AccessTokenValid) {
			$Resultado=$this->AccessTokenResult;
			return $Resultado;		
		}		
		$sql="SELECT calendar_events.*, calendar_alerts.IDUser FROM calendar_alerts LEFT JOIN calendar_events ON calendar_alerts.IDFather=calendar_events.ID WHERE calendar_alerts.IDUser=" . $this->userID . " AND Sended=0";
		$TotalAlarms=self::$db->GetDataListFromSQL($sql,$Alarms);
		if ($TotalAlarms>0) {
			$Resultado['Success']=1;
			$Resultado['Result']['ItemCount']=$TotalAlarms;
			$Resultado['Result']['Items']=$Alarms;
		} else {
			$Resultado['Success']=0;
			$Resultado['Result']="No records found";
		}
		return $Resultado;
	}
	
	function ws_likes($accion='+') {
		//Comprobamos que haya un AccessToken
		if (! $this->AccessTokenValid) {
			$Resultado=$this->AccessTokenResult;
			return $Resultado;		
		}		
		$sql="SELECT * FROM likethis WHERE IDUser=" . $this->userID . " AND Vote='" . $accion . "' ORDER BY ID";
		$TotalLikes=self::$db->GetDataListFromSQL($sql,$Likes);
		if ($TotalLikes>0) {
			foreach($Likes as $idl=>$like) {
				$Likes[$idl]['Title']='';
				$Likes[$idl]['Permalink']='';
				if (($like['TableName']!="") and ($like['TableID']!="")) {
					$sql="SELECT * FROM " . $like['TableName'] . " WHERE ID=" . $like['TableID'];	
					$datos=parent::$db->GetDataRecordFromSQL($sql);
					if ($datos!==false) {
						$pag="";
						if (isset($datos['Title'])) { $pag=$datos['Title']; }
						if (isset($datos['Name'])) { $pag=$datos['Name']; }	
						$Likes[$idl]['Title']=$pag;
						$Likes[$idl]['Permalink']=siteprotocol . sitedomain . $this->GetPermalink($like['TableName'],$like['TableID']);
					}
				}
				unset($Likes[$idl]['ID']);
				unset($Likes[$idl]['IDUser']);
				unset($Likes[$idl]['TableName']);
				unset($Likes[$idl]['TableID']);
				unset($Likes[$idl]['Options']);
				unset($Likes[$idl]['Vote']);
			}
			$Resultado['Success']=1;
			$Resultado['Result']['ItemCount']=$TotalLikes;
			$Resultado['Result']['Items']=$Likes;
		} else {
			$Resultado['Success']=0;
			$Resultado['Result']=$sql;		
		}
		return $Resultado;
	}

	function ws_sendlike($url,$type) {
		if (! $this->AccessTokenValid) {
			$Resultado=$this->AccessTokenResult;
			$Resultado['Result']['yes']=$this->GetLikes('+',$url);
			$Resultado['Result']['no']=$this->GetLikes('-',$url);
			return $Resultado;		
		}	
		$url=str_replace(siteprotocol . sitedomain, "", $url);
		$Resultado['Success']=$this->LikeThis($type,$url,false);
		$Resultado['Result']['yes']=$this->GetLikes('+',$url);
		$Resultado['Result']['no']=$this->GetLikes('-',$url);
		return $Resultado;
	}

	function ws_sendcomment() {
		$GLOBALS['Core']=$this;
		$XtraComments= new ExtraComments($this,0);
		$XtraComments->table = $_POST['System_module'];
		$result=$XtraComments->JQuery_post();
		$Resultado['Success']=intval($result);
	}

	
	function ws_oauthlink($consumerkey,$consumertoken) {
		$OAuth= new OAuth($_GET);
		$salida['URL']=$OAuth->OAuthGetURL($consumerkey,$consumertoken);
		return $salida;
	}
}

class ws_userlogin extends User {

	function wsLogin($username,$password,$deviceType="",$deviceID="") {
		//Comprobamos que haya un AccessToken
		if (! $this->AccessTokenValid) {
			$Resultado=$this->AccessTokenResult;
			return $Resultado;		
		}
		$result=$this->Login($username, $password, 1);
		if ($result==0) {
			$Resultado['Success']=0;
			$Resultado['Result']="Bad username or password";
		}
		if ($result==-1) {
			$Resultado['Success']=-1;
			$Resultado['Result']="Account not activate";
		}
		if ($result==1) {
			$Resultado['Success']=1;
			if (($deviceType!="") and ($deviceID!="")) {
				$this->RegisterUserDevice($this->userID,$deviceType,$deviceID);
			}
			$this->EditItem($this->userID);
			unset($this->Data['Action']);
			unset($this->Data['PassW']);
			unset($this->Data['Active']);
			unset($this->Data['CountPages']);
			unset($this->Data['CountPost']);
			unset($this->Data['CountComments']);
			unset($this->Data['ProfilePublic']);
			unset($this->Data['Template']);
			unset($this->Data['TokenPassword']);
			unset($this->Data['fb_uid']);
			unset($this->Data['fb_link']);
			unset($this->Data['fb_gender']);
			unset($this->Data['fb_updateenable']);
			unset($this->Data['twitter_uid']);
			unset($this->Data['twitter_link']);
			unset($this->Data['twitter_access_token']);
			unset($this->Data['twitter_access_token_secret']);
			$Resultado['Result']=$this->Data;
		}
		return $Resultado;
	}

	function wsRegisterDevice($userID,$deviceType,$deviceID) {
		//Comprobamos que haya un AccessToken
		if (! $this->AccessTokenValid) {
			$Resultado=$this->AccessTokenResult;
			return $Resultado;		
		}
		$result=$this->RegisterUserDevice($userID,$deviceType,$deviceID);
		if ($result) {
			$Resultado['Success']=1;
			$Resultado['Result']="Device has been added";
		} else {
			$Resultado['Success']=0;
			$Resultado['Result']="Insufficient parameters";
		}
		return $Resultado;
	}

	function wsUnregisterDevice($deviceID) {
		//Comprobamos que haya un AccessToken
		if (! $this->AccessTokenValid) {
			$Resultado=$this->AccessTokenResult;
			return $Resultado;		
		}
		$result=$this->UnregisterUserDevice($deviceID);
		if ($result) {
			$Resultado['Success']=1;
			$Resultado['Result']="Device has been deleted";
		} else {
			$Resultado['Success']=0;
			$Resultado['Result']="Insufficient parameters";
		}
		return $Resultado;
	}
}
?>