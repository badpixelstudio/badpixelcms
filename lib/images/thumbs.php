<?php
//Clase Thumbs
//Propiedad de BadPixel Studio
//Escrito por Israel García
//Basado en código de Jarrod Oberto.
//Fecha de creación 28/Marzo/2013
//Revisión 16/12/2013: Añadida la transparencia en PNGs si el background se deja en blanco.

class Thumbs {
	// *** Class variables
	private $filename;
	public $Options;
	private $image;
	private $imageWidth;
	private $imageHeight;
	private	$newWidth;
	private	$newHeight;
	private	$newBackground;			
	
	private $imageResized;

	function __construct(){
		
	}

	function openImage($file) {	
		$this->filename=$file;		
		$partes=explode('.',$file);
		$extension=strtolower($partes[count($partes)-1]);	
		
		switch($extension) {
			case 'jpg':
			case 'jpeg':
				$this->image = @imagecreatefromjpeg($file);
				$this->type="jpg";
				break;
			case 'gif':
				$this->image = @imagecreatefromgif($file);
				$this->type="gif";
				break;
			case 'png':
				$this->image = @imagecreatefrompng($file);
				$this->type="png";
				break;
			default:
				$this->image = false;
				break;
		}
		$this->imageWidth  = imagesx($this->image);
		$this->imageHeight = imagesy($this->image);
	}

	public function resizeImage($newWidth, $newHeight, $background="") {
		//Si el Width o Height es un rango, elegimos aleatoriamente
		if (strpos($newWidth,'-')!==false) {
			$partes=explode('-',$newWidth);
			$newWidth=rand($partes[0],$partes[1]);
		}
		if (strpos($newHeight,'-')!==false) {
			$partes=explode('-',$newHeight);
			$newHeight=rand($partes[0],$partes[1]);
		}
		
		$crop="";
		if (strpos($background,"crop")!==false) {
			$crop="crop";
			$background=str_replace("crop","",$background);
		}
		$optionArray = $this->getDimensions($newWidth, $newHeight, $crop);
		$optimalWidth  = $optionArray['optimalWidth'];
		$optimalHeight = $optionArray['optimalHeight'];
		$optimalLeft=intval(($this->newWidth-$optimalWidth)/2);
		$optimalTop=intval(($this->newHeight-$optimalHeight)/2);
		$this->imageResized = imagecreatetruecolor($this->newWidth, $this->newHeight);
		$bg="0x000000";
		if (strlen($background)==6) { $bg=("0x" . $background); }
		$rgb = $this->hexrgb($bg);
		$bgc = imagecolorallocate ($this->imageResized, $rgb['red'], $rgb['green'], $rgb['blue']);
		if (($this->type=="png") and ($background=="")) { imagealphablending($this->imageResized, false); $bgc=imagecolorallocatealpha($this->imageResized, 0, 0, 0, 127); }
		imagefilledrectangle ($this->imageResized, 0, 0, $this->newWidth, $this->newHeight, $bgc);
		imagecopyresampled($this->imageResized, $this->image, $optimalLeft, $optimalTop, 0, 0, $optimalWidth, $optimalHeight, $this->imageWidth, $this->imageHeight);
		if (($this->type=="png") and ($background=="")) { imagesavealpha($this->imageResized, true); }
	}
	
	public function cropImage($fromx,$fromy,$width,$height) {
		$im=imagecreatetruecolor($width,$height);
		imagecopy($im, $this->imageResized, 0, 0, $fromx, $fromy, $width, $height);
		$this->imageResized=$im;
	}

