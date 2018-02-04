<?php
require_once(sitepath . "include/core/core.class.php");
require_once(sitepath . "include/contact/contact.config.php");

class MasterContact extends Core{
	var $title = 'Contacto';
	var $module = 'contact';
	var $class = 'contact';
	var $table = 'contact';
	var $typemodule='tools';
	var $tables_required=array('contact');
	var $permalink_conf=false;
	var $version="4.3.0.0";

	function __construct($values) {
		parent::__construct($values);
		$this->conf = new ConfigContact($this->businessID);
		$this->BreadCrumb[$this->title]=$this->module;
	}

	function CheckReCaptcha($response) {
		$request['secret']=$this->conf->Export("ReCaptchaSecret");
		$request['response']=$response;
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$request);
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION,false);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		$result = curl_exec($ch);
		curl_close($ch);
		echo $result;
	}
	
	function SendForm() {
		if ($this->conf->Check('UseReCaptcha')) {
			if (! isset($_POST['g-recaptcha-valided'])) return -2;
		}
		$email=siteMainMail;
		$remite=siteMainMail;
		if (isset($_POST['Email'])) { 
			$remite=$_POST['Email']; 
			if (filter_var($remite, FILTER_VALIDATE_EMAIL)===false) { return "-1";  }
		}
		$asunto="Solicitud de contacto recibida desde " . siteTitle;
		if (isset($_POST['Subject'])) { $asunto=$_POST['Subject']; }	
		if (isset($_POST['subject'])) { $asunto=$_POST['subject']; }	
		if (isset($_POST['Asunto'])) { $asunto=$_POST['Asunto']; }	
		if (isset($_POST['asunto'])) { $asunto=$_POST['asunto']; }	
		$cuerpo="<hr /><strong>" . $asunto . "</strong><br />";
		foreach($_POST as $id=>$texto) {
			if (strpos($id, "g-recaptcha")===false) {
				$cuerpo.="<strong>" . str_replace("_"," ",$id) . ":</strong> " . stripslashes(($texto)) . "<br />";	
			}
		}
		if ($this->conf->Check("SaveInDatabase")) {
			$Data['System_Action']="new";
			$Data['System_ID']=-1;
			$Data['Form_Name']="";
			$Data['Form_Email']="";
			$Data['Form_Phone']="";
			$Data['Form_Subject']=$asunto;
			$Data['Form_Message']=$cuerpo;
			$Data['Form_BodyEmail']=$cuerpo;
			$Data['Form_DatePublish']=date("Y-m-d H:i:s");
			if (isset($_POST['Name'])) { $Data['Form_Name']=$_POST['Name']; }
			if (isset($_POST['name'])) { $Data['Form_Name']=$_POST['name']; }
			if (isset($_POST['Nombre'])) { $Data['Form_Name']=$_POST['Nombre']; }
			if (isset($_POST['nombre'])) { $Data['Form_Name']=$_POST['nombre']; }
			if (isset($_POST['Email'])) { $Data['Form_Email']=$_POST['Email']; }
			if (isset($_POST['email'])) { $Data['Form_Email']=$_POST['email']; }
			if (isset($_POST['Mail'])) { $Data['Form_Email']=$_POST['Mail']; }
			if (isset($_POST['mail'])) { $Data['Form_Email']=$_POST['mail']; }
			if (isset($_POST['Phone'])) { $Data['Form_Phone']=$_POST['Phone']; }
			if (isset($_POST['phone'])) { $Data['Form_Phone']=$_POST['phone']; }
			if (isset($_POST['Telephone'])) { $Data['Form_Phone']=$_POST['Telephone']; }
			if (isset($_POST['telephone'])) { $Data['Form_Phone']=$_POST['telephone']; }
			if (isset($_POST['Telefono'])) { $Data['Form_Phone']=$_POST['Telefono']; }
			if (isset($_POST['telefono'])) { $Data['Form_Phone']=$_POST['telefono']; }
			if (isset($_POST['Message'])) { $Data['Form_Message']=$_POST['Message']; }
			if (isset($_POST['message'])) { $Data['Form_Message']=$_POST['message']; }
			if (isset($_POST['Comments'])) { $Data['Form_Message']=$_POST['Comments']; }
			if (isset($_POST['comments'])) { $Data['Form_Message']=$_POST['comments']; }
			if (isset($_POST['Mensaje'])) { $Data['Form_Message']=$_POST['Mensaje']; }
			if (isset($_POST['mensaje'])) { $Data['Form_message']=$_POST['mensaje']; }
			if (isset($_POST['Comentarios'])) { $Data['Form_Message']=$_POST['Comentarios']; }
			if (isset($_POST['comentarios'])) { $Data['Form_Message']=$_POST['comentarios']; }
			parent::$db->PostToDatabase($this->table,$Data);
		}
		//enviamos el correo...
		$realizarenvio=SendMail($_POST['Nombre'], $email , $asunto, $cuerpo, $remite, 1);
		return $realizarenvio;			
	}

	function ListAdmItems() {
		if (! $this->conf->Check("SaveInDatabase")) {
			header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/home/error/" . urlencode(base64_encode(_("El módulo de contacto no tiene habilitado el almacenado en base de datos"))));
			exit;
		}
		$this->GetItems("",false,"",$this->search,false);
		$this->PrepareTableList();
		if ($this->paged) {
			$this->LoadTemplate('list_paged.tpl.php');
		} else {
			$this->LoadTemplate('list.tpl.php');
		}
	}

	function PrepareTableList() {
		$this->AddMainMenu('Exportar',$this->module . '/export');
		$this->AddTableContent('','data','','==(999999999-{{ID}})');
		$this->AddTableContent('Nombre','data','{{Name}}','',$this->module . '/edit/id/{{ID}}');
		$this->AddTableContent('Email','data','{{Email}}','',$this->module . '/edit/id/{{ID}}');
		$this->AddTableContent('Asunto','data','{{Subject}}','',$this->module . '/edit/id/{{ID}}');
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block,'Editar',$this->module . '/edit/id/{{ID}}');
		$this->AddTableOperations($in_block,'Eliminar',$this->module . '/delete/id/{{ID}}');
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('General');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Nombre","FieldName":"Form_Name","Value":"' . addcslashes($this->Data['Name'],'\\"') . '","Required": true}');
		$this->AddFormContent($in_block,'{"Type":"email","Text":"Email","FieldName":"Form_Email","Value":"' . addcslashes($this->Data['Email'],'\\"') . '","Required": true}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Teléfono","FieldName":"Form_Phone","Value":"' . addcslashes($this->Data['Phone'],'\\"') . '"}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Asunto","FieldName":"Form_Subject","Value":"' . addcslashes($this->Data['Subject'],'\\"') . '","Required": true}');
		$this->AddFormContent($in_block,'{"Type":"textarea","Text":"Mensaje","FieldName":"Form_Message","Value":"' . addcslashes($this->Data['Message'],'\\"') . '"}');
		$this->AddFormContent($in_block,'{"Type":"html","Text":"Email remitido","FieldName":"Form_BodyEmail","Value":"' . addcslashes($this->Data['BodyEmail'],'\\"') . '"}'); 
		$this->AddFormContent($in_block,'{"Type":"datetime","Text":"Fecha y hora de recepción","FieldName":"Form_DatePublish","Value":"' . addcslashes($this->Data['DatePublish'],'\\"') . '"}');
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
	}

	function RunAction() {
		if($this->action=="export") { 
			$this->GetItems();
			parent::$db->ExportToExcel($this);
			exit;
		}
		parent::RunAction();
	}

}
?>