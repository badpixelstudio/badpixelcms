function ChangeFCMDestination() {
	var current=$("#Form_Destination").val();
	if (current=="user") {
		$("#Block_Form_UserID").slideDown();
		$('#Block_Form_Topic').slideUp();
		$('#Block_Form_IDDevice').slideUp();
	}
	if (current=="topic") {
		$("#Block_Form_UserID").slideUp();
		$('#Block_Form_Topic').slideDown();
		$('#Block_Form_IDDevice').slideUp();
	}
	if (current=="device") {
		$("#Block_Form_UserID").slideUp();
		$('#Block_Form_Topic').slideUp();
		$('#Block_Form_IDDevice').slideDown();
	}
}
$(document).ready(function () {
	if ($("#Form_Destination").length) {
		$("#Form_Destination").change(function() { ChangeFCMDestination(); });
		ChangeFCMDestination();
	}
});