<?php
require_once(sitepath . "include/core/core.class.php");
require_once(sitepath . "include/apps/apps.config.php");
require_once(sitepath . "include/users/oauth.class.php");

class MasterAccessTokens extends Core{
	
	//Inicializamos valores por defecto
	var $title = 'Access Tokens';
	var $module = 'apps';
	var $class = 'accesstokens';
	var $typemodule='system';
	var $table = 'oauth_accesstokens';
	var $tablefather = 'api_apps';	
	var $version=false;
	
	function __construct($values) {
		parent::__construct($values); 
		$title=$this->GetModuleName($this->class);
		if ($title!=$this->class) { $this->title=$title; }
		$this->conf = new ConfigApps($this->businessID);
		$this->Father=parent::$db->GetDataRecord($this->tablefather,$this->idparent);
		if ($this->Father!==false) {
			$this->title=$this->Father['Name'];
		} else {
			$this->Father['ID']=0;
			$this->Father['Name']="Todos los elementos";
		}
		$this->BreadCrumb[$this->GetModuleName($this->module)] = $_SERVER['PHP_SELF'];
		$this->BreadCrumb[$this->Father['Name']] = $this->module . "/accesstokens_list/idparent/" . $this->idparent;	
	}

	function GetItemsAddData(&$data) {
		if ($data['UserName']=="") { $data['UserName']="[Sin Definir]"; }
	}
	
	function ListAdmItems() {
		$select="SELECT " . $this->table . ".*, users.UserName AS UserName FROM " . $this->table . " LEFT JOIN users ON users.ID=" . $this->table . ".IDUser WHERE " . $this->table . ".ID IS NOT NULL";
		$this->GetItems("IDApp=" . $this->idparent,false,"",$this->search,false,$select);
		$this->PrepareTableList();
		if ($this->paged) {
			$this->LoadTemplate('list_paged.tpl.php');
		} else {
			$this->LoadTemplate('list.tpl.php');
		}
	}

	function NewAdmItem() {
		$values['IDApp']=$this->idparent;
		$tiempo=siteOAuthAccessTokenExpires;
		$values['Expires']=date('Y-m-d H:i:s', time()+$tiempo); 
		$this->NewItem($values);
		$this->TotalLanguages=0;	
		$this->PrepareForm();
		$this->LoadTemplate('edit.tpl.php');
	}

	function PostAdmItem($redirect=false) {
		$tiempo=siteOAuthAccessTokenExpires;
		if ($_POST['Form_LongLife']==1) { $tiempo=siteOAuthExtendedAccessTokenExpires; }
		$_POST['Form_Expires']=date('Y-m-d H:i:s', time()+$tiempo); 
		$ActualID=$this->PostItem(false);
		if ($_POST['System_Action']=="new") { $_POST['Form_AccessToken']=$this->GenerateAccessToken($_POST['Form_IDApp'],$_POST['Form_IDUser']); }
		header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "/" . $this->class . "_list/idparent/" . $_POST['Form_IDApp'] . "/text/" . urlencode(base64_encode("AccessCode: " . $_POST['Form_AccessToken'])));
	}
	
	function GenerateAccessToken($AppID,$UserID) {
		$c_oauth=new OAuth($_GET);
		return $c_oauth->AccessToken($AppID,$UserID);
	}
	
	function RegenerateAccessToken() {
		parent::$db->LoadFormData($this,$this->id);	
		$_POST['Form_AccessToken']=$this->GenerateAccessToken($this->Data['IDApp'],$this->Data['IDUser']);
		header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "/" . $this->class . "_list/idparent/" . $this->Data['IDApp'] . "/text/" . urlencode(base64_encode("AccessCode: " . $_POST['Form_AccessToken'])));	
	}

	function PrepareTableList() {
		$this->AddMainMenu('Crear',$this->module . '/' . $this->class . '_new/idparent/' . $this->idparent);
		$this->AddTableContent('Usuario','data','{{UserName}}','',$this->module . '/' . $this->class . '_edit/idparent/' . $this->idparent . '/id/{{ID}}');
		$this->AddTableContent('Valido hasta','data','{{Expires}}','',$this->module . '/' . $this->class . '_edit/idparent/' . $this->idparent . '/id/{{ID}}');
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block,'Generar claves nuevas',$this->module . '/' . $this->class . '_regenerate/idparent/' . $this->idparent . '/id/{{ID}}');
		$this->AddTableOperations($in_block);
		$this->AddTableOperations($in_block,'Editar',$this->module . '/' . $this->class . '_edit/idparent/' . $this->idparent . '/id/{{ID}}');
		$this->AddTableOperations($in_block,'Eliminar',$this->module . '/' . $this->class . '_delete/idparent/' . $this->idparent . '/id/{{ID}}');
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('Acceso');
		$this->AddFormContent($in_block,'{"Type":"combo","Text":"Usuario","FieldName":"Form_IDUser","Value":"' . $this->Data['IDUser'] . '", "ListTable": "users", "ListValue": "ID", "ListOption": "UserName", "ListOrder":"UserName", "NullValue": "0", "Help":"La aplicación actuará en nombre del usuario seleccionado"}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"AccessToken","FieldName":"Form_AccessToken","Value":"' . $this->Data['AccessToken'] . '"}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Caducidad","FieldName":"Form_Expires","Value":"' . $this->Data['Expires'] . '"}');
		$this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Extender validez del AccessToken","FieldName":"Form_LongLife","Value":"' . $this->Data['LongLife'] . '", "Help": "La sesión tendrá una validez mayor a la habitual"}');	
		$this->AddFormHiddenContent("Form_IDApp",$this->Data['IDApp']);
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->TemplatePostScript=siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "/" . $this->class . "_post";
	}

	function RunAction() {
		parent::RunAction();
		if ($this->action=="regenerate") { $this->RegenerateAccessToken();	}
	}
	
	function __destruct(){

	}

}
?>