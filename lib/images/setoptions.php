<?php
require_once("thumbs.php");
$precarga_field="";
if (isset($_GET['f'])) {
	$precarga_field=$_GET['f'];
}
?>
<html>    
  <head> 
  	<meta charset="utf-8"/>
  	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <style>
      *{
        font-family: verdana;
        font-size: 10px;
      }
      body{
        text-align:center;
		background: #E1E1E1;
      }
      table { width: 100%; border: 0; }
      table td { text-align: center; }
      table tr { height: 22px;}
      select { width: 70px;}
      .watermark { background: #ccc;}
      .helper { border-bottom: 1px dotted blue;}
      tr .btn { width:  100%; border: 0; }
      
    </style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<body>
	<table >
		<thead>
			<tr>
				<td colspan="4"><a class="helper" title="Especifica el tipo de recorte a realizar">Imagen</a></td>
				<td colspan="4" class="watermark">Marca de agua</td>
				<td><input type="button" id="addOpt" value="Añadir" class=" btn btn-xs btn-info"></td>
			</tr>
			<tr>
				<td><a class="helper" title="Nombre de subcarpeta de /public">Carpeta</a></td>
				<td><a class="helper" title="Indicar píxeles, rango de píxeles (separado por guión), o 'no' para no procesar">Ancho</a></td>
				<td><a class="helper" title="Indicar píxeles o rango de píxeles (separado por guión)">Alto</a></td>
				<td><a class="helper" title="Indicar color RGB de fondo, 'crop' para forzar el recorte o 'nozoom' para evitar que la imagen se amplíe y pierda resolución">Fondo</a></td>
				<td class="watermark"><a class="helper" title="Selecciona la posición de la marca de agua">Posición</a></td>
				<td class="watermark"><a class="helper" title="Número de repeticiones de la marca de agua">Rep</a></td>
				<td class="watermark"><a class="helper" title="Ruta de la imagen a usar en la marca de agua">Imagen</a></td>
				<td class="watermark"><a class="helper" title="Margen en píxeles a usar en la marca de agua">Marg.</a></td>
				<td><input type="button" id="cleanOpt" class="btn btn-xs btn-danger" value="Limpiar"></td>
			</tr>
		</thead>
		<tbody>

		</tbody>
	</table>
	<hr>
    <input type="hidden" name="field" id="field" value="<?php echo $precarga_field; ?>">
    <input type="button" id="finish" class="btn btn-success" value="Devolver datos y cerrar">
  </body>
</html>
<script>
var options = [];
$(document).ready(function() {
	function loadField() {
		var nameField=$("#field").val();
		if(nameField!="") {
			if(parent.$('#'+nameField).length) {
				var currentOptions=parent.$('#'+nameField).val();
				explodeOptions(currentOptions);
			}	
		}
	}

	function explodeOptions(currentOptions) {
		var tmpOptions=currentOptions.split(";");
		for(var i=0; i<tmpOptions.length; i++) {
			var opt=tmpOptions[i];
			opt=opt.replace("(","");
			opt=opt.replace(")","");
			var parts = opt.split(",");
			var optItem = [];
			optItem['folder']="";
			optItem['width']="";
			optItem['height']="";
			optItem['background']="";
			optItem['wm_pos']="";
			optItem['wm_rep']="";
			optItem['wm_image']="";
			optItem['wm_margin']="";
			for(var x=0;x<parts.length;x++) {
				var val=parts[x];
				val=val.trim();
				if(x==0) { optItem['folder']=val; }
				if(x==1) { optItem['width']=val; }
				if(x==2) { optItem['height']=val; }
				if(x==3) { optItem['background']=val; }
				if(x==4) { optItem['wm_pos']=val; }
				if(x==5) { optItem['wm_rep']=val; }
				if(x==6) { optItem['wm_image']=val; }
				if(x==7) { optItem['wm_margin']=val; }
			}
			options.push(optItem);
			console.log(options.length);
			AddTableItem(options.length-1);
		}
		//console.log(options);
	}

	function AddTableItem(id) {
		var folder="";
		var width="";
		var height="";
		var background="";
		var wm_pos="";
		var wm_rep="";
		var wm_image="";
		var wm_margin="";
		if (id!="new") {
			if (options[id]) {
				folder=options[id]['folder'];
				width=options[id]['width'];
				height=options[id]['height'];
				background=options[id]['background'];
				wm_pos=options[id]['wm_pos'];
				wm_rep=options[id]['wm_rep'];
				wm_image=options[id]['wm_image'];
				wm_margin=options[id]['wm_margin'];
			}
		} 
		var add="<tr>";
		add=add+"<td><input type='text' class='i_folders' name='folder[]' value='"+ folder +"' size='10'></td>";
		add=add+"<td><input type='text' class='i_widths' name='width[]' value='"+ width +"' size='3'></td>";
		add=add+"<td><input type='text' class='i_heights' name='height[]' value='"+ height +"' size='3'></td>";
		add=add+"<td><input type='text' class='i_backgrounds' name='background[]' value='"+ background +"' size='4'></td>";
		add=add+"<td class='watermark'><select class='i_wm_poss' name='wm_pos[]'>";
		if (wm_pos=="") { selected="selected"; } else { selected=""; }
		add=add+"<option " + selected + " value=''></option>";
		if (wm_pos=="center") { selected="selected"; } else { selected=""; }
		add=add+"<option " + selected + " value='center'>center</option>";
		if (wm_pos=="topleft") { selected="selected"; } else { selected=""; }
		add=add+"<option " + selected + " value='topleft'>topleft</option>";
		if (wm_pos=="topright") { selected="selected"; } else { selected=""; }
		add=add+"<option " + selected + " value='topright'>topright</option>";
		if (wm_pos=="topcenter") { selected="selected"; } else { selected=""; }
		add=add+"<option " + selected + " value='topcenter'>topcenter</option>";
		if (wm_pos=="bottomleft") { selected="selected"; } else { selected=""; }
		add=add+"<option " + selected + " value='bottomleft'>bottomleft</option>";
		if (wm_pos=="bottomright") { selected="selected"; } else { selected=""; }
		add=add+"<option " + selected + " value='bottomright'>bottomright</option>";
		if (wm_pos=="bottomcenter") { selected="selected"; } else { selected=""; }
		add=add+"<option " + selected + " value='bottomcenter'>bottomcenter</option>";
		add=add+"</select></td>";
		add=add+"<td class='watermark'><input type='text' class='i_wm_reps' name='wm_rep[]' value='"+ wm_rep +"' size='2'></td>";
		add=add+"<td class='watermark'><input type='text' class='i_wm_images' name='wm_image[]' value='"+ wm_image +"' size='5'></td>";
		add=add+"<td class='watermark'><input type='text' class='i_wm_margins' name='wm_margin[]' value='"+ wm_margin +"' size='2'></td>";
		add=add+"<td class='watermark'><input type='button' class='deleteOpt btn btn-xs btn-warning' value='Eliminar'></td>";
		add=add+"<td>"+"</tr>";
		$('tbody').append(add);
	}

	function ClearTableItems() {
		$('tbody').html("");
	}

	function LoadFormConfiguration() {
		var folders=[];
		var widths= [];
		var heights= [];
		var backgrounds= [];
		var wm_poss= [];
		var wm_reps= [];
		var wm_images = [];
		var wm_margins = [];
		$('.i_folders').each(function(id){ folders.push($(this).val()); console.log(id); });
		$('.i_widths').each(function(id){ widths.push($(this).val()); });
		$('.i_heights').each(function(id){ heights.push($(this).val()); });
		$('.i_backgrounds').each(function(id){ backgrounds.push($(this).val()); });
		$('.i_wm_poss').each(function(id){ wm_poss.push($(this).val()); });
		$('.i_wm_reps').each(function(id){ wm_reps.push($(this).val()); });
		$('.i_wm_images').each(function(id){ wm_images.push($(this).val()); });
		$('.i_wm_margins').each(function(id){ wm_margins.push($(this).val()); });
		//Construimos los valores de salida...
		options=[];
		for(var i=0; i<folders.length; i++) {
			var optItem = [];
			if (folders[i]!="") {
				optItem['folder']=folders[i];
				optItem['width']=0;
				optItem['height']="";
				optItem['background']="";
				optItem['wm_pos']="";
				optItem['wm_rep']="";
				optItem['wm_image']="";
				optItem['wm_margin']="";
				if (widths[i]!="") { optItem['width']=widths[i]; }
				if (heights[i]!="") { optItem['height']=heights[i]; }
				if (backgrounds[i]!="") { optItem['background']=backgrounds[i]; }
				if (wm_poss[i]!="") { optItem['wm_pos']=wm_poss[i]; }
				if (wm_reps[i]!="") { optItem['wm_rep']=wm_reps[i]; }
				if (wm_images[i]!="") { optItem['wm_image']=wm_images[i]; }
				if (wm_margins[i]!="") { optItem['wm_margin']=wm_margins[i]; }
				options.push(optItem);
			}
		}
		console.log(options);
		implodeOptions();
	}

	function implodeOptions() {
		var output=[];
		for(var i=0;i<options.length;i++) {
			var opt="";		
			//Empezamos de atrás hacia delante.
			if ((opt!="") || (options[i]['wm_margin']!="")) {
				var val= options[i]['wm_margin'];
				if (opt!="") { opt=','+opt; }
				opt=val+opt;
			}
			if ((opt!="") || (options[i]['wm_image']!="")) {
				var val= options[i]['wm_image'];
				if (val=="") { val="watermark.png"; }
				if (opt!="") { opt=','+opt; }
				opt=val+opt;
			}
			if ((opt!="") || (options[i]['wm_rep']!="")) {
				var val= options[i]['wm_rep'];
				if (val=="") { val="1"; }
				if (opt!="") { opt=','+opt; }
				opt=val+opt;
			}
			if ((opt!="") || (options[i]['wm_pos']!="")) {
				var val= options[i]['wm_pos'];
				if (val=="") { val="center"; }
				if (opt!="") { opt=','+opt; }
				opt=val+opt;
			}
			if ((opt!="") || (options[i]['background']!="")) {
				var val= options[i]['background'];
				if (val=="") { val="fit"; }
				if (opt!="") { opt=','+opt; }
				opt=val+opt;
			}
			if ((opt!="") || (options[i]['height']!="")) {
				var val= options[i]['height'];
				if (val=="") { val="0"; }
				if (opt!="") { opt=','+opt; }
				opt=val+opt;
			}
			if ((opt!="") || (options[i]['width']!="")) {
				var val= options[i]['width'];
				if (val=="") { val="0"; }
				if (opt!="") { opt=','+opt; }
				opt=val+opt;
			}
			if (opt!="") { opt=','+opt; }
			opt='('+options[i]['folder']+opt+')';
			output.push(opt);
		}
		var nameField=$("#field").val();
		if(nameField!="") {
			if(parent.$('#'+nameField).length) {
				parent.$('#'+nameField).val(output.join(";"));
			}
		}
		parent.jQuery.fancybox.close();
	}

	$('#addOpt').click(function () {
		AddTableItem("new");
	});

	$('#cleanOpt').click(function () {
		ClearTableItems();
	});

	$(document).delegate('.deleteOpt','click',function() {
		$(this).parent().parent().remove();
	});

	$('#finish').click(function () {
		LoadFormConfiguration();
	});


	loadField();
});
</script>