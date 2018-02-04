<?php $this->loadtemplatepublic('header.tpl.php'); ?>
<?php $this->loadtemplatepublic('menu.tpl.php'); ?>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo _("Buscar"); ?>
                    <small><?php echo $this->searching; ?></small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="./"><?php echo _("Inicio"); ?></a>
                    </li>
                    <li class="active"><?php echo _("Buscar"); ?></li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
            <?php if ($this->ItemsCount>0) {
                foreach($this->Items as $item) { 
                    $img="../templates/" . $this->template . "/assets/img/no-image.jpg"; 
                    if (is_file("public/images/" . $item['Image'])) { $img="public/images/" . $item['Image']; }?>
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <a href="<?php echo $item['Permalink']; ?>">
                                <img class="img-responsive img-hover" src="public/thumbnail.php?src=<?php echo $img; ?>&maxw=250&maxh=250&min=true&cut=true" alt="<?php echo stripslashes($item['Title']); ?>">
                            </a>
                        </div>
                        <div class="col-md-8">
                            <h3><a href="<?php echo $item['Permalink']; ?>"><?php echo stripslashes($item['Title']); ?></a></h3>
                            <p><?php echo LimitString(strip_tags(stripslashes($item['Text'])),120); ?></p>
                            <a class="btn btn-primary" href="<?php echo $item['Permalink']; ?>"><?php echo _("Leer más");?> <i class="fa fa-angle-right"></i></a>
                        </div>
                        <hr>
                    </div>
                    <br>
                    <div style="clear:both;"></div>
                <?php }
            } else { ?>



            <?php } ?>
            </div>
            <div class="col-md-4">
                <div class="well">
                    <form action="buscar">
                    <h4><?php echo _("Buscar"); ?></h4>
                    <div class="input-group">
                        <input type="text" class="form-control" id="q" name="q" value="<?php echo $this->searching; ?>">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                    </form>
                </div>
            </div>
        </div>   
        <hr>
        <div class="row text-center">
            <?php TemplatePutPaginator($this->page,$this->offset,$this->ItemsCount,"buscar","q=" . $this->searching); ?>
        </div>
        <hr>
<?php $this->loadtemplatepublic('footer.tpl.php'); ?>