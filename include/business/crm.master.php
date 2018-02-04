<?php
require_once(sitepath . "include/core/core.class.php");
require_once(sitepath . "include/business/business.config.php");
require_once(sitepath . "include/extras/attachments.class.php");

class MasterBusinessCRM extends Core{
	var $title = 'CRM';
	var $class = 'crm';
	var $module = 'business';
	var $table = 'business_mailing';
	var $tablefather = 'business';
	var $typemodule='business';
	var $InstallAdminMenu=array(array('Block' => 'business', 'Icon' => 'fa-building'));
	var $version=false;

	function __construct($values) {
		parent::__construct($values);  
		$this->conf = new ConfigBusiness();
		$this->BreadCrumb['CRM'] = $this->module . "--" . $this->class;
	}
	
	function fetchUrl($url){
		 $ch = curl_init();
		 curl_setopt($ch, CURLOPT_URL, $url);
		 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		 curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	 
		 $retData = curl_exec($ch);
		 curl_close($ch); 
	 
		 return $retData;
	}	
		
	function ListAdmItems(){
		$this->GetItems("",false,"ID DESC",$this->search);
		$this->PrepareTableList();
		if ($this->paged) {
			$this->LoadTemplate('list_paged.tpl.php');
		} else {
			$this->LoadTemplate('list.tpl.php');
		}
	}
	
	function NewAdmItem() {
		$values['Body']=fetchUrl(siteprotocol . sitedomain . "/basemails/crm.html");
		$values['CheckIncludeBusinessTypes']=1;
		$values['CheckGeography']=1;
		$values['IncludeWithPhone']=3;
		$values['IncludeWithFacebook']=3;
		$values['IncludeWithTwitter']=3;
		$values['IncludeWithGoogleP']=3;
		$values['IncludeActive']=3;
		$values['IncludeDrafted']=3;
		$this->NewItem($values);
		$this->TotalLanguages=0;	
		$this->PrepareForm();
		$this->LoadTemplate('edit.tpl.php');
	}
	
	function NewItemFromUrl() {
		$values['FromURL']=siteprotocol . sitedomain . "basemails/gen_newsletter.php?from=" . date('Y-m-d');
		$values['CheckIncludeBusinessTypes']=1;
		$values['CheckGeography']=1;
		$values['IncludeWithPhone']=3;
		$values['IncludeWithFacebook']=3;
		$values['IncludeWithTwitter']=3;
		$values['IncludeWithGoogleP']=3;
		$values['IncludeActive']=3;
		$values['IncludeDrafted']=3;
		$this->NewItem($values);

		$this->TotalLanguages=0;	
		$this->PrepareFormLoadURL();
		$this->LoadTemplate('edit.tpl.php');
	}

	function EditAdmItem($id="") {
		$valid=$this->EditItem($id);
		if (! $valid) { 
			$return=siteprotocol . sitedomain . sitePanelFolder ."/home";
			if(isset($_SERVER['HTTP_REFERER'])){ $return=$_SERVER['HTTP_REFERER'];}
			$return.="/error/" . urlencode(base64_encode("El elemento especificado no existe"));
			header("Location: " . $return); exit;
		}
		if ($this->Data['IncludeBusinessTypes']=="") {
			$this->Data['CheckIncludeBusinessTypes']=1;
		} else {
			$this->Data['CheckIncludeBusinessTypes']=0;
		}
		if (($this->Data['IncludeInIDState']==0) and ($this->Data['IncludeInIDCity']==0) and ($this->Data['IncludeInIDZone']==0)) { 
			$this->Data['CheckGeography']=1;
		} else {
			$this->Data['CheckGeography']=0;
		}
		$this->PrepareLangMenu(true);
		$this->PrepareForm();
		$this->LoadTemplate('edit.tpl.php');
	}

	function PostAdmItem($redirect=true) {
		if ($_POST['Temp_AllAttributes']) { $_POST['Form_IncludeBusinessTypes']=""; }
		if ($_POST['Temp_AllGeography']) { 
			$_POST['Form_IncludeInIDState']="0"; 
			$_POST['Form_IncludeInIDCity']="0"; 
			$_POST['Form_IncludeInIDZone']="0"; 
			$_POST['Form_IncludeInState']=""; 
			$_POST['Form_IncludeInCity']=""; 
			$_POST['Form_IncludeInZone']="";
		}
		if ($_POST['System_Action']=='new') {
			$_POST['Form_DatePublish']=date('Y-m-d');
			$_POST['Form_Sended']=0;
		}
		$this->NotPurgeHTML=true;
		$this->PostItem(siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "--" . $this->class);
	}

