<?php $this->loadtemplatepublic('header.tpl.php'); ?>
<?php $this->loadtemplatepublic('menu.tpl.php'); ?>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo stripslashes($this->SingleBlog->Data['Title']); ?>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="./"><?php echo _("Inicio"); ?></a></li>
                    <li><a href="<?php echo $this->SingleBlog->conf->Export("PermalinkFolder"); ?>"><?php echo $this->GetModuleName("singleblog"); ?></a></li>
                    <li class="active"><?php echo stripslashes($this->SingleBlog->Data['Title']); ?></li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-md-<?php echo (($this->SingleBlog->XtraImages->Total>0) or ($this->SingleBlog->XtraLinks->Total>0) or ($this->SingleBlog->XtraAttachments->Total>0))? 9 : 12; ?>">
                <p><i class="fa fa-clock-o"></i> <?php echo _("Publicado el"); ?> <?php echo $this->SingleBlog->Data['DatePublish']; ?></p>
                <?php if($this->SingleBlog->Data['Image_images']!="") { ?>
                    <img class="img-responsive" src="<?php echo $this->SingleBlog->Data['Image_images']; ?>" alt="<?php echo stripslashes($this->SingleBlog->Data['Title']); ?>">
                <?php } ?>
                <h2><?php echo stripslashes($this->SingleBlog->Data['Title']); ?></h2>
                <?php echo stripslashes($this->SingleBlog->Data['LongDescription']); ?>
                <?php if($this->SingleBlog->XtraVideos->Total>0) { ?>
                <hr>
                <?php foreach($this->SingleBlog->XtraVideos->Data as $video) { ?>
                    <h4><?php echo stripslashes($video['Description']); ?></h4>
                    <?php echo ChangeWidthEmbed(stripslashes($video['Embed']),"100%",307); ?>
                <?php } ?>    
                <?php } ?>
            </div>
            <?php if(($this->SingleBlog->XtraImages->Total>0) or ($this->SingleBlog->XtraLinks->Total>0) or ($this->SingleBlog->XtraAttachments->Total>0)) { ?>
            <div class="col-md-3">
                
                <?php if ($this->SingleBlog->XtraImages->Total>0) { ?>
                <div class="row">
                    <?php foreach($this->SingleBlog->XtraImages->Data as $image) { ?>
                    <div class="col-md-4 col-sm-2">
                        <a href="public/images/<?php echo $image['Image']; ?>" data-title="<?php echo stripslashes($image['Description']); ?>" data-lightbox="gallery" >
                            <img class="img-responsive img-portfolio img-hover" src="public/thumbnails/<?php echo $image['Image']; ?>" alt="<?php echo stripslashes($image['Description']); ?>">
                        </a>
                    </div>
                    <?php } ?>
                </div>
                <?php } ?>
                <div class="list-group">
                <?php if ($this->SingleBlog->XtraLinks->Total>0) { 
                    foreach($this->SingleBlog->XtraLinks->Data as $link) { ?>
                    <a href="<?php echo $link['Link']; ?>" class="list-group-item"><i class="fa fa-link"></i> <?php echo stripslashes($link['Description']); ?></a>
                    <?php }
                } ?>
                <?php if ($this->SingleBlog->XtraAttachments->Total>0) { 
                    foreach($this->SingleBlog->XtraAttachments->Data as $attach) { ?>
                    <a href="public/files/<?php echo $attach['File']; ?>" class="list-group-item"><i class="fa fa-download"></i> <?php echo stripslashes($attach['Description']); ?></a>
                    <?php }
                } ?>
                </div>
            </div>
            <?php } ?>
            
        </div>

        <hr>
<?php $this->loadtemplatepublic('footer.tpl.php'); ?>