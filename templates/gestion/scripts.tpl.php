<!--[if lt IE 9]>
<script type="text/javascript" src="../templates/gestion/assets/other-js/respond.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/other-js/plugins/excanvas.min.js"></script> 
<![endif]-->
<script type="text/javascript" src="../templates/gestion/assets/other-js/01.jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/other-js/02.jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/other-js/03.jquery-ui-1.10.3.custom.min.js"></script>
<script>var root_route="<?php echo siteprotocol . sitedomain; ?>";</script>
<?php if (sitePanelMinResources) { ?>
<script type="text/javascript" src="../<?php echo GetJSMin("gestion","assets/js"); ?>"></script>
<?php } else { ?>
<script type="text/javascript" src="../templates/gestion/assets/js/04.bootstrap.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/05.bootstrap-hover-dropdown.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/06.jquery.slimscroll.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/07.jquery.blockui.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/08.jquery.cokie.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/09.jquery.uniform.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/10.jquery.flot.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/11.jquery.flot.resize.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/12.jquery.flot.pie.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/13.jquery.flot.stack.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/14.jquery.flot.crosshair.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/15.jquery.flot.categories.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/16.moment.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/17.daterangepicker.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/18.jquery.nestable.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/19.fullcalendar.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/20.jquery.easypiechart.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/21.jquery.sparkline.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/22.bootstrap-datepicker.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/23.ckeditor.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/24.spinner.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/25.bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/26.bootstrap-timepicker.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/27.select2.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/28.jquery.dataTables.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/29.dataTables.tableTools.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/30.DT_bootstrap.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/31.jquery.validate.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/32.additional-methods.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/33.typeahead.bundle.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/34.bootstrap-tagsinput.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/35.bootstrap-switch.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/36.bootbox.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/37.jstree.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/40.wl_File.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/41.wl_Gallery.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/42.scripts.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/43.fancybox.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/44.jquery.mockjax.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/45.bootstrap-editable.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/50.metronic.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/51.layout.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/52.login.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/60.pages.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/70.sh_main.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/71.sh_javascript.min.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/72.jquery.jrac.js"></script>
<script type="text/javascript" src="../templates/gestion/assets/js/99.loader.js"></script>
<?php } ?>
<?php 
if ($this->TemplateLoadScript!=="") {
	if (is_array($this->TemplateLoadScript)) {
		foreach($this->TemplateLoadScript as $script) {
			if (is_file(sitepath . "templates/gestion/assets/other-js/" . $script)) { $script="../templates/gestion/assets/other-js/" . $script; }
			echo '<script type="text/javascript" src="' . $script . '"></script>';
		}
	} else {
		$script=$this->TemplateLoadScript;
		if (is_file(sitepath . "templates/gestion/assets/other-js/" . $script)) { $script="../templates/gestion/assets/other-js/" . $script; }
		echo '<script type="text/javascript" src="' . $script . '"></script>';
	}
}
?>