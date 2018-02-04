<?php $this->loadtemplatepublic('header.tpl.php'); ?>
<?php $this->loadtemplatepublic('menu.tpl.php'); ?>
    <div class="container">
        <!-- Page Heading/Breadcrumbs -->
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo stripslashes($this->Gallery->Data['Title']); ?>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="./"><?php echo _("Inicio"); ?></a></li>
                    <li><a href="<?php echo $this->Gallery->conf->Export("PermalinkFolder"); ?>"><?php echo $this->GetModuleName("gallery"); ?></a></li>
                    <li class="active"><?php echo stripslashes($this->Gallery->Data['Title']); ?></li>
                </ol>
            </div>
        </div>
        <!-- /.row -->

        <!-- Projects Row -->
        <div class="row">
            <?php if($this->Gallery->XtraImages->Total>0) {
            foreach($this->Gallery->XtraImages->Data as $item) { ?>
            <div class="col-md-3 img-portfolio">
                <a href="public/images/<?php echo $item['Image']; ?>" data-title="<?php echo stripslashes($item['Description']); ?>" data-lightbox="gallery" >
                    <img class="img-responsive img-portfolio img-hover" src="public/thumbnails/<?php echo $item['Image']; ?>" alt="<?php echo stripslashes($item['Description']); ?>">
                </a>
            </div>
            <?php }
            } else { ?>
            <div class="alert alert-danger fade in"><?php echo _("Oops! Aquí parece que no hay nada todavía"); ?></div>
            <?php } ?>
        </div>
        <hr>
<?php $this->loadtemplatepublic('footer.tpl.php'); ?>