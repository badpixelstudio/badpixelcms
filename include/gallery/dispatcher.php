<?php
require_once(sitepath . "templates/" . $this->template . "/adapter.php");
require_once(sitepath . "include/gallery/gallery.class.php");
require_once(sitepath . "include/extras/images.class.php");

$this->Gallery=new Gallery($_GET);

if ($this->params['action']=='list') {
	$select="SELECT " . $this->Gallery->table .".*, users.UserName as UserName FROM " . $this->Gallery->table . " LEFT JOIN users ON " . $this->Gallery->table . ".IDAuthor=users.ID WHERE " .  $this->Gallery->table . ".ID IS NOT NULL";
	$this->Gallery->GetItems($this->Gallery->table . ".Active=1",true,"DatePublish DESC, Orden, ID",false,false,$select);
	$this->HeadTitle=_($this->Gallery->title);
	$this->HeadDescription=_($this->Gallery->title);
	$this->loadtemplatepublic('gallery_list.tpl.php');
}

if ($this->params['action']=='all') {
	$select="SELECT " . $this->Gallery->table .".*, users.UserName as UserName FROM " . $this->Gallery->table . " LEFT JOIN users ON " . $this->Gallery->table . ".IDAuthor=users.ID WHERE " .  $this->Gallery->table . ".ID IS NOT NULL";
	$this->Gallery->GetItems($this->Gallery->table . ".Active=1",false,"DatePublish DESC, Orden, ID",false,false,$select);
	$this->Gallery->Data['TotalImages']=self::$db->GetDataListFromSQL('SELECT * FROM ' . $this->Gallery->table . '_images ORDER BY Orden,ID',$this->Gallery->Data['Images']);
	$this->HeadTitle=_($this->Gallery->title);
	$this->HeadDescription=_($this->Gallery->title);
	$this->loadtemplatepublic('gallery_all.tpl.php');
}

if ($this->params['action']=='show') {
	//Obtenemos los datos de la galeria...
	$this->Gallery->id=$this->params['id'];
	$this->Gallery->EditItem($this->params['id']); 
	$this->Gallery->Data['UserName']=self::$db->GetDataField('users',$this->Gallery->Data['IDAuthor'],'UserName');
	$this->HeadTitle=($this->Gallery->Data['Title']);
	$this->HeadDescription=(LimitString(strip_tags(stripslashes($this->Gallery->Data['Description'])),200));
	if ($this->Gallery->Data['Image_images']!="") { $this->HeadImage=$this->Gallery->Data['Image_images']; }
	$this->loadtemplatepublic('gallery_show.tpl.php');
}

if ($this->params['action']=='zip') {
	ini_set('memory_limit', '-1');
	$this->Gallery->id=$this->params['id'];
	echo $this->Gallery->DownloadGallery();
}

?>