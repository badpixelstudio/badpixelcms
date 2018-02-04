<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="content-language" content="es">
    <base href="<?php echo siteprotocol . sitedomain; ?>" />
    <meta name="description" content="<?php $this->HeadDescription; ?>" />
    <meta name="keywords" content="<?php echo siteHeadTags; ?>" />
    <meta name="author" content="BadPixel">
    <title><?php if ($this->HeadTitle!="") { echo $this->HeadTitle . " - "; } echo siteTitle; ?></title>
    <meta property="og:title" content="<?php if ($this->HeadTitle!="") { echo $this->HeadTitle . " - "; } echo siteTitle; ?>"/>
    <meta property="og:description" content="<?php echo $this->HeadDescription; ?>"/>
    <meta property="og:image" content="<?php echo $this->HeadImage; ?>"/>
    <link rel="image_src" href="<?php echo $this->HeadImage; ?>" />
    <link rel="apple-touch-icon" sizes="57x57" href="templates/<?php echo $this->template; ?>/assets/favicons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="templates/<?php echo $this->template; ?>/assets/favicons/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="templates/<?php echo $this->template; ?>/assets/favicons/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="templates/<?php echo $this->template; ?>/assets/favicons/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="templates/<?php echo $this->template; ?>/assets/favicons/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="templates/<?php echo $this->template; ?>/assets/favicons/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="templates/<?php echo $this->template; ?>/assets/favicons/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="templates/<?php echo $this->template; ?>/assets/favicons/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="templates/<?php echo $this->template; ?>/assets/favicons/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="templates/<?php echo $this->template; ?>/assets/favicons/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="templates/<?php echo $this->template; ?>/assets/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="templates/<?php echo $this->template; ?>/assets/favicons/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="templates/<?php echo $this->template; ?>/assets/favicons/favicon-16x16.png">
    <link rel="manifest" href="templates/<?php echo $this->template; ?>/assets/favicons/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="templates/<?php echo $this->template; ?>/assets/favicons/ms-icon-144x144.png">
    <meta name="theme-color" content="#1CBBB4">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" media="none" onload="if(media!='all')media='all'">
    <?php if (siteTemplateMinResources) { ?>
    <link rel="stylesheet" type="text/css" href="<?php echo GetCSSMin($this->template,"assets/css"); ?>"  media="none" onload="if(media!='all')media='all'"/>
    <?php } else { ?>
    <link href="templates/<?php echo $this->template; ?>/assets/css/styles.css" rel="stylesheet" media="none" onload="if(media!='all')media='all'">
    <?php } ?>
    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.css" rel="stylesheet" type="text/css" media="none" onload="if(media!='all')media='all'">
</head>