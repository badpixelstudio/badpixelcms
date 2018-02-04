<?php
require_once(sitepath . "include/core/core.class.php");
require_once(sitepath . "include/apps/apps.config.php");
require_once(sitepath . "include/apps/accesstokens.class.php");
require_once(sitepath . "include/users/oauth.class.php");
require_once(sitepath . "lib/images/thumbs.php");	

class MasterApps extends Core{
	
	//Inicializamos valores por defecto
	var $title = 'API Apps';
	var $module = 'apps';
	var $class= 'apps';
	var $table = 'api_apps';	
	var $typemodule='system';
	var $tables_required=array('api_apps');
	var $FieldsOfImages=array("Image"=>"ImageOptions");
	var $version="3.1.0.0";
	
	function __construct($values) {
		parent::__construct($values); 
		$title=$this->GetModuleName($this->class);
		if ($title!=$this->class) { $this->title=$title; }
		$this->conf = new ConfigApps($this->businessID);
		$this->BreadCrumb[$this->title]=$this->module;
	}
	
	
	function GetMyApp($token) {
		$sql="SELECT * FROM " . $this->table . " WHERE MD5(ID)='" . $token . "' AND IDUser='" .$this->userID . "'";
		$this->Data=parent::$db->GetDataRecordFromSQL($sql);
		if ($this->Data!==false) {
			return true;
		} else {
			return false;
		}
	}
	
	function PostMyApp($formulario,$url="") {
		if ($url!="") {
			//Obtenemos el archivo de la url
			if(is_array($_POST['Change_Image'])){
				CopyExternalResource($url . $formulario['Change_Image'][0],sitepath . "public/temp/" . $formulario['Change_Image'][0]);
				if (! is_file(sitepath . "public/temp/" . $formulario['Change_Image'][0])) { unset($formulario['Change_Image']); }
			}
		}
		$_POST=$formulario;
		$this->PostItem();
	}
	
	function ListAdmItems() {
		$this->GetItems("",false,"",$this->search,false);
		$this->PrepareTableList();
		if ($this->paged) {
			$this->LoadTemplate('list_paged.tpl.php');
		} else {
			$this->LoadTemplate('list.tpl.php');
		}
	}

	function MyApps() {
		$this->GetItems("IDUser=" . $this->userID,false,"","",false);
		return $this->ItemsCount;
	}

