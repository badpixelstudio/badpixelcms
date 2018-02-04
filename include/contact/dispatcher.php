<?php
require_once(sitepath . "templates/" . $this->template . "/adapter.php");
require_once(sitepath . "include/contact/contact.class.php");
$this->Contact= new Contact($this->params);
if (isset($this->params['send'])) {$this->params['action']="send"; }
if ($this->params['action']=="send") {
	echo $this->Contact->SendForm();
	die;
}

if ($this->params['action']=='recaptcha') {
	echo $this->Contact->CheckReCaptcha($_POST['g-recaptcha-response']);
	die;
}

$this->HeadTitle=_("Contactar");
$this->HeadDescription=_("Contactar con") . " " . siteTitle;
if (siteOwnerGeolocation!="") {	ExpandGeo(siteOwnerGeolocation,$this->Data); }	
$this->loadtemplatepublic('contact.tpl.php');
?>