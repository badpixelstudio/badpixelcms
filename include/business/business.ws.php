<?php
require_once(sitepath . "/include/business/business.class.php");
require_once(sitepath . "/include/business/timetable.class.php");
require_once(sitepath . "/include/business/holidays.class.php");

class ws_business extends Business {
	function GetItemsAddData(&$data) {
		$data['ILikeThis']=$this->GetLikes('+',$data['Permalink']);
		$data['NoLikeThis']=$this->GetLikes('-',$data['Permalink']);
	}

	function wsListTypes() {
		//Comprobamos que haya un AccessToken
		if (! $this->AccessTokenValid) {
			$Resultado=$this->AccessTokenResult;
			return $Resultado;		
		}		
		$sql = "SELECT * FROM " . $this->module . "_attributes_sets";
		$this->ItemsCount=self::$db->GetDataListFromSQL($sql,$this->Items);
		if ($this->ItemsCount>0) {
			foreach ($this->Items as $id=>$itm){
				unset($this->Items[$id]['IDBusiness']); 
				unset($this->Items[$id]['Orden']); 
			}
			$Resultado['Success']=1;
			$Resultado['Result']['ItemsCount']=$this->ItemsCount;
			$Resultado['Result']['Items']=$this->Items;
		} else {
			$Resultado['Success']=0;
			$Resultado['Result']="No records found";
		}
		return $Resultado;
	}
	
	function wsListItems($tipo=0,$offset="",$pagina="",$buscar="",$geo="",$radio="") {
		//Comprobamos que haya un AccessToken
		if (! $this->AccessTokenValid) {
			$Resultado=$this->AccessTokenResult;
			return $Resultado;		
		}
		$paginado=false;
		if ($offset!="") { $this->offset=$offset; $paginado=true; }
		if ($pagina!="") { $this->page=$pagina; $paginado=true; }		
		$this->GetBusinessItems("active",$paginado,$buscar,$tipo,0,0,0,0,"",$geo,$radio);
		if ($this->ItemsCount>0) {
			foreach ($this->Items as $idelemento=>$elemento) {
				unset($this->Items[$idelemento]['Package']);
				unset($this->Items[$idelemento]['IDState']);
				unset($this->Items[$idelemento]['IDCity']);
				unset($this->Items[$idelemento]['IDZone']);
				unset($this->Items[$idelemento]['BillingCIF']);
				unset($this->Items[$idelemento]['BillingName']);
				unset($this->Items[$idelemento]['BillingStreet']);
				unset($this->Items[$idelemento]['BillingState']);
				unset($this->Items[$idelemento]['BillingCity']);
				unset($this->Items[$idelemento]['BillingZipCode']);
				unset($this->Items[$idelemento]['BillingPhone']);
				unset($this->Items[$idelemento]['BillingFax']);
				unset($this->Items[$idelemento]['BillingEmail']);
				unset($this->Items[$idelemento]['CloudFiles']);
				unset($this->Items[$idelemento]['Active']);
				unset($this->Items[$idelemento]['Drafted']);
				unset($this->Items[$idelemento]['VotosPositivos']);
				unset($this->Items[$idelemento]['VotosNegativos']);
				unset($this->Items[$idelemento]['Comentarios']);
				$this->Items[$idelemento]['Permalink']=siteprotocol . sitedomain . $elemento['Permalink'];
			}
			$Resultado['Success']=1;
			$Resultado['Result']['ItemsCount']=$this->ItemsCount;
			$Resultado['Result']['Items']=$this->Items;
		} else {
			$Resultado['Success']=0;
			$Resultado['Result']="No records found";			
		}
		return $Resultado;		
	}

