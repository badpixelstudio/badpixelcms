<?php
require_once(sitepath . "include/core/core.class.php");
require_once(sitepath . "lib/translate/locale-utils.php");

class MasterLocaleCore extends Core{
	
	var $ModuleVersion='3.0.0.1';
	
	//Inicializamos valores por defecto
	var $title = 'Idiomas';
	var $class = 'locale';
	var $module = 'core';
	var $table = 'languages';	

	
	//constructor
	function __construct($values) {
		parent::__construct($values);  
		$title=$this->GetModuleName($this->class);
		if ($title!=$this->class) { $this->title=$title; }
		//breadcrumb
		$this->BreadCrumb[$this->title]=$_SERVER['PHP_SELF'];
	}
	
	function ListAdmItems() {
		$this->GetItems("",false,"factorysetting DESC, language",$this->search,false);
		$this->PrepareTableList();
		if ($this->paged) {
			$this->LoadTemplate('list_paged.tpl.php');
		} else {
			$this->LoadTemplate('list.tpl.php');
		}
	}

	function SetLangDefault() {
		parent::$db->Qry("UPDATE " . $this->table . " SET factorysetting=0");
		parent::$db->Qry("UPDATE " . $this->table . " SET factorysetting=1 WHERE ID=" . $this->id);
		header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "--" . $this->class);
	}

	function GetLocale() {
		parent::$db->LoadFormData($this,$this->id);
		$archivo="";
		if (! file_exists(sitepath . "locale/" . $this->Data['code'] . "/LC_MESSAGES/")) { 
			mkdir(sitepath . "locale/" . $this->Data['code'] . "/LC_MESSAGES/",0777,true); 
		}
		if (is_file(sitepath . "locale/" . $this->Data['code'] . "/LC_MESSAGES/general.po")) { $archivo=sitepath . "locale/" . $this->Data['code'] . "/LC_MESSAGES/general.po"; }

		if ($archivo=="") {
			//Buscamos el idioma oficial, para traducir...
			$sql="SELECT * FROM " . $this->table . " WHERE factorysetting=1";
			$Datos=parent::$db->GetDataRecordFromSQL($sql);
			if ($Datos!==false) {
				if (is_file(sitepath . "locale/" . $Datos['code'] . "/LC_MESSAGES/general.po")) { 
					copy(sitepath . "locale/" . $Datos['code'] . "/LC_MESSAGES/general.po", sitepath . "locale/" . $this->Data['code'] . "/LC_MESSAGES/general.po");
					chmod(sitepath . "locale/" . $this->Data['code'] . "/LC_MESSAGES/general.po",777);
					$archivo=sitepath . "locale/" . $this->Data['code'] . "/LC_MESSAGES/general.po"; 
				}
			}
		}
		if ($archivo=="") { header("Location: " . $_SERVER['PHP_SELF'] . "?error=" . urlencode(_("No hay plantilla .PO básica de donde extraer los textos"))); exit; }
		//Instanciamos la clase editora PO/MO
		$this->LocaleUtils=new PoParser();
		$this->LocaleUtils->parseFile($archivo);
		$this->BreadCrumb[$this->Data['language']]="";
		$this->title="Traducción " . $this->Data['language'];
		$this->AddMainMenu('Compilar .MO','core--locale/compile/lang/' . $this->Data['code']);
		$this->LoadTemplate('locale_translations.tpl.php');
	}

	function PostLocale() {
		$archivo=sitepath . "locale/" . $_POST['name'] . "/LC_MESSAGES/general.po";
		$this->LocaleUtils=new PoParser();
		$this->LocaleUtils->parseFile($archivo);
		$guardar['msgid']=explode("<##EOL##>", $_POST['pk']);
		$guardar['msgstr']=$_POST['value'];
		$guardar['reference'][]="Edit by " . $this->username . " in " . date("Y-m-d H:i:s");
		$this->LocaleUtils->setEntry($_POST['pk'], $guardar);
		if (! is_writable($archivo)) {chmod($archivo,0777);}
		$this->LocaleUtils->writeFile($archivo);
	}

	function CompileLocale() {
		if (is_file(sitepath . "locale/" . $this->_values['lang'] . "/LC_MESSAGES/general.po")) {
			phpmo_convert(sitepath . "locale/" . $this->_values['lang'] . "/LC_MESSAGES/general.po");
			header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/core--locale/text/" . urlencode(base64_encode("Se ha compilado el idioma " . $this->_values['lang'] . ". Es posible que tenga que reiniciar Apache para ver los cambios")));
		} else {
			header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/core--locale/error/" . urlencode(base64_encode("Archivo de origen .po no disponible")));
		}
	}
	
	
	function PrepareTableList() {
		$this->AddMainMenu('Crear','core--locale/new');
		$this->AddTableRowClass('success','{{factorysetting}}==1');
		$this->AddTableContent('Idioma','data','{{language}}');
		$this->AddTableContent('Codificación','data','{{code}}');
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block,'Traducir textos','core--locale/translation/id/{{id}}','{{factorysetting}}==0');
		$this->AddTableOperations($in_block,'Predeterminar','core--locale/default/id/{{id}}','{{factorysetting}}==0');
		$this->AddTableOperations($in_block,'','','{{factorysetting}}==0');
		$this->AddTableOperations($in_block,'Editar','core--locale/edit/id/{{id}}');
		$this->AddTableOperations($in_block,'Eliminar','core--locale/delete/id/{{id}}');
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('General');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Idioma","FieldName":"Form_language","Value":"' . addslashes($this->Data['language']) . '", "Help":"Nombre del idioma, escrito en su lengua original"}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Códificación (Código Pais ISO3166  y Código Idioma ISO639 de 2 letras)","FieldName":"Form_code","Value":"' . addslashes($this->Data['code']) . '", "Help":"En formato xx_XX"}');
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->TemplatePostScript='core--locale/post';
	}
	
	function RunAction() { 
		parent::RunAction();
		if ($this->action=="default") { $this->SetLangDefault(); }
		if ($this->action=="translation") { $this->GetLocale(); }
		if ($this->action=="post-translation") { $this->PostLocale(); }
		if ($this->action=="compile") { $this->CompileLocale(); }
	}
}
?>