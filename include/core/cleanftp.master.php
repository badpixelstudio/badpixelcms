<?php
require_once(sitepath . "include/core/core.class.php");
require_once(sitepath . "lib/minimizer/functions.php");

class MasterUtil_CleanFTP extends Core{
	
	var $version=false;	
	
	//Inicializamos valores por defecto
	var $title = 'Limpieza del FTP';
	var $class = 'cleanftp';
	var $module = 'core';
	var $table = '';
	var $default_folders="avatar,avatar_original,banners,files,images,medium,mobile,sponsors,thumbnails";

	
	//constructor
	function __construct($values) {
		parent::__construct($values);  
		$title=$this->GetModuleName($this->class);
		if ($title!=$this->class) { $this->title=$title; }
		//breadcrumb
		$this->BreadCrumb[$this->title]=$_SERVER['PHP_SELF'];
	}

	function ListAdmItems() {
		$this->PrepareForm();		
	}
	
	function PrepareForm() {
		$this->BreadCrumb['Limpiar carpetas']='';
		$in_block=$this->AddFormBlock('CSS');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Carpetas a limpiar","FieldName":"Folders","Value":"' . addslashes($this->default_folders) . '", "Help":"Nombre de la carpeta de la plantilla en \templates\"", "Required": true}');
		$this->TemplatePostScript=$this->module . "--" . $this->class . "/post";
		$this->LoadTemplate($this->module . '_edit.tpl.php');

	}

	function PostItem($redirect=true) {
		echo "<h1>Realizando limpieza de las carpetas " . $_POST['Folders'] . "</h1>";
		$contar=0;
		$carpetas=explode(",",$_POST['Folders']);
		if (count($carpetas)>0) {
			foreach($carpetas as $carpeta) {
				$carpeta=trim($carpeta);
				if (is_dir(sitepath . "public/" . $carpeta)) {
					$carpeta=sitepath . "public/" . $carpeta;
					$directorio=opendir($carpeta);
					while ($archivo = readdir($directorio)) {
						set_time_limit(300);
						if (is_file($carpeta . '/' . $archivo)) {
							if (! $this->CheckFileDependencies($archivo)) {
								echo "Borrando " . $carpeta . "/" . $archivo . "<br>";
								ob_flush();
								unlink($carpeta . "/" . $archivo);
								$contar++;
							}	
						}
					}
				}
			}
		}
		echo "<hr>";
		echo "<h1>Finalizó el proceso con " . $contar . " archivos eliminados</h1>";
		echo "<a href='" . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "--" . $this->class . "/list/text/" . urlencode(base64_encode("Se han eliminado " . $contar . " archivos no vinculados a ningún elemento")) . "'>Volver al panel de gestión</a>";
	}

	function CheckFileDependencies($archivo) {
		$TablesCount=parent::$db->GetDataListFromSQL("SHOW TABLES",$Tables);
		if ($TablesCount>0) {
			foreach($Tables as $table) {
				$table=$table['Tables_in_' . dbname];
				unset($TableStru);
				$TableStruCount=parent::$db->GetDataListFromSQL("SHOW FIELDS FROM " . $table . " WHERE Type LIKE 'varchar%'",$TableStru);
				if ($TableStruCount>0) {
					$sql="SELECT ID FROM " . $table . " WHERE (ID IS NULL";
					foreach($TableStru as $field) {
						$sql.=" OR " . $field['Field'] . '="' . utf8_encode($archivo) . '" COLLATE utf8_general_ci';
					}
					$sql.=")";
					//echo $sql . "<br>";
					$found=parent::$db->GetDataFieldFromSQL($sql,"ID");
					if ($found!==false) {
						return true;
					}
				}
			}
		}
		return false;
	}
}
?>