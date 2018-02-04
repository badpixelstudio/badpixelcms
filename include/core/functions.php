<?php
$ModuleVersion='2.0.0.0';

// Biblioteca de funciones comunes 
// Creado por Israel García Sáez para BadPixel,
// Basado en el código de Israel García Sáez, para MixMusic.es
// Revisión: 1.0 de 1 de Junio de 2012, por Israel Garcia.

function ParseMySqlDate($fecha){
	if (strlen($fecha)>10) {
		$lafecha=preg_replace('#^(\d{2})/(\d{2})/(\d{4}) (.+)$#', '$3-$2-$1 $4', $fecha);
	} else {
		$lafecha=preg_replace('#^(\d{2})/(\d{2})/(\d{4})$#', '$3-$2-$1', $fecha);
	}
	return $lafecha;
} 

function PatchCheckBox(&$formulario,$campo) {
	if(isset($formulario[$campo])){
		if (($formulario[$campo]==="false") or ($formulario[$campo]===0) or ($formulario[$campo]==="0")) {$formulario[$campo]=0; }
		else { $formulario[$campo]=1; }
	} else {
		$formulario[$campo]=0;
	}
}

function PatchDate(&$formulario,$campo) {
	$formulario[$campo]=ParseMySqlDate($formulario[$campo]);
}

function ViewDay($fecha){
	preg_match("/(\d+)-(\d+)-(\d+)/", $fecha, $mifecha);
	$lafecha=$mifecha[3];
	return $lafecha;
} 

function ViewTextDay($fecha){
	$mifecha = explode('-',$fecha);
	$mifecha[2]=substr($mifecha[2],0,2);
	$day = date ( "l", mktime ( 0 , 0 , 0 , $mifecha[1] , $mifecha[2] , $mifecha[0] ) );

	switch($day) {
		case 'Monday': $day='Lunes'; break;
		case 'Tuesday': $day='Martes'; break;
		case 'Wednesday': $day='Miércoles'; break;
		case 'Thursday': $day='Jueves'; break;
		case 'Friday': $day='Viernes'; break;
		case 'Saturday': $day='Sábado'; break;
		case 'Sunday': $day='Domingo'; break;
	}
	return $day;

}

function ViewMonthNumber($fecha) {
	preg_match("/(\d+)-(\d+)-(\d+)/", $fecha, $mifecha);
	return $mifecha[2];
}

function ViewMonth($fecha){
	preg_match("/(\d+)-(\d+)-(\d+)/", $fecha, $mifecha);
	switch($mifecha[2]) {
		case '1': $lafecha='Enero'; break;
		case '2': $lafecha='Febrero'; break;
		case '3': $lafecha='Marzo'; break;
		case '4': $lafecha='Abril'; break;
		case '5': $lafecha='Mayo'; break;
		case '6': $lafecha='Junio'; break;
		case '7': $lafecha='Julio'; break;
		case '8': $lafecha='Agosto'; break;
		case '9': $lafecha='Septiembre'; break;
		case '10': $lafecha='Octubre'; break;
		case '11': $lafecha='Noviembre'; break;
		case '12': $lafecha='Diciembre'; break;
	}
	return $lafecha;
} 

function ViewYear($fecha) {
	preg_match("/(\d+)-(\d+)-(\d+)/", $fecha, $mifecha);
	$lafecha=$mifecha[1];
	return $lafecha;
}

function ViewDate($fecha){
	$year = substr($fecha,0,4);
	$day=ViewDay($fecha);
	$month=ViewMonth($fecha);
	return $day." ".$month." ".$year;
} 

function AddDays($fecha,$ndias) {
	preg_match("/(\d+)-(\d+)-(\d+)/", $fecha, $wdate);
	return date('Y-m-d',mktime(0,0,0,$wdate[2],$wdate[3]+$ndias,$wdate[1]));		
	//return date('Y-m-d', strtotime($ndias . ' day')) ;	
}

