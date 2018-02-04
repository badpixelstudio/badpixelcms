<?php
require_once(sitepath . "include/core/database.class.php");

class MasterConfigModule {

	var $ModuleVersion='3.0.0.0';
	
	//other class
	private static $db = '';

	function __construct($empresa){
	   	//objeto para acceder a la base de datos
		self::$db = DBase::getInstance(); 
		if (defined("siteMultiConfig")) {
			if (! siteMultiConfig) { $empresa=0; } 
		}
		$this->InitConf($empresa);
	} 

	//Carga la config desde xxx.config.php, luego por defecto para todas las empresas y si existe, la de la empresa seleccionada.
	//Si no existen datos de config en la BD, lo genera desde los parámetros por defecto.
	function InitConf($empresa=0) {
		if (defined("siteMultiConfig")) {
			if (! siteMultiConfig) { $empresa=0; } 
		}
		$this->defaultConf=$this->ExportToJson();
		//Guardamos la configuración por defecto del módulo
		if (count($this->columns)>0) {
			$Confs=$this->LoadFromJSON(0);
			//Actualizamos la configuración que se usará...
			foreach ($this->columns as $param=>$cfg) {
				if (isset($Confs[$param])) {
					$config = array('param' => $param, 'value' => $Confs[$param]);
					$this->Load(0,$config);
				}
			}

			//Exportamos la configuración a JSON
			$NewConfs=$this->ExportToJson();
			if ($Confs!=$NewConfs) {$this->SaveCurrentConfig(0,$NewConfs);}
		}
		//Si estamos ejecutando la webapp como una entidad, cargamos la configuración de esa empresa
		if ($empresa!=0) {
			$BusinessConfs=$this->LoadFromJSON($empresa);
			//Actualizamos la configuración que se usará...
			foreach ($this->columns as $param=>$cfg) {
				if (isset($BusinessConfs[$param])) {
					$config = array('param' => $param, 'value' => $Confs[$param]);
					$this->Load(0,$config);
				}
			}
			//Exportamos la configuración a JSON
			$NewConfs=$this->ExportToJson();
			if ($BusinessConfs!=$NewConfs) { $this->SaveCurrentConfig($empresa,$NewConfs);}
		}
	}
	
	//Sobreescribe la configuración del módulo en la tabla existen campos con igual nombre que los parámetros
	//Si la tabla dispone del campo "UseConfigDefault", ignora los parámetros de la tabla.
	function LoadActualConfig($table="",$id=0) {
		if (($table!="") and ($id!=0)) {
			$Categoria=self::$db->GetDataRecord($table,$id);
			if (! $this->Check('UseConfigDefault')){
				//Recorremos los ajustes actuales y vemos cuales hay que actualizar...
				foreach ($this->columns as $config) {
					$parametro=$config->param;
					//Buscamos el valor en la categoria...
					if (isset($Categoria[$parametro])) {
						$this->columns[$parametro]->value=$Categoria[$parametro];
					}
				}
				return true;
			}
		} else {
			return false;
		}
	}	
	
	//Carga la configuración por defecto para todos los parámetros de la base de datos.
	function ReloadConfig($idbusiness,$redirect=false){
		$this->SaveCurrentConfig($idbusiness,$this->defaultConf);
		if ($redirect) {
			header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/config/list/text/" . urlencode(base64_encode(_("Configuración del módulo actualizada correctamente"))));
		}
	}

	function CreateTempConf($param,$type="STRING",$value="") {
		$this->columns[$param]= new Config($param,$type,$value);
		$this->columns[$param]->istemp=true;
	}
	  
	//Modifica el valor de un parámetro
   	function Load($idbussines,$arg){
		if (isset($this->columns[$arg['param']])) {
			$this->columns[$arg['param']]->Modif($arg);
		}
   	}
   
   	//Actualiza el valor de un parámetro, dando prioridad al valor menor
   	function LoadUpdate($arg){
		if(isset($this->columns[$arg['param']])){
			$this->columns[$arg['param']]->Update($arg);
		}
   	}
	
	//Devuelve el valor actual de un parámetro determinado
	function Export($arg){
		if(isset($this->columns[$arg])){
			return $this->columns[$arg]->value;
		}
   	}
	
	//Comprueba si está habilitado un parámetro determinado
	function Check($param){
		$devolver=false;
		if (isset($this->columns[$param])) {
			if(($this->columns[$param]->param==$param) and ($this->columns[$param]->value==1)) { $devolver=true; }
		}
        return $devolver;
   	}
   
   	//Comprueba el nivel de usuario para un parámetro determinado
   	function CheckRol($param,$rol,$arg=0){
		if($this->columns[$param]->param==$param) {
        	if (($this->columns[$param]->value<=$rol) and ($this->columns[$param]->value!=-1)) {
				if ($arg<=$rol)	{ return true; }
            }
		}
        return false;
   	}
	
	//Devuelve en un único array todos los valores actualmente configurados...
	function GetActualConfig() {
		foreach ($this->columns as $key=>$value) {
			$salida[$key]=$value->value;
		}
		return $salida;
	}

