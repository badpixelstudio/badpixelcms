<?php
//USO:
//Realizar llamada desde <script language="javascript" src="lib/jsmin">
//
// Puede ejecutarse con los parametros:
// lib/jsmin?force Para forzar el refresco de la caché
// lib/jsmin?inline para ver el contenido del archivo, forzando el proceso pero NO guarda el resultado en la caché
// lib/jsmin?folder=<carpeta> indicando la carpeta de templates/<plantilla_actual>/<carpeta>

require '../../include/core/common.php';
require sitepath . 'lib/jsmin/jsmin.class.php';
require_once(sitepath . "/include/core/core.class.php");

function checkCanGzip(){
    if (array_key_exists('HTTP_ACCEPT_ENCODING', $_SERVER)) {
        if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) return "gzip";
        if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) return "x-gzip";
    }
    return false;
}

function gzDocOut($contents, $level=6){
    $return = array();
    $return[] = "\x1f\x8b\x08\x00\x00\x00\x00\x00";
    $size = strlen($contents);
    $crc = crc32($contents);
    $contents = gzcompress($contents,$level);
    $contents = substr($contents, 0, strlen($contents) - 4);
    $return[] = $contents;
    $return[] = pack('V',$crc);
    $return[] = pack('V',$size);
    return implode(null, $return);
}

//inicialiazamos Core para tener datos sobre el template actual.
$Core=new Core($_GET);
$enable_gen=false;
$force_output=false;
$folder="js";
if (isset($_GET['force'])) { $enable_gen=true; }
if (isset($_GET['inline'])) { $enable_gen=true; $force_output=true; }
if (isset($_GET['folder'])) { $folder=$_GET['folder']; }

$js_origin_folder=sitepath . "templates/" . $Core->template . "/" . $folder . "/";
//Buscamos si existe un js en caché
$oldfile="";
$files_in_cache=glob(sitepath . "public/cache/" . $Core->template . "-" . stripfilename($folder) . "-*.js");
if (count($files_in_cache)>0) {
    sort($files_in_cache);
    $files_in_cache=array_reverse($files_in_cache);
    $oldfile=basename($files_in_cache[0]);
    $filedate=date("Y-m-d", filemtime(sitepath . "public/cache/" . $oldfile));
    if ($filedate<date("Y-m-d")) { $enable_gen=true; }
} else {
    $enable_gen=true;
}

//Generamos el nuevo js
if ($enable_gen) {
    // $ite = new RecursiveDirectoryIterator($js_origin_folder);
    // foreach(new RecursiveIteratorIterator($ite) as $file => $fileInfo) {
    //     $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    //     if ($extension == 'js') {
    //         $f = $fileInfo->openFile('r');
    //         $fdata = "";
    //         while ( ! $f->eof()) {
    //             $fdata .= $f->fgets();
    //         }
    //         $buffer[] = $fdata;
    //     }
    // }

    $directorio=opendir($js_origin_folder);
    $files=array();
    $count=0;
    while ($file = readdir($directorio)) {
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if ($extension == 'js') {
            $files[]=$file;
            $count++;
            //$buffer[]=file_get_contents($js_origin_folder . $file);
            //$buffer[] = $fdata;
        }
    }
    if ($count>0) {
        sort($files);
        foreach($files as $file) {
            $buffer[]=file_get_contents($js_origin_folder . $file);
            //$buffer[] = $fdata;
        }
    }


    $output = JSMin::minify(implode(";\n", $buffer));

    if ($force_output) {
        //Imprimimos el archivo sin mas.
        header("Content-type: application/x-javascript; charset: UTF-8");
        $forceGz    = filter_input(INPUT_GET, 'gz', FILTER_SANITIZE_STRING);
        $forcePlain = filter_input(INPUT_GET, 'plain', FILTER_SANITIZE_STRING);
        $encoding = checkCanGzip();
        if ($forceGz) {
            header("Content-Encoding: {$encoding}");
            echo gzDocOut($output);
        } elseif ($forcePlain) {
            echo $output;
        } else {
            if ($encoding){
                header("Content-Encoding: {$encoding}");
                echo GzDocOut($output);
            } else {
                echo $output;
            }
        }
        exit;
    }


    //Comprobamos el archivo anterior...
    $save=true;
    if (is_file(sitepath . "public/cache/" . $oldfile)) {
        $old_content=file_get_contents(sitepath . "public/cache/" . $oldfile);
        if ($old_content==$output) {
            //Touch al archivo, para evitar que se vuelva a ejecutar este minificador en las proximas 24 horas.
            touch(sitepath . "public/cache/" . $oldfile);
            $save=false;
        } 
    }
    if ($save) {
        //Guardamos el nuevo archivo
        $file=siteTemplate . "-" . stripfilename($folder) . "-" . date('YmdHis') . ".js";
        $saved=file_put_contents(sitepath . "public/cache/" . $file,$output);
        //Si se guardó, borramos los antiguos archivos no necesarios.
        if ($saved!==false) {
            if (count($files_in_cache)>0) {
                foreach($files_in_cache as $delfile) { unlink($delfile); }
            }
            $oldfile=$file;
        }
    } 
}
echo "public/cache/" . $oldfile;
// echo 'var script = document.createElement("script");';
// echo 'script.type = "text/javascript";';
// echo 'script.src = "public/cache/' . $oldfile . '";';
// echo 'document.getElementsByTagName("head")[0].appendChild(script);';


//echo '<script type="text/javascript" src="public/cache/' . $oldfile . '"></script>';

?>