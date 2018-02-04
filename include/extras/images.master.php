<?php
// Gestión de extra imágenes 
// Creado por Israel García Sáez para BadPixel.
// Revisión: 1.0 de 1 de Junio de 2012, por Israel Garcia.

//cargamos el resto de archivos necesarios
require_once(sitepath . "include/extras/extras.class.php");
require_once(sitepath . "lib/images/thumbs.php");

// ****** D O C U M E N T A C I Ó N ******
//****************************************
//

class MasterExtraImages extends Extras{
	//constructor
	var $uselink=false;
	var $usedownload=false;
	var $folderfiles="public/files/";
	
	var $OptionsArray = array();

	function __construct($hostclass,$idlink=0) {
		$this->title_extra="Imágenes";
		$this->extra = 'images';			
		parent::__construct($hostclass,$idlink);
		//Cargamos los campos opcionales...
		if (isset($conf['ExtrasImagesEnableLink'])) {
			if ($conf['ExtrasImagesEnableLink']==1) { $this->uselink=true; }
		}
		if (isset($conf['ExtrasImagesEnableDownload'])) {
			if ($conf['ExtrasImagesEnableDownload']==1) { $this->usedownload=true; }
		}		
	}
	
	function EnableFields($link=false,$download=false) {
		$this->uselink=$link;
		$this->usedownload=$download;	
	}

	function AddItemRevision($opciones) {
		$this->options=$opciones;
		$this->OptionsArray=GetOptionsImages($opciones);
	}	
	
	function GetDescription($filename) {
		//Extraemos la extensión...
		$extension = preg_split("/\./", strtolower($filename)) ;
		$n = count($extension)-1;
		$extension = $extension[$n];
		$nombrearchivo=substr($filename,0,strpos($filename,$extension)-1);
		return $nombrearchivo;
	}

	function ProcessImage($filename,$descripcion="") {
		$nombrefinal=$this->GetFileName("img",$this->id,$filename,$descripcion); 
		rename(sitepath . "public/temp/" . $filename, sitepath . "public/temp/" . $nombrefinal);
		UploadImage($nombrefinal,$this->options);
		return $nombrefinal;
	}
	
	function DeleteImageOlderVersions($filename) {
		if ($filename!="") {
			DeleteOptionsImagesFolders($this->options,$filename);
		}
	}
	
	function ProcessFile($filename,$description="") {
		$nombrefinal=$this->GetFileName("att",$this->id,$filename,$descripcion); 
		copy(sitepath . "public/temp/" . $filename, sitepath . $this->folderfiles . "/" . $nombrefinal);
		unlink(sitepath . "public/temp/" . $filename);
		return $nombrefinal;
	}
	
	function DeleteFileOlderVersions($filename) {
		if ($filename!="") {
			if (is_file($this->folderfiles . "/" . $filename)) {
				@unlink($this->folderfiles . "/" . $filename);
			}
		}
	}
	
	function PostXtraItem($formulario) {
		//Guardamos la imagen
		$imagen=$formulario['Extra_images_Image'];
		if (! isset($formulario['Extra_images_Description'])) { $formulario['Extra_images_Description']=$this->GetDescription($imagen); }
		$formulario['Extra_images_Image']=$this->ProcessImage($imagen,$formulario['Extra_images_Description']);
		$valido=$this->PostExtra($formulario);	
		if ($valido!==false) { 
			return $formulario['Extra_images_Image']; 
		} else {
			return false;	
		}
	}	
	

	function PostAllItems($volver="") {
		//Comprueba si tenemos que procesarlo inmediatamente ($_POST) o de forma retrasada (AJAX)
		$contar=0;
		if(isset($_POST['Extra_images_multi'])) { $contar=count($_POST['Extra_images_multi']); }
		if (($contar>10) and ($volver!="")) { 
			$this->PostAllItemsDelayed($volver);
		} else {
			$this->PostAllItemsNow();
		}
	}
	
