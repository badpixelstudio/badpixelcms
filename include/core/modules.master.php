<?php
require_once(sitepath . "include/core/core.class.php");

class MasterModules extends Core{
	var $title = 'Módulos';
	var $class = 'modules';
	var $module = 'core';
	var $action = 'list';
	var $table = 'modules_installed';
	var $mod="core";	
	var $ServerModules=array();
	var $TotalServerModules=0;

	
	function __construct($values) {
		$_SESSION['ItsUpdated']="";
		unset($_SESSION['ItsUpdated']);
		parent::__construct($values);  
		$title=$this->GetModuleName($this->class);
		if ($title!=$this->class) { $this->title=$title; }
		if (isset($values['mod'])) { $this->mod=$values['mod']; }
		//breadcrumb
		$this->BreadCrumb[$this->title]=$this->class . "--" . $this->class;
	}

	function GetServerVersions() {
		$api=siteURLUpdatesAPI . "/getmodules";
		try {
			$content=@file_get_contents($api);
			$content=json_decode($content,true);
			if ($content!==null) {
				if ($content['Success']==1) {
					foreach($content['Result']['Items'] as $item) {
						$this->ServerModules[$item['ModuleName']]=$item['LatestVersion'];
						$this->TotalServerModules++;
					}
				}
			}
		}
		catch (Exception $e) {
			$this->TotalServerModules=0;
		}
	}

	function GetStatus() {
		$this->GetServerVersions();
		if ($this->TotalServerModules==0) {
			$this->error=_("No se ha podido conectar con el servidor de actualizaciones. Revise la configuración");
		}
		$carpeta=sitepath . "include";
		$directorio=opendir($carpeta);	
		while ($dir = readdir($directorio)) {
			unset($Item);
			if (is_dir($carpeta . '/' . $dir)) {
				if (($dir!=".") and ($dir!=="..")) {
					if ((is_file($carpeta . "/" . $dir . "/" . $dir . ".master.php")) and ($dir!="extras")) {
						require_once($carpeta . "/" . $dir . "/" . $dir . ".master.php");
						$Item['Module']=$dir;
						$Item['Name']=$dir;
						$Item['Installed']=0;
						$Item['ActualVersion']="";
						$Item['LatestVersion']="";	
						$Item['Updatable']=0;				
						$ver=parent::$db->GetDataRecordFromSQL("SELECT * FROM modules_installed WHERE Module='" . $dir . "'");
						if ($ver!==false) {
							$Item['Name']=$ver['ModuleName'];
							$Item['Installed']=1;
							$Item['ActualVersion']=($ver['Version']);
							$Item['LatestVersion']="-No disponible-";
							//CheckUpdate
							if (isset($this->ServerModules[$dir])) {
								$Item['LatestVersion']=$this->ServerModules[$dir];
							}
							//Is update available?
							if (FormatVersion($Item['LatestVersion'])>FormatVersion($Item['ActualVersion'])) { $Item['Updatable']=1; }
						}
						if (true) {  //$Item['ActualVersion']!="";
							$Item['ViewState']=_("No instalado");
							if ($Item['Installed']==1) { 
								$Item['ViewState']=_("Instalado");
								if ($Item['Updatable']==0) {
									$Item['ViewState'].=", " . _("actualizado");
								} else {
									$Item['ViewState'].=", " . _("actualizable");
								}
							}

							$this->Items[]=$Item; 
						}
					}
				}
			}
		}
		$this->ItemsCount=count($this->Items);
	}

	function GetUpdatesAvailables() {
		$total=0;
		$this->GetServerVersions();
		if ($this->TotalServerModules==0) {
			$_SESSION['ItsUpdated']=0;
			return 0;
		}
		$TotalMods=parent::$db->GetDataListFromSQL("SELECT * FROM modules_installed WHERE Version<>''",$mods);
		if ($TotalMods>0) {
			foreach($mods as $mod) {
				if (isset($this->ServerModules[$mod['Module']])) {
					if (FormatVersion($this->ServerModules[$mod['Module']])>FormatVersion($mod['Version'])) { $total++; } 
				}
			}
		}
		$_SESSION['ItsUpdated']=$total;
		return $total;
	}

	function ListAdmItems() {
		$this->GetStatus();
		$this->PrepareTableList();
		$this->LoadTemplate('list.tpl.php');
	}

