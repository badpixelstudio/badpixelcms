<?php $this->loadtemplatepublic('header.tpl.php'); ?>
<?php $this->loadtemplatepublic('menu.tpl.php'); ?>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo stripslashes($this->pages->Data['Title']); ?>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="./"><?php echo _("Inicio"); ?></a></li>
                    <?php if (isset($this->cats->BreadCrumb)) {
                        if(count($this->pages->BreadCrumb)>0) { 
                        foreach($this->pages->BreadCrumb as $title=>$link) {?>
                        <li><a href="<?php echo $link; ?>"><?php echo stripslashes($title); ?></a>
                        <?php }
                    } 
                    }?>
                    <li class="active"><?php echo stripslashes($this->pages->Data['Title']); ?></li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-md-<?php echo (($this->pages->Data['Image_images']!="") or ($this->pages->XtraImages->Total>0) or ($this->pages->XtraLinks->Total>0) or ($this->pages->XtraAttachments->Total>0))? 9 : 12; ?>">
                <h2><?php echo stripslashes($this->pages->Data['Title']); ?></h2>
                <?php echo stripslashes($this->pages->Data['Page']); ?>
                <?php if($this->pages->XtraVideos->Total>0) { ?>
                <hr>
                <?php foreach($this->pages->XtraVideos->Data as $video) { ?>
                    <h4><?php echo stripslashes($video['Description']); ?></h4>
                    <?php echo ChangeWidthEmbed(stripslashes($video['Embed']),"100%",307); ?>
                <?php } ?>    
                <?php } ?>
            </div>
            <?php if(($this->pages->Data['Image_images']!="") or ($this->pages->XtraImages->Total>0) or ($this->pages->XtraLinks->Total>0) or ($this->pages->XtraAttachments->Total>0)) { ?>
            <div class="col-md-3">
                <?php if($this->pages->Data['Image_images']!="") { ?>
                <div class="row">
                    <div class="col-md-12">
                    <a href="<?php echo $this->pages->Data['Image_images']; ?>" data-title="<?php echo stripslashes($this->pages->Data['Title']); ?>" data-lightbox="gallery" >
                            <img class="img-responsive img-portfolio img-hover" src="<?php echo $this->pages->Data['Image_images']; ?>" alt="<?php echo stripslashes($this->pages->Data['Title']); ?>">
                        </a>
                    </div>
                </div>
                <?php } ?>
                <?php if ($this->pages->XtraImages->Total>0) { ?>
                <div class="row">
                    <?php foreach($this->pages->XtraImages->Data as $image) { ?>
                    <div class="col-md-4 col-sm-2">
                        <a href="public/images/<?php echo $image['Image']; ?>" data-title="<?php echo stripslashes($image['Description']); ?>" data-lightbox="gallery" >
                            <img class="img-responsive img-portfolio img-hover" src="public/thumbnails/<?php echo $image['Image']; ?>" alt="<?php echo stripslashes($image['Description']); ?>">
                        </a>
                    </div>
                    <?php } ?>
                </div>
                <?php } ?>
                <div class="list-group">
                <?php if ($this->pages->XtraLinks->Total>0) { 
                    foreach($this->pages->XtraLinks->Data as $link) { ?>
                    <a href="<?php echo $link['Link']; ?>" class="list-group-item"><i class="fa fa-link"></i> <?php echo stripslashes($link['Description']); ?></a>
                    <?php }
                } ?>
                <?php if ($this->pages->XtraAttachments->Total>0) { 
                    foreach($this->pages->XtraAttachments->Data as $attach) { ?>
                    <a href="public/files/<?php echo $attach['File']; ?>" class="list-group-item"><i class="fa fa-download"></i> <?php echo stripslashes($attach['Description']); ?></a>
                    <?php }
                } ?>
                </div>
            </div>
            <?php } ?>
            
        </div>

        <hr>
<?php $this->loadtemplatepublic('footer.tpl.php'); ?>