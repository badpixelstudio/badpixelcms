<?php 
require_once("header.tpl.php"); 
require_once("topmenu.tpl.php"); 
require_once("topcontent.tpl.php"); 
?>

<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue tabbable">
			<div class="portlet-title">
				<div class="caption"><?php echo $this->title; ?></div>
			</div>
			
			<div class="portlet-body" id="ajaxcontent">
				<div class="progress progress-striped">
					<div class="progress-bar progress-bar-success" id="progressbar" style="width: 0%" aria-valuemax="0" aria-valuemin="0" aria-valuenow="40" role="progressbar">
						<span class="sr-only"></span>
					</div>
				</div>
				<h2>Obteniendo paquetes...</h2>
			</div>
		</div>
		<!-- END TAB PORTLET-->
	</div>
</div>
<?php 
$cont=0;
if ($this->files!==false) {
	foreach($this->files as $file) { 
		if ($file!=="") { ?>
			<input type="hidden" id="file_<?php echo $cont; ?>" value="<?php echo $file; ?>">
		<?php 
		$cont++;
		}
	}
} ?>
<input type="hidden" id="totalcount" name="totalcount" value="<?php echo $cont; ?>">

<?php require_once("footer.tpl.php"); ?>