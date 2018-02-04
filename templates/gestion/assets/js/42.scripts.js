var FormEditable = function () {
    var initEditables = function () {
        //global settings 
        $.fn.editable.defaults.mode = 'inline';
        $.fn.editable.defaults.inputclass = 'form-control';
        $.fn.editable.defaults.url = $('#translate-table').attr("action");

        //editables element samples 
        $('.translate').editable({
            type: 'text',
            pk: 1,
            showbuttons: 'bottom'
        });
    }

    return {
        //main function to initiate the module
        init: function () {
            initEditables();
        }
    };
}();

$(document).ready(function() {

    //Extras
	$("#uploadlink").click(function(){
		var lnk='';
		var des ='';
		var tam ='';
		lnk=$("#Extra_LinksUrlTemp").val();
		des=$("#Extra_LinksDesTemp").val();
		if((lnk.length>10)&&(des.length>0)){
			$('#linksuploader').append('<div class="list-group-item note-info"><input name="Extra_links_Description[]"" value="'+des+'" type="hidden" /><input name="Extra_links_Link[]"" value="'+lnk+'" type="hidden" /><h4 class="block">'+des+' <small>' + lnk +' </small><a class="btn btn-xs red pull-right extra_delete">Borrar</a></h4></div>');
			$('#Extra_LinksDesTemp').val('');
			$('#Extra_LinksUrlTemp').val('');
		}
		else {
			bootbox.alert('Debe introducir la descripción y la URL del enlace');	
		}
		return false;
	});
	$("#uploadvideo").click(function(){
		var emb='';
		var des ='';
		var tam ='';
		emb=$("#Extra_VideosUrlTemp").val();
		des=$("#Extra_VideosDesTemp").val();
		if((emb.length>10)&&(des.length>0)){
			$('#videosuploader').append('<div class="list-group-item note-info"><input name="Extra_videos_Description[]" value="'+des+'" type="hidden" /><textarea name="Extra_videos_Embed[]" class="hidden">'+emb+'</textarea><h4 class="block">'+des+'<a class="btn btn-xs red pull-right extra_delete">Borrar</a></h4></div>');
			$('#Extra_VideosDesTemp').val('');
			$('#Extra_VideosUrlTemp').val('');
		}
		else {
			bootbox.alert('Debe introducir la descripción y el código a incrustar');		
		}
		return false;
	});
	$("#uploadembed").click(function(){
		var emb='';
		var des ='';
		var tam ='';
		emb=$("#Extra_EmbedUrlTemp").val();
		des=$("#Extra_EmbedDesTemp").val();
		if((emb.length>10)&&(des.length>0)){
			$('#embeduploader').append('<div class="list-group-item note-info"><input name="Extra_embed_Description[]" value="'+des+'" type="hidden" /><textarea name="Extra_embed_Embed[]" class="hidden">'+emb+'"'+'</textarea><h4 class="block">'+des+'<a class="btn btn-xs red pull-right extra_delete">Borrar</a></h4></div>');
			$('#Extra_EmbedDesTemp').val('');
			$('#Extra_EmbedUrlTemp').val('');
		}
		else {
			bootbox.alert('Debe introducir la descripción y el código a incrustar');		
		}
		return false;
	});
	$("#uploadaward").click(function(){
		var premio='';
		var textopremio='';
		var pax ='';
		var usado =0;
		var textousado="";
		premio=$("#Temp_IDAward").val();
		textopremio=$("#Temp_IDAward option:selected").text();
		pax=$("#Temp_Pax").val();
		if ($('Temp_Used').is(':checked')) { usado=1; $textousado="(usado)"; }
		$('#awarduploader').append('<div class="list-group-item note-info"><input name="Items_IDAward[]"" value="'+premio+'" type="hidden" /><input name="Items_Pax[]"" value="'+pax+'" type="hidden" /><input name="Items_Used[]"" value="'+usado+'" type="hidden" /><h4 class="block">'+pax+' X '+textopremio+textousado+'<a class="btn btn-xs red pull-right extra_delete">Borrar</a></h4></div>');
		$('#Temp_IDAward').val('0');
		$('#Temp_IDAward').val('0');
		$('#Temp_IDAward').attr('checked', false);
		return false;
	});
    $("#uploadreserves").click(function(){
        var premio='';
        var textopremio='';
        var pax ='';
        var usado =0;
        var textousado="";
        premio=$("#Extra_reserves_Award").val();
        textopremio=$("#Extra_reserves_Award option:selected").text();
        pax=$("#Extra_reserves_Pax").val();
        if ($('#Extra_reserves_Used').is(':checked')) { usado=1; $textousado="(usado)"; }
        $('#reservesuploader').append('<div class="list-group-item note-info"><input name="Items_IDAward[]"" value="'+premio+'" type="hidden" /><input name="Items_Pax[]"" value="'+pax+'" type="hidden" /><input name="Items_Used[]"" value="'+usado+'" type="hidden" /><h4 class="block">'+pax+' X '+textopremio+textousado+'<a class="btn btn-xs red pull-right extra_delete">Borrar</a></h4></div>');
        $('#Extra_reserves_IDAward').val('0');
        $('#Extra_reserves_IDAward').val('0');
        $('#Extra_reserves_Used').attr('checked', false);
        return false;
    });
    $("#uploadchapters").click(function(){
        var title='';
        var minuto=0;
        var segundo =0;
        title=$("#Extra_Chapters_Title").val();
        minuto=$("#Extra_Chapters_PosMinutes").val();
        segundo=$("#Extra_Chapters_PosSeconds").val();
        if(title.length>3){
            $('#chaptersuploader').append('<div class="list-group-item note-info"><input name="Chapters_Title[]"" value="'+title+'" type="hidden" /><input name="Chapters_PosMinutes[]"" value="'+minuto+'" type="hidden" /><input name="Chapters_PosSeconds[]"" value="'+segundo+'" type="hidden" /><h4 class="block">'+title+' ('+minuto+'m'+segundo+'s)<a class="btn btn-xs red pull-right extra_delete">Borrar</a></h4></div>');
            $("#Extra_Chapters_Title").val('')
            $('#Extra_Chapters_PosMinutes').val('0');
            $('#Extra_Chapters_PosSeconds').val('0');
        } else {
           bootbox.alert('Debe introducir el título del capítulor y el minuto y segundo en el que comienza');
        }
        return false;
    });
    $("#uploadprices").click(function(){
        var price=0;
        var desde=0;
        var hasta =0;
        price=$("#Extra_PricePriceTemp").val();
        desde=$("#Extra_PriceDatePublish").val();
        hasta=$("#Extra_PriceDateExpire").val();
        if((price!=0)&&(desde!="")&&(hasta!="")){
            $('#pricesuploader').append('<div class="list-group-item note-info"><input name="Prices_Price[]"" value="'+price+'" type="hidden" /><input name="Prices_DatePublish[]"" value="'+desde+'" type="hidden" /><input name="Prices_DateExpire[]"" value="'+hasta+'" type="hidden" /><h4 class="block">'+price+'&euro; (De '+desde+' hasta '+hasta+')<a class="btn btn-xs red pull-right extra_delete">Borrar</a></h4></div>');
            $("#Extra_PricePriceTemp").val('')
            $('#Extra_PriceDatePublish').val('');
            $('#Extra_PriceDateExpire').val('');
        } else {
           bootbox.alert('Debe introducir el precio y fechas de validez de la promoción');
        }
        return false;
    });

    $("#uploadrapels").click(function(){
        var minquantity=0;
        var price=0;
        minquantity=$("#Extra_RapelsMinQuantityTemp").val();
        price=$("#Extra_RapelsPriceTemp").val();
        if((price!=0)&&(minquantity!="")){
            $('#rapelsuploader').append('<div class="list-group-item note-info"><input name="Rapels_MinQuantity[]"" value="'+minquantity+'" type="hidden" /><input name="Rapels_Price[]"" value="'+price+'" type="hidden" /><h4 class="block">'+minquantity+' unidades o más a '+price+'&euro; <a class="btn btn-xs red pull-right extra_delete">Borrar</a></h4></div>');
            $("#Extra_RapelsMinQuantityTemp").val('')
            $('#Extra_RapelsPriceTemp').val('');
        } else {
           bootbox.alert('Debe introducir la cantidad mínima y el precio');
        }
        return false;
    });

    $("#uploadsizes").click(function(){
        var text="";
        text=$("#Extra_SizesNameTemp").val();
        if(text!=""){
            $('#sizesuploader').append('<div class="list-group-item note-info"><input name="Sizes_Name[]"" value="'+text+'" type="hidden" /><h4 class="block">'+text+' <a class="btn btn-xs red pull-right extra_delete">Borrar</a></h4></div>');
            $("#Extra_SizesNameTemp").val('')
        } else {
           bootbox.alert('Debe introducir la talla');
        }
        return false;
    });

    $("#uploadcolors").click(function(){
        var text="";
        text=$("#Extra_ColorsNameTemp").val();
        if(text!=""){
            $('#colorsuploader').append('<div class="list-group-item note-info"><input name="Colors_Name[]"" value="'+text+'" type="hidden" /><h4 class="block">'+text+' <a class="btn btn-xs red pull-right extra_delete">Borrar</a></h4></div>');
            $("#Extra_ColorsNameTemp").val('')
        } else {
           bootbox.alert('Debe introducir el color');
        }
        return false;
    });


	$(document).delegate('.extra_delete','click',function(){
        var temp=$(this).parent("h4").parent("div");
        bootbox.confirm("¿Borrar el elemento seleccionado?", function(result) {
            if (result) {
                temp.fadeOut(800);
            }
        }); 
        return false;
    });

    $(document).delegate('.xtra-delete-confirm','click',function(){
    	var temp=$(this).parent("span").parent("div").parent("li");
        var myUrl=$(this).attr('href');
        bootbox.confirm("¿Borrar el elemento seleccionado?", function(result) {
            if (result) {
                $.ajax({
                    type: 'GET',
                    url: myUrl,
                    data: '',
                    success:function(msj){  
                        //$.wl_Alert(msj,'note','#content');
                        if ( msj == 1 ){
                            temp.fadeOut(800);                     
                        }
                        else{
                            bootbox.alert("No se ha podido eliminar el elemento"); 
                        }
                    },
                    error:function(){
                        bootbox.alert("Error interno"); 
                    }
                });
            }
        }); 
        return false;
    });

    $(document).delegate('.comment-delete-confirm','click',function(){
    	var temp=$(this).parent("div").parent("li");
        var myUrl=$(this).attr('href');
        bootbox.confirm("¿Borrar el comentario seleccionado?", function(result) {
            if (result) {
                $.ajax({
                    type: 'GET',
                    url: myUrl,
                    data: '',
                    success:function(msj){  
                        //$.wl_Alert(msj,'note','#content');
                        if ( msj == 1 ){
                            temp.fadeOut(800);                     
                        }
                        else{
                            bootbox.alert("No se ha podido eliminar el comentario"); 
                        }
                    },
                    error:function(){
                        bootbox.alert("Error interno"); 
                    }
                });
            }
        }); 
        return false;
    });

    $(document).delegate('.comment-change-activation','click',function(){
    	var temp=$(this);
        var myUrl=$(this).attr('href');
        temp.removeClass('red');
        temp.removeClass('green');
        temp.text('Espere...');
        $.ajax({
            type: 'GET',
            url: myUrl,
            data: '',
            success:function(msj){  
                if ( msj == 1 ){
                    temp.addClass('green');
                    temp.text('Activado');
                }
                else{
                  	temp.addClass('red');
                    temp.text('Desactivado');  
                }
            },
            error:function(){
                bootbox.alert("Error interno"); 
            }
        });
        return false;
    });

    $('.timeline').delegate('.delete-confirm','click',function(){
        var temp=$(this).parent("div").parent("div").parent("div").parent("li");
        var myUrl=$(this).attr('href');
        bootbox.confirm("¿Borrar el mensaje seleccionado?", function(result) {
            if (result) {
                $.ajax({
                    type: 'GET',
                    url: myUrl,
                    data: '',
                    success:function(msj){  
                        //$.wl_Alert(msj,'note','#content');
                        if ( msj == 1 ){
                            temp.fadeOut(800);                     
                        }
                        else{
                            bootbox.alert("No se ha podido eliminar el comentario"); 
                        }
                    },
                    error:function(){
                        bootbox.alert("Error interno"); 
                    }
                });
            }
        }); 
        return false;
    });

    $('.timeline').delegate('.delete-top-confirm','click',function(){
        var back=$(this).attr("back");
        var myUrl=$(this).attr('href');
        bootbox.confirm("¿Borrar el mensaje seleccionado?", function(result) {
            if (result) {
                $.ajax({
                    type: 'GET',
                    url: myUrl,
                    data: '',
                    success:function(msj){  
                        if ( msj == 1 ){
                            location.href= back;                  
                        }
                        else{
                            bootbox.alert("No se ha podido eliminar el comentario"); 
                        }
                    },
                    error:function(){
                        bootbox.alert("Error interno"); 
                    }
                });
            }
        }); 
        return false;
    });


    //Geolocalizador
    $(".showgeo").fancybox({
        'autoScale'         : true,
        'type'              : 'iframe',
    });

    $('.fancybox').fancybox();
    
    //Campos de fechas dobles.
    function CheckDoubleDate(field) {
        var field_in=$('#'+field).attr('validate-from');
        var field_out=$('#'+field).attr('validate-to');
        var weekcheck=$('#'+field).attr('weekcheck');
        var _in=$('#'+field_in).val();
        var _out=$('#'+field_out).val();
        var compare_in= _in.split("/").reverse().join('-');
        var compare_out= _out.split("/").reverse().join('-');

        //Comprobamos en qué campo estamos y sobre él hacemos el cambio...
        if (field==field_in) {
           if (compare_out<compare_in) { $('#'+field_out).val(_in); }  
        } else {
            if (compare_in>compare_out) { $('#'+field_in).val(_out); } 
        }
        if (weekcheck!="") {
            var _in=$('#'+field_in).val();
            var _out=$('#'+field_out).val();
            var compare_in= _in.split("/").reverse().join('-');
            var compare_out= _out.split("/").reverse().join('-');
            if (compare_in==compare_out) {
                $("." + weekcheck).hide(300);
            } else {
                $("." + weekcheck).show(300);
            }
        }
    }

    if ($('.check-double-date').length) { 
        $('.check-double-date').each( function() { CheckDoubleDate($(this).attr('id')); });
    }

    $(document).delegate('.check-double-date','change',function(){
        CheckDoubleDate($(this).attr('id'));
    });

    //Campos de fechas dobles.
    function CheckDoubleTime(field) {
        var field_in=$('#'+field).attr('validate-from');
        var field_out=$('#'+field).attr('validate-to');
        var weekcheck=$('#'+field).attr('weekcheck');
        var _in=$('#'+field_in).val();
        var _out=$('#'+field_out).val();
        var compare_in= parseInt(_in.split(":").join(''));
        var compare_out= parseInt(_out.split(":").join(''));

        //Comprobamos en qué campo estamos y sobre él hacemos el cambio...
        if (field==field_in) {
           if (compare_out<compare_in) { $('#'+field_out).val(_in); }  
        } else {
            if (compare_in>compare_out) { $('#'+field_in).val(_out); } 
        }
    }

    if ($('.check-double-time').length) { 
        $('.check-double-time').each( function() { CheckDoubleTime($(this).attr('id')); });
    }

    $(document).delegate('.check-double-time','change',function(){
        CheckDoubleTime($(this).attr('id'));
    });

    function CheckRetypePassword(_this) {
        if (_this.val()!="") {
            $('#Block_Retype_'+_this.attr("id")).slideDown();
            $('#Retype_'+_this.attr("id")).attr("equalTo","#" + _this.attr("id"));
            $('#Retype_'+_this.attr("id")).attr("required","required");
        } else {
            $('#Block_Retype_'+_this.attr("id")).slideUp();
            $('#Retype_'+_this.attr("id")).removeAttr("equalTo");
            $('#Retype_'+_this.attr("id")).removeAttr("required");
        }
    }

    $(document).delegate('.retypepassw','keyup',function (){
        CheckRetypePassword($(this));
    });

    if ($('.retypepassw').length) { 
        CheckRetypePassword($('.retypepassw'));
    }

    //Forms Checkers Ajax
    $(document).delegate('input[check-script]','change',function() {
        var _this=$(this);
        _this.addClass("spinner");
        $.ajax({
            type: 'POST',
            url: _this.attr('check-script'),
            data: 'value=' + _this.val() + '&id=' + $('#System_ID').val(),
            success:function(msj){  
                if ( msj == 0 ){
                    _this.attr('placeholder','Ya existe un elemento con el mismo valor');
                    _this.val('');
                    _this.removeClass("spinner");
                }
            },
            complete:function() { _this.removeClass("spinner"); }
        });
    });

    //Image Crop & Resize
    if ($('.pane').length) {
        sh_highlightDocument();
        // Apply jrac on some image.
        var width_orig;
        $('.pane img').jrac({
            'crop_width': $('#Init_Width').val(),
            'crop_height': $('#Init_Height').val(),
            'crop_x': 100,
            'crop_y': 100,
            'crop_resize': false,
            'viewport_onload': function() {
                var $viewport = this;
                var inputs = $viewport.$container.parent('.pane').find('.coords input:text');
                var events = ['crop_x','crop_y','crop_width','crop_height','image_width','image_height'];
                for (var i = 0; i < events.length; i++) {
                    var event_name = events[i];
                    // Register an event with an element.
                    $viewport.observator.register(event_name, inputs.eq(i));
                    // Attach a handler to that event for the element.
                    inputs.eq(i).bind(event_name, function(event, $viewport, value) {
                        $(this).val(value);
                    })
                    // Attach a handler for the built-in jQuery change event, handler
                    // which read user input and apply it to relevent viewport object.
                    .change(event_name, function(event) {
                        var event_name = event.data;
                        $viewport.$image.scale_proportion_locked = $viewport.$container.parent('.pane').find('.coords input:checkbox').is(':checked');
                        $viewport.observator.set_property(event_name,$(this).val());
                    });
                }
                var width_orig = $viewport.$image.originalWidth;
                $viewport.$container.append('<div>Tamaño Original de la Imagen: '+$viewport.$image.originalWidth+' x '+$viewport.$image.originalHeight+'</div><br/>')
            },
            'image_width': width_orig,
            'zoom_max': $('#Init_MaxZoom').val(),
        })
        // React on all viewport events.
        .bind('viewport_events', function(event, $viewport) {
            //var inputs = $(this).parents('.pane').find('.coords button');
            inputs = $('.submit');
            if (! $viewport.observator.crop_consistent()) { inputs.attr("disabled","disabled"); } else { inputs.removeAttr("disabled"); }
            //inputs.css('background-color',($viewport.observator.crop_consistent())?'green':'red');
        });
    }

    function CheckUpdates() {
        $.ajax({
            type: "GET",
            url: "modules/checkupdates",
            success: function(data){
                console.log(data);
                if (data>1) {
                    $('.warning-updates').html(" "+data+" ");
                    $('.warning-updates').fadeIn(500);
                } else {
                    $('.warning-updates').fadeOut(500);
                }
            }
        });
    }

    function LoadCheckUpdates() {
        if ($("#ItsUpdated").length) {
            var m=$("#ItsUpdated").html();
            if (m=="  ") { 
                console.log("a");
                CheckUpdates(); 
            } else {
                if (m!=" 0 ") {
                    $('.warning-updates').fadeIn(500);
                }
            }
        }
    }

    LoadCheckUpdates();

});