	function PostAdmItem($redirect=false) {
		$ActualID=$this->PostItem(false);
		if ($_POST['System_Action']=="new") { $this->GenerateConsumerKeys($ActualID); }
		header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module);
	}

	function DeleteAdmItem($id=0) {
		parent::$db->Qry("DELETE FROM oauth_accesstokens WHERE IDApp=" . $id);	
		echo intval($this->DeleteItem($id));
	}
	
	function GenerateConsumerKeys($id) {
		$Datos['System_ID']=$id;
		$Datos['System_Action']="edit";
		$valido=false;
		while (! $valido) {
			$Datos['Form_ConsumerKey']=md5($id) . '-' . KeyGen(22);	
			$sql="SELECT ID FROM " . $this->table . " WHERE ConsumerKey='" . $Datos['Form_ConsumerKey'] . "'";
			$valido=false;
			$datos=parent::$db->GetDataRecordFromSQL($sql);
			if ($datos===false) { $valido=true; }
		}
		$Datos['Form_ConsumerToken']=KeyGen(60);
		$ActualID = $this->PostToDatabase($this->table,$Datos);
	}

	function GetAccessToken($consumer_key,$consumer_token,$username="",$password="",$devicetype="",$deviceid="") {
		//Inicilizamos los valores de error por defecto.
		$Resultado['Success']=0;
		$Resultado['Status']="Application credentials invalid";
		$c_oauth=new OAuth($_GET);
		$sql="SELECT * FROM " . $this->table . " WHERE ConsumerKey='" . $consumer_key . "' AND ConsumerToken='" . $consumer_token . "'";
		$App=parent::$db->GetDataRecordFromSQL($sql);
		if ($App!==false) {
			if ($App['PermitGuest']==1) {
				$this->RegisterUserDevice(0,$devicetype,$deviceid);
				$Resultado['Success']=1;
				$Resultado['Status']="OK, use default AccessToken";
				$Resultado['AccessToken']=$c_oauth->AccessToken($App['ID'],0);
			}
			//Si llegaron datos de usuario comprobamos que se haga login con esas credenciales
			if ($App['AllowOAuthSign']==1) {
				//La app permite que se haga login con un usuario...
				$result = parent::$db->GetDataRecordFromSQL("SELECT * FROM users WHERE ((UserName = '".$username."') or (Email = '" . $username . "'))  AND (PassW = '".$password."')");	
				if ($result===false) {
					if ($App['PermitGuest']==0) {
						$Resultado['Status']="Bad username or password";
					} else {
						if (($username!="") or ($password!="")) {
							$Resultado['Status']="Bad username or password";
						}
					}
					return $Resultado;
				}
		
				if ($result['Active']==0) {
					$Resultado['Status']="User account disabled";
					return $Resultado;
				}
				if ($result['UserName'] !="" && $result['PassW'] !="") {
					if ($result['RegCode']=="") { 
						//Regeneramos el regcode
						$result['RegCode']=$result['UserName'] . KeyGen(30);
						$sql ="UPDATE users SET RegCode='" . $result['RegCode'] ."' WHERE ID=" . $result['ID'];
						parent::$db->Qry($sql);
					}
					if ((isset($result['IDDevice'])) and ($deviceid!="")) {
						$sql="UPDATE users SET IDDevice='" . $deviceid . "' WHERE ID=" . $result['ID']; 
						parent::$db->Qry($sql);
					}
					$_SESSION['userid'] = $result['ID'];
					$_SESSION['username'] = $result['UserName'];
					$_SESSION['userlevel'] = $result['Rol'];
					$_SESSION['regcode']= $result['RegCode'];						
					$c_oauth=new OAuth($_GET);
					$Resultado['Success']=2;
					$Resultado['Status']="OK, user credentials accepted.";
					$Resultado['AccessToken']=$c_oauth->AccessToken($App['ID'],$result['ID']);
					$this->RegisterUserDevice($result['ID'],$devicetype,$deviceid);
					return $Resultado;
				}
				$Resultado['Status']="Bad username or password";
			}
		}
		return $Resultado;
	}

	function PrepareTableList() {
		$this->AddMainMenu('Crear',$this->module . '/new');
		$this->AddTableRowClass('warning','{{Enabled}}==0');
		$this->AddTableContent('Aplicación','data','{{Name}}','',$this->module . '/edit/id/{{ID}}');
		$this->AddTableContent('Web','data','{{Web}}','',$this->module . '/edit/id/{{ID}}');
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block,'Access Tokens',$this->module . '/accesstokens_list/idparent/{{ID}}');
		$this->AddTableOperations($in_block);
		$this->AddTableOperations($in_block,'Generar ConsumerKey',$this->module . '/gen_consumer/id/{{ID}}');
		$this->AddTableOperations($in_block);
		$this->AddTableOperations($in_block,'Editar',$this->module . '/edit/id/{{ID}}');
		$this->AddTableOperations($in_block,'Eliminar',$this->module . '/delete/id/{{ID}}');
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('Contenido');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Nombre de la Aplicación","FieldName":"Form_Name","Value":"' . $this->Data['Name'] . '","Required": true}');
		$this->AddFormContent($in_block,'{"Type":"textarea","Text":"Descripción","FieldName":"Form_Description","Value":"' . $this->Data['Description'] . '","Required": true}');
		$this->AddFormContent($in_block,'{"Type":"url","Text":"Web de la Aplicación","FieldName":"Form_Web","Value":"' . $this->Data['Web'] . '"}');
		if($this->Check('UseCallbackURL')){ $this->AddFormContent($in_block,'{"Type":"url","Text":"URL de regreso oAuth","FieldName":"Form_CallbackURL","Value":"' . $this->Data['CallbackURL'] . '"}');}
		if($this->Check('UseAppImage')){ $this->AddFormContent($in_block,'{"Type":"upload","Text":"Imagen de la Aplicación","FieldName":"Form_Image","Value":"' . $this->Data['Image'] . '","UploadType": "image", "UploadItem":"first", "Extensions": "jpg,jpeg,gif,png", "PreviewFolder": "public/thumbnails", "External":"' . $this->Data['RenameImage'] . '"}'); }
		if($this->Check('UseOrganizationData')){
			$this->AddFormContent($in_block,'{"Type":"text","Text":"Nombre de la Organización","FieldName":"Form_OrganizationName","Value":"' . $this->Data['OrganizationName'] . '"}');
			$this->AddFormContent($in_block,'{"Type":"url","Text":"Web de la Organización","FieldName":"Form_OrganizationWeb","Value":"' . $this->Data['OrganizationWeb'] . '"}');
		}
		$in_block=$this->AddFormBlock('Permisos');
		if($this->Check('UsePermissions')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Permisos de la Aplicación","FieldName":"Form_Permissions","Value":"' . $this->Data['Permissions'] . '"}'); }
		$this->AddFormContent($in_block,'{"Type":"text","Text":"ConsumerKey","FieldName":"Form_ConsumerKey","Value":"' . $this->Data['ConsumerKey'] . '","ReadOnly": true, "Help": "Identifica la aplicación en el sistema"}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"ConsumerToken","FieldName":"Form_ConsumerToken","Value":"' . $this->Data['ConsumerToken'] . '","ReadOnly": true, "Help": "Valida la aplicación en el sistema"}');
		$this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Aplicación activa","FieldName":"Form_Enabled","Value":"' . $this->Data['Enabled'] . '"}');
		$this->AddFormContent($in_block,'{"Type":"checkbox","Text":"OAuth Login permitido","FieldName":"Form_AllowOAuthSign","Value":"' . $this->Data['AllowOAuthSign'] . '"}');
		$this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Permitir tokens sin usuario vinculado","FieldName":"Form_PermitGuest","Value":"' . $this->Data['PermitGuest'] . '"}');
		if($this->Check('EnableUserLink')){ $this->AddFormContent($in_block,'{"Type":"combo","Text":"Usuario","FieldName":"Form_IDUser","Value":"' . $this->Data['IDUser'] . '", "ListTable": "users", "ListValue": "ID", "ListOption": "UserName", "ListOrder":"UserName", "NullValue": "0", "Help":"La aplicación actuará en nombre del usuario seleccionado"}'); }
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
	}

	function RunAccessTokens($action) {
		$action=str_replace("accesstokens_", "", $this->action);
		$this->Xtra= new AccessTokens($this->_values);
		$this->Xtra->action=$action;
		$this->Xtra->RunAction();
	}

	function RunAction() {
		parent::RunAction();
		if ($this->action=="gen_consumer") { 
			$this->GenerateConsumerKeys($this->id);
			$this->EditAdmItem();
		}	
		if (strpos($this->action, "accesstokens_")!==false) { $this->RunAccessTokens($this->action); }
	}
	
	
	function __destruct(){

	}

}
?>