	function AfterPostItem($ActualID=-1) {
		$this->id=$ActualID;
		$this->GetDataFilter();
		$this->DeleteSystemVariable('CRMSendSubscriber_' . $ActualID);
		$this->DeleteSystemVariable('CRMSendMail');
		return true;
	}
		
	
	function PostItemFromURL() {
		$_POST['Form_DatePublish']=date('Y-m-d');
		$_POST['Form_Body']=fetchUrl($_POST['FromURL']);
		$this->PostAdmItem();
	}


	function GetAttributesJSON() {
		$salida=array();
		$this->EditItem();
		$valores=$this->Data['IncludeBusinessTypes'];
		$valores=explode(",",$valores);
		$sql="SELECT ID,Title FROM " . $this->tablefather . "_attributes_sets ORDER BY Orden, ID";
		$ItemsCount=parent::$db->GetDataListFromSQL($sql,$Items);
		if ($ItemsCount>0) {
			foreach($Items as $item) {
				unset($Dato);
				$Dato['id']=$item['ID'];
				$Dato['text']=$item['Title'];
				$Dato['state']['opened']=true;
				$Dato['state']['selected']=false;
				if (in_array($item['ID'], $valores)) {$Dato['state']['selected']=true; }
				$salida[]=$Dato;
			}
		}
		return json_encode($salida);
	}
	
	function GetDataFilter() {
		parent::$db->Qry("DELETE FROM " . $this->table . "_mails WHERE IDMailing=" . $this->id);
		parent::$db->LoadFormData($this,$this->id,1);
		$sql_select="SELECT * FROM " . $this->tablefather;
		$sql_where=" WHERE " . $this->tablefather . ".ID>0";
		if ($this->Data['IncludeBusinessTypes']!="") {
			$sql_select="SELECT business.*, business_lnk_attributes_sets.IDLink as IDLink FROM business_lnk_attributes_sets LEFT JOIN business ON business.ID=business_lnk_attributes_sets.IDFather";
			$sql_where.=" AND (IDTypeBusiness IN (" . $this->Data['IncludeBusinessTypes'] . ") OR IDLink IN (" . $this->Data['IncludeBusinessTypes'] . "))";
		}
		if ($this->Data['IncludeWithName']!="") { $sql_where.=" AND Name LIKE '%" . $this->Data['IncludeWithName'] . "%'"; }
		if ($this->Data['IncludeInIDState']!="0") { $sql_where.=" AND IDState=". $this->Data['IncludeInIDState']; }
		if ($this->Data['IncludeInState']!="") { $sql_where.=" AND State LIKE '%" . $this->Data['IncludeInState'] . "%'"; }
		if ($this->Data['IncludeInIDCity']!="0") { $sql_where.=" AND IDCity=". $this->Data['IncludeInIDCity']; }
		if ($this->Data['IncludeInCity']!="") { $sql_where.=" AND City LIKE '%" . $this->Data['IncludeInCity'] . "%'"; }
		if ($this->Data['IncludeInIDZone']!="0") { $sql_where.=" AND IDZone=". $this->Data['IncludeInIDZone']; }
		if ($this->Data['IncludeInZone']!="") { $sql_where.=" AND Zone LIKE '%" . $this->Data['IncludeInZone'] . "%'"; }
		if ($this->Data['IncludeWithPhone']=="0") { $sql_where.=" AND Phone=''"; }
		if ($this->Data['IncludeWithPhone']=="1") { $sql_where.=" AND Phone<>''"; }
		if ($this->Data['IncludeWithFacebook']=="0") { $sql_where.=" AND Facebook=''"; }
		if ($this->Data['IncludeWithFacebook']=="1") { $sql_where.=" AND Facebook<>''"; }
		if ($this->Data['IncludeWithTwitter']=="0") { $sql_where.=" AND Twitter=''"; }
		if ($this->Data['IncludeWithTwitter']=="1") { $sql_where.=" AND Twitter<>''"; }
		//if ($this->Data['IncludeWithGoogleP']=="0") { $sql_where.=" AND GooglePlus=''"; }
		//if ($this->Data['IncludeWithGoogleP']=="1") { $sql_where.=" AND GooglePlus<>''"; }
		if ($this->Data['IncludeActive']=="0") { $sql_where.=" AND Active=0"; }
		if ($this->Data['IncludeActive']=="1") { $sql_where.=" AND Active=1"; }
		if ($this->Data['IncludeDrafted']=="0") { $sql_where.=" AND Drafted=0"; }
		if ($this->Data['IncludeDrafted']=="1") { $sql_where.=" AND Drafted=1"; }	
		$sql=$sql_select . " " . $sql_where;
		$this->ItemsCount= parent::$db->GetDataListFromSQL($sql,$this->Items);
		$emails=array();
		if ($this->ItemsCount>0) {
			foreach($this->Items as $item) {
				if ($this->Data['IncludePublicEmail']==1) {
					$em=$item['Email'];
					if ((! in_array($em,$emails)) and ($em!="")) { 
						$emails[]=$em; 
						$Data['System_Action']="new";
						$Data['System_ID']=-1;
						$Data['Form_IDMailing']=$this->id;
						$Data['Form_Email']=$em;
						parent::$db->PostToDatabase($this->table . "_mails",$Data);
					}
				}
				if ($this->Data['IncludeBillingEmail']==1) {
					$em=$item['BillingEmail'];
					if ((! in_array($em,$emails)) and ($em!="")) { 
						$emails[]=$em;
						$Data['System_Action']="new";
						$Data['System_ID']=-1;
						$Data['Form_IDMailing']=$this->id;
						$Data['Form_Email']=$em;
						parent::$db->PostToDatabase($this->table . "_mails",$Data);
					}
				}
				if ($this->Data['IncludeAdminsEmails']==1) {
					$sql_paginas = "SELECT business_users.*,users.UserName as UserName,users.Email as Email,users_roles.RolName as RolName FROM business_users INNER JOIN users ON users.ID=business_users.IDUser LEFT JOIN users_roles ON users_roles.ID=business_users.Rol WHERE business_users.IDBusiness=" . $item['ID'] . " ORDER BY business_users.Rol,business_users.id";	
					unset($Recorrido);
					$total = parent::$db->GetDataListFromSQL($sql_paginas,$Recorrer);
					if ($total>0) {
						foreach ($Recorrer as $recorrido) {
							$em=$recorrido['Email'];
							if ((! in_array($em,$emails)) and ($em!="")) { 
								$emails[]=$em;
								$Data['System_Action']="new";
								$Data['System_ID']=-1;
								$Data['Form_IDMailing']=$this->id;
								$Data['Form_Email']=$em;
								parent::$db->PostToDatabase($this->table . "_mails",$Data);
							}
						}
					}
				}	
			}
		}
		unset($this->ItemsCount);
		unset($this->Items);
		$this->ItemsCount=parent::$db->GetDataListFromSQL("SELECT Email FROM " . $this->table . "_mails WHERE IDMailing=" . $this->id, $this->Items);
		return $this->ItemsCount;									
	}