	//Actualiza los Permalinks de todos los modulos y los del Menú (si existen las tablas)
	function UpdatePermalinks($module="",$antiguo,$nuevo) {
		if ($module=="") { $module=$this->name; }
		$total=self::$db->GetDataListFromSQL("SHOW TABLES LIKE 'permalinks'",$ignorar);
		if ($total>0) {
			$sql="SELECT * FROM permalinks WHERE ModuleName='" . $module . "'";
			unset($Items);
			$ItemsCount=self::$db->GetDataListFromSQL($sql,$Items);
			foreach($Items as $itm) {
				if (strpos($itm['Permalink'], $antiguo)!==false) {
					unset($Update);
					$Update['System_Action']="edit";
					$Update['System_ID']=$itm['ID'];
					$Update['Form_Permalink']=str_replace($antiguo, $nuevo, $itm['Permalink']);
					self::$db->PostToDatabase('permalinks',$Update);
				}
			}
		}
		$total=self::$db->GetDataListFromSQL("SHOW TABLES LIKE 'menu'",$ignorar);
		if ($total>0) {
			$sql="SELECT * FROM menu";
			unset($Items);
			$ItemsCount=self::$db->GetDataListFromSQL($sql,$Items);
			foreach($Items as $itm) {
				if (strpos($itm['Link'], $antiguo)!==false) {
					unset($Update);
					$Update['System_Action']="edit";
					$Update['System_ID']=$itm['ID'];
					$Update['Form_Link']=str_replace($antiguo, $nuevo, $itm['Link']);
					self::$db->PostToDatabase('menu',$Update);
				}
			}
		}
	}

	//Guarda toda la configuración en la BD. Se usa en config.php
	function PostConfig($form) {
		$update_permalinks=false;
		if ((isset($this->columns['PermalinkFolder'])) and (isset($form['Update_Permalink']))) {  
	    	$antiguo=$this->columns['PermalinkFolder']->value;
	    	$nuevo=$form['Form_PermalinkFolder'];
	    	//echo $antiguo . "->" . $nuevo;
	    	if ($antiguo!="") {	$update_permalinks=true; }
	    }
		foreach($form as $key => $value) {
			$valido=strpos($key,'Form_');
	        if($valido===0) {
	            $entrada = substr($key,5,strlen($key)-5);
				$temp = "Form_".$entrada;
				$valor = $form[$temp];
				switch ($valor){
					case 'SI': 	$valor=1;
								break;	
					case 'NO': 	$valor=0;
								break;
				}
				$this->columns[$entrada]->value=$valor;
			    //$config = array('param' => $entrada, 'value' => $valor);
				//$this->Edit($_POST['id'],$config,$this->name);
	        }
	    }
	    $configJSON=$this->ExportToJson();
	    $this->SaveCurrentConfig($_POST['id'],$configJSON);
	    if ($update_permalinks) {
	    	$this->UpdatePermalinks($this->name,$antiguo,$nuevo);
	    }
	}

   function PrepareForm($clase,$cnf=false) {
   		if ($cnf==false) { $cnf=$clase->conf; }
		$in_block=$clase->AddFormBlock('Configuración');
		foreach($cnf->columns as $column) {
			$type=$column->type;
			if (($type=="STRING") and (strpos($column->param, "Image")!==false) and (strpos($column->param, "Options")!==false)) { $type="FILEOPT"; }
			switch($type) {
				case 'SEPARATOR':
					$in_block=$clase->AddFormBlock($column->value);
					break;
	    		case 'BOOLEAN':
	    			$clase->AddFormContent($in_block,'{"Type":"combo-json","Text":"' . $column->param . '","FieldName":"Form_' . $column->param . '","Value":"' . $column->value . '", "JsonValues": {"0":"Deshabilitado","1":"Habilitado"}}');
	        		break;
	    		case 'INTEGER':
	    			$clase->AddFormContent($in_block,'{"Type":"number","Text":"' . $column->param . '","FieldName":"Form_' . $column->param . '","Value":"' . addcslashes($column->value,'\\"') . '","MinValue":"-99999999999", "MaxValue":"99999999999"}');
	        		break;
				case 'STRING':
					$clase->AddFormContent($in_block,'{"Type":"text","Text":"' . $column->param . '","FieldName":"Form_' . $column->param . '","Value":"' . addcslashes($column->value,'\\"') . '"}');
	        		break;
	        	case 'FILEOPT':
	        		$clase->AddFormContent($in_block,'{"Type":"fileopt","Text":"' . $column->param . '","FieldName":"Form_' . $column->param . '","Value":"' . addcslashes($column->value,'\\"') . '"}');
	        		break;
	        }
		}
		if (isset($cnf->columns['PermalinkFolder'])) {
			$in_block=$clase->AddFormBlock('Avanzado');
			$clase->AddFormContent($in_block,'{"Type":"checkbox","Text":"Actualizar Permalinks con el nuevo valor","FieldName":"Update_Permalink","Value":"1"}');
		}
		$clase->AddFormHiddenContent("module",$clase->conf->name);
		$clase->AddFormHiddenContent("id",$clase->id);
		$clase->TemplatePostScript="config/postconfig/id/" . $clase->id;
	}

