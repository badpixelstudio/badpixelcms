<?php $this->loadtemplatepublic('header.tpl.php'); ?>
<?php $this->loadtemplatepublic('menu.tpl.php'); ?>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo $this->GetModuleName("services"); ?>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="./"><?php echo _("Inicio"); ?></a></li>
                    <li class="active"><?php echo $this->GetModuleName("services"); ?></li>
                </ol>
            </div>
        </div>
        <div class="row">
            <?php if($this->Services->ItemsCount>0) {
            foreach($this->Services->Items as $item) { ?>
            <div class="col-md-3 col-sm-6">
                <div class="panel panel-default text-center">
                    <div class="panel-heading">
                        <span class="fa-stack fa-5x">
                              <i class="fa fa-circle fa-stack-2x text-primary"></i>
                              <i class="fa <?php echo $item['Icon']; ?> fa-stack-1x fa-inverse"></i>
                        </span>
                    </div>
                    <div class="panel-body">
                        <h4><?php echo stripslashes($item['Title']); ?></h4>
                        <p><?php echo LimitString(strip_tags(stripslashes($item['LongDescription'])),40); ?></p>
                        <a href="<?php echo $item['Permalink']; ?>" class="btn btn-primary"><?php echo _("Ver más"); ?></a>
                    </div>
                </div>
            </div>
            <?php }
            } else { ?>
            <div class="alert alert-danger fade in"><?php echo _("Oops! Aquí parece que no hay nada todavía"); ?></div>
            <?php } ?>
        </div>
        <hr>
<?php $this->loadtemplatepublic('footer.tpl.php'); ?>