<?php
if (! isset($TimeStart)) { $TimeStart=microtime(true); }
session_start();
error_reporting(E_ALL & ~E_STRICT);
require_once(sitepath . "include/core/database.class.php");
require_once(sitepath . "include/core/core.config.php");
require_once(sitepath . "include/core/functions.php");

	
class MasterCore{
	var $module="core";
	var $class= 'core';
	var $typemodule="";
	var $siteTitle = "";
	var $sitedomain = "";
	var $sitedescrip = "";
	var $sitemetatags = "";
   	var $registrocode = '';
	var $username = '';
	var $userID = 0;
	var $userLevel = 0;
	var $useremail = '';
	var $useravatar= '';
	var $rememberme = '';
	var $userlang = '';
	var $userMP=0;
	var $template = '';
	var $table_prefix="";
	var $tables_required=array("modules_installed");
	var $IndependentSubModules=array();
	var $version="4.4.1802.0";
	var $AppVersion="4.4.1802.0";
	
	var $businessID = 0;
	var $businessName= "";
	var $EnableBusiness= true;
	var $MultiBusiness= false;
	var $Business='';
	var $AccessTokenResult=array("Success"=>"0","Status"=>"Error","Message"=>"An active access token must be used.");
	var $AccessTokenValid=false;
	var $MySession="";

	var $MainMenu=array();
	var $TableContent=array();
	var $TableHeader=array();
	var $TableRowConditions=array();
	var $TableOperations=array();
	var $TableMenu=array();
	var $FormContent=array();
	var $FormHiddenContent=array();
	var $TemplateLoadScript="";
	var $TemplatePostScript="";
	var $TemplateMethodScript="POST";
	var $TotalLangs=0;
	var $Langs=array();
	var $TotalLangsAvailables=0;
	var $LangsAvailables=array();
	var $ActualLang="";

	var $ImportDefaultValues="";	//Array con los valores predeterminados de los campos array['Campo']="Valor";
	var $ImportCheckFields="";		//Array con la lista de campos a comprobar previo a importar array("campo-1", ..., "campo-n")
	var $FieldsOfImages="";			//Array con nombres de campo y su relación con la conf. de corte array("Image"=>"ImageOptions", ..., "Image-n"=>"Image-nOptions")
	var $FieldsOfFiles="";			//Array con los campos que almacenan un archivo array("file-1", ..., "file-n");
	var $xtras_prefix="";			//Prefijo a usar en las tablas de los extras
	var $xtras_RunSubClass=false; 	//Establece si las subclases se ejecutan como proceso interno del módulo principal (true) o como proceso independiente (false);
	var $InstallActions=array();	//Añade opciones a modules_installed, pero dependen de la clase principal
	var $InstallAdminMenu=array();	//Añade bloques en el menú del panel de gestión.
	var $xtraimages_options="ImagesOptions";	//nombre de la conf. por defecto de las imagenes extra
	var $permalink_conf="PermalinkFolder";
	var $permalink_action="action=show";
	var $paged=false;				//Establece si el resultado que se devuelve está o no paginado
	var $ItsUpdated="";

	public static $db = '';

	var $title="Sistema";
	var $action = 'list';
	var $id=0;
	var $linkid = 0;
	var $idparent = '0';
	var $page = 1;
	var $offset = 20;
	var $search="";
	var $text = '';
	var $error = '';
	var $view = '';
	var $conf =null;

	var $BreadCrumb = array('Inicio'=>'home');
	var $Permissions = array();
	var $Data = array();
	var $SystemVariables = null;
	
	//constructor
	function __construct($values=null){
		$this->_values=$values;
		//objeto para acceder a la base de datos
		self::$db = DBase::getInstance();
		if (defined('siteBusinessDefault')) { $this->businessID=siteBusinessDefault; }	
		$this->LoadSystemConfig($this->businessID);
		//Reconfiguramos las variables...
		$this->sitetitle = siteTitle;
		$this->businessName= siteTitle;
		$this->sitedomain = sitedomain;
		$this->siteHeadDescription = siteHeadDescription;
		$this->sitemetatags = siteHeadTags;	
		$this->userlang = siteLang;
		$this->template = siteTemplate;
		$this->MySession=session_id();
		$this->TemplatePostScript=$this->module . "/post";
		//load values
		if (isset($values['id'])) { 
			$p=CheckParamValid($values['id'],'id');
			if ($p!==false) { $this->id=$p; }
		}
		if (isset($values['action'])) { $this->action=$values['action']; }
		if (isset($values['view'])) { $this->view=$values['view']; }
		if (isset($values['page'])) { 
			$p=CheckParamValid($values['page'],'integer');
			if ($p!==false) { $this->page=$p; }
		}
		if (isset($values['offset'])) { 
			$p=CheckParamValid($values['offset'],'integer');
			if ($p!==false) { $this->offset=$p; }
			$this->offset=$p; 
		}
		if (isset($values['search'])) { $this->search=$values['search']; }
		if (isset($values['text'])) { $this->text=$values['text']; }
		if (isset($values['error'])) { $this->error=$values['error']; }
		if (isset($values['view'])) { $this->view=$values['view']; }
		if (isset($values['idparent'])) { 
			$p=CheckParamValid($values['idparent'],'integer');
			if ($p!==false) { $this->idparent=$p; }
		}
		if (isset($values['prior'])) { 
			$p=CheckParamValid($values['prior'],'integer');
			if ($p!==false) { $this->linkid=$p; }
		}
		$this->CheckDependencies();
		$CfgTitle=$this->GetModuleName($this->title);
		if ($CfgTitle!=$this->title) { $this->title=$CfgTitle; }
		//Si la configuración impide el uso de Multiempresa o no existe la tabla, desactivamos su uso.
		$this->CheckMultiBusiness();
		//Cargamos el logeo desde Oauth de terceros...	
		require_once(sitepath . "include/users/oauth.php");
		//Cargamos las preferencias almacenadas en $_SESSION
		$this->LoadSessionsProfile();
		//Logeamos desde access_token
		if (siteAPIRequiresOAuthLogin==0) { $this->AccessTokenValid=true; }
		if (isset($_GET['access_token'])) {
			$estado=$this->LoginFromAccessToken($_GET['access_token']);
		}
		//Eliminamos los usuarios que transcurridos 8 días no hayan activado su cuenta...
		$this->DeleteInactiveUsers();
		//Logeamos desde $_SESSION o $_COOKIE
		$this->AutoLoginUser();
		$this->LoadLang();
	}

	//cargar template
	function LoadTemplate($file){
		$this->template="gestion";
		$ruta=sitepath . "templates/gestion/".$file;
		if (! is_file($ruta)) {
			$valido=false;
			if (strpos($file,'_nestable')!==false) { $ruta=sitepath . "templates/gestion/nestable.tpl.php";}
			if (strpos($file,'_list')!==false) { $ruta=sitepath . "templates/gestion/list.tpl.php";}
			if (strpos($file,'_edit')!==false) { $ruta=sitepath . "templates/gestion/edit.tpl.php";}
			if (strpos($file,'_order')!==false) { $ruta=sitepath . "templates/gestion/order.tpl.php";}
		}
		if (is_file($ruta)) { 
			require_once($ruta);
		} else {
			die('Template file ' . $file . ' not found.'); 
		}
	}
	
	//cargar template publico
	function LoadTemplatePublic($file){
		$ruta=sitepath."/templates/".$this->template."/".$file;
		if (is_file($ruta)) {
			require_once($ruta);
		} else {
			die('Template file ' . $file . ' not found');
		}
	}
	
	function GetBusinessPermission() {
	   if ($this->EnableBusiness) {
		   //Cargamos las empresas si esta habilitado el multisitio y no se han cargado con anterioridad...
		   if ((siteMulti) and (!$this->MultiBusiness)) {
		   		if (isset($GLOBALS['Business'])) {
		   			if ($GLOBALS['Business']!==false) {
		   				$this->Business=$GLOBALS['Business'];
		   				$TotalEmpresas=count($this->Business);
		   			} else {
		   				$TotalEmpresas=0;
		   			}
		   		} else {
				   //Recorremos la tabla de usuarios de empresa para cargar las empresas disponibles...
				   	if (($this->userLevel>=siteLevelRootMulti)) {
						//Tiene permiso para todas las empresas, asi que cargamos la lista...
						$TotalEmpresas = self::$db->GetDataListFromSQL("SELECT business.ID AS IDBusiness, business.Name as Name FROM business ORDER BY Name",$this->Business);         
						//Añadimos una empresa más, para forzar que saca el selector.
						$TotalEmpresas++;
				   	} else {
						//Cargamos solo las empresas para las que es usuario...
						$TotalEmpresas = self::$db->GetDataListFromSQL("SELECT business_users.*, business.Name as Name FROM business_users LEFT JOIN business ON business_users.IDBusiness=business.ID WHERE IDUser = " . $this->userID . " ORDER BY Name",$this->Business);
				   }
				   if ($TotalEmpresas!=0) {
				   		$GLOBALS['Business']=$this->Business;
				   } else {
				   		$GLOBALS['Business']=false;
				   }
				}
			   if ($TotalEmpresas>=1) {
					$this->MultiBusiness=true;        
			   }
			   //Si no esá definida la sesión IDBusiness, el rol es distinto a superadmin y hay empresas disponibles por gestionar... Seleccionamos la primera activa.
			   if ((! isset($_SESSION['Business'])) and ($this->userLevel<siteLevelRootMulti) and ($TotalEmpresas>0)) {
					$_SESSION['Business']=$this->Business[0]['IDBusiness'];
					$this->businessID==$this->Business[0]['IDBusiness'];
					//Recargamos la página
					header("Location:" . $_SERVER['REQUEST_URI']);
			   }
		   }              
	   }
	}
	
	function CheckItemBusinessPermission($empresa) {
		if (is_array($empresa)) {
			if (isset($empresa['IDBusiness'])) { 
				$empresa=$empresa['IDBusiness']; 
			} elseif (isset($empresa['IDFather'])) {
				//Buscamos el elemento padre...
				if (isset($this->table)) {
					$partes=explode('_',$this->table);
					$total_partes=count($partes)-1;
					unset($partes[$total_partes]);
					$table=implode('_',$partes);
					$ElementoSuperior=self::$db->GetDataRecordFromSQL("SHOW COLUMNS FROM " . $this->table . " WHERE Field = 'IDFather'");
					if ($ElementoSuperior!==false) {
						if (self::$db->GetDataRecordFromSQL("SHOW COLUMNS FROM " . $this->table . " WHERE Field = 'IDBusiness'")!==false) {
							$temp_empresa=self::$db->GetDataFieldFromSQL("SELECT IDBusiness FROM " . $this->table . " WHERE ID = " . $empresa['IDFather'],'IDBusiness');
							if ($temp_empresa!==false) { $empresa=$temp_empresa; } else { $empresa=-1; }
						}
					}
				}
			} else {
				$empresa="";
			}
		}
		if (($empresa!=$this->businessID) and ($this->businessID!=0) and ($empresa!="") and (!defined('InFrontEnd'))) {
			header("Location: index.php?error=" . urlencode(_('Error al acceder al contenido restringido.')));	
			return false;
		} else {
			return true;
		}
	}
	
	function IsUserOnBusiness($empresa) {
		$devolver=false;
		if ($this->MultiBusiness) {
			foreach ($this->Business as $actbus) {
				if ($empresa==$actbus['IDBusiness']) { $devolver=true; }
			}
		}
		return $devolver;	
	}

