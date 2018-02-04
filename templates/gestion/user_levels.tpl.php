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
			<?php if ($this->ItemsCount>0) {  ?>
				<table class="table table-striped table-bordered table-hover" id="sample_1">
					<thead>
						<tr>
							<th><?php echo _('Módulo'); ?></th>
							<?php foreach($this->Roles as $rol) { ?>
							<th><?php echo $rol['RolName']; ?></th>
							<?php } ?>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($this->Items as $item) { ?>
						<tr>
							<td><a class="levels_name" href="<?php echo $item['File'];?>" id="name_<?php echo $item['File'];?>"><?php echo $item['Name'] ?></a></td>
							<?php foreach($item['Status'] as $idrol=>$status) { ?>
								<td><input id="<?php echo $item['File'] . "_" . $idrol; ?>" module="<?php echo $item['File']; ?>" rol="<?php echo $idrol; ?>" type="checkbox" class="make-switch levels_conf" <?php if ($status==1) { echo "checked"; } ?> data-on-color="primary" data-off-color="danger" data-on-text="<i class='fa fa-check'></i>" data-off-text="<i class='fa fa-times'></i>"></td>
							<?php } ?>	
						</tr>
						<?php } ?>
					</tbody>					
				</table>
			<?php } else { ?>
				<div class="alert alert-danger"><?php echo _('No hay datos que mostrar'); ?></div>
			<?php } ?>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>

<?php $this->loadtemplate("footer.tpl.php"); ?>
<script>
$(document).ready(function () {
	$(document).delegate('.levels_conf','switchChange.bootstrapSwitch', function(event){
		var _this = $(this);
		var _module = _this.attr('module');
		var _idrol= _this.attr('rol');
		var _value = 0 ;
		if (_this.is(":checked")) { _value = 1; }
		_this.prop('disabled',true);
		$.ajax({
			type: 'POST',
			url: 'levels/setflag/module/' + _module + '/idrol/' + _idrol + '/flag/'+ _value,
			success:function(msj){	
				if ( msj == 1 ){
					console.log('conf saved');
				}
				else{
					console.log('conf not saved');
				}
			},
			error:function(){
				console.log('error');
			}
		});
	});

});
</script>