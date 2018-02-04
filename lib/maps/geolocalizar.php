<?php
require_once("../../include/core/common.php");
require_once("../../include/core/core.class.php");
$Core=new Core(null);
$geo='[40.6564225, -4.700322400000005]';
$zoom=15;
$precarga_field="Form_Geolocation";
$precargar=true; 
$return_field=$precarga_field;

$search="";
if (isset($_GET['geo'])) {
	if ($_GET['geo']!="") {
		$geo=$_GET['geo'];
		if (($pos=strpos($geo,')'))!==false) {
			$nuevogeo=substr($geo,1,$pos-1);
			$nuevozoom=substr($geo,$pos+1);
			$geo=$nuevogeo;
			if ($nuevozoom!="") { $zoom=$nuevozoom; }
		}
		$geo=str_replace('(','',$geo);
		$geo=str_replace(')','',$geo);	
		$geo='['.$geo.']';
	}
}
if (isset($_GET['field'])) {
	$precargar=true;
	$precarga_field=$_GET['field'];
}
if (isset($_GET['return'])) {
	$return_field=$_GET['return'];
}
if (isset($_GET['search'])) {
	if ($_GET['search']!="") {
		$search=$_GET['search'];
	}
}
?>
<html>    
  <head> 
    <script type="text/javascript" src="js/jquery-1.6.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui-1.8.12.custom.min.js"></script>        
    <script src="http://maps.google.com/maps/api/js?key=<?php echo siteGoogleMapsAPIKey; ?>" type="text/javascript"></script>
    <script type="text/javascript" src="js/gmap3.min.js"></script> 
    <link rel="stylesheet" type="text/css" href="js/jquery-ui-1.8.12.custom.css"/>
    <style>
      *{
        font-family: verdana;
        font-size: 12px;
      }
      body{
        text-align:center;
		background: #E1E1E1;
      }
      .gmap3{
        margin: 20px auto;
        border: 1px dashed #C0C0C0;
        width: 95%;
        height: 70%;
      }
      .ui-menu .ui-menu-item{
        text-align: left;  
        font-weight: normal;
      }
      .ui-menu .ui-menu-item a.ui-state-hover{
        border: 1px solid red; 
        background: #FFBFBF; 
        color: black;
        font-weight:bold;
      }
    </style>
    
    <script type="text/javascript">
	
	$(document).ready(function() {
		var geo_actual=<?php echo $geo; ?>;
		var zoom_actual=<?php echo $zoom; ?>;
		var campo_devolucion="<?php echo $return_field; ?>";
		var precarga_desde_campo=<?php echo $precargar; ?>;
		var address_search='<?php echo $search; ?>';
	
		$('#geo').val('('+geo_actual+')');
		
		var tmp_geo=parent.$('#'+campo_devolucion).val();
		if ((tmp_geo) && (precarga_desde_campo)) { 
			var filtro= /\(([0-9 \-,.\(\)])/;
			if (tmp_geo.match(filtro)) {
				latlon=tmp_geo.substring(1,tmp_geo.indexOf(')'));
				zm=tmp_geo.substring(tmp_geo.indexOf(')')+1);
				if (latlon!="") { 
					$('#geo').val('('+latlon+')');
					geo_actual=latlon.split(',');
				}
				if (zm!="") { zoom_actual=parseInt(zm); }
			}
		}

	//Función que inicializa el mapa con la posición actual	
	$('#test').gmap3({ 
		action:'init',
		options:{
			zoom: zoom_actual
		}
	});
	
	if(address_search!="") {
		//Si tenemos una busqueda, cargamos el mapa con la dirección...	
		$('#address').val(address_search);
		$('#test').gmap3({ 
			action:'addMarker',
			address: address_search,
			map:{center:true},
			marker:	{
				options:{draggable: true},
				events:{
					dragend: function(marker, event){
						$(this).gmap3(
							{action:'getAddress',
							  latLng:marker.getPosition(),
							  callback:function(results){
								var map = $(this).gmap3('get'),
									infowindow = $(this).gmap3({action:'get', name:'infowindow'}),
									content = results && results[1] ? results && results[1].formatted_address : 'no address';
								if (infowindow){
									infowindow.open(map, marker);
									infowindow.setContent(content);
								} else {
									$(this).gmap3(
								  
									{
										action:'addinfowindow', 
										anchor:marker, 
										options:{content: content}
									}
								  
									);
								}
							  }
							},
							{action: 'getZoom', 
								callback:function(value){
									$("#address").val('');
									$("#geo").val(marker.getPosition()+value);
									parent.$('#Form_Geolocation').val($('#geo').val());
								}
							}
						
						);//cerrar $(this).gmap3
					}
				}
			},
		});
		$("#test").gmap3({
        	action:'getAddress',
            address: address_search,
            callback:function(results){
            	if (!results) return;
				$.map(results, function(item) {
					$("#geo").val(item.geometry.location);
					geo_actual=item.geometry.location;
				});
			}
		});

	} else {
		//mostramos posición por LatLon
		$('#test').gmap3(
			{ 
				action:'addMarker',
				latLng: geo_actual,
				map:{center:true},
				marker:
				{
					options:{draggable: true},
					events:{
						dragend: function(marker, event){
							$(this).gmap3(
								{action:'getAddress',
								  latLng:marker.getPosition(),
								  callback:function(results){
									var map = $(this).gmap3('get'),
										infowindow = $(this).gmap3({action:'get', name:'infowindow'}),
										content = results && results[1] ? results && results[1].formatted_address : 'no address';
									if (infowindow){
										infowindow.open(map, marker);
										infowindow.setContent(content);
									} else {
										$(this).gmap3(
									  
										{
											action:'addinfowindow', 
											anchor:marker, 
											options:{content: content}
										}
									  
										);
									}
								  }
								},
								{action: 'getZoom', 
									callback:function(value){
										$("#address").val('');
										$("#geo").val(marker.getPosition()+value);
										parent.$('#Form_Geolocation').val($('#geo').val());
									}
								}
							
							);//cerrar $(this).gmap3
						}
					}
				},
			}
			
		);
	}

		
		$('#address').autocomplete({
            
			//This bit uses the geocoder to fetch address values
			source: function(request, response) {
            	$("#test").gmap3(
						
				{
                	action:'getAddress',
                	address: request.term,
                	callback:function(results){
                  		if (!results) return;
                 			response($.map(results, function(item) {
                    			return {
                      				label:  item.formatted_address,
                      				value: item.formatted_address,
                      				latLng: item.geometry.location
                    			}
                  			}));
                		}
              		});
            	},
            
				//This bit is executed upon selection of an address
            	select: function(event, ui) {
				
			  		$("#geo").val(ui.item.latLng);
              		$("#test").gmap3(
			  				  
                		{
							action:'clear', 
							name:'marker'
						},
						
                		{
							action:'addMarker',
                  			latLng:ui.item.latLng,
                  			map:{center:true},
				  			marker:{
					  			options:{draggable: true},
					  			events:{
									dragend: function(marker, event){
										$(this).gmap3(
										
											{ 
												action:'getAddress',
												latLng:marker.getPosition(),
												callback:function(results){
													var map = $(this).gmap3('get'),
													infowindow = $(this).gmap3({action:'get', name:'infowindow'}),
													content = results && results[1] ? results && results[1].formatted_address : 'no address';
													if (infowindow){
														infowindow.open(map, marker);
														infowindow.setContent(content);
													} else {
														$(this).gmap3(
																	
														{
															action:'addinfowindow',
															anchor:marker, 
															options:{content: content}
														}
																	
														);
													}
												}
											},	
																	
											{
												action: 'getZoom', 
												callback:function(value){
													zoom_actual=value;
													$("#address").val('');
													$("#geo").val(marker.getPosition()+value);
													parent.$('#Form_Geolocation').val($('#geo').val());
												}
											}
							
										);//cerrar $(this).gmap3
									}
					  			}
				  			},
               			}
										
              		);//cerrar $(this).gmap3
			  
            	}//cerrar select
          });
		  
		  $(".miboton").click(function(){
			  	zoom_actual=$("#test").gmap3('get').getZoom();
				geo_actual=$('#geo').val();
				//Nos quedamos solo con la parte entre paréntesis...
				geo_actual=geo_actual.substring(0,geo_actual.indexOf(')')+1);
				geo_actual=geo_actual+zoom_actual;
				$('#geo').val(geo_actual);
			  	parent.$('#'+campo_devolucion).val($('#geo').val());
			    parent.jQuery.fancybox.close();
				return false;
  		  });
		  
		  $(".geolocalizar").click(function(){
			$('#test-result').html('Autorice al navegador para geolocalizar. Este proceso tarda unos segundos');
				$("#test").gmap3({
					action : 'geoLatLng',
				  	callback : function(latLng){
						if (latLng){
					  		$('#test-result').html('Auto-geolocalizar a finalizado con éxito');
					 		$("#address").val('');
					  		$("#geo").val(latLng);
							parent.$('#Form_Geolocation').val($('#geo').val());
					  		$(this).gmap3({ 
									action:'clear',
									name:'marker'
								},
								
					  			{ 
									action: 'addMarker', 
									latLng:latLng,
									marker:{
						  				options:{draggable: true},
						  				events:{
											dragend: function(marker, event){
												$(this).gmap3({
														action:'getAddress',
								  						latLng:marker.getPosition(),
								  						callback:function(results){
															var map = $(this).gmap3('get'),
															infowindow = $(this).gmap3({action:'get', name:'infowindow'}),
															content = results && results[1] ? results && results[1].formatted_address : 'no address';
															if (infowindow){
																infowindow.open(map, marker);
																infowindow.setContent(content);
															} else {
																$(this).gmap3(
																
																{
																	action:'addinfowindow',
																	anchor:marker, 
																	options:{content: content}
																}
																
																);
															}
								 						}
													},
													
													{
														action: 'getZoom', 
														callback:function(value){
															$("#address").val('');
															$("#geo").val(marker.getPosition()+value);
															parent.$('#Form_Geolocation').val($('#geo').val());
													}
												
												}//cerrar $(this).gmap3
											);
										}
						  			}
					  			},
							},
							"autofit");//cerrar $(this).gmap3 
						} else {
							$('#test-result').html('Imposible geolocalizar automaticamente.');
						}
					}//cerrar action geolocation
				}	
			)
  		  });
	});
	
    </script>

    
    
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<body>
    Buscar direcci&oacute;n: <input type="text" id="address" size="60"> <a href="#" class="geolocalizar">Auto-Geolocalizar</a> <a href="#" class="miboton">Guardar</a>
    <input type="hidden" id="geo" size="50" />
    <div id="test" class="gmap3"></div>
    <div id="test-result"></div>   
  </body>
</html>