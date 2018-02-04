<?php
require_once(sitepath . "templates/" . $this->template . "/adapter.php");
require_once(sitepath . "include/business/business.class.php");
require_once(sitepath . "include/tags/tags.class.php");
require_once(sitepath . "include/extras/comments.class.php");
//Comprobamos si la función corresponde a comentarios...
$Is_Comments_Function=strpos($this->params['action'],'comments_');
if ($Is_Comments_Function!==false) { 
	$this->XtraComments= new ExtraComments('calendar','','IDFather',0);
	$this->XtraComments->Run(substr($this->params['action'],$Is_Comments_Function+strlen('comments_'),strlen($this->params['action'])-$Is_Comments_Function));
	exit;
}
$this->business=new Business($this->params);
$this->business->type=0;
$this->business->state=siteStateDefault;
$this->business->city=0;
$this->business->attr=0;
$this->business->val="";
$this->business->tag="";
if (isset($this->params['type'])) { 
	$p=CheckParamValid($this->params['type'],'integer');
	if ($p!==false) { $this->business->type=$p; }
}
if (isset($this->params['city'])) { 
	$p=CheckParamValid($this->params['city'],'integer');
	if ($p!==false) { $this->business->city=$p; }
}
if (isset($this->params['attr'])) { $this->business->attr=$this->params['attr']; }
if (isset($this->params['val'])) { $this->business->val=$this->params['val']; }
if (isset($this->params['tag'])) { $this->business->action="tags"; $this->business->tag=$this->params['tag']; }

if ($this->params['action']=='all') {
	$this->params['id']=0;
	$this->business->id=0;
	$this->params['action']="list";
}

