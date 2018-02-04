<?php
require_once(sitepath . "include/core/core.master.php");

class Core extends MasterCore{ 

	function GenerateLikesItems() {
		$showfield="";
		$sql="SHOW COLUMNS FROM " . $this->table;
		$TotalEstructura=parent::$db->GetDataListFromSQL($sql,$Estructura);
		if ($TotalEstructura>0) {
			foreach($Estructura as $stru) {
				if ($stru['Field']=="Title") { $showfield="Title"; }
				if ($stru['Field']=="Name") { $showfield="Name"; }
			}
		}
		
		$sql="SELECT ID, " . $showfield . " FROM " . $this->table . " ORDER BY " . $showfield;
		$salida[0]="-- TODOS --";
		$ItemsCount=parent::$db->GetDataListFromSQL($sql,$Items);
		if ($ItemsCount>0) {
			foreach($Items as $item) {
				$salida[$item['ID']]=$item[$showfield];
			}
		}
		return json_encode($salida);
	}

	function GenerateLikes() {
		$in_block=$this->AddFormBlock('Generar likes');
		$this->AddFormContent($in_block,'{"Type":"combo-json","Text":"Elementos","FieldName":"Form_ID","Value":"0","JsonValues": ' . $this->GenerateLikesItems() . '}');
		$this->AddFormContent($in_block,'{"Type": "group", "Text": "Likes positivos","FieldName": "Xtr_LikesPositives"}');
		$this->AddFormContent($in_block,'{"Type":"number","Text":"Mínimo","FieldName":"Form_MinPositives","Value":"0","Required": true}');
		$this->AddFormContent($in_block,'{"Type":"number","Text":"Máximo","FieldName":"Form_MaxPositives","Value":"100","Required": true}');
		$this->AddFormContent($in_block,'{"Type": "group", "Text": "Likes negativos","FieldName": "Xtr_LikesPositives"}');
		$this->AddFormContent($in_block,'{"Type":"number","Text":"Mínimo","FieldName":"Form_MinNegatives","Value":"0","Required": true}');
		$this->AddFormContent($in_block,'{"Type":"number","Text":"Máximo","FieldName":"Form_MaxNegatives","Value":"100","Required": true}');
		$this->TemplatePostScript=$this->module . "/post_generatelikes";
		$this->LoadTemplate('edit.tpl.php');
	}

	function GenerateLikesPost() {
		$sql="SELECT ID FROM " . $this->table;
		if (intval($_POST['Form_ID'])!=0) {
			$sql.=" AND ID=" . $_POST['Form_ID'];
		}
		$ItemsCount=parent::$db->GetDataListFromSQL($sql,$Items);
		if ($ItemsCount>0) {
			foreach($Items as $item) {
				set_time_limit(0);
				$do_votes=rand($_POST['Form_MinPositives'],$_POST['Form_MaxPositives']);
				if ($do_votes>0) {
					for($x=1;$x<$do_votes;$x++) {
						$Datos['System_Action']="new";
						$Datos['System_ID']=-1;
						$Datos['Form_IDUser']="gen_" . KeyGen(20);
						$Datos['Form_TableName']=$this->table;
						$Datos['Form_TableID']=$item['ID'];
						$Datos['Form_ModuleName']=$this->module;
						$Datos['Form_Options']="action=show";
						$Datos['Form_Vote']="+";
						parent::$db->PostToDatabase("likethis",$Datos);
					}
				}
				$do_votes=rand($_POST['Form_MinNegatives'],$_POST['Form_MaxNegatives']);
				if ($do_votes>0) {
					for($x=1;$x<$do_votes;$x++) {
						$Datos['System_Action']="new";
						$Datos['System_ID']=-1;
						$Datos['Form_IDUser']="gen_" . KeyGen(20);
						$Datos['Form_TableName']=$this->table;
						$Datos['Form_TableID']=$item['ID'];
						$Datos['Form_ModuleName']=$this->module;
						$Datos['Form_Options']="action=show";
						$Datos['Form_Vote']="-";
						parent::$db->PostToDatabase("likethis",$Datos);
					}
				}
			}
		}
		header("Location: " . siteprotocol . sitedomain . sitePanelFolder . "/" . $this->module . "/list/text/" . urlencode(base64_encode("Se ha generado votos para " . $ItemsCount . " elementos")));
	}

	function RunAction() {
		parent::RunAction();
		if ($this->action=="generatelikes") { $this->GenerateLikes(); }
		if ($this->action=="post_generatelikes") { $this->GenerateLikesPost(); }
	}
}
?>