	function PostAllItemsNow($volver="") {	
		if(isset($_POST['Extra_images_multi'])) {
			if(is_array($_POST['Extra_images_multi'])) {
				foreach ($_POST['Extra_images_multi'] as $idelemento=>$elemento) {
					$enviar=$_POST;
					unset($enviar['Extra_images_multi']);
					if (is_array($elemento)) {
						$enviar['Extra_images_Image']=$elemento['Extra_images_Image'];
						$enviar['Extra_images_Description']=$this->default_description;
						if (isset($_POST['Extra_images_description'][$idelemento])) { $enviar['Extra_images_Description']=$_POST['Extra_images_description'][$idelemento]; }
					} else {
						$enviar['Extra_images_Image']=$elemento;
						$enviar['Extra_images_Description']=$this->default_description;
						if (isset($_POST['Extra_images_description'])) { $enviar['Extra_images_Description']=$_POST['Extra_images_description']; }
					}
					$this->PostXtraItem($enviar);
				}
			}
		}
	}
	
	function PostAllItemsDelayed($volver="") {
		//Guardamos la configuración en una sesión, así nos ahorramos cargarla en cada ejecución del ajax...
		$_SESSION['XtraImagesOptions']=$this->options;
		$_SESSION['XtraImagesFiles']=$_POST['Extra_images_multi'];
		if (! isset($_POST['Extra_images_Description']))  {
			foreach($_POST['Extra_images_multi'] as $image) {
				$_POST['Extra_images_Description'][]=$this->default_description;
			}
		}
		$_SESSION['XtraImagesDescription']=$_POST['Extra_images_Description'];
		$_SESSION['XtraImagesID']=$this->linkid;
		$_SESSION['XtraImagesModule']=$this->module;
		$_SESSION['XtraImagesReturn']=$volver;
		//Generamos la url donde cargar el mensaje de proceso...
		//Si Volver está en blanco, utilizamos el script actual...
		$url_process=siteprotocol . sitedomain;
		if (stripos($volver, sitePanelFolder)!==false) {
			$url_postprocess=$url_process . sitePanelFolder . "/" . $this->folderlink . "/" . $this->post_prefix . "images_post_delayed";
			$url_process.=sitePanelFolder . "/" . $this->folderlink . "/" . $this->post_prefix . "images_delayed";
		} else {
			$url_postprocess=$url_process . "/index.php?module=" . $this->module . "&action=images_post_delayed";
			$url_process.=sitePanelFolder . "/index.php?module=" . $this->module . "&action=images_delayed";
		}
		$_SESSION['XtraImagesPostScript']=$url_postprocess;
		header("Location:" . $url_process);
		exit;
	}
	
	function PostItemDelayed() {
		$files=$_SESSION['XtraImagesFiles'];
		$description=$_SESSION['XtraImagesDescription'];
		$total=count($files);
		$Devolver['Processed']="";
		if ($total>0) {		
			$archivo=$files[0];	
			$this->linkid=$_SESSION['XtraImagesID'];
			$this->options=$_SESSION['XtraImagesOptions'];
			$enviar['Extra_images_Image']=$archivo;
			$enviar['Extra_images_Description']="";
			if (isset($description[0])) { $enviar['Extra_images_Description']=$description[0]; }
			$devolver_archivo=$this->PostXtraItem($enviar);
			$Devolver['Processed']=siteprotocol . sitedomain . "public/thumbnails/" . $devolver_archivo;
			$files=$_SESSION['XtraImagesFiles'];
			$posicion=array_search($archivo,$files);
			unset($files[$posicion]);
			if (isset($description[$posicion])) { unset($description[$posicion]); }
			$_SESSION['XtraImagesFiles']=array_values($files);
			$_SESSION['XtraImagesDescription']=array_values($description);
		}
		$Devolver['Return']="/";
		$Devolver['Total']=count($files);
		if (isset($_SESSION['XtraImagesReturn'])) {
			$Devolver['Return']=$_SESSION['XtraImagesReturn'];
		}
		if (count($files)==0) {
			unset($_SESSION['XtraImagesReturn']);
		}
		return json_encode($Devolver);
	}