	function Install() {
		//Check module status...
		$status=parent::$db->GetDataRecordFromSQL("SELECT * FROM " . $this->table . " WHERE Module='" . $this->mod . "'");
		if ($status===false) {
			if (is_file(sitepath . "include/" . $this->mod . "/" . $this->mod . ".master.php")) {
				require_once(sitepath . "include/" . $this->mod . "/" . $this->mod . ".master.php");
				//Cargamos la primera clase que nos encontremos en el documento...
				$php_code = file_get_contents(sitepath . "include/" . $this->mod . "/" . $this->mod . ".master.php");
				$classes = get_php_classes($php_code);
				if (count($classes)>0) {
					$loadClass=$classes[0];
					$Run=new $loadClass(null);
					return true;
				}
			}
		}
		return false;
	}

	function InstallAll() {
		$this->GetStatus();
		if ($this->ItemsCount>0) {
			foreach($this->Items as $item) {
				if ($item['Installed']!==1) { 
					$this->mod=$item['Module'];
					set_time_limit(5000);
					try {
						$this->Install();
					} catch (Exception $e) {}
				}
			}
		}
	}

	function Uninstall() {
		//Check module status...
		$status=parent::$db->GetDataRecordFromSQL("SELECT * FROM " . $this->table . " WHERE Module='" . $this->mod . "'");
		if ($status!==false) {
			if (is_file(sitepath . "include/" . $this->mod . "/" . $this->mod . ".master.php")) {
				$this->PrepareForm();
				$this->LoadTemplate('edit.tpl.php');		
			}
		} else {
			header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->class . "/error/" . urlencode(base64_encode(_("El modulo no existe o no está instalado"))));
		}
	}

	function UninstallNow() {
		$result=0;
		$resultbackup=0;
		$status=parent::$db->GetDataRecordFromSQL("SELECT * FROM " . $this->table . " WHERE Module='" . $this->mod . "'");
		if ($status!==false) {
			if (is_file(sitepath . "include/" . $this->mod . "/" . $this->mod . ".master.php")) {
				require_once(sitepath . "include/" . $this->mod . "/" . $this->mod . ".master.php");
				//Create backup
				if ($_POST['CreateBackup']) {
					if (is_file(sitepath . "include/backup/backup.master.php")) { 
						require_once(sitepath . "include/backup/backup.master.php");
						$_POST['Form_Name']=date('Y-m-d H:i') . " " . dbname . " " . _("previo desinstalación") . " " . $this->mod;
						$Backup= new MasterBackup(null);
						$Backup->CreateBackup(false);
						$resultbackup=1;
					}
				}
				$delete_data=false;
				if ($_POST['DeleteData']==1) { $delete_data=true; }
				//Load the class...
				$php_code = file_get_contents(sitepath . "include/" . $this->mod . "/" . $this->mod . ".master.php");
				$classes = get_php_classes($php_code);
				if (count($classes)>0) {
					$loadClass=$classes[0];
					$class= new $loadClass(null);
					$result=intval($class->UninstallThisModule($delete_data));
				}
				if (($result==1) and ($_POST['DeleteModule']==1)) {
					//Try set chmod 7777 all module files
					chmod(sitepath . "include/" . $this->mod, 0777);
					$carpeta=sitepath . "include/" . $this->mod;
					DeleteFolder($carpeta);
					//Check is deleted...
					if (! is_dir(sitepath . "include/" . $this->mod)) { $result=2; }
				}
			}
		}
		if ($result==0) { $message="/error/" . urlencode(base64_encode(_("No se pudo desinstalar el módulo") . " " . $this->mod)); }	
		if ($result==1) { $message="/error/" . urlencode(base64_encode(_("Se ha desinstalado el módulo") . " " . $this->mod)); }	
		if ($result==2) { $message="/error/" . urlencode(base64_encode(_("Se ha desinstalado el módulo") . " " . $this->mod . " " . _("y se eliminó del servidor"))); }
		if ($resultbackup==1) { $message.="/text/" . urlencode(base64_encode(_("Se creó una copia de seguridad de la base de datos con nombre") . " " . $_POST['Form_Name'])); }
		header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->class . "/list" . $message);
	}

	function NewAdmItem() {
		$values['LastMod']=date("d/m/Y");
		$values['Priority']="0.5";
		$values['ChangeFreq']="daily";
		$this->NewItem($values);
		$this->TotalLanguages=0;	
		$this->PrepareForm();
		$this->LoadTemplate('edit.tpl.php');
	}

	function Update($force_install=false) {
		//Check Update...
		$api=siteURLUpdatesAPI . "/latest/" . $this->mod;
		$content=file_get_contents($api);
		$content=json_decode($content,true);
		if ($content!==null) {
			if ($content['Sucess']==1) {
				$ver=$content['Result']['Version'];
				$file=$content['Result']['URL'];
				$procesar=$force_install;
				if (! $procesar) {
					//Get actual version...
					$actver=parent::$db->GetDataFieldFromSQL("SELECT Version FROM modules_installed WHERE Module='" . $this->mod . "'","Version");
					if ($actver!==false) {
						if (FormatVersion($ver)>FormatVersion($actver)) { 
							$procesar=true; 
						} else {
							header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->class . "/error/" . urlencode(base64_encode(_("No se puede actualizar") . " " . $this->mod . ": no existen versiones más actuales que instalar"))); exit;
						}
					} else {
						header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->class . "/error/" . urlencode(base64_encode(_("No se puede actualizar") . " " . $this->mod . ": el módulo no está instalado"))); exit;
					}
				}
				if ($procesar) {
					//Generate file to download...
					if (! is_dir(sitepath . "public/updates")) {
						mkdir(sitepath . "public/updates");
						chmod(sitepath . "public/updates", 0777);
					}
					$updater=fopen(sitepath . "public/updates/process.list","w+");
					$line=$this->mod . ";";
					if ($force_install) { 
						$line.="upd;"; 
					} else {
						$line.="ins:";
					}
					$line.=$file . "\n\r";
					fputs($updater,$line);
					fclose($updater);
					header("Location: " . siteprotocol . sitedomain . "public/update.php"); exit;
				} else {
					
				}
			}
		}	
		header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->class . "/error/" . urlencode(base64_encode(_("No hay versión disponible para instalar o actualizar") . " " . $this->mod)));
	}

	function UpdateAll() {
		//Generate file to download...
		if (! is_dir(sitepath . "public/updates")) {
			mkdir(sitepath . "public/updates");
			chmod(sitepath . "public/updates", 0777);
		}
		$cont=0;
		$updater=fopen(sitepath . "public/updates/process.list","w+");
		$carpeta=sitepath . "include";
		$directorio=opendir($carpeta);	
		while ($dir = readdir($directorio)) {
			unset($Item);
			if (is_dir($carpeta . '/' . $dir)) {
				if (($dir!=".") and ($dir!=="..")) {
					if (is_file($carpeta . "/" . $dir . "/" . $dir . ".master.php")) {
						$installed=parent::$db->GetDataRecordFromSQL("SELECT * FROM modules_installed WHERE Module='" . $dir . "'");
						if ($installed!==false) {
							$api=siteURLUpdatesAPI . "/latest/" . $this->mod;
							$content=file_get_contents($api);
							$content=json_decode($content,true);
							if ($content!==null) {
								if ($content['Sucess']==1) {
									$ver=$content['Result']['Version'];
									$file=$content['Result']['URL'];
									if (FormatVersion($ver)>FormatVersion($installed['Version'])) {
										$line=$dir . ";upd;" . $file  . "\n\r";
										fputs($updater,$line);
										$cont++;
									}
								}
							}
						}
					}
				}
			}
		}
		fclose($updater);
		if ($cont>0) {
			header("Location: " . siteprotocol . sitedomain . "public/update.php");
		} else {
			header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->class . "/error/" . urlencode(base64_encode(_("Todos los módulos ya están actualizados"))));
		}
	}

	function MasterDownloadModules() {
		$api=siteURLUpdatesAPI . "/getmodules";
		$content=file_get_contents($api);
		$content=json_decode($content,true);
		if ($content!==null) {
			if ($content['Sucess']==1) {
				foreach($content['Result']['Items'] as $item) {
					$this->ServerModules[]=$item;
					$this->TotalServerModules++;
				}
			}
		}
		$this->ItemsCount=0;
		unset($this->Items);
		foreach($this->ServerModules as $module) {
			//Check actual install status
			$installed=parent::$db->GetDataRecordFromSQL("SELECT * FROM modules_installed WHERE Module='" . $module['ModuleName'] . "'");
			$add=false;
			if ($installed===false) { 
				$this->Items[]=$module;
				$this->ItemsCount++;
			}
		}
		$this->PrepareMasterDownloadList();
		$this->LoadTemplate('list.tpl.php');
	}

	function DownloadModules() {
		$this->PrepareDownloadForm();
		$this->LoadTemplate('edit.tpl.php');
	}

	function DownloadNow() {
		$module="";
		if (isset($_POST['Package'])) {
			$module=$_POST['Package'];
			$module=str_replace("badpixelcms://cms.badpixel.es/", "", $module);
		}
		$api=siteURLUpdatesAPI . "/latest/" . $module;
		$content=file_get_contents($api);
		$content=json_decode($content,true);
		if ($content!==null) {
			if ($content['Sucess']==1) {
				$ver=$content['Result']['Version'];
				$file=$content['Result']['URL'];
				if (! is_dir(sitepath . "public/updates")) {
						mkdir(sitepath . "public/updates");
						chmod(sitepath . "public/updates", 0777);
					}
				$updater=fopen(sitepath . "public/updates/process.list","w+");
				$line=$module . ";ins;" . $file;
				fputs($updater,$line);
				fclose($updater);
				header("Location: " . siteprotocol . sitedomain . "public/update.php"); exit;
			}
		}
		header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->class . "/download/error/" . urlencode(base64_encode(_("El paquete solicitado no existe o no dispones de permisos para descargarlo"))));
	}

	function PrepareTableList() {
		$this->AddMainMenu('Instalar todo',$this->class . '/install-all');
		$this->AddMainMenu('Actualizar todo',$this->class . '/update-all');
		if (siteEnableModuleInstall) { $this->AddMainMenu('Descargar módulo',$this->class . '/download'); }
		$this->AddTableRowClass('warning','{{Updatable}}==1'); 
		$this->AddTableRowClass('success','{{Installed}}==1');
		$this->AddTableRowClass('danger','{{Installed}}==0');
		$this->AddTableContent('Módulo','data','{{Name}} ({{Module}})');
		$this->AddTableContent('Estado','data','{{ViewState}}');
		$this->AddTableContent('Versión instalada','data','{{ActualVersion}}');
		$this->AddTableContent('Versión disponible','data','{{LatestVersion}}');
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block,'Instalar',$this->class . '/install/mod/{{Module}}','{{Installed}}==0');
		$this->AddTableOperations($in_block,'Actualizar',$this->class . '/update/mod/{{Module}}','{{Updatable}}==1');
		$this->AddTableOperations($in_block,'Configurar...','config/mod/{{Module}}/id/' . $this->businessID,'{{Installed}}==1');
		$this->AddTableOperations($in_block,'Desinstalar',$this->class . '/uninstall/mod/{{Module}}','(({{Installed}}==1) and ("{{Module}}"!="core"))');
	}

	function PrepareMasterDownloadList() {
		$this->AddMainMenu('Módulos instalados',$this->class);
		$this->AddTableContent('Módulo','data','{{Title}} ({{ModuleName}})');
		$this->AddTableContent('Tipo','data','{{Type}}');
		$this->AddTableContent('Últ. versión','data','{{LatestVersion}} ({{DateVersion}}');
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block,'Descargar',$this->class . '/download-now/mod/{{ModuleName}}');
	}

	function PrepareDownloadForm() {
		$this->title=_("Instalar módulo") . " " . $this->mod;
		$in_block=$this->AddFormBlock('Instalar');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Paquete a instalar","FieldName":"Package","Value":""}');
		$this->TemplatePostScript=$this->class . '/download-now';
	}

	function PrepareForm() {
		$this->title=_("Desinstalar el módulo") . " " . $this->mod;
		$in_block=$this->AddFormBlock('Desinstalar');
		$this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Hacer una copia de seguridad de la base de datos antes de desinstalar","FieldName":"CreateBackup","Value":"1"}');
		$this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Borrar todos los datos referentes al módulo","FieldName":"DeleteData","Value":"0"}');
		$this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Eliminar el módulo del servidor, si es posible","FieldName":"DeleteModule","Value":"0"}');
		$this->TemplatePostScript=$this->class . '/uninstall-now/mod/' . $this->mod;
	}
	
	function RunAction() { 
		parent::RunAction();
		if ($this->action=="install") { 
			$this->Install();
			header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->class);
		}
		if ($this->action=="install-all") { 
			$this->InstallAll();
			header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->class);
		}
		if ($this->action=="uninstall") { $this->Uninstall();}
		if ($this->action=="uninstall-now") { $this->UninstallNow();}
		if ($this->action=="update") { $this->Update();}
		if ($this->action=="update-all") { $this->UpdateAll(false);}
		if ($this->action=="download") { $this->DownloadModules();}
		if ($this->action=="download-now") { $this->DownloadNow();}
		if ($this->action=="masterdownload") { $this->MasterDownloadModules();}
		if ($this->action=="masterdownload-now") { $this->Update(true);}
		if ($this->action=="checkupdates") { echo $this->GetUpdatesAvailables();}
	}
	
	
	function __destruct(){

	}

}
?>