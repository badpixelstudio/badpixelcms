<?php 
require_once(sitepath . "templates/" . $this->template . "/adapter.php");
require_once(sitepath . "include/catpages/catpages.class.php");
require_once(sitepath . "include/catpages/pages.class.php");
require_once(sitepath . "include/extras/images.class.php");
require_once(sitepath . "include/extras/attachments.class.php");
require_once(sitepath . "include/extras/links.class.php");
require_once(sitepath . "include/extras/videos.class.php");
require_once(sitepath . "include/extras/comments.class.php");
//Comprobamos si la función corresponde a comentarios...
$this->pages=new Pages($this->params);

$Is_Comments_Function=strpos($this->params['action'],'comments_');
if ($Is_Comments_Function!==false) { 
	$this->XtraComments= new ExtraComments($this->pages,0);
	$this->XtraComments->Run(substr($this->params['action'],$Is_Comments_Function+strlen('comments_'),strlen($this->params['action'])-$Is_Comments_Function));
	exit;
}
if ($this->params['action']=='list') {
	$this->cats=new Cats($this->params);
	$this->cats->EditItem($this->params['id']);
	unset($this->cats->BreadCrumb);
	$this->cats->GetTreeBreadCrumb($this->cats->Data['IDFather'],true);
	$this->subcats=new Cats($_GET);
	$this->subcats->GetItems("IDFather=" . $this->params['id'],false,$order="Orden");
	
	if (! isset($this->params['offset'])) { $this->pages->offset=12; }
	$sel="SELECT " . $this->pages->table . ".*, users.UserName as ViewAuthor, (SELECT COUNT(ID) FROM catpages_pages_comments WHERE IDFather=" . $this->pages->table . ".ID AND Active=1) AS TotalComments FROM " . $this->pages->table . " LEFT JOIN users ON users.ID=" . $this->pages->table . ".IDAuthor WHERE " . $this->pages->table  .".IDFather=" . $this->params['id'];
	if($this->pages->conf->Check("PageUseActivation")) { $sel.=" AND " . $this->pages->table . ".Active=1"; }
	if($this->pages->conf->Check('PageUseDates')){ $sel.=" AND DatePublish<=NOW() AND DateExpire>=NOW()"; }
	$this->pages->GetItems("",true,$order="Orden DESC","",false,$sel);
	$this->HeadTitle=stripslashes($this->cats->Data['Title']);
	if ($this->cats->Data['Description']!="") { 
		$this->HeadDescription=strip_tags(stripslashes($this->cats->Data['Description']));
	} else {
		$this->HeadDescription=stripslashes($this->cats->Data['Title']);
	}
	if ($this->cats->Data['Image_images']!="") { $this->HeadImage=$this->cats->Data['Image_images']; }
	$template="cats_list.tpl.php";
	if (is_file(sitepath . "templates/" . $this->template . "/cats_list_" . $this->params['id'] . ".tpl.php")) { $template="cats_list_" . $this->params['id'] . ".tpl.php";	}
	$this->loadtemplatepublic($template);
}

if ($this->params['action']=='show') {
	$this->pages->EditItem($this->params['id']);
	unset($this->pages->BreadCrumb);
	$this->pages->GetTreeBreadCrumb($this->pages->Data['IDFather'],true);
	$user=self::$db->GetDataRecord("users",$this->pages->Data['IDAuthor']);
	$this->pages->Data['ViewAuthor']=$user['UserName'];

	$this->cats=new Cats($this->params);
	$this->cats->EditItem($this->pages->Data['IDFather']);
	$this->XtraComments= new ExtraComments($this->pages,$this->params['id']);	
	$this->XtraComments->GetComments();	
	if (($this->userLevel>=99) or ($this->IsUserOnBusiness($this->cats->Data['IDBusiness'])) or ($this->IsUserOnBusiness($this->pages->Data['IDBusiness']))) { $this->XtraComments->EnableAdmin=true; }
	
	$this->pages->Data['ILikeThis']=$this->GetLikes($LikeType='+',$this->pages->Data['Permalink']);
	$this->pages->Data['NoLikeThis']=$this->GetLikes($LikeType='-',$this->pages->Data['Permalink']);
	ExpandGeo($this->pages->Data['Geolocation'],$this->pages->Data);
	$this->HeadTitle=stripslashes($this->pages->Data['Title']);
	if ($this->pages->Data['Summary']!="") { 
		$this->HeadDescription=strip_tags(stripslashes($this->pages->Data['Summary']));
	} else {
		if ($this->pages->Data['Page']!="") { 
			$this->HeadDescription=strip_tags(stripslashes($this->pages->Data['Page']));
		} else {
			$this->HeadDescription=stripslashes($this->pages->Data['Title']);
		}
	}
	if ($this->cats->Data['Image_images']!="") { $this->HeadImage=$this->cats->Data['Image_images']; }
	//Enable redirect for no content and only one attach.
	if ((($this->pages->Data['Page']=="") or ($this->pages->Data['Page']=="<p></p>")) and ($this->pages->XtraAttachments->Total==1)) {
		header("Location: " . siteprotocol . sitedomain . "public/files/" . $this->pages->XtraAttachments->Data[0]['File']); exit;
	}
	$upd="UPDATE catpages_pages SET Readings=Readings+1, TotalReadings=TotalReadings+1 WHERE ID=" . $this->params['id'];
	self::$db->Qry($upd);
	$template="cats_show.tpl.php";
	if (is_file(sitepath . "templates/" . $this->template . "/cats_show_" . $this->pages->Data['IDFather'] . ".tpl.php")) { $template="cats_show_" . $this->pages->Data['IDFather'] . ".tpl.php";	}
	$this->loadtemplatepublic($template);
}

?>