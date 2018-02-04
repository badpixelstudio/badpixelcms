<?php
require_once(sitepath . "templates/" . $this->template . "/adapter.php");
require_once(sitepath . "include/contents/contents.class.php");
require_once(sitepath . "include/extras/images.class.php");
require_once(sitepath . "include/extras/attachments.class.php");
require_once(sitepath . "include/extras/links.class.php");
require_once(sitepath . "include/extras/videos.class.php");
require_once(sitepath . "include/extras/comments.class.php");
//Comprobamos si la función corresponde a comentarios...
$Is_Comments_Function=strpos($this->params['action'],'comments_');
if ($Is_Comments_Function!==false) { 
	$this->XtraComments= new ExtraComments('calendar','','IDFather',0);
	$this->XtraComments->Run(substr($this->params['action'],$Is_Comments_Function+strlen('comments_'),strlen($this->params['action'])-$Is_Comments_Function));
	exit;
}
$this->Contents= new Contents($this->params);

if ($this->params['action']=="list") {
	$this->Contents->GetItems("",false,$order="Orden");
	$this->HeadTitle=_($this->Contents->title);
	$this->HeadDescription=_($this->Contents->title);
	$this->loadtemplatepublic('contents_list.tpl.php');
}

if ($this->params['action']=="show") {
	$this->Contents->EditItem($this->params['id']);
	ExpandGeo($this->Contents->Data['Geolocation'],$this->Contents->Data);
	$this->HeadTitle=($this->Contents->Data['Title']);
	$this->HeadDescription=stripslashes($this->Contents->Data['ShortDescription']);
	if ($this->HeadDescription=="") { $this->HeadDescription=(LimitString(strip_tags(stripslashes($this->Contents->Data['LongDescription'])),400)); }
	if ($this->Contents->Data['Image_images']!="") { $this->HeadImage=$this->Contents->Data['Image_images']; }
	$template='contents_show.tpl.php';
	if (is_file(sitepath . "templates/" . $this->template . "/contents_show_" . $this->params['id'] . ".tpl.php")) {
		$template='contents_show_' . $this->params['id'] . '.tpl.php';
	}
	$this->loadtemplatepublic($template);
}
?>