function AddMonths($fecha,$nmeses) {
	preg_match("/(\d+)-(\d+)-(\d+)/", $fecha, $wdate);
	return date('Y-m-d',mktime(0,0,0,$wdate[2]+$nmeses,$wdate[3],$wdate[1]));
	//return date('Y-m-d', strtotime($nmeses . ' month')) ;
}

function AddMinutes($HoraStr, $MinASumar) {
    $HoraOrigen = explode(":", $HoraStr);
    $Horas = $HoraOrigen[0];
    $Minutos = $HoraOrigen[1];
	if (count($HoraOrigen)>2) {
	    $Segundos = $HoraOrigen[2];
	} else {
		$Segundos= 0;
	}
    // Sumo los minutos
    $Minutos = ((int)$Minutos) + ((int)$MinASumar);
    // Asigno la fecha modificada a una nueva variable
    $HoraNueva = date("H:i:s",mktime($Horas,$Minutos,$Segundos,1,1,1980));
    return $HoraNueva;
}

function StripFileName($archive,$limit=70) {
	$nueva_cadena = str_replace("/","_", $archive);
	$nueva_cadena = preg_replace("[^ A-Za-z0-9_]", "", $nueva_cadena); 
	$eliminar=array("!","¡","?","¿","'","\"","$","(",")",":",";","/","\\","\$","%","@","#",",", "«", "»","+","ª","º","&","`","´","<",">","€","{","}","[","]","*","·");
	$buscados=array(" ","á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ç","Ç","ü","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","ä","ë","ï","ö","ü","Ä","Ë","Ï","Ö","Ü");
	$sustitut=array("-","a","e","i","o","u","a","e","i","o","u","n","n","c","C","u","a","e","i","o","u","A","E","I","O","U","a","e","i","o","u","A","E","I","O","U");
	$nueva_cadena=strtolower(str_replace($buscados,$sustitut,str_replace($eliminar,"",$nueva_cadena)));
	$nueva_cadena=str_replace("–","-",$nueva_cadena);
	$nueva_cadena=preg_replace("[^A-Za-z\_\-\.0-9]", "", $nueva_cadena);               
	$nueva_cadena = substr($nueva_cadena,0,$limit);
	return $nueva_cadena;  
}

function StripFileNameTildes($archive) {
	$nueva_cadena = str_replace("/","_", $archive);
	$eliminar=array("!","¡","?","¿","'","\"","$","(",")",".",":",";","/","\\","\$","%","@","#",",", "«", "»");
	$buscados=array(" ","á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","ä","ë","ï","ö","ü","Ä","Ë","Ï","Ö","Ü");
	$sustitut=array("-","a","e","i","o","u","a","e","i","o","u","n","n","u","a","e","i","o","u","A","E","I","O","U","a","e","i","o","u","A","E","I","O","U");
	$nueva_cadena=str_replace($buscados,$sustitut,str_replace($eliminar,"",$nueva_cadena));
	return $nueva_cadena;  
}

function GetPermanentLink($folder,$id,$urltext, $primary_folder='..') {
	$eliminar=array("!","¡","?","¿","'","\"","$","(",")",".",":",";","_","/","\\","\$","%","@","#",",", "«", "»");
	$buscados=array(" ","á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","ä","ë","ï","ö","ü","Ä","Ë","Ï","Ö","Ü");
	$sustitut=array("-","a","e","i","o","u","a","e","i","o","u","n","n","u","a","e","i","o","u","A","E","I","O","U","a","e","i","o","u","A","E","I","O","U");
	$final=strtolower(str_replace($buscados,$sustitut,str_replace($eliminar,"",$urltext)));
	$final=str_replace("–","-",$final);
	$final=str_replace("–","-",$final);
	$folder=strtolower(str_replace($buscados,$sustitut,str_replace($eliminar,"",$folder)));
	$folder=str_replace("–","-",$folder);
	$folder=str_replace("–","-",$folder);
	return $primary_folder . '/' . $folder . "/" . $id ."-" . $final;
}

