<?php
require_once(sitepath . "include/core/core.class.php");
require_once(sitepath . "include/htmleditor/htmleditor.config.php");

class MasterHTMLEditor extends Core{
	
	//Inicializamos parametros por defecto
	var $title = 'Editor HTML';
	var $class = 'htmleditor';
	var $module = 'htmleditor';
	var $typemodule='appearance';
	var $InstallAdminMenu=array(array('Block' => 'appareance', 'Icon' => 'fa-dashboard'));
	var $action = 'list';
	var $file = '';
	var $folder = '';
	var	$showtitle = 'Editor HTML';
	var $tables_required=false;
	var $conf = null;
	var $version=false;	

	function __construct($values) {
		parent::__construct($values);  
		$title=$this->GetModuleName($this->class);
		if ($title!=$this->class) { $this->title=$title; }
		$this->conf = new ConfigHTMLEditor();
		$this->folder=sitepath . $this->conf->Export('DefaultFolder');
		if (isset($values['action'])) { $this->action=$values['action']; }
		if (isset($values['file'])) { $this->file=urldecode($values['file']); }
		if (isset($values['folder'])) { $this->folder=urldecode(base64_decode($values['folder'])); }
		
		$this->BreadCrumb[$this->title] = $this->module;
	}

	
	function ReadDir() {
		$contador=0;
		if (! is_dir($this->folder)) {
			mkdir($this->folder);
			chmod($this->folder, 0777);
		}
		$directorio=opendir($carpeta=$this->folder);	  
		$Elementos=array(); 
		while ($archivo = readdir($directorio)) {
			if (is_file($carpeta . '/' . $archivo)) {
				$Elementos[$contador+1]['File']=$archivo;
				$contador++;
			}
		}
		$this->Items=array();
		$this->ItemsCount=count($Elementos);
		if ($this->ItemsCount>0) { $this->Items=$Elementos; }
		closedir($directorio);
		return $contador;	
	}

	function ListAdmItems() {
		$this->ReadDir();
		$this->PrepareTableList();
		if ($this->paged) {
			$this->LoadTemplate('list_paged.tpl.php');
		} else {
			$this->LoadTemplate('list.tpl.php');
		}
	}

	function EditHtml($file) {
		$this->Data['FileName']=$file;
		$this->Data['ActualPerms']=@fileperms($this->folder . '/' .$file);
		$fp = fopen($this->folder . '/' .$file,"r");
		$this->Data['FileContent']=@file_get_contents($this->folder . '/' .$file);
	}
	
	function PostHtml() {
		$folder=$_POST['Folder'];
		$file=$_POST['FileName'];
		$file=$folder . '/' . $file;
		$contenido=stripslashes($_POST['FileContent']);
		$permisos=$_POST['ActualPerms'];
		try {
			file_put_contents($file,$contenido);
			chmod($file,$permisos);
			return true;
		} catch(Exception $e) {
			return false;
		}
	}
	
	function NewAdmItem() {
		$this->BreadCrumb['Archivos'] = $this->module;		
		$this->BreadCrumb['Nuevo'] = "";	
		$this->Data['FileName']=$this->file;
		$this->Data['ActualPerms']='';
		$this->Data['FileContent']='';
		$this->Data['Action']="new";
		$this->PrepareForm();
		$this->LoadTemplate('edit.tpl.php');
	}

	function EditAdmItem($id="") {
		$this->BreadCrumb['Archivos'] = $this->module;
		$this->BreadCrumb[$this->file] = '';			
		$this->EditHtml($this->file);
		$this->Data['Action']="edit";
		$this->PrepareForm();
		$this->LoadTemplate('edit.tpl.php');
	}
	
	function PostAdmItem($redirect=true) {
		$resultado=$this->PostHtml($_POST);
		$error="";
		if (! $resultado) { $error="/error/" . urlencode(base64_encode("No se ha podido guardar el documento")); }
		header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/htmleditor" . $error);		
	}

	function DeleteAdmItem($id=0) {
		$file=$this->folder . '/' . $this->file;
		DeleteFile($file);
		echo "1";
	}

	function PrepareTableList() {
		$this->AddMainMenu('Crear',$this->module . '/new/folder/' . urlencode(base64_encode($this->folder)));
		$this->AddTableContent('Archivo','data','{{File}}','',$this->module . '/edit/folder/' . urlencode(base64_encode($this->folder)) . '/file/{{File}}');
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block,'Editar',$this->module . '/edit/folder/' . urlencode(base64_encode($this->folder)) . '/file/{{File}}');
		$this->AddTableOperations($in_block,'Eliminar',$this->module . '/delete/folder/' . urlencode(base64_encode($this->folder)) . '/file/{{File}}');
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('General');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Nombre del archivo","FieldName":"FileName","Value":"' . $this->Data['FileName'] . '","Required": true}');
		$this->AddFormContent($in_block,'{"Type":"textarea","Text":"Contenido del archivo","FieldName":"FileContent","Value":"' . addcslashes($this->Data['FileContent'],'\\"') . '","Required": true}');
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("ActualPerms",$this->Data['ActualPerms']);
		$this->AddFormHiddenContent("Folder",$this->folder);
	}
}

?>