	function Export() {
		$this->ItemsCount=parent::$db->GetDataListFromSQL("SELECT Email FROM " . $this->table . "_mails WHERE IDMailing=" . $this->id, $this->Items);
		parent::$db->ExportToExcel($this,array("Email"));
	}
	
	
	function ViewItem() {
		$sql = "SELECT * FROM " . $this->table . " WHERE ID= '" . $this->id . "' OR MD5(ID)='" . $this->id . "'";	
		$Correo=parent::$db->GetDataRecordFromSQL($sql);	
		$AttachsCount=self::$db->GetDataListFromSQL("SELECT * FROM " . $this->table . "_attachments WHERE IDFather=" . $Correo['ID'] . " ORDER BY Orden", $Attachs);
		$adjuntos="";
		if ($AttachsCount>0) {
			$adjuntos.="<ul>";
			foreach ($Attachs as $adjunto) {
				$adjuntos.="<li style='font-size:20px'><a href='" . siteprotocol . sitedomain . "public/files/" . $adjunto['File'] . "'>" . $adjunto['Description'] . "</a></li>";
			}
			$adjuntos.="</ul>";
		}		
		$view_mail=sitePasswordsMail;
		$view_link="javascript:alert('No disponible en la vista previa');";
		$remove_link="javascript:alert('No disponible en la vista previa');";
		//Cambiamos los metaetiquetas [ViewMail] y [RemoveFromList] de los campos cabecera y pie...
		$Correo['Body']=stripslashes($Correo['Body']);
		$Correo['Body']=str_replace('{{RecipentMail}}',$view_mail,$Correo['Body']);
		$Correo['Body']=str_replace('{{ViewMail}}',$view_link,$Correo['Body']);
		$Correo['Body']=str_replace('{{RemoveFromList}}',$remove_link,$Correo['Body']);
		$Correo['Body']=str_replace('{{Attachments}}',$adjuntos,$Correo['Body']);
		$Correo['Body']=str_replace('%7B%7BRecipentMail%7D%7D',$view_mail,$Correo['Body']);
		$Correo['Body']=str_replace('%7B%7BViewMail%7D%7D',$view_link,$Correo['Body']);
		$Correo['Body']=str_replace('%7B%7BRemoveFromList%7D%7D',$remove_link,$Correo['Body']);	
		$Correo['Body']=str_replace('%7B%7BAttachments%7D%7D',$adjuntos,$Correo['Body']);			
		//Devolvermos el HTML Completo...
		$devolver=$Correo['Body'];
		return $devolver;
	}
	

