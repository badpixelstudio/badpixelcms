<?php
// Gestión de extra enlaces 
// Creado por Israel García Sáez para BadPixel.
// Revisión: 1.0 de 1 de Junio de 2012, por Israel Garcia.

require_once(sitepath . "include/extras/extras.class.php");

// ****** D O C U M E N T A C I Ó N ******
//****************************************
//

class XtraRelated extends Extras{
	//constructor

	function __construct($hostclass,$idlink=0) {
		$this->title_extra="Relacionados";
		$this->extra = 'related';
		parent::__construct($hostclass,$idlink);
	}

	function GetXtraItems($paginate=false,$orden="") {
		$sql_paginas = "SELECT " . $this->table . "_" . $this->extra . ".*, " . $this->table . ".Title AS RelatedTitle, (SELECT Permalink FROM permalinks WHERE TableName='" . $this->table . "' AND TableID=" . $this->table . "_" . $this->extra . ".Link) AS Permalink ";
		$sql_paginas.= "FROM " . $this->table . "_" . $this->extra;
		$sql_paginas.= " LEFT JOIN " . $this->table . " ON " . $this->table . ".ID=" . $this->table . "_" . $this->extra . ".Link";
		$sql_paginas.= " WHERE " . $this->linkfield . "=" . $this->idprior;
		$sql_paginas.= " ORDER BY Orden ASC";
		if ($paginate) {
			$this->ItemsCount=parent::$db->GetDataListPagedFromSQL($sql_paginas,$this->page,$this->offset,$this->Items);	
		} else {
			$this->ItemsCount=parent::$db->GetDataListFromSQL($sql_paginas,$this->Items);
		}
		//Update tags deprecated
		$this->Total=$this->ItemsCount;
		$this->Data=$this->Items;

		return $this->ItemsCount; 
	}	

	function PostXtraItem($link) { //Guarda un único elemento, con sus diferentes versiones...
		//Guardamos en la base de datos...
		$Datos['System_Action']="new";
		$Datos['Form_' . $this->linkfield]=$this->linkid;
		//if (isset($_POST['Form_' . $this->linkfield])) { $Datos['Form_' . $this->linkfield]=$_POST['Form_' . $this->linkfield]; }		
		$Datos['Form_Link']=$link;
		parent::$db->PostToDatabase($this->table . '_related',$Datos);
		//Añadimos el valor al campo ORDEN
		$addorder="UPDATE " . $this->table . "_related SET Orden=ID WHERE Orden=0";
		$ejecutar = parent::$db->Qry($addorder);	
	}
	
	function PostAllItems() {
		if(isset($_POST['Extra_related_Link'])) {
			if(is_array($_POST['Extra_related_Link'])) {
				foreach ($_POST['Extra_related_Link'] as $contador=>$related) {
					$this->PostXtraItem($related);
				}
			}
		}
		//Fin del proceso
		if ($this->return!="") { header("Location: " . $this->return); }
	}

	function GetAvailableRelations() {
		$sql="SELECT ID,Title FROM " . $this->table . " WHERE ID<>" . $this->linkid . " ORDER BY ID DESC";
		$ItemsCount=parent::$db->GetDataListFromSQL($sql,$Items);
		$salida=array();
		if($ItemsCount>0) {
			foreach($Items as $item) {
				$salida[$item['ID']]=$item['Title'];
			}
		}
		return json_encode($salida);
	}

	function PutTemplate($clase,$in_block) {
		if ($this->Total>0) {
			$module=$this->module;
			if ($this->module=="cats") { $module="catpages"; }
			$clase->AddFormContent($in_block,'{"Type": "group", "Text": "Gestionar relacionados","FieldName": "Xtr_Related_Nestable"}');
			$clase->AddFormContent($in_block,'{"Type": "xtra_nestable","Items": ' . json_encode($this->Data) . ',"Prior": "' .$this->linkid . '","Prefix": "' . $this->post_prefix . '", "Module":"' . $module . '", "LinkBase": "' . $this->folderlink . '", "ExtraModule":"related", "FieldViewText": "RelatedTitle"}');
		}
		if ($this->EnableAppend) { 
			$clase->AddFormContent($in_block,'{"Type": "group", "Text": "Añadir nuevos relacionados","FieldName": "Xtr_Related_Append"}');
			$clase->AddFormContent($in_block,'{"Type": "combo-json", "Text": "Relacionado","FieldName": "Extra_RelatedLinkTemp","Value":"","JsonValues": ' . $this->GetAvailableRelations() . '}');
			$clase->AddFormContent($in_block,'{"Type": "button", "Text": "Añadir relacionado","FieldName": "uploadrelated"}');
			$clase->AddFormContent($in_block,'{"Type": "div","FieldID": "relateduploader"}');
			$clase->AddFormHiddenContent("Extra_related_IDFather",$this->idprior);
		}
	}	

	function PrepareView() {
		$in_block=$this->AddFormBlock('Relacionados');
		$this->PutTemplate($this,$in_block);
		$this->TemplatePostScript=$this->module . "/" . $this->baselink . $this->extra . "_post";
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('Relacionado');
		$this->AddFormContent($in_block,'{"Type": "combo-json", "Text": "Relacionado","FieldName": "Extra_related_Link","Value":"' . $this->Data['Link'] . '","JsonValues": ' . $this->GetAvailableRelations() . '}');
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->AddFormHiddenContent("Extra_related_IDFather",$this->Data['IDFather']);
		$this->TemplatePostScript=$this->module . "/" . $this->baselink . $this->extra . "_item_post";
	}
}
?>