	private function getDimensions($newWidth, $newHeight, $crop="") {				
		$this->newWidth=$newWidth;
		$this->newHeight=$newHeight;
		$option="fit";
		if (($this->newWidth==0) and ($this->newHeight==0)) {
			$this->newWidth=$this->imageWidth;
			$this->newHeight=$this->imageHeight;
			return array('optimalWidth' => $this->imageWidth, 'optimalHeight' => $this->imageHeight);
		}				
		if ($this->newWidth==0) { $option="portrait"; }
		if ($this->newHeight==0) { $option="landscape"; }
		if (($this->newWidth!=0) and ($this->newHeight!=0) and ($crop=="crop")) { $option="crop"; }
		switch ($option) {
			case 'portrait':
				$optimalWidth = $this->getSizeByHeight($this->newHeight);
				$optimalHeight= $newHeight;
				$this->newWidth=$optimalWidth;
				$this->newHeight=$optimalHeight;
				break;
			case 'landscape':
				$optimalWidth = $newWidth;
				$optimalHeight= $this->getSizeByWidth($this->newWidth);
				$this->newWidth=$optimalWidth;
				$this->newHeight=$optimalHeight;						
				break;
			case 'fit':
				$optionArray = $this->getSizeByFit($newWidth, $newHeight);
				$optimalWidth = $optionArray['optimalWidth'];
				$optimalHeight = $optionArray['optimalHeight'];				
				break;
			case 'crop':
				$optionArray = $this->getOptimalCrop($newWidth, $newHeight);
				$optimalWidth = $optionArray['optimalWidth'];
				$optimalHeight = $optionArray['optimalHeight'];
				break;
		}
		return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
	}

	private function getSizeByHeight($newHeight) {
		$ratio = $this->imageWidth / $this->imageHeight;
		$newWidth = $newHeight * $ratio;
		return $newWidth;
	}

	private function getSizeByWidth($newWidth){
		$ratio = $this->imageHeight / $this->imageWidth;
		$newHeight = $newWidth * $ratio;
		return $newHeight;
	}

	private function getSizeByFit(){
		$RestHeight=$this->newHeight-$this->getSizeByWidth($this->newWidth);
		$RestWidth=$this->newWidth-$this->getSizeByHeight($this->newHeight);
		if (($RestWidth<$RestHeight) and ($RestWidth>0)) {
			$optimalWidth=$this->getSizeByHeight($this->newHeight);
			$optimalHeight=$this->newHeight;
		} else {
			$optimalWidth=$this->newWidth;
			$optimalHeight=$this->getSizeByWidth($this->newWidth);					
		}
		return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
	}

	## --------------------------------------------------------

	private function getOptimalCrop($newWidth, $newHeight)
	{

		$heightRatio = $this->imageHeight / $newHeight;
		$widthRatio  = $this->imageWidth /  $newWidth;

		if ($heightRatio < $widthRatio) {
			$optimalRatio = $heightRatio;
		} else {
			$optimalRatio = $widthRatio;
		}

		$optimalHeight = $this->imageHeight / $optimalRatio;
		$optimalWidth  = $this->imageWidth  / $optimalRatio;

		return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
	}
	
	public function getCrop($origin,$destination,$image,$from_cropx,$from_cropy,$cropwidth,$cropheight,$imagewidth,$imageheight) {
		$this->openImage(sitepath . $origin . $image);
		$this->resizeImage($imagewidth,$imageheight);
		$this->cropImage($from_cropx,$from_cropy,$cropwidth,$cropheight);
		$this->saveImage(sitepath . $destination . $image,100);
	}
	
	public function putWatermark($position="center",$repetitions=1,$margin=0,$watermark="") {
		if ($watermark=="") { $watermark=sitepath . "lib/images/water.png"; }
		$partes=explode('.',$this->filename);
		$extension=strtolower($partes[count($partes)-1]);	

		$wm = imagecreatefrompng($watermark);
		$width= imagesx($wm);
		$height= imagesy($wm);
		
		switch ($position){
			case "center" :
				imagecopy($this->imageResized, $wm, ($this->newWidth-$width)/2, ($this->newHeight-$height)/2, 0, 0, $width, $height);
				break;			
			case "topleft" :
				imagecopy($this->imageResized, $wm, $margin, $margin, 0, 0, $width, $height);
				break;								
			case "topright" :
				imagecopy($this->imageResized, $wm, ($this->newWidth-$width-$margin), $margin, 0, 0, $width, $height);
				break;	
			case "topcenter" :
				imagecopy($this->imageResized, $wm, ($this->newWidth-$width)/2, $margin, 0, 0, $width, $height);
				break;								
			case "bottomleft" :
				imagecopy($this->imageResized, $wm, $margin, $this->newHeight-$height-$margin, 0, 0, $width, $height);
				break;								
			case "bottomright" :
				imagecopy($this->imageResized, $wm, ($this->newWidth-$width-$margin), ($this->newHeight-$height-$margin), 0, 0, $width, $height);
				break;	
			case "bottomcenter" :
				imagecopy($this->imageResized, $wm, ($this->newWidth-$width)/2, ($this->newHeight-$height-$margin), 0, 0, $width, $height);
				break;	
		}
		if($repetitions==1) {
			$waterless = imagesx($this->imageResized) - imagesx($wm);
			$rest = ceil($waterless/imagesx($wm)/2);
		
			for($n=1; $n<=$rest; $n++) {
				imagecopy($this->imageResized, $wm, ((imagesx($this->image)/2)-(imagesx($wm)/2))-(imagesx($wm)*$n), (imagesy($this->imageResized)/2)-(imagesy($wm)/2), 0, 0, imagesx($iwm), imagesy($wm));
				imagecopy($this->imageResized, $wm, ((imagesx($this->image)/2)-(imagesx($wm)/2))+(imagesx($wm)*$n), (imagesy($this->imageResized)/2)-(imagesy($wm)/2), 0, 0, imagesx($wm), imagesy($wm));
			}
		}				
		imagedestroy($wm);
	}

