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
						<li class="inbox active"><a href="users--pm" class="btn" > <?php echo _("Bandeja de Entrada"); ?> (<?php echo $this->userMP; ?>)</a><b></b></li>
						<li class="sent"><a class="btn" href="users--pm/sent" > <?php echo _("Enviados"); ?> </a><b></b></li>
					</ul>
				</div>
				<div class="col-md-10">
					<div class="inbox-header">
						<h1 class="pull-left"><?php echo _("Leer mensaje recibido"); ?></h1>
					</div>
					<div class="inbox-content">
						<div class="inbox-header inbox-view-header">
							<h1 class="pull-left"><?php echo stripslashes($this->Data['Subject']); ?><a href="users--pm"> <?php echo _("Bandeja de Entrada"); ?> </a></h1>
						</div>
						<div class="inbox-view-info">
							<div class="row">
								<div class="col-md-7">
									<img class="avatar-top" src="<?php echo $this->Remitente['Avatar']; ?>">
									<span class="bold"> <?php echo $this->Remitente['UserName']; ?> </span> <?php echo _("el"); ?> <?php echo ($this->Data['DateSend']); ?>
								</div>
							</div>
						</div>
						<div class="inbox-view">
						<?php echo stripslashes($this->Data['Message']); ?>	
						</div>
					</div>
					<form class="form" id="form" action="<?php echo $this->TemplatePostScript; ?>" enctype="multipart/form-data" method="<?php echo $this->TemplateMethodScript; ?>" autocomplete="off">
						<div class="form-body">
							<div id="form_error" class="alert alert-danger display-hide">
								<button class="close" data-close="alert"></button>
								Hay errores en el formulario. Revise los campos marcados en rojo.
							</div>
						</div>
						<div class="portlet box blue tabbable">
							<div class="portlet-title">
								<div class="caption"><?php echo _("Responder a este mensaje"); ?></div>
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
							<button  class="btn green" type="submit">Enviar respuesta</button>
							<?php if (count($this->FormHiddenContent)>0) {
								foreach($this->FormHiddenContent as $field) { PutField($field); }
							} ?>
						</div>
						<div id="form_saving" class="alert alert-success display-hide">
								<button class="close" data-close="alert"></button>
								Enviando el mensaje. Por favor, espere...
							</div>
					</div>

				</form>
				</div>
			</div>
		</div>
	<!-- END CONTENT -->
<?php $this->loadtemplate("footer.tpl.php"); ?>