<?php
require_once(sitepath . "include/core/core.class.php");
if (is_file(sitepath . "include/movies/movies.class.php")) { require_once(sitepath . "include/movies/movies.class.php"); }
if (is_file(sitepath . "lib/oauth/gplus/postToGooglePlus.php")) { require_once(sitepath . "lib/oauth/gplus/postToGooglePlus.php"); }
if (is_file(sitepath . "lib/oauth/pinterest/postToGooglePlus.php")) { require_once(sitepath . "lib/oauth/gplus/postToPinterest.php"); }

class MasterSocialMedia extends Core{
	
	//Inicializamos valores por defecto
	var $title = 'Social Media';
	var $module = 'core';
	var $class = 'socialmedia';
	var $table = 'socialmedia_publish';
	var $typemodule='tools';
	var $InstallAdminMenu=array(array('Block' => 'tools', 'Icon' => 'fa-french'));
	var $version=false;	

	function __construct($values) {
		parent::__construct($values);  
		$title=$this->GetModuleName($this->class);
		if ($title!=$this->class) { $this->title=$title; }
		$this->conf=new ConfigCore($this->businessID);
		$this->GetSystemVariables();
		//breadcrumb
		$this->BreadCrumb[$this->title]=$this->class;
	}
	
	function ListAdmItems($view="") {
		if ($view=="")  { $view=$this->view; }
		$sql_paginas = "SELECT * FROM " . $this->table . " WHERE ID>0";
		if (siteMulti) {
			$sql_paginas = "SELECT " . $this->table . ".*, business.Name as businessName FROM " . $this->table . " LEFT JOIN business ON " . $this->table . ".IDBusiness=business.ID WHERE " . $this->table . ".ID>0";	
		}
		if ($view=="sended") { $sql_paginas .= " AND Published=1"; }
		if (($view=="") or ($view=="pending")) { $sql_paginas .= " AND Published=0"; }		
		$sql_paginas.=" ORDER BY Orden";
		$this->ItemsCount = parent::$db->GetDataListFromSQL($sql_paginas,$this->Items);
		return $this->ItemsCount;
	}
	
	function NewAdmItem() {
		parent::$db->InitFormData($this);
		$this->BreadCrumb['Crear']='';
		$this->Data['IDBusiness']=$this->businessID;
		$this->Data['DatePublish']=date('d/m/Y');
		$this->Data['HourPublish']='09:00';
		$this->Data['PublishTwitter']=1;
		$this->Data['PublishFacebook']=1;
		$this->Data['PublishGPlus']=1;
		$this->Data['System_PublishNow']=0;
		$this->Data['Return']='';
		if (isset($_SESSION['SocialMedia'])) {
			if ($_SESSION['SocialMedia']['Twitter']) { $this->Data['Twitter']=$_SESSION['SocialMedia']['Twitter']; }
			if ($_SESSION['SocialMedia']['FBUrl']) { 
				$this->Data['FBUrl']=$_SESSION['SocialMedia']['FBUrl']; 
				$this->Data['GPlusUrl']=$_SESSION['SocialMedia']['FBUrl']; 
			}
			if ($_SESSION['SocialMedia']['FBTitle']) { 
				$this->Data['FBTitle']=$_SESSION['SocialMedia']['FBTitle']; 
				$this->Data['GPlusDescription']=$_SESSION['SocialMedia']['FBTitle']; 
			}
			if ($_SESSION['SocialMedia']['FBDescription']) { 
				$this->Data['FBDescription']=$_SESSION['SocialMedia']['FBDescription']; 
				//Se ignora pues solo coge el título
				if ($this->Data['GPlusDescription']!="") { $this->Data['GPlusDescription'].='. '; }
				$this->Data['GPlusDescription'].=$_SESSION['SocialMedia']['FBDescription']; 
			}
			if ($_SESSION['SocialMedia']['FBImage']) { 
				$this->Data['FBImage']=$_SESSION['SocialMedia']['FBImage']; 
				$this->Data['GPlusImage']=$_SESSION['SocialMedia']['FBImage']; 
			}
			if ($_SESSION['SocialMedia']['Return']) { $this->Data['Return']=$_SESSION['SocialMedia']['Return']; }
			$this->Data['System_PublishNow']=1;
		}
		if (siteTwitterPreTweet!="") { $this->Data['Form_Twitter']=siteTwitterPreTweet . " " . $this->Data['Twitter']; }
		if (siteTwitterPostTweet!="") { $this->Data['Form_Twitter'].=" " . siteTwitterPostTweet; }
		$this->PrepareForm();
		$this->LoadTemplate($this->module . '_edit.tpl.php');		
	}
	
	function EditAdmItem($id="") {
		if ($id=="") { $id=$this->id; }
		parent::$db->LoadFormData($this,$id);
		$this->BreadCrumb['Editar']='';
		$this->Data['System_PublishNow']=0;
		$this->Data['Return']='';
		$this->PrepareForm();
		$this->LoadTemplate($this->module . '_edit.tpl.php');		
	}
	
	function PostAdmItem($redirect=true) {
		$result=false;
		PatchDate($_POST,'Form_DatePublish');;
		PatchCheckBox($_POST,'Form_PublishTwitter');
		PatchCheckBox($_POST,'Form_PublishFacebook');
		PatchCheckBox($_POST,'Form_Published');
		PatchCheckBox($_POST,'System_PublishNow');
		$ActualID= $this->PostToDatabase($this->table,$_POST);
		unset($_SESSION['SocialMedia']);
		if ($_POST['System_PublishNow']==1) {
			$result=$this->Publish($ActualID);
		} 
		$redirigir=$this->class . "/list";
		if (isset($_POST['System_Return'])) { 
			if ($_POST['System_Return']!="") { $redirigir=$_POST['System_Return']; }
			$redirect=false;
		}
		if ($redirect) { header("Location: " . $redirigir); }
		return $result;
	}
	
	function DeleteItem($id=0) {
		return $this->Delete();
	}
	
	function DeletePending() {
		$sql="DELETE FROM " . $this->table . " WHERE Published=0";
		parent::$db->Qry($sql);
		return 1;	
	}
	
