<?php
require_once(sitepath . "include/core/core.class.php");
require_once(sitepath . "include/singleblog/singleblog.config.php");
require_once(sitepath . "lib/images/thumbs.php");	

class MasterSingleBlogCats extends Core{
	var $title = 'Categorías de Blog';
	var $class = 'cats';
	var $module = 'singleblog';
	var $published= array();
	var $table = 'singleblog_cats';
	var $version=false;
	var $permalink_action="action=cats";

	function __construct($values) {
		parent::__construct($values); 
		$this->conf = new ConfigSingleBlog($this->businessID);
		$this->BreadCrumb[$this->GetModuleName($this->module)]=$this->module;
		$this->BreadCrumb[$this->title]=$this->module . "/cats_list";
	}

	function PostAdmItem($redirect=true) {
		$redirect=siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "/" . $this->class . "_list";
		$this->PostItem($redirect);
	}

	function PrepareTableList() {
		$this->AddMainMenu('Crear',$this->module . '/cats_new');
		$this->AddTableContent('Categoría','data','{{Title}}','',$this->module . '/cats_edit/id/{{ID}}');
		$this->AddTableContent('Ver elementos','data','Ver Elementos...','',$this->module . '/list/idparent/{{ID}}');
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block,'Ver elementos',$this->module . '/list/idparent/{{ID}}');
		$this->AddTableOperations($in_block);
		$this->AddTableOperations($in_block,'Editar',$this->module . '/cats_edit/id/{{ID}}');
		$this->AddTableOperations($in_block,'Eliminar',$this->module . '/cats_delete/id/{{ID}}');
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('General');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Categoría","FieldName":"Form_Title","Value":"' . addslashes($this->Data['Title']) . '","Required": true}');
		$in_block=$this->AddFormBlock('Avanzado');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Enlace permanente","FieldName":"Permalink","Value":"' . $this->Data['Permalink'] . '", "Help": "En blanco genera la dirección URL de forma automática"}');
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->TemplatePostScript=siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "/" . $this->class . "_post";
	} 
}
?>