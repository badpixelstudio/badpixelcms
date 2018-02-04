$(document).ready(function() {
	function ChangeMinimizeOrigin() {
		var estado=$('#Form_MinimizerOrigin').val();
		if (estado=="template") {
			$("#Block_Form_folder").show(300);
			$("#Block_Form_ChangeURL").show(300);
			$("#Block_uploads").hide(300);
			$("#Form_folder").attr("required","required");
		} else {
			$("#Block_Form_folder").hide(300);
			$("#Block_Form_ChangeURL").hide(300);
			$("#Block_uploads").show(300);
			$("#Form_folder").removeAttr("required");
		}	
	}
	$("#Form_MinimizerOrigin").change(function() { ChangeMinimizeOrigin(); });
	if ($('#Form_MinimizerOrigin').length) {ChangeMinimizeOrigin();}
});