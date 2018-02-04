<?php 
$host=$_SERVER['SERVER_NAME'];
$ini_host=strpos($host,'www.');
if ($ini_host!==false) {
	$host=substr($host,1,$ini_host) . substr($host,$ini_host+4) . $_SERVER['REQUEST_URI'];
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: http://" . $host);
	exit;
}

set_time_limit(0);
ini_set("max_execution_time", 0);
ini_set( 'arg_separator.output', '&amp;');
ini_set( 'url_rewriter.tags', 'a=href,area=href,frame=src,input=src');
ini_set( 'session.use_trans_sid', 0); 
ini_set( 'session.use_cookies', 1); 
ini_set( 'session.use_only_cookies', 1); 
ini_set( 'session.gc_maxlifetime', 60); 
session_cache_limiter('nocache, private');

//Check installation
if (! is_file('include/core/common.php')) { 
    $fld=$_SERVER['DOCUMENT_ROOT'];
    if (isset($_SERVER['CONTEXT_DOCUMENT_ROOT'])) { $fld=$_SERVER['CONTEXT_DOCUMENT_ROOT']; }
    $this_file=str_replace("\\", "/", __DIR__);
    $route=str_replace($fld, "", $this_file);
    header("Location: " . $route . "/install"); die; 
}
require_once('include/core/common.php');	
require_once(sitepath . 'include/core/core.class.php');

//*********************
// GESTOR DE ERRORES DE SERVIDOR
function OnRaiseError($errno, $errstr, $errfile, $errline) {
	$errnodesc=$errno;
	switch($errno) {
        case E_ERROR: 			$errnodesc=$errno .' E_ERROR'; break;
        case E_WARNING: 		$errnodesc=$errno .' E_WARNING'; break;
        case E_PARSE: 			$errnodesc=$errno .' E_PARSE'; break;
        case E_NOTICE: 			$errnodesc=$errno .' E_NOTICE'; break;
        case E_CORE_ERROR:		$errnodesc=$errno .' E_CORE_ERROR'; break;
        case E_CORE_WARNING: 	$errnodesc=$errno .' E_CORE_WARNING'; break;
        case E_COMPILE_ERROR: 	$errnodesc=$errno .' E_COMPILE_ERROR'; break;
        case E_COMPILE_WARNING: $errnodesc=$errno .' E_COMPILE_WARNING'; break;
        case E_USER_ERROR: 		$errnodesc=$errno .' E_USER_ERROR'; break;
        case E_USER_WARNING: 	$errnodesc=$errno .' E_USER_WARNING'; break;
        case E_USER_NOTICE: 	$errnodesc=$errno .' E_USER_NOTICE'; break;
        case E_STRICT: 			$errnodesc=$errno .' E_STRICT'; break;
        case E_RECOVERABLE_ERROR: $errnodesc=$errno .' E_RECOVERABLE_ERROR'; break;
        case E_DEPRECATED: 		$errnodesc=$errno .' E_DEPRECATED'; break;
        case E_USER_DEPRECATED: $errnodesc=$errno .' E_USER_DEPRECATED'; break;
    }
    $is_fatal_error=true;
    $log_error=true;
    if (($errno!=E_ERROR) and ($errno!=E_CORE_ERROR) and ($errno!=E_COMPILE_ERROR) and ($errno!=E_RECOVERABLE_ERROR)) { $is_fatal_error=false; }
    if ((! $is_fatal_error) and (! siteLogAllErrorMessages)) { $log_error=false; }
	if ($log_error) { error_log(date('d/m/Y H:i:s') . " Error " . $errnodesc . ": " . $errstr . " in " . $errfile . ", on line " . $errline . "\r\n", 3, "public/errorlog.txt"); }
    if (! $is_fatal_error) {
       	trigger_error($errstr);
        return;
    }
    $GLOBALS['Core']->Error500($errno,$errstr,$errline);
	return true;
}

$Core=new Core();
if (siteDisplayErrors) { ini_set("display_errors", 1); } else { ini_set("display_errors", 0); }
if (siteDebugActive) {$gestor_errores_antiguo = set_error_handler("OnRaiseError");}

$Core->params = '';
$Core->RunApp();
?>