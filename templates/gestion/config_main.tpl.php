<?php 
$this->loadtemplate("header.tpl.php"); 
$this->loadtemplate("topmenu.tpl.php"); 
$this->loadtemplate("mainmenu.tpl.php"); 
$this->loadtemplate("topcontent.tpl.php"); 
?>

<div class="row">
	<div class="col-md-12">
		<!-- BEGIN EXAMPLE TABLE PORTLET-->
		<div class="portlet box blue-hoki">
			<div class="portlet-title">
				<div class="caption"><?php echo $this->title; ?>
					
				</div>
			</div>
			<div class="portlet-body">
				<table class="table table-striped table-bordered table-hover" id="sample_1">
					<thead>
						<tr>
							<th><?php echo _("Módulo"); ?></th>
							<th><?php echo _("Operaciones"); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><span style="display: none;">0</span><a href="config/editconfig/mod/core/id/<?php echo $this->businessID; ?>"><strong><?php echo _('Core (Núcleo del sistema)'); ?></strong></a></td>
							<td>
								<a class="btn btn-xs green" href="config/editconfig/mod/core/id/<?php echo $this->businessID; ?>"><?php echo _("Editar"); ?></a>
								<a class="btn btn-xs btn-warning" href="config/reloadconfig/mod/core/id/<?php echo $this->businessID; ?>"><?php echo _("Recargar"); ?></a>
								<a class="btn btn-xs btn-danger" href="config/clear_deprecated/mod/core/id/<?php echo $this->businessID; ?>"><?php echo _("Eliminar conf. obsoleta"); ?></a>
							</td>
						</tr>
						<?php foreach($this->Permissions as $module=>$datos) { 
							if (strpos($module, "--")===false) { ?>
						<tr>
							<td><span style="display: none;">1</span>
							<a href="config/editconfig/mod/<?php echo $module; ?>/id/<?php echo $this->businessID; ?>">
								<?php $texto=$this->GetModuleName($module); 
								if ($texto=="") { $texto=$module; }
								echo $texto;
								?>
							</a></td>
							<td>
								<a class="btn btn-xs green" href="config/editconfig/mod/<?php echo $module; ?>/id/<?php echo $this->businessID; ?>"><?php echo _("Editar"); ?></a>
								<a class="btn btn-xs btn-warning" href="config/reloadconfig/mod/<?php echo $module; ?>/id/<?php echo $this->businessID; ?>"><?php echo _("Recargar"); ?></a>
								<a class="btn btn-xs btn-danger" href="config/clear_deprecated/mod/<?php echo $module; ?>/id/<?php echo $this->businessID; ?>"><?php echo _("Eliminar conf. obsoleta"); ?></a>
							</td>
						</tr>
						<?php } 
						}?>
					</tbody>					
				</table>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>

<?php $this->loadtemplate("footer.tpl.php"); ?>