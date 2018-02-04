<?php
// file: bitly.php
// author: unijimpe
// web: http://blog.unijimpe.net

class Bitly {
	var $path;
	var $user; 
	var $key;
	
	function Bitly ($_user, $_key) {
		$this->path = "http://api.bit.ly/v3/";
		$this->user = $_user;
		$this->key = $_key;
	}
	function shorten($url) {
		$temp = $this->path."shorten?login=".$this->user."&apiKey=".$this->key."&uri=".$url."&format=txt";
		$data = file_get_contents($temp);
		return $data;
	}
	function expand($url) {
		$temp = $this->path."expand?login=".$this->user."&apiKey=".$this->key."&shortUrl=".$url."&format=txt";
		$data = file_get_contents($temp);
		return $data;
	}	
}
?>