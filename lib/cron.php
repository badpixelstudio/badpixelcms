<?php 
// Ejecuta las acciones del crontab
// Creado por Israel García Sáez para BadPixel.
// Revisión: 3.0 de 02 de febrero de 2018, por Israel Garcia.

//Cargamos los ficheros que contienen las clases necesarias
set_time_limit(0);
ini_set("max_execution_time", 0);
ini_set( 'arg_separator.output', '&amp;');
ini_set( 'url_rewriter.tags', 'a=href,area=href,frame=src,input=src');
ini_set( 'session.use_trans_sid', 0); 
ini_set( 'session.use_cookies', 1); 
ini_set( 'session.use_only_cookies', 1); 
ini_set( 'session.gc_maxlifetime', 60); 
ini_set( 'display_errors', 1);
session_cache_limiter('nocache, private');

require_once('../include/core/common.php');	
require_once(sitepath . 'include/core/core.class.php');

$separator="<br>\r\n";

$Core=new Core();
$Core->params = '';
$Core->HeadMenuSelected="";
require_once(sitepath . "include/core/permalinks.class.php");
require_once(sitepath . 'include/core/socialmedia.class.php');
$Core->Permalinks=new Permalinks($Core->params);
$Core->SocialMedia=new SocialMedia($Core->params);


//Cargamos las variables del sistema
$Core->GetSystemVariables();
if (! isset($Core->SystemVariables['SocialMediaDate'])) { $Core->SystemVariables['SocialMediaDate']='0000-00-00'; }
if (! isset($Core->SystemVariables['SocialMediaInterval'])) { $Core->SystemVariables['SocialMediaInterval']=0; }
echo "Tareas CRON" . $separator;
echo "*******************************" . $separator;
//Inicialización diaria...
if ($Core->SystemVariables['SocialMediaDate']!=date("Y-m-d")) { 
	if ($Core->businessID==0) {
		if ($Core->Permalinks->CreateSiteMap()) {
			echo "- El archivo sitemap.xml ha sido creado" . $separator;
		} else {
			echo "- No se ha podido crear el archivo sitemap.xml" . $separator;
		}
	}
	//Aqui se pueden añadir las tareas que se ejecutarán una vez al día


	//Cargamos los datos de SocialMedia para publicar...
	$Core->SocialMedia->LoadDataForPublish($Core->SocialMedia->conf->Export('AutoSocialMediaParameters'),$Core->businessID); 
}
//Aqui se pueden incluir las tareas que se ejecutarán cada vez que se ejecute la tarea CRON.


//SocialMedia
$Core->SocialMedia->CronProcess(false);

echo $separator . $separator;
echo "*******************************" . $separator;
echo "Fin de las tareas CRON" . $separator;
?>