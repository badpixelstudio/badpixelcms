<?php
require_once(sitepath . "templates/" . $this->template . "/adapter.php");
require_once(sitepath . "include/services/services.class.php");

$this->Services=new Services($this->params);

if ($this->params['action']=="list") { 
	$this->Services->GetItems("",false,"Orden");
	$this->HeadTitle=_($this->Services->title);
	$this->HeadDescription=_($this->Services->title);
	$this->loadtemplatepublic('services_list.tpl.php');	
}

if ($this->params['action']=="show") {
	$this->Services->EditItem($this->params['id']);
	$this->HeadTitle=($this->Services->Data['Title']);
	$this->HeadDescription=(LimitString(strip_tags(stripslashes($this->Services->Data['LongDescription'])),200));
	$this->Services->XtraComments= new ExtraComments($this->Services,$this->params['id']);	
	$this->Services->XtraComments->GetComments();
	$template='services_show.tpl.php';
	if (is_file(sitepath . "templates/" . $this->template . "/services_show_" . $this->params['id'] . ".tpl.php")) {
		$template='services_show_' . $this->params['id'] . '.tpl.php';
	}
	$this->loadtemplatepublic($template);	
}
?>