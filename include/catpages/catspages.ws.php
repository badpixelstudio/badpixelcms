<?php
require_once(sitepath . "/include/catpages/catpages.class.php");
require_once(sitepath . "/include/catpages/pages.class.php");
require_once(sitepath . "include/extras/images.class.php");
require_once(sitepath . "include/extras/attachments.class.php");
require_once(sitepath . "include/extras/links.class.php");
require_once(sitepath . "include/extras/videos.class.php");
require_once(sitepath . "include/extras/attributes.class.php");
require_once(sitepath . "include/extras/comments.class.php");

class ws_cats extends Cats {

	function wsCatsList() {
		//Comprobamos que haya un AccessToken
		if (! $this->AccessTokenValid) {
			$Resultado=$this->AccessTokenResult;
			return $Resultado;		
		}
		$this->GetTreeItems($this->Items);
		$Resultado['Success']=1;
		$Resultado['Result']=$this->Items;
		return $Resultado;
	}

	function wsCatView($id) {
		//Comprobamos que haya un AccessToken
		if (! $this->AccessTokenValid) {
			$Resultado=$this->AccessTokenResult;
			return $Resultado;		
		}		
		$this->EditItem($id);
		if ($this->Data!==false) {
			//Obtenemos la categoria....
			$this->Categoria=self::$db->GetDataRecord('catpages',$this->Data['IDFather']);	
			unset($this->Data['Action']);
			unset($this->Data['ID']);
			unset($this->Data['IDBusiness']);
			$this->Data['Permalink']=siteprotocol . sitedomain . $this->Data['Permalink'];
			$Core=$this;
			$salida=$this->Data;
			$Resultado['Success']=1;
			$Resultado['Result']=$salida;
		} else {
			$Resultado['Success']=0;
			$Resultado['Result']="No records found";
		}
		return $Resultado;
	}
}

class ws_catpage extends Pages {
	
	function wsCatPageList($id="1",$page="",$offset="",$last_update="") {
		//Comprobamos que haya un AccessToken
		if (! $this->AccessTokenValid) {
			$Resultado=$this->AccessTokenResult;
			return $Resultado;		
		}		
		$this->id=$id;
		$paginado=false;
		if ($offset!="") { $this->offset=$offset; $paginado=true; }
		if ($page!="") { $this->page=$page; $paginado=true; }
		$this->table="catpages";
		self::$db->LoadFormData($this,$this->id);
		$this->Data['Permalink']=$this->GetPermalink();	
		$this->table="catpages_pages";
		$select = "SELECT catpages_pages.*, users.UserName AS AuthorUserName, users.Name AS AuthorName, users.Email AS AuthorEmail FROM catpages_pages INNER JOIN users ON users.ID = catpages_pages.IDAuthor WHERE catpages_pages.ID>0";
		$cond = "catpages_pages.IDFather IN (" . $id . ") AND catpages_pages.Active=1 AND catpages_pages.DatePublish<=NOW() AND catpages_pages.DateExpire>=NOW()";
		if ($last_update != "") { 
			$base_cond = $cond;
			$cond .= "AND catpages_pages.LastUpdate>='" . $last_update . "'"; 
		}
		$this->GetItems($cond,$paginado,"Orden DESC",false,true,$select);
		if ($this->ItemsCount>0) {
			foreach ($this->Items as $idelemento=>$elemento) {
				unset($this->Items[$idelemento]['IDBusiness']);
				unset($this->Items[$idelemento]['IDFather']);
				unset($this->Items[$idelemento]['ImageAlign']);
				unset($this->Items[$idelemento]['Active']);
			}
			$Resultado['Success']=1;
			$Resultado['Result']['ItemCount']=$this->ItemsCount;
			$Resultado['Result']['Items']=$this->Items;
			if (($last_update != "") and (! $paginado)) {
				unset($this->Items);
				$select= "SELECT * FROM catpages WHERE ID>0";
				$this->GetItems($base_cond,false,"ID",false,false,$select);
				$Resultado['Result']['All']['ItemsCount']=$this->ItemsCount;
				if ($this->ItemsCount>0) {
					foreach ($this->Items as $item) {
						$Resultado['Result']['All']['Items'][]=$item['ID'];
					}
				}
			}
		} else {
			$Resultado['Success']=0;
			$Resultado['Result']="No records found";
		}	
		return $Resultado;
	}

	
	function wsCatPageView($id) {
		//Comprobamos que haya un AccessToken
		if (! $this->AccessTokenValid) {
			$Resultado=$this->AccessTokenResult;
			return $Resultado;		
		}		
		$this->table="catpages_pages";
		$this->id=$id;
		$GLOBALS['Core']=$this;
		$this->EditItem($id);
		if ($this->Data!==false) {
			//Obtenemos la categoria....
			$this->Categoria=self::$db->GetDataRecord('catpages',$this->Data['IDFather']);	
			$this->Data['ILikeThis']=$this->GetLikes($LikeType='+',$this->Data['Permalink']);
			$this->Data['NoLikeThis']=$this->GetLikes($LikeType='-',$this->Data['Permalink']);
			$this->XtraComments= new ExtraComments($this,$id);	
			$this->XtraComments->GetComments();			
			
			unset($this->Data['Action']);
			unset($this->Data['ID']);
			unset($this->Data['IDBusiness']);
			unset($this->Data['IDFather']);
			unset($this->Data['ImageAlign']);
			unset($this->Data['EnableComments']);
			$this->Data['Permalink']=siteprotocol . sitedomain . $this->Data['Permalink'];
			$Core=$this;
			$salida=$this->Data;
			$salida['XtraImages']['ItemsCount']=$this->XtraImages->ItemsCount;
			$salida['XtraImages']['Items']=$this->XtraImages->Items;
			$salida['XtraAttachments']['ItemsCount']=$this->XtraAttachments->ItemsCount;
			$salida['XtraAttachments']['Items']=$this->XtraAttachments->Items;
			$salida['XtraLinks']['ItemsCount']=$this->XtraLinks->ItemsCount;
			$salida['XtraLinks']['Items']=$this->XtraLinks->Items;
			$salida['XtraVideos']['ItemsCount']=$this->XtraVideos->ItemsCount;
			$salida['XtraVideos']['Items']=$this->XtraVideos->Items;
			$salida['XtraComments']['ItemsCount']=$this->XtraComments->ItemsCount;
			$salida['XtraComments']['Items']=$this->XtraComments->Items;
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