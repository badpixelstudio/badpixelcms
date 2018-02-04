<?php
// Gestión básica de los extras 
// Creado por Israel García Sáez para BadPixel.
// Revisión: 1.0 de 1 de Junio de 2012, por Israel Garcia.
// Revisión: 1.1 de 14 de Agosto de 2012, por Israel Garcia.
//		Elimina error de carga de permisos en Módulos estándar
//		Corregido bug que hace ignorar $_GET[id] cuando el linkid viene definido en el constructor. (Linea 53)


class MasterExtras extends Core{

	//Inicializamos valores por defecto
	var $idprior=0;
	var $id = 0;
	var $page = 1;
	var $offset = 10;
	var $module = ''; 	//Establece el prefijo usado en las tablas.
	var $title_module='Paginas';
	var $title_extra="Extra";
	var $extra = 'images';
	var $return = ''; 		//Define donde debe redirigir el script al finalizar los procesos, en blanco no redirije.
	var $options = array(); //Define el número de archivos que se crearán, y las opciones de cada uno de ellos.
							// []['Folder'] Carpeta donde se guardará la imagen
							// []['Subname'] particula que se añadirá al final del nombre del archivo, en blanco es ignorado.
	var $filename = ''; //Define el nombre del archivo final.
	var $default_description="";
	var $linkfield = 'IDFather';
	var $linkid = 0;
	var $post_prefix="";
	var $EnableAppend = true;
	var $EnableAdmin = false;
	
	var $Total=0;
	var $Data = array();
	
	var $structure = array(); //Guarda la estructura de la tabla
	var $TotalFields= 0;
	
	var $conf = null;
	
	//constructor
	function __construct($hostclass,$idlink=0) {
		//Parámetros del constructor:
		//hostclass -> Es la clase sobre la que se ejecuta el extra
		//idlink 	-> Es el identificador del elemento sobre la que se añaden los extras

		//Variables de la clase Extra.
		//module 	-> Identifica el módulo sobre el que se ejecuta el extra
		//idlink	-> ID del elemento del módulo al que se añaden los extras
		//folderlink-> Establece la clase que controlará al extra en el panel
		//baselink	-> Establece el prefijo a usar en la acción del panel
		//conf
		parent::$db = DBase::getInstance(); 
		parent::__construct();
		$params=$GLOBALS['Core']->_values;
		if (count($params)>0) {
			foreach($params as $k=>$v) {
				$_GET[$k]=$v;
			}
		}
		$this->module=$hostclass->module;
		$this->title_module=$hostclass->title;
		$this->linkid=$idlink;
		if ($this->linkid==0) { $this->linkid=$hostclass->id; }
		$this->table=$hostclass->table;
		if ($hostclass->xtras_prefix!="") { $this->table=$hostclass->xtras_prefix; }
		$this->folderlink=$this->module;
		if ($hostclass->class!=$hostclass->module) {
			if (! $hostclass->xtras_RunSubClass) {
				$this->folderlink.="--" . $hostclass->class;
			} else {
				$this->post_prefix=$hostclass->class . "_";
			}
		}
		
		//Cargamos la configuración del extra...
		$this->LoadConfig($hostclass->conf->GetActualConfig());
		$this->title=$this->title_extra;
		//Parcheamos algunos parámetros URL
		if (($this->linkid==0) and (isset($_POST[$this->linkfield]))) { $this->linkid=$_POST[$this->linkfield]; }
		if (isset($_GET['id'])) { $this->linkid=$_GET['id']; }	 //($this->linkid==0) and 
		if ($this->idprior==0) {
			if (isset($_GET['prior'])) { 
				$this->idprior=$_GET['prior'];
			} else {
				$this->idprior=$idlink;
			}
		}
		//MIGA DE PAN...	
		$this->BreadCrumb[$this->title_module]=$this->module;	
		//Tratamos de conseguir el nombre del elemento actual...
		if (self::$db->GetDataRecordFromSQL("SHOW TABLES LIKE '" . $this->table . "'")!==false) {
			$Categoria=parent::$db->GetDataRecordFromSQL("SELECT * FROM " . $this->table . " WHERE ID=" . $this->idprior);
			$nombre="Elemento";
			if (isset($Categoria['Title'])) { 
				$nombre=$Categoria['Title']; 
			}
			if ((isset($Categoria['Name']))) {
				$nombre=$Categoria['Name']; 
			}
			if ($idlink!=0) { $this->CheckItemBusinessPermission($Categoria); }
			$this->BreadCrumb[$nombre]=$this->module . "/edit/id/" . $this->idprior;
			$this->default_description=$nombre;
		}
		$this->BreadCrumb[$this->title_extra]=$this->module . '/' . $this->extra . "_view/prior/" . $this->idprior;			
	}
	
