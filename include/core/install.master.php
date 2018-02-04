<?php
session_start();
$conexion=null;

define("siteTitle", "Programa de instalación");
define("siteCopyright", "&copy 2003-" . date("Y") . " BadPixel Studios");
define("siteDebugActive", false);


class MasterInstaller {
	var $username="";
	var $userID=0;
	var $useravatar="";
	var $MainMenu=array();
	var $BreadCrumb=array(); 
	var $title="Programa de instalación";
	var $text = '';
	var $error = '';
	var $FormContent=array();
	var $FormHiddenContent=array();
	var $TemplateLoadScript="";
	var $TemplatePostScript="";
	var $TemplateMethodScript="POST";
	var $AppVersion="InstallerApp 4";

	function __construct() {
		$this->BreadCrumb['Bienvenida']=$_SERVER['PHP_SELF'] . "?action=index";
		if (isset($values['text'])) { $this->text=$values['text']; }
		if (isset($values['error'])) { $this->error=$values['error']; }
		define("sitePanelMinResources", false);
	}

	function SimulateCommon() {
		define ("sitealiasfolder", "");
		$fld=$_SERVER['DOCUMENT_ROOT'];
		if (isset($_SERVER['CONTEXT_DOCUMENT_ROOT'])) { $fld=$_SERVER['CONTEXT_DOCUMENT_ROOT']; }
		$this_file=str_replace("\\", "/", __DIR__);
		$route=str_replace($fld, "", $this_file);
		$route=str_replace("include/core","",$route);
		$protocol="http://";
		if (isset($_SERVER['HTTPS'])) { $protocol="https://"; }

		define ("sitefolder", $route);
		define ("siteprotocol", $protocol);
		define ("sitedomain", $_SERVER['HTTP_HOST'] . sitealiasfolder . sitefolder);
		define ("sitepath", $fld . sitefolder);
		define ("sitePanelFolder","install");
	}

