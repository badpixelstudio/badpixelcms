<?php
require_once("extras.class.php");

class MasterExtraAttachments extends Extras{
	//constructor

	function __construct($hostclass,$idlink=0) {
		$this->title_extra="Archivos";
		$this->extra = 'attachments';
		parent::__construct($hostclass,$idlink);
	}
	
	function AddItemRevision($folder='', $subname='') {
		if ($folder=='') { sitepath . 'public/files'; }
		$option['Folder']=$folder;
		$option['Subname']=$subname;
		//Lo volcamos al array de opciones...
		array_push($this->options,$option);
	}
	
	function GetDescription($filename) {
		//Extraemos la extensión...
		$extension = preg_split("/\./", strtolower($filename)) ;
		$n = count($extension)-1;
		$extension = $extension[$n];
		$nombrearchivo=substr($filename,0,strpos($filename,$extension)-1);
		$nombrearchivo=str_replace("-", " ", $nombrearchivo);
		$nombrearchivo=str_replace("_", " ", $nombrearchivo);
		$nombrearchivo=ucwords(strtolower($nombrearchivo));
		return $nombrearchivo;
	}
	
	function ProcessFile($filename,$description="") {
		$nombre_archivo=$this->GetDescription($filename);
		if ($nombre_archivo!=$description) { $nombre_archivo.= "-" . $description; }
		$nombrefinal=$this->GetFileName("att",$this->id,$filename,$nombre_archivo); 
		//Recorremos el array de opciones y borramos los archivos...
		foreach ($this->options as $opcion) {
			if (! is_dir($opcion['Folder'])) { 
				mkdir($opcion['Folder']); 
				chmod($opcion['Folder'], 0777);
			}
			copy(sitepath . "public/temp/".$filename, $opcion['Folder'] . "/" . $nombrefinal);
			chmod($opcion['Folder'] . "/" . $nombrefinal, 0777);
		}
		@unlink(sitepath . "public/temp/" . $filename);
		return $nombrefinal;
	}
	
	function DeleteFileOlderVersions($filename) {
		if ($filename!="") {
			//Extraemos la extensión...
			$extension = preg_split("/\./", strtolower($filename)) ;
			$n = count($extension)-1;
			$extension = $extension[$n];
			$nombrearchivo=substr($filename,0,strpos($filename,$extension)-1);
			foreach ($this->options as $opcion) {
				$archivo=$opcion['Folder'] . "/" . $nombrearchivo;
				if ($opcion['Subname']!="") { $archivo.="-" . $opcion['Subname']; }
				$archivo.="." . $extension;
				if (is_file($archivo)) {
					@unlink($archivo);
				}
			}
		}
	}
		
	function PostXtraItem($formulario) { //Guarda un único elemento, con sus diferentes versiones...
		$formulario['System_Action']="new";	
		$archivo=$formulario['Extra_attachments_File'];
		if (! isset($formulario['Extra_attachments_Description'])) { $formulario['Extra_attachments_Description']=$this->GetDescription($archivo); }
		$formulario['Extra_attachments_File']=$this->ProcessFile($archivo,$formulario['Extra_attachments_Description']);
		$valido=$this->PostExtra($formulario);	
		if ($valido!==false) { 
			return $formulario['Extra_attachments_File']; 
		} else {
			return false;	
		}	
	}
	
	function PostAllItems() {
		if(isset($_POST['Extra_attachments_multi'])) {
			if(is_array($_POST['Extra_attachments_multi'])) {
				foreach ($_POST['Extra_attachments_multi'] as $idelemento=>$elemento) {
					$enviar=$_POST;
					unset($enviar['Extra_attachments_multi']);
					if (is_array($elemento)) {
						$enviar['Extra_attachments_File']=$elemento['Extra_attachments_File'];
						$enviar['Extra_attachments_Description']=$this->GetDescription($enviar['Extra_attachments_File']);
						if (isset($_POST['Extra_attachments_description'][$idelemento])) { $enviar['Extra_attachments_Description']=$_POST['Extra_attachments_description'][$idelemento]; }
					} else {
						$enviar['Extra_attachments_File']=$elemento;
						$enviar['Extra_attachments_Description']=$this->GetDescription($enviar['Extra_attachments_File']);
						if (isset($_POST['Extra_attachments_description'])) { $enviar['Extra_attachments_Description']=$_POST['Extra_attachments_description']; }
					}
					$this->PostXtraItem($enviar);
				}
			}
		}
		//Fin del proceso
		if ($this->return!="") { header("Location: " . $this->return); }
	}
	
	function DeleteItem($id=0) {
		$tabla = $this->table . "_attachments";
		$Datos=$this->GetDataRecord($tabla,$id);
		//Recorremos el array de opciones y borramos los archivos...
		foreach ($this->options as $carpeta) {
			$archivo=$carpeta['Folder'] . "/" . $Datos['File'];
			if ((file_exists($archivo)) and (is_file($archivo))) {
				DeleteFile($archivo);		
			}
		}
		//Borramos el registro de la tabla...
		$borrar= "DELETE FROM ". $tabla." WHERE ID=" . $id;
		$borrarexecute = parent::$db->Qry($borrar);
		return true;
	}
	
