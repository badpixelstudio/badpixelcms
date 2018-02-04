<?php
require_once(sitepath . "templates/" . $this->template . "/adapter.php");
require_once(sitepath . "include/singleblog/singleblog.class.php");
require_once(sitepath . "include/extras/comments.class.php");
//Comprobamos si la función corresponde a comentarios...
$Is_Comments_Function=strpos($this->params['action'],'comments_');
if ($Is_Comments_Function!==false) { 
	$this->XtraComments= new ExtraComments('singleblog','','IDFather',0);
	$this->XtraComments->Run(substr($this->params['action'],$Is_Comments_Function+strlen('comments_'),strlen($this->params['action'])-$Is_Comments_Function));
	exit;
}
$this->SingleBlog=new SingleBlog($this->params);

if ($this->params['action']=="list") { 
	$cond="";
	if($this->SingleBlog->conf->Check("UseActivation")) { $cond="Active=1"; }
	$this->SingleBlog->GetItems($cond,true,"Orden DESC, DatePublish DESC, ID DESC");
	$this->HeadTitle=_($this->SingleBlog->title);
	$this->HeadDescription=_($this->SingleBlog->title);
	$this->Permalink=$this->SingleBlog->conf->Export("PermalinkFolder");
	$this->loadtemplatepublic('singleblog_list.tpl.php');	
}

if ($this->params['action']=="cats") {
	$this->SingleBlog->GetItems("IDCategory=" . $this->params['id'],true,"Orden DESC, DatePublish DESC, ID DESC");
	$this->Father=self::$db->GetDataRecord($this->SingleBlog->table . "_cats",$this->params['id']);
	$this->HeadTitle=_($this->SingleBlog->title) . " - " . $this->Father['Title'];
	$this->HeadDescription=_($this->SingleBlog->title);
	$this->Permalink=$this->GetPermalink($this->SingleBlog->table . "_cats",$this->params['id']);
	$this->loadtemplatepublic('singleblog_list.tpl.php');	
}

if ($this->params['action']=="show") {
	$this->SingleBlog->EditItem();
	$this->HeadTitle=($this->SingleBlog->Data['Title']);
	$this->HeadDescription=(LimitString(strip_tags(stripslashes($this->SingleBlog->Data['LongDescription'])),200));
	if ($this->SingleBlog->Data['LongDescription']!="") { $this->HeadDescription=strip_tags(stripslashes($this->SingleBlog->Data['ShortDescription'])); }
	$this->loadtemplatepublic('singleblog_show.tpl.php');	
}

?>