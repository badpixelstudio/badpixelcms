<?php
require_once("extras.class.php");

// ****** D O C U M E N T A C I Ó N ******
//****************************************
//

class MasterExtraComments extends Extras{

	var $view="";
	var $EnableAnonymous=siteEnableCommentsAnonymousUsers;
	var $EnableNotifyAnonymous=true;
	var $AutoActivateFromDataUsers=siteRequireActivationCommentsLoggedUsers;
	//constructor

	function __construct($hostclass,$idlink=0) {
		$this->AutoActivateFromDataUsers=!$this->AutoActivateFromDataUsers;
		$this->title_extra="Comentarios";
		$this->extra = 'comments';
		
		if (isset($_GET['view'])) { $this->view=$_GET['view']; }
		
		parent::__construct($hostclass,$idlink);
	}

	function GetXtraItems($paginate=false,$orden="") {
		$sql_paginas = "SELECT " . $this->table . "_comments.*, users.userName as Author, users.Image as Image FROM " . $this->table . "_comments LEFT JOIN users ON " . $this->table . "_comments.IDAuthor=users.ID WHERE " . $this->linkfield . "=" . $this->idprior;
		if ($this->view=="active") { $sql_paginas.= " AND " . $this->table . "_comments.Active=1"; }
		if ($this->view=="noactive") { $sql_paginas.= " AND " . $this->table . "_comments.Active=0"; }
		$sql_paginas.= " ORDER BY Orden ASC";
		if ($paginate) {
			$this->ItemsCount=parent::$db->GetDataListPagedFromSQL($sql_paginas,$this->page,$this->offset,$this->Items);
		} else {
			$this->ItemsCount=parent::$db->GetDataListFromSQL($sql_paginas,$this->Items);
		}
		if ($this->ItemsCount>0) {
			foreach ($this->Items as $id=>$itm){
				if ($itm['Name']=="") {	$this->Items[$id]['Name']=$itm['Author']; }
				$this->Items[$id]['UserAvatar']='';
				if (isset($itm['Image'])) {
					if (is_file(sitepath . "public/avatar/" . $itm['Image'])) {
						$this->Items[$id]['UserAvatar']=siteprotocol . sitedomain . "public/avatar/" . $itm['Image'];
					} else {
						$this->Items[$id]['UserAvatar']='';
					}
				}
			}
		}
		//Support deprecated variables
		$this->Total=$this->ItemsCount;
		$this->Data=$this->Items;
		return $this->ItemsCount; 
	}	
	
	function GetComments() {
		$this->view="active";
		$this->GetXtraItems();	
	}
	
	function PostExtra($formulario) {
		//Return:
		//  2: Received, moderation pending
		//  1: Received and published
		//  0: Internal error
		// -1: Only registered users cant send comments.
		$devolver=2;
		
		//Si no se recibieron datos de usuario, suponemos que el comentario lo envía el usuario activo.
		if (! isset($formulario['Extra_comments_IDAuthor'])) { 
			if ($this->userID!=0) { 
				$formulario['Extra_comments_IDAuthor']=$this->userID; 
			} else {
				//Si no está logueado
				$formulario['Extra_comments_IDAuthor']=0;
				//Si no se permiten publicaciones de invitados, devolvemos error
				if (intval($this->EnableAnonymous)==0) { 
					$devolver=-1; 
				} else {
					$formulario['Extra_comments_Active']=1; 
					$devolver=1; 
				}
			}
		}
		if (($this->AutoActivateFromDataUsers) and (! isset($formulario['Extra_comments_Active']))) { 
			$formulario['Extra_comments_Active']=1; 
			$devolver=1; 
		}
		// if (intval($this->EnableAnonymous)==0) {
		// 	$formulario['Extra_comments_Active']=1; 
		// 	$devolver=1; 
		// }
		
		//Ahora, si tenemos ID de usuario, buscamos los datos y los volcamos al array...
		if ($formulario['Extra_comments_IDAuthor']!=0) {
			//Guardamos los datos que corresponden al usuario...	
			$sql="SELECT UserName, Email FROM users WHERE ID='" . $formulario['Extra_comments_IDAuthor'] . "'";
			$Datos=parent::$db->GetDataRecordFromSQL($sql);
			$formulario['Extra_comments_Name']=$Datos['UserName'];
			$formulario['Extra_comments_Email']=$Datos['Email'];
		}
		//Parcheamos la activación, por si acaso
		PatchCheckBox($formulario,'Extra_comments_Active');
		
		//Corregimos la fecha, si el modo es Creacion
		if (! isset($formulario['System_Action'])) { $formulario['System_Action']="new"; }
		if ($formulario['System_Action']!="edit") {
			$formulario['Extra_comments_DatePublish']=date('Y-m-d H:i:s');	
		}
		
		//Guardamos los datos, si estamos autorizados.
		if ($devolver>0) { parent::PostExtra($formulario); }
		//Devolvemos los datos al $_POST por si se requieren en otras funciones (el envio de email lo requiere);
		$_POST=$formulario;
		//Devolvemos el ID de operación
		return $devolver;
	}
	
