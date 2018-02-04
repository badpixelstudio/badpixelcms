<?php $this->loadtemplatepublic('header.tpl.php'); ?>
<?php $this->loadtemplatepublic('menu.tpl.php'); ?>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo stripslashes($this->cats->Data['Title']); ?>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="./"><?php echo _("Inicio"); ?></a></li>
                    <?php if (isset($this->cats->BreadCrumb)) {
                    if(count($this->cats->BreadCrumb)>0) { 
                        foreach($this->cats->BreadCrumb as $title=>$link) {?>
                        <li><a href="<?php echo $link; ?>"><?php echo stripslashes($title); ?></a>
                        <?php }
                    } 
                    }?>
                    <li class="active"><?php echo stripslashes($this->cats->Data['Title']); ?></li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
            <?php if ($this->pages->ItemsCount>0) {
                foreach($this->pages->Items as $item) { 
                    $img="../templates/" . $this->template . "/assets/img/no-image.jpg"; 
                    if (is_file("public/images/" . $item['Image'])) { $img="images/" . $item['Image']; }?>
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <a href="<?php echo $item['Permalink']; ?>">
                                <img class="img-responsive img-hover" src="public/thumbnail.php?src=<?php echo $img; ?>&maxw=250&maxh=250&min=true&cut=true" alt="<?php echo stripslashes($item['Title']); ?>">
                            </a>
                        </div>
                        <div class="col-md-8">
                            <h3><a href="<?php echo $item['Permalink']; ?>"><?php echo stripslashes($item['Title']); ?></a></h3>
                            <p><?php echo LimitString(strip_tags(stripslashes($item['Page'])),120); ?></p>
                            <a class="btn btn-primary" href="<?php echo $item['Permalink']; ?>"><?php echo _("Leer más");?> <i class="fa fa-angle-right"></i></a>
                        </div>
                        <hr>
                    </div>
                    <br>
                    <div style="clear:both;"></div>
                <?php }
            } else { ?>
            <div class="alert alert-danger fade in"><?php echo _("Oops! Aquí parece que no hay nada todavía"); ?></div>
            <?php } ?>
            </div>
            <div class="col-md-4">
                <?php if($this->subcats->ItemsCount>0) { ?>
                <div class="well">
                    <div class="list-group">
                    <?php foreach($this->subcats->Items as $item) { ?>
                        <a href="<?php echo $item['Permalink']; ?>" class="list-group-item"><?php echo stripslashes($item['Title']); ?></a>
                    <?php } ?>
                    </div>
                </div>
                <?php } ?>
                <div class="well">
                    <form action="buscar">
                    <h4><?php echo _("Buscar"); ?></h4>
                    <div class="input-group">
                        <input type="text" class="form-control" id="q" name="q" value="">
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
            <?php TemplatePutPaginator($this->pages->page,$this->pages->offset,$this->pages->ItemsCount,$this->cats->Data['Permalink'],""); ?>
        </div>
        <hr>
<?php $this->loadtemplatepublic('footer.tpl.php'); ?>