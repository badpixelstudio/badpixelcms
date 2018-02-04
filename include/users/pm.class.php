<?php
require_once(sitepath . "include/core/core.class.php");
require_once(sitepath . "include/users/users.config.php");


class UserPM extends Core{
	var $title = 'Mensajería Interna';
	var $class = 'pm';
	var $module = 'users';
	var $table = 'users_messages';
	var $version=false;	
	var $Destinations = array();
	var $offset=20;
	
	function __construct($values) {
		parent::__construct($values); 
		$title=$this->GetModuleName($this->class);
		if ($title!=$this->class) { $this->title=$title; }
		$this->conf = new ConfigUsers($this->businessID); 
		$this->BreadCrumb[$this->title]=$this->module;
		$this->CleanObsoletes();
	}

	function CleanObsoletes() {
		$sql="DELETE FROM " . $this->table . " WHERE ReadMsg=1 AND DateSend<'" . AddDays(date("Y-m-d"),($this->conf->Export('UserDaysToAutoDeletePM')*-1)) . "'";
		parent::$db->Qry($sql);
		$sql="DELETE FROM " . $this->table . "_sent WHERE DateSend<'" . AddDays(date("Y-m-d"),($this->conf->Export('UserDaysToAutoDeletePM')*-1)) . "'";
		parent::$db->Qry($sql);
	}

	function GetDestinations() {
		$salida=array();
		$sql="SELECT ID, UserName FROM users ORDER BY UserName";
		$TotalUsuarios=parent::$db->GetDataListFromSQL($sql,$Usuarios);
		if ($TotalUsuarios>0) {
			foreach($Usuarios as $item) {
				unset($Datos);
				$Datos['value']="u-" . $item['ID'];
				$Datos['text']=$item['UserName'];
				$Datos['type']="user";
				$salida[]=$Datos;
			}
		}
		$sql="SELECT IDRol, RolName FROM users_roles ORDER BY RolName";
		$TotalRoles=parent::$db->GetDataListFromSQL($sql,$Roles);
		if ($TotalRoles>0) {
			foreach($Roles as $item) {
				unset($Datos);
				$Datos['value']="r-" . $item['IDRol'];
				$Datos['text']=$item['RolName'];
				$Datos['type']="rol";
				$salida[]=$Datos;
			}
		}
		//Add others destinations...
		// $sql="SELECT ID, Title FROM <<table>> ORDER BY Title";
		// if ($this->businessID!=0) {
		// 	$sql.=" AND IDBusiness=" . $this->businessID;
		// }
		// $TotalOthers=parent::$db->GetDataListFromSQL($sql,$Others);
		// if ($TotalOthers>0) {
		// 	foreach($Others as $item) {
		// 		unset($Datos);
		// 		$Datos['value']="g-" . $item['ID'];
		// 		$Datos['text']=$item['Title'];
		// 		$Datos['type']="other1";
		// 		$salida[]=$Datos;
		// 	}
		// }
		//Reordenamos el array
		if (count($salida)>0) {
			$this->Destinations=orderMultiDimensionalArray($salida,'text');
		}

	}
	
	function Inbox() {
		$sql_paginas = "SELECT " . $this->table . ".*, users.UserName FROM " . $this->table . " LEFT JOIN users ON " . $this->table . ".FromID=users.ID WHERE ToID=" . $this->userID;
		$sql_paginas.=" ORDER BY DateSend DESC";
		$this->ItemsCount = parent::$db->GetDataListPagedFromSQL($sql_paginas,$this->page,$this->offset,$this->Items);
		return $this->ItemsCount;
	}

