<?php
class MasterDBase{
	
	var $ModuleVersion='2.0.0.0';
	
	//Configuración de la bd, se carga del common.php 
	var $hostname_logon = dbserver;		//Database server LOCATION
	var $database_logon = dbname;		//Database NAME
	var $username_logon = dbuser;		//Database USERNAME
	var $password_logon = dbpsw;		//Database PASSWORD
	var $last_affected_rows = 0;

	//Variable que almacena la conexion	
	public $conexion;
	
	//patrón llamado Singleton. Este patrón obliga a instanciar unicamente una vez la clase para evitar 2 o más conexiones simultáneas
	private static $_singleton;
	
	//procedimiento para instanciar la clase desde cualquier otra clase
	public static function getInstance(){
		if (is_null (self::$_singleton)) {
			self::$_singleton = new DBase();
		}
		return self::$_singleton;
	}

	//constructor que realiza la conexion
	function __construct() {
		$this->DbConnect();	
		//Set variables to control times...
		if (! isset($this->QuerysCount)) { $this->QuerysCount=0; }
		if (! isset($this->QuerysTime)) { $this->QuerysTime=0; }
	}
	
	//conecta con la base de datos
	function DbConnect(){
		$this->conexion = @mysqli_connect($this->hostname_logon, $this->username_logon, $this->password_logon) or die ('No se puede conectar a la base de datos');
		mysqli_select_db($this->conexion,$this->database_logon) or die ('No se puede seleccionar la base de datos!');
		@mysqli_query($this->conexion,"SET NAMES 'utf8'"); 	
		return;
	}
	
	//ejecuta la sentencia a la base de datos
	function Qry($query) {
		//echo $query."<br/>\r\n";
		$t_start=microtime(true);
		$result = mysqli_query($this->conexion,$query) or die(mysqli_error($this->conexion));
		$this->last_affected_rows=mysqli_affected_rows($this->conexion);
		if($result){}else{$result = "Error";}
		//echo $result."<br/>";
		$t_execution=microtime(true)-$t_start;
		//error_log(date('d/m/Y H:i:s') . " t: " . number_format($t_execution,8,",",".") . " s., SQL: " . $query .  "\r\n", 3, sitepath . "public/bdlog.txt");
		$this->QuerysTime+=$t_execution;
		$this->QuerysCount++;
		return $result;
    }
	
	//devuelve la consulta de la base de datos
	function GetDataRecordFromSQL($query) {
		$result = $this->Qry($query);
		if($result!="Error"){
			$row_registro = mysqli_fetch_assoc($result);
			$ValorDevolver=false;
			if (mysqli_num_rows($result)>0) {
				$ValorDevolver=$row_registro;   
			}
			return $ValorDevolver;	
		}else{
			return $result;
		}

    }

	//devuelve el registro consultado	
	function GetDataRecord($table,$idafectado=0) {	
		$result = $this->GetDataRecordFromSQL("SELECT * FROM " . $table." WHERE id = ".$idafectado);
		if($result !== "Error"){
			return $result;
		} else {
			return false;
		}
	}
	
	//eliminar el registro
	function Delete($Sys) {
		$result = $this->GetDataRecordFromSQL("DELETE FROM " . $Sys->table." WHERE id = ".$Sys->id);
		if($result !== "Error"){return true;}
		else{return false;}
	}
	
	//devuleve el número de registros y los almacena en $resultado
	function GetDataListFromSQL($sentencia,&$resultado){
		$registros = $this->Qry($sentencia);
		if($registros !== "Error"){
			$resultado=array();
			$row_registros = mysqli_fetch_assoc($registros);
			$totalRows_registros = mysqli_num_rows($registros); 
			do {
				$resultado[]=$row_registros;
			} while ($row_registros = mysqli_fetch_assoc($registros));
			return $totalRows_registros;
		}
		else{
			return false;	
		}
	}
	
	//devuleve el número de paginas y almacena los registros en $resultado
	function GetDataListPagedFromSQL($sentencia,$pagina,$offset,&$resultado,&$totalregistros=0) {
		$sql_count=$sentencia;
		$pos=strpos($sql_count, "ORDER BY");
		if($pos!==false) { $sql_count=substr($sql_count, 0,$pos); }
		$sql_count_enhaced=false;
		//Limpiamos subquerys...
		$sql_count_enhaced=preg_replace('/\(\s*(select.+)\) AS /i', '', $sql_count);
		//Buscamos el primer WHERE, que es el que necesitamos...
		$pos=strpos($sql_count_enhaced,"FROM");
		if ($pos!==false) {
			$sql_count_enhaced="SELECT COUNT(*) AS Total " . substr($sql_count_enhaced,$pos);
			try {
				$tmp=$this->Qry($sql_count_enhaced);
				$row=mysqli_fetch_assoc($tmp);
				if (isset($row['Total'])) { 
					$totalPaginas_registros=ceil($row['Total']/$offset);
				} else {
					$sql_count_enhaced=false;
				}
			} catch (Exception $e) {
				$sql_count_enhaced=false;
			}
		}
		if ($sql_count_enhaced===false) {
			$totalregistros = $this->Qry($sql_count);
			$totalPaginas_registros =ceil(mysqli_num_rows($totalregistros)/$offset);
		}	 
		$pagina=$pagina-1;
		$InicioCorte=$pagina * $offset;
		$sentencia.=" LIMIT " . $InicioCorte . "," . $offset;
		$registros = $this->Qry($sentencia);
		if($registros !== "Error"){
			$row_registros = mysqli_fetch_assoc($registros);
			do {
				$resultado[]=$row_registros;
			} while ($row_registros = mysqli_fetch_assoc($registros));
			return $totalPaginas_registros;
		}
		else{
			return false;	
		}
	}
	