	function DeleteProcessed() {
		$sql="DELETE FROM " . $this->table . " WHERE Published=1";
		parent::$db->Qry($sql);
		return 1;	
	}
	
	function LoadDataForPublish($incluir="",$empresa="") {
		//Comprobamos que módulos debemos incluir
		//En blanco todos. Con el parametro /all permite incluir todos excepto los que tengan - delante del nombre del modulo.
		$modulos=array("calendar","blog","galleries","videos","dtapas","movies","pharmacies");
		foreach($modulos as $modulo) {
			if (($incluir=="") or (strpos($incluir,"/all")!==false)) {
				$habilitado[$modulo]=1;
			} else {
				$habilitado[$modulo]=0;
			}
		}
		if ($incluir!="") {
			$partes=explode(',',$incluir);	
			foreach($partes as $parte) {
				$parte=trim($parte);
				$desactiva=strpos($parte,'-');
				if ($desactiva!==false) { 
					$parte=substr($parte,$desactiva+1);
					$desactiva=true;
				}
				foreach($modulos as $modulo) {
					if ($modulo==$parte) {
						if ($desactiva)	{ 
							$habilitado[$modulo]=0;
						} else {
							$habilitado[$modulo]=1;
						}
					}
				}
			}
		}
		//Si mandamos un ID de empresa, solo aplicamos a la empresa elegida.
		if (($empresa!="") and ($empresa!="0")) { $empresa=" AND IDBusiness=" . $empresa; } else { $empresa=""; }
		//Empezamos a generar los eventos a publicar
		$publicaciones=array();
		$total_publicaciones=0;
		if ((self::$db->GetDataRecordFromSQL("SHOW TABLES LIKE 'pharmacies_guards'")!==false) and 
			(is_file(sitepath . "include/pharmacies/pharmacies.config.php")) and ($habilitado['pharmacies']==1)) { 
			require_once(sitepath . "include/pharmacies/pharmacies.config.php");
			$pharma_conf=new ConfigPharmacies();
			$sql="SELECT * FROM pharmacies_guards WHERE DateGuard='" .  date("Y-m-d") . "' ORDER BY ID DESC LIMIT 1";
			$farmacia=parent::$db->GetDataRecordFromSQL($sql);	
			if ($farmacia!==false) {
				$permalink=$pharma_conf->Export("PermalinkFolder");
				//Generamos el texto base, por si no hay datos estructurados
				$texto=nl2br(stripslashes($farmacia['Details']));
				$texto=str_replace('<br />','<br>',$texto);
				$partes=explode('<br>',$texto);
				$num_partes=count($partes);
				if ($num_partes<2) { $texto=str_replace('<br>',' ',$texto); }
				if ($num_partes>2) { $texto=$partes[2] . "(" . $partes[3] . ")"; }
				if ($num_partes>3) { $texto.=" y otras"; }
				$texto=LimitString('Farmacia de guardia: ' . $texto,90) . " " . siteprotocol . sitedomain . $permalink;
				$fb=$farmacia['Details'];
				$image="";
				//Obtenemos los datos del establecimiento.
				if ($farmacia['IDFather']!=0) {
					$tabla="pharmacies";
					if($pharma_conf->Check("StoreEnableBusinessVinculation")) { $tabla="business"; }
					$Pharma=parent::$db->GetDataRecord($tabla,$farmacia['IDFather']);
					if ($Pharma!==false) {
						$texto="Farmacia de guardia " . date("d/m/Y") . ": " . stripslashes($Pharma['Name']);
						if($Pharma['Street']!="") { $texto.=" en " . stripslashes($Pharma['Street']); }
						$fb=$texto;
						$texto.=" " . siteprotocol . sitedomain . $permalink;
						$fb.=" " . $farmacia['Details'];
						if (is_file(sitepath . "public/images/" . $Pharma['SecondImage'])) {
							$image=siteprotocol . sitedomain . "public/images/" . $Pharma['SecondImage'];
						} elseif (is_file(sitepath . "public/images/" . $Pharma['Image'])) {
							$image=siteprotocol . sitedomain . "public/images/" . $Pharma['Image'];
						}
					}
				}
				unset($add);
				$add['System_Action']="new";
				$add['System_ID']=-1;
				$add['Form_IDBusiness']=0;
				$add['Form_PublishTwitter']=1;
				$add['Form_Twitter']=$texto;
				$add['Form_PublishFacebook']=1;
				$add['Form_FBUrl']=siteprotocol . sitedomain . $permalink;
				$add['Form_FBTitle']="Farmacia de guardia de hoy";
				$add['Form_FBDescription']=stripslashes($fb);
				$add['Form_FBDescription']=str_replace('<br>',' ',$add['Form_FBDescription']);
				$add['Form_FBDescription']=str_replace('<br />',' ',$add['Form_FBDescription']);
				$add['Form_FBDescription']=str_replace('<li>',' ',$add['Form_FBDescription']);
				$add['Form_FBDescription']=str_replace('&nbsp;',' ',$add['Form_FBDescription']);
				$add['Form_FBDescription']=htmlspecialchars_decode($add['Form_FBDescription']);
				$add['Form_FBDescription']=strip_tags($add['Form_FBDescription']);
				$add['Form_FBDescription']=trim($add['Form_FBDescription']);
				$add['Form_FBImage']=$image;
				$add['Form_PublishGPlus']=1;
				$add['Form_GPlusUrl']=$add['Form_FBUrl'];
				$add['Form_GPlusTitle']=$add['Form_FBTitle'];
				$add['Form_GPlusDescription']=$add['Form_FBDescription'];
				$add['Form_GPlusImage']='';
				if ($add['Form_GPlusTitle']!="") { $add['Form_GPlusDescription']=$add['Form_GPlusTitle'] . ": " . $add['Form_GPlusDescription']; }
				$add['Form_GPlusImage'].=$add['Form_FBImage'];				
				$add['Form_DatePublish']=date("Y-m-d");
				$add['Form_HourPublish']='09:00';
				$add['Form_Published']=0;
				$publicaciones[]=$add;
				$total_publicaciones++;	
			}
		}
		
		
		if ((self::$db->GetDataRecordFromSQL("SHOW TABLES LIKE 'calendar_events'")!==false) and ($habilitado['calendar']==1)) { 
			$sql="SELECT * FROM calendar_events WHERE Active=1 AND Canceled=0 AND (DatePublish='" . date("Y-m-d") . "' OR DateExpire='" . date("Y-m-d") . "')" . $empresa . " ORDER BY DateExpire";
			$total=self::$db->GetDataListFromSQL($sql,$eventos);
			if ($total>0) {
				foreach($eventos as $evento) {
					//Corregimos la hora...
					$hora=99;
					$minuto=99;
					if (preg_match("/(\d+):(\d+)/", $evento['Hours'], $horario)) {
						$hora=$horario[1];
						$minuto=$horario[2];
					}
					if (($hora==99) and (preg_match("/(\d+),(\d+)/", $evento['Hours'], $horario))) {
						$hora=$horario[1];
						$minuto=$horario[2];									
					}
					if (($hora==99) and (preg_match("/(\d+)h/", $evento['Hours'], $horario))) {
						$hora=$horario[1];
						$minuto=0;									
					}
					$hora=sprintf('%1$02d:%1$02d',$hora,$minuto);
					//Añadimos el evento...
					unset($add);
					$permalink=$this->GetPermalink('calendar_events',$evento['ID']);
					$add['System_Action']="new";
					$add['System_ID']=-1;
					$add['Form_IDBusiness']=$this->businessID;
					$add['Form_PublishTwitter']=1;
					$add['Form_Twitter']=stripslashes($evento['Title']) . " " . siteprotocol . sitedomain . $permalink;
					if ($evento['DatePublish']!=$evento['DateExpire']) {
						if ($evento['DatePublish']==date("Y-m-d")) { $add['Form_Twitter']="Comienza " . $add['Form_Twitter']; }
						if ($evento['DateExpire']==date("Y-m-d")) { $add['Form_Twitter']="Finaliza " . $add['Form_Twitter']; }
					}
					if (siteTwitterPreTweet!="") { $add['Form_Twitter']=siteTwitterPreTweet . " " . $add['Form_Twitter']; }
					if (siteTwitterPostTweet!="") { $add['Form_Twitter'].=" " . siteTwitterPostTweet; }
					$add['Form_PublishFacebook']=1;
					$add['Form_FBUrl']=siteprotocol . sitedomain . $permalink;
					$add['Form_FBTitle']=stripslashes($evento['Title']);
					if ($evento['DatePublish']!=$evento['DateExpire']) {
						if ($evento['DatePublish']==date("Y-m-d")) { $add['Form_FBTitle']="Comienza " . $add['Form_FBTitle']; }
						if ($evento['DateExpire']==date("Y-m-d")) { $add['Form_FBTitle']="Finaliza " . $add['Form_FBTitle']; }
					}
					$add['Form_FBDescription']="";
					if (isset($evento['Summary'])) { 
						$add['Form_FBDescription']=stripslashes($evento['Summary']);
						$add['Form_FBDescription']=str_replace('<br>',' ',$add['Form_FBDescription']);
						$add['Form_FBDescription']=str_replace('<br />',' ',$add['Form_FBDescription']);
						$add['Form_FBDescription']=str_replace('<li>',' ',$add['Form_FBDescription']);
						$add['Form_FBDescription']=str_replace('&nbsp;',' ',$add['Form_FBDescription']);
						$add['Form_FBDescription']=htmlspecialchars_decode($add['Form_FBDescription']);
						$add['Form_FBDescription']=strip_tags($add['Form_FBDescription']);
						$add['Form_FBDescription']=trim($add['Form_FBDescription']);
					}
					if (($add['Form_FBDescription']=="") and (isset($evento['Page']))) { 
						$add['Form_FBDescription']=stripslashes($evento['Page']); 
						$add['Form_FBDescription']=str_replace('<br>',' ',$add['Form_FBDescription']);
						$add['Form_FBDescription']=str_replace('<br />',' ',$add['Form_FBDescription']);
						$add['Form_FBDescription']=str_replace('<li>',' ',$add['Form_FBDescription']);
						$add['Form_FBDescription']=str_replace('&nbsp;',' ',$add['Form_FBDescription']);
						$add['Form_FBDescription']=htmlspecialchars_decode($add['Form_FBDescription']);
						$add['Form_FBDescription']=strip_tags($add['Form_FBDescription']);
						$add['Form_FBDescription']=trim($add['Form_FBDescription']);
						$add['Form_FBDescription']=limitstring($add['Form_FBDescription'],200);
					}
					//Si finaliza el evento, no se pasa ninguna descripción
					if ($evento['DateExpire']==date("Y-m-d")) { $add['Form_FBDescription']=""; }
					$add['Form_FBImage']="";
					if ($evento['Image']!="") { $add['FBImage']=siteprotocol . sitedomain . "public/images/" . $evento['Image']; }
					if (($add['Form_FBImage']=="") and ($evento['SecondImage']!="")) { $add['FBImage']=siteprotocol . sitedomain . "public/images/" . $evento['SecondImage']; }
					$add['Form_PublishGPlus']=1;
					$add['Form_GPlusUrl']=$add['Form_FBUrl'];
					$add['Form_GPlusTitle']=$add['Form_FBTitle'];
					$add['Form_GPlusDescription']=$add['Form_FBDescription'];
					$add['Form_GPlusImage']='';
					if ($add['Form_GPlusTitle']!="") { $add['Form_GPlusDescription']=$add['Form_GPlusTitle'] . ". " . $add['Form_GPlusDescription']; }
					$add['Form_GPlusImage'].=$add['Form_FBImage'];
					$add['Form_DatePublish']=date("Y-m-d");
					$add['Form_HourPublish']=$hora;
					$add['Form_Published']=0;
					$publicaciones[]=$add;
					$total_publicaciones++;
				}
				//Recolocamos los eventos por hora
				if ($total_publicaciones>0) { $publicaciones=orderMultiDimensionalArray ($publicaciones, 'Form_HourPublish'); }
			}
		}	
		if ((self::$db->GetDataRecordFromSQL("SHOW TABLES LIKE 'blogs_entries'")!==false)  and ($habilitado['blog']==1)) {
			$sql="SELECT * FROM blogs_entries WHERE Status='post' AND Visible=1 AND EnableShare=1 AND DatePublish LIKE '%" . AddDays(date("Y-m-d"),-1) . "%' ORDER BY ID";
			$total=self::$db->GetDataListFromSQL($sql,$entradas);
			if ($total>0) {
				foreach($entradas as $entrada) {
					//Añadimos la entrada...
					unset($add);
					$permalink=$this->GetPermalink('blogs_entries',$entrada['ID']);
					$add['System_Action']="new";
					$add['System_ID']=-1;
					$add['Form_IDBusiness']=$this->businessID;
					$add['Form_PublishTwitter']=1;
					$add['Form_Twitter']=stripslashes($entrada['Title']) . " " . siteprotocol . sitedomain . $permalink;
					if (siteTwitterPreTweet!="") { $add['Form_Twitter']=siteTwitterPreTweet . " " . $add['Form_Twitter']; }
					if (siteTwitterPostTweet!="") { $add['Form_Twitter'].=" " . siteTwitterPostTweet; }
					$add['Form_PublishFacebook']=1;
					$add['Form_FBUrl']=siteprotocol . sitedomain . $permalink;
					$add['Form_FBTitle']=stripslashes($entrada['Title']);
					$add['Form_FBDescription']=stripslashes($entrada['Page']);
					$add['Form_FBDescription']=str_replace('<br>',' ',$add['Form_FBDescription']);
					$add['Form_FBDescription']=str_replace('<br />',' ',$add['Form_FBDescription']);
					$add['Form_FBDescription']=str_replace('<li>',' ',$add['Form_FBDescription']);
					$add['Form_FBDescription']=str_replace('&nbsp;',' ',$add['Form_FBDescription']);
					$add['Form_FBDescription']=htmlspecialchars_decode($add['Form_FBDescription']);
					$add['Form_FBDescription']=strip_tags($add['Form_FBDescription']);
					$add['Form_FBDescription']=trim($add['Form_FBDescription']);
					$add['Form_FBDescription']=limitstring($add['Form_FBDescription'],200);
					$add['Form_FBImage']="";
					if ($entrada['Image']!="") { $add['FBImage']=siteprotocol . sitedomain . "public/images/" . $entrada['Image']; }
					$add['Form_PublishGPlus']=1;
					$add['Form_GPlusUrl']=$add['Form_FBUrl'];
					$add['Form_GPlusTitle']=$add['Form_FBTitle'];
					$add['Form_GPlusDescription']=$add['Form_FBDescription'];
					$add['Form_GPlusImage']='';
					if ($add['Form_GPlusTitle']!="") { $add['Form_GPlusDescription']=$add['Form_GPlusTitle'] . ". " . $add['Form_GPlusDescription']; }
					$add['Form_GPlusImage'].=$add['Form_FBImage'];				
					$partes=explode(' ',$entrada['DatePublish']);
					$add['Form_DatePublish']=date("Y-m-d");
					$add['Form_HourPublish']=$partes[1];
					$add['Form_Published']=0;
					$publicaciones[]=$add;
					$total_publicaciones++;					
				}
			}
		}
		if ((self::$db->GetDataRecordFromSQL("SHOW TABLES LIKE 'galleries'")!==false) and ($habilitado['galleries']==1)) {
			$sql="SELECT * FROM galleries WHERE Active=1 AND DatePublish LIKE '%" . AddDays(date("Y-m-d"),-1) . "%'" . $empresa . " ORDER BY Orden";
			$total=self::$db->GetDataListFromSQL($sql,$galerias);
			if ($total>0) {
				foreach($galerias as $galeria) {
					//Añadimos la entrada...
					unset($add);
					$permalink=$this->GetPermalink('galleries',$galeria['ID']);
					$add['System_Action']="new";
					$add['System_ID']=-1;
					$add['Form_IDBusiness']=$this->businessID;
					$add['Form_PublishTwitter']=1;
					$add['Form_Twitter']='Nueva galeria: ' . stripslashes($galeria['Title']) . " " . siteprotocol . sitedomain . $permalink;
					if (siteTwitterPreTweet!="") { $add['Form_Twitter']=siteTwitterPreTweet . " " . $add['Form_Twitter']; }
					if (siteTwitterPostTweet!="") { $add['Form_Twitter'].=" " . siteTwitterPostTweet; }
					$add['Form_PublishFacebook']=1;
					$add['Form_FBUrl']=siteprotocol . sitedomain . $permalink;
					$add['Form_FBTitle']='Nueva galeria: ' . stripslashes($galeria['Title']);
					$add['Form_FBDescription']=stripslashes($galeria['Description']);
					$add['Form_FBDescription']=str_replace('<br>',' ',$add['Form_FBDescription']);
					$add['Form_FBDescription']=str_replace('<br />',' ',$add['Form_FBDescription']);
					$add['Form_FBDescription']=str_replace('<li>',' ',$add['Form_FBDescription']);
					$add['Form_FBDescription']=str_replace('&nbsp;',' ',$add['Form_FBDescription']);
					$add['Form_FBDescription']=htmlspecialchars_decode($add['Form_FBDescription']);
					$add['Form_FBDescription']=strip_tags($add['Form_FBDescription']);
					$add['Form_FBDescription']=trim($add['Form_FBDescription']);
					$add['Form_FBDescription']=limitstring($add['Form_FBDescription'],200);
					$add['Form_FBImage']="";
					if ($galeria['Image']!="") { $add['FBImage']=siteprotocol . sitedomain . "public/images/" . $galeria['Image']; }
					$add['Form_PublishGPlus']=1;
					$add['Form_GPlusUrl']=$add['Form_FBUrl'];
					$add['Form_GPlusTitle']=$add['Form_FBTitle'];
					$add['Form_GPlusDescription']=$add['Form_FBDescription'];
					$add['Form_GPlusImage']='';
					if ($add['Form_GPlusTitle']!="") { $add['Form_GPlusDescription']=$add['Form_GPlusTitle'] . ". " . $add['Form_GPlusDescription']; }
					$add['Form_GPlusImage'].=$add['Form_FBImage'];					
					$partes=explode(' ',$galeria['DatePublish']);
					$add['Form_DatePublish']=date("Y-m-d");
					$add['Form_HourPublish']='20:00';
					$add['Form_Published']=0;
					$publicaciones[]=$add;
					$total_publicaciones++;					
				}
			}
		}		
	if ((self::$db->GetDataRecordFromSQL("SHOW TABLES LIKE 'singlevideos'")!==false) and ($habilitado['videos']==1)) {
			$sql="SELECT * FROM singlevideos WHERE DatePublish LIKE '%" . AddDays(date("Y-m-d"),-1) . "%'" . $empresa . " ORDER BY ID DESC";
			$total=self::$db->GetDataListFromSQL($sql,$videos);
			if ($total>0) {
				foreach($videos as $video) {
					//Añadimos la entrada...
					unset($add);
					$permalink=$this->GetPermalink('singlevideos',$video['ID']);
					$add['System_Action']="new";
					$add['System_ID']=-1;
					$add['Form_IDBusiness']=$this->businessID;
					$add['Form_PublishTwitter']=1;
					$add['Form_Twitter']='Nuevo video: ' . stripslashes($video['Title']) . " " . siteprotocol . sitedomain . $permalink;
					if (siteTwitterPreTweet!="") { $add['Form_Twitter']=siteTwitterPreTweet . " " . $add['Form_Twitter']; }
					if (siteTwitterPostTweet!="") { $add['Form_Twitter'].=" " . siteTwitterPostTweet; }
					$add['Form_PublishFacebook']=1;
					$add['Form_FBUrl']=siteprotocol . sitedomain . $permalink;
					$add['Form_FBTitle']='Nuevo video: ' . stripslashes($video['Title']);
					$add['Form_FBDescription']=stripslashes($video['Description']);
					$add['Form_FBDescription']=str_replace('<br>',' ',$add['Form_FBDescription']);
					$add['Form_FBDescription']=str_replace('<br />',' ',$add['Form_FBDescription']);
					$add['Form_FBDescription']=str_replace('<li>',' ',$add['Form_FBDescription']);
					$add['Form_FBDescription']=str_replace('&nbsp;',' ',$add['Form_FBDescription']);
					$add['Form_FBDescription']=htmlspecialchars_decode($add['Form_FBDescription']);
					$add['Form_FBDescription']=strip_tags($add['Form_FBDescription']);
					$add['Form_FBDescription']=trim($add['Form_FBDescription']);
					$add['Form_FBDescription']=limitstring($add['Form_FBDescription'],200);
					$add['Form_FBImage']="";
					if ($video['Image']!="") { $add['FBImage']=siteprotocol . sitedomain . "public/images/" . $video['Image']; }
					$add['Form_PublishGPlus']=1;
					$add['Form_GPlusUrl']=$add['Form_FBUrl'];
					$add['Form_GPlusTitle']=$add['Form_FBTitle'];
					$add['Form_GPlusDescription']=$add['Form_FBDescription'];
					$add['Form_GPlusImage']='';
					if ($add['Form_GPlusTitle']!="") { $add['Form_GPlusDescription']=$add['Form_GPlusTitle'] . ". " . $add['Form_GPlusDescription']; }
					$add['Form_GPlusImage'].=$add['Form_FBImage'];
					$partes=explode(' ',$video['DatePublish']);
					$add['Form_DatePublish']=date("Y-m-d");
					$add['Form_HourPublish']='20:00';
					$add['Form_Published']=0;
					$publicaciones[]=$add;
					$total_publicaciones++;					
				}
			}
		}			
		
		if ((self::$db->GetDataRecordFromSQL("SHOW TABLES LIKE 'special_dtapas'")!==false) and ($habilitado['dtapas']==1)) {
			$sql="SELECT * FROM special_dtapas WHERE Active=1 AND DatePublish LIKE '%" . AddDays(date("Y-m-d"),-1) . "%' ORDER BY ID";
			$total=self::$db->GetDataListFromSQL($sql,$dtapas);
			if ($total>0) {
				foreach($dtapas as $tapas) {
					//Añadimos la entrada...
					unset($add);
					$permalink=$this->GetPermalink('special_dtapas',$tapas['ID']);
					$add['System_Action']="new";
					$add['System_ID']=-1;
					$add['Form_IDBusiness']=$this->businessID;
					$add['Form_PublishTwitter']=1;
					$add['Form_Twitter']='DTapeo en ' . stripslashes($tapas['Title']) . " " . siteprotocol . sitedomain . $permalink;
					if (siteTwitterPreTweet!="") { $add['Form_Twitter']=siteTwitterPreTweet . " " . $add['Form_Twitter']; }
					if (siteTwitterPostTweet!="") { $add['Form_Twitter'].=" " . siteTwitterPostTweet; }
					$add['Form_PublishFacebook']=1;
					$add['Form_FBUrl']=siteprotocol . sitedomain . $permalink;
					$add['Form_FBTitle']='DTapeo en ' . stripslashes($tapas['Title']);
					$add['Form_FBDescription']=stripslashes($tapas['Page']);
					$add['Form_FBDescription']=str_replace('<br>',' ',$add['Form_FBDescription']);
					$add['Form_FBDescription']=str_replace('<br />',' ',$add['Form_FBDescription']);
					$add['Form_FBDescription']=str_replace('<li>',' ',$add['Form_FBDescription']);
					$add['Form_FBDescription']=str_replace('&nbsp;',' ',$add['Form_FBDescription']);
					$add['Form_FBDescription']=htmlspecialchars_decode($add['Form_FBDescription']);
					$add['Form_FBDescription']=strip_tags($add['Form_FBDescription']);
					$add['Form_FBDescription']=trim($add['Form_FBDescription']);
					$add['Form_FBDescription']=limitstring($add['Form_FBDescription'],200);
					$add['Form_FBImage']="";
					if ($tapas['Image']!="") { $add['FBImage']=siteprotocol . sitedomain . "public/images/" . $tapas['Image']; }
					$add['Form_PublishGPlus']=1;
					$add['Form_GPlusUrl']=$add['Form_FBUrl'];
					$add['Form_GPlusTitle']=$add['Form_FBTitle'];
					$add['Form_GPlusDescription']=$add['Form_FBDescription'];
					$add['Form_GPlusImage']='';
					if ($add['Form_GPlusTitle']!="") { $add['Form_GPlusDescription']=$add['Form_GPlusTitle'] . ". " . $add['Form_GPlusDescription']; }
					$add['Form_GPlusImage'].=$add['Form_FBImage'];						
					$partes=explode(' ',$tapas['DatePublish']);
					$add['Form_DatePublish']=date("Y-m-d");
					$add['Form_HourPublish']='20:00';
					$add['Form_Published']=0;
					$publicaciones[]=$add;
					$total_publicaciones++;					
				}
			}
		}		
		
		if ((is_file(sitepath . "include/movies/movies.class.php"))  and ($habilitado['movies']==1)) { 
			$cine=new Movies($_GET);
			$cine->PublicMovieListings(date("Y-m-d"),"news");
			if ($cine->ItemsCount>0) {
				foreach($cine->Items as $peli) {
					//Añadimos la entrada...
					unset($add);
					$permalink=$peli['Permalink'];
					$add['System_Action']="new";
					$add['System_ID']=-1;
					$add['Form_IDBusiness']=$this->businessID;
					$add['Form_PublishTwitter']=1;
					$add['Form_Twitter']='Cartelera de cine: ' . stripslashes($peli['Title']) . " " . siteprotocol . sitedomain . $permalink;
					if (siteTwitterPreTweet!="") { $add['Form_Twitter']=siteTwitterPreTweet . " " . $add['Form_Twitter']; }
					if (siteTwitterPostTweet!="") { $add['Form_Twitter'].=" " . siteTwitterPostTweet; }
					$add['Form_PublishFacebook']=1;
					$add['Form_FBUrl']=siteprotocol . sitedomain . $permalink;
					$add['Form_FBTitle']='';
					$add['Form_FBDescription']='Hoy en la cartelera de cine: ' . stripslashes($peli['Title']);
					foreach($peli['Prog'] as $item=>$horario) {
						$add['Form_FBDescription'].=" en " . $horario['Theater'];
						if ($horario['Is3D']==1) { $add['Form_FBDescription'].= "(3D) "; }
						$add['Form_FBDescription'].=" a las " . $horario['Hours'];
						if ((count($peli['Prog'])-1)>$item) { $add['Form_FBDescription'].=", "; }
					}
					$add['Form_FBDescription']=str_replace('<br>',' ',$add['Form_FBDescription']);
					$add['Form_FBDescription']=str_replace('<br />',' ',$add['Form_FBDescription']);
					$add['Form_FBDescription']=str_replace('<li>',' ',$add['Form_FBDescription']);
					$add['Form_FBDescription']=str_replace('&nbsp;',' ',$add['Form_FBDescription']);
					$add['Form_FBDescription']=htmlspecialchars_decode($add['Form_FBDescription']);
					$add['Form_FBDescription']=strip_tags($add['Form_FBDescription']);
					$add['Form_FBDescription']=trim($add['Form_FBDescription']);
					$add['Form_FBDescription']=limitstring($add['Form_FBDescription'],200);
					$add['Form_FBImage']="";
					if ($peli['Image']!="") { $add['Form_FBImage']=siteprotocol . sitedomain . "public/images/" . $peli['Image']; }
					$add['Form_PublishGPlus']=1;
					$add['Form_GPlusUrl']=$add['Form_FBUrl'];
					$add['Form_GPlusTitle']=$add['Form_FBTitle'];
					$add['Form_GPlusDescription']=$add['Form_FBDescription'];
					$add['Form_GPlusImage']='';
					if ($add['Form_GPlusTitle']!="") { $add['Form_GPlusDescription']=$add['Form_GPlusTitle'] . ". " . $add['Form_GPlusDescription']; }
					$add['Form_GPlusImage'].=$add['Form_FBImage'];
					$add['Form_DatePublish']=date("Y-m-d");
					$add['Form_HourPublish']='20:00';
					$add['Form_Published']=0;
					$publicaciones[]=$add;
					$total_publicaciones++;					
				}
			}
		}			
		
		//Abrimos todos los elementos para el dia de ayer que no fueron procesados por anticuados
		$sql="SELECT * FROM socialmedia_publish WHERE DatePublish='" . AddDays(date('Y-m-d'),-1) . "' AND Orden=0 AND Published=0";
		$total_bd=self::$db->GetDataListFromSQL($sql,$registros);
		if ($total_bd>0) {
			foreach ($registros as $reg) {
				unset($add);
				$add['System_Action']="edit";
				$add['System_ID']=$reg['ID'];
				$add['Form_DatePublish']=date('Y-m-d');
				$add['Form_HourPublish']=$reg['HourPublish'];
				$add['Form_Orden']=$reg['Orden'];
				$publicaciones[]=$add;
				$total_publicaciones++;
			}
		}
		
		if ($total_publicaciones>0) {
			//Generamos un array con los horas aproximadas a las que se deberían enviar las publicaciones.
			$horario=array();	
			$intervalos=$this->conf->Export('AutoSocialMediaMaxIntervals');
			for($x=0;$x<=$intervalos;$x++) {
				$horario[]=AddMinutes($this->conf->Export('AutoSocialMediaHourFirstPublish'), $this->conf->Export('AutoSocialMediaIntervalMinutes')*$x);
			}
			if ($total_publicaciones<$intervalos) {
				$cada_intervalos=intval($intervalos/$total_publicaciones);
				$eventos_intervalo=	1;
			} else {
				$cada_intervalos=1;
				$eventos_intervalo=	intval($total_publicaciones/$intervalos);
			}
			$intervalo_actual=0;
			$eventos_acumulados=0;
			foreach($publicaciones as $idpubli=>$publicacion) {
				//Comprobamos que la hora del elemento no sea posterior a la estimada de publicación...
				if (substr(strval($publicacion['Form_HourPublish']),0,5)<substr(strval($horario[$intervalo_actual]),0,5)) {
					$x=$intervalos;
					$enc=false;
					while ((! $enc) and ($x>=0)) {
						if (substr($publicacion['Form_HourPublish'],0,5)<substr($horario[$x],0,5)) {
							$x--;
						} else {
							$enc=true;
						}
					}
					$pos=$x-2;
					if ($pos<0) { $pos=0; }
					$publicacion['Form_Orden']=$pos;
				} else {
					$publicacion['Form_Orden']=$intervalo_actual;
					$eventos_acumulados++;
				}
				if ($eventos_acumulados>=$eventos_intervalo) { 
					$eventos_acumulados=0; 
					$intervalo_actual=$intervalo_actual+$cada_intervalos; 
				}
				if ($intervalo_actual>=$intervalos) { 
					$intervalo_actual=$intervalo_actual-$intervalos; 
				}	
				//Lo guardamos en la base de datos...
				self::$db->PostToDatabase($this->table,$publicacion);
			}
		}
		//Almacenamos los valores en las variables en base de datos...
		$this->EditSystemVariable('SocialMediaDate',date('Y-m-d'));
		$this->EditSystemVariable('SocialMediaInterval',0);
	}
	
