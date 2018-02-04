<?php
// Gestión de extra de elementos embedidos
// Creado por Israel García Sáez para BadPixel.
// Revisión: 1.0 de 7 de Octubre de 2012, por Israel Garcia.

require_once("extras.class.php");

// ****** D O C U M E N T A C I Ó N ******
//****************************************
//

class MasterExtraEmbed extends Extras{
	//constructor

	function __construct($hostclass,$idlink=0) {
		$this->title_extra="Embedidos";
		$this->extra = 'embed';
		parent::__construct($hostclass,$idlink);
	}

	function PostXtraItem($link,$description) { //Guarda un único elemento, con sus diferentes versiones...
		//Guardamos en la base de datos...
		$Datos['System_Action']="new";
		$Datos['Form_' . $this->linkfield]=$this->linkid;
		//if (isset($_POST['Form_' . $this->linkfield])) { $Datos['Form_' . $this->linkfield]=$_POST['Form_' . $this->linkfield]; }		
		$Datos['Form_Embed']=$link;
		$Datos['Form_Description']=$description;
		parent::$db->PostToDatabase($this->table . '_embed',$Datos);
		//Añadimos el valor al campo ORDEN
		$addorder="UPDATE " . $this->table . "_embed SET Orden=ID WHERE Orden=0";
		$ejecutar = parent::$db->Qry($addorder);	
	}
	
	function PostAllItems() {
		if(isset($_POST['Extra_embed_Embed'])) {
			if(is_array($_POST['Extra_embed_Embed'])) {
				foreach ($_POST['Extra_embed_Embed'] as $contador=>$filename) {
					$this->PostXtraItem($filename,$_POST['Extra_embed_Description'][$contador]);
				}
			}
		}
		//Fin del proceso
		if ($this->return!="") { header("Location: " . $this->return); }
	}

	function PutTemplate($clase,$in_block) {
		if ($this->Total>0) {
			$module=$this->folderlink;
			$clase->AddFormContent($in_block,'{"Type": "group", "Text": "Gestionar elementos incrustados existentes","FieldName": "Xtr_Embeds_Nestable"}');
			$clase->AddFormContent($in_block,'{"Type": "xtra_nestable","Items": ' . json_encode($this->Data) . ',"Prior": "' .$this->linkid . '","Prefix": "' . $this->post_prefix . '", "Module":"' . $module . '", "LinkBase": "' . $this->folderlink . '", "ExtraModule":"embed", "FieldViewText": "Description"}');
		}
		if ($this->EnableAppend) { 
			$clase->AddFormContent($in_block,'{"Type": "group", "Text": "Añadir nuevos elementos incrustados","FieldName": "Xtr_Embeds_Append"}');
			$clase->AddFormContent($in_block,'{"Type": "text", "Text": "Descripción del elemento","FieldName": "Extra_EmbedDesTemp","Value":""}');
			$clase->AddFormContent($in_block,'{"Type": "textarea", "Text": "Código a incrustar","FieldName": "Extra_EmbedUrlTemp","Value":""}');
			$clase->AddFormContent($in_block,'{"Type": "button", "Text": "Añadir el elemento","FieldName": "uploadembed"}');
			$clase->AddFormContent($in_block,'{"Type": "div","FieldID": "embeduploader"}');
			$clase->AddFormHiddenContent("Extra_embed_IDFather",$this->idprior);
		}
	}

	function PrepareView() {
		$in_block=$this->AddFormBlock('Elementos Incrustados');
		$this->PutTemplate($this,$in_block);
		$this->TemplatePostScript=$this->folderlink . "/" . $this->post_prefix . $this->extra . "_post";
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('Elementos incrustados');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Título","FieldName":"Extra_embed_Description","Value":"' . addslashes($this->Data['Description']) . '","Required": true}');
		$this->AddFormContent($in_block,'{"Type":"textarea","Text":"Código a incrustar","FieldName":"Extra_embed_Embed","Value":"' . addcslashes($this->Data['Embed'],'\\"') . '","Required": true}');
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->AddFormHiddenContent("Extra_embed_IDFather",$this->Data['IDFather']);
		$this->TemplatePostScript=$this->folderlink . "/" . $this->post_prefix . $this->extra . "_item_post/prior/" . $this->idprior;
	}
}
?>