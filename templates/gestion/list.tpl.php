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
			<?php if ($this->ItemsCount>0) { 
				if (count($this->TableHeader)>0) { ?>
				<table class="table table-striped table-bordered table-hover" id="sample_1">
					<thead>
						<tr>
							<?php foreach($this->TableHeader as $header) { 
								$w="";
								if ($header=="") { $w='style="width: 25px;"'; } ?>
							<th <?php echo $w; ?>><?php echo $header; ?></th>
							<?php } ?>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($this->Items as $item) { 
							//Check row_conditions;
							$class="";
							if (count($this->TableRowConditions)>0) {
								foreach ($this->TableRowConditions as $cond) {
									if (EvaluatePHP($this->ParseMetaData($cond['Condition'],$item))) { $class.=$cond['Class'] . " "; }
								}
							}?>
						<tr class="<?php echo $class; ?>">
						<?php foreach($this->TableContent as $cell) { 
							if ($cell['Type']=='data') { ?>
							<td><?php if ($cell['HiddenContent']!="") {?><span class="hidden"><?php echo $this->ParseMetaData($cell['HiddenContent'],$item); ?></span><?php } ?>
								<?php if ($cell['Link']!="") { ?><a href="<?php echo $this->ParseMetaData($cell['Link'],$item); ?>"><?php } ?>
								<?php echo $this->ParseMetaData($cell['Content'],$item); ?>
								<?php if ($cell['Link']!="") { ?></a><?php } ?>
							</td>
							<?php } 
							if ($cell['Type']=='menu') { 
								if (count($cell['Content'])>5) { 
									$cell['Type']='buttons';
								} else { ?>
							<td>
								<li class="btn-group-table">
									<button type="button" class="btn btn-xs blue dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
									<span>Acciones</span><i class="fa fa-angle-down"></i>
									</button>
									<ul class="dropdown-menu pull-right" role="menu">
									<?php foreach($cell['Content'] as $submenu) { 
										if ($submenu['Text']=="{{separator}}") { ?>
											<li class="divider"></li>
										<?php } else { 
											if (EvaluatePHP($this->ParseMetaData($submenu['Condition'],$item))) { 
												$icono=""; $add_class="";
												if (strpos($submenu['Link'],"edit")!==false) { $icono='<i class="fa fa-edit"></i>'; }
												if (strpos($submenu['Link'],"delete")!==false) { $icono='<i class="fa fa-trash-o"></i>'; $add_class='class="delete-confirm" '; }
												if (strpos($submenu['Link'],"socialmedia")!==false) { $icono='<i class="fa fa-share"></i>'; }
											?>
											<li><a <?php echo $add_class; ?>href="<?php echo $this->ParseMetaData($submenu['Link'],$item); ?>"><?php echo $icono . $submenu['Text']; ?></a></li>
											<?php }
										} ?>
									<?php } ?>
									</ul>
								</li>
							</td>
								<?php } 
							} 
							if ($cell['Type']=='buttons') { ?>
							<td>
								<button type="button" class="btn btn-xs blue dt-add-td" id="dt-itm-<?php echo $item['ID']; ?>"><span>Acciones</span><i class="fa fa-angle-down"></i></button>
								<div class="dt-opts-<?php echo $item['ID']; ?> pull-right" style="display:none;">
								<?php foreach($cell['Content'] as $submenu) { 
									if ($submenu['Text']=="{{separator}}") { ?>
										&nbsp; | &nbsp; 
									<?php } else { 
										if (EvaluatePHP($this->ParseMetaData($submenu['Condition'],$item))) { 
											$icono=""; $add_class="default";
											if (strpos($submenu['Link'],"edit")!==false) { $icono='<i class="fa fa-edit"></i>'; $add_class="green"; }
											if (strpos($submenu['Link'],"delete")!==false) { $icono='<i class="fa fa-trash-o"></i>'; $add_class='red delete-confirm-row'; }
											if (strpos($submenu['Link'],"socialmedia")!==false) { $icono='<i class="fa fa-share"></i>'; }
										?>
										<a class="btn btn-xs <?php echo $add_class; ?>" href="<?php echo $this->ParseMetaData($submenu['Link'],$item); ?>"><?php echo $icono . $submenu['Text']; ?></a>
										<?php }
									} ?>
								<?php } ?>
								</div>
							</td>
							<?php }
						} ?>
						</tr>
						<?php } ?>
					</tbody>					
					<?php } ?>
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