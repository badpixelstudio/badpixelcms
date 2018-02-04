<?php
require_once(sitepath . "include/core/core.class.php");

class MasterPermalinks extends Core{
	
	var $ModuleVersion='2.0.0.0';
	
	//Inicializamos valores por defecto
	var $title = 'Permalinks';
	var $class = 'permalinks';
	var $module = 'core';
	var $action = 'list';
	var $table = 'permalinks';	

	
	function __construct($values) {
		parent::__construct($values);  
		$title=$this->GetModuleName($this->class);
		if ($title!=$this->class) { $this->title=$title; }
		//breadcrumb
		$this->BreadCrumb[$this->title]=$this->class;
	}

	function PopulateTables($seleccionado) {
		$salida="";
		$query_combo = "SHOW TABLES";
		$total=parent::$db->GetDataListFromSQL($query_combo,$tablas);
		if ($total>0) {
			foreach ($tablas as $tabla) {
				$ponerselected="";
				if ($tabla['Tables_in_' . dbname]==$seleccionado) { $ponerselected=" selected"; }
				$salida.="<option" . $ponerselected . " value='" . $tabla['Tables_in_' . dbname] . "'>" . $tabla['Tables_in_' . dbname] . "</option>";
			}
		}
		return $salida;
	}
	
	function CreateSiteMap($business="") {
		$sql_paginas = "SELECT * FROM " . $this->table . " WHERE (ChangeFreq<>'disabled' OR ChangeFreq IS NULL)";
		if ($business!="") { $sql_paginas.=" AND IDBusiness=" . $business; }
		$sql_paginas.=" ORDER BY Priority DESC";
		$this->ItemsCount = parent::$db->GetDataListFromSQL($sql_paginas,$this->Items);	
		if ($this->ItemsCount>0) {
			$codigo='<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';	
			foreach ($this->Items as $elemento) {
				$codigo.='<url>';
				$codigo.='<loc>http://'. sitedomain . "/" . $elemento['Permalink'] . '</loc>';
				if (($elemento['LastMod']!="") and ($elemento['LastMod']!="0000-00-00")) {
					$codigo.='<lastmod>'.$elemento['LastMod'].'</lastmod>';
				} else {
					$codigo.='<lastmod>'. date("Y-m-d") .'</lastmod>';
				}
				if ($elemento['ChangeFreq']!="") {
					$codigo.='<changefreq>' . $elemento['ChangeFreq'] . '</changefreq>';
				}
				if ($elemento['Priority']!="") {
					$codigo.='<priority>'.$elemento['Priority'].'</priority>';
				} else {
					$codigo.='<priority>0.5</priority>';
				}
				$codigo.='</url>';
			}
			$codigo.='</urlset> ';
			if ($fp=fopen(sitepath . "sitemap.xml",'w+')) {
   				fwrite($fp,$codigo);
				fclose($fp);
				chmod(sitepath . "sitemap.xml", 0777);
   				return true;
			}
		}
		return false;
	}
	
	function PrepareTableList() {
		$this->AddMainMenu('Crear',$this->class . '/new');
		$this->AddTableContent('Enlace Permanente','data','{{Permalink}}','',$this->class . '/edit/id/{{ID}}');
		$this->AddTableContent('Módulo','data','{{ModuleName}}','',$this->class . '/edit/id/{{ID}}');
		$this->AddTableContent('Opciones','data','{{Options}}','',$this->class . '/edit/id/{{ID}}');
		$this->AddTableContent('Acceso a Datos','data','{{TableName}} {{TableID}}');
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block,'Editar',$this->class . '/edit/id/{{ID}}');
		$this->AddTableOperations($in_block,'Eliminar',$this->class . '/delete/id/{{ID}}');
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('General');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Enlace Permanente","FieldName":"Form_Permalink","Value":"' . addcslashes($this->Data['Permalink'],'\\"') . '","Required": true}');
		$query_combo = "SHOW TABLES";
		$salida=array();
		$total=parent::$db->GetDataListFromSQL($query_combo,$tablas);
		if ($total>0) {
			foreach ($tablas as $tabla) {
				$salida[$tabla['Tables_in_' . dbname]]=$tabla['Tables_in_' . dbname];
			}
		}
		$opciones=json_encode($salida,true);
		$this->AddFormContent($in_block,'{"Type":"combo-json","Text":"Tabla","FieldName":"Form_TableName","Value":"' . $this->Data['TableName'] . '", "JsonValues": ' . $opciones . ', "NullValue":""}');
		$this->AddFormContent($in_block,'{"Type":"number","Text":"ID de Tabla","FieldName":"Form_TableID","Value":"' . addslashes($this->Data['TableID']) . '"}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Módulo","FieldName":"Form_ModuleName","Value":"' . addslashes($this->Data['ModuleName']) . '", "Help":"Nombre del módulo"}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Opciones","FieldName":"Form_Options","Value":"' . addslashes($this->Data['Options']) . '", "Help":"En formato QueryString HTML"}');
		if ($this->MultiBusiness) { $this->AddFormContent($in_block,'{"Type":"combo","Text":"Empresa","FieldName":"Form_IDBusiness","Value":"' . $this->Data['IDBusiness'] . '", "ListTable": "business", "ListValue": "ID", "ListOption": "Name", "ListOrder":"Name", "NullValue": "0"}'); }
		$in_block=$this->AddFormBlock('Sitemap');
		$this->AddFormContent($in_block,'{"Type":"date","Text":"Fecha de modificación","FieldName":"Form_LastMod","Value":"' . addslashes($this->Data['LastMod']) . '"}');
		$this->AddFormContent($in_block,'{"Type":"number","Text":"Prioridad","FieldName":"Form_Priority","Value":"' . addslashes($this->Data['Priority']) . '", "MinValue":"0.1", "MaxValue":"1", "StepValue": "0.1", "Help":"Valores comprendidos entre 0.1 y 1. En blanco se usará un valor intermedio."}');
		$this->AddFormContent($in_block,'{"Type":"combo-json","Text":"Periodicidad de muestreo","FieldName":"Form_ChangeFreq","Value":"' . $this->Data['ChangeFreq'] . '", "JsonValues": {"disabled": "No incluir el enlace", "": "No informar de la periodicidad", "always": "Siempre", "hourly": "Cada hora", "daily": "Cada día", "weekly": "Cada semana", "montly": "Cada mes", "yearly": "Cada año", "never": "Nunca"}}');
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->TemplatePostScript=$this->class . '/post';
	}

	function NewAdmItem() {
		$values['LastMod']=date("d/m/Y");
		$values['Priority']="0.5";
		$values['ChangeFreq']="daily";
		$this->NewItem($values);
		$this->TotalLanguages=0;	
		$this->PrepareForm();
		$this->LoadTemplate('edit.tpl.php');
	}

	function PostAdmItem($redirect=true) {
		$this->PostItem($this->class);
	}
	
	function RunAction() { 
		parent::RunAction();
		if ($this->action=="sitemap") { echo intval($this->CreateSiteMap()); }
	}
	
	
	function __destruct(){

	}

}
?>