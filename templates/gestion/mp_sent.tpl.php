<?php 
$this->loadtemplate("header.tpl.php"); 
$this->loadtemplate("topmenu.tpl.php"); 
$this->loadtemplate("mainmenu.tpl.php"); 
$this->loadtemplate("topcontent.tpl.php"); 
?>

	<div class="row inbox">
		<div class="col-md-2">
			<ul class="inbox-nav margin-bottom-10">
				<li class="compose-btn"><a href="users--pm/new" class="btn green"><i class="fa fa-edit"></i> <?php echo _("Redactar"); ?> </a></li>
				<li class="inbox"><a href="users--pm" class="btn" > <?php echo _("Bandeja de Entrada"); ?> (<?php echo $this->userMP; ?>)</a><b></b></li>
				<li class="sent active"><a class="btn" href="users--pm/sent" > <?php echo _("Enviados"); ?> </a><b></b></li>
			</ul>
		</div>
		<div class="col-md-10">
			<div class="inbox-header">
				<h1 class="pull-left"><?php echo _("Enviados"); ?></h1>
			</div>
			<div>
			<?php if ($this->ItemsCount>0) { ?>
				 <table class="table table-striped table-advance table-hover">
					<thead>
					<tr>
						<th colspan="3">
							<input type="checkbox" class="mail-checkbox mail-group-checkbox">
							<div class="btn-group">
								<a class="btn btn-sm blue" href="#" data-toggle="dropdown">
								Acciones <i class="fa fa-angle-down"></i>
								</a>
								<ul class="dropdown-menu">
									<li><a href="users--pm/markdelete/sent/sent" id="inbox-mark-delete"><i class="fa fa-trash-o"></i> Borrar </a></li>
								</ul>
							</div>
						</th>
						<th class="pagination-control" colspan="2">
							<span class="pagination-info">
							Página <?php echo $this->page; ?> de <?php echo $this->ItemsCount; ?> </span>
							<?php if ($this->page>1) { ?>
							<a href="users--pm/send/page/<?php echo ($this->page-1); ?>/offset/<?php echo $this->offset; ?>" class="btn btn-sm blue">
							<i class="fa fa-angle-left"></i>
							</a>
							<?php } ?>
							<?php if ($this->page<$this->ItemsCount) { ?>
							<a href="users--pm/send/page/<?php echo ($this->page+1); ?>/offset/<?php echo $this->offset; ?>" class="btn btn-sm blue">
							<i class="fa fa-angle-right"></i>
							</a>
							<?php } ?>
						</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach($this->Items as $item) { 
						$class="read";
						if ($item['ReadMsg']==0) { $class="noread"; } ?>
						<tr class="<?php echo $class; ?>" data-messageid="1">
							<td class="inbox-small-cells"><input name="selected[]" type="checkbox" class="mail-checkbox" value="<?php echo $item['ID']; ?>"></td>
							<td class="view-message hidden-xs"><a href="users--pm/sentmessage/id=<?php echo $item['ID']; ?>"><?php echo stripslashes($item['Destinations']); ?></a></td>
							<td class="view-message "><a href="users--pm/sentmessage/id=<?php echo $item['ID']; ?>"><?php echo stripslashes($item['Subject']); ?></a></td>
							<td class="view-message text-right"><a href="users--pm/sentmessage/id=<?php echo $item['ID']; ?>"><?php echo EuroScreenDateTime($item['DateSend']); ?></a></td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			<?php } else { ?>
				<div class="alert alert-danger"><?php echo _('No hay datos que mostrar'); ?></div>
			<?php } ?>
			</div>
		</div>
	</div>
</div>
<?php $this->loadtemplate("footer.tpl.php"); ?>