	//comprobar permisos
	function CheckPermission($module){
		if (isset($_SERVER['REQUEST_URI'])) {
			$_SESSION[siteCookie . 'Return'] = $_SERVER['REQUEST_URI'];
		}		
		if (($this->userID=="") or ($this->userID==0)) { header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/security"); exit(0); }
		if ($this->Permissions[$module]==0) { header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/security/error/" . urlencode(base64_encode('No tiene permiso para ver esta página'))); exit(0); }
	}
	
	
	function CheckViewOption($module){
		$devolver=true;
		if ($this->userID=="") { 
			$devolver=false; 
		} else {
			if ($this->Permissions[$module]==0) { 
				$devolver=false;
			}
		}
		return $devolver;
	}
	
	//DEPRECATED
	function CheckLevel($level){
		if(isset($_SERVER['REQUEST_URI'])){$referer = $_SERVER['REQUEST_URI'];}	
		//if(isset($_SERVER['HTTP_REFERER'])){$referer = $_SERVER['HTTP_REFERER'];}
		if($level>$this->userLevel) { header("Location: security.php?action=start&error=" . urlencode ('No tiene permiso para ver esta página') . "&urlrefer=" . $referer); exit(0); }
	}
	
	//DEPRECATED
	function Check($param){
		return $this->conf->Check($param);
	}
	
	//DEPRECATED
	function CheckRol($param){
		return $this->conf->CheckRol($param,$this->userLevel,$this->conf->columns[$param]->value);
	}
	
	
	//cambiar el id de la empresa para la configuración
	function ChangeBusiness($param){	
		//Comprobamos si el sitio es multisitio
		if (siteMulti) { $this->businessID=$param; }
	}

	function ChangeLang($lang){
		$sql="SELECT * FROM languages WHERE code='" . $lang . "' OR language='" . $lang . "'";
		$lng=self::$db->GetDataRecordFromSQL($sql);
		if ($lng!==false) {
			//echo sitepath . "locale/" . $lng['code'];
			if (file_exists(sitepath . "locale/" . $lng['code'])) {
				$_SESSION['lang']=$lng['code'];
				return true;
			}
		}
		unset($_SESSION['lang']);
		return false;
	}
	

	function LoadLang($force=false){	
		if ((! isset($GLOBALS['language'])) or ($force)) {
			$this->TotalLangsAvailables=self::$db->GetDataListFromSQL("SELECT * FROM languages ORDER BY factorysetting DESC, language", $this->LangsAvailables);
			$this->ActualLang=self::$db->GetDataFieldFromSQL("SELECT language FROM languages WHERE code='" . $this->userlang . "'","language");
			$GLOBALS['language']=$this->userlang;
			$GLOBALS['ActualLang']=$this->ActualLang;
			$GLOBALS['TotalLangs']=$this->TotalLangsAvailables;
			$GLOBALS['LangsAvailables']=$this->LangsAvailables;
			// settings you may want to change
			$language = $this->userlang;  // the locale you want
			$locales_root = sitepath . "locale";  // locales directory
			$domain = "general"; // the domain you're using, this is the .PO/.MO file name without the extension
			clearstatcache();
			putenv("LC_ALL=$language");
			setlocale(LC_ALL, $language);
			bindtextdomain($domain, $locales_root);
			textdomain($domain);
			bind_textdomain_codeset($domain, 'UTF-8');
		} else {
			$this->TotalLangsAvailables=$GLOBALS['TotalLangs'];
			$this->LangsAvailables=$GLOBALS['LangsAvailables'];
			$this->ActualLang=$GLOBALS['ActualLang'];
		}
		
	}

	function EditTranslation($lang="") {
		if ($lang=="") { $lang=$this->_values['lang']; }
		$sql="SELECT * FROM languages WHERE code='" . $lang . "'";
		$CheckLanguage=self::$db->GetDataRecordFromSQL($sql);
		if ($CheckLanguage===false) { 
			$return="index.php?error=" . urlencode(_('Idioma no disponible'));
			if (isset($_SESSION['deft_edit'])) { 
				$return=$_SESSION['deft_edit'] . "&error="  . urlencode(_('Idioma no disponible')); 
				unset($_SESSION['deft_edit']);
			}
			header("Location: " . $return);
			return;
		}

		unset($this->BreadCrumb);
		$this->BreadCrumb['Traductor']="";
		$this->BreadCrumb[$CheckLanguage['language']]="";
		$this->title=$this->title . " (" . $CheckLanguage['language'] . ")";
		$salida=self::$db->EditTranslate($this->table,$this->id,$lang);
		
		$this->Data=$salida;
		@$this->PrepareForm();
		$this->PrepareLangMenu();
		//Eliminamos los campos que no son necesarios...
		foreach($this->FormContent as $id=>$block) {
			$count=0;
			foreach($block['Fields'] as $idfield=>$field) {
				$fieldname=$field['fieldname'];
				$x=strpos($fieldname,'Form_');
				if ($x!==false) { $fieldname=substr($fieldname,5); }
				if (isset($this->Data[$fieldname])) {
					$count++;
				} else {
					unset($this->FormContent[$id]['Fields'][$idfield]);
				}
			}
			if ($count==0) { unset($this->FormContent[$id]); }
		}
		//Eliminamos los campos ocultos...
		foreach($this->FormHiddenContent as $id=>$block) {
			if (($block['fieldname']!="System_ID") and ($block['fieldname']!="System_Action")) { unset($this->FormHiddenContent[$id]); }
		}
		$this->TemplatePostScript=$_SERVER['REQUEST_URI'];
		$this->LoadTemplate('translation_edit.tpl.php');
	}

	function PostTranslation($table="",$id=0,$lang="",$values="") {
		if ($table=="") {$table=$this->table; }
		if ($id==0) { $id=$this->id; }
		if ($lang=="") { $lang=$this->_values['lang']; }
		if ($values=="") { $values=$_POST; }
		self::$db->PostTranslate($table,$id,$lang,$values);
		$return=$_SERVER['REQUEST_URI'] . "/text/" . urlencode(base64_encode(_("Se ha guardado la traducción")));
		if (isset($_SESSION['deft_edit'])) {
			$return=$_SESSION['deft_edit'] . "/text/" . urlencode(base64_encode(_("Se ha guardado la traducción")));;
			unset($_SESSION['deft_edit']);
		}
		header("Location: " . $return);
	}

	function DeleteTranslations($table="",$id=0,$lang="") {
		if ($id==0) { $id=$this->id; }
		if ($table=="") { $table=$this->table; }
		$sql="SHOW TABLES LIKE '" . $table . "_translations'";
		$TranslationsExists=self::$db->GetDataRecordFromSQL($sql);
		if ($TranslationsExists!==false)  {
			$sql="DELETE FROM " . $table . "_translations WHERE ID=" . $id;
			if ($lang!="") { $sql.=" AND LangCode='" . $lang . "'"; }
			self::$db->Qry($sql);
			return true;
		}
		return false;
	}
	
	function PrepareLangMenu($default_edit=false) {
		if ($default_edit===true) {	$_SESSION['deft_edit']=$_SERVER['REQUEST_URI']; }
		if (($default_edit!==true) and ($default_edit!==false)) { 	$_SESSION['deft_edit']=$default_edit; }
		if (isset($_SESSION['deft_edit'])) { $default_edit=$_SESSION['deft_edit']; }
		if (isset($_SESSION['deft_edit'])) { $default_edit=$_SESSION['deft_edit']; }
		$sql="SHOW TABLES LIKE '" . $this->table . "_translations'";
		$TranslationsExists=self::$db->GetDataRecordFromSQL($sql);
		if ($TranslationsExists!==false)  {
			$sql_languages="SELECT * FROM languages ORDER BY FactorySetting DESC, language";
			$TotalLangs=self::$db->GetDataListFromSQL($sql_languages,$Langs);
			if ($TotalLangs>0) {
				if (count($this->MainMenu)>0) { $this->AddMainMenu(); }
				foreach($Langs as $lng) {
					$clnk=$this->module . "/translate";
					if ($this->class!=$this->module) { 
						$clnk=$this->module . "--" . $this->class . "/translate"; 
						if ($this->xtras_RunSubClass) { $clnk=$this->module . "/" . $this->class . "_translate"; }
					}	
					$lnk=siteprotocol . sitedomain . sitePanelFolder . "/" . $clnk . "/id/". $this->id . "/lang/" . $lng['code'];
					if ($lng['factorysetting']==1) { $lnk=$default_edit; }
					if ($_SERVER['REQUEST_URI']!=$lnk) { $this->AddMainMenu($lng['language'],$lnk); }
				}
			}
		}
	}

	function LoadSystemConfig($idbusiness=0) {
		//Prevent reload config...
		if (isset($GLOBALS['conf_loaded'])) { return;}
		$GLOBALS['conf_loaded']=true;
		$this->init=new ConfigCore($idbusiness);
		if (count($this->init->columns)>0) {
			foreach($this->init->columns as $param) {
				$cfg="site" . $param->param;
				$val=$param->value;
				//if ($param->type=="BOOLEAN") { $val=intval($val); }
				//Hacemos una salvedad, si es "siteMulti", y no existe la tabla business, no puede estar habilitado.
				if ($cfg=="siteMulti") { 
					$total=self::$db->GetDataListFromSQL("SHOW TABLES LIKE 'business'",$ignorar);
					if ($total==0) { $value=0; }
				}
				//echo $cfg . "=>" . $val . "<br>";
				if (! defined($cfg)) { define($cfg,$val); }
			}
		}
		if (is_dir(sitepath . "install")) { define("ShowAlertInstallFolder",true);	}	
		return;


		$sql="SELECT * FROM modules_config WHERE Module='core' AND UserID=".$idbusiness;
		$TotalCampos=self::$db->GetDataListFromSQL($sql,$Campos);	
		if ($TotalCampos>0) {
			foreach ($Campos as $campo) {
				$campo["ParamName"]="site" . $campo["ParamName"];
				//Hacemos una salvedad, si es "siteMulti", y no existe la tabla business, no puede estar habilitado.
				if ($campo['ParamName']=="siteMulti") {
					$total=self::$db->GetDataListFromSQL("SHOW TABLES LIKE 'business'",$ignorar);
					if ($total==0) { $campo["ParamValue"]=0; }
				}
				//echo $campo["ParamName"] . "=>" . $campo["ParamValue"] . "<br>";
				if (! defined($campo["ParamName"])) { define($campo["ParamName"],$campo["ParamValue"]); }
			}
		}
		if (is_dir(sitepath . "install")) { define("ShowAlertInstallFolder",true);	}		
	}
	
	function Error500($errno,$errstr,$errline) {
		$ruta=sitepath."/templates/".$this->template."/500.tpl.php";
		header("HTTP/1.0 500 Internal Server Error");
		header("Status: 500 Internal Server Error");
		if (is_file($ruta)) {
			$this->LoadPublicTemplate("500.tpl.php");
		} else {
			die("Error 500");
		}
	}

	//obtener la miga de pan
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
	
	//Algunas funciones de database que hay que sobreescribir para acceder al objeto DBase

	function Qry($query){
		return self::$db->Qry($query);
	}
	
	function PostToDatabase($table,$params,$purgeHTML=true){
		return self::$db->PostToDatabase($table,$params,$purgeHTML);
	}
	
	function Delete(){
		return self::$db->Delete($this);
	}
	
	//recupera un registro de una tabla determinada
	function GetDataRecord($table,$id=0) {
		if ($id==0) { $id=$this->id; }
		return self::$db->GetDataRecord($table,$id); 
	}

	//recupera un campo de un registro de una tabla determinada	
	function GetDataField($table,$id,$Field) {
		return self::$db->GetDataField($table,$id,$Field); 	
	}
	
	function UploadAttach($subido,$upload_uploadfolder='public/files/'){
		$msg = "";
		//$upload_uploadfolder='public/files/';
		$filename=$subido;			
		$extension = preg_split("/\./", strtolower($filename)) ;
		$n = count($extension)-1;
		$extension = $extension[$n];
		$nombrefinal=$filename;
		if (! is_dir(sitepath . $upload_uploadfolder)) {
			mkdir(sitepath . $upload_uploadfolder);
			chmod(sitepath . $upload_uploadfolder, 0777);
		}
		copy(sitepath . "public/temp/".$subido, sitepath . $upload_uploadfolder . $nombrefinal);
		chmod(sitepath . $upload_uploadfolder . $nombrefinal, 0777);
		$msg=$nombrefinal;
		@unlink(sitepath . "public/temp/".$subido);		
	}
		
	//Rename Upload
	function RenameUpload($tabla,$campo,$id,$valor,$nuevonombre,$carpeta){
		return self::$db->RenameUpload($tabla,$campo,$id,$valor,$nuevonombre,$carpeta);
	}
	
	function SetPermalink($title='',$table_name='',$table_id=0,$module='',$options='',$carpeta='',$empresa=0,$duplicar=true) {
		//Parcheamos los parametros...
		if (($title=="") and (isset($_POST['Form_Title']))) { $title=$_POST['Form_Title']; }
		if (($title=="") and (isset($_POST['Form_Name']))) { $title=$_POST['Form_Name']; }
		if (($title=="") and (isset($this->Data['Title']))) { $title=$this->Data['Title']; }
		if (($title=="") and (isset($this->Data['Name']))) { $title=$this->Data['Name']; }
		//if (($title=="") and (isset($_POST['Permalink']))) { $title=$_POST['Permalink']; }
		if ($table_name=='') { $table_name=$this->table; }
		if ($table_id==0) { $table_id=$this->id; }
		if ($module=='') { $module=$this->module; }
		//Comprobamos la carpeta, la arreglamos o quitamos,
		//saparamos por /
		$temp=explode("/",$title);
		$ultimo=count($temp);
		$pl_title=$temp[$ultimo-1];
		if ($carpeta!="") { $pl_title=$carpeta . "/" . $pl_title; }
		if ($empresa!=0) {
			$pl_empresa=GetPermalink('business',$empresa);
			if ($pl_empresa!="") { $pl_title=$pl_empresa . "/" . $pl_title; }
		}
		//Construimos la ruta del enlace...
		$eliminar=array("!","¡","?","¿","'","\"","$","(",")",".",":",";","_","+","\\","\$","%","@","#",",", "«", "»",'"',"+","ª","º","&","`","´","<",">","€","{","}","[","]","*");
		$buscados=array(" ","á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Ä","Ë","Ï","Ö","Ü","ä","ë","ï","ö","ü");
		$sustitut=array("-","a","e","i","o","u","a","e","i","o","u","n","n","u","a","e","i","o","u","A","E","I","O","U","a","e","i","o","u","a","e","i","o","u");
		$final=strtolower(str_replace($buscados,$sustitut,str_replace($eliminar,"",$pl_title)));
		$final=preg_replace("[^A-Za-z\_\-\.0-9]", "", $final); 
		//Buscamos si existe una ruta previa o hay que crearla...
		$sql="SELECT ID FROM " . $this->table_prefix ."permalinks WHERE TableName='" . $table_name . "' AND TableID='" . $table_id . "' AND IDBusiness='" . $empresa . "' AND Options='" . $options . "'";
		$Actual=self::$db->GetDataFieldFromSQL($sql,'ID');
		//Comprobamos que no exista la url ya en la base de datos...
		$valido=false;
		$contador='';
		while (! $valido) {
			$buscamos="SELECT COUNT(ID) AS Total FROM " . $this->table_prefix ."permalinks WHERE Permalink='" . $final . $contador . "' AND ID<>'" . $Actual . "'";
			$hay=self::$db->GetDataFieldFromSQL($buscamos,'Total');
			if ($hay>0) {
				//Hay elementos, por tanto sumamos uno al contador...
				$buscamos="SELECT COUNT(ID) AS Total FROM " . $this->table_prefix ."permalinks WHERE Permalink LIKE '" . $final . "%' AND ID<>'" . $Actual . "'";
				if ($contador=='') { $contador=self::$db->GetDataFieldFromSQL($buscamos,'Total')-1; }
				$contador++;
			} else {
				$valido=true;
			}
		}
		if ($Actual===false) {
			$Datos['System_Action']="new";
			$Datos['System_ID']=-1;
		} else {
			$Datos['System_Action']="edit";
			$Datos['System_ID']=$Actual;				
		}
		$Datos['Form_Permalink']=$final . $contador;
		$Datos['Form_TableName']=$table_name;
		$Datos['Form_TableID']=$table_id;
		$Datos['Form_ModuleName']=$module;
		$Datos['Form_Options']=$options;
		$Datos['Form_LastMod']=date("Y-m-d");
		$Datos['Form_IDBusiness']=$empresa;
		self::$db->PostToDatabase('permalinks',$Datos);	
		if (($duplicar) and ($empresa!=0)) { $this->SetPermalink($title,$table_name,$table_id,$module,$options,$carpeta); }
		return $Datos['Form_Permalink'];
	}
	
	function GetPermalink($table_name='',$table_id=0,$empresa=0,$accion="") {
		if ($table_name=='') { $table_name=$this->table; }	
		if ($table_id=='') { $table_id=$this->id; }
		$sql="SELECT Permalink FROM " . $this->table_prefix ."permalinks WHERE TableName='" . $table_name . "' AND TableID='" . $table_id . "' AND IDBusiness='" . $empresa . "'";
		if ($accion!="") { $sql.=" AND Options LIKE '%" . $accion . "%'"; }
		$Actual=self::$db->GetDataFieldFromSQL($sql,'Permalink');
		if ($Actual===false) { $Actual=""; }
		if ($this->userlang!=siteLang) {
			$Actual=$this->userlang . '/' . $Actual;
		}
		return $Actual;
	}
	
	function GetModulePermalink($module="",$empresa=0) {
		$sql="SELECT Permalink FROM " . $this->table_prefix ."permalinks WHERE ModuleName='" . $module . "' AND TableID='0' AND (TableName='' OR TableName IS NULL) AND IDBusiness='" . $empresa . "'";
		$Actual=self::$db->GetDataFieldFromSQL($sql,'Permalink');
		if ($Actual===false) { $Actual=""; }
		if ((defined("siteFrontend")) and ($this->userlang!=siteLang)) {
		 	$Actual=$this->userlang . '/' . $Actual;
		}
		return $Actual;
	}
	
	function GetParamsPermalink($permalink) {
		$params = $_GET;
		$params_url = '';
		$paginador='';
		//Quitamos la última barra, si es que existe...
		if (substr($permalink,strlen($permalink)-1)=="/") {
			$permalink=substr($permalink, 0, strlen($permalink)-1);
		}
		//Comprobamos que la URL no sea un único identificador de idioma
		if (strlen($permalink)==5) {
			$act=self::$db->GetDataRecordFromSQL("SELECT * FROM languages WHERE code='" . $permalink . "'");
			if ($act!==false) { $permalink.="/"; $_SESSION['lang']=$permalink; $this->userlang=$permalink;}
		}
		//Comprobamos si el permalink incluye algún identificador de idioma...
		if (substr($permalink, 5,1)=="/") {
			$idioma=substr($permalink, 0,5);
			//Comprobamos si el idioma existe configurado
			$act=self::$db->GetDataRecordFromSQL("SELECT * FROM languages WHERE code='" . $idioma . "'");
			if ($act!==false) { 
				//Seleccionamos el nuevo idioma activo
				$permalink=substr($permalink, 6);
				$_SESSION['lang']=$idioma;
				//Si no es el idioma por defecto, redirigimos a la url correcta.
				//añadiendo los parametros GET
				if ($act['factorysetting']) { 
					//header("HTTP/1.1 301 Moved Permanently");
					header("Location:" . siteprotocol . sitedomain . $permalink); 
					die();
				}
				$this->userlang=$idioma;
			}
		} else {
			//Comprobamos si el idioma no es el por defecto, que la url sea correcta.
			if ($this->userlang!=siteLang) {
				if (strpos($permalink, $this->userlang . '/')===false) { 
					$get=$_SERVER['REQUEST_URI'];
					$p=stripos($get,"?");
					if ($p!==false) { $permalink.=substr($get, $p);}
					$permalink=$this->userlang . '/' . $permalink;
					header("Location:" . siteprotocol . sitedomain . $permalink); 
					die();
				}
			}
		}
		//Limpiamos parámetros obsoletos
		$posicion=strpos($permalink,',');
		if ($posicion!==false) {
			$paginador=substr($permalink,$posicion+1,strlen($permalink));
			$permalink=substr($permalink,0,$posicion);
		}	
		//Si la url está en blanco, se ha hecho el cambio de idioma, y llevamos al index...
		if ($permalink=="")	 {
			$params['module']="index";
			return $params;
		}	
		$sql="SELECT * FROM " . $this->table_prefix ."permalinks WHERE Permalink='" . $permalink . "'";
		$Actual=self::$db->GetDataRecordFromSQL($sql);
		if ($Actual===false) { 
			$Actual=""; 
		} else {
			if (($Actual['TableID']!=0) or (! isset($params['id']))) { $params['id']=$Actual['TableID'];}
			$params['table']=$Actual['TableName'];
			$params['module']=$Actual['ModuleName'];
			$params['idbusiness']=$Actual['IDBusiness'];
			if ($Actual['Options']!="") {
				$myOptions=explode("&",$Actual['Options']);
				foreach($myOptions as $op){
					$myVar='';
					$myVar=explode("=",$op);
					$params[$myVar[0]]=$myVar[1];
				}
			}
			//Parcheamos el paginador...
			if ($paginador!="") {
				$posicion=strpos($paginador,',');
				if ($posicion!==false) {	
					$params['page']=substr($paginador,0,$posicion);
					$params['offset']=substr($paginador,$posicion+1,strlen($paginador));
				} else {
					$params['page']=$paginador;
				}
			}
			//Devolvemos los valores
			return $params;
		}
	}
	
	function DeletePermalink($table_name='',$table_id=0) {
		if ($table_name=='') { $table_name=$this->table; }	
		if ($table_id=='') { $table_id=$this->id; }
		$sql="DELETE FROM " . $this->table_prefix ."permalinks WHERE TableName='" . $table_name . "' AND TableID='" . $table_id . "'";
		$Actual=self::$db->Qry($sql);
		return true;
	}	
	
	function LikeThis($LikeType='+',$permalink,$enable_anonymous=false) {
		$devolver=-1;
		$valido=false;
		$usuario=$this->userID;
		//Si no es usuario registrado, tiramos de la cookie con la session.
		if ($usuario!=0) { $valido=true; }
		if (($usuario==0) and ($enable_anonymous)) {
			if (isset($_COOKIE[siteCookie . '_usr_tracker'])) {
				$usuario=$_COOKIE[siteCookie . '_usr_tracker'];
			} else {
				$usuario="sess_" .session_id();
			}
			setcookie(siteCookie . "_usr_tracker",$usuario,time() + 31536000,"/");
			$valido=true;
		}
		if ($valido) {
			$EnlacePermanente=self::$db->GetDataRecordFromSQL("SELECT * FROM permalinks WHERE permalink='" . $permalink . "'");
			if ($EnlacePermanente!==false) {
				//Buscamos un voto anterior en la BD...
				$sql="SELECT * FROM likethis WHERE (IDUser='" . $usuario . "' OR MD5(IDUser)='" . $usuario . "') AND TableName='" . $EnlacePermanente['TableName'] . "' AND TableID='" . $EnlacePermanente['TableID'] . "' AND ModuleName='" . $EnlacePermanente['ModuleName'] . "' AND Options='" . $EnlacePermanente['Options'] . "'";
				$VotoAnterior=self::$db->GetDataRecordFromSQL($sql);
				if ($VotoAnterior===false) {
					$Datos['System_Action']="new";
					$Datos['Form_IDUser']=$usuario;
					$Datos['Form_TableName']=$EnlacePermanente['TableName'];
					$Datos['Form_TableID']=$EnlacePermanente['TableID'];
					$Datos['Form_ModuleName']=$EnlacePermanente['ModuleName'];
					$Datos['Form_Options']=$EnlacePermanente['Options'];
					$devolver=1;
				} else {
					$Datos['System_Action']="edit";
					$Datos['System_ID']=$VotoAnterior['ID'];
					$devolver=1;
					if ($VotoAnterior['Vote']==$LikeType) { $devolver=0; }
				}
				$Datos['Form_Vote']=$LikeType;
				self::$db->PostToDatabase('likethis',$Datos);
			} 
		} 
		return $devolver;
	}
	
	function GetLikes($LikeType='+',$permalink) {
		$EnlacePermanente=self::$db->GetDataRecordFromSQL("SELECT * FROM permalinks WHERE permalink='" . $permalink . "'");
		if ($EnlacePermanente!==false) {
			$sql="SELECT COUNT(ID) as Total FROM likethis WHERE TableName='" . $EnlacePermanente['TableName'] . "' AND TableID='" . $EnlacePermanente['TableID'] . "' AND ModuleName='" . $EnlacePermanente['ModuleName'] . "' AND Options='" . $EnlacePermanente['Options'] . "' AND Vote='" . $LikeType . "'";
			$TotalLikes=self::$db->GetDataFieldFromSQL($sql,'Total');
			return $TotalLikes;
		} else {
			return false;
		}
	}
	
	function ModuleInstalledAndEnabled($module) {
		$devolver=false;
		if (isset($this->Permissions[$module])) {
			if ($this->Permissions[$module]==1) {	
				$devolver=true;
			}
		}
		return $devolver;
	}
	
	function GetSystemVariables() {
		$sql="SELECT * FROM modules_config WHERE Module='socialmedia' AND UserID=" . $this->businessID;
		$total=self::$db->GetDataListFromSQL($sql,$configuracion);
		if ($total!=0) {
			foreach ($configuracion as $cfg) {
				$this->SystemVariables[$cfg['ParamName']]=$cfg['ParamValue'];
			}
		}
	}
	
	//Edita el valor de un parámetro en la base de datos
	function EditSystemVariable($variable,$valor){	
		//Buscamos el valor previo, para saber si existe...
		$identificador=self::$db->GetDataFieldFromSQL("SELECT ID FROM modules_config WHERE Module='socialmedia' AND UserID=" . $this->businessID . " AND ParamName = '" . $variable . "'","ID");
		if ($identificador!==false) {
			$execute="UPDATE modules_config SET ParamValue = '". addslashes($valor)."' WHERE Module='socialmedia' AND UserID=" . $this->businessID . " AND ParamName = '". $variable."'";
		} else {
			$execute="INSERT INTO modules_config (UserID, Module, ParamName, ParamValue) VALUES (" . $this->businessID . ", 'socialmedia', '" . $variable . "', '" . addslashes($valor) . "')";
		}
		self::$db->Qry($execute);
		$this->SystemVariables[$variable]=$valor;
	}

	//Borra el valor de un parámetro en la base de datos
	function DeleteSystemVariable($variable){	
		$execute="DELETE FROM modules_config WHERE Module='socialmedia' AND UserID=" . $this->businessID . " AND ParamName = '" . $variable . "'";
		self::$db->Qry($execute);
	}
	
	function LoadSessionsProfile() { 
		if (isset($_SESSION['lang'])) { $this->userlang=$_SESSION['lang']; }
		if (isset($_SESSION['template'])) { $this->template=$_SESSION['template']; }
		if (isset($_SESSION['username'])) { $this->username=$_SESSION['username']; }
		if (isset($_SESSION['userid'])) { $this->userID=$_SESSION['userid']; }
		if (isset($_SESSION['userlevel'])) { $this->userLevel=$_SESSION['userlevel']; }
		//Check number of updates...
		if (isset($_SESSION['ItsUpdated'])) { $this->ItsUpdated=$_SESSION['ItsUpdated']; }
		if (isset($_SESSION['Business'])) { 
			if ($this->EnableBusiness) {
				if (self::$db->GetDataRecordFromSQL("SELECT ID FROM business WHERE ID='". $_SESSION['Business'] . "'")!=false) {	
					$this->businessID=$_SESSION['Business']; 
				} else {
					unset($_SESSION['Business']);
				}
			} else {
				unset($_SESSION['Business']);
			}
		}		
	}
	
	function DeleteInactiveUsers() {
		if (! isset($GLOBALS['userData'])) {
			preg_match("/(\d+)-(\d+)-(\d+)/", date('Y-m-d'), $wdate);
			$fecha=date('Y-m-d',mktime(0,0,0,$wdate[2],$wdate[3]-8,$wdate[1]));
			$borrado="DELETE FROM users WHERE Active=0 AND DateInscribe<'" . $fecha . "' AND LastLogin='0000-00-00 00:00:00'";
			self::$db->Qry($borrado);		
		}
	}
	
	function CheckMultiBusiness() {
		if (isset($GLOBALS['siteMulti'])) {
			$this->EnableBusiness=$GLOBALS['siteMulti'];
			if ($this->businessID!=0) {	$this->businessName=$GLOBALS['businessName']; }
		} else {
			if (siteMulti) {
				if (self::$db->GetDataRecordFromSQL("SHOW TABLES LIKE 'business'")==false) { $this->EnableBusiness=false; }
			} else {
				$this->EnableBusiness=false;
			}
			//Cargamos el nombre de la empresa activa
			if ($this->businessID!=0) {	$this->businessName=self::$db->GetDataField("business",$this->businessID,"Name"); }
			$GLOBALS['siteMulti']=$this->EnableBusiness;
			$GLOBALS['businessName']=$this->businessName;
		}
	}
	
	function LoginFromAccessToken($access_token="") {
		if (siteAPIRequiresOAuthLogin==0) {
			$Resultado['Success']="1";
			$Resultado['Status']="Not required";
			$Resultado['UserName']='unknown';
			$Resultado['AccessToken']='Not required';
			$this->AccessTokenResult=$Resultado;
			$this->AccessTokenValid=true;
			return true;
		}

		if ($access_token!="") {
			if (self::$db->GetDataRecordFromSQL("SHOW TABLES LIKE 'oauth_accesstokens'")!=false) {
				$sql="SELECT * FROM oauth_accesstokens WHERE AccessToken='" . $access_token . "'";
				$token=self::$db->GetDataRecordFromSQL($sql);
				if ($token!==false) {
					if ($token['Expires']>date('Y-m-d H:i:s')) {
						//Buscamos los datos de la App
						$App=self::$db->GetDataRecordFromSQL("SELECT * FROM api_apps WHERE ID='" . $token['IDApp'] . "'");
						//Buscamos el usuario...
						$usuario=self::$db->GetDataRecordFromSQL("SELECT * FROM users WHERE ID='" .$token['IDUser']."'");
						if ($App['AllowOAuthSign']==1) {
							if ($usuario!==false) {
								//Extendemos la vida del token.
								$Datos['System_Action']="edit";
								$Datos['System_ID']=$token['ID'];
								$tiempo=siteOAuthAccessTokenExpires;
								if ($token['LongLife']==1) { $tiempo=siteOAuthExtendedAccessTokenExpires; }
								$Datos['Form_Expires']=date('Y-m-d H:i:s', time()+$tiempo);
								$this->PostToDatabase('oauth_accesstokens',$Datos);		
								$setcookie=false;
								if ($App!==false) { if ($App['AllowOAuthSign']==1) { $setcookie=true; } }
								if ($setcookie) { 		
									$_SESSION['regcode']=$usuario['RegCode'];
								}
								$_SESSION['access_token']=$access_token;
								//Devolvemos los resultados
								if (($usuario['Active']==1) and ($usuario['UserDisallowed']==0)) {
									$devolver=true;
									$Resultado['Success']="1";
									$Resultado['Status']="Logged";
									$Resultado['UserName']=$usuario['UserName'];
									$Resultado['AccessToken']=$access_token;
								}
								if ($usuario['UserDisallowed']==1) {
									$devolver=false;
									$Resultado['Success']="-9";
									$Resultado['Status']=_("Acceso al usuario bloqueado por el administrador");
								}
								if ($usuario['Active']==0) {
									$devolver=false;
									$Resultado['Success']="-1";
									$Resultado['Status']=_("Cuenta no activada");
								}
							} else {
								if ($App['PermitGuest']==0) {
									//El usuario no existe	
									$devolver=false;
									$Resultado['Success']="0";
									$Resultado['Status']="Error";
									$Resultado['Message']='The user does not exist';
								} else {
									$Resultado['Success']="1";
									$Resultado['Status']="Logged";
									$Resultado['UserName']='[Not required for this app]';
									$Resultado['AccessToken']=$access_token;	
									$devolver=true;
								}
							}
						} else {
							$devolver=true;
							$Resultado['Success']="1";
							$Resultado['Status']="Not logged";
							$Resultado['Message']='[Not required for this app]';
						}
					} else {
						//El token ha caducado, hay que generar uno nuevo	
						$devolver=false;
						$Resultado['Success']="0";
						$Resultado['Status']="Error";
						$Resultado['Message']='The token has expired. You must generate a new token.';
					}
				} else {
					//No hay token
					$devolver=false;
					$Resultado['Success']="0";
					$Resultado['Status']="Error";
					$Resultado['Message']='An active access token must be used.';
				}
			} else {
				$devolver=false;
				$Resultado['Success']="0";
				$Resultado['Status']="Error";
				$Resultado['Message']='Access is disabled by access_token';
			}
		} else {
			$devolver=false;
			$Resultado['Status']="Error";
			$Resultado['Message']='An active access token must be used.';
		}
		$this->AccessTokenResult=$Resultado;
		$this->AccessTokenValid=$devolver;
		return $devolver;
	}
	
	function UnauthorizeAccessToken($usuario) {
		$sql="UPDATE oauth_accesstokens SET AccessToken='Not-valid-for-password-change-" . KeyGen(20) . "' WHERE IDUser=" . $this->userID;
		$token=self::$db->Qry($sql);	
	}
	
	function AutoLoginUser() {
		if (isset($GLOBALS['userData'])) {
			$usuarios=$GLOBALS['userData'];
			$this->userMP=$GLOBALS['userMP'];
		} else {
			//Cargamos los datos del regcode
			if (isset($_SESSION['regcode'])) { $this->registrocode=$_SESSION['regcode']; }
			if ((isset($_COOKIE[siteCookie . '_regcode'])) and ($this->registrocode=="")) { $this->registrocode=$_COOKIE[siteCookie . '_regcode']; $_SESSION['regcode']=$this->registrocode; }	
			//Consultamos si el usuario esta en la bd
			$sql="SELECT * FROM users WHERE RegCode = '" . $this->registrocode . "' AND Active=1 AND UserDisallowed=0";
			$usuarios = self::$db->GetDataRecordFromSQL($sql);
			//Actualizamos los datos de último acceso...
			$ActUsr['System_Action']="edit";
			$ActUsr['System_ID']=$usuarios['ID'];
			$ActUsr['Form_LastLogin']=date("Y-m-d H:i:s");
			$ActUsr['Form_LastIP']=$_SERVER["REMOTE_ADDR"];
			if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {  $ActUsr['Form_LastIP'].=" (" . $_SERVER["HTTP_X_FORWARDED_FOR"] . ")"; }
			if ($usuarios['DateInscribe']=="") { $ActUsr['Form_DateInscribe']=date("Y-m-d H:i:s"); }
			self::$db->PostToDatabase('users',$ActUsr);
			unset($ActUsr);
			//Cargamos el núm. de mensajes no leidos
			$sql="SELECT COUNT(ID) AS Total FROM users_messages WHERE ToID=" . $this->userID . " AND ReadMsg=0";
			$this->userMP=self::$db->GetDataFieldFromSQL($sql,"Total");
			//Cambiamos el tracker por el ID del usuario encriptado.
			setcookie(siteCookie . "_usr_tracker",md5($usuarios['ID']),time() + 31536000,"/");
			$GLOBALS['userData']=$usuarios;
			$GLOBALS['userMP']=$this->userMP;
		}
		//Si está, guardamos en el vector Permisos, los permisos de cada módulo asociados a ese usuario	
		if($usuarios) {
			$this->userData=$usuarios;
			$this->username=$usuarios['UserName']; 
			$this->userID=$usuarios['ID'];
			$this->useremail=$usuarios['Email'];
			$this->userLevel=$usuarios['Rol'];
			$this->useravatar=$usuarios['Image'];
			if (is_file(sitepath . "public/avatar/" . $this->useravatar)) { 
				$this->useravatar=siteprotocol . sitedomain . "public/avatar/" . $this->useravatar;
			} else {
				$this->useravatar="";
			}
			//Comprobamos los permisos del panel de gestion
			if (! defined('InFrontEnd')) {
				//Cargamos la lista de modulos instalados
				if (isset($GLOBALS['Permissions'])) {
					$this->Permissions=$GLOBALS['Permissions'];
					$this->ModulesNames=$GLOBALS['ModulesNames'];
				} else {
					$sql="SELECT modules_installed.*, users_roles_permissions.OptionStatus AS OptionStatus";
					if ($this->EnableBusiness) { $sql.=", (SELECT COUNT(business_modules.ID) FROM business_modules WHERE modules_installed.Module=business_modules.OptionFile AND IDBusiness=" . $this->businessID . ") AS BusinessStatus"; }
					$sql.=" FROM modules_installed ";
					$sql.="LEFT JOIN users_roles_permissions ON modules_installed.Module=users_roles_permissions.OptionFile AND users_roles_permissions.RolID=" . $this->userLevel . " ";
					//if ($this->EnableBusiness) { $sql.="LEFT JOIN business_modules ON modules_installed.Module=business_modules.OptionFile AND IDBusiness=" . $this->businessID . " "; }
					$sql.="WHERE Module<>'core'";
					$TotalModulesInstalled=self::$db->GetDataListFromSQL($sql,$ModulesInstalled);
					if ($TotalModulesInstalled>0) {
						foreach($ModulesInstalled as $mod) {
							$this->Permissions[$mod['Module']]=0;
							$this->ModulesNames[$mod['Module']]=$mod['Module'];
							if ($mod['ModuleName']!="") { $this->ModulesNames[$mod['Module']]=$mod['ModuleName']; }
							if ($this->businessID==0) {
								if ($mod['OptionStatus']!="") { $this->Permissions[$mod['Module']]=$mod['OptionStatus']; }
							} else {
								if (($this->EnableBusiness) and ($mod['BusinessStatus']>0)) {  $this->Permissions[$mod['Module']]=1; }
							}
						}
					}
					$GLOBALS['Permissions']=$this->Permissions;
					$GLOBALS['ModulesNames']=$this->ModulesNames;
				}
				//Si la empresa es distinta a la principal, añadimos las opciones generales de la empresa...
				if ($this->businessID!=0) {
					$otrasopciones=array('_data','_images','_attachments','_links','_videos');
					foreach($otrasopciones as $archivo) {
						$query_getpermissions = self::$db->GetDataRecordFromSQL("SELECT * FROM business_modules WHERE OptionFile='" . $archivo . "' AND IDBusiness=" . $this->businessID);
						if($query_getpermissions){ $this->Permissions[$archivo]=1; }
					}
				}	
				//Cargamos los permisos de la empresa activa
				$this->GetBusinessPermission();
			}
		} else {
			$_SESSION['regcode']="";
			$_SESSION['username']="";
			$_SESSION['userid']="";
			$_SESSION['userlevel']="";
   			unset($_SESSION['regcode']);
			unset($_SESSION['username']);
			unset($_SESSION['userid']);
			unset($_SESSION['userlevel']);	
			$this->userData=array();
			$this->username=""; 
			$this->userID=0;
			$this->useremail="";
			$this->userLevel=0;		
   			setcookie(siteCookie . "_regcode","",time() + 31536000,"/");	
		}		
	}
	
	function LoginUserID($id,$write_session=false) {
		$usuario=self::$db->GetDataRecord("users",$id);
		if ($usuario!==false) {
			$_SESSION['regcode']=$usuario['RegCode'];
			$this->userlang=$usuario['Language'];
			$this->template=$usuario['Template'];
			$this->username=$usuario['UserName'];
			$this->userID=$usuario['ID'];
			$this->userLevel=$usuario['Rol'];
			return true;
		} else {
			return false;
		}
	}
	
	function WebServiceLoginWithAccessToken() {
		if (siteAPIRequiresOAuthLogin==0) { return true; }
		if ($this->AccessTokenValid) {
			return true;
		} else {
			return json_encode($this->AccessTokenResult);
		}
	}

	function RegisterUserDevice($userID=0,$devicetype="",$deviceid="") {
		if (($devicetype!="") and ($deviceid!="")) {
			//Buscamos si hay un dispositivo con esos datos ya guardados...
			$sql="SELECT * FROM users_devices WHERE DeviceType='" . $devicetype . "' AND DeviceID='" . $deviceid . "' LIMIT 1";
			$Datos=self::$db->GetDataRecordFromSQL($sql);
			if ($Datos===false) {
				$Post['System_Action']="new";
				$Post['System_ID']=-1;
				$Post['Form_IDUser']=$userID;
				$Post['Form_DeviceType']=$devicetype;
				$Post['Form_DeviceID']=$deviceid;
				self::$db->PostToDatabase("users_devices",$Post);
			} else {
				if (($userID!=0) and ($userID!=$Datos['IDUser'])) {
					$Post['System_Action']="edit";
					$Post['System_ID']=$Datos['ID'];
					$Post['Form_IDUser']=$userID;
					$Post['Form_DeviceType']=$devicetype;
					$Post['Form_DeviceID']=$deviceid;
					self::$db->PostToDatabase("users_devices",$Post);
				}
			}
			return true;
		}
		return false; 
	}

	function UnregisterUserDevice($deviceid="") {
		if ($deviceid!="") {
			self::$db->Qry("DELETE FROM users_devices WHERE DeviceID='" . $deviceid . "'");
			return true;
		}
		return false;
	}

	function SetNotification($type="",$id="",$datePublish="",$method="",$destination="",$title="",$body="",$data=null) {
		if (($type=="") or ($id=="") or ($datePublish=="") or ($method=="") or ($destination=="")) { return false; }
		if ($this->GetNotification($type,$id,$method,$destination)===false) {
			$Data["System_Action"]="new";
			$Data['System_ID']=-1;
			$Data['Form_Type']=$type;
			$Data['Form_IDElement']=$id;
			$Data['Form_DatePublish']=$datePublish;
			$Data['Form_Method']=$method;
			$Data['Form_Destination']=$destination;
			$Data['Form_Title']=$title;
			$Data['Form_Body']=$body;
			$Data['Form_Data']=$data;
			self::$db->PostToDatabase("notifications",$Data);
			return true;
		} else {
			return false;
		}
	}

	function GetNotification($type="",$id="",$method="",$destination="") {
		$sql="SELECT * FROM notifications WHERE Type='" . $type . "' AND IDElement='" . $id . "' AND Method='" . $method . "' AND Destination='" . $destination . "' LIMIT 1";
		$this->Notification=self::$db->GetDataRecordFromSQL($sql);
		if ($this->Notification!==false) {
			return $this->Notification['ID'];
		} else {
			return false;
		}
	}

	function DeleteNotification($id=0) {
		$check=self::$db->GetDataRecord("notifications",$id);
		if ($check!==false) {
			self::$db->Qry("DELETE FROM notifications WHERE ID=" . $id);
			return true;
		} else {
			return false;
		}
	}

	function SendNotifications() {
		$use_firebase=false;
		if ((is_file(sitepath . "lib/push/firebasecm.php")) and (siteFirebaseAPIKey!="")) {
			$use_firebase=true;
			require_once(sitepath . "lib/push/firebasecm.php");

		}
		$sql="SELECT * FROM notifications WHERE DatePublish<=NOW()";
		$ItemsCount=self::$db->GetDataListFromSQL($sql,$Items);
		if ($ItemsCount>0) {
			foreach($Items as $item) {
				$sendDevices=array();
				$email="";
				switch($item['Method']) {
					case "user":
						//Buscamos los dispositivos que tenga vinculado el usuario
						$sqlDevices="SELECT * FROM users_devices WHERE IDUser=" . $item['Destination'];
						$DevicesCount=self::$db->GetDataListFromSQL($sqlDevices,$Devices);
						if ($DevicesCount>0) {
							foreach($Devices as $device) {
								array_push($sendDevices, $device['DeviceID']);
							}
						} else {
							$email=self::$db->GetDataField("users",$item['Destination'],"Email");
						}
						break;
					case "email":
						$email=$item['Destination'];
						break;
					case "fcm":
						array_push($sendDevices, $item['Destination']);
						break;
				}
				//Procesamos el envío
				if (($use_firebase) and (count($sendDevices)>0)) {
					$FCM=new FirebaseCMPush();
					$FCM->set_devices($sendDevices);
					$FCM->send("","","default","high",json_decode($item['Data'],true));
				}
				if ($email!="") {
					SendMail(siteTitle, $email, $item['Title'], $item['Body'], sitePasswordsMail, 1);
				}
				self::$db->Qry("DELETE FROM notifications WHERE ID=" . $item['ID']);
			}
		}
		return $ItemsCount;

	}
	
	function CheckDependencies() {
		$updateModulesInstalled=false;
		if ($this->tables_required===false) {
			$updateModulesInstalled=true;
		} else {
			$total=count($this->tables_required);
			if ($total>0) {
				$valido=true;
				$x=0;
				$error_in='unknown';
				while (($x<$total) and ($valido)) {	
					if (self::$db->GetDataRecordFromSQL("SHOW TABLES LIKE '" . $this->tables_required[$x] . "'")==false) {
						$valido=false;
						$error_in=$this->tables_required[$x];
					} else {
						$x++;
					}
				}
				if (! $valido) {
					$checked=false;
					if (isset($_SESSION['CheckDependencies'])) {
						if ($_SESSION['CheckDependencies']==$this->module) { $checked=true; }
						$_SESSION['CheckDependencies']="";
						unset($_SESSION['CheckDependencies']);
					}
					if (! $checked) {
						//Obtenemos el modulo
						$carpeta=sitepath . "include/" . $this->module . "/sql/install.sql";
						if (is_file($carpeta)) {
							$sql = explode(";",file_get_contents($carpeta));
							foreach($sql as $query)	{
								try {
									if ($query!="") {self::$db->Qry($query);}
								} catch (Exception $e) { };
							}
							//Actualizamos modules_installed
							if ($this->module!="") { $updateModulesInstalled=true; }
							//Recargamos la página
							$_SESSION['CheckDependencies']=$this->module;
							header("Location: " . $_SERVER['REQUEST_URI']);
							die;
						} else {
							die("The module " . $this->module . " can not be installed automatically. Error in table '" . $error_in . "'");
						}
					} else {
						die("The module " . $this->module . " is not installed and can not be used. Error in table '" . $error_in . "'");
					}
				}	
			}
		}
		if ($updateModulesInstalled) {
			$sql="SELECT * FROM modules_installed WHERE Module='" . $this->module . "'";
			$mod=self::$db->GetDataRecordFromSQL($sql);
			$Upd['System_Action']="new";
			$Upd['System_ID']=-1;
			$Upd['Form_Module']=$this->module;
			$Upd['Form_Version']=$this->version;
			$Upd['Form_BlockMenu']=$this->typemodule;
			if ($mod!==false) {
				$Upd['System_Action']="edit";
				$Upd['System_ID']=$mod['ID'];	
			} else {
				$Upd['Form_Name']=$this->title;
			}
			self::$db->PostToDatabase('modules_installed',$Upd);
		}
		//Check secondary classes...
		if (count($this->InstallActions)>0) {
			foreach($this->InstallActions as $subclass) {
				$submod=self::$db->GetDataRecordFromSQL("SELECT * FROM modules_installed WHERE Module='" . $subclass . "'");
				if ($submod===false) {
					unset($Upd);
					$Upd['System_Action']="new";
					$Upd['System_ID']=-1;
					$Upd['Form_Module']=$subclass;
					$Upd['Form_BlockMenu']=$this->typemodule;
					$Upd['Form_Version']="";
					$Upd['Form_Name']="";
					self::$db->PostToDatabase('modules_installed',$Upd);
				}
			}
		}
		//Añadimos los bloques del menú de administración
		if (count($this->InstallAdminMenu)>0) {
			foreach($this->InstallAdminMenu as $item) {
				$get=self::$db->GetDataRecordFromSQL("SELECT * FROM modules_adminmenu WHERE Block='" . $item['Block'] . "'");
				if ($get===false) {
					$Data['System_Action']="new";
					$Data['System_ID']=-1;
					$Data['Form_Block']=$item['Block'];
					$Data['Form_Title']=$item['Block'];
					$Data['Form_Icon']=$item['Icon'];
					self::$db->PostToDatabase("modules_adminmenu",$Data);
					self::$db->Qry("UPDATE modules_adminmenu SET Orden=ID WHERE Orden=0");
				}
			}
		}
		$this->UpdateVersion();
	}

	function CalcExecTime() {
		//Calc time execution...
		if (! isset($GLOBALS['TimeStart'])) {
			$GLOBALS['AppTimeExec']=0;
		} else {
			$GLOBALS['AppTimeExec']=number_format(microtime(true)-$GLOBALS['TimeStart'],5);
		}
		$GLOBALS['QuerysCount']=self::$db->QuerysCount;
		$GLOBALS['QuerysTimeExec']=number_format(self::$db->QuerysTime,5);
	}

	function UpdateVersion() {
		if ($this->version===false) { return true; }
		$sql="SELECT * FROM modules_installed WHERE Module='" . $this->module . "'";
		$VersionDB=self::$db->GetDataRecordFromSQL($sql);
		if ($VersionDB!==false) {
			//Actualizamos el bloque de menú al que le corresponde esta opción.
			if (($VersionDB['BlockMenu']=="") and ($this->typemodule!="")) { 
				self::$db->GetDataRecordFromSQL("UPDATE modules_installed SET BlockMenu='" . $this->typemodule . "' WHERE Module='" . $this->module . "'");
				self::$db->GetDataRecordFromSQL("UPDATE modules_installed SET Orden=ID WHERE Orden=0");
			}
			$ActVer=FormatVersion($VersionDB['Version']);
			$ReqVer=FormatVersion($this->version);
			while ($ReqVer>$ActVer) {
				$carpeta=sitepath . "include/" . $this->module . "/sql/update_" . $VersionDB['Version'] .".sql";
				if (is_file($carpeta)) {
					$sql = explode(";",file_get_contents($carpeta));
					foreach($sql as $query)	{
						try {
							if (trim($query)!="") {self::$db->Qry($query);}
						} catch (Exception $e) { };
					}
				} else {
					//No hay update para la version actual...
					die("The module " . $this->module . " require database update to version " . $this->version . ", but there is no update file from version " . $VersionDB['Version']);
				}
				$sql="SELECT * FROM modules_installed WHERE Module='" . $this->module . "'";
				$VerUpdated=self::$db->GetDataRecordFromSQL($sql);
				if ($VerUpdated['Version']==$VersionDB['Version']) {
					die("Failed to complete the upgrade: The system reports that the installed version remains the " . $VersionDB['Version'] . " (Required: " . $this->version . ")");
				}
				$VersionDB=$VerUpdated;
				$ActVer=FormatVersion($VersionDB['Version']);
			}
		} else {
			if ($this->module!="") {
				//No hay información sobre el módulo instalado, entendemos que la BD es la adecuada.
				$Upd['System_Action']="new";
				$Upd['System_ID']=-1;
				$Upd['Form_Module']=$this->module;
				$Upd['Form_BlockMenu']=$this->typemodule;
				$Upd['Form_Version']=$this->version;
				$Upd['Form_Orden']=0;
				self::$db->PostToDatabase('modules_installed',$Upd);
				self::$db->Qry("UPDATE modules_installed SET Orden=ID WHERE Orden=0");
			}
		}
	}

	function ParseMetaData($cadena,$datos) {
		$tmp=$cadena;
		while (($pos=strpos($tmp,'{{'))!==false) {
			$tmp=substr($tmp,$pos+2);
			$pos=strpos($tmp,'}}');
			if ($pos!==false) {
				$txt=substr($tmp,0,$pos);
				$cadena=str_replace('{{'.$txt.'}}',stripslashes($datos[$txt]),$cadena);
				$tmp=substr($tmp,$pos+2);
			} else {
				$tmp="";
			}
		}
		if (strpos($cadena, "==")===0) {
			$cadena=eval("return " . substr($cadena,2) . ";");
		}
		return $cadena;
	}

	//Funciones para la autogeneración de plantilla del gestor.
	function AddTableContent($title="",$type="text",$field="",$hidden="",$link="") {
		//Todos los campos entre {{campo}}
		$this->TableHeader[]=$title;
		unset($data);
		$data['Type']=$type;
		$data['Content']=$field;
		$data['HiddenContent']=$hidden;
		$data['Link']=$link;
		$this->TableContent[]=$data;
		return count($this->TableContent)-1;
	}

	function AddTableRowClass($clase="",$condition="") {
		unset($data);
		$data['Class']=$clase;
		$data['Condition']=$condition;
		$this->TableRowConditions[]=$data;
	}

	function AddTableOperations($item=0,$text="{{separator}}",$url="",$condition="",$help="") {
		//Todos los campos entre {{campo}}
		unset($data);
		$data['Text']=$text;
		$data['Condition']=$condition;
		$data['Link']=$url;
		$data['Help']=$help;
		if (! (is_array($this->TableContent[$item]['Content']))) { $this->TableContent[$item]['Content']=array();}
		array_push($this->TableContent[$item]['Content'],$data);
	}

	function AddMainMenu($text="{{separator}}",$url="",$selected=false) {
		unset($data);
		$data['Text']=$text;
		$data['Link']=$url;
		$data['Selected']=$selected;
		array_push($this->MainMenu,$data);
	}

	function AddFormBlock($block) {
		unset($data);
		$data['Name']=$block;
		$data['Fields']=array();
		array_push($this->FormContent,$data);
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
		$data['fieldname']=$field;
		$data['value']=$value;
		$this->FormHiddenContent[]=$data;
	}

	function GetModuleName($module) {
		$return=$module;
		if (strpos($module, '.php')!==false) { $module=str_replace('.php', '', $module); }
		if (strpos($module, 'adm')===0) { $module=str_replace('adm', '', $module); }
		if (isset($this->ModulesNames[$module])) {
			return $this->ModulesNames[$module];
		} else {
			$query_permiso="SELECT ModuleName FROM modules_installed WHERE Module='" . $module . "'";
			$Nombre = self::$db->GetDataFieldFromSQL($query_permiso, "ModuleName");
			if ($Nombre!==false) { 
				if ($Nombre!="") { $return=$Nombre; }
			}
			return $return;
		}
	}

	function ResampleImages($campos=false,$extra_images=false) {
		require_once(sitepath . "lib/images/thumbs.php");
		if ($campos===false) {
			if (count($this->FieldsOfImages)) {
				foreach($this->FieldsOfImages as $f=>$opt) {
					$campos[$f]="";
					if(isset($this->conf->columns[$opt])){ $campos[$f]=$this->conf->Export($opt); }
				}
			} else {
				$ItemsCount=self::$db->GetDataListFromSQL("SHOW COLUMNS FROM " . $this->table,$Items);
				if ($ItemsCount>0) {
					foreach($Items as $item) {
						if ($item['Field']=="Image") { 
							$campos[$item['Field']]="";
							if(isset($this->conf->columns["FirstImageOptions"])){ $campos[$item['Field']]=$this->conf->Export("FirstImageOptions"); }
						}
					}
					foreach($Items as $item) {
						if ($item['Field']=="SecondImage") { 
							$campos[$item['Field']]="";
							if(isset($this->conf->columns["SecondImageOptions"])){ $campos[$item['Field']]=$this->conf->Export("SecondImageOptions"); }
						}
					}
					foreach($Items as $item) {
						if ($item['Field']=="ThirdImage") { 
							$campos[$item['Field']]="";
							if(isset($this->conf->columns["ThirdImageOptions"])){ $campos[$item['Field']]=$this->conf->Export("ThirdImageOptions"); }
						}
					}
				}
			}
		}
		$procesar_extra=false;
		if ($extra_images!==false) {
			if (self::$db->GetDataRecordFromSQL("SHOW TABLES LIKE '" . $this->table . "_images'")!==false) {
				$procesar_extra=true;
				if (($extra_images===false) or ($extra_images===true)) { $extra_images=$this->conf->Export("ImagesOptions"); }
			}
		}
		//Cargamos la lista de elementos
		unset($Items);
		$ItemsCount=self::$db->GetDataListFromSQL("SELECT * FROM " . $this->table,$Items);
		if ($ItemsCount>0) {
			echo "* Procesando " . $ItemsCount . " elementos...<br>";
			foreach($Items as $item) {
				echo "&nbsp; Elemento " . $item['ID'] . "<br>";
				ob_flush();
				set_time_limit(300);
				foreach($campos as $campo=>$opciones) {
					if ($item[$campo]!="") {
						echo "&nbsp; &nbsp; Campo " . $campo . " con valor " . $item[$campo] . " resampleado a " . $opciones . "<br>";
						if (is_file(sitepath. "public/images/" . $item[$campo])) {
							echo "&nbsp; &nbsp; Imagen " . $item[$campo] . "<br>";
							copy(sitepath . "public/images/" . $item[$campo],sitepath . "public/temp/" . $item[$campo]);
							UploadImage($item[$campo],$opciones);
							self::$db->UpdateCacheTag($item[$campo],GetOptionsImagesFolders($opciones,false));
						}
					}
				}

				if ($procesar_extra) {
					unset($Images);
					$ImagesCount=self::$db->GetDataListFromSQL("SELECT * FROM " . $this->table . "_images WHERE IDFather=" . $item['ID'],$Images);
					if ($ImagesCount>0) {
						echo "&nbsp; &nbsp; Procesando " . $ImagesCount . " imagenes adjuntas al elemento<br>";
						ob_flush();
						set_time_limit(300);
						foreach($Images as $image) {
							if (is_file(sitepath. "public/images/" . $image['Image'])) {
								echo "&nbsp; &nbsp; Subimagen " . $image['Image'] . "<br>";
								copy(sitepath . "public/images/" . $image['Image'],sitepath . "public/temp/" . $image['Image']);
								UploadImage($image['Image'],$extra_images);
								//OJO, al renombrar el CacheTag se renombran todas las imagenes.
								//self::$db->UpdateCacheTag($image['Image'],GetOptionsImagesFolders($extra_images,false));
							}
						}
					}
				}
				echo "<br>";
			}
			echo "* Fin del proceso<br>";
		} else {
			echo "! No hay elementos para procesar<br>";
		}
		echo "<br><a href='" . $_SERVER['PHP_SELF'] . "'>Volver al panel</a>";
	}

	function GetItems($conditions="",$paged=false,$order="",$search=false,$translate=true,$SQLselect="") {
		if ($translate) {
			$check=self::$db->GetDataRecordFromSQL("SHOW TABLES FROM " . dbname . " WHERE Tables_in_" . dbname . "='" . $this->table . "_translations'");
			if ($check===false) { $translate=false;}
		}
		$sql="SELECT * FROM " . $this->table . " WHERE ID IS NOT NULL";
		if ($SQLselect!="") { $sql=$SQLselect; }
		if ($conditions!="") { $sql.=" AND " . $conditions; }
		if (($search!==false) and ($search!="")) {
			if ($paged!=="no_force") { $paged=true; }
			$sql2="SHOW COLUMNS FROM " . $this->table . " WHERE Type LIKE 'varchar%' OR Type LIKE '%text'";
			$Total=self::$db->GetDataListFromSQL($sql2,$fields);
			if ($Total>0) {
				$sql.=" AND (";
				$cnt=0;
				foreach($fields as $field) {
					if ($cnt!=0) { $sql.=" OR "; }
					$sql.=$field['Field'] . " LIKE '%" . $search . "%' ";
					$cnt++;
				}
				$sql.=")";
			}
		}
		if ($order=="") {
			$sql.=" ORDER BY ID";
		} else {
			$sql.=" ORDER BY " . $order;
		}
		//echo $sql; //die;
		if ($paged) {
			$this->ItemsCount=self::$db->GetDataListPagedFromSQL($sql,$this->page,$this->offset,$this->Items,$this->ItemsTotalCount);
			$this->paged=true;	
		} else {
			$this->ItemsCount=self::$db->GetDataListFromSQL($sql,$this->Items);
			if (($this->ItemsCount>=1000) and ($paged!=="no_force")) { //Solo forzamos si viene como no_force
				unset($this->Items);
				$this->ItemsCount=self::$db->GetDataListPagedFromSQL($sql,$this->page,$this->offset,$this->Items,$this->ItemsTotalCount);
				$this->paged=true;
			}
		}
		//Add Permalink...
		if ($this->ItemsCount>0) {
			foreach ($this->Items as $idelemento=>$elemento) {
				if (($translate) && (siteLang!=$this->userlang)) { self::$db->GetTranslate($this->table,$elemento['ID'],$this->userlang,$this->Items[$idelemento]); }
				if ((! isset($this->Items[$idelemento]['Permalink'])) and (isset($elemento['ID']))) { $this->Items[$idelemento]['Permalink']=$this->GetPermalink($this->table,$elemento['ID']); }
				if ($this->FieldsOfImages!="") {
					foreach($this->FieldsOfImages as $img=>$cnf) {
						$opt=GetOptionsImages($this->conf->Export($cnf));
						if (count($opt)>0) {
							foreach($opt as $o) {
								$this->Items[$idelemento][$img . "_" . $o['folder']]="";
								if (is_file(sitepath . "public/" . $o['folder'] . "/" . $elemento[$img])) { 
									$this->Items[$idelemento][$img . "_" . $o['folder']]=siteprotocol . sitedomain . "public/" . $o['folder'] . "/" .$elemento[$img];
								}
							}
						}
					}
				}
				$this->GetItemsAddData($this->Items[$idelemento]);
			}
		}
		return $this->ItemsCount;
	}

	function GetItemsAddData(&$data) {
		//Add data to array list
	}

	function ListAdmItems() {
		$this->GetItems("",false,"",$this->search,false);
		$this->PrepareTableList();
		if ($this->paged) {
			$this->LoadTemplate('list_paged.tpl.php');
		} else {
			$this->LoadTemplate('list.tpl.php');
		}
	}

	function NewItem($default_values=false) {
		if ($this->xtras_prefix=="") { $this->xtras_prefix=$this->table; }
		self::$db->InitFormData($this);
		//Load default values...
		if ($default_values!==false) {
			foreach($default_values as $k=>$v) {
				$this->Data[$k]=$v;
			}
		}
		$this->Data['Permalink']="";
		if ($this->FieldsOfImages!="") {
			foreach($this->FieldsOfImages as $img=>$cnf) {
				$this->Data['Rename' . $img]="";
			}
		}
		//Load extras class
		if (self::$db->GetDataRecordFromSQL("SHOW TABLES WHERE Tables_in_" . dbname . "='" . $this->xtras_prefix . "_images'")!==false) {
			$this->XtraImages= new ExtraImages($this,0);
			$this->XtraImages->GetItems();
		}
		if (self::$db->GetDataRecordFromSQL("SHOW TABLES WHERE Tables_in_" . dbname . "='" . $this->xtras_prefix . "_attachments'")!==false) {
			$this->XtraAttachments= new ExtraAttachments($this,0);
			$this->XtraAttachments->GetItems();	
		}
		if (self::$db->GetDataRecordFromSQL("SHOW TABLES WHERE Tables_in_" . dbname . "='" . $this->xtras_prefix . "_links'")!==false) {
			$this->XtraLinks= new ExtraLinks($this,0);	
			$this->XtraLinks->GetItems();
		}
		if (self::$db->GetDataRecordFromSQL("SHOW TABLES WHERE Tables_in_" . dbname . "='" . $this->xtras_prefix . "_videos'")!==false) {
			$this->XtraVideos= new ExtraVideos($this,0);	
			$this->XtraVideos->GetItems();
		}
	}

	function NewAdmItem() {
		$this->NewItem(false);
		$this->TotalLanguages=0;	
		$this->PrepareForm();
		$this->LoadTemplate('edit.tpl.php');
	}

	function EditItem($id="") {
		if ($id=="") { $id=$this->id; }
		if ($this->xtras_prefix=="") { $this->xtras_prefix=$this->table; }
		if (! self::$db->LoadFormData($this,$id)) { return false; } 
		if (! isset($this->Data['Permalink'])) { $this->Data['Permalink']=$this->GetPermalink($this->table,$id); }
		if ($this->FieldsOfImages!="") {
			foreach($this->FieldsOfImages as $img=>$cnf) {
				$opt=GetPriorOptions($this->conf->Export($cnf));
				$opt=str_replace("&", "/", $opt);
				$opt=str_replace("=", "/", $opt);
				$link_rename=$this->module;
				if ($this->module!=$this->class) {
					if (! $this->xtras_RunSubClass) {
						$link_rename.="--" . $this->class ."/";
					} else {
						$link_rename.="/" . $this->class . "_";
					}
				} else {
					$link_rename.="/";
				}
				$link_rename.="images_editor_first/prior/".$id."/option/-1/".$opt."/filename/".$this->Data[$img];
				$this->Data['Rename' . $img]=$link_rename;
				$opt=GetOptionsImages($this->conf->Export($cnf));
				if (count($opt)>0) {
					foreach($opt as $o) {
						$this->Data[$img . "_" . $o['folder']]="";
						if (is_file(sitepath . "public/" . $o['folder'] . "/" . $this->Data[$img])) { 
							$this->Data[$img . "_" . $o['folder']]=siteprotocol . sitedomain . "public/" . $o['folder'] . "/" .$this->Data[$img];
						}
					}
				}
			}
		}
		//Load extras class
		if (self::$db->GetDataRecordFromSQL("SHOW TABLES WHERE Tables_in_" . dbname . "='" . $this->xtras_prefix . "_images'")!==false) {
			$this->XtraImages= new ExtraImages($this,$id);
			$this->XtraImages->GetItems();
		}
		if (self::$db->GetDataRecordFromSQL("SHOW TABLES WHERE Tables_in_" . dbname . "='" . $this->xtras_prefix . "_attachments'")!==false) {
			$this->XtraAttachments= new ExtraAttachments($this,$id);
			$this->XtraAttachments->GetItems();	
		}
		if (self::$db->GetDataRecordFromSQL("SHOW TABLES WHERE Tables_in_" . dbname . "='" . $this->xtras_prefix . "_links'")!==false) {
			$this->XtraLinks= new ExtraLinks($this,$id);	
			$this->XtraLinks->GetItems();
		}
		if (self::$db->GetDataRecordFromSQL("SHOW TABLES WHERE Tables_in_" . dbname . "='" . $this->xtras_prefix . "_videos'")!==false) {
			$this->XtraVideos= new ExtraVideos($this,$id);	
			$this->XtraVideos->GetItems();
		}
		return true;
	}

	function EditAdmItem($id="") {
		$valid=$this->EditItem($id);
		if (! $valid) { 
			$return=siteprotocol . sitedomain . sitePanelFolder ."/home";
			if(isset($_SERVER['HTTP_REFERER'])){ $return=$_SERVER['HTTP_REFERER'];}
			$return.="/error/" . urlencode(base64_encode("El elemento especificado no existe"));
			header("Location: " . $return); exit;
		}
		$this->PrepareLangMenu(true);
		$this->PrepareForm();
		$this->LoadTemplate('edit.tpl.php');
	}

	function PostItem($redirect=true) {
		if ($this->xtras_prefix=="") { $this->xtras_prefix=$this->table; }
		//Patch Checkboxes...
		$sql="SHOW COLUMNS FROM " . $this->table . " WHERE Type LIKE '%int(1)'";
		$Total=self::$db->GetDataListFromSQL($sql,$fields);
		if ($Total>0) {
			foreach($fields as $field) {
				PatchCheckBox($_POST,'Form_' . $field['Field']);
			}
		}
		//Add User, if field exists
		unset($fields);
		$sql="SHOW COLUMNS FROM " . $this->table . " WHERE Type LIKE '%int%' AND (Field LIKE '%author%' OR Field Like '%user%')";
		$Total=self::$db->GetDataListFromSQL($sql,$fields);
		if ($Total>0) {
			foreach($fields as $field) {
				if (! isset($_POST['Form_' . $field['Field']])) { $_POST['Form_' . $field['Field']]=$this->userID; }
			}
		}
		//Add Business, if field exists
		unset($fields);
		$sql="SHOW COLUMNS FROM " . $this->table . " WHERE Type LIKE '%int%' AND Field LIKE '%business%'";
		$Total=self::$db->GetDataListFromSQL($sql,$fields);
		if ($Total>0) {
			foreach($fields as $field) {
				if (! isset($_POST['Form_' . $field['Field']])) { $_POST['Form_' . $field['Field']]=$this->businessID; }
			}
		}
		//Patch Images...
		if ($this->FieldsOfImages!="") {
			foreach($this->FieldsOfImages as $img=>$cnf) {
				if (isset($_POST['Form_' . $img])) {
					if (($_POST['Form_' . $img]=="") and (isset($_POST['Original_' . $img]))) {
						DeleteOptionsImagesFolders($this->conf->Export($cnf),$_POST['Original_' . $img]);
					}	
				}
				if (isset($_POST['Change_' . $img])) {
					if(is_array($_POST['Change_' . $img])) {
						// no está vacio
						$temp=$_POST['Change_' . $img][0];
						unset($_POST['Change_' . $img]);
						$_POST['Form_' . $img]=$temp;
						$process=true;
						if (isset($this->NotProcessImages[$img])) { $process=false; }
						UploadImage($_POST['Form_' . $img],$this->conf->Export($cnf),$process);
					}
				}
			}
		}
		//Patch Files...
		if ($this->FieldsOfFiles!="") {
			foreach($this->FieldsOfFiles as $file=>$cnf) {
				$folder=$cnf;
				if (($folder=="") or ($folder==$file)) { $folder="files"; }
				if (isset($_POST['Form_' . $file])) {
					if (($_POST['Form_' . $file]=="") and (isset($_POST['Original_' . $file]))) {
						DeleteOptionsImagesFolders($folder,$_POST['Original_' . $file]);
					}	
				}
				if (isset($_POST['Change_' . $file])) {
					if (isset($_POST['Change_' . $file])) {
						if(is_array($_POST['Change_' . $file])) {
							$temp=$_POST['Change_' . $file][0];
							unset($_POST['Change_' . $file]);
								$_POST['Form_' . $file]=$temp;
							$this->UploadAttach($_POST['Form_' . $file],'public/' . $folder .'/');
						}
					}	
				}
			}
		}
		$this->BeforePostItem();
		//Save data...
		$purgeHTML=true;
		if (isset($this->NotPurgeHTML)) { $purgeHTML=false; }
		$ActualID = self::$db->PostToDatabase($this->table,$_POST,$purgeHTML);
		//Rename images and files...
		if ($ActualID!=-1) { 
			$title=$ActualID;
			if (isset($_POST['Form_Title'])) { $title=$_POST['Form_Title']; }
			if ((isset($_POST['Form_Name'])) and ($title==$ActualID)) { $title=$_POST['Form_Name']; }
			if ((isset($_POST['Form_UserName'])) and ($title==$ActualID)) { $title=$_POST['Form_UserName']; }
			if ($this->FieldsOfImages!="") {
				foreach($this->FieldsOfImages as $img=>$cnf) {
					if (isset($_POST['Form_' . $img])) {
						$this->RenameUpload($this->table,$img,$ActualID,$_POST['Form_' . $img],$this->module . "-" . $ActualID . "-" . $img . "-" . $title,GetOptionsImagesFolders($this->conf->Export($cnf)));
					}
				}
			}
			if ($this->FieldsOfFiles!="") {
				foreach($this->FieldsOfFiles as $file=>$cnf) {
					$folder=$cnf;
					if (($folder=="") or ($folder==$file)) { $folder="files"; }
					if (isset($_POST['Form_' . $file])) {
						$this->RenameUpload($this->table,$file,$ActualID,$_POST['Form_' . $file],$this->module . "-" . $ActualID . "-" . $file . "-" . $title,array(sitepath . "public/" . $folder ."/"));
					}
				}
			}
		}	
		//Save permalink, is conf folder exists...
		if(isset($this->conf->columns[$this->permalink_conf])) {
			$permafolder=$this->conf->Export($this->permalink_conf);
			if ((isset($this->tablefather)) and (isset($_POST['Form_IDFather']))) { 
				$pf=$this->GetPermalink($this->tablefather,$_POST['Form_IDFather']);
				if ($pf!="") { $permafolder=$pf; }
			}
			$perm="";
			if (isset($_POST['Permalink'])) { $perm=$_POST['Permalink']; }
			$this->SetPermalink($perm,$this->table,$ActualID,'',$this->permalink_action,$permafolder);
		}
		//Set order...
		$sql="SHOW COLUMNS FROM " . $this->table . " WHERE Field='Orden'";
		$Total=self::$db->GetDataListFromSQL($sql,$fields);
		if ($Total>0) { self::$db->Qry("UPDATE " . $this->table . " SET Orden=ID WHERE Orden=0"); }
		//Post extras...
		$volver=siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module;
		if ($this->class!=$this->module) { $volver.="--" . $this->class; }
		if (isset($this->return)) {
			if ($this->return!="") { $volver=$this->return; }
		}
		if (($redirect!==false) and ($redirect!==true)) { 
			$volver=$redirect; 
			$volver=str_replace("{{ActualID}}", $ActualID, $volver);
		}
		//Patch $volver
		if (strpos($volver, "http")===false) {
			$volver=siteprotocol . sitedomain. sitePanelFolder . "/" . $volver;
		}

		$parametros=array();
		if (isset($this->conf)) { $parametros=$this->conf->GetActualConfig(); }
		$old_action=$_POST['System_Action'];
		unset($_POST['System_Action']);
		//EXTRAS: Adjuntos...
		if (self::$db->GetDataRecordFromSQL("SHOW TABLES WHERE Tables_in_" . dbname . "='" . $this->xtras_prefix . "_attachments'")!==false) {
			$this->XtraAttachments= new ExtraAttachments($this,$ActualID);
			$this->XtraAttachments->AddItemRevision(sitepath . 'public/files');
			$this->XtraAttachments->PostAllItems();	
		}
		//EXTRAS: Enlaces...
		if (self::$db->GetDataRecordFromSQL("SHOW TABLES WHERE Tables_in_" . dbname . "='" . $this->xtras_prefix . "_links'")!==false) {
			$this->XtraLinks= new ExtraLinks($this,$ActualID);
			$this->XtraLinks->PostAllItems();	
		}
		//EXTRAS: Videos...
		if (self::$db->GetDataRecordFromSQL("SHOW TABLES WHERE Tables_in_" . dbname . "='" . $this->xtras_prefix . "_videos'")!==false) {
			$this->XtraVideos= new ExtraVideos($this,$ActualID);
			$this->XtraVideos->PostAllItems();	
		}
		//EXTRAS: Imagenes...
		if (self::$db->GetDataRecordFromSQL("SHOW TABLES WHERE Tables_in_" . dbname . "='" . $this->xtras_prefix . "_images'")!==false) {
			$this->XtraImages= new ExtraImages($this,$ActualID);
			$this->XtraImages->AddItemRevision($parametros[$this->xtraimages_options]);
			$this->XtraImages->PostAllItems($volver);
		}
		$this->AfterPostItem($ActualID);
		//Devolvemos al objeto el id correspondiente...
		$this->id=$ActualID;
		//Redirect, or return ID affected.
		$_POST['System_Action']=$old_action;
		if ($redirect!==false) { header("Location: " . $volver);}
		return $ActualID;
	}

	function BeforePostItem() {
		return true;
	}

	function AfterPostItem($ActualID=-1) {
		return true;
	}

	function PostAdmItem($redirect=true) {
		$this->PostItem($redirect);
	}

	function DeleteItem($id=0) {
		if ($id==0) { $id=$this->id; }
		if ($this->xtras_prefix=="") { $this->xtras_prefix=$this->table; }
		//Load data...
		$elemento=self::$db->GetDataRecord($this->table,$id);
		//Delete first images and files...
		if ($this->FieldsOfImages!="") {
			foreach($this->FieldsOfImages as $img=>$cnf) {
				DeleteOptionsImagesFolders($this->conf->Export($cnf),$elemento[$img]);
			}
		}
		if ($this->FieldsOfFiles!="") {
			foreach($this->FieldsOfFiles as $file=>$cnf) {
				DeleteOptionsImagesFolders($cnf,$elemento[$file]);
			}
		}
		//Delete all secondary images...
		if (self::$db->GetDataRecordFromSQL("SHOW TABLES WHERE Tables_in_" . dbname . "='" . $this->xtras_prefix . "_images'")!==false) {
			$TotalRecorridoImages=self::$db->GetDataListFromSQL("SELECT * FROM " . $this->xtras_prefix . "_images WHERE IDFather = " . $elemento['ID'], $RecorridoImages);
			if ($TotalRecorridoImages>0) {
				foreach($RecorridoImages as $adjunto) {
					DeleteOptionsImagesFolders($this->conf->Export($this->xtraimages_options),$adjunto['Image']);
				}
			}	
			self::$db->Qry("DELETE FROM " . $this->xtras_prefix . "_images WHERE IDFather=" . $id);
		}
		if (self::$db->GetDataRecordFromSQL("SHOW TABLES WHERE Tables_in_" . dbname . "='" . $this->xtras_prefix . "_attachments'")!==false) {
			$TotalRecorridoAttachments=self::$db->GetDataListFromSQL("SELECT * FROM " . $this->xtras_prefix . "_attachments WHERE IDFather = " . $elemento['ID'], $RecorridoAttachments);
			if ($TotalRecorridoAttachments>0) {
				foreach($RecorridoAttachments as $adjunto) {
					deletefile(sitepath . '/public/files/' . $adjunto['File']); 
				}
			} 
			self::$db->Qry("DELETE FROM " . $this->xtras_prefix . "_attachments WHERE IDFather=" . $id);  
		}
		if (self::$db->GetDataRecordFromSQL("SHOW TABLES WHERE Tables_in_" . dbname . "='" . $this->xtras_prefix . "_links'")!==false) {
			self::$db->Qry("DELETE FROM " . $this->xtras_prefix . "_links WHERE IDFather=" . $id);  
		}
		if (self::$db->GetDataRecordFromSQL("SHOW TABLES WHERE Tables_in_" . dbname . "='" . $this->xtras_prefix . "_videos'")!==false) {
			self::$db->Qry("DELETE FROM " . $this->xtras_prefix . "_videos WHERE IDFather=" . $id);  
		}
		//Delete database elements...
		self::$db->Qry("DELETE FROM " . $this->table . " WHERE ID=" . $id);
		$this->DeletePermalink($this->table,$id);
		$this->DeleteTranslations($this->table,$id);
		return true;
	}

	function DeleteAdmItem($id=0) {
		echo intval($this->DeleteItem($id));
	}

	function OrderItems($viewfield="Title",$order="Orden",$cond="") {
		$this->GetItems($cond,false,$order,false,false);
		$salida=array();
		if ($this->ItemsCount>0) {
			foreach($this->Items as $item) {
				unset($block);
				$block['id']=$item['ID'];
				$block['title']=$item[$viewfield];
				$block['items']=array();
				$salida[]=$block;
			}
			$this->Items=$salida;
		}
	}

	function OrderAdmItems($viewfield="Title",$order="Orden",$prefix_action="") {
		$this->OrderItems($viewfield,$order);
		$this->script=$this->module;
		if ($this->class!=$this->module) { $this->script.="--" . $this->class; }
		$this->script.='/' . $prefix_action . 'saveorderjson/o/' . $order;
		$this->LoadTemplate('order.tpl.php');
	}

	function SaveOrderJSON($items=false,$father=0) {
		if (($father==0) and ($this->idparent!=0)) { $father=$this->idparent; }
		if ($items==false) { $items=json_decode($_POST['order']); }
		$desc=false;
		if (isset($this->_values['o'])) {
			if (strpos(strtolower($this->_values['o']), "desc")!==false) { $desc=true; }
		}
		$x=1;
		if ($desc) { $x=count($items); }
		$HayIDFather=self::$db->GetDataRecordFromSQL("SHOW COLUMNS FROM " . $this->table . " WHERE Field='IDFather'");
		foreach ($items as $item) {
			$sql="UPDATE " . $this->table . " SET Orden=" . ($x+1);
			if ($HayIDFather!==false) { $sql.=", IDFather=" . $father; }
			$sql.=" WHERE ID=" . $item->id;
			echo $sql . "\r\n";
			self::$db->Qry($sql);
			if ($desc) {
				$x--;
			} else {
				$x++;
			}
			if (isset($item->children)) {$this->SaveOrderJSON($item->children,$item->id);}
		}
	}

	function RunXtraImages($action) {
		if ($this->xtras_prefix=="") { $this->xtras_prefix=$this->table; }
		if (self::$db->GetDataRecordFromSQL("SHOW TABLES LIKE '" . $this->xtras_prefix . "_images'")===false) { die($this->xtras_prefix . "_images not found"); }
		$action=str_replace("images_", "", $action);
		$this->XtraImages= new ExtraImages($this,$this->linkid);
		$parametros=$this->conf->GetActualConfig();
		$this->XtraImages->AddItemRevision($parametros[$this->xtraimages_options]);
		$this->XtraImages->Run($action);
	}

	function RunXtraAttachments($action) {
		if ($this->xtras_prefix=="") { $this->xtras_prefix=$this->table; }
		if (self::$db->GetDataRecordFromSQL("SHOW TABLES LIKE '" . $this->xtras_prefix . "_attachments'")===false) { die($this->xtras_prefix . "_attachments not found"); }
		$action=str_replace("attachments_", "", $action);

		$this->XtraAttachments= new ExtraAttachments($this,$this->linkid);
		$parametros=$this->conf->GetActualConfig();
		$this->XtraAttachments->AddItemRevision(sitepath . 'public/files');
		$this->XtraAttachments->Run($action);
	}

	function RunXtraLinks($action) {
		if ($this->xtras_prefix=="") { $this->xtras_prefix=$this->table; }
		if (self::$db->GetDataRecordFromSQL("SHOW TABLES LIKE '" . $this->xtras_prefix . "_links'")===false) { die($this->xtras_prefix . "_links not found"); }
		$action=str_replace("links_", "", $action);
		$this->XtraLinks= new ExtraLinks($this,$this->linkid);
		$parametros=$this->conf->GetActualConfig();
		$this->XtraLinks->Run($action);
	}

	function RunXtraVideos($action) {
		if ($this->xtras_prefix=="") { $this->xtras_prefix=$this->table; }
		if (self::$db->GetDataRecordFromSQL("SHOW TABLES LIKE '" . $this->xtras_prefix . "_videos'")===false) { die($this->xtras_prefix . "_videos not found"); }
		$action=str_replace("videos_", "", $action);
		$this->XtraVideos= new ExtraVideos($this,$this->linkid);
		$parametros=$this->conf->GetActualConfig();
		$this->XtraVideos->Run($action);
	}

	function RunXtraComments($action) {
		if ($this->xtras_prefix=="") { $this->xtras_prefix=$this->table; }
		if (self::$db->GetDataRecordFromSQL("SHOW TABLES LIKE '" . $this->xtras_prefix . "_comments'")===false) { die($this->xtras_prefix . "_comments not found"); }
		$action=str_replace("comments_", "", $action);
		$this->XtraComments= new ExtraComments($this,$this->linkid);
		$parametros=$this->conf->GetActualConfig();
		$this->XtraComments->Run($action);
	}

	function ImportData($extended=true,$action="import") {
		if (isset($_POST['Table'][0])) {
			//Procesamos
			$filename=$_POST['Change_File'][0];
			$Valores=false;
			$Check=false;
			$str_valores="";
			if ($_POST['Form_DefaultValues']!="") {
				$str_valores=explode(";",$_POST['Form_DefaultValues']);
				foreach ($str_valores as $valor) {
					$partes=explode("=", $valor);
					if (count($partes)==2) {
						$Valores[$partes[0]]=$partes[1];
					}
				}
			}
			if ($_POST['Form_CheckValues']!="") {
				$Check=explode(";",$_POST['Form_CheckValues']);
			}
			//Comprobamos la naturaleza del archivo enviado...
			$extension = preg_split("/\./", strtolower($filename)) ;
			$n = count($extension)-1;
			$extension = $extension[$n];
			$resultado=false;
			$this->table=$_POST['Table'];
			if (($extension=="xls") or ($extension=="xlsx")) { $resultado=self::$db->ImportFromExcel($this,sitepath . "public/temp/" . $filename,$Valores,$Check); }
			if ($extension=="csv") { $resultado=self::$db->ImportFromCSV($this,sitepath . "public/temp/" . $filename,$Valores,$Check); }
			//Borramos el temporal
			unlink(sitepath . "public/temp/" . $filename);
			if ($resultado===false) { 
				$message="error=" . urlencode("No se ha podido procesar el archivo."); 
			} else {
				$message="text=" . urlencode("Se han importado " . $resultado['Processed'] . " elementos y " . $resultado['Unprocessed'] . " no pudieron procesarse.");
			}
			$back=$_POST['Return'];
			if (strpos($back, sitefolder)!==false) { $back=str_replace(sitefolder, "", $back); }
			$back=siteprotocol . sitedomain . $back;
			if (strpos($back, "?")!==false) { $back.="&"; } else { $back.="?"; }
			$back.=$message;
			header("Location: " . $back);
			exit;
		} else {
			$this->BreadCrumb['Importar datos']="";
			$back=$_SERVER['PHP_SELF'];
			if (isset($_SERVER['HTTP_REFERER'])) {
				if ($_SERVER['HTTP_REFERER']!==$back) { $back=$_SERVER['PHP_SELF']; }
			}
			$this->AddMainMenu('Volver',$_SERVER['PHP_SELF']);
			$in_block=$this->AddFormBlock('Importar datos');
			$this->AddFormContent($in_block,'{"Type":"upload","Text":"Archivos Excel o CSV (*.xls, *.xlsx, *.csv)","FieldName":"Form_File","Value":"","UploadType": "file", "UploadItem":"first", "Extensions": "xls,xlsx,csv", "PreviewFolder": "public/temp"}');
			if ($extended) {
				$this->AddFormContent($in_block,'{"Type":"text","Text":"Valores por defecto (nombre_del_campo=<valor> separados por ;)","FieldName":"Form_DefaultValues","Value":"' . addcslashes($this->ImportDefaultValues,'\\"') . '"}');
				$this->AddFormContent($in_block,'{"Type":"text","Text":"Comprobar que no existan duplicados por los campos (separados por ;)","FieldName":"Form_CheckValues","Value":"' . addcslashes($this->ImportCheckFields,'\\"') . '"}');
			} else {
				$this->AddFormHiddenContent("Form_DefaultValues",$this->ImportDefaultValues);
				$this->AddFormHiddenContent("Form_CheckValues",$this->ImportCheckValues);
			}
			$this->AddFormHiddenContent("Table",$this->table);
			$this->AddFormHiddenContent("Return",$back);
			$this->TemplatePostScript=$_SERVER['PHP_SELF'] . "?action=" . $action;
			$this->LoadTemplate($this->module . '_edit.tpl.php');
		}
	}

	function UninstallThisModule($deletedata=false) {
		if ($this->class=="core") { return false; }
		if ($deletedata) {
			//Delete database data...
			if (count($this->tables_required)>0) {
				foreach($this->tables_required as $submodule) {
					self::$db->Qry("DROP TABLE " . $submodule);
				}
			}
			//Delete public files...
			$carpeta=sitepath . "include";
			$directorio=opendir($carpeta);	
			while ($dir = readdir($directorio)) {
				if (($directorio!=".") and ($directorio!="..")) {
					if (is_dir($carpeta . "/" . $directorio)) {
						$subcarpeta=$carpeta . "/" . $directorio;
						$subdirectorio=opendir($subcarpeta);
						while ($archivo = readdir($subdirectorio)) {
							if (is_file($subcarpeta . "/" . $archivo)) {
								if (strpos($archivo, $this->module . "-")===0) {
									unlink($subcarpeta . "/" . $archivo);
								}
							}
						}
					}
				}
			}
		}
		//Delete module instalation data in modules_installed.
		self::$db->Qry("DELETE FROM modules_installed WHERE Module='" . $this->module . "'");
		if (count($this->InstallActions)>0) {
			foreach($this->InstallActions as $subclass) {
				self::$db->Qry("DELETE FROM modules_installed WHERE Module='" . $subclass . "'");
			}
		}
		return true;
	}

	function GetModulePackage() {
		$files=FilesInFolder(sitepath,"include/" . $this->module);
		if (is_file(sitepath . "include/" . $this->module . "/sql/install.list")) {
			$contents=file_get_contents(sitepath . "include/" . $this->module . "/sql/install.list");
			if ($contents!==false) {
				if (strlen($contents)>2) {
					foreach(preg_split("/((\r?\n)|(\r\n?))/", $contents) as $lf){
						$lfiles=FilesInFolder(sitepath,$lf);
						if (count($lfiles)>0) { $files=array_merge($files,$lfiles); }
					}
				}
			}
		}
		if (count($files)>0) {
			$filename=sitepath . "public/temp/" . $this->module . ".zip";
			unlink($filename);
			$zip = new ZipArchive;
			if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
				$output['Success']=0;
				$output['Message']="Can't create output file";
				$output['File']=$this->module . ".zip";
				$output['Version']=$this->version;
	    		return json_encode($output);
			}
			foreach($files as $file) {
				$zip->addFile(sitepath . $file,$file);
			}
			$zip->close();
		}
		$output['Success']=1;
		$output['Message']="File created in public/temp/";
		$output['File']=$this->module . ".zip";
		$output['Version']=$this->version;
		return json_encode($output);
	}

	//PARTE PÚBLICA
	function RunApp(){
		//Check Maintenance
		if ((siteMaintenanceActive==1) and ($this->userLevel!=99)) { require_once("in_maintenance.html"); exit; }
		define("siteFrontend", 1);
		$this->HeadTitle=siteTitle;
		$this->HeadDescription=siteHeadDescription;
		$this->HeadImage="templates/" .$this->template . "/images/headerlogo.png";
		if(isset($_GET['action'])){
			if(isset($_GET['url'])){		
				$this->params = $this->GetParamsPermalink($_GET['url']);
			} else {
				$this->params = $_GET;
			}
			$module=$this->params['module'];
		}else{
			$module='index'; 
		}
		//Forzamos la carga del idioma si en la URL hay un parámetro LANG
		if (isset($this->params['lang'])) {	
			$this->ChangeLang($this->params['lang']);
			$this->LoadLang(true);
		}
		//Comprobamos si existe un script propio para el módulo en la carpeta web...
		//Sino, abrimos el dispatcher de la clase principal
		if (is_file(sitepath . "web/" . $module .".php")) {
			require_once(sitepath . "web/".$module . ".php");
			exit;
		}
		if (is_file(sitepath . "include/" . $module . "/dispatcher.php")) {
			require_once(sitepath . "include/" . $module . "/dispatcher.php");
			exit;
		}
		//Como no se ha ejecutado nada, se muestra el error 404
		if (is_file(sitepath . "templates/" . $this->template . "/adapter.php")) {
			require_once(sitepath . "templates/" . $this->template . "/adapter.php");
		}
		header("HTTP/1.0 404 Not Found");
		header("Status: 404 Not Found");
		$this->LoadTemplatePublic('404.tpl.php');
	}

	function RunAction() {
		if ($this->action=="list") { $this->ListAdmItems(); }
		if ($this->action=="new") { $this->NewAdmItem(); }
		if ($this->action=="edit") { $this->EditAdmItem(); }
		if ($this->action=="post") { $this->PostAdmItem(); }
		if ($this->action=="delete") { $this->DeleteAdmItem(); }
		if ($this->action=="listorder") { $this->OrderAdmItems(); }
		if ($this->action=="saveorderjson") { $this->SaveOrderJSON(); }
		if ($this->action=="translate") { 
			if (isset($_POST['System_ID'])) {
				$this->PostTranslation();
			} else {
				$this->EditTranslation(); 
			}
		}
		if ($this->action=="resampleimages") { $this->ResampleImages(); }
		if ($this->action=="zippackage") { echo $this->GetModulePackage(); }
		if (strpos($this->action, "images_")!==false) { $this->RunXtraImages($this->action); }
		if (strpos($this->action, "attachments_")!==false) { $this->RunXtraAttachments($this->action); }
		if (strpos($this->action, "links_")!==false) { $this->RunXtraLinks($this->action); }
		if (strpos($this->action, "videos_")!==false) { $this->RunXtraVideos($this->action); }
		if (strpos($this->action, "comments_")!==false) { $this->RunXtraComments($this->action); }
	}
	
	
	function __destruct(){

	}
}

?>