	function Sentbox() {
		$sql_paginas = "SELECT * FROM " . $this->table . "_sent WHERE FromID=" . $this->userID;
		$sql_paginas.=" ORDER BY DateSend DESC";
		$this->ItemsCount = parent::$db->GetDataListPagedFromSQL($sql_paginas,$this->page,$this->offset,$this->Items);
		if ($this->ItemsCount>0) {
			foreach($this->Items as $iditem=>$item) {
				$to_ids=explode(",", $item['ToID']);
				$destinatarios="";
				$partes=explode("-", $to_ids[0]);
				if ($partes[0]=="u") { 
					//Es un usuario...
					$sql="SELECT UserName FROM users WHERE ID=" . $partes[1];
					$destinatarios.=parent::$db->GetDataFieldFromSQL($sql,"UserName");
				}
				if ($partes[0]=="r") {
					//Es un rol...
					$sql="SELECT RolName FROM users_roles WHERE IDRol=" . $partes[1];
					$destinatarios.=parent::$db->GetDataFieldFromSQL($sql,"RolName");
				}
				if (count($to_ids)>1) {
					$destinatarios.=" (" . _("y otros") . " " . (count($to_ids)-1) . " " . _("más") . ")";
				}
				$this->Items[$iditem]['Destinations']=$destinatarios;
			}
		}
		return $this->ItemsCount;
	}