	function JQuery_post() {
		if (count($_POST)>0) {
			foreach ($_POST as $iden=>$valor) {
				$valor=str_replace('â‚¬','&euro;',$valor);
				$_POST[$iden]=$valor;
			}
		}	
		$_POST['Extra_' . $this->extra . "_Comment"]=CleanHTML($_POST['Extra_' . $this->extra . "_Comment"]);	
		$this->linkid=$_POST['Extra_' . $this->extra . '_IDFather'];	
		$devuelve=$this->PostExtra($_POST);
		if (($this->EnableNotifyAnonymous) and ($devuelve>0)) { $this->NotifyNewComment($_POST); }
		echo $devuelve;
		exit;		
	}
	
	function JQuery_delete() {
		//Comprobamos si tenemos autorización...
		$autorizado=false;
		//Si el user tiene nivel 99, estará autorizado
		if ($this->userLevel>=99) { $autorizado=true; }
		if (! $autorizado) {
			$usuario_autorizado=-1;
			$sql="SELECT * FROM " . $this->table . " WHERE ID=" . $this->linkid;
			$Registro=self::$db->GetDataRecordFromSQL($sql);
			if (isset($Registro['IDUser'])) { $usuario_autorizado=$Registro['IDUser']; }
			if (isset($Registro['IDAuthor'])) { $usuario_autorizado=$Registro['IDAuthor']; }
			if (($usuarios_autorizado>0) and ($usuario_autorizado==$this->userID)) { $autorizado=true; }
		}
		//Si esta autorizado, borramos...
		if ($autorizado) {
			echo intval($this->DeleteItem($_GET['id']));	
		} else {
			echo "0";
		}	
		exit;
	}
	
	function JQuery_edit() {
		//Comprobamos si tenemos autorización...
		$autorizado=false;
		//Si el user tiene nivel 99, estará autorizado
		if ($this->userLevel>=99) { $autorizado=true; }
		if (! $autorizado) {
			$usuario_autorizado=-1;
			$sql="SELECT * FROM " . $this->table . " WHERE ID=" . $this->linkid;
			$Registro=self::$db->GetDataRecordFromSQL($sql);
			if (isset($Registro['IDUser'])) { $usuario_autorizado=$Registro['IDUser']; }
			if (isset($Registro['IDAuthor'])) { $usuario_autorizado=$Registro['IDAuthor']; }
			if (($usuarios_autorizado>0) and ($usuario_autorizado==$this->userID)) { $autorizado=true; }
		}
		//Si esta autorizado, borramos...
		if ($autorizado) {	
			//Obtenemos los datos del post...
			$this->LoadFormExtra();
			if (isset($_POST['Extra_comments_Comment'])) {
				$Datos['System_ID']=$_GET['id'];
				$Datos['System_Action']="edit";
				$Datos['Form_Comment']=$_POST['Extra_comments_Comment'];
				self::$db->PostToDatabase($this->table . "_comments",$Datos);
				header("Location: " . $_POST['Return_to']);
				
			} else {
				$this->HeadTitle="Editar comentario";
				$this->loadtemplatepublic('extras_comments_edit.tpl.php');
			}
		} else {
			header("Location: " . $_SERVER['HTTP_REFERER']);	
		}
	}
	