	//Sobreescribimos PostExtra para incorporar los datos archivo
	function PostExtra($formulario) {
		$archivo_procesado=false;
		$nombre=$this->default_description;
		if (isset($formulario['Form_File'])) {
			if ($formulario['Form_File']=="") {
				$this->DeleteFileOlderVersions($formulario['Form_File']);
			}
			if (isset($formulario['Original_File'])) {
				if (($formulario['Original_File']!="") and ($formulario['Original_File']!=$formulario['Form_File'])) {
					$this->DeleteFileOlderVersions($formulario['Original_File']);
				}
			}
		}
		if (isset($_POST['Change_File'])) {
			if(is_array($_POST['Change_File'])) {
				// no está vacio
				$temp=$_POST['Change_File'][0];
				unset($_POST['Change_File']);
				//Cargamos el nuevo archivo...
				$formulario['Extra_attachments_File']=$this->ProcessFile($temp,$nombre);
				$archivo_procesado=true;
			}
		}		
		if (! $archivo_procesado) {
			if (isset($formulario['Form_File'])) {
				//se debe renombrar la imagen existente con el nombre nuevo
				$nombre_archivo=$formulario['Extra_attachments_Description'];
				if (($nombre!="") and ($nombre!="Elemento")) { $nombre_archivo.="-" . $nombre; }
				$nuevonombre=$this->GetFileName("att",$this->id,$formulario['Form_File'],$nombre_archivo);
				if (count($this->options)>0) {
					foreach($this->options as $carpeta) {
						if (is_file($carpeta['Folder'] . "/" . $formulario['Form_File'])) {
							rename($carpeta['Folder'] . "/" . $formulario['Form_File'],$carpeta['Folder'] . "/" . $nuevonombre);
							$formulario['Extra_' . $this->extra . "_File"]=$nuevonombre;
						}
					}
				}
			}
		}
		parent::PostExtra($formulario);
	}

	function GetFile($filename,$opcion=0) {
		$salida="";
		if ($filename!="") {
			$extension = preg_split("/\./", strtolower($filename)) ;
			$n = count($extension)-1;
			$extension = $extension[$n];
			$nombrearchivo=substr($filename,0,strpos($filename,$extension)-1);
			if ($this->options[$opcion]['Subname']!="") { $nombrarchivo.="-" . $this->options[$opcion]['Subname']; }
			$salida=$this->options[$opcion]['Folder'] . "/" . $nombrearchivo;
			$salida.="." . $extension;
		}
		return $salida;
	}

	function PutTemplate($clase,$in_block) {
		if ($this->Total>0) {
			$module=$this->folderlink;
			$clase->AddFormContent($in_block,'{"Type": "group", "Text": "Gestionar archivos existentes","FieldName": "Xtr_Attachments_Nestable"}');
			$clase->AddFormContent($in_block,'{"Type": "xtra_nestable","Items": ' . json_encode($this->Data) . ',"Prior": "' .$this->idprior . '","Prefix": "' . $this->post_prefix . '", "Module":"' . $module . '", "LinkBase": "' . $this->folderlink . '", "ExtraModule":"attachments", "FieldViewText": "Description", "FieldLink": "File", "FolderLink":"../public/files/", "NestableLevels": 1}');
		}
		if ($this->EnableAppend) { 
			$clase->AddFormContent($in_block,'{"Type": "group", "Text": "Añadir nuevos archivos","FieldName": "Xtr_Attachments_Append"}');
			$clase->AddFormContent($in_block,'{"Type": "upload-multiple","Text": "Seleccione o arrastre nuevos archivos","FieldName": "Extra_attachments_multi", "Extensions":"pdf,zip,rar,doc,docx,xls,xlsx,ppt,pptx"}');
			$clase->AddFormHiddenContent("Extra_attachments_IDFather",$this->idprior);
		}
	}	
	
	function NewSingleItem() {
		$this->InitFormExtra();
		$this->Data['IDFather']=$this->idprior;
		$this->BreadCrumb['Crear']="";
		$this->LoadTemplate('extras_' . $this->extra . '_edit.tpl.php');
	}

	function PrepareView() {
		$in_block=$this->AddFormBlock('Adjuntos');
		$this->PutTemplate($this,$in_block);
		$this->TemplatePostScript=$this->module . "/" . $this->post_prefix . "attachments_post/prior/" . $this->idprior;
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('Adjunto');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Título","FieldName":"Extra_attachments_Description","Value":"' . addslashes($this->Data['Description']) . '","Required": true}');
		$this->AddFormContent($in_block,'{"Type":"upload","Text":"Archivo","FieldName":"Form_File","Value":"' . $this->Data['File'] . '","UploadType": "file", "UploadItem":"first", "Extensions": "pdf,zip,rar,doc,docx,xls,xlsx,ppt,pptx", "PreviewFolder": "public/files"}');
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->AddFormHiddenContent("Extra_attachments_IDFather",$this->Data['IDFather']);
		$this->AddFormHiddenContent("Extra_attachments_File",$this->Data['File']);
		$this->TemplatePostScript=$this->folderlink . "/" . $this->post_prefix . $this->extra . "_item_post/prior/" . $this->idprior;
	}
	
	function Run($action) {
		if ($action=="new") { $this->NewSingleItem(); }
		
		parent::Run($action);
	}

}
?>