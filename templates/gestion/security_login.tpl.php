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
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<meta id="PanelFolder" name="<?php echo sitePanelFolder; ?>">
<?php $this->loadtemplate("css.tpl.php");  ?>
<!-- END THEME STYLES -->
</head>
<body class="login">
<!-- BEGIN LOGO -->
<div class="logo">
	<img src="../templates/gestion/assets/images/logo-big.png" alt=""/>
</div>
<!-- END LOGO -->
<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
<div class="menu-toggler sidebar-toggler">
</div>
<!-- END SIDEBAR TOGGLER BUTTON -->
<!-- BEGIN LOGIN -->
<div class="content">
	<div id="LoginBlock" class="display-hide">
		<h3><?php echo _('Iniciando sesión'); ?></h3>
		<p><?php echo _('Por favor, espere...'); ?></p>
		<div class="progress progress-striped active">
			<div class="progress-bar progress-bar-info" style="width: 100%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="20" role="progressbar"></div>
		</div>
	</div>
	<!-- BEGIN LOGIN FORM -->
	<form class="login-form" method="post">
		<h3 class="form-title"><?php echo _('Iniciar sesión'); ?></h3>
		<div class="alert alert-danger <?php if (! isset($_GET['text'])) { ?>display-hide<?php } ?>">
			<button class="close" data-close="alert"></button>
			<span><?php if (isset($_GET['text'])) {echo $_GET['text'];} ?></span>
		</div>
		<div class="form-group">
			<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
			<label class="control-label visible-ie8 visible-ie9"><?php echo _('Usuario o Email'); ?></label>
			<div class="input-icon">
				<i class="fa fa-user"></i>
				<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Usuario o Email" name="username" id="username"/>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9"><?php echo _('Contraseña'); ?></label>
			<div class="input-icon">
				<i class="fa fa-lock"></i>
				<input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Contraseña" name="password" id="password"/>
			</div>
		</div>
		<div class="form-actions">
			<label class="checkbox">
			<input type="checkbox" name="remember" id="remember" value="1"/> <?php echo _("Recuerdame"); ?> </label>
			<input type="hidden" id="urlrefer" name="urlrefer" value="<?php echo $this->Data['Form_Redirect'];?>" >
			<button type="submit" class="btn green pull-right"><?php echo _("Iniciar sesión"); ?> <i class="m-icon-swapright m-icon-white"></i>
			</button>
		</div>
		<?php if ((siteFacebookEnabled) or (siteTwitterEnabled) or (siteGoogleAPIEnabled)) { ?>
			<div class="login-options">
				<h4><?php echo _('O accede con tu perfil de'); ?></h4>
				<ul class="social-icons">
					<?php if (siteFacebookEnabled) { ?>
					<li><a class="facebook" data-original-title="facebook" href="<?php echo $GLOBALS['loginUrl']; ?>"></a></li>
					<?php } ?>
					<?php if (siteTwitterEnabled) { ?>
					<li>
						<a class="twitter" data-original-title="Twitter" href="<?php echo $GLOBALS['twloginUrl']; ?>">
						</a>
					</li>
					<?php } ?>
					<?php if (siteGoogleAPIEnabled) { ?>
					<li>
						<a class="googleplus" data-original-title="Goole Plus" href="<?php echo $GLOBALS['gloginUrl']; ?>">
						</a>
					</li>
					<?php } ?>
				</ul>
			</div>
		<?php } ?>
		<div class="forget-password">
			<h4><?php echo _('No recuerdas tu contraseña'); ?>?</h4>
			<p><a href="security/send_password" ><?php echo _("Pulsa aquí para generar una nueva contraseña"); ?></a></p>
		</div>
		<?php if ($this->conf->Check('UserSecurityCreate')) { ?>
			<div class="create-account">
				<h4><?php echo _('Crear cuenta de usuario'); ?>?</h4>
				<p><a href="security/new" ><?php echo _('Si aún no eres usuario pulsa aquí para crear una nueva cuenta'); ?></a></p>
			</div>
		<?php } ?>
	</form>
	<!-- END LOGIN FORM -->
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
</html>