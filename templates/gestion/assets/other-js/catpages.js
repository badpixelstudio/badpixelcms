$(document).ready(function () {
	changetype($('#Form_CategoryType').val());
	$('#Form_CategoryType').change(function() {
		changetype($(this).val());
	});
	function changetype(tipo) {
     	if (tipo=="0") {
			$('#toptab_2').hide();
		} else {
			$('#toptab_2').show();
		}
	}
	if($("#Cnf_Default").length) {
		if ($("#Cnf_Default").val()=1) {
			$('#toptab_1').hide();
		}
	}
});