	function GetTableFields() {
		if ($this->module!="") {
			$sql_estructura="SHOW COLUMNS FROM ". $this->table . "_" . $this->extra;
			$this->TotalFields=parent::$db->GetDataListFromSQL($sql_estructura,$this->structure);
		} else {
			$this->TotalFields=0;
			$this->structure=array();
		}
	}
	
	function ExistsField($campo) {
		$encontrado=false;
		if ($this->TotalFields>0) {
			foreach ($this->structure as $field) {
				if ($field['Field']==$campo) { 
					$encontrado=true;
				}
			}
		}
		return $encontrado;
	}
	
	function LoadConfig($config) {
		$this->conf['EnableAuthor']=false;
		$this->conf['EnableLevel']=false;
		$this->conf['EnableCounter']=false;
		if (isset($config['ExtraEnableAuthor'])) {
			if ($config['ExtraEnableAuthor']==1) { $this->conf['EnableAuthor']=true; }
		}
		if (isset($config['ExtraEnableLevel'])) {
			if ($config['ExtraEnableLevel']==1) { $this->conf['EnableLevel']=true; }
		}
		if (isset($config['ExtraEnableCounter'])) {
			if ($config['ExtraEnableCounter']==1) { $this->conf['EnableCounter']=true; }
		}
	}
	
	function CheckConfig($param) {
		$devolver=false;
		if (isset($this->conf[$param])) { $devolver=$this->conf[$param]; }
		return $devolver;	
	}
	
	function GetXtraItems($paginate=false,$orden="") {
		$select_part=$this->table . "_" . $this->extra . ".*";
		$join_part="";
		$where_part=" WHERE " . $this->linkfield . "=" . $this->idprior;
		if ((siteMulti) and ($this->ExistsField('IDBusiness'))) {
			$select_part.=", business.Name as BusinessName";
			$join_part.=" LEFT JOIN business ON " . $this->table . "_" . $this->extra . ".IDBusiness=business.ID";
			if ((siteMulti) and ($this->businessID!=0) and (!defined('InFrontEnd'))) {  
				$where_part.=" AND IDBusiness= " . $this->businessID;
			}
		}
		if ($this->ExistsField('IDAuthor')) {
			$select_part.=", users.UserName as UserName";
			$join_part.=" LEFT JOIN users ON " . $this->table . "_" . $this->extra . ".IDAuthor=users.ID";
		}
		if ($this->ExistsField('Active')){
			if ($this->view=="active") { $where_part.=" AND " . 	$this->table . "_" . $this->extra . ".Active=1"; }
			if ($this->view=="noactive") { $where_part.=" AND " . 	$this->table . "_" . $this->extra . ".Active=0"; }
		}
		$sql_paginas = "SELECT " . $select_part . " FROM " . $this->table . "_" . $this->extra . $join_part . $where_part;
		if ($orden=="") {  $orden= " Orden ASC"; }
		$sql_paginas.=" ORDER BY ". $orden;
		if ($paginate) {
			$this->ItemsCount=parent::$db->GetDataListPagedFromSQL($sql_paginas,$this->page,$this->offset,$this->Items);	
		} else {
			$this->ItemsCount=parent::$db->GetDataListFromSQL($sql_paginas,$this->Items);
		}
		//Update tags deprecated
		$this->Total=$this->ItemsCount;
		$this->Data=$this->Items;

		return $this->ItemsCount; 
	}	

	//DEPRECATED: Compatibilidad versiones antiguas...
	function GetItems($conditions="",$paged=false,$order="",$search=false,$translate=true,$SQLselect="") {
		$this->GetXtraItems($paged,$order);
	}
	