	public function saveImage($savePath, $imageQuality="100"){
		$partes=explode('.',$savePath);
		$extension=strtolower($partes[count($partes)-1]);	
		
		switch($extension) {
			case 'jpg':
			case 'jpeg':
				if (imagetypes() & IMG_JPG) {
					imagejpeg($this->imageResized, $savePath, $imageQuality);
				}
				break;
			case 'gif':
				if (imagetypes() & IMG_GIF) {
					imagegif($this->imageResized, $savePath);
				}
				break;
			case 'png':
				$scaleQuality = round(($imageQuality/100) * 9);
				$invertScaleQuality = 9 - $scaleQuality;
				if (imagetypes() & IMG_PNG) {
					 imagepng($this->imageResized, $savePath, $invertScaleQuality);
				}
				break;
			default:
				imagejpeg($this->imageResized, NULL, $imageQuality);
				break;
		}
		imagedestroy($this->imageResized);
	}
	
	public function flushImage($imageQuality="100"){
		imagejpeg($this->imageResized, NULL, $imageQuality);
		imagedestroy($this->imageResized);			
	}

	private function hexrgb ($hexstr){
		$int = hexdec($hexstr);
		return array("red" => 0xFF & ($int >> 0x10),"green" => 0xFF & ($int >> 0x8),"blue" => 0xFF & $int);
	}
		
	function Process($image,$option) {
		//Parcheamos...
		if (isset($option['folder'])) {
			if ($option['folder']=="") { $option['folder']="images"; }
		} else { 
			$option['folder']="images";
		}
		if (isset($option['width'])) {
			if ($option['width']=="") { $option['width']="0"; }
		} else { 
			$option['width']="0";
		}
		if (isset($option['height'])) {
			if ($option['height']=="") { $option['height']="0"; }
		} else { 
			$option['height']="0";
		}
		if (! isset($option['background'])) { $option['background']="#FFFFFF"; }
		if (! isset($option['wm_pos'])) { $option['wm_pos']=""; }
		if (! isset($option['wm_rep'])) { $option['wm_rep']="1"; }
		if (! isset($option['wm_image'])) { $option['wm_image']=""; }
		if (! isset($option['wm_margin'])) { $option['wm_margin']="0"; }
		
//		$this->openImage(sitepath . "public/temp/" . $image);
		$this->resizeImage($option['width'], $option['height'], $option['background']);
		if ($option['wm_pos']!="") {
			$this->putWatermark($option['wm_pos'],$option['wm_rep'],$option['wm_image'],$option['wm_margin']);
		}
		if (! is_dir(sitepath . "public/" . $option['folder'])) {
			mkdir(sitepath . "public/" . $option['folder']);
			chmod(sitepath . "public/" . $option['folder'], 0777);
		}
		$this->saveImage(sitepath . "public/" . $option['folder'] . "/" . $image, 100);
	}

