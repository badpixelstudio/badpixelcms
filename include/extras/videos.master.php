<?php
// Gestión de extra videos
// Creado por Israel García Sáez para BadPixel.
// Revisión: 1.0 de 1 de Junio de 2012, por Israel Garcia.

require_once("extras.class.php");

// ****** D O C U M E N T A C I Ó N ******
//****************************************
//

class MasterExtraVideos extends Extras{
	//constructor

	function __construct($hostclass,$idlink=0) {
		$this->title_extra="Videos";
		$this->extra = 'videos';
		parent::__construct($hostclass,$idlink);
	}

	function PostXtraItem($link,$description) { //Guarda un único elemento, con sus diferentes versiones...
		//Guardamos en la base de datos...
		$Datos['System_Action']="new";
		$Datos['Form_' . $this->linkfield]=$this->linkid;
		//if (isset($_POST['Form_' . $this->linkfield])) { $Datos['Form_' . $this->linkfield]=$_POST['Form_' . $this->linkfield]; }	
		if ((strpos($link,"<iframe")===false) and (strpos($link, "youtu")!==false)) {
			$rep="//youtu.be/";
			$p=strpos($link, $rep);
			if ($p!==false) {
				$codeyt=substr($link, $p+strlen($rep));
				$link='<iframe width="420" height="315" src="http://www.youtube.com/embed/' . $codeyt . '" frameborder="0" allowfullscreen></iframe>';
			}
			$rep="/watch";
			$p=strpos($link, $rep);
			if ($p!==false) {
				$p=strpos($link, "v=");
				$codeyt=substr($link, $p+2);
				$p=strpos($codeyt, "&");
				if ($p!==false) { $codeyt=substr($codeyt, 0,($p-1)); }
				$link='<iframe width="420" height="315" src="http://www.youtube.com/embed/' . $codeyt . '" frameborder="0" allowfullscreen></iframe>';
			}
		}		
		$Datos['Form_Embed']=$link;
		$Datos['Form_Description']=$description;
		parent::$db->PostToDatabase($this->table . '_videos',$Datos);
		//Añadimos el valor al campo ORDEN
		$addorder="UPDATE " . $this->table . "_videos SET Orden=ID WHERE Orden=0";
		$ejecutar = parent::$db->Qry($addorder);	
	}
	
	function PostAllItems() {
		if(isset($_POST['Extra_videos_Embed'])) {
			if(is_array($_POST['Extra_videos_Embed'])) {
				foreach ($_POST['Extra_videos_Embed'] as $contador=>$filename) {
					$this->PostXtraItem($filename,$_POST['Extra_videos_Description'][$contador]);
				}
			}
		}
		//Fin del proceso
		if ($this->return!="") { header("Location: " . $this->return); }
	}

	function PutTemplate($clase,$in_block) {
		if ($this->Total>0) {
			$module=$this->folderlink;
			$clase->AddFormContent($in_block,'{"Type": "group", "Text": "Gestionar videos existentes","FieldName": "Xtr_Videos_Nestable"}');
			$clase->AddFormContent($in_block,'{"Type": "xtra_nestable","Items": ' . json_encode($this->Data) . ',"Prior": "' .$this->linkid . '","Prefix": "' . $this->post_prefix . '", "Module":"' . $module . '", "LinkBase": "' . $this->folderlink . '", "ExtraModule":"videos", "FieldViewText": "Description"}');
		}
		if ($this->EnableAppend) { 
			$clase->AddFormContent($in_block,'{"Type": "group", "Text": "Añadir nuevos videos","FieldName": "Xtr_Videos_Append"}');
			$clase->AddFormContent($in_block,'{"Type": "text", "Text": "Descripción del video","FieldName": "Extra_VideosDesTemp","Value":""}');
			$clase->AddFormContent($in_block,'{"Type": "textarea", "Text": "Código a incrustar del vídeo","FieldName": "Extra_VideosUrlTemp","Value":""}');
			$clase->AddFormContent($in_block,'{"Type": "button", "Text": "Añadir el video","FieldName": "uploadvideo"}');
			$clase->AddFormContent($in_block,'{"Type": "div","FieldID": "videosuploader"}');
			$clase->AddFormHiddenContent("Extra_videos_IDFather",$this->idprior);
		}
	}	

	function PrepareView() {
		$in_block=$this->AddFormBlock('Videos');
		$this->PutTemplate($this,$in_block);
		$this->TemplatePostScript=$this->folderlink . "/" . $this->post_prefix . $this->extra . "_post";
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('Videos');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Título","FieldName":"Extra_videos_Description","Value":"' . addslashes($this->Data['Description']) . '","Required": true}');
		$this->AddFormContent($in_block,'{"Type":"textarea","Text":"Código a incrustar o URL de YouTube","FieldName":"Extra_videos_Embed","Value":"' . addcslashes($this->Data['Embed'],'\\"') . '","Required": true}');
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->AddFormHiddenContent("Extra_videos_IDFather",$this->Data['IDFather']);
		$this->TemplatePostScript=$this->folderlink . "/" . $this->post_prefix . $this->extra . "_item_post/prior/" . $this->idprior;
	}
}
?>