	function InitFormExtra() {
		$sql_estructura="SHOW COLUMNS FROM ". $this->table . "_" . $this->extra;
		$StruCount=parent::$db->GetDataListFromSQL($sql_estructura,$Stru);
		if($StruCount>0){
			$this->Data['Action']="new";
			$this->Data['ID']=-1;
			foreach($Stru as $stru) {
				$nombrecampo=$stru['Field'];
				$tipocampo=$stru['Type'];
				//Inicializamos los valores por defecto...
				$valor='';
				$pos=strpos($tipocampo,'date');
				if ($pos!=false) {	$valor=date('d/m/Y'); }
				//Si es un timestamp
				$pos=strpos($tipocampo,'timestamp');
				if ($pos!=false) {	$valor=time(); }
				//Si es un valor numerico...
				$pos=strpos($tipocampo,'int');
				if ($pos!=false) {	$valor=0; }
				$pos=strpos($tipocampo,'float');
				if ($pos!=false) {	$valor=0; }					
				$this->Data[$nombrecampo]=$valor;	
			}
		} else{
			return false;	
		}		
	}
	
	
	function LoadFormExtra() {
		$sql_estructura="SHOW COLUMNS FROM ". $this->table . "_" . $this->extra;
		$StruCount=parent::$db->GetDataListFromSQL($sql_estructura,$Stru);
		$Data=parent::$db->GetDataRecord($this->table . "_" . $this->extra,$this->linkid);		
		if($StruCount>0){
			$this->Data['Action']="edit";
			$this->Data['ID']=$this->linkid;
			foreach($Stru as $stru) {
				$nombrecampo=$stru['Field'];
				$tipocampo=$stru['Type'];
				//Inicializamos los valores por defecto...
				$valor='';
				//Cargamos el valor actual...
				if ($Data!==false) {
					$valor=$Data[$nombrecampo];	
				}				
				$this->Data[$nombrecampo]=$valor;	
			} 
		} else{
			return false;	
		}		
	}
	
	function PostExtra($formulario) {
		//Inicializamos las variables de sistema...
		$System_Action="new";
		$System_ID=-1;
		if (isset($formulario['System_Action'])) { $System_Action=$formulario['System_Action']; }
		if (isset($formulario['System_ID'])) { $System_ID=$formulario['System_ID']; }		
		//Procesamos unicamente los que comienzan por "Extra_" y el nombre del extra seguido de "_", por ejemplo "Extra_attachments_"
		$identificador_formulario='Extra_' . $this->extra . "_";
		$long_id_formulario=strlen($identificador_formulario);
		$num_fields=0;
		$list_fields='';
		$list_values='';
		$to_get_id='';
		//Si es una creación de registro, añadimos algunos campos
		if ($System_Action=="new") {
			//Si no se ha mandado la información de IDFather, la introducimos...
			if (! isset($formulario['Extra_' . $this->extra . "_" . $this->linkfield])) {
				$formulario['Extra_' . $this->extra . "_" . $this->linkfield]=$this->linkid;
			}
			if (($formulario['Extra_' . $this->extra . "_" . $this->linkfield]==0) or ($formulario['Extra_' .$this->extra . "_" . $this->linkfield]=="")) {
				$formulario['Extra_' . $this->extra . "_" . $this->linkfield]=$this->linkid;
			}
			//Añadimos algunos campos, si existen en la BD...
			if ($this->ExistsField('IDBusiness')) {
				if (! isset($fomulario['Extra_' . $this->extra . '_IDBusiness'])) { $formulario['Extra_' . $this->extra . '_IDBusiness']=$this->businessID; }
			}
			if ($this->ExistsField('IDAuthor')) {
				if (! isset($fomulario['Extra_' . $this->extra . '_IDAuthor'])) { $formulario['Extra_' . $this->extra . '_IDAuthor']=$this->userID; }
			}
		}
		foreach($formulario as $clave=>$valor) {
			$valido=strpos($clave,$identificador_formulario);
			if ($valido===0) {
				$num_fields++;
				//Es un campo "Form_", se procesa...
				if ($System_Action=="new") {
					$list_fields.=substr($clave,strlen($identificador_formulario),strlen($clave)) . ", ";
					$list_values.="'" . addslashes($valor) . "', ";
					if ($num_fields<=3) { $to_get_id.=substr($clave,$long_id_formulario,strlen($clave)-$long_id_formulario) . "='" . addslashes($valor) . "' AND "; }
				}
				if ($System_Action=="edit") {
					$list_values.=substr($clave,$long_id_formulario,strlen($clave)-$long_id_formulario) . "='" . addslashes($valor) . "', ";
				}				
			}
		}
		$devolver=false;
		if ($num_fields>0) {
			//Quitamos la parte final...
			$list_fields=substr($list_fields,0,strlen($list_fields)-2);
			$list_values=substr($list_values,0,strlen($list_values)-2);	
			$to_get_id=substr($to_get_id,0,strlen($to_get_id)-5);		
			//Construimos la sql final...
			if ($System_Action=="new") { $sql="INSERT INTO " . $this->table . "_" . $this->extra . " (" . $list_fields . ") VALUES (" . $list_values . ")"; }
			if ($System_Action=="edit") { $sql="UPDATE " . $this->table . "_" . $this->extra . " SET " . $list_values . " WHERE ID=" . $System_ID; }
			//Ejecutamos la sql...
			//echo $sql."<br/>"; die();
			$ejecutar = parent::$db->Qry($sql);
			//echo $ejecutar;
			if($ejecutar !== "Error"){
				//Añadimos el valor al orden...
				$sql="UPDATE " .  $this->table . "_" . $this->extra . " SET Orden=ID WHERE Orden=0";
				$ejecutar = parent::$db->Qry($sql);
				//Obtenemos el IDentificador del registro guardado...
				if ($System_Action=="new") {
					$sql="SELECT * FROM " .  $this->table . "_" . $this->extra . " WHERE " . $to_get_id . " ORDER BY ID DESC";			
					//Ejecutamos la sql...
					$getLast = parent::$db->GetDataRecordFromSQL($sql);
					if($getLast!==false){		
						//Validamos por si el campo está en minúsculas o mayúsculas...
						if (isset($getLast['id'])) { $System_ID=$getLast['id']; }
						if (isset($getLast['Id'])) { $System_ID=$getLast['Id']; }
						if (isset($getLast['ID'])) { $System_ID=$getLast['ID']; }
					} else {
						return false;	
					}
				}
				$devolver=$System_ID;
			} else {
				return false;
			}
		}
		//Devolvemos el ID afectado...
		return $devolver;	
	}
	
