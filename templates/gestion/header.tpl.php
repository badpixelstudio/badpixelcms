<?php require_once("adapter.php"); ?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="es" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="es" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="es" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title><?php echo _('Panel de Gestión') . " " . siteTitle . " | " . $this->title; ?></title>
<base href="<?php echo siteprotocol . sitedomain . sitePanelFolder . "/"; ?>" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<meta id="PanelFolder" name="<?php echo sitePanelFolder; ?>">
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<?php require_once("css.tpl.php"); ?>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-header-fixed">