<?php 
$this->loadtemplate("header.tpl.php"); 
$this->loadtemplate("topmenu.tpl.php"); 
$this->loadtemplate("mainmenu.tpl.php"); 
$this->loadtemplate("topcontent.tpl.php"); 
?>

<div class="row">
	<div class="col-md-12">
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
					<div class="tab-content">
						<table id="translate-table" class="table table-bordered table-striped" action="core--locale/post-translation">
							<tbody>
							<?php foreach($this->LocaleUtils->entries as $id=>$entry) { ?>
							<tr>
								<td style="width:50%"><?php echo implode("",$entry['msgid']); ?></td>
								<td style="width:50%">
									<a href="#" class="translate" data-type="textarea" data-pk="<?php echo $id; ?>" data-name="<?php echo $this->Data['code']; ?>" data-original-title="Enter username" data-placeholder="<?php echo implode("",$entry['msgid']); ?>"><?php echo implode("",$entry['msgstr']); ?></a>
								</td>
							</tr>
							<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<!-- END TAB PORTLET-->
</div>

<?php $this->loadtemplate("footer.tpl.php"); ?>