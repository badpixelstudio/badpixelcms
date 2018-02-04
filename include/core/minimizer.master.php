<?php
require_once(sitepath . "include/core/core.class.php");
require_once(sitepath . "lib/minimizer/functions.php");

class MasterUtil_Minimizer extends Core{
	var $typemodule='tools';
	var $InstallAdminMenu=array(array('Block' => 'tools', 'Icon' => 'fa-french'));
	var $version=false;	
	
	//Inicializamos valores por defecto
	var $title = 'Minimizador de Archivos';
	var $class = 'minimizer';
	var $module = 'core';
	var $table = '';	

	
	//constructor
	function __construct($values) {
		parent::__construct($values);  
		$title=$this->GetModuleName($this->class);
		if ($title!=$this->class) { $this->title=$title; }
		//breadcrumb
		$this->BreadCrumb[$this->title]=$this->module . "--" . $this->class;
	}

	function ListItems() {
		$this->ItemsCount=2;
		$this->Items[]=array('ID' => 'css', 'Name' => "Minificar CSS");
		$this->Items[]=array('ID' => 'js', 'Name' => "Minificar JS");
		$this->PrepareTableList();
		$this->LoadTemplate($this->module . '_list.tpl.php');		
	}
	
	function StartCSS() {
		$this->BreadCrumb['Minificar CSS']='';
		$in_block=$this->AddFormBlock('CSS');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Nombre de la plantilla","FieldName":"Form_template","Value":"' . addslashes($this->template) . '", "Help":"Nombre de la carpeta de la plantilla en \templates\"", "Required": true}');
		$this->AddFormContent($in_block,'{"Type":"combo-json","Text":"Origen de los datos","FieldName":"Form_MinimizerOrigin","Value":"template", "JsonValues": {"template": "CSS en una carpeta del template", "upload": "Otros archivos a cargar"}}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Carpeta donde se encuentran los CSS originales","FieldName":"Form_folder","Value":"css", "Help":"Todos los CSS tienen que estar en la misma carpeta", "Required": true}');
		$this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Sobreescribir la ruta de los paramétros url()","FieldName":"Form_ChangeURL","Value":"1"}');
		$this->AddFormContent($in_block,'{"Type":"upload-multiple","Text":"Cargar archivos a comprimir","FieldName":"uploads","Extensions":"css"}');
		$this->AddFormContent($in_block,'{"Type":"combo-json","Text":"Destino del proceso","FieldName":"Form_Destination","Value":"cache", "JsonValues": {"cache": "Guardar en caché del CMS","download": "Descargar el archivo", "inline": "Mostrar el contenido del archivo"}}');
		$this->TemplatePostScript=$this->module . "--" . $this->class . "/process-css";
		$this->TemplateLoadScript="minimizer.js";
		$this->LoadTemplate($this->module . '_edit.tpl.php');
	}

	function ProcessCSS() {
		$origin="template";
		$template="test";
		$folder="";
		$force=true;
		$inline=false;
		$changeURL=false;
		$destination="cache";
		$clean_temp=false;
		if (isset($_POST['Form_MinimizerOrigin'])) { $origin=$_POST['Form_MinimizerOrigin']; }
		if (isset($_POST['Form_template'])) { $template=$_POST['Form_template']; }
		if (isset($_POST['Form_folder'])) { $folder=$_POST['Form_folder']; }
		if (isset($_POST['Form_ChangeURL'])) { $changeURL=true; }
		if (isset($_POST['Form_Destination'])) { $destination=$_POST['Form_Destination']; }
		if (isset($_POST['uploads'])) {
			if (is_array($_POST['uploads'])) {
				unset($folder);
				foreach($_POST['uploads'] as $item) {
					$folder[]=sitepath . "public/temp/" . $item;
				}
				$clean_temp=true;
			}
		}
		if ($destination!="cache")  { $inline=true; }
		if ($destination=="download") {
			header('Content-Description: File Transfer');
	        header('Content-Type: text/css');
	        header('Content-Disposition: attachment; filename=compress.css');
	        header('Content-Transfer-Encoding: binary');
	    }
		$archivo=GetCSSMin($template,$folder,$force,$inline,$changeURL);
		if ($clean_temp) {
			foreach($_POST['uploads'] as $item) {
				unlink(sitepath . "public/temp/" . $item);
			}
		}
		if ($destination=="cache") { header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "--" . $this->class . "/list/text/" . urlencode(base64_encode("Se ha generado el archivo " . $archivo))); }
	}

	function StartJS() {
		$this->BreadCrumb['Minificar JS']='';
		$in_block=$this->AddFormBlock('JS');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Nombre de la plantilla","FieldName":"Form_template","Value":"' . addslashes($this->template) . '", "Help":"Nombre de la carpeta de la plantilla en \templates\"", "Required": true}');
		$this->AddFormContent($in_block,'{"Type":"combo-json","Text":"Origen de los datos","FieldName":"Form_MinimizerOrigin","Value":"template", "JsonValues": {"template": "JS en una carpeta del template", "upload": "Otros archivos a cargar"}}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Carpeta donde se encuentran los JS originales","FieldName":"Form_folder","Value":"js", "Help":"Todos los JS tienen que estar en la misma carpeta", "Required": true}');
		$this->AddFormContent($in_block,'{"Type":"upload-multiple","Text":"Cargar archivos a comprimir","FieldName":"uploads","Extensions":"js"}');
		$this->AddFormContent($in_block,'{"Type":"combo-json","Text":"Destino del proceso","FieldName":"Form_Destination","Value":"cache", "JsonValues": {"cache": "Guardar en caché del CMS","download": "Descargar el archivo", "inline": "Mostrar el contenido del archivo"}}');
		$this->TemplatePostScript=$this->module . "--" . $this->class . "/process-js";
		$this->TemplateLoadScript="minimizer.js";
		$this->LoadTemplate($this->module . '_edit.tpl.php');
	}

	function ProcessJS() {
		$origin="template";
		$template="test";
		$folder="";
		$force=true;
		$inline=false;
		$destination="cache";
		$clean_temp=false;
		if (isset($_POST['Form_MinimizerOrigin'])) { $origin=$_POST['Form_MinimizerOrigin']; }
		if (isset($_POST['Form_template'])) { $template=$_POST['Form_template']; }
		if (isset($_POST['Form_folder'])) { $folder=$_POST['Form_folder']; }
		if (isset($_POST['Form_Destination'])) { $destination=$_POST['Form_Destination']; }
		if (isset($_POST['uploads'])) {
			if (is_array($_POST['uploads'])) {
				unset($folder);
				foreach($_POST['uploads'] as $item) {
					$folder[]=sitepath . "public/temp/" . $item;
				}
				$clean_temp=true;
			}
		}
		if ($destination!="cache")  { $inline=true; }
		if ($destination=="download") {
			header('Content-Description: File Transfer');
	        header('Content-Disposition: attachment; filename=compress.js');
	        header('Content-Transfer-Encoding: binary');
	    }
		$archivo=GetJSMin($template,$folder,$force,$inline);
		if ($clean_temp) {
			foreach($_POST['uploads'] as $item) {
				unlink(sitepath . "public/temp/" . $item);
			}
		}
		if ($destination=="cache") { header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/"  . $this->module . "--" . $this->class . "/list/text=" . urlencode(base64_encode("Se ha generado el archivo " . $archivo))); }
	}

	function PrepareTableList() {
		$this->AddTableContent('Minificar','data','{{Name}}','',$this->module . "--" . $this->class . '/{{ID}}');
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block,'Ejecutar',$this->module . "--" . $this->class . '/{{ID}}');
	}

	
	
	function RunAction() { 
		if ($this->action=="list") { $this->ListItems(); }
		if ($this->action=="css") { $this->StartCSS(); }
		if ($this->action=="process-css") { $this->ProcessCSS(); }
		if ($this->action=="js") { $this->StartJS(); }
		if ($this->action=="process-js") { $this->ProcessJS(); }
	}
	
	
	function __destruct(){

	}

}
?>