function PatchDateGraph($fecha){
	if ($fecha!="") {
		preg_match("/(\d+)-(\d+)-(\d+)/", $fecha, $mifecha);
		$menos=$mifecha[2]-1;
		$lafecha=$mifecha[1].", ".$menos.", ".$mifecha[3];
		return $lafecha;
	} else {
		return false;
	}		
} 

function EuroScreenDate($fecha){
	if ($fecha!="") {
		$result=preg_match("/(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)/", $fecha, $mifecha);
		if ($result==1) { 
			$lafecha=$mifecha[3]."/".$mifecha[2]."/".$mifecha[1] . " " . $mifecha[4] . ":" . $mifecha[5]; // . ":" . $mifecha[6];
		} else {
			$result=preg_match("/(\d+)-(\d+)-(\d+)/", $fecha, $mifecha);
			$lafecha=$mifecha[3]."/".$mifecha[2]."/".$mifecha[1];
		}
		//$lafecha=$mifecha[3]."/".$mifecha[2]."/".$mifecha[1];
		return $lafecha;
	} else {
		return false;
	}
} 

function EuroScreenDateTime($fecha){
	if ($fecha!="") {	
		preg_match("/(\d+)-(\d+)-(\d+) (\d+):(\d+)/", $fecha, $mifecha);
		$lafecha=$mifecha[3]."/".$mifecha[2]."/".$mifecha[1]." ".$mifecha[4].":".$mifecha[5];
		return $lafecha;
	} else {
		return false;
	}
} 

function PutPaginator($page,$offset,$lastpage,$linkpage,$linkparams) {
	if ($linkparams!="") { $linkparams="&".$linkparams; }
	$page--;
	if ($lastpage>1) {
		echo '<ul class="vs-pagination">';
		$anteriorpag=$page;
		if ($anteriorpag<1) {$anteriorpag=1; }
		$siguientepag=$page+2;
		if ($siguientepag>$lastpage) {$siguientepag=$lastpage; }
		$iniciopag=$page-4;
		if ($iniciopag<0) {$iniciopag=0; }
		$finalpag=$iniciopag+9;
		if ($finalpag>$lastpage) {$finalpag=$lastpage; }
		if ($page!=0) { 
			echo "<li><a href=" . $linkpage . "?page=$anteriorpag&offset=$offset" . $linkparams . ">&lt;</a></li> ";
		}
		for($i=$iniciopag; $i<$finalpag; $i++) {
			$linkpag=$i+1;
			$clase="";
			if ($i==$page) { $clase=" class='active'"; }
			echo "<li " . $clase ."><a href=" . $linkpage . "?page=$linkpag&offset=$offset" . $linkparams . '>' . ($linkpag) . '</a></li> '; 
		} 
		if (($page+1)!=$lastpage) { 
			echo "<li><a href=" . $linkpage . "?page=$siguientepag&offset=$offset" . $linkparams . ">&gt;</a></li> ";
		}
		echo "</ul>";
	}
}

function StripMetatags($etiquetas, $enlace) {
	$metatags="";
	$listatags=explode(', ',$etiquetas);
	foreach($listatags as $elemento) {
		$metatags.="<a href='" . $enlace . urlencode($elemento) . "'>" . $elemento . "</a>, ";
	}
	$metatags=substr($metatags,0,strlen($metatags)-2);
	return $metatags;
}

function PutThumb($urlimage,$cache=1) {
	$ruta="";
	if ( !defined('InFrontEnd') )
		$ruta="../";
	$externo=strpos($urlimage,"http://");
	if ($externo!==false) {
		$devolver=$ruta."public/timthumb.php?src=".$urlimage; }
	else {
		$devolver=$ruta."public/thumbnails/" . $urlimage;
	}
	if($cache==0){$devolver.='?d='.rand(100,1000);}
	return $devolver;
}

