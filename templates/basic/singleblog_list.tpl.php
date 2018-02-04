<?php $this->loadtemplatepublic('header.tpl.php'); ?>
<?php $this->loadtemplatepublic('menu.tpl.php'); ?>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo $this->GetModuleName("singleblog"); ?>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="./"><?php echo _("Inicio"); ?></a>
                    </li>
                    <li class="active"><?php echo $this->GetModuleName("singleblog"); ?></li>
                </ol>
            </div>
        </div>
        <!-- /.row -->

        <div class="row">
            <div class="col-md-8">
                <?php if($this->SingleBlog->ItemsCount>0) {
                foreach($this->SingleBlog->Items as $item) { ?>
                    <h2><a href="<?php echo $item['Permalink']; ?>"><?php echo stripslashes($item['Title']); ?></a></h2>
                    <p class="lead">
                        <?php echo _("por"); ?> <a href="#"><?php echo siteTitle; ?></a>
                    </p>
                    <p><i class="fa fa-clock-o"></i> <?php echo _("Publicado el"); ?> <?php echo EuroScreenDate($item['DatePublish']); ?></p>
                    <?php if($item['Image_thumbnails']!="") { ?>
                    <hr>
                    <a href="<?php echo $item['Permalink']; ?>">
                        <img class="img-responsive img-hover" src="<?php echo $item['Image_thumbnails']; ?>" alt="<?php echo stripslashes($item['Title']); ?>">
                    </a>
                    <?php } ?>
                    <hr>
                    <?php if($item['ShortDescription']!="") { ?>
                    <p><?php echo stripslashes($item['ShortDescription']); ?></p>
                    <?php } else { ?>
                    <p><?php echo LimitString(strip_tags(stripslashes($item['LongDescription'])),250); ?></p>
                    <?php } ?>
                    <a class="btn btn-primary" href="<?php echo $item['Permalink']; ?>"><?php echo _("Leer más"); ?> <i class="fa fa-angle-right"></i></a>
                    <hr>
                    <?php }
                } else { ?>
                <div class="alert alert-danger fade in"><?php echo _("Oops! Aquí parece que no hay nada todavía"); ?></div>
                <?php } ?>
                <div class="text-center">
                    <div class="col-lg-12">
                        <?php TemplatePutPaginator($this->SingleBlog->page,$this->SingleBlog->offset,$this->SingleBlog->ItemsCount,$this->SingleBlog->conf->Export("PermalinkFolder"),""); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="well">
                    <form action="buscar">
                    <h4><?php echo _("Buscar"); ?></h4>
                    <div class="input-group">
                        <input type="text" class="form-control" id="q" name="q">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <hr>
<?php $this->loadtemplatepublic('footer.tpl.php'); ?>