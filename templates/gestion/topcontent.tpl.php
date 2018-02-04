	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">		
			<!-- BEGIN PAGE HEADER-->
			<div class="row">
				<div class="col-md-12">
					<!-- BEGIN PAGE TITLE & BREADCRUMB-->
					<h3 class="page-title"><?php echo $this->title; ?></h3>
					<ul class="page-breadcrumb breadcrumb">
						<?php if (count($this->MainMenu)==1) { ?>
						<li class="btn-group">
							<a class="btn blue btn-a" href="<?php echo $this->MainMenu[0]['Link']; ?>"><span><?php echo $this->MainMenu[0]['Text']; ?></span></a>
						</li>
						<?php } ?>
						<?php if (count($this->MainMenu)>1) { ?>
						<li class="btn-group">
							<button type="button" class="btn blue dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
							<span>Acciones</span><i class="fa fa-angle-down"></i>
							</button>
							<ul class="dropdown-menu pull-right" role="menu">
								<?php foreach($this->MainMenu as $menu) { 
									if ($menu['Text']=="{{separator}}") { ?>
										<li class="divider"></li>
									<?php } else { ?>
										<li><a href="<?php echo $menu['Link']; ?>"><?php echo $menu['Text']; ?></a></li>
									<?php } 
								} ?>
							</ul>
						</li>
						<?php } ?>
						<?php $this->GetBreadcrumb(false); ?>
					</ul>
					<!-- END PAGE TITLE & BREADCRUMB-->
				</div>
			</div>
			<div class="form-body">
				<?php if (defined('ShowAlertInstallFolder')) { ?><div class="alert alert-warning"><button class="close" data-close="alert"></button><strong><?php echo _("Brecha de seguridad"); ?></strong>: <?php echo _("La carpeta de instalación aún existe en el servidor, bórrela inmediatamente para evitar accesos no autorizados."); ?></div><?php } ?>
				<?php if ($this->error!="") { ?><div class="alert alert-danger"><button class="close" data-close="alert"></button><?php echo $this->error; ?></div><?php } ?>
				<?php if ($this->text!="") { ?><div class="alert alert-info"><button class="close" data-close="alert"></button><?php echo $this->text; ?></div><?php } ?>
			</div>
			<!-- END PAGE HEADER-->
			<!-- BEGIN PAGE CONTENT-->