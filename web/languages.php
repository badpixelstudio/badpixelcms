<?php
$lang=$this->userlang;
if (isset($this->params['lang'])) { $lang=$this->params['lang']; }
$this->ChangeLang($lang);

$referer=siteprotocol . sitedomain;
if (isset($_SERVER['HTTP_REFERER'])) { $referer=$_SERVER['HTTP_REFERER']; }
$referer=str_replace(siteprotocol . sitedomain, "", $referer);
//Buscamos si la url tiene un código de idioma...
if ((strlen($referer)==5) or (substr($referer, 5,1)=="/")) {
	//quitamos idioma anterior
	$part=substr($referer, 0,5);
	$count=self::$db->GetDataFieldFromSQL("SELECT COUNT(ID) AS Total FROM languages WHERE code='" . $part . "'","Total");
	if ($count>0) {
		$referer=substr($referer,5);
		if (substr($referer,0,1)=="/") { $referer=substr($referer,1); }
	}
}
//$_SESSION['lang']=$lang;
//Clear double slash
$referer=siteprotocol . preg_replace('#/+#','/',sitedomain . $lang . "/" . $referer);
header("Location: " . $referer);
?>