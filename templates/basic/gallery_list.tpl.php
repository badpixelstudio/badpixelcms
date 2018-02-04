<?php $this->loadtemplatepublic('header.tpl.php'); ?>
<?php $this->loadtemplatepublic('menu.tpl.php'); ?>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo $this->GetModuleName("gallery"); ?>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="./"><?php echo _("Inicio"); ?></a></li>
                    <li class="active"><?php echo $this->GetModuleName("gallery"); ?></li>
                </ol>
            </div>
        </div>
        <div class="row">
        <?php if($this->Gallery->ItemsCount>0) {
            $count=0;
            foreach($this->Gallery->Items as $item) { 
            $count++;?>
            <div class="col-md-4 img-portfolio">
                <a href="<?php echo $item['Permalink']; ?>">
                    <img class="img-responsive img-hover" src="<?php echo $item['Image_thumbnails']; ?>" alt="<?php echo stripslashes($item['Title']); ?>">
                </a>
                <h3>
                    <a href="<?php echo $item['Permalink']; ?>"><?php echo stripslashes($item['Title']); ?></a>
                </h3>
                <p><?php echo LimitString(strip_tags(stripslashes($item['Title'])),100); ?></p>
            </div>
            <?php 
            if ($count>2) { $count=0; echo '</div><div class="row">'; }
            }
        } else { ?>
        <div class="alert alert-danger fade in"><?php echo _("Oops! Aquí parece que no hay nada todavía"); ?></div>
        <?php } ?> 
        </div>

        <hr>
        <div class="row text-center">
            <div class="col-lg-12">
                <?php TemplatePutPaginator($this->Gallery->page,$this->Gallery->offset,$this->Gallery->ItemsCount,$this->Gallery->conf->Export("PermalinkFolder"),""); ?>
            </div>
        </div>
        <hr>
<?php $this->loadtemplatepublic('footer.tpl.php'); ?>