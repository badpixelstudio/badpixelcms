<?php 
$this->loadtemplate("header.tpl.php"); 
$this->loadtemplate("topmenu.tpl.php"); 
$this->loadtemplate("mainmenu.tpl.php"); 
$this->loadtemplate("topcontent.tpl.php"); 
?>
<!--Welcome block-->
<div class="row">
	<div class="col-md-12 col-sm-12">
		<div class="portlet box blue-steel">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-home"></i><?php echo _('Panel de Gestión') . " " . siteTitle; ?>
				</div>
			</div>
			<div class="portlet-body">
				<p>Bienvenido al Panel de Gestión de <strong><?php echo siteTitle; ?></strong></p>
				<p>Para administrar los contenidos de utilice el menú de disponible en el lateral izquierdo de la página.</p>
				<p>Una vez haya finalizado de realizar los cambios deseados no olvide cerrar sesión con el fin de evitar que terceras personas no autorizadas realicen modificaciones no autorizadas</p>
			</div>
		</div>
	</div>
</div>
<!-- End Welcome block-->
<div class="row ">
	<?php if ($this->ModuleInstalledAndEnabled('calendar')) { ?>
	<div class="col-md-6 col-sm-6">
		<!-- BEGIN PORTLET-->
		<div class="portlet box blue-madison calendar">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-calendar"></i><?php echo $this->GetModuleName('calendar'); ?>
				</div>
			</div>
			<div class="portlet-body light-grey">
				<div id="calendar">
				</div>
			</div>
		</div>
		<!-- END PORTLET-->
	</div>
	<?php } ?>
	<div class="col-md-6 col-sm-6">
		<!-- BEGIN PORTLET-->
		<?php if (($this->XtraComments->Total>0) and ($this->businessID==0)) {
			if ($this->XtraComments->Total>10) { $this->XtraComments->Total=10; } ?>
		<div class="portlet box blue-steel">
			<div class="portlet-title line">
				<div class="caption">
					<i class="fa fa-comment"></i><?php echo $this->GetModuleName('comments'); ?>
				</div>
			</div>
			<div class="portlet-body" id="chats">
				<div class="scroller" style="height: 435px;" data-always-visible="1" data-rail-visible1="1">
					<ul class="chats">
					<?php for ($x=0;$x<=10;$x++) { 
						$datos=$this->XtraComments->Data[$x]; 
						if ($datos['UserAvatar']=="") { $datos['UserAvatar']="../templates/gestion/assets/images/no-avatar.png";}
						?>
						<li class="in">
							<img class="avatar img-responsive" alt="" src="<?php echo $datos['UserAvatar']; ?>" />
							<div class="message">
								<span class="arrow">
								</span>
								<a href="allcomments" class="name"><?php echo $datos['Name']; ?><span class="datetime"> escribió el <?php echo EuroScreenDateTime($datos['DatePublish']); ?> en el módulo <?php echo $datos['TableName']; ?> <?php if ($datos['Active']==0) { echo "<strong style='color:red;'>[No Activado]</strong>"; } ?></span></a>
								<span class="body"><?php echo limitstring(stripslashes($datos['Comment']),200); ?></span>
							</div>
						</li>
					<?php } ?>
					</ul>
				</div>
			</div>
		</div>
		<?php } ?>
		<!-- END PORTLET-->
	</div>
	<?php if ($this->ModuleInstalledAndEnabled('calendar')) {
	if (($this->TotalEvents>0) and ($this->businessID==0)) { ?>
	<div class="col-md-6 col-sm-6">
		<div class="portlet box blue-steel">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-bell-o"></i><?php echo _('Últimos eventos creados'); ?>
				</div>
			</div>
			<div class="portlet-body">
				<div class="scroller" style="height: 300px;" data-always-visible="1" data-rail-visible="0">
					<ul class="feeds">
					<?php foreach ($this->DataEvents as $elemento) { ?>
						<li>
							<div class="col1">
								<div class="cont">
									<div class="cont-col1">
										<div class="label label-sm label-info">
											<i class="fa fa-check"></i>
										</div>
									</div>
									<div class="cont-col2">
										<div class="desc"><a href="calendar/events_edit/id/<?php echo $elemento['ID']; ?>">
											<?php echo stripslashes($elemento['Title']);?>
										</a></div>
									</div>
								</div>
							</div>
						</li>
					<?php } ?>
					</ul>
				</div>
				<div class="scroller-footer">
					<div class="pull-right">
						<a href="calendar"><?php echo _('Ir a la agenda'); ?> <i class="m-icon-swapright m-icon-gray"></i>
						</a>
						&nbsp;
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php } 
	}?>
</div>			


<?php $this->loadtemplate("footer.tpl.php"); ?>