	//Migra la configuración al formato JSON
   	function LoadFromJSON($id=0) {
   		//Buscamos si existe una configuración para el módulo en JSON
   		$sql="SELECT * FROM modules_config WHERE Module='" . $this->name . "' AND UserID=" . $id . " AND ParamName='json'";
   		$dataJson=self::$db->GetDataRecordFromSQL($sql);
   		if ($dataJson===false) {
   			//No hay datos, pero los cargamos desde la BD en base a la configuración de versiones anteriores...
	   		$sql="SELECT * FROM modules_config WHERE Module='" . $this->name . "' AND UserID=" . $id;
			$DBConfsCount=self::$db->GetDataListFromSQL($sql,$DBConfs);
			if ($DBConfsCount>0) {
				foreach($DBConfs as $dbcnf) {
					$Confs[$dbcnf['ParamName']]=$dbcnf['ParamValue'];
				}
			}
			//Guardamos el json y borramos las configuraciones obsoletas...
			$configJSON=$this->ExportToJson();
			$this->SaveCurrentConfig($id,$configJSON);
			self::$db->Qry("DELETE FROM modules_config WHERE Module='" . $this->name . "' AND UserID=" . $id . " AND ParamName<>'json'");
			$json=$Confs;
		} else {
			$json=json_decode(stripslashes($dataJson['ParamValue']),true);
		}
		return $json;
   	}

   	function ExportToJson() {
   		$output=array();
   		foreach ($this->columns as $param=>$cfg) {
   			if (($cfg->type!="SEPARATOR") and (!$cfg->istemp)) {
   				$val=$cfg->value;
   				if($cfg->type=="BOOLEAN") { $val=intval($val); }
   				$output[$param]=$val;
   			}
   		}
   		return json_encode($output);
   	}

   	function SaveCurrentConfig($idbusiness,$configJSON) {
   		//Comprobamos si existe una configuración previa...
   		$sql="SELECT * FROM modules_config WHERE Module='" . $this->name . "' AND UserID=" . $idbusiness . " AND ParamName='json'";
   		$dataJson=self::$db->GetDataRecordFromSQL($sql);
   		if ($dataJson===false) {
   			$Updater['System_Action']="new";
   			$Updater['System_ID']=-1;
   		} else {
   			$Updater['System_Action']="edit";
   			$Updater['System_ID']=$dataJson['ID'];
   		}
   		$Updater['Form_Module']=$this->name;
   		$Updater['Form_UserID']=$idbusiness;
   		$Updater['Form_ParamName']='json';
   		$Updater['Form_ParamValue']=addslashes($configJSON);
   		self::$db->PostToDatabase('modules_config',$Updater);
   	}

	function __destruct(){}
     
}

//Clase usada para determinar los parámetros a configurar
class Config {
    var $param;
	var $type;
	var $value;
	var $title;
	var $help;
	var $istemp = false;

   	function __construct($par,$typ,$val,$title="",$help=""){
		$this->param=$par;
		$this->type=$typ;
		$this->title=$title;
		$this->help=$help;
		if ($this->title=="") { $this->title=$this->param; }
		if(!$val){ 
			$this->value="";
			if ($this->type=="INTEGER") { $this->value=0; } 
		} else { 
			$this->value=$val; 
		}  
	}
   
   //Muestra el campo determinado del formulario
   function Show(){
	    $lectura="false";	
		echo "<tr><td>".$this->param."</td>";
		echo "<td>";
		switch($this->type){
    		case 'BOOLEAN':
				echo "<select name='Form_".$this->param."'><option ";
				if($this->value=='1'){ echo 'selected'; }
				echo " value='1'>SI</option><option ";
				if($this->value=='0'){ echo 'selected'; }
				echo " value='0'>NO</option></select>";
        		break;
				
    		case 'INTEGER':
        		echo "<input name='Form_".$this->param."' value='$this->value' size='7' ".$lectura." />";
        		break;
				
			case 'STRING':
        		echo "<input name='Form_".$this->param."' value='$this->value' size='15' ".$lectura." />";
        		break;
				
		}
		echo "</td></tr>";
    }
   
	//Modifica el valor
	function Modif($arg){
		$this->param=$arg['param'];
		if(! isset($arg['value'])){ 
			$this->value='';
			if ($this->type=="INTEGER") { $this->value=0; }
			if ($this->type=="BOOLEAN") { $this->value=0; } 
		}
		else { $this->value=$arg['value']; }   
	}
   
   //Actualiza el valor
   function Update($arg){
	   $this->param=$arg['param'];
	   if(!$arg['value']) { 
			if($this->type=='BOOLEAN') { $this->value=0; }
			if($this->type=='INTEGER') { $this->value=0; }
			if($this->type=='STRING')  { $this->value='';}	   
	   } else { 
			if($this->type=='BOOLEAN') {
				if($this->value<$arg['value']) { $this->value=$arg['value']; }
			}
			if($this->type=='INTEGER') {
				if($this->value!=-1) {			
					if(($this->value<$arg['value'])||($arg['value']==-1)) { $this->value=$arg['value']; }
				}	
			}
	   }
   }
     
}

?>