<?php
require_once(sitepath . "include/core/core.class.php");
require_once(sitepath . "include/backup/backup.config.php");
require_once(sitepath . "lib/mysql/backup_restore.class.php");

class MasterBackup extends Core{
	var $title = 'Copias de Seguridad';
	var $class = 'backup';
	var $module = 'backup';
	var $typemodule='system';
	var $table="";
	var $file = '';
	var $folder = '';
	var $version="3.0.0.2";

	function __construct($values) {
		parent::__construct($values);  
		$title=$this->GetModuleName($this->class);
		if ($title!=$this->class) { $this->title=$title; }
		if (isset($values['file'])) { $this->file=urldecode($values['file']); }
		$this->conf = new ConfigBackup();
		$this->folder=sitepath . "public/" . $this->conf->Export('DefaultFolder');
		$this->BreadCrumb[$this->title] = $this->module;
	}

	
	function ReadDir() {
		$contador=0;
		if (! is_dir($this->folder)) { mkdir($this->folder); }
		$directorio=opendir($carpeta=$this->folder);	
		$Elementos=array();   
		while ($archivo = readdir($directorio)) {
			if (is_file($carpeta . '/' . $archivo)) {
				$Elementos[$contador+1]=$archivo;
				$contador++;
			}
		}
		$this->ItemsCount=count($Elementos);
		if ($this->ItemsCount>0) {
			foreach($Elementos as $elemento) { 
				$size=filesize($this->folder . "/" . $elemento);
				unset($dat);
				$dat['File']=$elemento; 
				$dat['Type']="Database";
				if (strpos($elemento,".sql")===false) { $dat['Type']="Contents"; }
				$dat['Size']=formatSizeUnits($size);
				$this->Items[]=$dat;
			}
		}
		closedir($directorio);
		return $contador;	
	}	
	
	function ListFolder() {
		$this->BreadCrumb['Archivos'] = '';
		$this->ReadDir();	
		$this->PrepareTableList();
		$this->LoadTemplate($this->module . '_file_list.tpl.php');	
	}

	function NewItem($default_values=false) {
		$this->BreadCrumb['Crear']='';
		$this->Data['Name']=date('Y-m-d H:i') . " " . _("Copia de seguridad");
		$this->PrepareForm();
		$this->LoadTemplate('edit.tpl.php');
	}

	function CreateBackup($redirect=true) {
		if ($_POST['Form_Type']=="Database") { $this->CreateBackupDataBase(); }
		if ($_POST['Form_Type']=="Contents") { $this->CreateBackupContents("public"); }
		if ($_POST['Form_Type']=="All") { 
			$this->CreateBackupDataBase(); 
			$this->CreateBackupContents();
		}
		if ($redirect) { header("Location:" . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "/text/" . urlencode(base64_encode(_("Se ha creado la copia de seguridad")))); }
	}

	function CreateBackupDataBase($redirect=true) {
		$_POST['Form_Name'].="-DB-" . $dbname;
		$filename=stripfilename($_POST['Form_Name']) . ".sql";
		$newImport = new backup_restore(dbserver,dbname,dbuser,dbpsw);
		$newImport->path=$this->folder;
		$newImport->set_database_dump_filename($filename);
		$content=$newImport->backup();
		$zip = new ZipArchive;
		if ($zip->open(sitepath . "public/backup/" . $filename . ".zip", ZipArchive::CREATE)===TRUE) {
			$zip->addFile(sitepath . "public/backup/" . $filename, $filename);
			$zip->close();
			unlink(sitepath . "public/backup/" . $filename);
		}
	}

