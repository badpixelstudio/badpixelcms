<?php
// Gestión de extra de atributos 
// Creado por Israel García Sáez para BadPixel.
// Revisión: 1.0 de 1 de Junio de 2012, por Israel Garcia.


// ****** D O C U M E N T A C I Ó N ******
//****************************************
// *** FUNCIONES DE INTEGRACIÓN...
//

class MasterExtraAttributes extends Core{
	
	//Inicializamos valores por defecto
	var $idprior=0;
	var $id = 0;
	var $module = 'cats'; 	//Establece el prefijo usado en las tablas.
	var $title_module='Catálogo';
	var $title_extra="Atributos";
	var $return = ''; 		//Define donde debe redirigir el script al finalizar los procesos, en blanco no redirije.
	var $options = array(); //Define el número de archivos que se crearán, y las opciones de cada uno de ellos.
							// []['Folder'] Carpeta donde se guardará la imagen
							// []['Subname'] particula que se añadirá al final del nombre del archivo, en blanco es ignorado.
	var $group='Atributos';
	var $linkfield = '';
	var $linkid = 0;
	var $EnableAppend = true;
	var $action="sets";
	var $Total=0;
	var $TotalFields=0;
	var $Data = array();
	
	var $SetsUsePermalink=false;
	var $SetsPermalinkFolder="";
	var $version=null;

	//constructor
	function __construct($modulo,$title='',$linkfield='IDFather', $linkid=0,$conf=NULL,$table="") {
		//objeto para acceder a la base de datos
		parent::$db = DBase::getInstance();  
				
		$this->module=$modulo;
		if ($title!="") { $this->title_module=$title; $this->title=$title; }
		$this->linkfield=$linkfield;
		$this->linkid=$linkid;
		$params=$GLOBALS['Core']->_values;
		if (count($params)>0) {
			foreach($params as $k=>$v) {
				$_GET[$k]=$v;
			}
		}
		
		if (isset($_GET['id'])) { $this->id=$_GET['id']; }
		if (isset($_GET['prior'])) { $this->idprior=$_GET['prior']; }
		if (isset($_GET['idelement'])) { $this->id=$_GET['idelement']; }
		
		$this->BreadCrumb[$this->title_module]=$this->module;
		$this->BreadCrumb['Atributos']=$this->module. "--sets";	
		parent::__construct();	
	}
	
	function InitFormData() {
		parent::$db->InitFormData($this); 
	}
	
	//recupera los valores de un usuario determinado
	function LoadFormData($id=0,$actualizar=1) {
		parent::$db->LoadFormData($this,$this->id,$actualizar=1); 
	}
	
	function GetSets($incluir_sets_base=false) {
		$sql_paginas = "SELECT * FROM " .  $this->module . "_attributes_sets";
		if (siteMulti) { 
			$sql_paginas.=" WHERE IDBusiness= " . $this->businessID; 
		if ($incluir_sets_base) { $sql_paginas.=" OR IDBusiness=0"; }
		}
		$sql_paginas.=" ORDER BY Orden";
		$this->ItemsCount=parent::$db->GetDataListFromSQL($sql_paginas,$this->Items);
		return $this->ItemsCount;
	}	
	
	function GetGroups($incluir_sets_base=false) {
		$Conjunto=parent::$db->GetDataRecord( $this->module . "_attributes_sets",$this->id);
		if (! $incluir_sets_base) { $this->CheckItemBusinessPermission($Conjunto);	}
		$sql_paginas = "SELECT * FROM " .  $this->module . "_attributes_groups WHERE IDFather=" . $this->id . " ORDER BY Orden";
		$this->ItemsCount=parent::$db->GetDataListFromSQL($sql_paginas,$this->Items);
		return $this->ItemsCount;
	}	

	function GetActualGroups() {
		$sql_paginas = "SELECT * FROM " .  $this->module . "_attributes_groups WHERE IDFather=" . $this->id . " ORDER BY Orden";
		$total=parent::$db->GetDataListFromSQL($sql_paginas,$lista);

	}
	
	function GetFields(){
		$sql_grupo = "SELECT Title,IDFather FROM " .  $this->module . "_attributes_groups WHERE ID=" . $this->id . " LIMIT 1";
		$Grupo=parent::$db->GetDataRecordFromSQL($sql_grupo);
		$this->group=$Grupo['Title'];
		$Conjunto=parent::$db->GetDataRecord( $this->module . "_attributes_sets",$Grupo['IDFather']);
		$this->CheckItemBusinessPermission($Conjunto);
		$sql_paginas = "SELECT * FROM " .  $this->module . "_attributes WHERE IDGroup=" . $this->id . " ORDER BY Orden";
		$this->ItemsCount=parent::$db->GetDataListFromSQL($sql_paginas,$this->Items);
		return $this->ItemsCount;
	}	
	
	function GetOptions(){
		$sql_paginas = "SELECT * FROM " .  $this->module . "_attributes_options WHERE IDAttribute=" . $this->id . " ORDER BY Orden";
		$this->ItemsCount=parent::$db->GetDataListFromSQL($sql_paginas,$this->Items);
	}	
	
