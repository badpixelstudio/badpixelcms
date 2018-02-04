<?php $this->loadtemplatepublic('header.tpl.php'); ?>
<?php $this->loadtemplatepublic('menu.tpl.php'); ?>
<?php if($this->Slider->ItemsCount>0) { ?>
    <header id="myCarousel" class="carousel slide">
        <ol class="carousel-indicators">
        <?php for($x=0;$x<$this->Slider->ItemsCount;$x++) { ?>
            <li data-target="#myCarousel" data-slide-to="<?php echo $x; ?>" class="<?php if($x==0) { echo "active"; } ?>"></li>
        <?php } ?>
        </ol>
        <div class="carousel-inner">
        <?php $count=0;
        foreach($this->Slider->Items as $item) { ?>
            <a class="item <?php if($count==0) { echo "active"; } ?>" <?php if($item['URL']!="") { ?>href="<?php echo $item['URL']; ?>"<?php } ?> >
                <div class="fill" style="background-image:url('public/slider/<?php echo $item['Image']; ?>');"></div>
                <?php if(($item['ShowTitle']==1) and ($item['Name']!="")) { ?>
                <div class="carousel-caption">
                    <h2><?php echo stripslashes($item['Name']); ?></h2>
                </div>
                <?php } ?>
            </a>
        <?php $count++;
        } ?>
        </div>
        <a class="left carousel-control" href="#myCarousel" data-slide="prev">
            <span class="icon-prev"></span>
        </a>
        <a class="right carousel-control" href="#myCarousel" data-slide="next">
            <span class="icon-next"></span>
        </a>
    </header>
<?php } ?>
    <div class="container">
    <?php if($this->Contents->Data!==false) { ?>
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">
                    <?php echo stripslashes($this->Contents->Data['Title']); ?>
                </h1>
            </div>
            <div class="col-md-<?php echo ($this->Contents->Data['Image_images']!="")? 6 : 12; ?>">
                <?php echo stripslashes($this->Contents->Data['LongDescription']); ?>
            </div>
            <?php if($this->Contents->Data['Image_images']!="") { ?>
            <div class="col-md-6">
                <img class="img-responsive" src="<?php echo $this->Contents->Data['Image_images']; ?>" alt="<?php echo stripslashes($this->Contents->Data['Title']); ?>">
            </div>
            <?php } ?>
        </div>
        <?php if ($this->Contents->XtraImages->Total>0) { ?>
        <div class="row">
            <div class="col-lg-12">
                <br>
            </div>
            <?php foreach($this->Contents->XtraImages->Data as $image) { ?>
            <div class="col-md-3 col-sm-4">
                <a href="public/images/<?php echo $image['Image']; ?>" data-title="<?php echo stripslashes($image['Description']); ?>" data-lightbox="gallery" >
                    <img class="img-responsive img-portfolio img-hover" src="public/thumbnails/<?php echo $image['Image']; ?>" alt="<?php echo stripslashes($image['Description']); ?>">
                </a>
            </div>
            <?php } ?>
        </div>
        <?php } ?>
        <hr>
    <?php } ?>
<?php $this->loadtemplatepublic('footer.tpl.php'); ?>