	function DeleteItem($id=0) {
		$tabla = $this->table . "_images";
		$Datos=$this->GetDataRecord($tabla,$id);
		//Recorremos el array de opciones y borramos los archivos...
		DeleteOptionsImagesFolders($this->options,$Datos['Image']);
		if (isset($Datos['Download'])) { $this->DeleteFileOlderVersions($Datos['Download']); }
		//Borramos el registro de la tabla...
		$borrar= "DELETE FROM ". $tabla." WHERE ID=" . $id;
		$borrarexecute = parent::$db->Qry($borrar);
		return true;
	}

	//Edit Image
	function EditImage($params,$origen='public/images/',$destino='public/thumbnails/'){
		CropImage($origen, $destino,$_POST['System_Name'], $_POST['Form_cropx'], $_POST['Form_cropy'], $_POST['Form_cropwidth'], $_POST['Form_cropheight'], $_POST['Form_imagewidth'], $_POST['Form_imageheight']);
		//Actualizamos el nombre del archivo para evitar la caché
		parent::$db->UpdateCacheTag($params['System_Name']);
	}	
	
	//Sobreescribimos PostExtra para incorporar los datos de imagen y adjunto
	function PostExtra($formulario) {
		$imagen_procesada=false;
		$adjunto_procesado=false;
		$nombre=$this->default_description;
		if (isset($formulario['Extra_images_Description'])) { $nombre=$formulario['Extra_images_Description'];  }
		if (isset($formulario['Form_Image'])) {
			if ($formulario['Form_Image']=="") {
				//Borramos la imagen anterior...
				$this->DeleteImageOlderVersions($formulario['Extra_images_Image']);
				if (isset($formulario['Original_Image'])) {
					if (($formulario['Original_Image']!="") and ($formulario['Original_Image']!=$formulario['Form_Image'])) {
						$this->DeleteFileOlderVersions($formulario['Original_Image']);
					}
				}
			}
		}
		if (isset($formulario['Change_Image'])) {
			if(is_array($formulario['Change_Image'])) {
				// no está vacio
				$temp=$formulario['Change_Image'][0];
				unset($formulario['Change_Image']);
				//Cargamos la nueva imagen...
				$formulario['Extra_images_Image']=$this->ProcessImage($temp,$nombre);
				$imagen_procesada=true;
			}
		}
		if (isset($formulario['Form_Download'])) {
			if ($formulario['Form_Download']=="") {
				//Borramos el archivo anterior...
				$this->DeleteFileOlderVersions($formulario['Extra_images_Download']);
				if (isset($formulario['Original_Download'])) {
					if (($formulario['Original_Download']!="") and ($formulario['Original_Download']!=$formulario['Form_Download'])) {
						$this->DeleteFileOlderVersions($formulario['Original_Download']);
					}
				}
			}
		}
		if (isset($formulario['Change_Download'])) {
			if(is_array($formulario['Change_Download'])) {
				// no está vacio
				$temp=$formulario['Change_Download'][0];
				unset($formulario['Change_Download']);
				//Cargamos la nueva imagen...
				$formulario['Extra_images_Download']=$this->ProcessFile($temp,$nombre);
				$$adjunto_procesado=true;
			}
		}
		if (! $imagen_procesada) {
			if (isset($formulario['Form_Image'])) {
				//se debe renombrar la imagen existente con el nombre nuevo
				$nuevonombre=$this->GetFileName("img",$this->id,$formulario['Form_Image'],$nombre);
				$carpetas=GetOptionsImagesFolders($this->options,true);
				if (count($carpetas)>0) {
					foreach($carpetas as $carpeta) {
						if (is_file($carpeta . $formulario['Form_Image'])) {
							rename($carpeta . $formulario['Form_Image'],$carpeta . $nuevonombre);
							$formulario['Extra_' . $this->extra . "_Image"]=$nuevonombre;
						}
					}
				}
			}	
		}
		if (! $adjunto_procesado) {
			if (isset($formulario['Form_Download'])) {
				$nuevonombre=$this->GetFileName("att",$this->id,$formulario['Form_Download'],$nombre);
				if (is_file(sitepath . "public/files/" . $formulario['Form_Download'])) {
					rename(sitepath . "public/files/" . $formulario['Form_Download'],sitepath . "public/files/" . $nuevonombre);
					$formulario['Extra_' . $this->extra . "_Download"]=$nuevonombre;
				}
			}	
		}
		parent::PostExtra($formulario);
	}

