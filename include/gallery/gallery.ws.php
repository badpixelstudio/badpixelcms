<?php
require_once(sitepath . "/include/gallery/gallery.class.php");

class ws_gallery extends Gallery {
	
	function wsGalleryList($page="",$offset="") {
		//Comprobamos que haya un AccessToken
		if (! $this->AccessTokenValid) {
			$Resultado=$this->AccessTokenResult;
			return $Resultado;		
		}		
		$paginado=false;
		if ($offset!="") { $this->offset=$offset; $paginado=true; }
		if ($page!="") { $this->page=$page; $paginado=true; }
		$this->view="active";
		//$this->GetGalleries("active");
		$select="SELECT " . $this->table .".*, 'General' as BusinessName, users.UserName as UserName FROM " . $this->table . " LEFT JOIN users ON " . $this->table . ".IDAuthor=users.ID WHERE " .  $this->table . ".ID IS NOT NULL";
		if (siteMulti) {
			$select="SELECT " . $this->table .".*, business.Name as BusinessName, users.UserName as UserName FROM " . $this->table . " LEFT JOIN business ON " . $this->table . ".IDBusiness=business.ID LEFT JOIN users ON " . $this->table . ".IDAuthor=users.ID WHERE " .  $this->table . ".ID IS NOT NULL";
			if (($this->businessID!=0) and (!defined('InFrontEnd'))) {  $select.=" AND IDBusiness= " . $this->businessID . " OR (IDBusiness=0 AND MultiBusiness=1)"; }
		}
		$cond="";
		if ($this->view=="active") {$cond=$this->table . ".Active=1"; }
		if ($this->view=="noactive") {$cond=$this->table . ".Active=0"; }
		$this->GetItems($cond,false,"DatePublish DESC, Orden, ID",$this->search,false,$select);

		if ($this->ItemsCount>0) {
			foreach ($this->Items as $idelemento=>$elemento) {
				unset($this->Items[$idelemento]['IDBusiness']);
				unset($this->Items[$idelemento]['MultiBusiness']);
				unset($this->Items[$idelemento]['AutoGenThumb']);
				unset($this->Items[$idelemento]['ImageOptions']);
				unset($this->Items[$idelemento]['IDAuthor']);
				unset($this->Items[$idelemento]['LastUpdate']);
				unset($this->Items[$idelemento]['Orden']);
				unset($this->Items[$idelemento]['Active']);
				$this->Items[$idelemento]['Permalink']=siteprotocol . sitedomain . $elemento['Permalink'];
			}
			$Resultado['Success']=1;
			$Resultado['Result']['TotalItems']=$this->ItemsCount;
			$Resultado['Result']['Items']=$this->Items;
		} else {
			$Resultado['Success']=0;
			$Resultado['Result']="No records found";
		}		
		return $Resultado;
	}

	
	function wsGalleryView($id) {
		//Comprobamos que haya un AccessToken
		if (! $this->AccessTokenValid) {
			$Resultado=$this->AccessTokenResult;
			return $Resultado;		
		}		
		if (self::$db->LoadFormData($this,$id)) {
			unset($this->Data['Action']);
			unset($this->Data['IDBusiness']);
			unset($this->Data['MultiBusiness']);
			unset($this->Data['AutoGenThumb']);
			unset($this->Data['ImageOptions']);
			unset($this->Data['IDAuthor']);
			unset($this->Data['LastUpdate']);
			unset($this->Data['Orden']);
			unset($this->Data['Active']);
			$this->Data['Permalink']="http://" . sitedomain . $this->GetPermalink($this->table,$id);		
			$salida=$this->Data;
			$this->XtraImages= new ExtraImages($this->table,'','IDFather',$id,$this->conf->GetActualConfig());			
			$this->XtraImages->GetItems();
			$salida['Total']=$this->XtraImages->Total;
			if ($this->XtraImages->Total>0) {
				foreach ($this->XtraImages->Data as $idElemento=>$elemento) {
					if (is_file(sitepath . "public/images/" . $elemento['Image'])) {
						unset($registro);
						$registro['Description']=$elemento['Description'];
						$salida['Items'][]=$registro;
					}
				}
			}
			$Resultado['Success']=1;
			$Resultado['Result']=$salida;
		} else {
			$Resultado['Success']=0;
			$Resultado['Result']="No records found";
		}
		return $Resultado;
	}
}

?>