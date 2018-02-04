<body>
    <?php if($this->Sticker->Data!==false) { ?>
    <?php if($this->Sticker->Data['URL']!="") { ?><a href="<?php echo $this->Sticker->Data['URL']; ?>"><?php } ?>
    <div class="alert alert-danger sticker text-center">
        <?php echo stripslashes($this->Sticker->Data['Name']) ?>
    </div>
    <?php if($this->Sticker->Data['URL']!="") { ?></a><?php } ?>
    <?php } ?>
    <nav class="navbar navbar-inverse navbar-static-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="./"><?php echo siteTitle; ?></a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                <?php foreach($this->Menu->Menu as $item) { 
                    $link="javascript:void(0);";
                    $li_class="";
                    $a_add="";
                    $icon="";
                    if ($item['Link']!="") { $link=$item['Link']; } 
                    if (count($item['Items'])>0) {
                        $li_class="dropdown";
                        $a_add='class="dropdown-toggle" data-toggle="dropdown"';
                        $icon='<b class="caret"></b>';
                    }
                    ?>
                    <li class="<?php echo $li_class; ?>">
                        <a href="<?php echo $link; ?>" <?php echo $a_add; ?>><?php echo stripslashes($item['Title']); ?> <?php echo $icon; ?></a>
                        <?php if (count($item['Items'])>0) { ?>
                        <ul class="dropdown-menu">
                        <?php foreach($item['Items'] as $subitem) { ?>
                            <li><a href="<?php echo $subitem['Link']; ?>"><?php echo stripslashes($subitem['Title']); ?></a></li>
                        <?php } ?>
                        </ul>
                        <?php } ?>
                    </li>
                <?php } ?>
                </ul>
            </div>
        </div>
    </nav>