	function PutTemplate($clase,$in_block) {
		if ($this->Total>0) {
			$module=$this->folderlink; // $this->module;
			//if ($this->module=="cats") { $module="catpages"; }
			$clase->AddFormContent($in_block,'{"Type": "group", "Text": "Gestionar imágenes existentes","FieldName": "Xtr_Images_Nestable"}');
			$clase->AddFormContent($in_block,'{"Type": "xtra_images","Items": ' . json_encode($this->Data) . ',"Prior": "' .$this->idprior . '","Prefix": "' . $this->post_prefix . '", "Module":"' . $module . '", "LinkBase": "' . $this->folderlink . '", "ExtraModule":"images"}');
		}
		if ($this->EnableAppend) { 
			$clase->AddFormContent($in_block,'{"Type": "group", "Text": "Añadir nuevas imágenes","FieldName": "Xtr_Images_Append"}');
			$clase->AddFormContent($in_block,'{"Type": "upload-multiple","Text": "Seleccione o arrastre nuevas imágenes","FieldName": "Extra_images_multi", "Extensions":"jpg,jpeg,gif,png"}');
			$clase->AddFormHiddenContent("Extra_images_IDFather",$this->idprior);
		}
	}	
	
	function NewSingleItem() {
		$this->InitFormExtra();
		$this->Data['IDFather']=$this->idprior;
		$this->BreadCrumb['Crear']="";
		$this->LoadTemplate('extras_' . $this->extra . '_edit.tpl.php');
	}

	function PrepareView() {
		$in_block=$this->AddFormBlock('Imágenes');
		$this->PutTemplate($this,$in_block);
		$this->TemplatePostScript=$this->folderlink . "/" . $this->post_prefix . "images_post/prior/" . $this->idprior;
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('Imagen');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Título","FieldName":"Extra_images_Description","Value":"' . addslashes($this->Data['Description']) . '","Required": true}');
		$this->AddFormContent($in_block,'{"Type":"upload","Text":"Imagen","FieldName":"Form_Image","Value":"' . $this->Data['Image'] . '","UploadType": "image", "UploadItem":"first", "Extensions": "jpg,jpeg,gif,png", "PreviewFolder": "public/thumbnails", "External":"","NoEdit":false}');
		if ($this->uselink) { $this->AddFormContent($in_block,'{"Type":"url","Text":"URL enlace","FieldName":"Extra_images_Link","Value":"' . addslashes($this->Data['Link']) . '"}'); }
		if ($this->usedownload) { $this->AddFormContent($in_block,'{"Type":"upload","Text":"Descargar archivo","FieldName":"Form_Download","Value":"' . $this->Data['Download'] . '","UploadType": "file", "UploadItem":"second", "Extensions": "pdf,rar,zip,doc,docx,xls,xlsx,ppt,pptx", "PreviewFolder": "public/files"}'); }
		if ($this->Data['Action']!="new") {
			foreach ($this->OptionsArray as $contador=>$option) {
				if ($contador>0) {
					$this->AddFormContent($in_block,'{"Type":"button-edit","Text":"Editar miniatura ' . $option['width'] . " x " . $option['height'] .'","FieldName":"Button_Edt_' . $contador . '","Value":"' . $this->module . '/images_editor/id/' . $this->Data['ID'] . '/option/' . $contador . '","Required": true}');
				}
			}
		}
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->AddFormHiddenContent("Extra_images_IDFather",$this->Data['IDFather']);
		$this->AddFormHiddenContent("Extra_images_Image",$this->Data['Image']);
		$this->TemplatePostScript=$this->folderlink . "/" . $this->post_prefix . $this->extra . "_item_post/prior/" . $this->idprior;
	}
	
