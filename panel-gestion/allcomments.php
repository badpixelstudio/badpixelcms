<?php 
require_once("../include/extras/comments.class.php");
$Core=new Core($params);
$Core->table="users";
$Core->conf= new ConfigCore(0);
$Core->title="Todos los comentarios";
$modulo="cats";
$prior=0;
if (isset($_GET['module'])) { $modulo=$_GET['module']; }
if (isset($_GET['prior'])) { $prior=$_GET['prior']; }

//Parcheamos los posibles problemas de parametros...
$Is_Comments_Function=strpos($Core->action,'comments_');
if ($Is_Comments_Function!==false) { $Core->action=substr($Core->action,$Is_Comments_Function+strlen('comments_'),strlen($Core->action)-$Is_Comments_Function); }

$Core->XtraComments= new ExtraComments($Core,$prior);
$Core->XtraComments->_values=$params;
$Core->XtraComments->Run($Core->action);

?>