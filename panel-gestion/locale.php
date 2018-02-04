<?php 
$Core=new Core($params);
$Core->ChangeLang($params['setlang']);
$redir=siteprotocol . sitedomain . sitePanelFolder;
if (isset($_SERVER['HTTP_REFERER'])) { $redir=$_SERVER['HTTP_REFERER']; }
header("Location: " . $redir);
?>