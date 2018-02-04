<?php
//ini_set("display_errors", 1);
if (! defined("sitepath")) { define("sitepath", "../"); }
require_once sitepath . 'include/core/functions.php';
require sitepath . 'lib/minimizer/jsmin.class.php';
require sitepath . 'lib/minimizer/cssmin.class.php';

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


function GetJSMin($template="",$folder="js",$force=false,$inline=false) {
    $enable_gen=$force;
    $oldfile="";
    if (is_array($folder)) {
        $fld="tmp";
    } else {
        $fld=$folder;
    }
    $files_in_cache=glob(sitepath . "public/cache/" . $template . "-" . stripfilename($fld) . "-*.js");
    if ((count($files_in_cache)>0) and (is_array($files_in_cache))) {
        sort($files_in_cache);
        $files_in_cache=array_reverse($files_in_cache);
        $oldfile=basename($files_in_cache[0]);
        $filedate=date("Y-m-d", filemtime(sitepath . "public/cache/" . $oldfile));
        if ($filedate<date("Y-m-d")) { $enable_gen=true; }
    } else {
        $enable_gen=true;
    }
    if ($enable_gen) {
        $cachables=array();
        $count=0;
        if (! is_array($folder)) {
            //Carga de origen desde carpeta...
            $js_origin_folder=sitepath . "templates/" . $template . "/" . $folder . "/";
            $directorio=opendir($js_origin_folder);
            while ($file = readdir($directorio)) {
                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if ($extension == 'js') {
                    $cachables[]=$js_origin_folder . $file;
                    $count++;
                    //$buffer[]=file_get_contents($js_origin_folder . $file);
                }
            }
        } else {
            //Carga de origen desde lista de archivos...
            foreach($folder as $file) {
                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if ($extension == 'js') {
                    $cachables[]=$file;
                    $count++;
                    //$buffer[]=file_get_contents($file);
                }
            }
        }
        if ($count>0) {
            sort($cachables);
            foreach($cachables as $file) {
                $buffer[]=file_get_contents($file);
            }
        }
        $output = JSMin::minify(implode(";\n", $buffer));
        if ($inline) {
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
            return;
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
            if (is_array($folder)) {$folder="tmp"; }
            $file=$template . "-" . stripfilename($folder) . "-" . date('YmdHis') . ".js";
            $saved=file_put_contents(sitepath . "public/cache/" . $file,$output);
            //Si se guardó, borramos los antiguos archivos no necesarios.
            if ($saved!==false) {
                if ((count($files_in_cache)>0) and (is_array($files_in_cache))) {
                    foreach($files_in_cache as $delfile) { unlink($delfile); }
                }
                $oldfile=$file;
            }
        } 
    }
    return "public/cache/" . $oldfile;
}


function GetCSSMin($template="",$folder="css",$force=false,$inline=false,$update_url=true) {
    $enable_gen=$force;
    $oldfile="";
    if (is_array($folder)) {
        $fld="tmp";
    } else {
        $fld=$folder;
    }
    $files_in_cache=glob(sitepath . "public/cache/" . $template . "-" . stripfilename($fld) . "-*.css");
    if ((count($files_in_cache)>0) and (is_array($files_in_cache))) {
        sort($files_in_cache);
        $files_in_cache=array_reverse($files_in_cache);
        $oldfile=basename($files_in_cache[0]);
        $filedate=date("Y-m-d", filemtime(sitepath . "public/cache/" . $oldfile));
        if ($filedate<date("Y-m-d")) { $enable_gen=true; }
    } else {
        $enable_gen=true;
    }
    if ($enable_gen) {
         $cachables=array();
         $count=0;
        if (! is_array($folder)) {
            $css_origin_folder=sitepath . "templates/" . $template . "/" . $folder . "/";
            //Carga de origen desde carpeta...
            $css_origin_folder=sitepath . "templates/" . $template . "/" . $folder . "/";
            $directorio=opendir($css_origin_folder);
            while ($file = readdir($directorio)) {
                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if ($extension == 'css') {
                    $cachables[]=$css_origin_folder . $file;
                    $count++;

                    // $fdata=file_get_contents($css_origin_folder . $file);
                    // if ($update_url) {
                    //     $search='/url\(["\']?(?![http|data])([^"\']+)["\']?\)/i';
                    //     $replace='url("../../templates/' . $template . '/' . $folder . '/$1")';
                    //     //Dividimos el contenido para evitar errores preg_replace
                    //     $partial_content=explode("\n",$fdata);
                    //     foreach($partial_content as $part) {
                    //         $buffer[]=preg_replace($search,$replace,$part);
                    //     }
                    // } else {
                    //     $buffer[] = $fdata;
                    // }
                }
            }
        } else {
            //Carga de origen desde lista de archivos...
            foreach($folder as $file) {
                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if ($extension == 'css') {
                    $cachables[]=$file;
                    $count++;

                    // $fdata=file_get_contents($file);
                    // if ($update_url) {
                    //     $search='/url\(["\']?(?![http|data])([^"\']+)["\']?\)/i';
                    //     $replace='url("../../templates/' . $template . '/' . $folder . '/$1")';
                    //     //Dividimos el contenido para evitar errores preg_replace
                    //     $partial_content=explode("\n",$fdata);
                    //     foreach($partial_content as $part) {
                    //         $buffer[]=preg_replace($search,$replace,$part);
                    //     }
                    // } else {
                    //     $buffer[] = $fdata;
                    // }
                }
            }
        }
        if ($count>0) {
            sort($cachables);
            foreach($cachables as $file) {
                $fdata=file_get_contents($file);
                if ($update_url) {
                    $search='/url\(["\']?(?![http|data])([^"\']+)["\']?\)/i';
                    $replace='url("../../templates/' . $template . '/' . $folder . '/$1")';
                    //Dividimos el contenido para evitar errores preg_replace
                    $partial_content=explode("\n",$fdata);
                    foreach($partial_content as $part) {
                        $buffer[]=preg_replace($search,$replace,$part);
                    }
                } else {
                    $buffer[] = $fdata;
                }
            }
        }
        $output = CssMin::minify(implode("", $buffer));
        //$output = implode("\n", $buffer);
        if ($inline) {
            //Imprimimos el archivo sin mas.
            header("Content-type: text/css; charset: UTF-8");
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
            return;
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
            if (is_array($folder)) {$folder="tmp"; }
            $file=$template. "-" . stripfilename($folder) . "-" . date('YmdHis') . ".css";
            $saved=file_put_contents(sitepath . "public/cache/" . $file,$output);
            //Si se guardó, borramos los antiguos archivos no necesarios.
            if ($saved!==false) {
                if ((count($files_in_cache)>0) and (is_array($files_in_cache))) {
                    foreach($files_in_cache as $delfile) { unlink($delfile); }
                }
                $oldfile=$file;
            }
        } 
    }
    return "public/cache/" . $oldfile;
}
?>