	function CronProcess($silencemode=true) {
		$horario=array();	
		$intervalos=$this->conf->Export('AutoSocialMediaMaxIntervals');
		for($x=0;$x<=$intervalos;$x++) {
			$horario[]=AddMinutes($this->conf->Export('AutoSocialMediaHourFirstPublish'), $this->conf->Export('AutoSocialMediaIntervalMinutes')*$x);
		}
		$currentHour=date("H:i:s");
		$currentBlock=$this->SystemVariables['SocialMediaInterval'];
		if (! isset($horario[$currentBlock])) { return "-2"; }
		if ($currentHour<$horario[$currentBlock]) { return "-1"; }
		if (! $silencemode) {
			echo "Procesando bloque " . $this->SystemVariables['SocialMediaInterval'] . " de publicaciones en redes sociales de fecha " . $this->SystemVariables['SocialMediaDate'] . "\r\n";	
		}
		$sql="SELECT ID FROM " . $this->table . " WHERE IDBusiness=" . $this->businessID . " AND DatePublish='" . date("Y-m-d") . "' AND Orden=" . $this->SystemVariables['SocialMediaInterval'];
		$total=parent::$db->GetDataListFromSQL($sql,$lista);
		if ($total>0) {
			foreach ($lista as $publicar) {
				$resultado=$this->Publish($publicar['ID']);	
				if (! $silencemode) { echo str_replace("<br>","\r\n",$resultado); }
			}
		} else {
			if (! $silencemode) { echo "No hay publicaciones para procesar\r\n"; }
		}
		$valor=intval($this->SystemVariables['SocialMediaInterval'])+1;
		$this->EditSystemVariable('SocialMediaInterval',$valor);
		return $total;
	}
	
