<?php 
$this->loadtemplate("header.tpl.php"); 
$this->loadtemplate("topmenu.tpl.php"); 
$this->loadtemplate("mainmenu.tpl.php"); 
$this->loadtemplate("topcontent.tpl.php"); 
?>

<div class="row">
	<div class="col-md-12">
		<!-- BEGIN EXAMPLE TABLE PORTLET-->
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption"><?php echo $this->title; ?></div>
			</div>
			<div class="portlet-body">
			<?php if (count($this->ItemsCount)>0) { ?>
				<div class="dd nestable" id="single_order" script="<?php echo $this->script; ?>">
				<?php TemplateTree($this->Items); ?>
				</div>
			<?php } else { ?>
				<div class="alert alert-danger"><?php echo _('No hay datos que mostrar'); ?></div>
			<?php } ?>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>

<?php $this->loadtemplate("footer.tpl.php"); ?>