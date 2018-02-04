<!DOCTYPE html>
<!--[if IE 8]> <html lang="es" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="es" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="es" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title><?php echo _('Procesando imagenes') . "... "; ?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1" name="viewport"/>
<meta name="description" content="Process multiple uploaded images for prevent timeout"/>
<meta name="author" content="BadPixel Studios"/>
<link rel="stylesheet" media="all" type="text/css" href="<?php echo siteprotocol . sitedomain; ?>/lib/extras/assets/process.css" />
</head>
<body>
<header>
    <div class="page-header-inner">
        <h1><?php echo _("Procesando imágenes"); ?>...</h1>
    </div>
    <div class="form-body">
        <div class="alert alert-info" id="mensaje"><?php echo _('Iniciando el proceso de carga de imagenes. Por favor, espere...'); ?></div>
    </div>
</header>
<div class="page-container">
    <form class="active" script="<?php echo $this->TemplatePostScript; ?>">
        <div>
            <div id="img"></div>
        </div>
    </form>
</div>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo siteprotocol . sitedomain; ?>/lib/extras/assets/process.js"></script>