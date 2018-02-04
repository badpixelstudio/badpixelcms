<?php
require_once(sitepath . "include/core/core.class.php");
require_once(sitepath . "include/business/business.config.php");
require_once(sitepath . "lib/images/thumbs.php");	

class MasterbUsers extends Core{
	var $title = 'Gestores de Empresa';
	var $class = 'users';
	var $module = 'business';
	var $table = 'business_users';
	var $version = false;
	
	function __construct($values) {
		parent::__construct($values);  
		$this->conf = new ConfigBusiness();
		$this->BreadCrumb['Empresas']=$this->module;
		$Empresa=parent::$db->GetDataRecord("business",$this->id);
		$this->BreadCrumb[$Empresa['Name']]=$this->module . "/edit/id/" . $this->id;	
		$this->BreadCrumb['Gestores']=$this->module . "/users_list/id/" . $this->id;	
		if ($this->id!=0) { $this->CheckItemBusinessPermission($this->id, true); }
	}

	function ListAdmItems() {
		$select="SELECT business_users.*,users.UserName as UserName,users.Email as Email FROM business_users INNER JOIN users ON users.ID=business_users.IDUser";
		$cond="business_users.IDBusiness=" . $this->id;
		$this->GetItems($cond,false,"business_users.Rol,business_users.id",$this->search,false,$select);
		$this->PrepareTableList();
		if ($this->paged) {
			$this->LoadTemplate('list_paged.tpl.php');
		} else {
			$this->LoadTemplate('list.tpl.php');
		}
	}
	
	function NewAdmItem() {
		$values['IDBusiness']=$this->id;
		$values['Email']="";
		$this->NewItem($values);
		$this->TotalLanguages=0;	
		$this->PrepareForm();
		$this->LoadTemplate('edit.tpl.php');
	}

	function EditAdmItem($id="") {
		$valid=$this->EditItem($id);
		if (! $valid) { 
			$return=siteprotocol . sitedomain . sitePanelFolder ."/home";
			if(isset($_SERVER['HTTP_REFERER'])){ $return=$_SERVER['HTTP_REFERER'];}
			$return.="/error/" . urlencode(base64_encode("El elemento especificado no existe"));
			header("Location: " . $return); exit;
		}
		$this->Data['Email']=parent::$db->GetDataFieldFromSQL($sql,'Email');
		$this->PrepareLangMenu(true);
		$this->PrepareForm();
		$this->LoadTemplate('edit.tpl.php');
	}

	function PostAdmItem($redirect=true) {
		$ActualID=$_POST['Form_IDBusiness'];
		//comprobamos si hay que crear usuario o no...
		if (isset($_POST['User_AdminID'])) {
			if ($_POST['User_AdminID']==0) {
				//Creamos un nuevo usuario...
				$Datos['System_ID']=-1;
				$Datos['System_Action']="new";
				$Datos['Form_UserName']=$_POST['User_AdminUserName'];
				$Datos['Form_RegCode']=$_POST['User_AdminUserName'] . KeyGen(30);
				$Datos['Form_Email']=$_POST['User_AdminEmail'];
				$Datos['Form_UserName']=$_POST['User_AdminUserName'];
				$Datos['Form_Passw']=md5($_POST['User_AdminPassword']);
				$Datos['Form_Active']=1;
				$Datos['Form_Rol']=1;
				$Datos['Form_DateInscribe']=date('Y-m-d H:i:s');
				$Datos['Form_LastLogin']=date('Y-m-d H:i:s');
				$Datos['Form_LastIP']=getenv("REMOTE_ADDR") . "(" . getenv("HTTP_X_FORWARDED_FOR") . ")";	
				$ActualUser=$this->PostToDatabase("users",$Datos);
			} else {
				$ActualUser=$_POST['User_AdminID'];
			}
		}	
		//Creamos el usuario...
		$_POST['System_ID']=$_POST['System_ID'];
		$_POST['System_Action']=$_POST['System_Action'];
		$_POST['Form_IDBusiness']=$ActualID;
		$_POST['Form_IDUser']=$ActualUser;
		$_POST['Form_Rol']=$_POST['Form_Rol'];
		$this->PostItem(siteprotocol . sitedomain . sitePanelFolder . "/business/users_list/id/" . $ActualID);
	}
	
	function OldPostItem() {
		$ActualID=$_POST['Form_IDBusiness'];
		//comprobamos si hay que crear usuario o no...
		if (isset($_POST['User_AdminID'])) {
			if ($_POST['User_AdminID']==0) {
				//Creamos un nuevo usuario...
				$Datos['System_ID']=-1;
				$Datos['System_Action']="new";
				$Datos['Form_UserName']=$_POST['User_AdminUserName'];
				$Datos['Form_RegCode']=$_POST['User_AdminUserName'] . KeyGen(30);
				$Datos['Form_Email']=$_POST['User_AdminEmail'];
				$Datos['Form_UserName']=$_POST['User_AdminUserName'];
				$Datos['Form_Passw']=md5($_POST['User_AdminPassword']);
				$Datos['Form_Active']=1;
				$Datos['Form_Rol']=1;
				$Datos['Form_DateInscribe']=date('Y-m-d H:i:s');
				$Datos['Form_LastLogin']=date('Y-m-d H:i:s');
				$Datos['Form_LastIP']=getenv("REMOTE_ADDR") . "(" . getenv("HTTP_X_FORWARDED_FOR") . ")";	
				$ActualUser=$this->PostToDatabase("users",$Datos);
			} else {
				$ActualUser=$_POST['User_AdminID'];
			}
			//Creamos el usuario...
			$NewAdmin['System_ID']=$_POST['System_ID'];
			$NewAdmin['System_Action']=$_POST['System_Action'];
			$NewAdmin['Form_IDBusiness']=$ActualID;
			$NewAdmin['Form_IDUser']=$ActualUser;
			$NewAdmin['Form_Rol']=$_POST['Form_Rol'];
			$this->PostToDatabase($this->table,$NewAdmin);
		}	
		header("Location: " . $this->module . "/users_list/id/" . $_POST['Form_IDBusiness']);			
	}

	function PrepareTableList() {
		$this->AddMainMenu('Crear',$this->module . '/users_new/id/' . $this->id);
		$this->AddTableContent('Usuario','data','{{UserName}}');
		$this->AddTableContent('Email','data','{{Email}}');
		if ($this->Check('UseRoles')) { $this->AddTableContent('Rol','data','{{RolName}}'); }
		$in_block=$this->AddTableContent('Operaciones','menu');
		if ($this->Check('UseRoles')) { $this->AddTableOperations($in_block,'Editar',$this->module . '/users_edit/id/{{ID}}'); }
		$this->AddTableOperations($in_block,'Eliminar',$this->module . '/users_delete/id/{{ID}}');
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('Añadir administrador');
		$this->AddFormContent($in_block,'{"Type":"combo","Text":"Administrador","FieldName":"User_AdminID","Value":"' . $this->userID . '", "ListTable": "users", "ListValue": "ID", "ListOption": "UserName", "ListOrder":"UserName"}');
		if ($this->Check('UseRoles')) { $this->AddFormContent($in_block,'{"Type":"combo","Text":"Rol de usuario","FieldName":"Form_Rol","Value":"' . $this->Data['Rol'] . '", "ListTable": "users_roles", "ListValue": "IDRol", "ListOption": "RolName", "ListOrder":"IDRol"}'); }
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->AddFormHiddenContent("Form_IDBusiness",$this->Data['IDBusiness']);
		$this->TemplatePostScript=$this->module . "/users_post";
	}
}
?>