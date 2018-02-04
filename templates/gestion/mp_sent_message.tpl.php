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
						<h1 class="pull-left"><?php echo _("Leer mensaje enviado"); ?></h1>
					</div>
					<div class="inbox-content">
						<div class="inbox-header inbox-view-header">
							<h1 class="pull-left"><?php echo stripslashes($this->Data['Subject']); ?><a href="users--pm/sent"> <?php echo _("Enviados"); ?> </a></h1>
						</div>
						<div class="inbox-view-info">
							<div class="row">
								<div class="col-md-10">
									<span class="bold">Para: </span>
									<?php foreach($this->Data['Destinations'] as $destination) {
										$clase="label label-primary";
										if ($destination['type']=="rol") { $clase="label label-danger label-important"; } ?>
										<span class="<?php echo $clase; ?>"> <?php echo $destination['text']; ?> </span> &nbsp;
									<?php } ?>
								</div>
								<div class="col-md-2"><?php echo ($this->Data['DateSend']); ?></div>
							</div>

						</div>
						<div class="inbox-view">
						<?php echo stripslashes($this->Data['Message']); ?>	
						</div>
					</div>
					
				</div>
			</div>
		</div>
<?php $this->loadtemplate("footer.tpl.php"); ?>