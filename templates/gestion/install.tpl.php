<?php 
require_once("header.tpl.php"); 
require_once("topmenu.tpl.php"); 
require_once("topcontent.tpl.php"); 
?>

<div class="row">
	<div class="col-md-12">
	<form class="form" id="form" action="<?php echo $this->TemplatePostScript; ?>" enctype="multipart/form-data" method="<?php echo $this->TemplateMethodScript; ?>" autocomplete="off">
		<div class="form-body">
			<div id="form_error" class="alert alert-danger display-hide">
				<button class="close" data-close="alert"></button>
				Hay errores en el formulario. Revise los campos marcados en rojo.
			</div>
		</div>
		<div class="portlet box blue tabbable">
			<div class="portlet-title">
				<div class="caption"><?php echo $this->title; ?></div>
			</div>
			
			<div class="portlet-body">
				<div class=" portlet-tabs">
					<ul class="nav nav-tabs">
						<?php $tabs=""; 
						foreach($this->FormContent as $id=>$block) { 
							$clase="";
							if ($id==count($tabs)-1) { $clase='class="active"'; }
							$tabs='<li ' . $clase . ' id="toptab_'. $id .'"><a href="#portlet_tab' . $id . '" data-toggle="tab">' . _($block['Name']) . '</a>' . $tabs;
						}
						echo $tabs; ?>
					</ul>
					<div class="tab-content">
						<?php foreach($this->FormContent as $id=>$block) {
							$clase=""; if ($id==0) { $clase="active"; } ?>
						<div class="tab-pane <?php echo $clase; ?>" id="portlet_tab<?php echo $id; ?>">
							<?php foreach($block['Fields'] as $field) {
							PutField($field);
							} ?>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<!-- END TAB PORTLET-->
		<div class="form-actions">
			<button  class="btn green" type="submit">Continuar</button>
			<?php if (count($this->FormHiddenContent)>0) {
				foreach($this->FormHiddenContent as $field) { PutField($field); }
			} ?>
		</div>
		<div id="form_saving" class="alert alert-success display-hide">
				<button class="close" data-close="alert"></button>
				Guardando los datos. Por favor, espere...
			</div>
	</div>

</form>
</div>

<?php require_once("footer.tpl.php"); ?>