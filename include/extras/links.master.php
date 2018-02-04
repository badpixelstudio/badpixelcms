<?php
// Gestión de extra enlaces 
// Creado por Israel García Sáez para BadPixel.
// Revisión: 1.0 de 1 de Junio de 2012, por Israel Garcia.

require_once("extras.class.php");

// ****** D O C U M E N T A C I Ó N ******
//****************************************
//

class MasterExtraLinks extends Extras{
	//constructor

	function __construct($hostclass,$idlink=0) {
		$this->title_extra="Enlaces";
		$this->extra = 'links';
		parent::__construct($hostclass,$idlink);
	}

	function PostXtraItem($link,$description) { //Guarda un único elemento, con sus diferentes versiones...
		//Guardamos en la base de datos...
		$Datos['System_Action']="new";
		$Datos['Form_' . $this->linkfield]=$this->linkid;
		//if (isset($_POST['Form_' . $this->linkfield])) { $Datos['Form_' . $this->linkfield]=$_POST['Form_' . $this->linkfield]; }		
		$Datos['Form_Link']=$link;
		$Datos['Form_Description']=$description;
		parent::$db->PostToDatabase($this->table . '_links',$Datos);
		//Añadimos el valor al campo ORDEN
		$addorder="UPDATE " . $this->table . "_links SET Orden=ID WHERE Orden=0";
		$ejecutar = parent::$db->Qry($addorder);	
	}
	
	function PostAllItems() {
		if(isset($_POST['Extra_links_Link'])) {
			if(is_array($_POST['Extra_links_Link'])) {
				foreach ($_POST['Extra_links_Link'] as $contador=>$filename) {
					$this->PostXtraItem($filename,$_POST['Extra_links_Description'][$contador]);
				}
			}
		}
		//Fin del proceso
		if ($this->return!="") { header("Location: " . $this->return); }
	}

	function PutTemplate($clase,$in_block) {
		if ($this->Total>0) {
			$module=$this->folderlink;
			$clase->AddFormContent($in_block,'{"Type": "group", "Text": "Gestionar enlaces existentes","FieldName": "Xtr_Links_Nestable"}');
			$clase->AddFormContent($in_block,'{"Type": "xtra_nestable","Items": ' . json_encode($this->Data) . ',"Prior": "' .$this->linkid . '","Prefix": "' . $this->post_prefix . '", "Module":"' . $module . '", "LinkBase": "' . $this->folderlink . '", "ExtraModule":"links", "FieldViewText": "Description"}');
		}
		if ($this->EnableAppend) { 
			$clase->AddFormContent($in_block,'{"Type": "group", "Text": "Añadir nuevos enlaces","FieldName": "Xtr_Links_Append"}');
			$clase->AddFormContent($in_block,'{"Type": "text", "Text": "Descripción del enlace","FieldName": "Extra_LinksDesTemp","Value":""}');
			$clase->AddFormContent($in_block,'{"Type": "url", "Text": "Dirección del enlace","FieldName": "Extra_LinksUrlTemp","Value":""}');
			$clase->AddFormContent($in_block,'{"Type": "button", "Text": "Añadir el enlace","FieldName": "uploadlink"}');
			$clase->AddFormContent($in_block,'{"Type": "div","FieldID": "linksuploader"}');
			$clase->AddFormHiddenContent("Extra_links_IDFather",$this->idprior);
		}
	}	

	function PrepareView() {
		$in_block=$this->AddFormBlock('Enlaces');
		$this->PutTemplate($this,$in_block);
		$this->TemplatePostScript=$this->folderlink . "/" . $this->post_prefix . $this->extra . "_post";
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('Enlaces');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Título","FieldName":"Extra_links_Description","Value":"' . addslashes($this->Data['Description']) . '","Required": true}');
		$this->AddFormContent($in_block,'{"Type":"url","Text":"Enlace","FieldName":"Extra_links_Link","Value":"' . addslashes($this->Data['Link']) . '","Required": true}');
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->AddFormHiddenContent("Extra_links_IDFather",$this->Data['IDFather']);
		$this->TemplatePostScript=$this->folderlink . "/" . $this->post_prefix . $this->extra . "_item_post/prior/" . $this->idprior;
	}
}
?>