function PutImage($urlimage,$cache=1){
	$ruta="";
	if ( !defined('InFrontEnd') )
		$ruta="../";		
	$externo=strpos($urlimage,"http://");
	if ($externo!==false) {
		$devolver=$ruta."public/timthumb.php?src=".$urlimage; }
	else {
		$devolver=$ruta."public/images/" . $urlimage;
	}
	if($cache==0){$devolver.='?d='.rand(100,1000);}
	return $devolver;
}

function PutImageGenerate($urlimage,$w=200,$h=150){
	$ruta = PutImage($urlimage);
	$ruta = "http://" . sitedomain ."/".$ruta;
	$devolver="public/timthumb.php?src=".$ruta."&w=".$w."&h=".$h;
	return $devolver;
}

function PutBanner($urlimage,$cache=1) {
	$ruta="";
	if ( !defined('InFrontEnd') )
		$ruta="../";
	$externo=strpos($urlimage,"http://");
	if ($externo!==false) {
		$devolver="public/timthumb.php?src=".$urlimage; }
	else {
		$devolver=$ruta."public/banners/" . $urlimage;
	}
	if($cache==0){$devolver.='?d='.rand(100,1000);}
	return $devolver;
}

function PutBannerGenerate($urlimage,$h=150,$w=200){
	$ruta = "public/banners/".$urlimage;
	$ruta = "http://" . sitedomain ."/".$ruta;
	$devolver="../public/timthumb.php?src=".$ruta."&h=".$h."&w=".$w;
	return $devolver;
}

function DeleteFile($urlfile) {
	$devolver=false;
	if ((file_exists($urlfile)) and (is_file($urlfile))) {
		unlink($urlfile);
		$devolver=true;
	}
	return $devolver;
}

function DeleteFolder($src) {
	$dir = opendir($src);
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
                DeleteFolder($src . '/' . $file); 
            } 
            else { 
            	chmod($src . '/' . $file, 0777);
                unlink($src . '/' . $file); 
            } 
        } 
    } 
    closedir($dir); 
    chmod($src, 0777);
    rmdir($src);
}

function FilesInFolder($primary_folder,$folder) {
	$output=array();
	if (is_file($primary_folder . '/' . $folder)) { return array($folder); }
	$files = scandir($primary_folder . '/' . $folder);
	if (count($files)>0) {
		foreach($files as $file) {
			if (($file!='.') and ($file!='..')) {
				if (is_file($primary_folder . '/' . $folder . '/' . $file)) {
					$output[]=$folder . '/' . $file;
				} else {
					$output=array_merge($output,FilesInFolder($primary_folder,$folder . '/' . $file));
				}
			}
		}
	}
	return $output;
}

function IsHostedFile($urlfile){
	$devolver=false;
	$externo=strpos($urlfile,"http://");
	if (($externo===false) and ($urlfile!="")) {
		if ((file_exists($urlfile)) and (is_file($urlfile))) {
			$devolver=true;
		}
	}
	return $devolver;
}

function url_exists($url) {
    $hdrs = @get_headers($url);
    return is_array($hdrs) ? preg_match('/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/',$hdrs[0]) : false;
} 

function KeyGen($longitud){
	$caracteres = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	@mt_srand(microtime() * 1000000);
	$clavepw="";
	for($i = 0; $i < $longitud; $i++)
	{
		$key = mt_rand(0,strlen($caracteres)-1);
		$clavepw = $clavepw . $caracteres{$key};
	}
	return $clavepw;
}

function LimitString($texto,$tamano) {
	if (strlen($texto)>$tamano) {
		$contador = 0; 
		$arrayTexto = explode(' ',$texto);
		$valores= count($arrayTexto);
		$texto = '';
		for ($contador=0;$contador<$valores;$contador++) {
			if ($tamano >= strlen($texto) + strlen($arrayTexto[$contador])) {
				$texto .= ' '.$arrayTexto[$contador];
			} else {
				break;
			}
		} 
		$texto.="...";
	}
	return $texto;
}