	function ViewMessage() {
		parent::$db->LoadFormData($this,$this->id,1);
		if ($this->Data['ToID']!=$this->userID) { header("Location: " . $this->module . "?error=" . urlencode(_("Acceso no autorizado"))); die; }
		$sql="UPDATE " . $this->table . " SET ReadMsg=1 WHERE ID=" . $this->id;
		parent::$db->Qry($sql);
		$this->Remitente=parent::$db->GetDataRecord("users",$this->Data['FromID']);
		$this->Remitente['Avatar']=siteprotocol . sitedomain . "templates/gestion/assets/images/no-avatar.png";
		if (is_file(sitepath . "public/avatar/" . $this->Remitente['Image'])) { 
			$this->Remitente['Avatar']=siteprotocol . sitedomain . "public/avatar/" . $this->Remitente['Image'];
		}
		$this->AddMainMenu('Borrar',$this->module . '/delete/id/' . $this->Data['ID']);
		$in_block=$this->AddFormBlock('Mensaje');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Asunto","FieldName":"Form_Subject","Value":"' . addcslashes("RE: " . $this->Data['Subject'],'\\"') . '","Required": true}');
		$this->AddFormContent($in_block,'{"Type":"html","Text":"Mensaje","FieldName":"Form_Message","Value":"", "Required": true}');
		$this->AddFormHiddenContent("Form_ToID","u-" . $this->Data['FromID']);
		$this->AddFormHiddenContent("System_Action","new");
		$this->AddFormHiddenContent("System_ID","-1");
	}

	function ViewSentMessage() {
		$this->table.="_sent";
		parent::$db->LoadFormData($this,$this->id,1);
		if ($this->Data['FromID']!=$this->userID) { header("Location: " . $this->module . "?action=sent&error=" . urlencode(_("Acceso no autorizado"))); die; }
		$to_ids=explode(",", $this->Data['ToID']);
		$this->Data['Destinations']=array();
		foreach($to_ids as $idto=>$to) {
			unset($Crear);
			$Crear['value']=$to;
			$partes=explode("-", $to);
			if ($partes[0]=="u") {
				//Es un usuario...
				$sql="SELECT UserName FROM users WHERE ID=" . $partes[1];
				$Crear['text']=parent::$db->GetDataFieldFromSQL($sql,"UserName");
				$Crear['type']="user";
			}
			if ($partes[0]=="r") {
				//Es un rol...
				$sql="SELECT RolName FROM users_roles WHERE IDRol=" . $partes[1];
				$Crear['text']=parent::$db->GetDataFieldFromSQL($sql,"RolName");
				$Crear['type']="rol";
			}
			// if ($partes[0]=="g") {
			// 	//Es un rol...
			// 	$sql="SELECT Title FROM courses_groups WHERE ID=" . $partes[1];
			// 	$Crear['text']=parent::$db->GetDataFieldFromSQL($sql,"Title");
			// 	$Crear['type']="other1";
			// }
			$this->Data['Destinations'][]=$Crear;
		}
		$this->AddMainMenu('Borrar',$this->module . '/deletesent/id/' . $this->Data['ID']);
	}

	function NewItem() {
		parent::$db->InitFormData($this);
		$this->GetDestinations();
		$to="";
		$disabled="";
		if (isset($_GET['to'])) { 
			$Datos['value']="u-" . $_GET['to'];
			$Datos['text']=parent::$db->GetDataField("users",$_GET['to'],"UserName");
			$Datos['type']="user";
			$to[]=$Datos;
			$disabled=',"Readonly":"readonly"';
		}
		$to=json_encode($to);
		$in_block=$this->AddFormBlock('Mensaje');
		$this->AddFormContent($in_block,'{"Type":"tags-fixed","Text":"Destinatario","FieldName":"Form_ToID","Preload":' . $to . ', "Suggestions": "users--pm/suggestions/rand/' . rand() . '", "Required": true' . $disabled . '}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Asunto","FieldName":"Form_Subject","Value":"' . addcslashes($this->Data['Subject'],'\\"') . '","Required": true}');
		$this->AddFormContent($in_block,'{"Type":"html","Text":"Mensaje","FieldName":"Form_Message","Value":"' . addcslashes($this->Data['Message'],'\\"') . '", "Required": true}');
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->LoadTemplate('mp_compose.tpl.php');	
	}

	function PostItem() {
		//Guardamos el enviado...
		$_POST['Form_FromID']=$this->userID;
		$_POST['Form_DateSend']=date("Y-m-d H:i:s");
		$_POST['Form_ReadMsg']=1;
		parent::$db->PostToDatabase($this->table . "_sent",$_POST);
		//Preparamos el envío de mensaje...
		$Envio['Form_FromID']=$_POST['Form_FromID'];
		$Envio['Form_DateSend']=$_POST['Form_DateSend'];
		$Envio['Form_Subject']=$_POST['Form_Subject'];
		$Envio['Form_Message']=$_POST['Form_Message'];
		$Envio['Form_ReadMsg']=0;
		//Buscamos los usuarios a los que se debe enviar una copia del mensaje...
		$destinatarios=array();
		$to_ids=explode(",", $_POST['Form_ToID']);
		foreach($to_ids as $to) {
			$partes=explode("-", $to);
			if ($partes[0]=="u") {
				//Es un usuario...
				if (! in_array($partes[1], $destinatarios)) {$destinatarios[]=$partes[1];}
			}
			if ($partes[0]=="r") {
				//Es un grupo de usuarios...
				$sql="SELECT ID FROM users WHERE Rol=" . $partes[1];
				$Total=parent::$db->GetDataListFromSQL($sql,$usuarios_grupo);
				if ($Total>0) {
					foreach($usuarios_grupo as $usuario) {
						if (! in_array($usuario['ID'], $destinatarios)) {$destinatarios[]=$usuario['ID'];}
					}
				}
			}
			// if ($partes[0]=="g") {
			// 	//Es un grupo de alumnos...
			// 	$sql="SELECT IDUser FROM courses_groups_students WHERE IDGroup=" . $partes[1] . " AND DatePublish<=now() AND DateExpire>=now() AND Active=1";
			// 	$Total=parent::$db->GetDataListFromSQL($sql,$usuarios_grupo);
			// 	if ($Total>0) {
			// 		foreach($usuarios_grupo as $usuario) {
			// 			echo $usuario['IDUser'] ."<br>";
			// 			if (! in_array($usuario['IDUser'], $destinatarios)) {$destinatarios[]=$usuario['IDUser'];}
			// 		}
			// 	}
			// }
		}
		//Recorremos todos los destinatarios y les enviamos el mensaje...
		if (count($destinatarios)>0) {
			foreach($destinatarios as $destinatario) {
				$Envio['Form_ToID']=$destinatario;
				parent::$db->PostToDatabase($this->table,$Envio);
				if ($this->conf->Check('UserNotifyPM')) {
					$sql="SELECT Email,UserName FROM users WHERE ID=" . $destinatario;
					$Usuario=parent::$db->GetDataRecordFromSQL($sql);
					if ($Usuario!==false) {
						if ($Usuario['Email']!="") {
							$cuerpo='<p>' . _("Hola ") . $Usuario['UserName'] . "!</p>";
							$cuerpo.='<p>' . _("Acabas de recibir un mensaje interno en") . " " . siteTitle . " " . _("de") . " <strong>" . $this->userName . "</strong> " . _("con el título");
							$cuerpo.=" <strong>" . $Envio['Form_Subject'] . "</strong>.</p>";
							$cuerpo.="<p>" . _("Para leer el mensaje inicia sesión en la web y accede al servicio de") . " <strong>" . _("Mensajería Interna") . "</strong>.</p>";
							$realizarenvio=SendMail(siteTitle, $Usuario['Email'], _("Has recibido un nuevo mensaje interno"), $cuerpo, sitePasswordsMail, 1);
						}	 
					} 
				}
			}
		}
		return true;
	}

	function MassiveMarkRead() {
		if (isset($_POST['selected'])) {
			$sql="UPDATE " . $this->table . " SET ReadMsg=1 WHERE ID IN (" . implode(',', $_POST['selected']) . ")";
			parent::$db->Qry($sql);
		}
	}

	function MassiveMarkUnRead() {
		if (isset($_POST['selected'])) {
			$sql="UPDATE " . $this->table . " SET ReadMsg=0 WHERE ID IN (" . implode(',', $_POST['selected']) . ")";
			parent::$db->Qry($sql);
		}
	}

	function MassiveDelete() {
		$table=$this->table;
		if (isset($_GET['sent'])) { $table.="_sent"; }
		$sql="DELETE FROM " . $table . " WHERE ID IN (" . implode(',', $_POST['selected']) . ")";
		echo $sql;
		parent::$db->Qry($sql);
	}

	function DeleteMessage() {
		parent::$db->LoadFormData($this,$this->id,1);
		if ($this->Data['ToID']!=$this->userID) { return false; }
		$this->Delete();
		return true;
	}

	function DeleteSent() {
		$this->table.="_sent";
		parent::$db->LoadFormData($this,$this->id,1);
		if ($this->Data['FromID']!=$this->userID) { return false; }
		$this->Delete();
		return true;
	}

	function AdminInbox() {
		$this->Inbox();
		$this->LoadTemplate('mp_inbox.tpl.php');
	}

	function AdminMessage() {
		$this->ViewMessage();
		$this->LoadTemplate('mp_message.tpl.php');
	}

	function AdminSent() {
		$this->Sentbox();
		$this->LoadTemplate('mp_sent.tpl.php');
	}

	function AdminSentMessage() {
		$this->ViewSentMessage();
		$this->LoadTemplate('mp_sent_message.tpl.php');
	}

	function AdminPost() {
		$this->PostItem(); 
		header("Location: " . siteprotocol . sitedomain. sitePanelFolder . "/" .  $this->module . "/text/" . urlencode(base64_encode(_("Se ha enviado el mensaje interno"))));
	}

	function JSONSuggestions() {
		$this->GetDestinations();
		echo json_encode($this->Destinations,true); 
	}

	function AdminDeleteMessage() {
		$result=$this->DeleteMessage();
		if ($result) { 
			$add="/text/" . urlencode(base64_encode(_("Se ha borrado el mensaje"))); 
		} else {
			$add="/error/" . urlencode(base64_encode(_("Acceso no autorizado"))); 
		}
		header("Location: " . siteprotocol . sitedomain. sitePanelFolder . "/" . $this->module . $add);
	}

	function AdminDeleteSent() {
		$result=$this->DeleteSent();
		if ($result) { 
			$add="/text/" . urlencode(_("Se ha borrado el mensaje")); 
		} else {
			$add="/error/" . urlencode(_("Acceso no autorizado")); 
		}
		header("Location: " . siteprotocol . sitedomain. sitePanelFolder . "/" . $this->module . "/action/sent" . $add);
	}

	function RunAction() {
		if ($this->action=="list") { $this->AdminInbox(); }
		if ($this->action=="message") { $this->AdminMessage(); }
		if ($this->action=="sent") { $this->AdminSent(); }
		if ($this->action=="sentmessage") { $this->AdminSentMessage(); }
		if ($this->action=="new") { $this->NewItem(); }
		if ($this->action=="post") { $this->AdminPost(); }
		if ($this->action=="suggestions") { $this->JSONSuggestions(); }
		if ($this->action=="markread") { $this->MassiveMarkRead(); }
		if ($this->action=="markunread") { $this->MassiveMarkUnRead(); }
		if ($this->action=="markdelete") { $this->MassiveDelete(); }
		if ($this->action=="delete") { $this->AdminDeleteMessage(); }
		if ($this->action=="deletesent") { $this->AdminDeleteSent(); }
	}	
	
	function __destruct(){}
}
?>