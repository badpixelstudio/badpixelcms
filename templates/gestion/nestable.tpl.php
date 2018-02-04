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
				<div class="caption"><?php echo $this->title; ?>
					
				</div>
			</div>
			<div class="portlet-body">
			<?php if (count($this->Items)>0) { 
				$script=$this->module;
				if ($this->class!=$this->module) { $script.="--" . $this->class; }
				$script.="/saveorderjson";
				if (strpos($this->TemplatePostScript, "/post")===false) { $script=$this->TemplatePostScript; }	?>
				<div class="dd nestable" id="cats_tree" maxdepth="<?php echo $this->MaxDepth; ?>" script="<?php echo $script; ?>">
				<?php TemplateCatsTree($this, $this->Items); ?>
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