if ($this->params['action']=='list') {
	if (! isset($this->params['offset'])) { $this->business->offset=12; }
	$paginado=true;
	if (isset($this->params['map'])) { $paginado=false; }
	$this->tags=new Tags($_GET);
	$this->tags->GetTagCloud();
	$this->ComboCities=self::$db->PopulateComboFromSQL("SELECT * FROM aux_cities WHERE IDFather=" . $this->business->state . " ORDER BY Name","ID","Name",$this->business->city);
	$this->ComboTipos=self::$db->PopulateComboFromSQL("SELECT * FROM business_attributes_options WHERE IDAttribute=" . $this->business->attr . " ORDER BY Title","Value","Title",$this->business->val);
	$perfil=$this->business->id;
	$this->Icon="http://maps.google.com/mapfiles/ms/icons/red-dot.png";
	if (is_file(sitepath . "templates/" . $this->template . "assets/maps/business-" . $perfil . ".png")) {
		$this->Icon=siteprotocol . sitedomain . "templates/" . $this->template . "img/maps/business-" . $perfil . ".png";
	}
	if ($this->business->id>0) {
		$this->TipoEmpresa=self::$db->GetDataRecord("business_attributes_sets",$perfil);
		$this->TipoEmpresa['Permalink']=$this->GetPermalink("business_attributes_sets",$this->TipoEmpresa['ID']);
	} else {
		$this->TipoEmpresa['ID']=0;
		$this->TipoEmpresa['Title']="Empresas en " . siteCityTextDefault;
		$this->TipoEmpresa['Permalink']=$this->business->conf->Export("PermalinkFolder");
	}
	$this->BasePermalink=$this->TipoEmpresa['Permalink'];
	$this->business->GetBusinessItems("active",$paginado,"",$perfil,0,$this->business->city,0,$this->business->attr,$this->business->val);	
	$this->HeadTitle=$this->business->title;
	$this->Permalink=$this->TipoEmpresa['Permalink'];	
	$concatenador="?";
	if ($this->business->city!=0) {
		$Ciudad=self::$db->GetDataRecord('aux_cities',$this->business->city);	
		if ($Ciudad!==false) {$this->Permalink.="?city=" . $this->business->city; $concatenador="&"; }
	}
	if ($this->TipoEmpresa['ID']==2) {		
		$Cocina=self::$db->GetDataRecordFromSQL("SELECT * FROM business_attributes_options WHERE IDAttribute=" . $this->business->attr . " AND Value='" . $this->business->val . "'");	
		if ($Cocina!==false) {
			if ($Cocina!==false) { $this->Permalink.=$concatenador . "val=" . $this->business->val . "&attr=" . $this->business->attr;$concatenador="&"; }
		}	
	}
	//Selección de plantilla y parcheo de enlace para intercambio de mapa y lista...
	$template="business_list.tpl.php";
	if (isset($this->params['map'])) { 
		$template="business_map.tpl.php";
		$this->LinkButtonChange=$this->Permalink . $concatenador . "list";
		$this->Permalink.=$concatenador . "map";
		//Generamos el json con los datos del mapa
		$json="";
		$center_lat=0;
		$center_lng=0;
		$count=0;
		if ($this->business->ItemsCount>0) {
			foreach($this->business->Items as $item) { 
				if ($item['Geolocation']!="") {
					unset($datos);
					ExpandGeo($item['Geolocation'],$datos);
					$center_lat=$center_lat+$datos['GeoLat'];
					$center_lng=$center_lng+$datos['GeoLng'];
					$count++;
					unset($element);
					$element['lat']=$datos['GeoLat'];
					$element['lng']=$datos['GeoLng'];
					$element['data']['permalink']=$item['Permalink'];
					$element['data']['name']=stripslashes($item['Name']);
					$element['data']['street']=stripslashes($item['Street']);
					$img="templates/" . $this->template . "/img/business.jpg"; 
            		if (is_file("public/thumbnails/" . $item['Image'])) { $img="public/thumbnails/" . $item['Image']; } 
					$element['data']['image']=$img;
					$icono='http://maps.gstatic.com/mapfiles/icon_green.png';
					if (is_file("templates/" . $this->template . "/img/maps/b-icon.png")) {
						$icono=siteprotocol . sitedomain . "templates/" . $this->template . "/img/maps/b-icon.png";
					}
					if ($item['IDTypeBusiness']>0) {
						if (is_file("templates/" . $this->template . "/img/maps/b-icon-" . $item['IDTypeBusiness'] . ".png")) {
							$icono=siteprotocol . sitedomain . "templates/" . $this->template . "/img/maps/b-icon-" . $item['IDTypeBusiness'] . ".png";
						}
					}
					$element['options']['icon']=$icono;
					if ($json!="") { $json.=",";}
					$json.=json_encode($element);
				}
			}
		}
		$this->GeoLat=0;
		$this->GeoLng=0;
		$this->GeoZoom=14;
		if ($count>0) {
			$this->GeoLat=$center_lat/$count;
			$this->GeoLng=$center_lng/$count;
		}
		$this->GeoMarkers="[" . $json . "]";
	} else {		
		$this->LinkButtonChange=$this->Permalink . $concatenador . "map";
	}
	$this->loadtemplatepublic($template);
}