function AddImgResponsive($text) {
	$pattern ="/<img(.*?)class=\"(.*?)\"(.*?)>/i";
	$replacement = '<img$1class="$2 img-responsive"$3>';
	$text = preg_replace($pattern, $replacement, $text);
	return $text;
}

function SendMail($de_nombre, $para, $asunto, $mensaje, $de_mail, $cabeceras, $use_base=true, $adjunto=NULL) {
	if ($use_base) {
	   if ($fp=fopen(sitepath . "/basemails/base.html",'r')) {
	    $texto='';
	    while ($trozo = fgets($fp, 1024)){
	        $texto .= $trozo;
	    }              
		//Reemplazamos {{Body}} por el $mensaje;
	    $mensaje=str_replace('{{Body}}',($mensaje),$texto);     
	   }
	}              
	if ((file_exists(sitepath . '/lib/phpmailer/include.php')) and (siteUsePHPMailer)) {
		require_once(sitepath . '/lib/phpmailer/include.php');
		$devolver=SendPHPMail($de_mail, $de_nombre, $para, $para, $asunto, $mensaje, $adjunto);
	} else {
		$asunto="=?UTF-8?B?".base64_encode($asunto)."=?=";
		$random_hash = md5(date('r', time())); 
		$devolver=false;
		$from=$de_mail;
		if ($de_nombre!='') { $from=$de_nombre . ' <' . $from . '> '; }                     
		if (preg_match("/(.*)<(.*)>/", $from, $regs)) {$from = '=?UTF-8?B?'.base64_encode($regs[1]).'?= <'.$regs[2].'>';}
		$added=array();
		$headers="";
		$body="";
		$headers.= 'From: ' . $from . "\r\n"; 
		$headers.= 'Reply-To: ' . $de_mail . "\r\n";
		$headers.= 'Return-path: ' . $de_mail . "\r\n";
		$headers.= 'MIME-Version: 1.0' . "\r\n"; 
		if ($adjunto!==NULL) {
			$added=array();
			if (! is_array($adjunto)) {
				$tmp[]=$adjunto;
				$adjunto=$tmp;
			}
			foreach($adjunto as $file) {
				if ((is_file($file)) and (! isset($added[$file]))) {
					$body.="\r\n--PHP-mixed-" . $random_hash ."\r\n";
					$body.="Content-Type: Binary; name=\"" . basename($file) . "\"" . " \r\n"; 
					$body.="Content-Transfer-Encoding: base64 \r\n";
					$body.="Content-Disposition: attachment \r\n";
					$body.="\r\n";
					$body.=chunk_split(base64_encode(file_get_contents($file)));
					//$body.="--PHP-mixed-" . $random_hash ."--\r\n";
					$added[$file]=1;
				}
			}              
		}
		//Patch headers and body...
		if (count($added)>0) {
			$headers.= "Content-Type: multipart/mixed; charset=UTF-8; boundary=\"PHP-mixed-".$random_hash. "\"\r\n";
			$bodytext = "\r\n--PHP-mixed-" . $random_hash . "\r\n";
			$bodytext.= "Content-Type: text/html; charset=UTF-8\r\n";
			$bodytext.= "Content-Transfer-Encoding: 7bit\r\n\r\n";
			$bodytext.= $mensaje;
			$bodytext.= "\r\n\r\n";
			$body=$bodytext . $body;
			$body.= "--PHP-mixed-" . $random_hash . "--";
		} else {
			$headers.= "Content-Type: text/html; charset=UTF-8;\r\n";
			$body=$mensaje . $body;
		}
		if ($cabeceras!=0) { 
			//file_put_contents("d:\\email.eml", $headers . $body);
			$devolver=mail($para, $asunto, $body, $headers); 
		} else {
			$mensaje=str_replace("<br>","\n ", $mensaje);
			$mensaje=str_replace('<br/>',"\n ", $mensaje);
			$mensaje=str_replace("<br />","\n ", $mensaje);
			$devolver=mail($para, $asunto, strip_tags($mensaje)); 
		}
	}
	return $devolver;
}


