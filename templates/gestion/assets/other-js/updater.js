$(document).ready(function(){
	var total=$("#totalcount").val();
	var actual=0;
	var type="";
	var action="";
	var url="";
	var progressbar=0;
	var pasos=(parseInt(total)*3)+1
	var progressstep=parseInt(100/pasos);

	GetNextFile();

	function UpdateProgressBar() {
		progressbar=progressbar+progressstep;
		$('#progressbar').attr("style","width: "+progressbar+ '%');
	}

	function GetNextFile() {
		UpdateProgressBar();
		if (actual<total) {
			var procesar=$('#file_'+actual).val();
			procesar=procesar.split(";");
			type=procesar[0];
			action=procesar[1];
			url=procesar[2];
			$('#ajaxcontent').append("<h4>Procesando "+type+"</h4>");
			DownloadFile();
		} else {
			InstallPackages();
		}
	}

	function DownloadFile() {
		$('#ajaxcontent').append("Descargando el paquete...<br>");
		$.ajax({
			type: "GET",
			url: "../public/update.php?action=download&url="+encodeURI(url),
			success: function(data){
				if (data==1) {
					UpdateProgressBar();
					UnzipFile();
				} else {
					$('#ajaxcontent').append("Error: no se ha podido descargar el paquete<br>");
					actual++;
					UpdateProgressBar();
					UpdateProgressBar();
					GetNextFile();
				}
			},
			error: function() {
				$('#ajaxcontent').append("Error: no se ha podido descargar el paquete<br>");
				actual++;
				UpdateProgressBar();
				UpdateProgressBar();
				GetNextFile();
			}
		});
	}

	function UnzipFile() {
		$('#ajaxcontent').append("Descomprimiendo archivos...<br>");
		$.ajax({
			type: "GET",
			url: "../public/update.php?action=unzip&type="+encodeURI(action)+"&url="+encodeURI(url),
			success: function(data){
				if (data==1) {
					$('#ajaxcontent').append("Listo para ser instalado<br>");
				} else {
					$('#ajaxcontent').append("Error: no se ha podido descomprimir el archivo<br>");
				}
			},
			error: function() {
				$('#ajaxcontent').append("Error: no se ha podido descomprimir el archivo<br>");
			}
		});
		UpdateProgressBar();
		actual++;
		GetNextFile();
	}

	function InstallPackages() {
		$('#ajaxcontent').append("<h2>Desplegando archivos...</h2>");
		$.ajax({
			type: "GET",
			url: "../public/update.php?action=upgradefiles",
			success: function(data){
				if (data==1) {
					$('#ajaxcontent').append("Realizando tareas finales<br>");
				} else {
					$('#ajaxcontent').append("Error: no se ha podido desplegar los archivos<br>");
				}
			},
			error: function() {
				$('#ajaxcontent').append("Error: no se ha podido desplegar los archivos<br>");
			}
		});
		UpdateProgressBar();
		$('#ajaxcontent').append("<h2>Volviendo al panel de gestión. Por favor, espere...</h2>");
		setTimeout(15000,GoPanel());
	}

	function GoPanel() {
		var baseHref = document.getElementsByTagName('base')[0].href
		location.href=baseHref + "modules";
	}
});