if ($this->params['action']=='show') {
	$this->business->EditItem($this->params['id']);	
	$this->business->Data['ILikeThis']=$this->GetLikes($LikeType='+',$this->business->Data['Permalink']);
	$this->business->Data['NoLikeThis']=$this->GetLikes($LikeType='-',$this->business->Data['Permalink']);
	$this->XtraAttributes= new ExtraAttributes('business','','IDFather',$this->business->id);	
	$this->XtraAttributes->id=$this->business->Data['IDTypeBusiness'];
	$this->XtraAttributes->GetAllAttributes();
	$this->XtraComments= new ExtraComments('business','','IDFather',$this->params['id'],$this->business->conf->GetActualConfig());	
	$this->XtraComments->GetComments();
	if (($this->userLevel>=99) or ($this->IsUserOnBusiness($this->params['id'])) or ($this->IsUserOnBusiness($this->params['id']))) { $this->XtraComments->EnableAdmin=true; }
	ExpandGeo($this->business->Data['Geolocation'],$this->business->Data);
	$this->business->Data['GeoIcon']='http://maps.gstatic.com/mapfiles/icon_green.png';
	if (is_file("templates/" . $this->template . "/img/maps/b-icon.png")) {
		$this->business->Data['GeoIcon']=siteprotocol . sitedomain . "templates/" . $this->template . "/img/maps/b-icon.png";
	}
	if (is_file("templates/" . $this->template . "/img/maps/b-icon-" . $this->business->Data['IDTypeBusiness'] . ".png")) {
		$icono=siteprotocol . sitedomain . "templates/" . $this->template . "/img/maps/b-icon-" . $this->business->Data['IDTypeBusiness'] . ".png";
	}
	//Miga de Pan...
	$this->business->TipoEmpresa['Permalink']=$this->GetPermalink('business_attributes_sets',$this->business->Data['IDTypeBusiness']);
	$Set=self::$db->GetDataRecord('business_attributes_sets',$this->business->Data['IDTypeBusiness']);
	if ($Set!==false) {
		$this->PublicBreadCrumbs[$Set['Title']]=$this->business->TipoEmpresa['Permalink'];	
	}
	if ($this->business->Data['IDCity']>0) {
		$Ciudad=self::$db->GetDataRecord('aux_cities',$this->business->Data['IDCity']);	
		if ($Ciudad!==false) {
			$this->PublicBreadCrumbs[$Ciudad['Name']]=$this->business->TipoEmpresa['Permalink'] . "?city=" . $this->business->Data['IDCity'];
		}
	}
	//Cargamos los tags...
	$this->Data['Tags']=self::$db->GetLinkedTags($this->business->table,$this->params['id'],false);

	$this->loadtemplatepublic("business_show.tpl.php");	
}

if ($this->params['action']=='tags') {
	$this->ComboCities=self::$db->PopulateComboFromSQL("SELECT * FROM aux_cities WHERE IDFather=". 5 . " ORDER BY Name","ID","Name",$this->business->city);
	$this->ComboTags=self::$db->PopulateComboFromSQL("SELECT * FROM business_tags","Tag","Tag",$this->business->tag);
	$this->business->TipoEmpresa['Permalink']=$this->business->conf->Export("PermalinkFolder");
	$paginar=true;
	if (isset($_GET['map'])) { $paginar=false; }
	
	$this->business->GetBusinessFromTag("active",$paginar,$this->business->tag,'',0,0,$this->business->city);	
	$template="business_tags_list.tpl.php";
	if (isset($_GET['map'])) { $template="business_tags_map.tpl.php"; }
	$this->HeadTitle=($this->business->tag) . " | " . siteTitle;
	$this->PublicBreadCrumbs[$this->business->tag]=$this->business->conf->Export("PermalinkFolder") . "?tag=" . $this->business->tag;
	if (isset($_GET['map'])) { $this->PublicBreadCrumbs['Mapa']=""; }
	$this->loadtemplatepublic($template);
}

if ($this->params['action']=="newbusiness") {
	if (isset($_POST['Form_Name'])) {
		$_POST['Form_Active']=0;
		$_POST['Form_LastUpdated']=date("Y-m-d H:i:s");
		$this->business->PostItem(false);
		$email=siteMainMail;
		$remite=siteMainMail;
		if ($this->userID!=0) {
			$remite=$this->useremail;
		}
		if (isset($_POST['Email'])) { 
			$remite=$_POST['Email']; 
			if (filter_var($remite, FILTER_VALIDATE_EMAIL)===false) { return "-1";  }
		}
		$asunto="Solicitud de alta de local recibida desde " . siteTitle;	
		$cuerpo="<hr /><strong>" . $asunto . "</strong><br />";
		foreach($_POST as $id=>$texto) {
			if (! is_array($texto)) {
				$cuerpo.="<strong>" . str_replace("_"," ",$id) . ":</strong> " . stripslashes(($texto)) . "<br />";	
			}
		}
		//enviamos el correo...
		$realizarenvio=SendMail($remite, $email , $asunto, $cuerpo, $remite, 1);
		return $realizarenvio;
		exit;
	}
	$values['IDState']=siteStateDefault;
	$values['IDCity']=siteCityDefault;
	$values['State']=siteStateTextDefault;
	$values['City']=siteCityTextDefault;
	$this->business->NewItem($values);
	$this->HeadTitle=("Dar de alta un local");		
	$this->loadtemplatepublic('business_new.tpl.php');	
}
?>