	function Publish($id) {
		parent::$db->LoadFormData($this,$id);
		if ($this->Data['Published']==0) {
			$cuerpo="";
			if ((siteTwitterEnabled) and ($this->Data['PublishTwitter']==1)) {
				Tweet(stripslashes($this->Data['Twitter']),$this->Data['FBImage']);
				$cuerpo.="Twitter: " . $this->Data['Twitter'] . "<br>";
			}
			if ((siteFacebookEnabled) and ($this->Data['PublishFacebook']==1)) {
				if (($this->Data['FBTitle']!="") or ($this->Data['FBImage']=="")) {
					FBPostWall(stripslashes($this->Data['FBDescription']),$this->Data['FBUrl'],$this->Data['FBImage'],stripslashes($this->Data['FBTitle']));
				} else {
					FBUploadImage(stripslashes($this->Data['FBDescription']),$this->Data['FBImage']);
				}
				$cuerpo.="Facebook: " . stripslashes($this->Data['FBDescription']) . " en " . $this->Data['FBUrl'] . "<br>";
			}
			if ((siteGoogleAPIEnabled) and ($this->Data['PublishGPlus']==1)) {
				$loginerror=doConnectToGooglePlus2(siteGoogleAccount, siteGooglePassword);
				if (!$loginerror) {
					if ($this->Data['GPlusUrl']!="") {
						$lnk = doGetGoogleUrlInfo2($this->Data['GPlusUrl']);
						doPostToGooglePlus2($this->Data['GPlusDescription'], $lnk, siteGoogleAPIProfile);
					} else {
						$lnk = array('img'=>$this->Data['GPlusImage']); 
						doPostToGooglePlus2($this->Data['GPlusDescription'], $lnk, siteGoogleAPIProfile);
					}
					$cuerpo.="Google+: " . stripslashes($this->Data['GPlusDescription']) . " en " . $this->Data['GPlusUrl'] . "<br>";
				} else {
					$cuerpo.="Google+: Error de inicio de sesión.";
				}
			}			
			$cuerpo.="<br>";	
			//SendMail(siteTitle, siteMainMail, _("Publicacion SocialMedia"), $cuerpo, siteMainMail, 1);
			$sql="UPDATE " . $this->table . " SET Published=1 WHERE ID=" . $id;
			parent::$db->Qry($sql);
			return $cuerpo;
		} else {
			return false;
		}
	}
	
