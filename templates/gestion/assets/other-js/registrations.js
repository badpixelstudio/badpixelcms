$(document).ready(function() {
	function ChangeCondiciones() {
		var esta_habilitado=$('#Form_EnableAcceptConditions').is(':checked');
		if (esta_habilitado) {
			$("#Block_Condiciones").hide(300);
		} else {
			$("#Block_Condiciones").show(300);
		}	
	}
	$("#Form_EnableAcceptConditions").click(function() { ChangeCondiciones(); });
});