	function Run($action) { 
		if ($action=="editor") {
			$this->LoadFormExtra();
			$tam = getimagesize(sitepath . "public/images/".$this->Data['Image']);
			$this->Data['MaxZoom']=$tam[0];
			//Breadcrumb
			$this->BreadCrumb['Editor']='';
			$this->Data['ActionForm']=$this->folderlink . "/" . $this->post_prefix . "images_editor_post";
			$this->Data['ReturnForm']=$_SERVER['HTTP_REFERER'];
			$this->LoadTemplate('image_editor.tpl.php');
			exit;
		} 
		
		if ($action=="editor_post") {
			$destino='public/thumbnails/';
			if (isset($_POST['System_Option'])) {
				if (intval($_POST['System_Option'])>0) {
					$destino='public/' . $this->OptionsArray[intval($_POST['System_Option'])]['folder'] . '/';
				}
			}
			$this->EditImage($_POST,'public/images/',$destino);
			//recupera un registro de la tabla imágenes
			$Imagen=$this->GetDataRecord($this->module . "_images",$_GET['id']);
			$this->return=$this->module . "?action=" . $this->extra . "_view&prior=" . $_POST['Extra_images_IDFather'];
			if (isset($_POST['System_Return'])) {
				if ($_POST['System_Return']!="") {
					$this->return=$_POST['System_Return'];
				}
			}
			header("Location: " . $this->return);
			exit;
		} 
		
		if ($action=="editor_first") {
			$_GET['option']=-1;
			$this->Data['Image']=$_GET['filename'];
			$tam = getimagesize(sitepath . "public/images/".$this->Data['Image']);
			$this->Data['MaxZoom']=$tam[0];
			//Breadcrumb
			$this->BreadCrumb['Editor']='';
			$this->Data['ActionForm']=$this->folderlink . "/" . $this->post_prefix . "images_editor_post_first";
			$this->Data['ReturnForm']=$_SERVER['HTTP_REFERER'];
			$this->LoadTemplate('image_editor.tpl.php');
			exit;
		} 
		
		if ($action=="editor_post_first") {
			$this->EditImage($_POST);
			//hay que añadir esto para las primeras imágenes de tablas que no son principales
			if($_POST['Extra_images_Table']){ $annadir=$_POST['Extra_images_Table']."_"; } else { $annadir = ''; }
			//recupera un registro de la tabla imágenes
			$this->return=$this->folderlink . "/" . $this->post_prefix . "edit/id/" . $_POST['Extra_images_IDFather'];
			if (isset($_POST['System_Return'])) {
				if ($_POST['System_Return']!="") {
					$this->return=$_POST['System_Return'];
				}
			}			
			header("Location: " . $this->return);
			exit;
		} 
		
		//Acciones para el proceso retardado...
		if ($action=="delayed") {
			if ( !defined('InFrontEnd') ) { define('InFrontEnd', dirname(__FILE__) . '../../../'); }
			$this->HeadTitle="Por favor, espere...";
			$this->TemplatePostScript=$_SESSION['XtraImagesPostScript'];
			require_once(sitepath . 'lib/extras/image_process.tpl.php');
		}
		
		if ($action=="post_delayed") { echo $this->PostItemDelayed(); }		
		
		if ($action=="new") { $this->NewSingleItem(); }
		
		parent::Run($action);

	}		
}
?>