	function wsListUpdated($fromDate="") {
		$add_select='(SELECT COUNT(ID) FROM business_comments WHERE IDFather=business.ID) as Comentarios, (SELECT COUNT(ID) FROM likethis WHERE TableName="business" AND TableID=business.ID AND Vote="+") AS VotesPositives';
		$select = "SELECT *, " . $add_select . " FROM " . $this->table . " WHERE ID>0";
		$cond="";
		if ($fromDate!="") { $cond="LastUpdated>='" . $fromDate . "'"; }
		$orden="business.Drafted DESC, Name";

		//Obtenemos la lista de elementos que sí están activos...
		$this->GetItems("",false,"VotesPositives DESC","",true,$select);
		$ActivesCount=$this->ItemsCount;
		$Actives = array();
		if ($ActivesCount>0) {
			foreach ($this->Items as $item) {
				$Actives[]=$item['ID'];
			}
		}
		unset($this->Items);
		$this->GetItems($cond,false,"VotesPositives DESC","",true,$select);
		if ($this->ItemsCount>0) {
			foreach ($this->Items as $idelemento=>$elemento) {
				unset($this->Items[$idelemento]['Package']);
				unset($this->Items[$idelemento]['IDState']);
				unset($this->Items[$idelemento]['IDCity']);
				unset($this->Items[$idelemento]['IDZone']);
				unset($this->Items[$idelemento]['BillingCIF']);
				unset($this->Items[$idelemento]['BillingName']);
				unset($this->Items[$idelemento]['BillingStreet']);
				unset($this->Items[$idelemento]['BillingState']);
				unset($this->Items[$idelemento]['BillingCity']);
				unset($this->Items[$idelemento]['BillingZipCode']);
				unset($this->Items[$idelemento]['BillingPhone']);
				unset($this->Items[$idelemento]['BillingFax']);
				unset($this->Items[$idelemento]['BillingEmail']);
				unset($this->Items[$idelemento]['CloudFiles']);
				unset($this->Items[$idelemento]['Active']);
				unset($this->Items[$idelemento]['Drafted']);
				unset($this->Items[$idelemento]['VotosPositivos']);
				unset($this->Items[$idelemento]['VotosNegativos']);
				unset($this->Items[$idelemento]['Comentarios']);
				$this->Items[$idelemento]['Permalink']=siteprotocol . sitedomain . $elemento['Permalink'];
			}
			$Resultado['Success']=1;
			$Resultado['Result']['ItemsCount']=$this->ItemsCount;
			$Resultado['Result']['Items']=$this->Items;
			$Resultado['Result']['ActivesCount']=$ActivesCount;
			$Resultado['Result']['Actives']=$Actives;
		} else {
			$Resultado['Success']=0;
			$Resultado['Result']['Message']="No records found";
			$Resultado['Result']['ActivesCount']=$ActivesCount;
			$Resultado['Result']['Actives']=$Actives;			
		}
		return $Resultado;
	}

	
	function wsViewItem($id) {
		//Comprobamos que haya un AccessToken
		if (! $this->AccessTokenValid) {
			$Resultado=$this->AccessTokenResult;
			return $Resultado;		
		}	
		$GLOBALS['Core']=$this;
		$this->EditItem($id);	
		if ($this->Data!==false) {
			unset($this->Data['Package']);
			unset($this->Data['IDState']);
			unset($this->Data['IDCity']);
			unset($this->Data['IDZone']);
			if (is_file(sitepath . 'public/thumbnails/' . $this->Data['Image'])) {
				$this->Data['MinImage']=siteprotocol . sitedomain . "public/thumbnails/" . $this->Data['Image'];
			} else {
				$this->Data['MinImage']="";
			}
			if (is_file(sitepath . 'public/medium/' . $this->Data['Image'])) {
				$this->Data['MedImage']=siteprotocol . sitedomain . "public/medium/" . $this->Data['Image'];
			} else {
				$this->Data['MedImage']="";
			}
			if (is_file(sitepath . 'public/mobile/' . $this->Data['Image'])) {
				$this->Data['MobileImage']=siteprotocol . sitedomain . "public/mobile/" . $this->Data['Image'];
			} else {
				$this->Data['MobileImage']="";
			}
			if (is_file(sitepath . 'public/thumbnails/' . $this->Data['Logo'])) {
				$this->Data['MinLogo']=siteprotocol . sitedomain . "public/thumbnails/" . $this->Data['Logo'];
			} else {
				$this->Data['MinLogo']="";
			}
			if (is_file(sitepath . 'public/medium/' . $this->Data['Logo'])) {
				$this->Data['MedLogo']=siteprotocol . sitedomain . "public/medium/" . $this->Data['Logo'];
			} else {
				$this->Data['MedLogo']="";
			}
			if (is_file(sitepath . 'public/mobile/' . $this->Data['Logo'])) {
				$this->Data['MobileLogo']=siteprotocol . sitedomain . "public/mobile/" . $this->Data['Logo'];
			} else {
				$this->Data['MobileLogo']="";
			}
		
			unset($this->Data['BillingCIF']);
			unset($this->Data['BillingName']);
			unset($this->Data['BillingStreet']);
			unset($this->Data['BillingState']);
			unset($this->Data['BillingCity']);
			unset($this->Data['BillingZipCode']);
			unset($this->Data['BillingPhone']);
			unset($this->Data['BillingFax']);
			unset($this->Data['BillingEmail']);
			unset($this->Data['CloudFiles']);
			unset($this->Data['Active']);
			unset($this->Data['Drafted']);
			unset($this->Data['Claim']);
			unset($this->Data['VotosPositivos']);
			unset($this->Data['VotosNegativos']);
			unset($this->Data['Comentarios']);
			$this->Data['Permalink']=$this->GetPermalink($this->table,$id);	
			$this->Data['ILikeThis']=$this->GetLikes($LikeType='+',$this->Data['Permalink']);
			$this->Data['NoLikeThis']=$this->GetLikes($LikeType='-',$this->Data['Permalink']);
			$this->Data['Permalink']=siteprotocol . sitedomain . $this->Data['Permalink'];	
			$GLOBALS['Core']=$this;
			$this->XtraImages= new ExtraImages($this,$id);
			$this->XtraImages->GetItems();
			$this->XtraAttachments= new ExtraAttachments($this,$id);
			$this->XtraAttachments->GetItems();	
			$this->XtraLinks= new ExtraLinks($this,$id);	
			$this->XtraLinks->GetItems();
			$this->XtraVideos= new ExtraVideos($this,$id);	
			$this->XtraVideos->GetItems();
		
			$salida=$this->Data;
			$salida['XtraImages']['ItemsCount']=$this->XtraImages->ItemsCount;
			$salida['XtraImages']['Items']=$this->XtraImages->Data;
			$salida['XtraAttachments']['ItemsCount']=$this->XtraAttachments->ItemsCount;
			$salida['XtraAttachments']['Items']=$this->XtraAttachments->Data;
			$salida['XtraLinks']['ItemsCount']=$this->XtraLinks->ItemsCount;
			$salida['XtraLinks']['Items']=$this->XtraLinks->Data;
			$salida['XtraVideos']['ItemsCount']=$this->XtraVideos->ItemsCount;
			$salida['XtraVideos']['Items']=$this->XtraVideos->Data;
			$sql="SELECT * FROM " . $this->table . "_comments WHERE IDFather=" . $id . " AND Active=1 ORDER BY Orden";
			$salida['XtraComments']['ItemsCount']=parent::$db->GetDataListFromSQL($sql,$salida['XtraComments']['Items']);
	
			$Resultado['Success']=1;
			$Resultado['Result']=$salida;
		} else {
			$Resultado['Success']=0;
			$Resultado['Result']="No records found";
		}
		return $Resultado;
	}