function orderMultiDimensionalArray ($toOrderArray, $field, $inverse = false) {
	$position = array();
	$newRow = array();
	foreach ($toOrderArray as $key => $row) {
			$position[$key]  = $row[$field];
			$newRow[$key] = $row;
	}
	if ($inverse) {
		arsort($position);
	}
	else {
		asort($position);
	}
	$returnArray = array();
	foreach ($position as $key => $pos) {     
		$returnArray[] = $newRow[$key];
	}
	return $returnArray;
}

function GetUrlScript($cad){
	//extracción del nombre junto a la extensión
	$f = explode("/", $cad);
	$arch=$f[count($f)-1];
	return $arch;
}

function ChangeWidthEmbed($embed,$new_width,$new_height) {
	$salida=str_replace("http://", siteprotocol, $embed);
	$salida=str_replace("youtu.be/", "youtube.com/watch?v=", $salida);
	$salida=preg_replace('#width="[0-9]+"#','width="' . $new_width . '"',$salida);
	$salida=preg_replace('#height="[0-9]+"#','height="' . $new_height . '"',$salida);	
	return $salida;
}

function CleanXSS($html) {
	//Prevent XSS
	$html=preg_replace("#<.*?script.*?>.*?<\/.*?script.*?>#", "", $html);
	return $html;
}

function CleanHTML($html){
	$html=CleanXSS($html);
	$clean_tags='<b><strong><i><u><strike><sup><sub><a><embed><iframe><p><q><br><img><div><span><li><ul><ol><h1><h2><h3><h4><h5><h6>';
	$clean_attributes="#<([^>]*)(class|lang|style|size|face)=(\"[^\"]*\"|'[^']*'|[^>]+)([^>]*)>#";
	if (defined("siteWYSIWYGTagsEnabled")) { $clean_tags=siteWYSIWYGTagsEnabled; }
	if (defined("siteWYSIWYGCleanAttributes")) { $clean_attributes=preg_quote("#<([^>]*)(" . siteWYSIWYGCleanAttributes . "=(\"[^\"]*\"|'[^']*'|[^>]+)([^>]*)>#"); }
    $html=strip_tags($html , $clean_tags);
	
	$html = preg_replace("#<(/)?(font|del|ins)[^>]*>#","",$html);
	$html = preg_replace($clean_attributes,"<\\1>",$html);
	$html = preg_replace($clean_attributes,"<\\1>",$html);	
	return $html;
}

