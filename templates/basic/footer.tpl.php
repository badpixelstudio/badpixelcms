        <footer>
            <div class="row">
                <div class="col-lg-12">
                    <p><?php echo siteCopyright; ?></p>
                </div>
            </div>
        </footer>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <?php if (siteTemplateMinResources) { ?>
    <script type="text/javascript" src="<?php echo GetJSMin($this->template,"assets/js"); ?>"></script>
    <?php } else { ?>
    <script src="templates/<?php echo $this->template; ?>/assets/js/scripts.js"></script>
    <?php } ?>
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</body>

</html>