	function wsGetBusinessAdmin() {
		$select="SELECT ID,Name FROM business WHERE ID IN (SELECT IDBusiness FROM business_users WHERE IDUser=" . $this->userID . ")";
		$this->GetItems("",false,"ID","",false,$select);
		if ($this->ItemsCount>0) {
			$Resultado['Success']=1;
			$Resultado['Result']['ItemCount']=$this->ItemsCount;
			$Resultado['Result']['Items']=$this->Items;
		} else {
			$Resultado['Success']=0;
			$Resultado['Result']="No records found";			
		}
		return $Resultado;
	}

	function wsGetTimeTable($id) {
		$this->timetable= new bTimeTable($this->_values);
		$this->timetable->GetItems("IDFather=" . $id,false,"","",false);
		if ($this->timetable->ItemsCount>0) {
			$Resultado['Success']=1;
			$Resultado['Result']['ItemCount']=$this->timetable->ItemsCount;
			$Resultado['Result']['Items']=$this->timetable->Items;
		} else {
			$Resultado['Success']=0;
			$Resultado['Result']="No records found";			
		}
		return $Resultado;
	}

	function wsGetHolidays($id) {
		$this->holidays= new bHolidays($this->_values);
		$this->holidays->GetItems("IDFather=" . $id . " AND DateHoliday>=NOW()",false,"","",false);
		if ($this->holidays->ItemsCount>0) {
			$Resultado['Success']=1;
			$Resultado['Result']['ItemCount']=$this->holidays->ItemsCount;
			$Resultado['Result']['Items']=$this->holidays->Items;
		} else {
			$Resultado['Success']=0;
			$Resultado['Result']="No records found";			
		}
		return $Resultado;
	}

	function wsCheckIsOpen($id) {
		$result=$this->IsOpen($id);
		$Resultado['Success']=1;
		$Resultado['Result']=$result;
		return $Resultado;
	}
	
}

?>