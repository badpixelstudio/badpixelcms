<?php
require_once(sitepath . "include/core/core.class.php");
$fcm_loaded=false;
if (is_file(sitepath . "lib/push/firebasecm.php")) {
	require_once(sitepath . "lib/push/firebasecm.php");
	$fcm_loaded=true;
}

class MasterFCMPush extends Core{
	
	var $ModuleVersion='1.0.0.0';
	
	//Inicializamos valores por defecto
	var $title = 'Notificaciones Push';
	var $class = 'fcmpush';
	var $module = 'core';

	
	//constructor
	function __construct($values) {
		parent::__construct($values);  
		$title=$this->GetModuleName($this->class);
		if ($title!=$this->class) { $this->title=$title; }
		//breadcrumb
		$this->BreadCrumb[$this->title]=$this->module . "--" . $this->class;
	}

	function sendNotification($devices,$message,$title="", $sound="default", $priority="high",$array_send=null) {
		if (siteFirebaseAPIKey=="") { return "Error: Firebase API Key not found!"; }
		if (! $GLOBALS['fcm_loaded']) { return "Error: FCM lib not found!"; }
		$FCM=new FirebaseCMPush();
		$resultado="Error: No destinations added!";
		if (count($devices)) { 
			$resultado="";
			$total=count($devices);
			$pos=0;
			while ($pos<$total) {
				unset($enviar);
				$contar=0;
				while (($contar<900) and ($pos<$total)) {
					$enviar[]=$devices[$contar];
					$pos++;
					$contar++;
				}
				if (count($enviar)>0) {
					$FCM->set_devices($enviar);
					$resultado.=$FCM->send($message,$title,$sound,$priority,$array_send);
					$resultado.="<br>";
				}
			}
		}
		return $resultado;
	}

	function ListAdmItems() {
		$this->PrepareForm();
		$this->LoadTemplate('edit.tpl.php');
	}

	function PostAdmItem($redirect=true) {
		$data=null;
		if ($_POST['Data_Title']!="") {$data['title']=$_POST['Data_Title']; }
		if ($_POST['Data_Message']!="") {$data['text']=$_POST['Data_Message']; }
		if ($_POST['Data_Action']!="") {$data['action']=$_POST['Data_Action']; }
		if ($_POST['Data_ID']!="") {$data['id']=$_POST['Data_ID']; }
		$result=$this->sendNotification(array($_POST['Form_IDDevice']),$_POST['Form_Message'],$_POST['Form_Title'],$_POST['Form_Sound'],$_POST['Form_Priority'],$data);
		$redirect=siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "--" . $this->class . "/list";
		if ($result!="") {
			$redirect.="/text/" . urlencode(base64_encode($result));
		}
		header("Location: " . $redirect);
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('Android');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"ID FCM de dispositivo","FieldName":"Form_IDDevice","Value":"", "required":"required"}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Título (en blanco no se envia la notificación, pero si los datos)","FieldName":"Form_Title","Value":""}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Mensaje","FieldName":"Form_Message","Value":""}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Sonido","FieldName":"Form_Sound","Value":"default"}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Prioridad","FieldName":"Form_Priority","Value":"high"}');
		$this->AddFormContent($in_block,'{"Type":"group", "Text": "Datos adjuntos","FieldName": "DataPush"}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Título","FieldName":"Data_Title","Value":""}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Mensaje","FieldName":"Data_Message","Value":""}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Opción 1: Acción","FieldName":"Data_Action","Value":""}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Opción 2: ID","FieldName":"Data_ID","Value":""}');
		$this->TemplatePostScript=siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "--" . $this->class . "/post";
	}
} ?>