	function PostAllItems() {
		$_POST['System_Action']="new";
		if(isset($_POST['Extras_' . $this->extra])) {
			if(is_array($_POST['Extras_' . $this->extra])) {
				foreach ($_POST['Extras_' . $this->extra] as $elemento) {
					$this->PostExtra($elemento);
				}
			}
		}
	}	

	function PostSingleItem() {
		$this->idprior=$_POST['Extra_' . $this->extra . '_IDFather'];
		$this->linkid=$_POST['Extra_' . $this->extra . '_IDFather']; //$_POST['System_ID'];
		$this->id=$_POST['System_ID'];
		if ($this->return=="") { $this->return=$this->folderlink . "/" . $this->post_prefix . $this->extra . "_view/prior/" . $this->idprior . "/id/" . $this->linkid; }
		$this->PostExtra($_POST);
	}
	

	function ChangeDescription($descripcion) {
		$sql="UPDATE " . $this->module . "_" . $this->table . " SET Description='" . addslashes($descripcion) . "' WHERE ID=" . $this->id;
		$ejecutar = parent::$db->Qry($sql);
		return true;
	}
	
	function ChangeLevel($rol) {
		$sql="UPDATE " . $this->module . "_" . $this->table . " SET Level=" . $rol . " WHERE ID=" . $this->id;
		$ejecutar = parent::$db->Qry($sql);
		return true;		
	}
	
	function ChangeOrder() {
		$Total=$this->GetXtraItems();
		$this->LoadTemplate('extras_' . $this->extra . '_order.tpl.php');
	}
	
	function SaveOrder() {
		$sumar=1;
		$new_order = explode(",", $_GET['elementsorder']);
		foreach ($new_order as $id=>$elemento) {
			$addorder="UPDATE " . $this->table . "_" . $this->extra . " SET Orden=" . ($id+$sumar) . " WHERE ID=" . $elemento;
			$ejecutar = parent::$db->Qry($addorder);	
		}
		echo 1;		
	}	

	function SaveOrderXtraJSON() {
		$items=json_decode($_POST['order']);
		$x=0;
		foreach ($items as $item) {
			$sql="UPDATE " . $this->table . "_" . $this->extra . " SET Orden=" . $x . " WHERE ID=" . $item->id;
			parent::$db->Qry($sql);
			$x++;
		}
	}
	