	function GetAllAttributes() {
		$this->Attr['Total']=$this->GetGroups();
		$this->Attr['Groups']=$this->Items;
		unset($this->Items);
		//Recorremos los grupos y obtenemos sus datos...
		if ($this->ItemsCount>0) {
			foreach ($this->Attr['Groups'] as $iditem=>$item) {
				$sql="SELECT * FROM " . $this->module . "_attributes WHERE IDGroup=" . $item['ID'] . " ORDER BY Orden";
				$this->Attr['Groups'][$iditem]['Total']=parent::$db->GetDataListFromSQL($sql,$this->Attr['Groups'][$iditem]['Fields']);
				//Cargamos los valores a los campos...
				if ($this->Attr['Groups'][$iditem]['Total']>0) {
					foreach ($this->Attr['Groups'][$iditem]['Fields'] as $idfield=>$field) {
						//Obtenemos el valor...
						$sql_valor="SELECT * FROM " . $this->module . "_attributes_values WHERE IDFather=" . $this->linkid . " AND IDAttribute=" . $field['ID'];
						unset($ConjuntoDatos);
						$TotalDatos=parent::$db->GetDataListFromSQL($sql_valor, $ConjuntoDatos);
						if ($TotalDatos>0) {
							$temporal='';
							foreach ($ConjuntoDatos as $iddato=>$Datos) {
								if ($iddato!=0) { $temporal.=", "; }
								//Si el tipo de campo es ENUM o MULTI, el valor a mostrar esta en otra tabla...
								if (($field['AttributeType']=="ENUM") or ($field['AttributeType']=="MULTI")) {
									$sql_multi="SELECT Title FROM " . $this->module . "_attributes_options WHERE IDAttribute=" . $field['ID'] . " AND Value='" . $Datos['Value'] . "'";
									$nuevo_valor=parent::$db->GetDataFieldFromSQL($sql_multi,"Title");	
									if ($nuevo_valor!==false) { $temporal.=$nuevo_valor; }
								} else {
									$temporal=$Datos['Value'];
								}
								if ($temporal!="") { $this->Attr['Groups'][$iditem]['Fields'][$idfield]['Value']=$temporal; }
							}
						} else {
							if ($field['AttributeType']!="BOOLEAN") {
								unset($this->Attr['Groups'][$iditem]['Fields'][$idfield]);	
							}
						}
					}
				}
				if (count($this->Attr['Groups'][$iditem]['Fields'])==0) { unset($this->Attr['Groups'][$iditem]); }
			}
		}
		$this->Attr['Total']=count($this->Attr['Groups']);
	}
	
	function PutTemplate($clase,$in_block) {
		if ($this->ItemsCount>0) {
			foreach($this->Items as $elemento=>$datos) { 
				$sql_paginas = "SELECT * FROM " .  $this->module . "_attributes WHERE IDGroup=" . $datos['ID'] . " ORDER BY Orden";
				unset($Campos);
				$TotalCampos=parent::$db->GetDataListFromSQL($sql_paginas,$Campos);
				if ($TotalCampos>0) {
					//echo "<fieldset>";
					$clase->AddFormContent($in_block,'{"Type":"group","Text": "' . $datos['Title'] . '","FieldName": "Grp_' . $datos['ID'] . '"}');
					//echo "<h2>" . _($datos['Title']) . "</h2>";
						foreach($Campos as $campo) {
							$this->PutField($clase,$in_block,$campo);
						}
					//echo "</fieldset>";
				}
			} 
		}
	}
	
	function PostAllItems() {
		foreach($_POST as $clave=>$valor) {
			$valido=strpos($clave,'AttrType_');
			if ($valido===0) {
				//Guardamos el ID del campo...
				$campo_id=substr($clave,9,strlen($clave)-5);
				$campo_valor="";
				//Si hay algún campo "peculiar", lo parcheamos...
				if ($valor=="DATE") { PatchDate($_POST,'Attr_' . $campo_id); }
				if ($valor=="BOOLEAN") { PatchCheckBox($_POST, 'Attr_' . $campo_id); }					
				//Cargamos el valor
				if (isset($_POST['Attr_' . $campo_id])) { $campo_valor=$_POST['Attr_' . $campo_id]; }

				//Listo para guardar...
				//Vamos a buscar en la base de datos si hay algún calificador ya guardado en la BD...
				//Antes de hacer nada, si es una lista multiple, borramos TODOS los elementos guardados anteriormente...
				if ($valor=="MULTI") {
					$sql_borrado="DELETE FROM " .  $this->module . "_attributes_values WHERE " . $this->linkfield . "=" . $this->linkid . " AND IDAttribute=" . $campo_id;
					parent::$db->Qry($sql_borrado);
				}
				unset($valores);
				if (is_array($campo_valor)) { 
					$valores=$campo_valor;
				} else { 
					if (true) {$valores[]=$campo_valor; }
				}
				if (isset($valores)) {
					foreach ($valores as $item) {
						//Buscamos elementos...
						$sql_busqueda="SELECT * FROM " .  $this->module . "_attributes_values WHERE " . $this->linkfield . "=" . $this->linkid . " AND IDAttribute=" . $campo_id;
						$Elemento=parent::$db->GetDataRecordFromSQL($sql_busqueda);
						if (($Elemento===false) or ($valor=="MULTI")) {
							$sql_guardar="INSERT INTO " .  $this->module . "_attributes_values (" . $this->linkfield . ",IDAttribute,Value) VALUES (" . $this->linkid . ", " . $campo_id . ", '" . addslashes($item) . "')";
							
						} else {
							if ($item!="") {
								$sql_guardar="UPDATE " .  $this->module . "_attributes_values SET Value='" . addslashes($item) . "' WHERE ID=" . $Elemento['ID'];
							} else {
								$sql_guardar="DELETE FROM " . $this->module . "_attributes_values WHERE ID=" .  $Elemento['ID'];
							}
						}
						//Volcamos a la BD...
						//echo $sql_guardar . "<br>";
						parent::$db->Qry($sql_guardar);
					}
				}
			}
		}
	}
	