	function ViewSend() {
		$this->Data=$this->GetDataRecord('business_mailing',$this->idparent);
		$this->BreadCrumb[$this->Data['Subject']]=$this->module . "--" . $this->class;
		$this->BreadCrumb['Enviar mensajes']='';

		$this->ItemsCount=parent::$db->GetDataListFromSQL("SELECT * FROM " . $this->table . "_mails WHERE IDMailing=" . $this->idparent . " ORDER BY ID", $this->Items);
		//Cargamos la variable del sistema que indica por qué elemento vamos enviando...
		$this->GetSystemVariables();
		if (! isset($this->SystemVariables['CRMSendMail'])) {
			$this->EditSystemVariable('CRMSendMail',$this->idparent);
			$this->SystemVariables['CRMSendMail']=$this->idparent;
		}
		if (! isset($this->SystemVariables['CRMSendSubscriber_' . $this->idparent])) {
			$this->EditSystemVariable('CRMSendSubscriber_' . $this->idparent,0);
			$this->SystemVariables['CRMSendSubscriber_' . $this->idparent]=0;
		}	
		$this->Remaining=$this->ItemsCount;
		if ($this->ItemsCount>0) {
			if ($this->SystemVariables['CRMSendSubscriber_' . $this->idparent]==0) { 
				$this->EditSystemVariable('CRMSendSubscriber_' . $this->idparent,$this->Items[0]['ID']);
				$this->SystemVariables['CRMSendSubscriber_' . $this->idparent]=$this->Items[0]['ID'];
			} else {
				$from=0;
				$from=$this->SystemVariables['CRMSendSubscriber_' . $this->idparent];
				$remaining=parent::$db->GetDataFieldFromSQL("SELECT COUNT(ID) AS Total FROM " . $this->table . "_mails WHERE IDMailing= " . $this->idparent . " AND ID>=" . $from,"Total");
				$this->Remaining=$remaining;

			}
		}
		$this->StartSendID=$this->SystemVariables['CRMSendSubscriber_' . $this->idparent];
		$this->Mailing=$this->Data;
		$this->TemplateLoadScript="crm_send.js";		
		$this->LoadTemplate('majordomo_send.tpl.php');	
	}
	
	function SendMail($debug=false) {
		$this->GetSystemVariables();
		$Lista=self::$db->GetDataRecord($this->table,$this->idparent);
		if ($Lista===false) { 
			$output['Success']=0;
			$output['Remaining']=0;
			$output['Message']="ERROR: Incomplete request URI";
			return json_encode($output);
		}
		$AttachsCount=self::$db->GetDataListFromSQL("SELECT * FROM " . $this->table . "_attachments WHERE IDFather=" . $this->idparent . " ORDER BY Orden", $Attachs);
		$adjuntos="";
		if ($AttachsCount>0) {
			$adjuntos.="<ul>";
			foreach ($Attachs as $adjunto) {
				$adjuntos.="<li style='font-size:20px'><a href='" . siteprotocol . sitedomain . "public/files/" . $adjunto['File'] . "'>" . $adjunto['Description'] . "</a></li>";
			}
			$adjuntos.="</ul>";
		}	
		$IDSubscriptor=$this->SystemVariables['CRMSendSubscriber_' . $this->idparent];
		$Subscriptor=self::$db->GetDataRecord($this->table . "_mails",$IDSubscriptor);
		$view_mail=sitePasswordsMail;
		$view_link=siteprotocol . sitedomain . "/lib/crm/?id=" . md5($this->idparent) . "";
		$this->Data['Body']=stripslashes($Lista['Body']);		
		$this->Data['Body']=str_replace('{{RecipentMail}}',$view_mail,$this->Data['Body']);
		$this->Data['Body']=str_replace('{{ViewMail}}',$view_link,$this->Data['Body']);
		$this->Data['Body']=str_replace('{{Attachments}}',$adjuntos,$this->Data['Body']);	
		$this->Data['Body']=str_replace('%7B%7BRecipentMail%7D%7D',$view_mail,$this->Data['Body']);
		$this->Data['Body']=str_replace('%7B%7BViewMail%7D%7D',$view_link,$this->Data['Body']);		
		$this->Data['Body']=str_replace('%7B%7BAttachments%7D%7D',$adjuntos,$this->Data['Body']);	
		$cuerpo=$this->Data['Body'];
		$output['Success']=0;	
		$output['Remaining']=0;
		$output['ID']=$IDSubscriptor;
		$output['Email']=$Subscriptor['Email'];
		$output['Message']="ERROR: Bad Email";	
		if (! $debug) {		
			//Envia realmente
			if (filter_var($view_mail, FILTER_VALIDATE_EMAIL)) {
				if (SendMail(siteTitle, $Subscriptor['Email'], $Lista['Subject'], $cuerpo, siteNewsletterMail, 1,false)) {
					$output['Success']=1; 
				    $output['Message']="SEND OK";
				} else { 
					$output['Success']=0; 
				    $output['Message']="SEND ERROR";
				}
			} else {
				$output['Success']=0; 
			    $output['Message']="BAD EMAIL";
				return json_encode($output);
			}
		} else {
			//Realiza un volcado a archivo
			$fh = fopen("emailbug.txt", "w");
			fwrite( $fh, $cuerpo);
			fclose( $fh );  
			$output['Success']=1; 
		    $output['Message']="TRACE OK";
		}
		//Buscamos el suscriptor siguiente...
		$Next=parent::$db->GetDataRecordFromSQL("SELECT * FROM " . $this->table . "_mails WHERE IDMailing= " . $this->idparent . " AND ID>" . $IDSubscriptor . " ORDER BY ID LIMIT 1");
		if ($Next!==false) {
			$this->EditSystemVariable('CRMSendSubscriber_' . $this->idparent,$Next['ID']);
		}
		//Recalculamos los pendientes...
		if ($output['Success']!=0) {
			$sql="SELECT COUNT(ID) AS Total FROM " . $this->table . "_mails WHERE IDMailing= " . $this->idparent . " AND ID>" . $IDSubscriptor;
			$output['Remaining']=parent::$db->GetDataFieldFromSQL($sql,"Total");
		}
		//Borramos el estado de las variables de sistema...
		if ($output['Remaining']==0) {
			$this->DeleteSystemVariable('CRMSendSubscriber_' . $this->idparent);
			$this->DeleteSystemVariable('CRMSendMail');
			parent::$db->Qry("UPDATE " . $this->table . " SET DateSend=NOW(), Sended=1");
		}
		return json_encode($output);
	}