	function PutTemplate($clase,$in_block) {
	
	}	
	
	function DeleteItem($id=0) {
		$tabla =  $this->table . "_" . $this->extra;
		$Datos=$this->GetDataRecord($tabla,$id);
		//Borramos el registro de la tabla...
		$borrar= "DELETE FROM ". $tabla." WHERE ID=" . $id;
		$borrarexecute = parent::$db->Qry($borrar);
		return true;
	}
	
	function DeleteAllItems() {
		$total=$this->GetXtraItems();
		foreach ($this->Data as $idelemento=>$elemento) {
			DeleteItem($elemento['ID']);	
		}
	}	

	function GetFileName($tag,$id=0,$filename,$descripcion="") {
		$extension = preg_split("/\./", strtolower($filename)) ;
		$n = count($extension)-1;
		$extension = $extension[$n];
		$nombrearchivo=substr($filename,0,strpos($filename,$extension)-1);
		$CacheSufix="";
		if (siteImageLengthKeyCache!=0) { $CacheSufix="." . KeyGen(siteImageLengthKeyCache); }
		$nombrefinal=$this->table . "-" . $this->linkid;
		if ($id!=0) { 
			$nombrefinal.="-" .$tag . "-" . $id; 
		} else {
			//Obtenemos el prox. ID de la tabla...
			$nombrefinal.="-" .$tag . "-" . intval(parent::$db->GetDataFieldFromSQL("SELECT MAX(ID) as Total FROM " . $this->table . "_" . $this->extra,"Total")+1);
		}
		if ($descripcion=="") { $descripcion=$nombrearchivo; }
		$nombrefinal.="-" . StripFileName($descripcion,60);
		if (siteImageTag!="") { $nombrefinal.="-" . siteImageTag; }
		$nombrefinal.=$CacheSufix . "." . $extension;
		return $nombrefinal;
	}

	function PrepareView() {
		$in_block=$this->AddFormBlock('Extra');
		$this->TemplatePostScript=$this->module . "/" . $this->module . "_post/prior/" . $this->idprior;
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('Extra');
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->TemplatePostScript=$this->folderlink . "/" . $this->post_prefix . $this->extra . "_item_post/prior/" . $this->idprior;
	}

	function Run($action) {
		if ($action=="view") {
			$this->GetXtraItems();
			$this->PrepareView();
			$this->BreadCrumb['Crear']="";
			$this->LoadTemplate('extras_' . $this->extra . '_edit.tpl.php');	
		}	
		if ($action=="post") {
			if ($this->return=="") { $this->return=$_SERVER['HTTP_REFERER']; }
			if (strpos($this->return, "gotab")===false) {
				$this->return.="/gotab/" . $this->title_extra; 
			}
			$this->linkid=$_POST['Extra_' . $this->extra . '_IDFather'];
			$this->PostAllItems($this->return);
			echo("Location: " . $this->return);
		}
		if ($action=="item_post") {
			if ($this->return=="") { $this->return=$_SESSION['BckXtr']; }
			unset($_SESSION['BckXtr']);
			$this->PostSingleItem();
			header("Location: " . $this->return);
		}		
		if ($action=="delete") {
			if($this->DeleteItem($this->linkid))
			{ echo 1; } else { echo 0; }
		}
		if ($action=="saveorder") { 
			$this->SaveOrder(); 
		}

		if ($action=="saveorderjson") { 
			$this->SaveOrderXtraJSON(); 
		}

		if ($action=="change_description") {
			$this->ChangeDescription($descripcion);
		}
		if ($action=="change_level") {
			$this->ChangeLevel($rol);
		}	
		
		if ($action=="edit") {
			$this->return=$_SERVER['HTTP_REFERER'];
			$p=strpos($this->return, "gotab");
			if ($p===false) {
				$this->return.="/gotab/" . $this->title_extra; 
			} else {
				$st=substr($this->return, $p+6);
				$this->return=str_replace("/gotab/" . $st, "/gotab/" . $this->title_extra, $this->return);
			}
			$_SESSION['BckXtr']=$this->return;
			$this->LoadFormExtra();
			$this->BreadCrumb['Editar']="";
			$this->PrepareForm();
			$this->LoadTemplate('extras_' . $this->extra . '_edit.tpl.php');
		}
	}	
	

}
?>