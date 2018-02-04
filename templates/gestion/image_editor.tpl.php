<?php 
if($_GET['option']!=-1){ $width=$this->OptionsArray[$_GET['option']]['width']; }  else { $width=$_GET['w']; } 
if($_GET['option']!=-1){ $height=$this->OptionsArray[$_GET['option']]['height']; }  else { $height=$_GET['h']; }
//Si el Width o Height es un rango, elegimos aleatoriamente
if (strpos($width,'-')!==false) {
	$partes=explode('-',$width);
	$width=rand($partes[0],$partes[1]);
}
if (strpos($height,'-')!==false) {
	$partes=explode('-',$height);
	$height=rand($partes[0],$partes[1]);
}

$this->loadtemplate("header.tpl.php"); 
$this->loadtemplate("topmenu.tpl.php"); 
$this->loadtemplate("mainmenu.tpl.php"); 
$this->loadtemplate("topcontent.tpl.php"); 
?>
<div class="row">
	<div class="col-md-12">
	<form class="form" id="form" action="<?php echo $this->Data['ActionForm']; ?>" enctype="multipart/form-data" method="POST" autocomplete="off">
		<div class="form-body">
			<div id="form_error" class="alert alert-danger display-hide">
				<button class="close" data-close="alert"></button>
				Hay errores en el formulario. Revise los campos marcados en rojo.
			</div>
		</div>
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">Editor de Imagen</div>
			</div>

			<div class="portlet-body">
				<button class="btn green submit" type="submit">Guardar</button>
				<p>&nbsp;</p>
				<div class="pane">
					<img src="<?php echo putimage($this->Data['Image']); ?>" border="2"/>
					<div class="coords hide">
						<input name="Extra_images_IDFather" id="Extra_images_IDFather" type="hidden" value="<?php echo $this->idprior; ?>">
						<input name="System_Name" id="System_Name" type="hidden" value="<?php echo $this->Data['Image']; ?>">
						<input name="System_Option" id="System_Option" type="hidden" value="<?php echo $_GET['option']; ?>">
						<input name="System_Return" id="System_Option" type="hidden" value="<?php echo $this->Data['ReturnForm']; ?>">
						<input type="text" id="Form_cropx" name="Form_cropx" value="" />
						<input type="text" id="Form_cropy" name="Form_cropy" value="" />
						<input type="text" id="Form_cropwidth" name="Form_cropwidth" value="" />
						<input type="text" id="Form_cropheight" name="Form_cropheight" value="" />
						<input type="text" id="Form_imagewidth" name="Form_imagewidth" value="" />
						<input type="text" id="Form_imageheight" name="Form_imageheight" value="" />
						<input type="hidden" id="Init_Width" name="Init_Width" value="<?php echo $width; ?>" />
						<input type="hidden" id="Init_Height" name="Init_Height" value="<?php echo $height; ?>" />
						<input type="hidden" id="Init_ZoomMax" name="Init_ZoomMax" value="<?php echo $this->Data['MaxZoom']; ?>" />
					</div>
				</div>
			</div>
		</div>
	</div>

</form>
</div>

<?php $this->loadtemplate("footer.tpl.php"); ?>