function AutoLink($text) {
// pad it with a space so we can match things at the start of the 1st line.
        $ret = ' ' . $text;
         
        // matches an "xxxx://yyyy" URL at the start of a line, or after a space.
        // xxxx can only be alpha characters.
        // yyyy is anything up to the first space, newline, comma, double quote or <
        $ret = preg_replace("#([\t\r\n ])([a-z0-9]+?){1}://([\w\-]+\.([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*)?)#i", '\1<a href="\2://\3" target="_blank">\2://\3</a>', $ret);
         
        // matches a "www|ftp.xxxx.yyyy[/zzzz]" kinda lazy URL thing
        // Must contain at least 2 dots. xxxx contains either alphanum, or "-"
        // zzzz is optional.. will contain everything up to the first space, newline, 
        // comma, double quote or <.
        $ret = preg_replace("#([\t\r\n ])(www|ftp)\.(([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*)?)#i", '\1<a href="http://\2.\3" target="_blank">\2.\3</a>', $ret);
         
        // matches an email@domain type address at the start of a line, or after a space.
        // Note: Only the followed chars are valid; alphanums, "-", "_" and or ".".
        $ret = preg_replace("#([\n ])([a-z0-9\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $ret);
         
        // Remove our padding..
        $ret = substr($ret, 1);
         
        return($ret);
}

function FormatPlainText($texto) {
	$texto=AutoLink($texto);
	return nl2br($texto);
}

function fetchUrl($url){
	 $ch = curl_init();
	 curl_setopt($ch, CURLOPT_URL, $url);
	 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	 curl_setopt($ch, CURLOPT_TIMEOUT, 20);
 
	 $retData = curl_exec($ch);
	 curl_close($ch); 
 
	 return $retData;
}

function CopyExternalResource($url_origen,$archivo_destino) {  
	//$url_origen='http://graph.facebook.com/' . $id . '/picture?type=large';
	//Obtener extensión...
	$mi_curl = curl_init ($url_origen);  
	$fs_archivo = fopen ($archivo_destino, "w");  
	curl_setopt ($mi_curl, CURLOPT_FILE, $fs_archivo);  
	curl_setopt ($mi_curl, CURLOPT_HEADER, 0);  
	curl_setopt ($mi_curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_exec ($mi_curl);  
	$error=curl_error($mi_curl);
	curl_close ($mi_curl);
	fclose ($fs_archivo);
	if ($error=="") {
	  	chmod($archivo_destino, 0777);
		return true;
	} else {
		unlink($archivo_destino);
		return false;
	}
} 

function GetNearThisGeo($geolocation,$margin=0.003,$field="Geolocation") {
	//Desgranamos la Dirección...
	$geo=str_replace('(','',$geolocation);
	$geo=substr($geo,0,strpos($geo,')')-1);
	$partes=explode(',',$geo);
	settype($partes[0],'double');
	settype($partes[1],'double');
	settype($margin,'double');
	$sql=" AND " . $field . "<>''";
	$sql.=" AND CONVERT(SUBSTRING(" . $field . ",2,LOCATE(','," . $field . ")-2),BINARY) BETWEEN " . str_replace(",",".",$partes[0]-$margin) . " AND " . str_replace(",",".",$partes[0]+$margin);
	$sql.=" AND CONVERT(SUBSTRING_INDEX(SUBSTRING(" . $field . ",LOCATE(','," . $field . ")+1),')',1),BINARY) BETWEEN " . str_replace(",",".",$partes[1]-$margin) . " AND " . str_replace(",",".",$partes[1]+$margin);
	return $sql;
}

function FormatVersion($version) {
	$salida="";
	$partes=explode('.',$version);
	foreach ($partes as $cnt=>$parte) {
		$salida.=str_pad($parte,4,'0', STR_PAD_LEFT);
		if ($cnt<(count($partes)-1)) { $salida.="."; }
	}
	return $salida;
}

function get_php_classes($php_code) {
	$classes = array();
	$tokens = token_get_all($php_code);
	$count = count($tokens);
	for ($i = 2; $i < $count; $i++) {
		if (   $tokens[$i - 2][0] == T_CLASS
		    && $tokens[$i - 1][0] == T_WHITESPACE
		    && $tokens[$i][0] == T_STRING) {

		    $class_name = $tokens[$i][1];
		    $classes[] = $class_name;
		}
	}
	return $classes;
}

function EvaluatePHP($query="") {
	if ($query!="") {
		try {
			$constructor="if (" . $query . ") { return true; } else { return false; }";
			//echo $constructor;
			return eval($constructor);
		} catch (Exception $e) {
			$error=$e->getMessage();
			return false;
		}
	} else {
		return true;
	}
}

function GetColAlphabet($id) {
	$alphabet="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$column='';
	$prefix=floor($id/strlen($alphabet));
	$letter=$id;
	if ($prefix>0) { 
		$column=$alphabet[intval($prefix-1)]; 
		$letter=$letter-($prefix*strlen($alphabet)); 
	}
	$column.=$alphabet[intval($letter)];	
	return $column;
}

function ExpandGeo($text="",&$salida) {
	if ($text!="") {
		$pos=strpos($text,'(');
		if ($pos!==false) { $text=substr($text,1); }
		$pos=strpos($text,')');
		if ($pos!==false) { 
			$temp=substr($text,0,$pos-1); 
			$salida['GeoZoom']=substr($text,$pos+1); 
			$salida['Geolocation']=$temp;
			}
		if ($salida['GeoZoom']=="") { $salida['GeoZoom']=14; }
		$salida['Geolocation']=str_replace(' ','',$salida['Geolocation']);
		$partes=explode(",", $salida['Geolocation']);
		$salida['GeoLat']="";
		$salida['GeoLng']="";
		if (count($partes)>=2) {
			$salida['GeoLat']=$partes[0];
			$salida['GeoLng']=$partes[1];
		}
	}
}

function GetAdminLink($module,$url) {
	$salida=$module ."/";
	$query_string=$url;
	$qpos=strpos($query_string, "?");
	if ($qpos!==false) { $query_string=substr($query_string, $qpos+1); }
	if ($query_string!="") {
		$params=explode("&",$query_string);
		if (count($params)>0) {
			foreach($params as $param) {
				$parts=explode("=",$param);
				if (count($parts)==2) {
					$field=$parts[0];
					$value=$parts[1];
					if (($field=="text") or ($field=="error")) { $value=urlencode(base64_encode($value)); }
					$salida.=$field . "/" . $value . "/";
				}
			}
		}
	}
	return $salida;
}

function GetParamsAdminLink($url,&$params) {
	$params=$_GET;
	$module="home";
	if ($url!=="") {
		if ($url[strlen($url)-1]=="/") { $url=substr($url,0,strlen($url)-1);}
		$get=explode("/",$url);
		$init=0;
		$max=count($get);
		if (count($get>0)) {
			$module=$get[0];
			$init++;
		}
		//Si el número de parámetros que hay son pares hay acción definida
		if (count($get)%2==0) {
			//Es impar, hay acción
			$params['action']=$get[$init];
			$init++;
		}
		//Ahora ya solo quedan los parámetros, en formato par campo valor
		$field="";
		while ($init<=($max-1)) {
			if ($field=="") { 
				$field=$get[$init]; 
			} else {
				$params[$field]=$get[$init];
				if (($field=="text") or ($field=="error")) {
					$params[$field]=urldecode(base64_decode($get[$init]));
				}
				$field="";
			}
			$init++;
		}
	}
	return $module;
}

function formatSizeUnits($bytes){
    if ($bytes >= 1073741824){ $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576){ $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024){ $bytes = number_format($bytes / 1024, 2) . ' kB';
	} elseif ($bytes > 1){ $bytes = $bytes . ' bytes';
	} elseif ($bytes == 1) { $bytes = $bytes . ' byte';
    } else { $bytes = '0 bytes';
    }
    return $bytes;
}

function CheckParamValid($value,$type="id") {
	$valid=false;
	switch ($type) {
		case 'id':
			if (preg_match("/^[a-f0-9]{32}$/i",$value)) { 
				$valid=$value; 
			} else {
				if (preg_match("/^[0-9]{1,}$/",$value)) { 
					$valid=intval($value); }
			}
			break;
		case 'integer':
			if (preg_match("/^[0-9]{1,}$/",$value)) { $valid=intval($value); }
			break;
		case 'date':
			if (preg_match("/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/",$value)) { $valid=$value; }
			break;
		default:
			return $value;
			break;
	}
	return $valid;
}

function getBrowser() {
	if (isset($_SERVER['HTTP_USER_AGENT'])) {
		$u_agent=$_SERVER['HTTP_USER_AGENT'];
		if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) {  return "Internet Explorer"; }
		if(preg_match('/Firefox/i',$u_agent)) { return "Firefox"; }
		if(preg_match('/Chrome/i',$u_agent)) { return "Chrome"; }
		if(preg_match('/Safari/i',$u_agent)) { return "Safari"; }
		if(preg_match('/Opera/i',$u_agent)) { return "Opera"; }
		if(preg_match('/Netscape/i',$u_agent)) { return "Netscape"; }
	}
	return "";
}
?>