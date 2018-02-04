<?php
require_once(sitepath . "/include/sticker/sticker.class.php");

class ws_Sticker extends Sticker {
	
	function wsSticker() {
		//Comprobamos que haya un AccessToken
		if (! $this->AccessTokenValid) {
			$Resultado=$this->AccessTokenResult;
			return $Resultado;		
		}		
		$this->GetItems("Active=1 AND DatePublish<='" . date('Y-m-d') . "' AND DateExpire>='". date('Y-m-d') . "'",false,"DatePublish DESC, Orden DESC");
		if ($this->ItemsCount>0) {
			$Resultado['Success']=1;
			$Resultado['Items']=$this->Items;
		} else {
			$Resultado['Success']=0;
			$Resultado['Result']="No records found";
		}
		return $Resultado;
	}
}

?>