	function DeleteValues() {
		$sql_borrado="DELETE FROM " . $this->module . "_attributes_values WHERE " . $this->linkfield . "=" . $this->linkid;
		parent::$db->Qry($sql_borrado);
	}

	function PrepareSetsTableList() {
		$this->AddMainMenu('Crear',$this->module . '--sets/sets_new');
		$this->AddTableContent('Nombre','data','{{Title}}','',$this->module . '--sets/groups/id/{{ID}}');
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block,'Grupos de atributos',$this->module . '--sets/groups/id/{{ID}}');
		$this->AddTableOperations($in_block);
		$this->AddTableOperations($in_block,'Editar',$this->module . '--sets/sets_edit/id/{{ID}}');
		$this->AddTableOperations($in_block,'Eliminar',$this->module . '--sets/sets_delete/id/{{ID}}');
	}

	function PrepareGroupsTableList() {
		$this->AddMainMenu('Crear',$this->module . '--sets/groups_new/id/' . $this->id);
		$this->AddTableContent('','data','','{{Orden}}');
		$this->AddTableContent('Nombre','data','{{Title}}','',$this->module . '--sets/fields_list/id/{{ID}}');
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block,'Atributos',$this->module . '--sets/fields_list/id/{{ID}}');
		$this->AddTableOperations($in_block);
		$this->AddTableOperations($in_block,'Editar',$this->module . '--sets/groups_edit/id/{{ID}}');
		$this->AddTableOperations($in_block,'Eliminar',$this->module . '--sets/groups_delete/id/{{ID}}');
	}

	function PrepareFieldsTableList() {
		$this->AddMainMenu('Crear',$this->module . '--sets/fields_new/id/' . $this->id);
		$this->AddMainMenu();
		$this->AddMainMenu('Ordenar',$this->module . '--sets/fields_order/id/' . $this->id);
		$this->AddTableContent('','data','','{{Orden}}');
		$this->AddTableContent('Nombre','data','{{Title}}');
		$this->AddTableContent('Tipo','data','{{AttributeType}}');
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block,'Opciones',$this->module . '--sets/options_list/id/{{ID}}','("{{AttributeType}}"=="ENUM") or ("{{AttributeType}}"=="MULTI")');
		$this->AddTableOperations($in_block);
		$this->AddTableOperations($in_block,'Editar',$this->module . '--sets/fields_edit/id/{{ID}}');
		$this->AddTableOperations($in_block,'Eliminar',$this->module . '--sets/fields_delete/id/{{ID}}');
	}

	function PrepareOptionsTableList() {
		$this->AddMainMenu('Crear',$this->module . '--sets/options_new/id/' . $this->id);
		$this->AddMainMenu();
		$this->AddMainMenu('Ordenar',$this->module . '--sets/options_order/id/' . $this->id);
		$this->AddTableContent('Nombre','data','{{Title}}','',$this->module . '--sets/options_edit/id/{{ID}}');
		$this->AddTableContent('Valor','data','{{Value}}','',$this->module . '--sets/options_edit/id/{{ID}}');
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block,'Editar',$this->module . '--sets/options_edit/id/{{ID}}');
		$this->AddTableOperations($in_block,'Eliminar',$this->module . '--sets/options_delete/id/{{ID}}');
	}

	function PrepareSetsForm() {
		$in_block=$this->AddFormBlock('Conjunto de atributos');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Nombre","FieldName":"Form_Title","Value":"' . $this->Data['Title'] . '","Required": true}');
		if ($this->SetsUsePermalink) { $this->AddFormContent($in_block,'{"Type":"text","Text":"Enlace permanente","FieldName":"Permalink","Value":"' . $this->Data['Permalink'] . '", "Help": "En blanco genera la dirección URL de forma automática"}'); }
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->TemplatePostScript=$this->module . "--sets/sets_post";
	}

	function PrepareGroupsForm() {
		$in_block=$this->AddFormBlock('Grupos de atributos');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Nombre","FieldName":"Form_Title","Value":"' . $this->Data['Title'] . '","Required": true}');
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->AddFormHiddenContent("Form_IDFather",$this->Data['IDFather']);
		$this->TemplatePostScript=$this->module . "--sets/groups_post";
	}

	function PrepareFieldsForm() {
		$in_block=$this->AddFormBlock('Atributos');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Nombre","FieldName":"Form_Title","Value":"' . $this->Data['Title'] . '","Required": true}');
		$this->AddFormContent($in_block,'{"Type":"combo-json","Text":"Tipo de campo","FieldName":"Form_AttributeType","Value":"' . $this->Data['AttributeType'] . '", "JsonValues": {"STRING":"Texto","INTEGER":"Número entero", "FLOAT": "Número real", "BOOLEAN":"Lógico", "DATE":"Fecha", "ENUM":"Selector", "MULTI":"Selector múltiple", "MEMO": "Texto largo", "HTML": "Texto en HTML"}}');
		$this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Obligatorio","FieldName":"Form_Required","Value":"' . $this->Data['Required'] . '"}');
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->AddFormHiddenContent("Form_IDGroup",$this->Data['IDGroup']);
		$this->TemplatePostScript=$this->module . "--sets/fields_post";
	}

	function PrepareOptionsForm() {
		$in_block=$this->AddFormBlock('Opciones');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Nombre","FieldName":"Form_Title","Value":"' . $this->Data['Title'] . '","Required": true}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Valor","FieldName":"Form_Value","Value":"' . $this->Data['Value'] . '","Required": true}');
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->AddFormHiddenContent("Form_IDAttribute",$this->Data['IDAttribute']);
		$this->TemplatePostScript=$this->module . "--sets/options_post";
	}
	
	function Run($action) {
		if (($action=="list") or ($action=="sets")) {
			$this->title="Conjunto de Atributos";
			$this->GetSets();
			$this->PrepareSetsTableList();
			$this->LoadTemplate('list.tpl.php');	
		}	
		if ($action=="sets_new") {
			$this->table=$this->module . "_attributes_sets";	
			$this->InitFormData();
			$this->Data['Permalink']="";
			$this->BreadCrumb['Crear']="";
			$this->title="Crear Conjunto de Atributos";
			$this->PrepareSetsForm();
			$this->LoadTemplate('edit.tpl.php');			
		}
		if ($action=="sets_edit") {
			$this->table=$this->module . "_attributes_sets";	
			$this->LoadFormData($this,$this->id);
			$this->Data['Permalink']=$this->GetPermalink();
			$this->CheckItemBusinessPermission($this->Data);
			$this->BreadCrumb['Crear']="";
			$this->title="Editar Conjunto de Atributos";
			$this->PrepareSetsForm();
			$this->LoadTemplate('edit.tpl.php');
		}
		if ($action=="sets_post") {
			$this->table=$this->module . "_attributes_sets";
			if (siteMulti) { $_POST['Form_IDBusiness']=$this->businessID; }
			$ActualID=parent::PostToDatabase($this->table,$_POST);
			if ($this->SetsUsePermalink) {
				$permalink=$this->SetPermalink($_POST['Permalink'],$this->table,$ActualID,'','action=list',$this->SetsPermalinkFolder);	
			}
			header("Location: " . $this->module . "--sets/attributes_sets");
		}
		if ($action=="sets_delete") {
			//OJO!!! Editar para que borre todos los grupos...
			//Borramos todos los elementos existentes...
			$sql="SELECT * FROM " . $this->module . "_attributes_groups WHERE IDFather=" . $this->id;
			$TotalGrupos=parent::$db->GetDataListFromSQL($sql,$Grupos);
			if ($TotalGrupos>0) {
				foreach ($Grupos as $grupo) {
					$sql="SELECT * FROM " . $this->module . "_attributes WHERE IDGroup=" . $grupo['ID'];
					$TotalCampos=parent::$db->GetDataListFromSQL($sql,$Campos);
					 if ($TotalCampos>0) {
						  foreach($Campos as $elemento=>$datos) {
							  $sql="DELETE FROM " .  $this->module . "_attributes_options WHERE IDAttribute=" . $datos['ID'];
							  parent::Qry($sql);
							  $sql="DELETE FROM " .  $this->module . "_attributes_values WHERE IDAttribute=" . $datos['ID'];
							  parent::Qry($sql);
						  }
						$sql="DELETE FROM " .  $this->module . "_attributes WHERE IDGroup=" . $this->id;
						parent::Qry($sql);	  
					 }
				}
			}
			$sql="DELETE FROM " .  $this->module . "_attributes_sets WHERE ID=" . $this->id;
			parent::Qry($sql);					 
			echo "1";
		}			
		if ($action=="groups") {
			//Obtenemos el nombre de la categoria...
			$SetInfo=parent::$db->GetDataFieldFromSQL("SELECT Title FROM " .   $this->module . "_attributes_sets WHERE ID=" . $this->id,"Title");
			$this->title="Grupos en " . $SetInfo;
			$this->BreadCrumb[$SetInfo]=$this->module . "--sets/groups/id/" . $this->id;
			$this->BreadCrumb['Grupos']=$this->module . "--sets/groups/id/" . $this->id;
			$this->GetGroups();
			$this->PrepareGroupsTableList();
			$this->LoadTemplate('list.tpl.php');	
		}
		if ($action=="groups_new") {
			$Conjunto=parent::$db->GetDataRecordFromSQL("SELECT Title,IDBusiness FROM " .   $this->module . "_attributes_sets WHERE ID=" . $this->id);
			$this->title="Crear Grupo en " . $Conjunto['Title'];
			$this->CheckItemBusinessPermission($Conjunto);
			$SetInfo=$Conjunto['Title'];
			$this->BreadCrumb[$SetInfo]=$this->module . "--sets/groups/id/" . $this->id;
			$this->BreadCrumb['Grupos']=$this->module . "--sets/groups/id/" . $this->id;
			$this->BreadCrumb['Crear']=$this->module . "--sets/groups_new/id/" . $this->id;;
			$this->table=$this->module . "_attributes_groups";	
			$this->InitFormData();
			$this->Data['IDFather']=$this->id;
			$this->PrepareGroupsForm();
			$this->LoadTemplate('edit.tpl.php');			
		}
		if ($action=="groups_edit") {
			$this->table=$this->module . "_attributes_groups";	
			$this->LoadFormData($this,$this->id);
			$Conjunto=parent::$db->GetDataRecordFromSQL("SELECT Title,IDBusiness FROM " .   $this->module . "_attributes_sets WHERE ID=" . $this->Data['IDFather']);
			$this->title="Editar Grupo en " . $Conjunto['Title'];
			$this->CheckItemBusinessPermission($Conjunto);
			$SetInfo=$Conjunto['Title'];
			$this->BreadCrumb[$SetInfo]=$this->module . "--sets/groups/id/" . $this->Data['IDFather'];
			$this->BreadCrumb['Grupos']=$this->module . "--sets/groups/id/" . $this->Data['IDFather'];
			$this->BreadCrumb['Editar']=$this->module . "--sets/groups_edit/id/" . $this->id;		
			$this->PrepareGroupsForm();	
			$this->LoadTemplate('edit.tpl.php');
		}
		if ($action=="groups_post") {
			$this->table=$this->module . "_attributes_groups";
			parent::PostToDatabase($this->table,$_POST);
			header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "--sets/groups/id/" . $_POST['Form_IDFather']);
		}
		if ($action=="groups_delete") {
			//Borramos todos los elementos existentes...
			 $this->GetFields();
			 if ($this->Total>0) {
				  foreach($this->Data as $elemento=>$datos) {
					  $sql="DELETE FROM " . $this->module . "_attributes_options WHERE IDAttribute=" . $datos['ID'];
					  parent::Qry($sql);
				  }
				$sql="DELETE FROM " . $this->module . "_attributes WHERE IDGroup=" . $this->id;
				parent::Qry($sql);	  
			 }
			$sql="DELETE FROM " . $this->module . "_attributes_groups WHERE ID=" . $this->id;
			parent::Qry($sql);					 
			echo "1";
		}
		
		if ($action=="fields_list") {
			$SetValues=parent::$db->GetDataRecordFromSQL("SELECT " .   $this->module . "_attributes_groups.Title as GroupTitle, " .   $this->module . "_attributes_sets.Title as SetTitle, " .  $this->module . "_attributes_sets.IDBusiness as IDBusiness, " .   $this->module . "_attributes_sets.ID as SetID FROM " .   $this->module . "_attributes_groups INNER JOIN " .   $this->module . "_attributes_sets ON " .   $this->module . "_attributes_groups.IDFather=" .   $this->module . "_attributes_sets.ID WHERE " .  $this->module . "_attributes_groups.ID=" . $this->id);
			$this->CheckItemBusinessPermission($SetValues);
			$this->BreadCrumb[$SetValues['SetTitle']]=$this->module . "--sets/sets/id/" . $SetValues['SetID'];
			$this->BreadCrumb['Grupos']=$this->module . "--sets/groups/id/" . $this->id;
			$this->BreadCrumb[$SetValues['GroupTitle']]=$this->module . "--sets/fields_list/id/" . $this->id;
			$this->BreadCrumb['Campos']=$this->module . "--sets/fields_list/id/" . $this->id;
			$this->title="Campos del grupo " . $SetValues['GroupTitle'] . " en " . $SetValues['SetTitle'];
			$this->GetFields();
			$this->PrepareFieldsTableList();
			$this->LoadTemplate('list.tpl.php');			
		}
		if ($action=="fields_new") {
			$this->table=$this->module . "_attributes";	
			$this->InitFormData();
			$SetValues=parent::$db->GetDataRecordFromSQL("SELECT " .   $this->module . "_attributes_groups.Title as GroupTitle, " .   $this->module . "_attributes_sets.Title as SetTitle, " .  $this->module . "_attributes_sets.IDBusiness as IDBusiness, " .   $this->module . "_attributes_sets.ID as SetID FROM " .   $this->module . "_attributes_groups INNER JOIN " .   $this->module . "_attributes_sets ON " .   $this->module . "_attributes_groups.IDFather=" .   $this->module . "_attributes_sets.ID WHERE " .   $this->module . "_attributes_groups.ID=" . $this->id);
			$this->CheckItemBusinessPermission($SetValues);
			$this->BreadCrumb[$SetValues['SetTitle']]=$this->module . "--sets/sets/id/" . $SetValues['SetID'];
			$this->BreadCrumb['Grupos']=$this->module . "--sets/groups/id/" . $this->id;
			$this->BreadCrumb[$SetValues['GroupTitle']]=$this->module . "--sets/fields_list/id/" . $this->id;
			$this->BreadCrumb['Campos']=$this->module . "--sets/fields_list/id/" . $this->id;
			$this->BreadCrumb['Crear']=$this->module . "--sets/fields_new/id/" . $this->id;
			$this->title="Crear Campo en el grupo " . $SetValues['GroupTitle'] . " en " . $SetValues['SetTitle'];
			$this->Data['IDGroup']=$this->id;
			$this->PrepareFieldsForm();
			$this->LoadTemplate('edit.tpl.php');				
		}
		if ($action=="fields_edit") {	
			$this->table=$this->module . "_attributes";	
			$this->LoadFormData($this,$this->id);
			$SetValues=parent::$db->GetDataRecordFromSQL("SELECT " .   $this->module . "_attributes_groups.Title as GroupTitle, " .   $this->module . "_attributes_sets.Title as SetTitle, " .  $this->module . "_attributes_sets.IDBusiness as IDBusiness, " .   $this->module . "_attributes_sets.ID as SetID FROM " .   $this->module . "_attributes_groups INNER JOIN " .   $this->module . "_attributes_sets ON " .   $this->module . "_attributes_groups.IDFather=" .   $this->module . "_attributes_sets.ID WHERE " .   $this->module . "_attributes_groups.ID=" . $this->Data['IDGroup']);
			$this->CheckItemBusinessPermission($SetValues);
			$this->BreadCrumb[$SetValues['SetTitle']]=$this->module . "--sets/sets/id/" . $SetValues['SetID'];
			$this->BreadCrumb['Grupos']=$this->module . "--sets/groups/id/" . $this->Data['IDGroup'];
			$this->BreadCrumb[$SetValues['GroupTitle']]=$this->module . "--sets/fields_list/id/" . $this->Data['IDGroup'];
			$this->BreadCrumb['Campos']=$this->module . "--sets/fields_list/id/" . $this->Data['ID'];
			$this->BreadCrumb['Editar']=$this->module . "--sets/fields_new/id/" . $this->id;	
			$this->title="Editar Campo en el grupo " . $SetValues['GroupTitle'] . " en " . $SetValues['SetTitle'];
			$this->PrepareFieldsForm();		
			$this->LoadTemplate('extras_attributes_edit.tpl.php');		
		}
		if ($action=="fields_post") {	
			$this->table=$this->module . "_attributes";
			parent::PostToDatabase($this->table,$_POST);
			header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "--sets/fields_list/id/" . $_POST['Form_IDGroup']);
		}
		if ($action=="fields_delete") {
			$sql="DELETE FROM " .   $this->module . "_attributes_options WHERE IDAttribute=" . $this->id;
			parent::Qry($sql);			
			$sql="DELETE FROM " .   $this->module . "_attributes WHERE ID=" . $this->id;
			parent::Qry($sql);
			echo "1";			
		}
		if ($action=="fields_order") {
			$this->GetFields();
			$salida=array();
			if ($this->ItemsCount>0) {
				foreach($this->Items as $item) {
					unset($block);
					$block['id']=$item['ID'];
					$block['title']=$item["Title"];
					$block['items']=array();
					$salida[]=$block;
				}
				$this->Items=$salida;
			}
			$this->script=$_SERVER['REQUEST_URI'];
			$this->script=str_replace("_order", "_saveorder", $this->script);
			$this->LoadTemplate('extras_attributes_order.tpl.php');			
		}		
		if ($action=="fields_saveorder") {
			$items=json_decode($_POST['order']);
			$x=1;
			foreach ($items as $item) {
				$addorder="UPDATE " .   $this->module . "_attributes SET Orden=" . $x . " WHERE ID=" . $item->id;
				$ejecutar = parent::$db->Qry($addorder);	
				$x++;
			}
			echo 1;			
		}		
		
		if ($action=="options_list") {
			$SetField=parent::$db->GetDataRecordFromSQL("SELECT * FROM " . $this->module . "_attributes WHERE ID=" . $this->id);	
			$SetValues=parent::$db->GetDataRecordFromSQL("SELECT " . $this->module . "_attributes_groups.Title as GroupTitle, " . $this->module . "_attributes_groups.ID as GroupID, " . $this->module . "_attributes_sets.Title as SetTitle, " .  $this->module . "_attributes_sets.IDBusiness as IDBusiness, " .   $this->module . "_attributes_sets.ID as SetID FROM " .   $this->module . "_attributes_groups INNER JOIN " .   $this->module . "_attributes_sets ON " .   $this->module . "_attributes_groups.IDFather=" .   $this->module . "_attributes_sets.ID WHERE " .   $this->module . "_attributes_groups.ID=" . $SetField['IDGroup']);
			$this->CheckItemBusinessPermission($SetValues);
			$this->BreadCrumb[$SetValues['SetTitle']]=$this->module . "--sets/sets/id/" . $SetValues['SetID'];
			$this->BreadCrumb['Grupos']=$this->module . "--sets/groups/id/" . $SetValues['SetID'];
			$this->BreadCrumb[$SetValues['GroupTitle']]=$this->module . "--sets/fields_list/id/" . $SetValues['GroupID'];
			$this->BreadCrumb['Campos']=$this->module . "--sets/fields_list/id/" . $SetField['IDGroup'];
			$this->BreadCrumb[$SetField['Title']]=$this->module . "--sets/options_list/id/" . $this->id;
			$this->BreadCrumb['Opciones']=$this->module . "--sets/options_list/id/" . $this->id;
			$this->title="Opciones de " . $SetField['Title'] . " en grupo " . $SetValues['GroupTitle'] . " en " . $SetValues['SetTitle'];	
			$this->GetOptions();
			$this->PrepareOptionsTableList();
			$this->LoadTemplate('list.tpl.php');
		}
		if ($action=="options_new") {	
			$this->table=$this->module . "_attributes_options";	
			$this->InitFormData();
			$this->Data['IDAttribute']=$this->id;
			$SetField=parent::$db->GetDataRecordFromSQL("SELECT * FROM " . $this->module . "_attributes WHERE ID=" . $this->id);	
			$SetValues=parent::$db->GetDataRecordFromSQL("SELECT " . $this->module . "_attributes_groups.Title as GroupTitle, " . $this->module . "_attributes_groups.ID as GroupID, " . $this->module . "_attributes_sets.Title as SetTitle, " .  $this->module . "_attributes_sets.IDBusiness as IDBusiness, " .   $this->module . "_attributes_sets.ID as SetID FROM " .   $this->module . "_attributes_groups INNER JOIN " .   $this->module . "_attributes_sets ON " .   $this->module . "_attributes_groups.IDFather=" .   $this->module . "_attributes_sets.ID WHERE " .   $this->module . "_attributes_groups.ID=" . $SetField['IDGroup']);
			$this->CheckItemBusinessPermission($SetValues);
			$this->BreadCrumb[$SetValues['SetTitle']]=$this->module . "--sets/sets/id/" . $SetValues['SetID'];
			$this->BreadCrumb['Grupos']=$this->module . "--sets/groups/id/" . $SetValues['SetID'];
			$this->BreadCrumb[$SetValues['GroupTitle']]=$this->module . "--sets/fields_list/id/" . $SetValues['GroupID'];
			$this->BreadCrumb['Campos']=$this->module . "--sets/fields_list/id/" . $SetField['IDGroup'];
			$this->BreadCrumb[$SetField['Title']]=$this->module . "--sets/options_list/id/" . $this->id;
			$this->BreadCrumb['Opciones']=$this->module . "--sets/options_list/id/" . $this->id;
			$this->BreadCrumb['Crear']=$this->module . "--sets/options_new/id/" . $this->id;
			$this->title="Crear Opción de " . $SetField['Title'] . " en grupo " . $SetValues['GroupTitle'] . " en " . $SetValues['SetTitle'];
			$this->PrepareOptionsForm();
			$this->LoadTemplate('edit.tpl.php');			
		}
		if ($action=="options_edit") {
			$this->table=$this->module . "_attributes_options";	
			$this->LoadFormData($this,$this->id);
			$SetField=parent::$db->GetDataRecordFromSQL("SELECT * FROM " . $this->module . "_attributes WHERE ID=" . $this->Data['IDAttribute']);	
			$SetValues=parent::$db->GetDataRecordFromSQL("SELECT " . $this->module . "_attributes_groups.Title as GroupTitle, " . $this->module . "_attributes_groups.ID as GroupID, " . $this->module . "_attributes_sets.Title as SetTitle, " .  $this->module . "_attributes_sets.IDBusiness as IDBusiness, " .   $this->module . "_attributes_sets.ID as SetID FROM " .   $this->module . "_attributes_groups INNER JOIN " .   $this->module . "_attributes_sets ON " .   $this->module . "_attributes_groups.IDFather=" .   $this->module . "_attributes_sets.ID WHERE " .   $this->module . "_attributes_groups.ID=" . $SetField['IDGroup']);
			$this->CheckItemBusinessPermission($SetValues);
			$this->BreadCrumb[$SetValues['SetTitle']]=$this->module . "--sets/sets/id/" . $SetValues['SetID'];
			$this->BreadCrumb['Grupos']=$this->module . "--sets/groups/id/" . $SetValues['SetID'];
			$this->BreadCrumb[$SetValues['GroupTitle']]=$this->module . "--sets/fields_list/id/" . $SetValues['GroupID'];
			$this->BreadCrumb['Campos']=$this->module . "--sets/fields_list/id/" . $SetField['IDGroup'];
			$this->BreadCrumb[$SetField['Title']]=$this->module . "--sets/options_list/id/" . $this->id;
			$this->BreadCrumb['Opciones']=$this->module . "--sets/options_list/id/" . $this->id;
			$this->BreadCrumb['Editar']=$this->module . "--sets/options_edit/id/" . $this->id;	
			$this->title="Editar Opción de " . $SetField['Title'] . " en grupo " . $SetValues['GroupTitle'] . " en " . $SetValues['SetTitle'];	
			$this->PrepareOptionsForm();	
			$this->LoadTemplate('edit.tpl.php');	
		}
		if ($action=="options_post") {
			$this->table=$this->module . "_attributes_options";
			parent::PostToDatabase($this->table,$_POST);
			header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "--sets/options_list/id/" . $_POST['Form_IDAttribute']);		
		}
		if ($action=="options_delete") {
			$sql="DELETE FROM " .   $this->module . "_attributes_options WHERE ID=" . $this->id;
			parent::Qry($sql);
			echo "1";		
		}
		if ($action=="options_order") {
			$this->GetOptions();
			$salida=array();
			if ($this->ItemsCount>0) {
				foreach($this->Items as $item) {
					unset($block);
					$block['id']=$item['ID'];
					$block['title']=$item["Title"];
					$block['items']=array();
					$salida[]=$block;
				}
				$this->Items=$salida;
			}
			$this->script=$_SERVER['REQUEST_URI'];
			$this->script=str_replace("_order", "_saveorder", $this->script);
			$this->LoadTemplate('extras_attributes_order.tpl.php');			
		}		
		if ($action=="options_saveorder") {
			$items=json_decode($_POST['order']);
			$x=1;
			foreach ($items as $item) {
				$addorder="UPDATE " .   $this->module . "_attributes_options SET Orden=" . $x . " WHERE ID=" . $item->id;
				$ejecutar = parent::$db->Qry($addorder);
				$x++;	
			}
			echo 1;
		}
		
	}
	
