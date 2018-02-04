<?php
define('ZCMSPATH', '../');
$Core->loadtemplatepublic('header.inc.php');
//$Core->loadtemplatepublic('menu.inc.php');
?>
<link rel="stylesheet" media="screen" type="text/css" href="../templates/<?php echo $Core->template; ?>/css/forms.css" />
<style>
.mensaje {
	background-color:#5CBBCA;
	padding: 15px 15px;	
	box-shadow: 0 1px 0 rgba(255, 255, 255, 0.5) inset, 0 2px 5px rgba(255, 255, 255, 0.1) inset, 0 -2px 8px rgba(0, 0, 0, 0.1) inset;
    color: #ffffff;
    margin: 9px 0;
    text-shadow: 0 1px 0 rgba(255, 255, 255, 0.4);
	border-radius: 4px;

}
</style>
<div class="bgbody">
<div class="wrapper">

<div class="modulo-index">
	<div class="localesmain">
    <div class="blog-tittle">
        <h2>Iniciar sesi&oacute;n con Twitter<br />
        <span>Necesitamos tu email, as&iacute; podr&aacute;s recuperar tu contrase&ntilde;a en cualquier momento</span></h2>
    </div>
    </div>
</div>
<?php if ($Core->message!="") { ?>
	<div class="modulo-index">
		<div class="mensaje"><?php echo urldecode($Core->message); ?></div>
    </div>
<?php } ?>
<div class="modulo-index">
    <form class="form_contact" name="email" id="email" method="post" action="lib/oauth_login.php?method=twitter">
    <fieldset>
        <section>
          <label>Correo Electr&oacute;nico</label>
        	<div><input name="Form_Email" id="Form_Email" /></div>
        </section>
    </fieldset>
	<fieldset>
	    <section>
            <div><input type="submit" value="Guardar" />
    			<!--<input type="hidden" id="referer" name="referer" value="<?php echo $_GET['referer']; ?>" />-->
            </div>
        </section>
    </fieldset>
    <?php foreach ($Datos as $nombre=>$campo) { ?>
    	<input type="hidden" id="<?php echo $nombre; ?>" name="<?php echo $nombre; ?>" value="<?php echo $campo; ?>" />
    <?php } ?>
    </form>
</div><!--MODULO LEFT-->
<div class="clear"></div>

</div><!--WRAPPER-->
</div><!--BGBODY-->    