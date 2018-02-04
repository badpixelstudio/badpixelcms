<?php
require_once("common.php");
require_once("functions.php");
session_start();
$conexion=null;
define("siteTitle", "Instalación de paquetes");
define("siteCopyright", "&copy 2003-" . date("Y") . " BadPixel Studios");

class MasterUpdater {
	var $username="";
	var $userID=0;
	var $useravatar="";
	var $MainMenu=array();
	var $BreadCrumb=array(); 
	var $title="Instalación de paquetes";
	var $text = '';
	var $error = '';
	var $FormContent=array();
	var $FormHiddenContent=array();
	var $TemplateLoadScript="";
	var $TemplatePostScript="";
	var $TemplateMethodScript="POST";

	function __construct() {
		define("sitePanelMinResources", false);
	}

	function Welcome() {
		if (! is_file(sitepath . "public/updates/process.list")) {
			header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/modules/error/" . urlencode(base64_encode(_("No se ha generado un script de actualización válido")))); exit;
		}
		$this->GetList();
		$this->TemplateLoadScript="updater.js";
		require_once("../templates/gestion/update.tpl.php");
	}

	function GetList() {
		$contents=file_get_contents("updates/process.list");
		if ($contents!==false) {
			$files=explode("\n\r", $contents);
			$this->files=$files;
			$this->filesCount=count($files);
		} else {
			$this->files=false;
			$this->filesCount=0;
		}
	}

	function DownloadFile() {
		$archivo=null;
		if (isset($_GET['url'])) { 
			$archivo=$_GET['url']; 
			$guardar=basename($archivo);
			CopyExternalResource($archivo,"updates/" . $guardar);
			if (is_file("updates/" . $guardar)) {
				return 1;
			}
		}
		return 0;
	}

	function UnZipFile() {
		$archivo=null;
		$method="ins";
		if (isset($_GET['url'])) { $archivo=$_GET['url']; }
		if (isset($_GET['type'])) { $method=$_GET['type']; }
		if ($archivo!=null) {
			$archivo=basename($archivo);
			if (is_file("updates/" . $archivo)) {
				$zip = new ZipArchive;
				if ($zip->open("updates/" . $archivo) === true) {
					$zip->extractTo("updates");
					if ($method="upd") {
					    for($i = 0; $i < $zip->numFiles; $i++) {	
					    	$filename = $zip->getNameIndex($i);
		        			$fileinfo = pathinfo($filename);
		        			if (strpos($fileinfo['basename'], '.class.php')!==false) {
		        				unlink("updates/" . $fileinfo['dirname'] . "/" . $fileinfo['basename']);
		        			}
		        		}
		        	}
		        	$zip->close();
		        	unlink("updates/" . $archivo);
		        	return 1;
				}
			}
		}
		return 0;
	}

	function UpgradeFiles() {
		$_SESSION['ItsUpdated']="";
		unset($_SESSION['ItsUpdated']);
		$this->getDirContents(sitepath . "public/updates");
		DeleteFolder(sitepath . "public/updates");
		return 1;
	}

	function getDirContents($dir){
	    $files = scandir($dir);
	    foreach($files as $key => $archivo){
	    	$original=$dir . "/" . $archivo;
	    	$destino_folder=sitepath . substr($dir, strpos($dir, "public/updates")+strlen("public/updates/"));
	    	$destino=$destino_folder . "/" . $archivo;
	    	//echo "copy " . $original . " --> " . $destino . "<br>";
	    	if (is_file($original)) {
	    		$restore_perms=fileperms($destino_folder);
	    		chmod($destino_folder, 0777);
	    		copy($original,$destino);
	    		chmod($destino_folder,$restore_perms);
	    	}
	    	if (is_dir($original)) {
	    		if($archivo != "." && $archivo != "..") {
	    			if (! is_dir($destino)) {
		    			echo "mkdir " . $destino . "<br>";
		    			$restore_perms=fileperms($destino_folder);
		    			chmod($destino_folder, 0777);
		    			mkdir($destino);
		    			chmod($destino_folder,$restore_perms);
		    		}
		    		$this->getDirContents($original);
	    		}
	    	}
	    }
	}


	function GetBreadcrumb($include_ul=true) {
		$class="previous";
		$total=count($this->BreadCrumb);
		$i=0; 		
		if ($include_ul) { echo '<ul class="page-breadcrumb breadcrumb">'; }
		foreach ($this->BreadCrumb as $item=>$url){ 
			$i++;
			echo '<li>';
			if ($i==1) { echo '<i class="fa fa-home"></i>';}
			echo '<a href="' . $url .'">';
			echo $item;
			echo '</a>';
			if($i!=$total){
				echo '<i class="fa fa-angle-right"></i>';
			} 
			echo '</li>';
			
		}
		if ($include_ul) { echo '</ul>'; }
	}

	function Run($action="") {
		if ($action=="") {
			$action="index";
			if (isset($_GET['action'])) { $action=$_GET['action']; }
		}
		if ($action=="index") { $this->Welcome(); }
		if ($action=="download") { echo $this->DownloadFile(); }
		if ($action=="unzip") { echo $this->UnZipFile(); }
		if ($action=="upgradefiles") { echo $this->UpgradeFiles(); }
	}
}
?>