//FUNCIONES INTERNAS	

	function PutField($clase,$in_block,$atributo) {
		//Buscamos el valor actual...
		$sql_paginas = "SELECT * FROM " . $this->module . "_attributes_values WHERE " . $this->linkfield . "=" . $this->linkid . " AND IDAttribute=" . $atributo['ID'];
		$TotalValores=parent::$db->GetDataListFromSQL($sql_paginas,$Valores);
		if ($atributo['AttributeType']!="MULTI"){
			if ($TotalValores==0) { $Valores=""; }
			if ($TotalValores==1) { $Valores=$Valores[0]['Value']; }
		} 		
		//Marcamos si es requerido o no...
		$requerido="false";
		if ($atributo['Required']==1) { $requerido="true"; }
		//Creamos el campo...
		//echo "<section><label for='Attr_" . $atributo['ID'] . "'>" . $atributo['Title'] . "</label>";
		//echo "<div>";
		switch($atributo['AttributeType']){
			case 'STRING': $clase->AddFormContent($in_block,'{"Type":"text","Text": "'. $atributo['Title'] . '","FieldName": "Attr_' . $atributo['ID'] .'","Value": "' . $Valores . '", "Required": ' . $requerido . '}');break;
			case 'INTEGER': $clase->AddFormContent($in_block,'{"Type":"number","Text": "'. $atributo['Title'] . '","FieldName": "Attr_' . $atributo['ID'] .'","Value": "' . $Valores . '", "Required": ' . $requerido . '}');	break;	
			case 'FLOAT': $clase->AddFormContent($in_block,'{"Type":"number","Text": "'. $atributo['Title'] . '","FieldName": "Attr_' . $atributo['ID'] .'","Value": "' . $Valores . '", "Required": ' . $requerido . '}');break;						
			case 'BOOLEAN': $clase->AddFormContent($in_block,'{"Type":"checkbox","Text": "'. $atributo['Title'] . '","FieldName": "Attr_' . $atributo['ID'] .'","Value": "' . $Valores . '", "Required": ' . $requerido . '}');break;			
			case 'DATE': $clase->AddFormContent($in_block,'{"Type":"date","Text": "'. $atributo['Title'] . '","FieldName": "Attr_' . $atributo['ID'] .'","Value": "' . $Valores . '", "Required": ' . $requerido . '}');break;			
			case 'MEMO': $clase->AddFormContent($in_block,'{"Type":"textaera","Text": "'. $atributo['Title'] . '","FieldName": "Attr_' . $atributo['ID'] .'","Value": "' . $Valores . '", "Required": ' . $requerido . '}');break;
			case 'HTML': $clase->AddFormContent($in_block,'{"Type":"html","Text": "'. $atributo['Title'] . '","FieldName": "Attr_' . $atributo['ID'] .'","Value": "' . $Valores . '", "Required": ' . $requerido . '}');break;	
			case 'ENUM': 
				//Cargamos los elementos disponibles en la lista de opciones...
				$sql_opciones = "SELECT * FROM " . $this->module . "_attributes_options WHERE IDAttribute=" . $atributo['ID'] . " ORDER BY Orden";
				$TotalOpciones=parent::$db->GetDataListFromSQL($sql_opciones,$Opciones);
				$json=array();

				if ($TotalOpciones>0) {
					foreach ($Opciones as $opcion) {
						$json[$opcion['Value']]=$opcion['Title'];
					}
				}
				$clase->AddFormContent($in_block,'{"Type":"combo-json","Text": "'. $atributo['Title'] . '","FieldName": "Attr_' . $atributo['ID'] .'","Value": "' . $Valores . '", "JsonValues": ' . json_encode($json) . ',"Required": ' . $requerido . '}');
        		break;					
			case 'MULTI': 
				$sql_opciones = "SELECT * FROM " . $this->module . "_attributes_options WHERE IDAttribute=" . $atributo['ID'] . " ORDER BY Orden";
				$TotalOpciones=parent::$db->GetDataListFromSQL($sql_opciones,$Opciones);
				$json=array();
				if ($TotalOpciones>0) {
					foreach ($Opciones as $opcion) {
						$json[$opcion['Value']]=$opcion['Title'];
					}
				}
				$selec=array();
				foreach($Valores as $valor) {
					$selec[]=$valor['Value'];
				}
				$Valores=implode(",",$selec);
				$clase->AddFormContent($in_block,'{"Type":"combo-multiple-json","Text": "'. $atributo['Title'] . '","FieldName": "Attr_' . $atributo['ID'] .'","Value": "' . $Valores . '", "JsonValues": ' . json_encode($json) . ',"Required": ' . $requerido . '}');
				break;
		}
		$clase->AddFormHiddenContent("AttrType_" . $atributo['ID'],$atributo['AttributeType']);
	}
	
	




	function __destruct(){
		//echo "Destruyendo CatPage<br/>";
	}

}
?>