	function PrepareTableList() {
		$this->AddMainMenu('Crear',$this->class . '/new');
		$this->AddMainMenu();
		$this->AddMainMenu('Borrar enviados',$this->class . '/deleteprocessed');
		$this->AddMainMenu('Cancelar pendientes',$this->class . '/cancel');
		$this->AddMainMenu();
		if ($this->view!="all") { $this->AddMainMenu('Ver todos',$this->class . '/list/view/all'); }
		if ($this->view!="sended") { $this->AddMainMenu('Ver enviados',$this->class . '/listview/sended'); }
		if (($this->view!="") and ($this->view!="pending")) { $this->AddMainMenu('Ver pendientes',$this->class . '/list'); }
		$this->AddTableRowClass('success','{{Published}}==1');
		$this->AddTableContent('Publicación','data','{{Twitter}}');
		$this->AddTableContent('Fecha','data','{{DatePublish}}');
		$this->AddTableContent('Bloque','data','{{Orden}}');
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block,'Editar',$this->class . '/edit/id/{{ID}}');
		$this->AddTableOperations($in_block,'Eliminar',$this->class . '/delete/id/{{ID}}');
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('Twitter');
		$this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Publicar en Twitter","FieldName":"Form_PublishTwitter","Value":"' . $this->Data['PublishTwitter'] . '"}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Mensaje en Twitter","FieldName":"Form_Twitter","Value":"' . addslashes($this->Data['Twitter']) . '"}');
		$in_block=$this->AddFormBlock('Facebook');
		$this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Publicar en Facebook","FieldName":"Form_PublishFacebook","Value":"' . $this->Data['PublishFacebook'] . '"}');
		$this->AddFormContent($in_block,'{"Type":"url","Text":"URL de Página a compartir","FieldName":"Form_FBUrl","Value":"' . addslashes($this->Data['FBUrl']) . '"}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Título de la publicación","FieldName":"Form_FBTitle","Value":"' . addslashes($this->Data['FBTitle']) . '"}');
		$this->AddFormContent($in_block,'{"Type":"textarea","Text":"Contenido de la publicación","FieldName":"Form_FBDescription","Value":"' . addslashes($this->Data['FBDescription']) . '"}');
		$this->AddFormContent($in_block,'{"Type":"url","Text":"URL de imágen","FieldName":"Form_FBImage","Value":"' . addslashes($this->Data['FBImage']) . '"}');
		$in_block=$this->AddFormBlock('Google+');
		$this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Publicar en Google+","FieldName":"Form_PublishGPlus","Value":"' . $this->Data['PublishGPlus'] . '"}');
		$this->AddFormContent($in_block,'{"Type":"url","Text":"URL de Página a compartir","FieldName":"Form_GPlusUrl","Value":"' . addslashes($this->Data['GPlusUrl']) . '"}');
		//$this->AddFormContent($in_block,'{"Type":"text","Text":"Título de la publicación","FieldName":"Form_GPlusTitle","Value":"' . addslashes($this->Data['GPlusTitle']) . '"}'); //Disabled
		$this->AddFormContent($in_block,'{"Type":"textarea","Text":"Contenido de la publicación","FieldName":"Form_GPlusDescription","Value":"' . addslashes($this->Data['GPlusDescription']) . '"}');
		$this->AddFormContent($in_block,'{"Type":"url","Text":"URL de imágen","FieldName":"Form_GPlusImage","Value":"' . addslashes($this->Data['GPlusImage']) . '"}');
		$in_block=$this->AddFormBlock('Programación');
		if ($this->Data['Published']==0) { $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Publicar inmediatamente","FieldName":"System_PublishNow","Value":"' . $this->Data['System_PublishNow'] . '"}'); }
		$this->AddFormContent($in_block,'{"Type":"date","Text":"Fecha de publicación","FieldName":"Form_DatePublish","Value":"' . addslashes($this->Data['DatePublish']) . '"}');
		$this->AddFormContent($in_block,'{"Type":"time","Text":"Hora de publicación","FieldName":"Form_HourPublish","Value":"' . addslashes($this->Data['HourPublish']) . '"}');
		$salida=array();
		$intervalos=$this->conf->Export('AutoSocialMediaMaxIntervals');
		for($x=1;$x<=$intervalos;$x++) { 
			$tiempo=AddMinutes($this->conf->Export('AutoSocialMediaHourFirstPublish'), $this->conf->Export('AutoSocialMediaIntervalMinutes')*($x-1));
			$salida[$x]="Bloque " . $x . " (" . substr($tiempo,0,5) . ")";
		}
		$opciones=json_encode($salida,true);
		$this->AddFormContent($in_block,'{"Type":"combo-json","Text":"Bloque de publicación","FieldName":"Form_Orden","Value":"' . $this->Data['Orden'] . '", "JsonValues": ' . $opciones . ', "NullValue":""}');
		if ($this->MultiBusiness) { $this->AddFormContent($in_block,'{"Type":"combo","Text":"Empresa","FieldName":"Form_IDBusiness","Value":"' . $this->Data['IDBusiness'] . '", "ListTable": "business", "ListValue": "ID", "ListOption": "Name", "ListOrder":"Name", "NullValue": "0"}'); }
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->TemplatePostScript=$this->class . '/post';
	}

	/*********************/
	/*	ACCION DEL ADMIN */
	/*********************/		
	
	function RunAction() {
		if ($this->action=="list") { 
			$this->ListAdmItems(); 
			$this->PrepareTableList();
			$this->LoadTemplate($this->module . '_list.tpl.php');
		}
		if ($this->action=="new") { $this->NewAdmItem(); }
		if ($this->action=="edit") { $this->EditAdmItem(); }
		if ($this->action=="post") { 
			$this->PostAdmItem(); 
		}
		if ($this->action=="delete") { echo intval($this->DeleteItem()); }
		if ($this->action=="deleteprocessed") { 
			$this->DeleteProcessed();
			header("Location: " . siteprotocol . sitedomain . sitePanelFolder. "/" . $this->class . "/list"); 
		}	
		if ($this->action=="cancel") { 
			$this->DeletePending();
			header("Location: " . siteprotocol . sitedomain . sitePanelFolder. "/" . $this->class . "/list"); 
		}
		if ($this->action=="cron") { 
			$total=$this->CronProcess();
			echo "Se han procesado " . $total . " publicaciones en las redes sociales.";
		}
		if ($this->action=="start") { $this->LoadDataForPublish(); }
		if ($this->action=="test-twitter") {
			echo "Test Twitter API<br>";
			ini_set("display_errors", "on");
			Tweet("Test API succesfully. Please delete this message.");	
		}
		
		if ($this->action=="test-gplus") {
			echo "Test Google+ API<br>";
			ini_set("display_errors", "on");
			$loginerror=doConnectToGooglePlus2(siteGoogleAccount, siteGooglePassword);
			if (!$loginerror) {
				$lnk = doGetGoogleUrlInfo2(siteprotocol . sitedomain);
				doPostToGooglePlus2('Prueba de publicación', $lnk, siteGoogleAPIProfile);
			} else {
				echo "Error de inicio de sesión";
			}
		}		

	}
	
	
	function __destruct(){

	}

}
?>