	function NotProcess($image,$option) {
		//Parcheamos...
		if (isset($option['folder'])) {
			if ($option['folder']=="") { $option['folder']="images"; }
		} else { 
			$option['folder']="images";
		}
		copy(sitepath . "public/temp/" . $image,sitepath . "public/" . $option['folder'] . "/" . $image);
	}
	 
	
	//MANEJAR SUBIDA DE ARCHIVOS
	function UploadImage($subido,$params=NULL,$process=true){
		//Si no le hemos pasado opciones, cogemos las cargadas por defecto
		if ($params == NULL) { $params=$this->Options; }
		
		$this->openImage(sitepath . "public/temp/" . $subido);
		//Procesamos la imagen como tantas versiones haya definidas
		if (count($params)>0) {
			foreach($params as $version) {
				if ($process) {
					$this->Process($subido,$version);
				} else {
					$this->NotProcess($subido,$version);
				}
			}
		}
		unlink(sitepath . "/public/temp/" . $subido);
	}
	
	function GetOptionsImages($configuracion) {
		unset($this->options);
		$this->options=GetOptionsImages($configuracion);
		return $this->options;	
	}
}

function GetOptionsImages($configuracion) {
	$parametros=array("folder","width","height","background","wm_pos","wm_rep","wm_image","wm_margin");
	$versiones=explode(";",$configuracion);
	if (count($versiones)>0) {
		foreach($versiones as $version) {
			$version=str_replace("(","",$version);
			$version=str_replace(")","",$version);
			$params=explode(",",$version);
			unset($tmpver);
			for($x=0;$x<count($parametros);$x++) {
				$tmpver[$parametros[$x]]='';
			}
			for($x=0;$x<count($params);$x++) {
				$tmpver[$parametros[$x]]=$params[$x];
			}
			$salida[]=$tmpver;
		}
	} else {
		//No hay revisiones, creamos una ficticia para evitar errores.	
		unset($tmpver);
		$tmpver[$parametros[0]]='images';
		$tmpver[$parametros[1]]=1200;
		$salida=$tmpver;
	}
	return $salida;
}

function GetOptionsImagesFolders($configuracion,$fullpath=true) {
	if (! is_array($configuracion)) { $configuracion=GetOptionsImages($configuracion); }
	$salida=array();
	foreach($configuracion as $i=>$itm) {
		if ($fullpath) {
			$salida[]=sitepath . "/public/" . $itm['folder'] . "/";
		} else {
			$salida[]=$itm['folder'];
		}
	}
	return $salida;
}

function DeleteOptionsImagesFolders($configuracion,$imagen) {
	$carpetas=GetOptionsImagesFolders($configuracion);
	if (count($carpetas)>0) {
		foreach ($carpetas as $carpeta) {
			if (is_file($carpeta . $imagen)) { DeleteFile($carpeta . $imagen); }	
		}
	}
}

function UploadImage($image,$options,$process=true) {
	$thumb= new Thumbs();
	$thumb->UploadImage($image,$thumb->GetOptionsImages($options),$process);	
}

function CropImage($origin,$destination,$image,$from_cropx,$from_cropy,$cropwidth,$cropheight,$imagewidth,$imageheight) {
	$thumb= new Thumbs();
	$thumb->getCrop($origin,$destination,$image,$from_cropx,$from_cropy,$cropwidth,$cropheight,$imagewidth,$imageheight);
}

function GetPriorOptions($configuracion) {
	$opciones=GetOptionsImages($configuracion);
	$total=count($opciones);
	$w=100;
	$h=100;
	$enc=false;
	$x=0;
	$f="thumbnails";
	while ((! $enc) and ($x<$total)) {
		if (strpos($opciones[$x]['folder'],'thumb')!==false) {
			$enc=true;
			$f=$opciones[$x]['folder'];
			$w=$opciones[$x]['width'];
			$h=$opciones[$x]['height'];	
		} else {
			$x++;
		}
	}
	return "w=" . $w . "&h=" . $h . "&folder=" . $f;
}

function thumbnail($src,$width,$height,$salvar=NULL,$bg="") {
	$thumb= new Thumbs();
	$thumb->openImage($src);
	$thumb->resizeImage($width, $height, $bg);	
	header('Content-type: image/jpg');
	$thumb->flushImage(100);	
}
		
		
?>
