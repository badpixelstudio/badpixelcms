function GeographyChanger(elemento) {
	var idelemento=elemento.attr("id");
	var id_valor='';
	var volcar_en='';
	var valor_defecto='';
	var tabla='';
	var elemento_form='';
	if (idelemento=='Form_IDState') {
		id_valor='Form_IDState';
		id_valor_text="State";
		tabla_valor="aux_states";
		tabla='aux_cities';
		volcar_en='select_IDCity';
		valor_defecto='System_IDCity';
		elemento_form='Form_IDCity';
	}
	if (idelemento=='Form_IDCity') {
		id_valor='Form_IDCity';
		id_valor_text="City";
		tabla_valor="aux_cities";
		tabla='aux_zones';
		volcar_en='select_IDZone';
		valor_defecto='System_IDZone';
		elemento_form='Form_IDZone';
	}
	if (idelemento=='Form_IDZone') {
		id_valor='Form_IDZone';
		tabla_valor="aux_zones";
		id_valor_text="Zone";
	}
	var Valor=$('#'+id_valor).val();
	var ValorActual=$('#'+valor_defecto).val();	
	//Volcamos el dato al campo de texto, si existe claro...
	$.ajax({
		type: 'GET',
		url: "../lib/geography/getvalue.php?table=" + encodeURIComponent(tabla_valor) +"&id=" + encodeURIComponent(Valor),
		success:function(msj){	
			//if ( msj != "" ){
				$('#Form_'+id_valor_text).val(msj);
				$('#Name_'+id_valor_text).val(msj);
			//}
		}
	});

	//Creamos el combo...
	//alert("../lib/geography/load.php?element=" + encodeURIComponent(tabla) +"&id=" + encodeURIComponent(Valor) + "&value=" + encodeURIComponent(ValorActual));
	$('#'+volcar_en).load("../lib/geography/load_crm.php?element=" +  encodeURIComponent(elemento_form) +"&table=" + encodeURIComponent(tabla) +"&id=" + encodeURIComponent(Valor) + "&value=" + encodeURIComponent(ValorActual),function() {
		if (idelemento=='Form_IDState') {
				//GeographyChanger($('#Form_IDCity'));
		}		
	});
	
}

$(document).ready(function() {
	$('#form').delegate('#Form_IDState','change',function() {
		GeographyChanger($(this));
	});
	$('#form').delegate('#Form_IDCity','change',function() {
		GeographyChanger($(this));
	});
	$('#form').delegate('#Form_IDZone','change',function() {
		GeographyChanger($(this));
	});	
	GeographyChanger($('#Form_IDState'));
});