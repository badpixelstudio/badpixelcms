$(document).ready(function() {
	ProcessImage();
});

function ProcessImage() {
	var url=$('.active').attr("script");
	$.ajax({
		type: "POST",
		dataType: "json",
		url: url,
		success: function(msj){
			console.log(msj);
			//$("div.info span").append('...' +msj);
			if (msj['Total']!=0) { 
				console.log(msj);
				$('#mensaje').html("Quedan " + msj['Total'] + " imágenes por procesar. Por favor, espere...");
				$('#img').html("<img src='"+msj['Processed']+"' />");
				ProcessImage(); 
			} else {
				$('#mensaje').html("Redirigiendo el navegador...");
				$('#img').html("");				
				document.location=msj['Return'];
			}
		}
	});
	
}