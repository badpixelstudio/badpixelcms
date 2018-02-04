<?php
require_once(sitepath . "/include/contents/contents.class.php");
require_once(sitepath . "include/extras/images.class.php");
require_once(sitepath . "include/extras/attachments.class.php");
require_once(sitepath . "include/extras/links.class.php");
require_once(sitepath . "include/extras/videos.class.php");
require_once(sitepath . "include/extras/attributes.class.php");
require_once(sitepath . "include/extras/comments.class.php");

class ws_contents extends Contents {
	
	function wsContentsList($page="",$offset="") {
		//Comprobamos que haya un AccessToken
		if (! $this->AccessTokenValid) {
			$Resultado=$this->AccessTokenResult;
			return $Resultado;		
		}		
		$paginado=false;
		if ($offset!="") { $this->offset=$offset; $paginado=true; }
		if ($page!="") { $this->page=$page; $paginado=true; }
		$this->GetItems("",$paginado,$order="Orden");
		if ($this->ItemsCount>0) {
			foreach ($this->Items as $idelemento=>$elemento) {
				unset($this->Items[$idelemento]['IDBusiness']);
				unset($this->Items[$idelemento]['Orden']);
			}
			$Resultado['Success']=1;
			$Resultado['Result']['ItemCount']=$this->ItemsCount;
			$Resultado['Result']['Items']=$this->Items;
		} else {
			$Resultado['Success']=0;
			$Resultado['Result']="No records found";
		}	
		return $Resultado;
	}
	
	function wsContentsSearch($buscar="",$page="",$offset="") {
		//Comprobamos que haya un AccessToken
		if (! $this->AccessTokenValid) {
			$Resultado=$this->AccessTokenResult;
			return $Resultado;		
		}		
		$this->id=0;
		$paginado=false;
		if ($offset!="") { $this->offset=$offset; $paginado=true; }
		if ($page!="") { $this->page=$page; $paginado=true; }
		$this->GetItems("",$paginado,$order="Orden",$buscar);
		if ($this->ItemsCount>0) {
			foreach ($this->Items as $idelemento=>$elemento) {
				unset($this->Items[$idelemento]['IDBusiness']);
				unset($this->Items[$idelemento]['Orden']);
			}
			$Resultado['Success']=1;
			$Resultado['Result']['ItemCount']=$this->ItemsCount;
			$Resultado['Result']['Items']=$this->Items;
		} else {
			$Resultado['Success']=0;
			$Resultado['Result']="No records found";
		}			
		return $Resultado;
	}
	
	function wsContentsNearGeolocation($geo,$radio,$page="",$offset="") {
		//Comprobamos que haya un AccessToken
		if (! $this->AccessTokenValid) {
			$Resultado=$this->AccessTokenResult;
			return $Resultado;		
		}		
		$this->id=0;
		$paginado=false;
		if ($offset!="") { $this->offset=$offset; $paginado=true; }
		if ($page!="") { $this->page=$page; $paginado=true; }
		$this->GetItems(GetNearThisGeo($geo,$radio,'Geolocation'),$paginado,$order="Orden");
		if ($this->ItemsCount>0) {
			foreach ($this->Items as $idelemento=>$elemento) {
				unset($this->Items[$idelemento]['IDBusiness']);
				unset($this->Items[$idelemento]['Orden']);
			}
			$Resultado['Success']=1;
			$Resultado['Result']['ItemCount']=$this->ItemsCount;
			$Resultado['Result']['Items']=$this->Items;
		} else {
			$Resultado['Success']=0;
			$Resultado['Result']="No records found";
		}	
		return $Resultado;
	}
	
	function wsContentsView($id) {
		//Comprobamos que haya un AccessToken
		if (! $this->AccessTokenValid) {
			$Resultado=$this->AccessTokenResult;
			return $Resultado;		
		}
		$GLOBALS['Core']=$this;
		$this->EditItem($id);
		if ($this->Data!==false) {
			unset($this->Data['IDBusiness']);		
			$salida['Data']=$this->Data;

			if(($this->Check('UseImages')) and (isset($this->XtraImages))) {
				$salida['TotalImages']=$this->XtraImages->Total;
				$salida['Images']=$this->XtraImages->Data;
			}
			if(($this->Check('UseAttachments')) and (isset($this->XtraAttachments))) {
				$salida['TotalAttachments']=$this->XtraAttachments->Total;
				$salida['Attachments']=$this->XtraAttachments->Data;
			}
			if(($this->Check('UseLinks')) and (isset($this->XtraLinks))) {
				$salida['TotalLinks']=$this->XtraLinks->Total;
				$salida['Links']=$this->XtraLinks->Data;
			}
			if(($this->Check('UseVideos')) and (isset($this->XtraVideos))){
				$salida['TotalVideos']=$this->XtraVideos->Total;
				$salida['Videos']=$this->XtraVideos->Data;
			}
			// $salida['TotalComments']=$this->XtraComments->Total;
			// $salida['Comments']=$this->XtraComments->Data;
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