	function CreateBackupContents($subfolder="") {
		$filename=sitepath . "public/backup/" . str_replace(".sql","",stripfilename($_POST['Form_Name'])) . ".zip";
		$to_backup=sitepath;
		if ($to_backup!="") { $to_backup.="/" . $subfolder; }
		$lng=strlen($to_backup);
		$lastchar=substr($to_backup,$lng-1);
		if (($lastchar=="/") or ($lastchar=="\\")) { $to_backup=substr($to_backup, 0,$lng-1); }
		$zip = new ZipArchive;
		if ($zip->open($filename, ZipArchive::CREATE)===TRUE) {
			$files = new RecursiveIteratorIterator(
			    new RecursiveDirectoryIterator($to_backup),
			    RecursiveIteratorIterator::LEAVES_ONLY
			);
			foreach ($files as $name => $file){
				if (!$file->isDir()){
			        // Get real and relative path for current file
			        $filePath = $file->getRealPath();
			        $relativePath = substr($filePath, strlen(sitepath)); //strlen($to_backup)
			        // Add current file to archive
        			$zip->addFile($filePath, $relativePath);
    			}
    		}
			$zip->close();
		}
	}
	
	function DeleteFile() {
		//echo $this->folder . "/" . $this->file;
		if (is_file($this->folder . "/" . $this->file)) {
			unlink($this->folder . "/" . $this->file);
			echo 1;
		} else {
			echo 0;
		}
	}
	
	function Download() {
		header("Content-disposition: attachment; filename=".$this->file);
	    header("Content-Type: application/force-download");
	    //header("Content-Transfer-Encoding: application/zip;\n");
	    header("Pragma: no-cache");
	    header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
	    header("Expires: 0");
	    echo file_get_contents($this->folder . "/" . $this->file);
	}

	function Restore() {
		if (strpos($this->file,".sql")!==false) { 
			$this->RestoreDataBase(); 
		} else {
			$this->RestoreContents();
		}
	}

	function RestoreDataBase() {
		if (! isset($_POST['Form_Confirm'])) {
			$this->BreadCrumb["Restaurar"] = "";
			$in_block=$this->AddFormBlock('Restaurar');
			$this->AddFormContent($in_block,'{"Type":"text","Text":"Archivo a restaurar","FieldName":"Form_File","Value":"' . addcslashes($this->file,'\\"') . '","Readonly": true}');
			$this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Confirmar la restauración de la base de datos","FieldName":"Form_Confirm","Value":"0","Required": true}');
			$this->TemplatePostScript=$this->module . "/restore/file/" . $this->file; 
			$this->LoadTemplate('edit.tpl.php');
		} else {
			//Si el archivo está comprimido en zip, previamente lo descomprimimos...
			$delete=false;
			if (strpos($this->file,'.zip')!==false) {
				$zip = new ZipArchive;
				if ($zip->open($this->file) === true) {
					$extracted=basename(str_replace(".zip", "", $this->file));
					if ($zip->extractTo(sitepath . "public/backup",array($extracted))) {
						$this->file=$extracted;
						$delete=true;
					} else {
						header("Location:" . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "/text/" . urlencode(base64_encode("El archivo no contiene una base de datos válida")));
						die;
					}
				}
			}
			$newImport = new backup_restore(dbserver,dbname,dbuser,dbpsw);
			$newImport->path=$this->folder;
			$message = $newImport -> restore ($this->file);
			if ($delete) { unlink(sitepath . "public/backup/" . $this->file); }
			header("Location:" . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "/text/" . urlencode(base64_encode(_($message))));
		}
	}

	function RestoreContents() {
		if (! isset($_POST['Form_Confirm'])) {
			$this->BreadCrumb["Restaurar"] = "";
			$in_block=$this->AddFormBlock('Restaurar');
			$this->AddFormContent($in_block,'{"Type":"text","Text":"Archivo a restaurar","FieldName":"Form_File","Value":"' . addcslashes($this->file,'\\"') . '","Readonly": true}');
			$this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Confirmar la restauración de todos los contenidos","FieldName":"Form_Confirm","Value":"0","Required": true}');
			$this->TemplatePostScript=$this->module . "/restore/file/" . $this->file; 
			$this->LoadTemplate('edit.tpl.php');
		} else {
			if (strpos($this->file,'.zip')!==false) {
				$zip = new ZipArchive;
				if ($zip->open($this->file) === true) {
					$zip->extractTo(sitepath);
					$zip->close();
					header("Location:" . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "/text/" . urlencode(base64_encode("Se ha restaurado el contenido desde la copia de seguridad")));
				} else {
					header("Location:" . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "/text/" . urlencode(base64_encode("No se ha podido restaurar el contenido de la copia de seguridad")));
				}
			} else {
				header("Location:" . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "/text/" . urlencode(base64_encode("El archivo no contiene una copia de seguridad válida")));
			}
		}
	}