	function UpdateMailRelay($autoSend=false) {
		//Devuelve true si procesa el envio, y false si no lo forzó por redirección.
		$curl = curl_init('http://' . $this->conf->Export("MailRelayDomain") . '/ccm/admin/api/version/2/&type=json');
		$postData = array();
		$postData['function']="getSubscribers";
		$postData['apiKey']=$this->conf->Export("MailRelayApiKey");
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postData));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$json = curl_exec($curl);
		$json=json_decode($json,true);
		if ($json['status']==1) {
			$MailRelay=$json['data'];
		}
		$MailRelayCount=count($MailRelay);
		if ($MailRelayCount>0) {
			$adapterMR=array();
			foreach($MailRelay as $mr) {
				array_push($adapterMR, $mr['email']);
			}
			$MailRelay=$adapterMR;
		}
		$sql = "SELECT Email FROM " . $this->table . "_mails WHERE IDMailing= " . $this->idparent;	
		$DBCount=parent::$db->GetDataListFromSQL($sql,$DB);
		if ($DBCount>0) {
			$tmp=array();
			foreach($DB as $s) {
				array_push($tmp, $s['Email']);
			}
			$DB=$tmp;
		}
		//Marcamos los suscriptores a añadir
		$toAdd=array();
		if($DBCount>0) {
			foreach($DB as $s) {
				$pos=array_search($s, $MailRelay);
				if ($pos===false) {
					array_push($toAdd, $s);
				}
			}
		}
		//Marcamos los suscriptores a eliminar de MailRelay
		$toDelete=array();
		if($MailRelayCount>0) {
			foreach($MailRelay as $mr) {
				$pos=array_search($mr, $DB);
				if ($pos===false) {
					array_push($toDelete, $mr);
				}
			}
		}
		//Procedemos con las bajas...
		if (count($toDelete)>0) {
			foreach($toDelete as $item) {
				$postData = array(
				    'function' => 'deleteSubscriber',
				    'apiKey' => $this->conf->Export("MailRelayApiKey"),
				    'email' => $item);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postData));
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$json=curl_exec($curl);
			}
		}
		//Procedemos con las altas...
		if (count($toAdd)>0) {
		    $postData = array(
				    'function' => 'import',
				    'apiKey' => $this->conf->Export("MailRelayApiKey"),
				    'fileContent' => base64_encode(implode("\r\n", $toAdd)),
				    'headers' => false,
				    'groups' => array($this->conf->Export("MailRelayGroupID")));
		    if ($autoSend) { $postData['notificationUrl']= siteprotocol . sitedomain . "lib/crm/mailrelay.php?id=" . $this->id . "&idparent=" . $this->idparent . "&hash=" . base64_encode($this->id . siteTitle . $this->idparent); }
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postData));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$json=curl_exec($curl);
			return true;
		}
		return false;
	}

	function PrepareMailRelay() {
		$redirigido= $this->UpdateMailRelay(true);
		if ($redirigido) {
			header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "--" . $this->class . "/list/idparent/" . $this->idparent . "/text/" . urlencode(base64_encode(_("Se ha programado el envío de mediante MailRelay"))));
			exit;
		} else {
			$this->SendMailRelay(true);
		}
	}

	function SendMailRelay($redirect=true) {
		$this->Boletin=self::$db->GetDataRecord($this->table,$this->idparent);
		$SubscriptorsCount=self::$db->GetDataListFromSQL("SELECT * FROM " . $this->table . "_mails WHERE IDMailing = " . $this->idparent,$Subscriptors);
		$emails=array();
		if ($SubscriptorsCount>0) {
			foreach ($Subscriptors as $subcriptor) {
				$emails[]=array("email" => $subcriptor['Email']);
			}
		}
		$view_mail=sitePasswordsMail;
		$view_link=siteprotocol . sitedomain . "/lib/crm/?id=" . md5($this->idparent) . "";
		$this->Data['Body']=stripslashes($this->Boletin['Body']);		
		$this->Data['Body']=str_replace('{{RecipentMail}}',$view_mail,$this->Data['Body']);
		$this->Data['Body']=str_replace('{{ViewMail}}',$view_link,$this->Data['Body']);
		$this->Data['Body']=str_replace('{{RemoveFromList}}',$remove_link,$this->Data['Body']);
		$this->Data['Body']=str_replace('%7B%7BRecipentMail%7D%7D',$view_mail,$this->Data['Body']);
		$this->Data['Body']=str_replace('%7B%7BViewMail%7D%7D',$view_link,$this->Data['Body']);
		$this->Data['Body']=str_replace('%7B%7BRemoveFromList%7D%7D',$remove_link,$this->Data['Body']);			
		$this->Data['Body']=$this->Data['Body'];
		$output = array();
		$output['function']="addCampaign";
		$output['apiKey']=$this->conf->Export("MailRelayApiKey");
		$output['subject']=$this->Boletin['Subject'];
		$output['mailboxFromId']=$this->conf->Export("MailRelayFromID");
		$output['mailboxReplyId']=$this->conf->Export("MailRelayReplyID");
		$output['mailboxReportId']=$this->conf->Export("MailRelayReportID");
		$output['emailReport']= true;
		$output['groups']=array($this->conf->Export("MailRelayGroupID"));
		$output['text']="Para ver correctamente este email visita " . $view_link;
		$output['html']=$this->Data['Body'];
		$output['packageId']=$this->conf->Export("MailRelayPackageID");
		$output['campaignFolderId']=$this->conf->Export("MailRelayCampaingFolderID");
		$curl = curl_init('http://' . $this->conf->Export("MailRelayDomain") . '/ccm/admin/api/version/2/&type=json');
		$post = http_build_query($output);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$resultado = curl_exec($curl);
		if ($resultado === false) {
		    $message = _("Petición fallida") . ": " . curl_error($curl);
		} else {
			//Enviamos
			$resultado=json_decode($resultado,true);
			if (isset($resultado['data'])) {
				$output = array();
				$output['function']="sendCampaign";
				$output['apiKey']=$this->conf->Export("MailRelayApiKey");
				$output['id']=$resultado['data'];
				$curl = curl_init('http://' . $this->conf->Export("MailRelayDomain") . '/ccm/admin/api/version/2/&type=json');
				$post = http_build_query($output);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				$resultado = curl_exec($curl);
				print_r($resultado);
				$message=_("Se ha creado y enviado la campaña") . " #" . $output['id'];
			} else {
				$message=_("Error al crear la campaña en MailRelay");
			}
		}
		//if ($redirect) { header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "--" . $this->class . "/list/idparent/" . $this->idparent . "/text/" . urlencode(base64_encode($message))); exit; }
		return $message;
	}


	function PrepareTableList() {
		$this->AddMainMenu('Crear',$this->module . "--" . $this->class . '/new/id/' . $this->id);
		$this->AddMainMenu('Crear desde plantilla',$this->module . "--" . $this->class . '/new_from_url/id/' . $this->id);
		$this->AddTableContent('','data','','==(99999999999999-{{ID}})',$this->module . "--" . $this->class . '/edit/id/{{ID}}');
		$this->AddTableContent('Asunto','data','{{Subject}}','',$this->module . "--" . $this->class . '/edit/id/{{ID}}');
		$this->AddTableContent('Fecha','data','{{DatePublish}}','{{DatePublish}}');
		$this->AddTableContent('Enviado','data','{{DateSend}}','{{DateSend]]');
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block,'Exportar a XLS',$this->module . "--" . $this->class . '/export/id/{{ID}}');
		if ($this->conf->Check("UseBasicSMTP")) { $this->AddTableOperations($in_block,'Enviar',$this->module . "--" . $this->class . '/send/idparent/{{ID}}'); }
		if ($this->conf->Check("UseMailRelay")) { $this->AddTableOperations($in_block,'Enviar MailRelay',$this->module . "--" . $this->class . '/mailrelay/idparent/{{ID}}'); }
		$this->AddTableOperations($in_block,'Ver',$this->module . "--" . $this->class . '/view/id/{{ID}}');
		$this->AddTableOperations($in_block);
		$this->AddTableOperations($in_block,'Editar',$this->module . "--" . $this->class . '/edit/id/{{ID}}');
		$this->AddTableOperations($in_block,'Eliminar',$this->module . "--" . $this->class . '/delete/id/{{ID}}');
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('General');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Asunto","FieldName":"Form_Subject","Value":"' . addcslashes($this->Data['Subject'],'\\"') . '","Required":true}');
		$this->AddFormContent($in_block,'{"Type":"html","Text":"Cuerpo del correo","FieldName":"Form_Body","Value":"' . addcslashes($this->Data['Body'],'\\"') . '"}');
		if ($this->Check('CRMUseAttachments')) {
			$in_block=$this->AddFormBlock('Archivos');
			$this->XtraAttachments->PutTemplate($this,$in_block);
		}
		$in_block=$this->AddFormBlock('Filtros');
		$this->AddFormContent($in_block,'{"Type":"combo-json","Text":"Incluir todas las actividades","FieldName":"Temp_AllAttributes","Value":"' . addcslashes($this->Data['CheckIncludeBusinessTypes'],'\\"') . '","JsonValues": {"0":"No","1":"Si"}}');
		$this->AddFormContent($in_block,'{"Type":"jstree","Text":"Actividades","FieldName":"Form_IncludeBusinessTypes","Value":"' . $this->Data['IncludeBusinessTypes'] . '", "JsonURL": "' . $this->module . '--' . $this->class . '/getattributes/id/' . $this->Data['ID'] . '"}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Que en Nombre Comercial contenga (en blanco incluye todos)","FieldName":"Form_IncludeWithName","Value":"' . $this->Data['IncludeWithName'] . '"}');
		if (($this->Check('BusinessUseState')) or ($this->Check('BusinessUseCity')) or ($this->Check('BusinessUseZone'))) {
			$this->AddFormContent($in_block,'{"Type":"combo-json","Text":"Incluir todas las localidades","FieldName":"Temp_AllGeography","Value":"' . addcslashes($this->Data['CheckGeography'],'\\"') . '","JsonValues": {"0":"No","1":"Si"}}');
			if($this->Check('BusinessUseStandarizedLocationData')){ 
				if($this->Check('BusinessUseState')){ 
					$this->AddFormContent($in_block,'{"Type":"combo","Text":"Provincia","FieldName":"Form_IncludeInIDState","Value":"' . $this->Data['IncludeInIDState'] . '", "ListTable": "aux_states", "ListValue": "ID", "ListOption": "Name", "ListOrder":"Name", "NullValue": "0"}');
					$this->AddFormHiddenContent("Form_IncludeInState",$this->Data['IncludeInState']);
				}
				if($this->Check('BusinessUseCity')){ 
					$this->AddFormContent($in_block,'{"Type":"combo","Text":"Localidad","FieldName":"Form_IncludeInIDCity","Value":"' . $this->Data['IncludeInIDCity'] . '", "ListTable": "aux_cities", "ListValue": "ID", "ListOption": "Name", "ListOrder":"Name", "NullValue": "0"}');
					$this->AddFormHiddenContent("Form_IncludeInCity",$this->Data['IncludeInCity']);
				}
				if($this->Check('BusinessUseZone')){ 
					$this->AddFormContent($in_block,'{"Type":"combo","Text":"Zona","FieldName":"Form_IncludeInIDZone","Value":"' . $this->Data['IncludeInIDZone'] . '", "ListTable": "aux_zones", "ListValue": "ID", "ListOption": "Name", "ListOrder":"Name", "NullValue": "0"}');
					$this->AddFormHiddenContent("Form_IncludeInZone",$this->Data['IncludeInZone']);
				}
			} else {
				if($this->Check('BusinessUseState')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Provincia","FieldName":"Form_IncludeInState","Value":"' . addcslashes($this->Data['IncludeInState'],'\\"') . '"}'); }
				if($this->Check('BusinessUseCity')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Localidad","FieldName":"Form_IncludeInCity","Value":"' . addcslashes($this->Data['IncludeInCity'],'\\"') . '"}'); }
				if($this->Check('BusinessUseZone')){ $this->AddFormContent($in_block,'{"Type":"text","Text":"Zona","FieldName":"Form_IncludeInZone","Value":"' . addcslashes($this->Data['IncludeInZone'],'\\"') . '"}'); }
			}
		}
		$this->AddFormContent($in_block,'{"Type":"combo-json","Text":"Teléfono","FieldName":"Form_IncludeWithPhone","Value":"' . $this->Data['IncludeWithPhone'] . '","JsonValues": {"":"Incluir todos", "0":"No tiene","1":"Si tiene"}}');
		$this->AddFormContent($in_block,'{"Type":"combo-json","Text":"Facebook","FieldName":"Form_IncludeWithFacebook","Value":"' . $this->Data['IncludeWithFacebook'] . '","JsonValues": {"":"Incluir todos", "0":"No tiene","1":"Si tiene"}}');
		$this->AddFormContent($in_block,'{"Type":"combo-json","Text":"Twitter","FieldName":"Form_IncludeWithTwitter","Value":"' . $this->Data['IncludeWithTwitter'] . '","JsonValues": {"":"Incluir todos", "0":"No tiene","1":"Si tiene"}}');
		$this->AddFormContent($in_block,'{"Type":"combo-json","Text":"Google+","FieldName":"Form_IncludeWithGoogleP","Value":"' . $this->Data['IncludeWithGoogleP'] . '","JsonValues": {"":"Incluir todos", "0":"No tiene","1":"Si tiene"}}');
		$this->AddFormContent($in_block,'{"Type":"combo-json","Text":"Activo","FieldName":"Form_IncludeActive","Value":"' . $this->Data['IncludeActive'] . '","JsonValues": {"":"Incluir todos", "0":"Sólo las activas","1":"Sólo las inactivas"}}');
		$this->AddFormContent($in_block,'{"Type":"combo-json","Text":"Destacado","FieldName":"Form_IncludeDrafted","Value":"' . $this->Data['IncludeDrafted'] . '","JsonValues": {"":"Incluir todos", "0":"Sólo las activas","1":"Sólo las inactivas"}}');
		$in_block=$this->AddFormBlock('Enviar a');
		$this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Enviar a email público","FieldName":"Form_IncludePublicEmail","Value":"' . $this->Data['IncludePublicEmail'] . '"}');
		$this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Enviar a email privado","FieldName":"Form_IncludeBillingEmail","Value":"' . $this->Data['IncludeBillingEmail'] . '"}');
		$this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Enviar a emails de administradores","FieldName":"Form_IncludeAdminsEmails","Value":"' . $this->Data['IncludeAdminsEmails'] . '"}');
		$this->AddFormHiddenContent("System_IDState",$this->Data['IncludeInIDState']);
		$this->AddFormHiddenContent("System_IDCity",$this->Data['IncludeInIDCity']);
		$this->AddFormHiddenContent("System_IDZone",$this->Data['IncludeInIDZone']);
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->TemplateLoadScript="crm.js";
		$this->TemplatePostScript=$this->module . "--" . $this->class . "/post";
	}

	function PrepareFormLoadURL() {
		$in_block=$this->AddFormBlock('General');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Asunto","FieldName":"Form_Subject","Value":"' . addcslashes($this->Data['Subject'],'\\"') . '","Required": true}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"URL de plantilla","FieldName":"FromURL","Value":"' . addcslashes($this->Data['FromURL'],'\\"') . '","Required": true}');
		$this->AddFormContent($in_block,'{"Type":"iframe","Text":"Contenido de la plantilla","FieldName":"contenido","Value":""}');
		$this->TemplatePostScript=$this->module . "--" . $this->class . "/post_url";
		$this->TemplateLoadScript="majordomo_from_url.js";
	}

	function RunAction() {
		if ($this->action=="new_from_url") { $this->NewItemFromUrl(); exit; }
		if ($this->action=="post_url") { $this->PostItemFromURL(); exit; }
		if ($this->action=="export") { $this->Export(); exit; }
		if ($this->action=="view") { echo $this->ViewItem(); exit; }
		if ($this->action=="send") { $this->ViewSend(); exit; }
		if ($this->action=="sendmail") { echo $this->SendMail(false); exit; }
		if ($this->action=="mailrelay") { echo $this->PrepareMailRelay(); }
		if ($this->action=="getattributes") { echo $this->GetAttributesJSON(); exit; }
		parent::RunAction();
	}
}
?>