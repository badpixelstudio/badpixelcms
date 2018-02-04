		</div>
	</div>
	<!-- END CONTENT -->
</div>
<!-- END CONTAINER -->
<!-- BEGIN FOOTER -->
<div class="page-footer">
	<div class="page-footer-inner">
		 <?php echo siteCopyright; ?> · Versión <?php echo $this->AppVersion; ?>
		 <?php if (siteDebugActive) { $this->CalcExecTime(); ?> · <strong>Datos de depuración:</strong> 
		 <?php echo $GLOBALS['QuerysCount']; ?> Querys (<?php echo $GLOBALS['QuerysTimeExec']; ?> s.) - Tiempo empleado: <?php echo $GLOBALS['AppTimeExec']; ?> s.
		 <?php } ?>
	</div>
	<div class="page-footer-tools">
		<span class="go-top">
		<i class="fa fa-angle-up"></i>
		</span>
	</div>
</div>
<!-- END FOOTER -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<?php require_once("scripts.tpl.php");  ?>
</body>
</html>