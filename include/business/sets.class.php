<?php
require_once(sitepath . "include/business/business.class.php");

class SetsBusiness extends Business{ 
	var $title='Tipos de Inmuebles';
	var $class="sets";
	var $module='business';
	var $table='business';
	var $version=null;

	function __construct($values) {
		parent::__construct($values); 
		//if ($this->action=="list") { $this->action="sets"; }
	}
	
	function RunAction() {
		$this->XtraAttributes= new ExtraAttributes($this->table,$this->GetModuleName($this->module),'IDFather');
		$this->XtraAttributes->SetsUsePermalink=true;
		$this->XtraAttributes->SetsPermalinkFolder=$this->conf->Export("PermalinkFolder");
		$this->XtraAttributes->Run($this->action);
	}
}
?>