	function UploadFiles() {
		$this->AddMainMenu('Volver',$this->module);
		$in_block=$this->AddFormBlock('Cargar archivos');
		$this->AddFormContent($in_block,'{"Type":"upload-multiple","Text":"Archivos SQL (*.sql, *.zip, *.gz)","FieldName":"Form_Files","Value":"","UploadType": "file", "UploadItem":"first", "Extensions": "sql,zip,gz", "PreviewFolder": "public/temp"}');
		$this->TemplatePostScript=$this->module . "/process_uploads";
		$this->LoadTemplate($this->module . '_edit.tpl.php');
	}

	function ProcessFiles() {
		if (is_array($_POST['Form_Files'])) {
			foreach($_POST['Form_Files'] as $file) {
				//Comprobamos si es un archivo comprimido...
				$extension = preg_split("/\./", strtolower($file)) ;
				$n = count($extension)-1;
				$extension = $extension[$n];
				if (strtolower($extension)=="sql") {
					copy(sitepath . "public/temp/" . $file, $this->folder . "/" . $file);
					chmod($this->folder . "/" . $file, 0777);
				}
				if (strtolower($extension)=="zip") {
					$zip = new ZipArchive;
					$zip->open(sitepath . "public/temp/" . $file);
					$zip->extractTo($this->folder);
				}
				if (strtolower($extension)=="gz") {
					$destino=$this->folder . "/" . substr($file,0,strpos($file,$extension)-1) . ".sql";
					$string = implode("", gzfile(sitepath . "public/temp/" . $file));
					$fp = fopen($destino, "w");
					fwrite($fp,$string);
					fclose($fp);
				}
				unlink(sitepath . "public/temp/" . $file);
			}
		}
		header("Location: " . $this->module);
	}

	function PrepareTableList() {
		$this->AddMainMenu('Crear',$this->module . '/new');
		$this->AddMainMenu('Cargar archivos...',$this->module . '/upload');
		$this->AddTableContent('Archivo','data','{{File}}','',$this->module . '/download/file/{{File}}');
		$this->AddTableContent('Tipo','data','{{Type}}','',$this->module . '/download/file/{{File}}');
		$this->AddTableContent('Tamaño','data','{{Size}}','',$this->module . '/download/file/{{File}}');
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block,'Descargar...',$this->module . '/download/file/{{File}}');
		$this->AddTableOperations($in_block,'Restaurar...',$this->module . '/restore/file/{{File}}');
		$this->AddTableOperations($in_block);
		$this->AddTableOperations($in_block,'Eliminar',$this->module . '/delete/file/{{File}}');
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('Backup');
		$this->AddFormContent($in_block,'{"Type":"combo-json","Text":"Crear una copia de seguridad de","FieldName":"Form_Type","Value":"Database","JsonValues": {"Database": "Base de datos","Contents":"Contenidos del FTP","All":"Todo (incluyendo CMS)"}}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Nombre","FieldName":"Form_Name","Value":"' . addcslashes($this->Data['Name'],'\\"') . '","Required": true}');
	}
	
	function RunAction() {
		if($this->action=="list") { $this->ListFolder(); }
		if($this->action=="new") { $this->NewItem(); }
		if($this->action=="post") { $this->CreateBackup(); }
		if($this->action=="process") { $this->Process(); }
		if($this->action=="download") { $this->Download(); }
		if($this->action=="restore") { $this->Restore(); }
		if($this->action=="delete") { $this->DeleteFile(); }
		if($this->action=="upload") { $this->UploadFiles(); }
		if($this->action=="process_uploads") { $this->ProcessFiles(); }
	}
}
?>