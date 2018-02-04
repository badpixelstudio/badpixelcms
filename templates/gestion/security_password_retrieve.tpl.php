<?php require_once("adapter.php"); ?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<head>
<meta charset="utf-8"/>
<title><?php echo _('Panel de Gestión') . " " . siteTitle . " | " . $this->title; ?></title>
<base href="<?php echo siteprotocol . sitedomain . sitePanelFolder . "/"; ?>" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<meta id="PanelFolder" name="<?php echo sitePanelFolder; ?>">
<?php $this->loadtemplate("css.tpl.php");  ?>
<!-- END THEME STYLES -->
<link rel="shortcut icon" href="favicon.ico"/>
</head>
<body class="login">
<!-- BEGIN LOGO -->
<div class="logo">
	<img src="../templates/gestion/assets/admin/layout/img/logo-big.png" alt=""/>
</div>
<!-- END LOGO -->
<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
<div class="menu-toggler sidebar-toggler">
</div>
<!-- END SIDEBAR TOGGLER BUTTON -->
<!-- BEGIN LOGIN -->
<div class="content">
	<div id="SendOK" class="display-hide">
		<h3><?php echo _('Instrucciones enviadas'); ?></h3>
		<p><?php echo _('Se ha enviado un email con las instrucciones necesarias para cambiar la contraseña'); ?></p>
		<p><?php echo _('En unos segundos será redirigido a la página de inicio de sesión'); ?></p>
		<div class="form-actions">
			<button type="button" class="btn back-btn"><i class="m-icon-swapleft"></i> <?php echo _("Volver"); ?> </button>
		</div>
	</div>
	<div id="LoginBlock" class="display-hide">
		<h3><?php echo _('Enviando instrucciones'); ?></h3>
		<p><?php echo _('Por favor, espere...'); ?></p>
		<div class="progress progress-striped active">
			<div class="progress-bar progress-bar-info" style="width: 100%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="20" role="progressbar"></div>
		</div>
	</div>
	<!-- BEGIN FORGOT PASSWORD FORM -->
	<form class="forget-form" method="post">
		<h3><?php echo _("Recuperar contraseña"); ?></h3>
		<div class="alert alert-danger display-hide">
			<button class="close" data-close="alert"></button>
			<span></span>
		</div>
		<p><?php echo _("Introduzca su email de usuario registrado para remitirle las instrucciones para restablecer su contraseña"); ?></p>
		<div class="form-group">
			<div class="input-icon">
				<i class="fa fa-envelope"></i>
				<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Email" name="email" id="email"/>
			</div>
		</div>
		<div class="form-actions">
			<button type="button" class="btn" id="back-btn"><i class="m-icon-swapleft"></i> <?php echo _("Volver"); ?> </button>
			<button type="submit" class="btn green pull-right"> <?php echo _("Enviar"); ?> <i class="m-icon-swapright m-icon-white"></i>
			</button>
		</div>
	</form>

	<!-- END FORGOT PASSWORD FORM -->
</div>
<!-- END LOGIN -->
<!-- BEGIN COPYRIGHT -->
<div class="copyright">
	 <?php echo _('Versión') . " " . $this->AppVersion; ?>
</div>
<!-- END COPYRIGHT -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<?php $this->loadtemplate("scripts.tpl.php");  ?>
</body>
<!-- END BODY -->
</html>