	function GetAllComments() {
		$sql="SHOW TABLES LIKE '%_comments'";
		$Total=parent::$db->GetDataListFromSQL($sql,$Tablas);
		$this->Total=0;
		if ($Total>0) {
			foreach ($Tablas as $table) {
				$table_name=$table['Tables_in_' . dbname . ' (%_comments)'];
				$partes=explode("_",$table_name);
				$x=count($partes)-1;
				if ($x>0) {
					unset($partes[$x]);
				}
				$modulo=implode("_",$partes);
				unset($this->structure);
				$sql_estructura="SHOW COLUMNS FROM ". $table_name;
				$this->TotalFields=parent::$db->GetDataListFromSQL($sql_estructura,$this->structure);
				$select_part=$table_name . ".*, '" . $modulo . "' as TableName";
				$join_part="";
				$where_part="WHERE " . $table_name . ".ID>0";
				if ((siteMulti) and ($this->ExistsField('IDBusiness'))) {
					$select_part.=", business.Name as BusinessName";
					$join_part.=" LEFT JOIN business ON " . $table_name . ".IDBusiness=business.ID";
					if ((siteMulti) and ($this->businessID!=0) and (!defined('InFrontEnd'))) {  
						$where_part.=" AND IDBusiness= " . $this->businessID;
					}
				}
				if ($this->ExistsField('IDAuthor')) {
					$select_part.=", users.UserName as UserName, users.Image as UserAvatar";
					$join_part.=" LEFT JOIN users ON " . $table_name . ".IDAuthor=users.ID";
				}
				if ($this->ExistsField('Active')){
					if ($this->view=="active") { $where_part.=" AND " .   $table_name . ".Active=1"; }
					if ($this->view=="noactive") { $where_part.=" AND " . $table_name . ".Active=0"; }
				}
				
				$sql_paginas = "SELECT " . $select_part . " FROM " . $table_name . $join_part . " " . $where_part . " ORDER BY DatePublish DESC";
				unset($Registros);
				$SubTotal=parent::$db->GetDataListFromSQL($sql_paginas,$Registros);
				if ($SubTotal>0) {
					foreach($Registros as $id=>$itm) {
						if ($itm['Name']=="") {	$Registros[$id]['Name']=$itm['UserName']; }
						if (isset($itm['UserAvatar'])) {
							if (is_file(sitepath . "public/avatar/" . $itm['UserAvatar'])) {
								$itm['UserAvatar']=siteprotocol . sitedomain . "public/avatar/" . $itm['UserAvatar'];
							} else {
								$itm['UserAvatar']='';
							}
						} else {
							$itm['UserAvatar']='';
						}
						$this->Data[]=$itm;						
					}
					$this->Total=$this->Total+$SubTotal;
				}
			}
		}
		//Ordenamos el array...
		$this->Data=orderMultiDimensionalArray ($this->Data, 'DatePublish', true);
	}
	
	function EditFromAll() {
		if (isset($GLOBALS['Core']->_values['table'])) { $this->table=$GLOBALS['Core']->_values['table']; }
		$this->LoadFormExtra();
		$this->PrepareFormEditAll();
		$this->LoadTemplate('edit.tpl.php');		
	}
	
