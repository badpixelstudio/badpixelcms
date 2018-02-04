<?php $this->loadtemplatepublic('header.tpl.php'); ?>
<?php $this->loadtemplatepublic('menu.tpl.php'); ?>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">404
                    <small><?php echo _("Página no encontrada"); ?></small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="./"><?php echo _("Inicio"); ?></a>
                    </li>
                    <li class="active">404</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="jumbotron">
                    <h1><span class="error-404">404</span>
                    </h1>
                    <p><?php echo _("La página que buscas no se encuentra disponible"); ?></p>
                </div>
            </div>
        </div>
        <hr>
<?php $this->loadtemplatepublic('footer.tpl.php'); ?>