	function InitFormData(&$Sys) {
		//Inicializa las variables para cumplimentar los <form> de la aplicación
		$sql_estructura="SHOW COLUMNS FROM " . $Sys->table;
		$estructura = $this->Qry($sql_estructura);
		if($estructura !== "Error"){

			$row_estructura = mysqli_fetch_assoc($estructura);
			$totalRows_estructura = mysqli_num_rows($estructura);
			//Escribimos la variable del sistema de acción
			$Sys->Data['Action']="new";
			do {
				$nombrecampo=$row_estructura['Field'];
				$tipocampo=$row_estructura['Type'];
				/*NUEVO*/
				//echo $nombrecampo." - ".$tipocampo." - ".$pordefecto." - ";
				$pordefecto=$row_estructura['Default'];
				if (isset($Sys->conf)) { $pordefecto=$Sys->conf->Export($nombrecampo); }
				//echo $pordefecto."<br/>";
				/*******/

				//Parcheamos el campo ID, en cualquiera de sus variantes...
				if (($nombrecampo=="ID") or ($nombrecampo=="id") or ($nombrecampo=="Id")) {
					$Sys->Data['ID']=-1;
				} else {
					//Inicializamos el valor con el valor por defecto...
					$valor=$pordefecto;
					//echo "ANTES:".$valor."<br/>";
					if ($pordefecto!="") {
						//Si es fecha...
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
					}
					//echo "DESPUES:".$valor."<br/>";
					$Sys->FieldType[$nombrecampo]=$tipocampo;
					$Sys->Data[$nombrecampo]=$valor;
				}
			} while ($row_estructura = mysqli_fetch_assoc($estructura));
		}
		else{
			return false;	
		}
	}
	