	function PostFromAll($redirect=true) {
		if ($_POST['Form_IDAuthor']!=0) {
			//Guardamos los datos que corresponden al usuario...	
			$sql="SELECT UserName, Email FROM users WHERE ID='" . $_POST['Form_IDAuthor'] . "'";
			$Datos=parent::$db->GetDataRecordFromSQL($sql);
			$_POST['Form_Name']=$Datos['UserName'];
			$_POST['Form_Email']=$Datos['Email'];
		}
		PatchCheckBox($_POST,'Form_Active');
		parent::$db->PostToDatabase($_POST['System_Table'] . "_comments", $_POST);
		if (redirect) { header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/allcomments"); }
		return true;
	}

	function DeleteFromAll() {
		if (isset($GLOBALS['Core']->_values['table'])) { $this->table=$GLOBALS['Core']->_values['table']; }
		parent::$db->Qry("DELETE FROM " . $this->table . "_comments WHERE ID=" . $this->linkid);
		echo "1";
	}
	
	function ChangeActivation($redirect=true) {
		if (isset($GLOBALS['Core']->_values['table'])) { $this->table=$GLOBALS['Core']->_values['table']; }
		$this->Data=parent::$db->GetDataRecord($this->table . "_" . $this->extra,$GLOBALS['Core']->_values['id']);
		$Datos['System_Action']="edit";
		$Datos['System_ID']=$GLOBALS['Core']->_values['id'];
		$Datos['Form_Active']=1;
		if ($this->Data['Active']==1) { $Datos['Form_Active']=0; }
		parent::$db->PostToDatabase($this->table . "_" . $this->extra, $Datos);
		if ($redirect) { 
			header("Location: " . $this->table);	
		} else {
			return $Datos['Form_Active'];
		}
	}
	
	function NotifyNewComment($formulario) {
		$cuerpo="<p>El usuario <strong>" . $formulario['Extra_comments_Name'] . "</strong>";
		if ($formulario['Extra_comments_IDAuthor']==0) { $cuerpo.=" (no registrado)"; }
		$cuerpo.=" ha enviado un comentario en el m&oacute;dulo " . $this->module . ", con el mensaje:</p>";
		$cuerpo.="<p><i>" . FormatPlainText(stripslashes($formulario['Extra_comments_Comment'])) . "</i></p>";
		$cuerpo.="<p>&nbsp;</p>";
		$cuerpo.="<p>Para que la publicaci&oacute;n sea visible, debe activarlo desde el Panel de Gesti&oacute;n:</p>";
		$cuerpo.="<ul>";
		$cuerpo.="<li><a href='" . siteprotocol . sitedomain . sitePanelFolder . "/comments'>Ver &uacute;ltimos comentarios</a></li>";
		$cuerpo.="</ul>";
		$realizarenvio=SendMail(siteTitle, siteMainMail, _("Comentario recibido"), $cuerpo, siteMainMail, 1);
	}

	function PutTemplate($clase,$in_block) {
		if ($this->Total>0) {
			$clase->AddFormContent($in_block,'{"Type": "group", "Text": "Gestionar comentarios existentes","FieldName": "Xtr_Comments"}');
			$clase->AddFormContent($in_block,'{"Type": "xtra_comments","Items": ' . json_encode($this->Data) . ',"Prior": "' .$this->linkid . '", "Module":"' . $this->module . '", "LinkBase": "' . $this->baselink . '","Prefix": "' . $this->post_prefix . '"}');
		}
		if ($this->EnableAppend) { 
			$clase->AddFormContent($in_block,'{"Type": "group", "Text": "Añadir nuevo comentario","FieldName": "Xtr_Comments_Append"}');
			$clase->AddFormContent($in_block,'{"Type": "textarea", "Text": "Comentario","FieldName": "Extra_comments_Comment","Value":"", "Required": true}');
			$clase->AddFormContent($in_block,'{"Type":"combo-json","Text":"Puntuación","FieldName":"Extra_comments_Points","Value":"3", "JsonValues": {"1":"1 Estrella","2":"2 Estrellas", "3":"3 Estrellas", "4":"4 Estrellas", "5":"5 Estrellas"}}');
			$clase->AddFormContent($in_block,'{"Type":"combo","Text":"Usuario","FieldName":"Extra_comments_IDAuthor","Value":"' . $this->userID . '", "ListTable": "users", "ListValue": "ID", "ListOption": "UserName", "ListOrder":"UserName", "NullValue": "0"}');
			$clase->AddFormContent($in_block,'{"Type": "text", "Text": "Nombre","FieldName": "Extra_comments_Name","Value":"' . $this->username . '"}');
			$clase->AddFormContent($in_block,'{"Type": "email", "Text": "Correo Electrónico","FieldName": "Extra_comments_Email","Value":"' . $this->useremail . '"}');
			$clase->AddFormContent($in_block,'{"Type":"checkbox","Text":"Comentario visible","FieldName":"Extra_comments_Active","Value":"1"}');
			$clase->AddFormHiddenContent("Extra_comments_IDFather",$this->idprior);
		}
	}	

	function PrepareView() {
		$in_block=$this->AddFormBlock('Comentarios');
		$this->PutTemplate($this,$in_block);
		$this->TemplatePostScript=$this->module . "/" . $this->baselink . $this->extra . "_item_post";
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('Comentario');
		$this->AddFormContent($in_block,'{"Type": "textarea", "Text": "Comentario","FieldName": "Extra_comments_Comment","Value":"", "Required": true}');
		$this->AddFormContent($in_block,'{"Type":"combo-json","Text":"Puntuación","FieldName":"Extra_comments_Points","Value":"3", "JsonValues": {"1":"1 Estrella","2":"2 Estrellas", "3":"3 Estrellas", "4":"4 Estrellas", "5":"5 Estrellas"}}');
		$this->AddFormContent($in_block,'{"Type":"combo","Text":"Usuario","FieldName":"Extra_comments_IDAuthor","Value":"' . $this->userID . '", "ListTable": "users", "ListValue": "ID", "ListOption": "UserName", "ListOrder":"UserName", "NullValue": "0"}');
		$this->AddFormContent($in_block,'{"Type": "text", "Text": "Nombre","FieldName": "Extra_comments_Name","Value":"' . $this->username . '"}');
		$this->AddFormContent($in_block,'{"Type": "text", "Text": "Correo Electrónico","FieldName": "Extra_comments_Email","Value":"' . $this->useremail . '"}');
		$this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Comentario visible","FieldName":"Extra_comments_Active","Value":"1"}');
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->AddFormHiddenContent("Extra_comments_IDFather",$this->Data['IDFather']);
		$this->TemplatePostScript=$this->module . "/" . $this->baselink . $this->extra . "_item_post";
	}

	function PrepareFormEditAll() {
		$in_block=$this->AddFormBlock('Comentario');
		$this->AddFormContent($in_block,'{"Type": "textarea", "Text": "Comentario","FieldName": "Form_Comment","Value":"' . addcslashes($this->Data['Comment'],'\\"') . '", "Required": true}');
		$this->AddFormContent($in_block,'{"Type":"combo-json","Text":"Puntuación","FieldName":"Form_Points","Value":"' . $this->Data['Points'] . '", "JsonValues": {"1":"1 Estrella","2":"2 Estrellas", "3":"3 Estrellas", "4":"4 Estrellas", "5":"5 Estrellas"}}');
		$this->AddFormContent($in_block,'{"Type":"combo","Text":"Usuario","FieldName":"Form_IDAuthor","Value":"' . $this->Data['IDAuthor'] . '", "ListTable": "users", "ListValue": "ID", "ListOption": "UserName", "ListOrder":"UserName", "NullValue": "0"}');
		$this->AddFormContent($in_block,'{"Type": "text", "Text": "Nombre","FieldName": "Form_Name","Value":"' . addcslashes($this->Data['Name'],'\\"') . '"}');
		$this->AddFormContent($in_block,'{"Type": "text", "Text": "Correo Electrónico","FieldName": "Form_Email","Value":"' . addcslashes($this->Data['Email'],'\\"') . '"}');
		$this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Comentario visible","FieldName":"Form_Active","Value":"' . $this->Data['Active'] . '"}');
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->AddFormHiddenContent("System_Table",$this->table);
		$this->TemplatePostScript="allcomments/post_from_all";
	}
	
	function Run($action) {
		if ($action=="jquery_post") { $this->JQuery_post(); }
		if ($action=="jquery_edit") { $this->JQuery_edit(); }
		if ($action=="jquery_delete") { $this->JQuery_delete(); }
		if ($action=="list") {
			$this->GetAllComments(); 
			$this->LoadTemplate('extras_comments_latest.tpl.php');	
		}
		if ($action=="edit_from_all") { $this->EditFromAll(); }
		if ($action=="post_from_all") { $this->PostFromAll(); }
		if ($action=="delete_from_all") { $this->DeleteFromAll(); }
		if ($action=="changeactivation") { $this->ChangeActivation(); }
		if ($action=="change") { echo ($this->ChangeActivation(false)); }
		parent::Run($action);	
	}
	
}
?>