	function Connect() {
		global $conexion;
		$common_valid=false;
		if (is_file('../include/core/common.php')) {
				require_once('../include/core/common.php');
				$common_valid=true;
				$conexion = @mysqli_connect(dbserver, dbuser, dbpsw) or $common_valid=false;
				if ($common_valid) { mysqli_select_db($conexion,dbname) or $common_valid=false; }
				if ($common_valid) { @mysqli_query($conexion,"SET NAMES 'utf8'"); }
			}	
		return $common_valid;
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

	function CreateConfig() {
		//Generamos el archivo de configuración
		$conf=file_get_contents('../include/core/common.sample.php');
		$conf=str_replace('{{dbserver}}', $_POST['dbserver'],$conf);
		$conf=str_replace('{{dbname}}', $_POST['dbname'],$conf);
		$conf=str_replace('{{dbuser}}', $_POST['dbuser'],$conf);
		$conf=str_replace('{{dbpsw}}', $_POST['dbpsw'],$conf);
		$conf=str_replace('{{backend}}', $_POST['backend'],$conf);
		$conf=str_replace('{{folder}}', $_POST['folder'],$conf);
		$conf=str_replace('{{protocol}}', $_POST['protocol'],$conf);
		$archivo=tempnam(sys_get_temp_dir(), 'bpcmscommon.tmp');
		$_SESSION['tmp_conf']=$archivo;
		$saveconfig=file_put_contents($archivo,$conf);
		return $archivo;
	}

	function SaveConfig($temp) {
		$conf=file_get_contents($temp);
		$saved=file_put_contents('../include/core/common.php', $conf);
		if ($saved===false) {
			return false;
		}
		return true;
	}

	function DownloadConfig($temp) {
		header("Content-disposition: attachment; filename=common.php");
		header("Content-type: application/octet-stream");
		readfile($temp);
	}

	function AddFormBlock($block) {
		unset($data);
		$data['Name']=$block;
		$data['Fields']=array();
		$this->FormContent[]=$data;
		return count($this->FormContent)-1;
	}

	function AddFormContent($block,$options) {
		$options = preg_replace('/\s+/S', " ", $options);
		$options=json_decode($options,true);
		$opts=array();
		if (count($options)>0) {
			foreach($options as $k=>$v) {
				$opts[strtolower($k)]=$v;
			}
		}
		if (isset($options['Type'])) {
			if ($options['Type']=="upload") { $opts['module']=$this->module; }
		}
		$this->FormContent[$block]['Fields'][]=$opts;
	}

	function AddFormHiddenContent($field,$value="") {
		unset($data);
		$data['type']="hidden";
		$data['fieldName']=$field;
		$data['value']=$value;
		$this->FormHiddenContent[]=$data;
	}

	function Welcome() {
		$this->SimulateCommon();
		$in_block=$this->AddFormBlock('Bienvenida');
		$html='<p>Este asistente te ayudará a instalar BadPixel CMS en tu servidor.';
		$html.='<p>Asegúrate, antes de continuar, que no ejecutas este programa sobre una instalación existente. Si lo haces puede que algunos datos se pierdan definitivamente.</p>';
		$this->AddFormContent($in_block,'{"Type":"inline","Text":"Bienvenido al asistente de instalación","HTML": "' . addcslashes($html,'\\"') . '", "FieldID":"Header"}');
		$this->TemplatePostScript="?action=config";
	}

	function DefineConnection() {
		$this->SimulateCommon();
		if (! isset($_POST['dbname'])) {
			$this->BreadCrumb['Configurar conexión']=$_SERVER['PHP_SELF'] . "?action=config";
			$fld=$_SERVER['DOCUMENT_ROOT'];
			if (isset($_SERVER['CONTEXT_DOCUMENT_ROOT'])) { $fld=$_SERVER['CONTEXT_DOCUMENT_ROOT']; }
			$this_file=str_replace("\\", "/", __DIR__);
			$route=str_replace($fld, "", $this_file);
			$route=str_replace("include/core","",$route);
			$protocol="http://";
			if (isset($_SERVER['HTTPS'])) { $protocol="https://"; }
			$in_block=$this->AddFormBlock('Conexión');
			$html='<p>El primer paso para instalar el CMS es disponer de conexión con el Servidor de Bases de Datos.</p>';
    		$html.='<p>Debes crear una nueva base de datos en tu servidor. En la mayoría de ocasiones este proceso se realiza a través del Panel de Administración del Servidor o de la herramienta phpMyAdmin.</p>';
    		$html.='<p>Cumplimenta el formulario con los datos solicitados para continuar</p>';
			$this->AddFormContent($in_block,'{"Type":"inline","Text":"Conectar a la base de datos","HTML": "' . addcslashes($html,'\\"') . '", "FieldID":"Header"}');
			$this->AddFormContent($in_block,'{"Type":"text","Text":"Servidor de bases de datos","FieldName":"dbserver","Value":"","Required": true, "Help": "En la mayoría de los casos es \'localhost\' "}');
			$this->AddFormContent($in_block,'{"Type":"text","Text":"Nombre de la base de datos","FieldName":"dbname","Value":"","Required": true, "Help": "La base de datos debe existir en el servidor"}');
			$this->AddFormContent($in_block,'{"Type":"text","Text":"Usuario de la base de datos","FieldName":"dbuser","Value":"","Required": true, "Help": "Necesario para autenticarse en el servidor"}');
			$this->AddFormContent($in_block,'{"Type":"password","Text":"Contraseña de la base de datos","FieldName":"dbpsw","Value":"", "Help": "Contraseña necesaria para autenticarse en el servidor"}');
			$this->AddFormContent($in_block,'{"Type":"text","Text":"Ruta de la instalación (generalmente este parámetro se autodetecta)","FieldName":"folder","Value":"' . addcslashes($route, '\\"') . '","Required": true, "Help": "Carpeta donde se instalará el CMS"}');
			$this->AddFormContent($in_block,'{"Type":"combo-json","Text":"Protocolo HTTP/HTTPS (generalmente este parámetro se autodetecta)","FieldName":"protocol","Value":"' . $protocol . '","Required": true, "JsonValues": {"http://": "Protocolo HTTP (http://...)", "https://": "Protocolo seguro HTTPS (https://...)"}}');
			$this->AddFormContent($in_block,'{"Type":"text","Text":"Ruta del panel de gestión","FieldName":"backend","Value":"panel-gestion","Required": true, "Help": "Carpeta donde se ubicará el panel de gestión"}');
			$this->TemplatePostScript="?action=config";
		} else {	
			$archivo=$this->CreateConfig();
			//Tratamos de guardar el archivo en la ruta por defecto...
			$status=$this->SaveConfig($archivo);
			//$status=false;
			if ($status===false) { 
				$this->BreadCrumb['Configurar conexión']=$_SERVER['PHP_SELF'] . "?action=config";
				$in_block=$this->AddFormBlock('Conexión');
				$html='<p>Hemos intentado guardar el archivo de configuración en el servidor, pero la configuración de permisos de carpeta impide que el programa de instalación lo haga de forma automática.</p>';
        		$html.='<p>En unos segundos se descargará automaticamente el archivo que debes subir a la carpeta "include/core" mediante un programa FTP. Si el archivo no se descarga automáticamente haz <a href="?action=download">clic aquí</a>.</p>';
        		$html.='<p>Una vez cargado vuelve a <a href="index.php?action=config">iniciar el programa de instalación</a>.</p>';
				$this->AddFormContent($in_block,'{"Type":"inline","Text":"Cargar archivo de configuración","HTML": "' . addcslashes($html,'\\"') . '", "FieldID":"Header"}');
				$this->TemplatePostScript="";
			} else {
				header("Location: " . $_SERVER['PHP_SELF'] . "?action=config"); exit;
			}
		}
	}

	function DefineAdmin() {
		$this->BreadCrumb['Configurar conexión']=$_SERVER['PHP_SELF'] . "?action=config";
		$this->BreadCrumb['Definir administrador']=$_SERVER['PHP_SELF'] . "?action=config";
		$in_block=$this->AddFormBlock('Administrador');
		$html='<p>Para poder utilizar el CMS es necesario disponer de un usuario registrado en el sistema con permisos de Administrador.</p>';
		$html.='<p>Define en esta página el nombre de usuario, la contraseña y un correo electrónico vinculado a la cuenta que usaremos para recordarte tus datos de acceso en caso de olvido o pérdida.</p>';
		$this->AddFormContent($in_block,'{"Type":"inline","Text":"Crear cuenta de Administrador","HTML": "' . addcslashes($html,'\\"') . '", "FieldID":"Header"}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Nombre de usuario (se recomienda no usar espacios, acentos ni caracteres especiales)","FieldName":"user","Value":"","Required": true, "Help": "Evite los usuarios como admin, root, etc..."}');
		$this->AddFormContent($in_block,'{"Type":"email","Text":"Correo electrónico","FieldName":"email","Value":"","Required": true, "Help": "Utiliza una dirección válida"}');
		$this->AddFormContent($in_block,'{"Type":"password-retype","Text":"Contraseña de acceso","FieldName":"passw","Value":"", "Help": "Haz tu contraseña segura incorporando mayúsculas, minúsculas, números y algún caracter especial","Required":true,"MinLength":"8"}');
		if (is_file(sitepath . "include/core/sql/example_data.sql")) {
			$this->AddFormContent($in_block,'{"Type":"checkbox","Text":"¿Instalar datos de demostración?","FieldName":"exampledata","Value":"1"}');
		}
		$this->TemplatePostScript="../install/?action=dump";
	}

	function DumpData($example_data=false) {
		require_once("../include/core/common.php");
		require_once(sitepath . "lib/mysql/backup_restore.class.php");
		require_once(sitepath . "include/core/database.class.php");
		require_once(sitepath . "include/core/functions.php");
		$db = DBase::getInstance();
		$newImport = new backup_restore(dbserver,dbname,dbuser,dbpsw);
		$newImport->path=sitepath . "include/core/sql";
		$message = $newImport -> restore ("install.sql");

		$dump=sitepath . "include/core/sql/install.sql";
		if (is_file($newImport->path . "/install.sql")) {
			$newImport = new backup_restore(dbserver,dbname,dbuser,dbpsw);
			$newImport->path=sitepath . "include/core/sql";
			$message=$newImport->restore("install.sql");
			if ($example_data) {
				$message=$newImport->restore("example_data.sql");
			}
			//Buscamos el usuario con email facilitado...
			$sql="SELECT * FROM users WHERE email='" . $_POST['email'] . "'";
			$result = $db->GetDataRecordFromSQL($sql);
			$Data['System_Action']="new";
			$Data['System_ID']=-1;
			if ($result!==false) {
				$Data['System_Action']="edit";
				$Data['System_ID']=$result['ID'];
			}
			$Data['Form_RegCode']=$_POST['user'] . KeyGen(30);
			$Data['Form_UserName']=$_POST['user'];
			$Data['Form_Email']=$_POST['email'];
			$Data['Form_PassW']=md5($_POST['passw']);
			$Data['Form_Rol']=99;
			$Data['Form_Active']=1;
			$db->PostToDatabase('users',$Data);
			header("Location: index.php?action=finish");
		} else {
			$this->BreadCrumb['Configurar conexión']=$_SERVER['PHP_SELF'] . "?action=config";
			$this->BreadCrumb['Definir administrador']=$_SERVER['PHP_SELF'] . "?action=start";
			$in_block=$this->AddFormBlock('Error');
			$html='<p>Se ha intentado cargar la base de datos inicial al servidor MySQL pero se ha producido un error.</p>';
			$html.='<p>Asegúrate de que existe el archivo "install.sql" en la carpeta "include/core/sql" e intenta de nuevo ejecutar la instalación.</p>';
			$this->AddFormContent($in_block,'{"Type":"inline","Text":"Error al cargar la base de datos","HTML": "' . addcslashes($html,'\\"') . '", "FieldID":"Header"}');
			$this->TemplatePostScript="";
		}
	}

	function Finish() {
		require_once("../include/core/common.php");
		$this->BreadCrumb['Configurar conexión']=$_SERVER['PHP_SELF'] . "?action=config";
		$in_block=$this->AddFormBlock('Finalizado');
		$html='<p>Es conveniente que borres la carpeta "install" mediante un programa cliente FTP para evitar problemas de seguridad.</p>';
		$html.='<p>Ahora accede al panel de gestión, desde la opción de Configuración puedes establecer los datos básicos de la web en el módulo "Core".</p>';
		$html.='<p>Recuerda que el CMS reconoce e instala los módulos que subas mediante FTP al servidor y puedes configurar los parámetros de cada uno de ellos desde la opción "Configuración"</p>';
		$html.='<p>Gracias por utilizar BadPixel CMS.</p>';
		$this->AddFormContent($in_block,'{"Type":"inline","Text":"Instalación finalizada","HTML": "' . addcslashes($html,'\\"') . '", "FieldID":"Header"}');
		$this->TemplatePostScript=siteprotocol . sitedomain . sitePanelFolder;
	}

	function Run($action="") {
		if ($action=="") {
			$action="index";
			if (isset($_GET['action'])) { $action=$_GET['action']; }
		}
		if ($action=="index") { $this->Welcome(); }
		if ($action=="config") {
			//Comprobamos el estado de la configuración...
			if (! $this->Connect()) { 
				$action="connect"; 
			} else {
				$action="start";
			}
		}
		if ($action=="connect") { $this->DefineConnection(); }
		if ($action=="download") {
			$this->DownloadConfig($_SESSION['tmp_conf']);
			exit;
		}

		if ($action=="start") { $this->DefineAdmin(); }
		if ($action=="dump") { 
			$example_data=false;
			if(isset($_POST['exampledata'])) { $example_data=true; }
			$this->DumpData($example_data); }
		if ($action=="finish") { $this->Finish(); }
		require_once("../templates/gestion/install.tpl.php");
	}

}
?>