<?php $this->loadtemplatepublic('header.tpl.php'); ?>
<?php $this->loadtemplatepublic('menu.tpl.php'); ?>
    <div class="container">

        <!-- Page Heading/Breadcrumbs -->
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo $this->GetModuleName("contact"); ?>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="./"><?php echo _("Inicio"); ?></a>
                    </li>
                    <li class="active"><?php echo $this->GetModuleName("contact"); ?></li>
                </ol>
            </div>
        </div>
        <!-- /.row -->

        <!-- Content Row -->
        <?php if (siteOwnerGeolocation!="") { ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="map" id="map" rel="<?php echo siteprotocol . sitedomain; ?>" lat="<?php echo $this->Data['GeoLat']; ?>" lng="<?php echo $this->Data['GeoLng']; ?>" z="<?php echo $this->Data['GeoZoom']; ?>" key="<?php echo siteGoogleMapsAPIKey; ?>" icon=""></div>
            </div>
        </div>
        <?php } ?>
        <div class="row">
            <div class="col-md-8">
                <h3><?php echo _("Envíanos un mensaje"); ?></h3>
                <div class="alert alert-danger fade in" id="sendcontact_error" style="display:none;"></div>
                <form id="sendcontact" novalidate>
                    <div class="control-group form-group">
                        <div class="controls">
                            <label><?php echo _("Nombre"); ?></label>
                            <input type="text" class="form-control" id="Nombre" name="Nombre">
                            <p class="help-block"></p>
                        </div>
                    </div>
                    <div class="control-group form-group">
                        <div class="controls">
                            <label><?php echo _("Teléfono"); ?></label>
                            <input type="tel" class="form-control" id="Telefono" name="Telefono">
                        </div>
                    </div>
                    <div class="control-group form-group">
                        <div class="controls">
                            <label><?php echo _("Correo electrónico"); ?></label>
                            <input type="email" class="form-control" id="Email" name="Email">
                        </div>
                    </div>
                    <div class="control-group form-group">
                        <div class="controls">
                            <label><?php echo _("Mensaje"); ?></label>
                            <textarea rows="10" cols="100" class="form-control" id="Mensaje" name="Mensaje" maxlength="999" style="resize:none"></textarea>
                        </div>
                    </div>
                    <?php if ($this->Contact->conf->Check("UseReCaptcha")) { ?>
                    <div class="control-group form-group">
                        <div class="controls">
                            <div class="g-recaptcha" data-sitekey="<?php echo $this->Contact->conf->Export("ReCaptchaKey"); ?>"></div>
                        </div>
                    </div>
                    <?php } ?>
                    <button type="submit" class="btn btn-primary" id="sendcontact_submit"><?php echo _("Enviar"); ?></button>
                </form>
            </div>
            <div class="col-md-4">
                <h3><?php echo siteTitle; ?></h3>
                <p>
                    <?php echo siteOwnerName; ?><br>
                    <?php echo siteOwnerStreet; ?><br>
                    <?php echo siteOwnerZipCode; ?> <?php echo siteOwnerCity; ?><?php if(siteOwnerState!=""){ echo " (".siteOwnerState.") "; }?><?php echo siteOwnerCountry; ?>
                </p>
                <?php if(siteOwnerPhone!=""){?>
                <p><i class="fa fa-phone"></i> 
                    <abbr title="Teléfono"></abbr>: <?php echo siteOwnerPhone; ?></p>
                <?php } ?>
                <?php if(siteOwnerPublicEmail!=""){?>
                <p><i class="fa fa-envelope-o"></i> 
                    <abbr title="Email">E</abbr>: <?php echo siteOwnerPublicEmail; ?></a>
                </p>
                <?php } ?>
                <ul class="list-unstyled list-inline list-social-icons">
                    <?php if(siteTwitterURL!="") { ?><li><a href="<?php echo siteTwitterURL; ?>"><i class="fa fa-twitter-square fa-2x"></i></a></li><?php } ?>
                    <?php if(siteFacebookURL!="") { ?><li><a href="<?php echo siteFacebookURL; ?>"><i class="fa fa-facebook-square fa-2x"></i></a></li><?php } ?>
                    <?php if(siteGooglePlusURL!="") { ?><li><a href="<?php echo siteGooglePlusURL; ?>"><i class="fa fa-google-square fa-2x"></i></a></li><?php } ?>
                    <?php if(siteLinkedInURL!="") { ?><li><a href="<?php echo siteLinkedInURL; ?>"><i class="fa fa-linkedin-square fa-2x"></i></a></li><?php } ?>
                    <?php if(siteYouTubeURL!="") { ?><li><a href="<?php echo siteYouTubeURL; ?>"><i class="fa fa-youtube-play-square fa-2x"></i></a></li><?php } ?>
                    <?php if(sitePinterestURL!="") { ?><li><a href="<?php echo sitePinterestURL; ?>"><i class="fa fa-pinterest-square fa-2x"></i></a></li><?php } ?>
                    <?php if(siteInstagramURL!="") { ?><li><a href="<?php echo siteInstagramURL; ?>"><i class="fa fa-instagram-square fa-2x"></i></a></li><?php } ?>
                    <?php if(siteSoundCloudURL!="") { ?><li><a href="<?php echo siteSoundCloudURL; ?>"><i class="fa fa-soundcloud-square fa-2x"></i></a></li><?php } ?>
                    <?php if(siteFlickrURL!="") { ?><li><a href="<?php echo siteFlickrURL; ?>"><i class="fa fa-flickr-square fa-2x"></i></a></li><?php } ?>
                </ul>
            </div>
        </div>

        <div class="row">
            

        </div>


        <hr>
<?php $this->loadtemplatepublic('footer.tpl.php'); ?>