	function LoadFormData(&$Sys,$idafectado=0,$actualizar_config=1) {
		if($idafectado!=0){}else{$idafectado=$Sys->id;}
		//Carga los datos del registro en las variables para cumplimentar los <form> de la aplicación
		$sql_estructura="SHOW COLUMNS FROM " . $Sys->table;
	
		$estructura = $this->Qry($sql_estructura);
		if($estructura !== "Error"){
		
			$row_estructura = mysqli_fetch_assoc($estructura);
			$totalRows_estructura = mysqli_num_rows($estructura);
			
			$sql_datos="SELECT * FROM " . $Sys->table . " WHERE ID=" . $idafectado;
			//echo $sql_datos."<br/>";
			$datos = $this->Qry($sql_datos);
			
			if($datos !== "Error"){	
					
				$row_datos = mysqli_fetch_assoc($datos);
				$totalRows_datos = mysqli_num_rows($datos);	
				
				if ($totalRows_datos>0) {
					//Escribimos la variable del sistema de acción
					$Sys->Data['Action']="edit";
					do {
						$nombrecampo=$row_estructura['Field'];
						$tipocampo=$row_estructura['Type'];
						/*NUEVO*/
						//echo $nombrecampo." - ".$tipocampo." - ".$pordefecto." - ";
						$pordefecto=$row_estructura['Default'];
						if (isset($Sys->conf)) { $pordefecto=$Sys->conf->Export($nombrecampo); }
						//echo $pordefecto." -> ";
						/*******/
						//Parcheamos el campo ID, en cualquiera de sus variantes...
						if (($nombrecampo=="ID") or ($nombrecampo=="id") or ($nombrecampo=="Id")) {
							$Sys->Data['ID']=$idafectado;
						} else {
							$valorguardado=$row_datos[$nombrecampo];
							$valor=$pordefecto;
							if ($row_datos[$nombrecampo]!="") { $valor=$row_datos[$nombrecampo]; }
							//Adaptamos los campos fecha a la visualización europea...
							$pos=strpos($tipocampo,'date');					
							if ($pos!==false) { $valor=EuroScreenDate($valor); }
							$config = array('param' => $nombrecampo, 'value' => $valor);	
							if(($actualizar_config!=0)&&(isset($Sys->conf))){
								$Sys->conf->LoadUpdate($config);
							}
							$Sys->FieldType[$nombrecampo]=$tipocampo;
							$Sys->Data[$nombrecampo]=$valor;
						}
					} while ($row_estructura = mysqli_fetch_assoc($estructura));
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	function PostToDatabase($table,$formulario,$purgeHTML=true) {
		//Comprobamos los campos que existen en la tabla, para evitar errores
		$sql="SHOW COLUMNS FROM " . $table;
		$Total=$this->GetDataListFromSQL($sql,$allfields);
		if ($Total>0) {
			foreach($allfields as $f) {
				$available_fields[$f['Field']]=$f['Field'];
			}
		}
		//Guarda los datos recibidos en $formulario, habitualmente $_POST del <form>, en la $tabla especificada.
		$System_Action="new";
		$System_ID=-1;
		if (isset($formulario['System_Action'])) { $System_Action=$formulario['System_Action']; }
		if (isset($formulario['System_ID'])) { $System_ID=$formulario['System_ID']; }
		//Procesamos unicamente los que comienzan por "Form_"
		$num_fields=0;
		$list_fields='';
		$list_values='';
		$to_get_id='';
		foreach($formulario as $clave=>$valor) {
			$valido=strpos($clave,'Form_');
			if ($valido===0) {
				$fieldname=substr($clave,5,strlen($clave)-5);
				//Comprobamos que el campo exista...
				if (isset($available_fields[$fieldname])) {
					$num_fields++;
					//Limpiamos el valor...
					if ($purgeHTML==true) { $valor=CleanHTML($valor); }
					//Es un campo "Form_", se procesa...
					if ($System_Action=="new") {
						$list_fields.=$fieldname . ", ";
						$list_values.="'" . addslashes($valor) . "', ";
						if ($num_fields<=2) { $to_get_id.=$fieldname . "='" . addslashes($valor) . "' AND "; }
					}
					if ($System_Action=="edit") {
						$list_values.=$fieldname . "='" . addslashes($valor) . "', ";
					}
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
			if ($System_Action=="new") { $sql="INSERT INTO " . $table . " (" . $list_fields . ") VALUES (" . $list_values . ")"; }
			if ($System_Action=="edit") { $sql="UPDATE " . $table . " SET " . $list_values . " WHERE ID=" . $System_ID; }
			//Ejecutamos la sql...
			$ejecutar = $this->Qry($sql);
			//echo $ejecutar;
			if($ejecutar !== "Error"){
				//Obtenemos el IDentificador del registro guardado...
				if ($System_Action=="new") {
					$sql="SELECT * FROM " . $table . " WHERE " . $to_get_id . " ORDER BY ID DESC";	
					//Ejecutamos la sql...
					$getLast = $this->Qry($sql);
					if($getLast !== "Error"){		
						$row_getLast = mysqli_fetch_assoc($getLast);
						if (mysqli_num_rows($getLast)>0) { 
							//Validamos por si el campo está en minúsculas o mayúsculas...
							if (isset($row_getLast['id'])) { $System_ID=$row_getLast['id']; }
							if (isset($row_getLast['Id'])) { $System_ID=$row_getLast['Id']; }
							if (isset($row_getLast['ID'])) { $System_ID=$row_getLast['ID']; }
						}
					}else{
						return false;	
					}
				}
				$devolver=$System_ID;
			}else{
				return false;
			}
		}
		//Devolvemos el ID afectado...
		return $devolver;	
	}
	
	function GetDataFieldFromSQL($sentencia,$campo) {
		$registro = $this->Qry($sentencia);
		$row_registro = mysqli_fetch_assoc($registro);
		$ValorDevolver=false;
		if (mysqli_num_rows($registro)>0) {
			$ValorDevolver=$row_registro[$campo];   
		}
		return $ValorDevolver;	
	}	

	function GetDataField($tabla,$id,$campo) {
		$sentencia = "SELECT " . $campo . " FROM " . $tabla . " WHERE id = " . $id;
		return $this->GetDataFieldFromSQL($sentencia,$campo);	
	}
	
	function PopulateCombo($tabla,$campovalor,$mostrarcampo,$seleccionado,$ordenar) {
		$salida="";
		$seleccionado=explode(",",$seleccionado);
		$query_combo = "SELECT * FROM " . $tabla . " ORDER BY " . $ordenar;
		$combo = $this->Qry($query_combo);
		$row_combo = mysqli_fetch_assoc($combo);
		$totalRows_combo = mysqli_num_rows($combo);
		if ($totalRows_combo>0) {
			do {
				$ponerselected="";
				$ponervalor=$row_combo[$campovalor];
				$poneritem=$row_combo[$mostrarcampo];
				if (in_array($ponervalor,$seleccionado)) { $ponerselected=" selected"; }
				$salida.="<option" . $ponerselected . " value='" . $ponervalor . "'>" . $poneritem . "</option>";
			} while ($row_combo = mysqli_fetch_assoc($combo));
		}
		return $salida;
	}
	
	function PopulateComboLevels($seleccionado,$nivelmaximo=10) {
		$salida="";
		$query_combo = "SELECT * FROM users_roles WHERE IDRol<=" . $nivelmaximo . " ORDER BY IDRol";
		echo $querycombo;
		$combo = $this->Qry($query_combo);
		$row_combo = mysqli_fetch_assoc($combo);
		$totalRows_combo = mysqli_num_rows($combo);
		if ($totalRows_combo>0) {
			do {
				$ponerselected="";
				$ponervalor=$row_combo['IDRol'];
				$poneritem=$row_combo['RolName'];
				if ($ponervalor==$seleccionado) { $ponerselected=" selected"; }
				$salida.="<option" . $ponerselected . " value='" . $ponervalor . "'>" . $poneritem . "</option>";
			} while ($row_combo = mysqli_fetch_assoc($combo));
		}
		return $salida;
	}
	
	function PopulateComboFromSQL($sql,$campovalor,$mostrarcampo,$seleccionado) {
		$salida="";
		$query_combo = $sql;
		$combo = $this->Qry($query_combo);
		$row_combo = mysqli_fetch_assoc($combo);
		$totalRows_combo = mysqli_num_rows($combo);
		if ($totalRows_combo>0) {
			do {
				$ponerselected="";
				$ponervalor=$row_combo[$campovalor];
				$poneritem=$row_combo[$mostrarcampo];
				if ($ponervalor==$seleccionado) { $ponerselected=" selected"; }
				$salida.="<option" . $ponerselected . " value='" . urlencode($ponervalor) . "'>" . $poneritem . "</option>";
			} while ($row_combo = mysqli_fetch_assoc($combo));
		}
		return $salida;
	}	
	
	function RenameUpload($tabla,$campo,$id,$valor,$nuevovalor,$carpeta) {
		if ($valor=="") { return false; }
		if (! is_array($carpeta)) {
			$tmpvalor=$carpeta;
			unset($carpeta);
			$carpeta[]=$tmpvalor;
			unset($tmpvalor);	
		}
		$devolver=false;
		if ($nuevovalor=="") { $nuevovalor=KeyGen(4); }
		$partesvalor = preg_split("/\./", strtolower($valor)) ;
		$n = count($partesvalor);
		$extension = $partesvalor[$n-1];
		//Comprobamos que el nombre del fichero sea válido...
		$nuevovalor=stripfilename($nuevovalor);
		if (siteImageTag!="") { $nuevovalor.="-" . siteImageTag; }
		$eliminar=array("!","¡","?","¿","'","\"","$","(",")",".",":",";","_","/","\\","\$","%","@","#",",", "«", "»");
		$buscados=array(" ","á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","à","è","ì","ò","ù","À","È","Ì","Ò","Ù");
		$sustitut=array("-","a","e","i","o","u","a","e","i","o","u","n","n","u","a","e","i","o","u","A","E","I","O","U");
		$nuevovalor=strtolower(str_replace($buscados,$sustitut,str_replace($eliminar,"",$nuevovalor)));		
		$nuevovalor=str_replace("–","-",$nuevovalor);
		$nuevovalor=str_replace("–","-",$nuevovalor);	
		if (siteImageLengthKeyCache!=0) { $nuevovalor.="." . KeyGen(siteImageLengthKeyCache); }
		$renombrara=$nuevovalor . "." . $extension;
		foreach ($carpeta as $folder) {
			if ((file_exists($folder . $valor)) and (is_file($folder . $valor))) {
				if ((file_exists($folder . $renombrara)) and ($valor!=$renombrara)) { unlink($folder . $renombrara); }
				rename($folder . $valor, $folder . $renombrara);
			}
		}
		$annadir="UPDATE " . $tabla . " SET " . $campo . "= '" . $renombrara . "' WHERE id=" . $id;
		//echo $annadir;
		$actualizar = $this->Qry($annadir);
		//echo $actualizar;
		return $renombrara;
	}
	
	function UpdateCacheTag($archivo,$carpetas="",$tabla=""){
		//Generamos el nuevo nombre de archivo
		$partesvalor = preg_split("/\./", strtolower($archivo)) ;
		$n = count($partesvalor);
		if ($n>2) {
			$partesvalor[$n-2]=KeyGen(siteImageLengthKeyCache);
		} else {
			$partesvalor[$n]=$partesvalor[$n-1];
			$partesvalor[$n-1]=KeyGen(siteImageLengthKeyCache);
		}
		$nuevonombre=implode(".",$partesvalor);
		if ($carpetas=="") {
			unset($carpetas);
			$d=opendir(sitepath . "public");
			while ($elemento = readdir($d)) {
				if (($elemento!=".") and ($elemento!="..") and (is_dir(sitepath . "public/" . $elemento))) {
					if (is_file(sitepath . "public/" . $elemento . "/" . $archivo)) {
						 $carpetas[]=$elemento; 
					}
				}
			}
		}
		if (! is_array($carpetas)) {
			$tmp=$carpetas;
			unset($carpetas);
			$carpetas[]=$tmp;
		}
		//Cambiamos el nombre del archivo.
		foreach($carpetas as $elemento) {
			//echo "renombro " . sitepath . "public/" . $elemento . "/" . $archivo . " a " . sitepath . "public/" . $elemento . "/" . $nuevonombre . "<br>";
			rename(sitepath . "public/" . $elemento . "/" . $archivo,sitepath . "public/" . $elemento . "/" . $nuevonombre);	
		}
		//Actualizamos la base de datos...
		$campos_a_localizar=array("Image","Logo","File");
		$TotalTablas=$this->GetDataListFromSQL("SHOW TABLES",$ListaTablas);
		if ($TotalTablas>0) {
			foreach ($ListaTablas as $tabla) {
				unset($ListaCampos);
				$sql="SHOW COLUMNS FROM " . $tabla['Tables_in_' . $this->database_logon];
				$x=count($campos_a_localizar);
				foreach($campos_a_localizar as $i=>$itm) {
					if ($i==0) { $sql.=" WHERE "; } else { $sql.=" OR "; }
					$sql.="Field LIKE '%" . $itm . "%'";
				}
				$TotalCampos=$this->GetDataListFromSQL($sql,$ListaCampos);
				if ($TotalCampos>0) {
					foreach($ListaCampos as $campo) {
						$sql="UPDATE " . $tabla['Tables_in_' . $this->database_logon] . " SET " . $campo['Field'] . "='" . $nuevonombre . "' WHERE " . $campo['Field'] . "='" . $archivo . "'";
						$this->Qry($sql);	
					}
				}
				
			}
		}
		return $nuevonombre;
	}
	
	
	function GetLastRecord($tabla,$campo,$valor) {
	   $devolver=-1;
	   $query_getlastrecord = "SELECT * FROM " . $tabla . " WHERE " . $campo . " = '" . $valor . "' ORDER BY id DESC";
	   $getlastrecord = $class->Qry($query_getlastrecord);
	   $row_getlastrecord = mysqli_fetch_assoc($getlastrecord);
	   $totalRows_getlastrecord = mysqli_num_rows($getlastrecord);
	   if ($totalRows_getlastrecord>0) {
		   if (isset($row_getlastrecord['ID'])) { $devolver=$row_getlastrecord['ID']; }
		   if (isset($row_getlastrecord['id'])) { $devolver=$row_getlastrecord['id']; }
	   }
		return $devolver;
	}

	function GetTranslate($table,$id,$lang,&$result,$clean=false) {
		$sql="SELECT * FROM " . $table . "_translations WHERE IDOriginal=" . $id . " AND LangCode='" . $lang . "'";
		$Translate=$this->GetDataRecordFromSQL($sql);
		if ($Translate!==false) {
			foreach($Translate as $field=>$value) {
				if (($field!="ID") and ($field!="IDOriginal") and ($field!="CodeLang")) {
					$result[$field]=$value;
				}
			}
			if ($clean) {
				foreach($result as $field=>$value) {
					if (! isset($Translate[$field])) {  unset($result[$field]); }
					if (($field=="ID") or ($field=="IDOriginal") or ($field=="CodeLang")) { unset($result[$field]);}
				}
			}
			return $Translate['ID'];
		} else {
			if ($clean) {
				//Buscamos la estructura de la tabla de traducciones para borrar los campos no necesarios.
				$sql="SHOW COLUMNS FROM " . $table . "_translations";
				$TotalEstructura=$this->GetDataListFromSQL($sql,$Estructura);
				if ($TotalEstructura>0) {
					$ftraducc=array();
					foreach($Estructura as $item) {
						if (($item['Field']!="ID") and ($item['Field']!="IDOriginal") and ($item['Field']!="CodeLang")) {
							$ftraducc[$item['Field']]=$item['Field'];
						}
					}
					foreach($result as $field=>$value) {
						if (! isset($ftraducc[$field])) {  unset($result[$field]); }
					}
				}
			}
		}
		return false;
	}

	function EditTranslate($table,$id,$lang) {
		$sql="SELECT * FROM " . $table . " WHERE ID=" . $id;
		$Row=$this->GetDataRecordFromSQL($sql);
		$ActualID=$this->GetTranslate($table,$id,$lang,$Row,true);
		$Row['Action']="new";
		$Row['ID']=-1;
		$Row['IDOriginal']=$id;
		$Row_['LangCode']=$lang;
		if ($ActualID!==false) {
			$Row['Action']="edit";
			$Row['ID']=$ActualID;
		}
		return $Row;
	}

	function PostTranslate($table,$id=0,$lang="",$values="") {
		if ($values==="") { $values=$_POST; }
		if ($id==0) { $id=$values['Form_IDFather']; }
		if ($lang=="") { $lang=$values['Form_LangCode']; }
		$sql="SELECT ID FROM " . $table . "_translations WHERE IDOriginal=" . $id . " AND LangCode='" . $lang . "'";
		$Translate=$this->GetDataFieldFromSQL($sql,"ID");
		$values['System_Action']="new";
		$values['System_ID']=-1;
		$values['Form_IDOriginal']=$id;
		$values['Form_LangCode']=$lang;
		if ($Translate!==false) {
			$values['System_Action']="edit";
			$values['System_ID']=$Translate;
		}
		$this->PostToDatabase($table . "_translations", $values);
	}
	
	function GetLinkedItems($tabla_tags="aux_tags",$tabla_link,$id_link,$mostrar="Name",$json=false) {
		//Cargamos todos los elementos disponibles en la tabla de enlace...
		$sql="SELECT " . $tabla_link . ".*, " . $tabla_tags . "." . $mostrar . " AS TagValue FROM " . $tabla_link . " LEFT JOIN " . $tabla_tags . " ON " . $tabla_link . ".IDLink=" . $tabla_tags . ".ID WHERE " . $tabla_link . ".IDFather=" . $id_link;
		$total=$this->GetDataListFromSQL($sql,$resultado);
			if ($json) {
				$salida=array();
				foreach ($resultado as $tg) {
					if ($tg['TagValue']!="") {
						$salida[]=$tg['TagValue'];
					}
				}
				return json_encode($salida);
			} else {
				if ($total==0){ $resultado=array(); }
				return $resultado;
			}
	}
	
	function SetLinkedItems($tabla_tags="aux_tags",$tabla_link,$id_link,$campo_valor_tag,$Tags,$json=false) {
		if ((! is_array($Tags)) and (! $json)) { 
			if ($Tags!="") {
				$Tags=explode(",",$Tags); 
			} else {
				$Tags=null;
			}
		}
		if ($json) { $Tags=json_decode(stripslashes($Tags)); }
		print_r($Tags);
		//Sustituimos los valores de tipo texto a sus identificadores,
		//Si no existen los creamos en la bd...
		if (count($Tags)>0) {
			foreach ($Tags as $idtag=>$tag) {
				$sql="SELECT ID FROM " . $tabla_tags . " WHERE " . $campo_valor_tag . "='" . $tag . "'";
				$ActualID=$this->GetDataFieldFromSQL($sql,"ID");
				if ($ActualID===false) {
					unset($Datos);
					$Datos['System_Action']="new";
					$Datos['System_ID']=-1;	
					$Datos['Form_' . $campo_valor_tag]=$tag;
					$ActualID=$this->PostToDatabase($tabla_tags,$Datos);
				}
				$Tags[$idtag]=$ActualID; 
			}
		}
		//Obtenemos los elementos existentes ya en la BD...
		$sql="SELECT " . $tabla_link . ".*, " . $tabla_tags . "." . $campo_valor_tag . " AS TagValue FROM " . $tabla_link . " INNER JOIN " . $tabla_tags . " ON " . $tabla_link . ".IDLink=" . $tabla_tags . ".ID WHERE " . $tabla_link . ".IDFather=" . $id_link;
		$total=$this->GetDataListFromSQL($sql,$en_bd);
		//Recorremos los tags guardados que debemos salvar y borramos de ambos arrays los idénticos...
		foreach ($en_bd as $iddato=>$dato) {
			//Comprobamos si existe en la base de datos...
			$pos=false;
			if (count($Tags)>0) { $pos=array_search($dato['IDLink'],$Tags); }
			if ($pos!==false) {
				//Encontrado, lo borramos de ambos lados...	
				unset($Tags[$pos]);
				unset($en_bd[$iddato]);
			}
		}
		//Ahora procedemos a eliminar los elementos que quedan en el array en_bd pues ya no son usados...
		if (count($en_bd)>0) {
			foreach ($en_bd as $iddato=>$dato) {
				$this->Qry("DELETE FROM " . $tabla_link . " WHERE ID='" . $dato['ID'] . "'");	
			}
		}
		//Finalmente guardamos los nuevos tags...
		if (count($Tags)>0) {
			foreach ($Tags as $idtag=>$tag) {
				unset($Datos);
				$Datos['System_Action']="new";
				$Datos['System_ID']=-1;	
				$Datos['Form_IDFather']=$id_link;
				$Datos['Form_IDLink']=$tag;
				$ActualID=$this->PostToDatabase($tabla_link,$Datos);
			}
		}	
	}
	
	function DeleteLinkedItems($tabla_link="",$id_link=0) {
		$sql="DELETE FROM " . $tabla_links . " WHERE IDFather=" . $id_link;
		$this->Qry($sql);
	}	
	
	function GetLinkedTags($tabla_link="",$id_link=0,$json=false) {
		//Cargamos todos los elementos disponibles en la tabla de enlace...
		$sql="SELECT lnk_tags.*, aux_tags.Tag AS TagValue FROM lnk_tags LEFT JOIN aux_tags ON lnk_tags.IDLink=aux_tags.ID WHERE lnk_tags.TableFather='" . $tabla_link . "' AND lnk_tags.IDFather=" . $id_link;
		$total=$this->GetDataListFromSQL($sql,$resultado);
			if ($json) {
				$salida=array();
				foreach ($resultado as $tg) {
					$salida[]=$tg['TagValue'];
				}
				return json_encode($salida);
			} else {
				if ($total==0){ $resultado=array(); }
				return $resultado;
			}
	}
	
	function SetLinkedTags($tabla_link="",$id_link=0,$Tags,$json=false) {
		if ((! is_array($Tags)) and (! $json)) $Tags=explode(",",$Tags);
		if ($json) { $Tags=json_decode(stripslashes($Tags)); }
		//Sustituimos los valores de tipo texto a sus identificadores,
		//Si no existen los creamos en la bd...
		if (count($Tags)>0) {
			foreach ($Tags as $idtag=>$tag) {
				$sql="SELECT ID FROM aux_tags WHERE Tag='" . $tag . "'";
				$ActualID=$this->GetDataFieldFromSQL($sql,"ID");
				if ($ActualID===false) {
					unset($Datos);
					$Datos['System_Action']="new";
					$Datos['System_ID']=-1;	
					$Datos['Form_Tag']=$tag;
					$ActualID=$this->PostToDatabase('aux_tags',$Datos);
				}
				$Tags[$idtag]=$ActualID; 
			}
		}
		//Obtenemos los elementos existentes ya en la BD...
		$sql="SELECT lnk_tags.*, aux_tags.Tag AS TagValue FROM lnk_tags INNER JOIN aux_tags ON lnk_tags.IDLink=aux_tags.ID WHERE lnk_tags.TableFather='" . $tabla_link . "' AND lnk_tags.IDFather=" . $id_link;
		$total=$this->GetDataListFromSQL($sql,$en_bd);
		//Recorremos los tags guardados que debemos salvar y borramos de ambos arrays los idénticos...
		foreach ($en_bd as $iddato=>$dato) {
			//Comprobamos si existe en la base de datos...
			$pos=false;
			if (count($Tags)>0) { $pos=array_search($dato['IDLink'],$Tags); }
			if ($pos!==false) {
				//Encontrado, lo borramos de ambos lados...	
				unset($Tags[$pos]);
				unset($en_bd[$iddato]);
			}
		}
		//Ahora procedemos a eliminar los elementos que quedan en el array en_bd pues ya no son usados...
		if (count($en_bd)>0) {
			foreach ($en_bd as $iddato=>$dato) {
				$this->Qry("DELETE FROM lnk_tags WHERE ID='" . $dato['ID'] . "'");	
			}
		}
		//Finalmente guardamos los nuevos tags...
		if (count($Tags)>0) {
			foreach ($Tags as $idtag=>$tag) {
				unset($Datos);
				$Datos['System_Action']="new";
				$Datos['System_ID']=-1;	
				$Datos['Form_TableFather']=$tabla_link;
				$Datos['Form_IDFather']=$id_link;
				$Datos['Form_IDLink']=$tag;
				$ActualID=$this->PostToDatabase('lnk_tags',$Datos);
			}
		}	
	}
	
	function DeleteLinkedTags($tabla_link="",$id_link=0) {
		$sql="DELETE FROM lnk_tags WHERE TableFather='" . $tabla_link . "' AND lnk_tags.IDFather=" . $id_link;
		$this->Qry($sql);
	}
	
	
	function PopulateMultiSelect($MasterData,$MasterLink="IDFather",$Filter=0,$ListLink="IDLink",$ListSource,$KeyField="ID",$ListField="Name") {
		//MasterData=tabla donde se guardan los enlaces.
		//MasterLink=Campo que almacena el padre de los enlaces
		//ListLink=Campo que almacena el enlace con $ListSource.
		//Filter= Valor del padre de los enlaces
		//ListSource= Tabla del contenido enlazado
		//KeyField= Nombre del campo por el que se enlaza.
		//ListField= Campo que almacena el valor que se muestra.
		$salida="";
		$query_combo = "SELECT * FROM " . $ListSource;
		$total=$this->GetDataListFromSQL($query_combo,$Opciones);
		if ($total>0) {
			foreach ($Opciones as $opcion) {
				$ponervalor=$opcion[$KeyField];
				$poneritem=$opcion[$ListField];
				$ponerselected="";
				$sql="SELECT COUNT(ID) as Total FROM " . $MasterData . " WHERE ID>0";
				if ($MasterLink!="") { $sql.=" AND " . $MasterLink . "='" . $Filter . "'"; }
				$sql.= " AND " . $ListLink . "='" . $opcion['ID'] . "'";
				$hay=$this->GetDataFieldFromSQL($sql, "Total");
				if ($hay!=0) { $ponerselected=' selected="selected" '; }
				$salida.="<option" . $ponerselected . " value='" . $ponervalor . "'>" . $poneritem . "</option>";
			} while ($row_combo = mysqli_fetch_assoc($combo));
		}
		return $salida;	
	}

	function LoadMultiSelect($MasterData,$MasterLink="IDFather",$Filter=0,$ListLink="IDLink",$json=false) {
		$sql="SELECT " . $ListLink . " FROM " . $MasterData . " WHERE " . $MasterLink. "=" . $Filter;
		$total=$this->GetDataListFromSQL($sql,$en_bd);
		$salida=array();
		if ($total>0) {
			foreach ($en_bd as $tg) {
				$salida[]=$tg[$ListLink];
			}
		}
		if ($json) {
			return json_encode($salida,true);
		} else {
			return $salida;
		}
	}
	
	function SaveMultiSelect($MasterData,$MasterLink="IDFather",$Filter=0,$ListLink="IDLink",$Values) {
		//Obtenemos los elementos existentes ya en la BD...
		$sql="SELECT * FROM " . $MasterData . " WHERE " . $MasterLink. "=" . $Filter;
		$total=$this->GetDataListFromSQL($sql,$en_bd);
		//Recorremos los enlaces guardados que debemos salvar y borramos de ambos arrays los idénticos...
		foreach ($en_bd as $iddato=>$dato) {
			//Comprobamos si existe en la base de datos...
			$pos=false;
			if (count($Values)>0) { $pos=array_search($dato[$ListLink],$Values); }
			if ($pos!==false) {
				//Encontrado, lo borramos de ambos lados...	
				unset($Values[$pos]);
				unset($en_bd[$iddato]);
			}
		}
		//Ahora procedemos a eliminar los elementos que quedan en el array en_bd pues ya no son usados...
		if (count($en_bd)>0) {
			foreach ($en_bd as $iddato=>$dato) {
				$this->Qry("DELETE FROM " . $MasterData . " WHERE ID='" . $dato['ID'] . "'");	
			}
		}
		//Finalmente guardamos los nuevos enlaces...
		if (count($Values)>0) {
			foreach ($Values as $idvalor=>$valor) {
				unset($Datos);
				$Datos['System_Action']="new";
				$Datos['System_ID']=-1;	
				$Datos['Form_' . $MasterLink]=$Filter;
				$Datos['Form_' . $ListLink]=$valor;
				$ActualID=$this->PostToDatabase($MasterData,$Datos);
			}
		}	
	}

	function ExportToExcel($Core,$fields=true,$save="") {
		//Admite $this, como clase del sistema, que tendrá que tener cargados los items a exportar en $this->Items;
		//Y un array con los nombres de los campos a exportar...
		if ($fields===false) {
			$sql="SHOW COLUMNS FROM " . $Core->table;
			$Total=$this->GetDataListFromSQL($sql,$allfields);
			if ($Total>0) {
				foreach($allfields as $f) {
					$fields[]=$f['Field'];
				}
			}
		}
		if ($fields===true) {
			$fields=array();
			if ($Core->ItemsCount>0) {
				foreach($Core->Items[0] as $key=>$value) {
					$fields[]=$key;
				}
			}
		}
		if ((is_file(sitepath . "lib/PHPExcel/PHPExcel.php")) and (is_file(sitepath . "lib/PHPExcel/PHPExcel/Writer/Excel2007.php"))) {
			require_once(sitepath . "lib/PHPExcel/PHPExcel.php");
			require_once(sitepath . "lib/PHPExcel/PHPExcel/Writer/Excel2007.php");
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator(siteTitle);
			$objPHPExcel->getProperties()->setLastModifiedBy(siteTitle);
			$objPHPExcel->getProperties()->setTitle($Core->table);
			$objPHPExcel->setActiveSheetIndex(0);
			$row=1;
			$col=0;
			foreach($fields as $f) {
				$cell=GetColAlphabet($col) . $row;
				$objPHPExcel->getActiveSheet()->setCellValue($cell,$f);
				$objPHPExcel->getActiveSheet()->getColumnDimension(GetColAlphabet($col))->setAutoSize(true);
				$col++;
			}
			if ($Core->ItemsCount>0) {
				foreach($Core->Items as $item) {
					$col=0;
					$row++;
					foreach($fields as $f) {
						$cell=GetColAlphabet($col) . $row;
						$value=$item[$f];
						$objPHPExcel->getActiveSheet()->setCellValue($cell,$value);
						$col++;
					}
				}
			}
			$objPHPExcel->getActiveSheet()->setTitle($Core->table);
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			if ($save!="") {
				$objWriter->save($save);
			} else {
				ob_end_clean();
				header('Content-type: application/vnd.ms-excel');
				header('Content-Disposition: attachment; filename="' . $Core->table . '.xlsx"');
				$objWriter->save("php://output");
			}
			return true;
		}
		return false;
	}

	function ExportToCSV($Core,$fields=true,$save="") {
		//Admite $this, como clase del sistema, que tendrá que tener cargados los items a exportar en $this->Items;
		//Y un array con los nombres de los campos a exportar...
		$export=array();
		if ($fields===false) {
			$sql="SHOW COLUMNS FROM " . $Core->table;
			$Total=$this->GetDataListFromSQL($sql,$allfields);
			if ($Total>0) {
				foreach($allfields as $f) {
					$fields[]=$f['Field'];
				}
				$export[]=$fields;
			}
		}
		if ($fields===true) {
			$fields=array();
			if ($Core->ItemsCount>0) {
				foreach($Core->Items[0] as $key=>$value) {
					$fields[]=$key;
				}
			}
		}
		if ($Core->ItemsCount>0) {
			foreach($Core->Items as $item) {
				unset($row);
				foreach($fields as $f) {
					$row[]=stripslashes($item[$f]);
				}
				$export[]=$row;
			}
		}
		if (count($export)>0) {
			if ($save=="") { 
				header('Content-Type: text/csv; charset=utf-8');
				header('Content-Disposition: attachment; filename=' . $Core->table . '.csv');
				$save="php://output";
			}
			$output = fopen($save, 'w');
			foreach($export as $line) {
				fputcsv($output,$line);
			}
			fclose($output);
			return $save;
		}
		return false;
	}

	function ImportFromExcel($Core,$file,$default_values=false,$check_fields=false) {
		$procesados=0;
		$sinprocesar=0;
		$sql="SHOW COLUMNS FROM " . $Core->table;
		$Total=$this->GetDataListFromSQL($sql,$allfields);
		if ($Total>0) {
			foreach($allfields as $f) {
				$available_fields[$f['Field']]=$f['Field'];
			}
		}
		ini_set("display_errors", 0);
		header('Content-Type: text/html; charset=utf-8');
		if (is_file(sitepath . "lib/PHPExcel/PHPExcel/IOFactory.php")) {
			require_once(sitepath . "lib/PHPExcel/PHPExcel/IOFactory.php");
			$objPHPExcel = PHPExcel_IOFactory::load($file);
			$sheet = $objPHPExcel->getSheet(0);
			$highestRow = $sheet->getHighestRow();
			$highestColumn = $sheet->getHighestColumn();
			//Obtenemos los nombres de los campos
			$rowData = $sheet->rangeToArray('A1:' . $highestColumn . '1', NULL, TRUE, FALSE);
			foreach($rowData[0] as $k=>$v) {
				$Fields[$k+1]=$v;
			}
			//Procesamos los campos...
			for ($row = 2; $row <= $highestRow; $row++) {
			    //  Read a row of data into an array
			    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
			    unset($Procesar);
			    $Procesar['System_Action']="new";
			    $Procesar['System_ID']=-1;
			    if ($default_values!==false) {
	        		foreach($default_values as $key=>$value) {
	        			if (isset($available_fields[$key])) {
	        				$Procesar['Form_' . $key]=$value;
	        			}
	        		}
	        	}
			    foreach($rowData[0] as $k=>$v) {
			    	if (isset($available_fields[$Fields[$k+1]])) {
			    		$Procesar['Form_' . $Fields[$k+1]]=$v;
			    	}
			    }
			    $process=true;
	        	if ($check_fields!==false) {
	        		$sql="SELECT ID FROM " . $Core->table . " WHERE ID>0";
	        		foreach($check_fields as $value) {
	        			$sql.=" AND " . $value . "='" . $Procesar['Form_' . $value] . "'";
	        		}
	        		$hay=$this->GetDataFieldFromSQL($sql,"ID");
	        		if ($hay!==false) {	$process=false; }
	        	}
	        	if ($process) {
	        		$this->PostToDatabase($Core->table,$Procesar);
	        		$procesados++;
	        	} else {
	        		$sinprocesar++;
	        	}
			}
			$salida['Processed']=$procesados;
			$salida['Unprocessed']=$sinprocesar;
			return $salida;
		}
		return false;
	}

	function ImportFromCSV($Core,$file,$default_values=false,$check_fields=false) {
		$fila = 1;
		$procesados=0;
		$sinprocesar=0;
		$sql="SHOW COLUMNS FROM " . $Core->table;
		$Total=$this->GetDataListFromSQL($sql,$allfields);
		if ($Total>0) {
			foreach($allfields as $f) {
				$available_fields[$f['Field']]=$f['Field'];
			}
		}
		if (is_file($file)) {
			if (($gestor = fopen($file, "r")) !== FALSE) {
			    while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {
			        $numero = count($datos);
			        if ($fila==1) {
		        		$Cols=$datos;
			        } else {
			        	$Procesar['System_Action']="new";
			        	$Procesar['System_ID']=-1;
			        	if ($default_values!==false) {
			        		foreach($default_values as $key=>$value) {
			        			if (isset($available_fields[$key])) {
			        				$Procesar['Form_' . $key]=$value;
			        			}
			        		}
			        	}
			        	for ($c=0; $c < $numero; $c++) {
			        		if (isset($available_fields[$Cols[$c]])) {
			        			$Procesar['Form_' . $Cols[$c]]=$datos[$c];
			        		}
			        	}
			        	$process=true;
			        	if ($check_fields!==false) {
			        		$sql="SELECT ID FROM " . $Core->table . " WHERE ID>0";
			        		foreach($check_fields as $value) {
			        			$sql.=" AND " . $value . "='" . $Procesar['Form_' . $value] . "'";
			        		}
			        		$hay=$this->GetDataFieldFromSQL($sql,"ID");
			        		if ($hay!==false) {	$process=false; }
			        	}
			        	if ($process) {
			        		$this->PostToDatabase($Core->table,$Procesar);
			        		$procesados++;
			        	} else {
			        		$sinprocesar++;
			        	}
			        }
			        $fila++;
			    }
			    fclose($gestor);
			    $salida['Processed']=$procesados;
			    $salida['Unprocessed']=$sinprocesar;
			    return $salida;
			}
		}
		return false;
	}
		
	function __destruct(){
		//echo "Destruyendo DBase ". $this->conexion ."<br/>";
		@mysqli_close($this->conexion);
	}
}
?>