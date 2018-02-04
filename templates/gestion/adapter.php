<?php
	require_once("../lib/minimizer/functions.php");
	if ($this->useravatar=="") { $this->useravatar="../templates/gestion/assets/images/no-avatar.png"; }
	
	
	function PutAdminPaginator($page,$offset,$lastpage,$linkpage,$linkparams) {
	if ($linkparams!="") { $linkparams="&".$linkparams; }
	$page--;
	if ($lastpage>1) {
		echo '<ul class="pagination pull-right">';
		$anteriorpag=$page;
		if ($anteriorpag<1) {$anteriorpag=1; }
		$siguientepag=$page+2;
		if ($siguientepag>$lastpage) {$siguientepag=$lastpage; }
		$iniciopag=$page-4;
		if ($iniciopag<0) {$iniciopag=0; }
		$finalpag=$iniciopag+9;
		if ($finalpag>$lastpage) {$finalpag=$lastpage; }
		if ($page!=0) { 
			echo "<li><a href=" . $linkpage . "?page=$anteriorpag&offset=$offset" . $linkparams . "><i class='fa fa-angle-left'></i></a></li> ";
		}
		for($i=$iniciopag; $i<$finalpag; $i++) {
			$linkpag=$i+1;
			$clase="";
			if ($i==$page) { $clase=" class='active'"; }
			echo "<li " . $clase ."><a href=" . $linkpage . "?page=$linkpag&offset=$offset" . $linkparams . '> ' . ($linkpag) . ' </a></li> '; 
		} 
		if (($page+1)!=$lastpage) { 
			echo "<li><a href=" . $linkpage . "?page=$siguientepag&offset=$offset" . $linkparams . "><i class='fa fa-angle-right'></i></a></li> ";
		}
		echo "</ul>";
	}
}

	function TemplateCatsTree($conf,$items,$level=0) {
		if (count($items)>0) {
			echo '<ol class="dd-list">';			
			foreach ($items as $item) {
				echo '	<li class="dd-item dd3-item" data-id="' . $item['ID'] . '">';
				echo '		<div class="dd-handle dd3-handle"></div>';
				$link=$conf->ParseMetaData($conf->TableContent[0]['Content'],$item);
				if ($conf->TableContent[0]['Link']!="") { $link= '<a href="' . $conf->ParseMetaData($conf->TableContent[0]['Link'],$item) . '">' . $conf->ParseMetaData($conf->TableContent[0]['Content'],$item). '</a>'; }
				echo '		<div class="dd3-content">' . $link ;
				echo '		<span class="pull-right">';
				foreach($conf->TableContent[1]['Content'] as $submenu) { 
					$enable=true;
					if ($submenu['Text']!="{{separator}}") { 
						if ($submenu['Condition']=='') {
							$enable=true;
						} else {
							$submenu['Condition']=str_replace("{{CheckMaxDepth}}", ($level+1), $submenu['Condition']);
							$enable=EvaluatePHP($GLOBALS['Core']->ParseMetaData($submenu['Condition'],$item));
						}
						if ($enable) {
							$add_class="";
							if (strpos($submenu['Link'],'delete')!==false) { $add_class="xtra-delete-confirm"; }
							echo '<a class="btn btn-xs default ' . $add_class . '" href="' . $conf->ParseMetaData($submenu['Link'],$item) . '">' . $submenu['Text'] . '</a> ';
						}
					}
				}
				echo '</span>';
				echo '</div>';
				if (isset($item['Items'])) {
					if (count($item['Items'])>0) { TemplateCatsTree($conf,$item['Items'],$level+1); }
				}
				echo '	</li>';
			}
		echo '</ol>';
		}
	}

	function TemplateTree($items,$level=0) {
		if (count($items)>0) {
			echo '<ol class="dd-list">';			
			foreach ($items as $item) {
				echo '	<li class="dd-item dd3-item" data-id="' . $item['id'] . '">';
				echo '		<div class="dd-handle dd3-handle"></div>';
				echo '		<div class="dd3-content">' . $item['title'] . '</a>';
				echo '</div>';
				if (count($item['items'])>0) { TemplateTree($item['items'],$level+1); }
				echo '	</li>';
			}
		echo '</ol>';
		}
	}

	function PutField($data) {
		if ($data['type']=="group") {
			echo '<div class="form-group" id="Block_' . $data['fieldname'] . '">';
			echo '<h2>' . $data['text'] . "</h2>";
			echo '</div>';
			return;
		}
		if ($data['type']=="xtra_images") {
			echo '<ul class="wl_gallery edit-confirm" script="' . $data['linkbase'] .'" prefix="' . $data['prefix'] . '" extra="images" module="' . $data['module'] . '" prior="' . $data['prior'] . '" >';
		    foreach($data['items'] as $item) {
				echo '<li id="' . $item['ID'] .'">';
		        	echo '<a title="' . $item['Description'] .'" id="' . $item['ID'] . '">';
		            if (is_file(sitepath . 'public/thumbnails/' . $item['Image'])) { 
		                echo '<img src="../public/thumbnails/' . $item['Image'] . '" alt="" width="100%" height="100%">';
		            } else {
		                echo '<div class="fileupload-nofile">'. $item['Image'] .'</div>';
		            }
		            echo '</a>';
		       	echo '</li>';
			}
			echo '</ul>';
			return;
		}
		if ($data['type']=="xtra_nestable") {
			$niveles="";
			if (isset($data['nestablelevels'])) { $niveles=' maxDepth="' . $data['nestablelevels'] . '"'; }
			echo '<div class="dd nestable" id="xtra_' . $data['extramodule'] . '" script="' . $data['linkbase'] . '/' . $data['extramodule'] . '_saveorderjson/prior/' . $data['prior'] . '"' . $niveles . '>';
			echo '<ol class="dd-list">';
			foreach($data['items'] as $item) {
				$text=$item[$data['fieldviewtext']];
				$link="";
				$enable=true;
				if (isset($data['condition'])) {
					if ($data['condition']!='') {$enable=EvaluatePHP($this->ParseMetaData($data['condition'],$item)); }
				}
				if ($enable) {
					if (isset($data['fieldlink'])) {
						if ($data['fieldlink']!="") {
							$folder="";
							if (isset($data['folderlink'])) { $folder=$data['folderlink']; }
							$text='<a href="' . $folder . $item[$data['fieldlink']] . '">' . $text . '</a>';
							$link='<a class="btn btn-xs default" href="' . $folder . $item[$data['fieldlink']] . '">Ver</a> ';
						}
					}
				}
				echo '<li class="dd-item dd3-item" data-id="'. $item['ID'] . '">';
				echo '<div class="dd-handle dd3-handle"></div>';
				echo '<div class="dd3-content"><span>' . $text . '</span>';
				echo '<span class="pull-right">';
				echo $link;
				echo '<a class="btn btn-xs default" href="' . $data['linkbase'] . '/' . $data['prefix'] . $data['extramodule'] . '_edit/prior/' . $data['prior'] . '/id/' . $item['ID'] . '">Editar</a> ';
				echo '<a class="btn btn-xs default xtra-delete-confirm" href="' . $data['linkbase'] . '/' . $data['prefix'] . $data['extramodule'] . '_delete/prior/' . $data['prior'] . '/id/' . $item['ID'] . '">Borrar</a>';
				echo '</span></div>';
				echo '</li>';
			}
			echo '</ol>';
			echo '</div>';
			return;
		}
		if ($data['type']=="xtra_comments") {
			echo '<div class="scroller" data-always-visible="1" data-rail-visible1="1">';
			echo '<ul class="chats">';
			foreach($data['items'] as $item) {
				if ($item['UserAvatar']=="") { $item['UserAvatar']="../templates/gestion/assets/images/no-avatar.png";}
				echo '<li class="in">';
				echo '<img class="avatar img-responsive" alt="" src="'.  $item['UserAvatar'] .'" />';
				echo '<div class="message">';
				echo '<span class="arrow"></span>';
				echo '<span class="name">' . $item['Name'] .'<span class="datetime"> escribió el ' . EuroScreenDateTime($item['DatePublish']) . '</span></span>';
				echo '<span class="body">' . stripslashes($item['Comment']) . '</span>';
				$clase="green"; $texto="Activado";
				if ($item['Active']==0) { $clase="red"; $texto="Desactivado"; }
				echo '<a class="btn btn-xs ' . $clase . ' default comment-change-activation" href="' . $data['linkbase'] . '/comments_change/prior/' . $data['prior'] . '/id/' . $item['ID'] . '">' . $texto .'</a> ';
				echo '<a class="btn btn-xs default" href="' . $data['linkbase'] . '/comments_edit/prior/' . $data['prior'] . '/id/' . $item['ID'] . '">Editar</a> ';
				echo '<a class="btn btn-xs default comment-delete-confirm" href="' . $data['linkbase'] . '/' . $data['prefix'] . 'comments_delete/prior/' . $data['prior'] . '/id/' . $item['ID'] . '">Borrar</a>';
				echo '</div>';
				echo '</li>';

			}
			echo '</ul>';
			echo '</div>';
			return;
		}
		if ($data['type']=="xtra_reserves") {
			echo '<div class="dd" id="xtra_reserves" script="' . $_SERVER['PHP_SELF'] . '?action=reserves_saveorderjson&prior=' . $data['prior'] . '">';
			echo '<ol class="dd-list">';
			foreach($data['items'] as $item) {
				echo '<li class="dd-item dd3-item" data-id="'. $item['ID'] . '">';
				echo '<div class="dd-handle dd3-handle"></div>';
				echo '<div class="dd3-content"><span>' . $item['Pax'] . " X " . $item['NameAward'] . '</span>';
				echo '<span class="pull-right">';
				echo '<a class="btn btn-xs default" href="' . $_SERVER['PHP_SELF'] . '?action=reserves_edit&prior=' . $data['prior'] . '&id=' . $item['ID'] . '">Editar</a> ';
				echo '<a class="btn btn-xs default xtra-delete-confirm" href="' . $_SERVER['PHP_SELF'] . '?action=' . $data['prefix'] . 'reserves_delete&prior=' . $data['prior'] . '&id=' . $item['ID'] . '">Borrar</a>';
				echo '</span></div>';
				echo '</li>';
			}
			echo '</ol>';
			echo '</div>';
			return;
		}
		if ($data['type']=="button") {
			echo '<button class="btn btn-default" type="button" id="' . $data['fieldname'] . '"><i class="fa fa-plus"></i> '. $data['text'] . '</button>';
			return;
		}
		if ($data['type']=="button-edit") {
			echo '<a class="btn btn-default" type="button" id="' . $data['fieldname'] . '" href="' . $data['value'] . '"><i class="fa fa-edit"></i>'. $data['text'] . '</a>';
			return;
		}
		if ($data['type']=="inline") {
			if (isset($data['text'])) { echo '<h2>' . $data['text'] . "</h2>"; }
			echo '<div class="form-group" id="Block_' . $data['fieldid'] . '">';
			echo stripslashes($data['html']);
			echo '</div>';
			return;
		}
		if ($data['type']=="password-retype") {
			$output="";
			if (isset($data['required'])) { if ($data['required']) { $output.=' required="required"'; } }
			if (isset($data['readonly'])) { if ($data['readonly']) { $output.=' readonly="readonly"'; } }
			if (isset($data['disabled'])) { if ($data['disabled']) { $output.=' disabled="disabled"'; } }
			if (isset($data['help'])) { $output.=' placeholder="' . $data['help'] . '"'; }
			if (isset($data['minlength'])) { $output.=' minlength="' . $data['minlength'] . '"'; }
			echo '<div class="form-group" id="Block_' . $data['fieldname'] . '">';
			echo "<label>" . _($data['text']) . "</label>";
			echo '<input class="form-control retypepassw" type="password" name="' . $data['fieldname'] .'" id="' . $data['fieldname'] .'"' . $output .' value=\''. $data['value'] . '\'>';
			echo '</div>';
			echo '<div class="form-group" id="Block_Retype_' . $data['fieldname'] . '">';
			echo '<label>Confirmar contraseña</label>';
			echo '<input class="form-control" type="password" name="Retype_' . $data['fieldname'] .'" id="Retype_' . $data['fieldname'] .'"' . $output .' value="">';
			echo '</div>';
			return;
		}
		if ($data['type']=="div") {
			if (! isset($data['text'])) { $data['text']=""; }
			echo '<h2>' . $data['text'] . "</h2>";
			echo '<div class="form-group" id="Block_' . $data['fieldid'] . '">';
			echo '<div id="' . $data['fieldid'] . '"></div>';
			echo '</div>';
			return;
		}
		if ($data['type']=="hidden") {
			echo '<input type="hidden" name="' . $data['fieldname'] .'" id="' . $data['fieldname'] .'" value="'. $data['value'] . '">';
			return;
		}
		if ($data['type']=="daysweek") {
			$clase="";
			if (isset($data['weekcheck'])) { $clase=" " . $data['weekcheck']; }
			echo '<div class="form-group' . $clase . '" id="Block_DaysWeek">';
			echo "<label>" . _($data['text']) . "</label>";
			echo '<div class="checkbox-list">';
			foreach($data['fieldsnames'] as $idfield=>$field) {
				$output=' name="' . $field .'" id="' . $field .'"';
				if ($data['values'][$idfield]==1) { $output.=' checked="checked"'; }
				echo '<label class="checkbox-inline">';
				echo '<input type="checkbox" value="1"' . $output . '>';
				echo $data['textfields'][$idfield];
				echo '</label>';
			}
			echo '</div>';
			echo '</div>';
			return;
		}
		if ($data['type']=="checkbox-basic") {
			$output=' name="' . $data['fieldname'] .'"';
			if (isset($data['fieldid'])) { $output.=' id="' . $data['fieldid'] . '"'; } else { $output.=' id="' . $data['fieldname'] . '"'; }
			if (isset($data['required'])) { if ($data['required']) { $output.=' required="required"'; } }
			if (isset($data['readonly'])) { if ($data['readonly']) { $output.=' readonly="readonly"'; } }
			if (isset($data['disabled'])) { if ($data['disabled']) { $output.=' disabled="disabled"'; } }
			if (isset($data['help'])) { $output.=' placeholder="' . $data['help'] . '"'; }
			if (isset($data['checked'])) { if ($data['checked']) { $output.=" checked"; } }
			echo '<div class="form-group" id="Block_' . $data['fieldname'] . '">';
			echo '<div class="checkbox-list">';
			echo '<label><input type="checkbox" value="' . $data['value'] . '"' . $output . '> ' . _($data['text']) . "</label>";
			echo '</div>';
			echo '</div>';
			return;
		}
		//print_r($data);
		echo '<div class="form-group" id="Block_' . $data['fieldname'] . '">';
		echo "<label>" . _($data['text']) . "</label>";
		if (strpos($data['type'], "multiple")!==false) {
			$output=' name="' . $data['fieldname'] .'[]"';
		} else {
			$output=' name="' . $data['fieldname'] .'"';
		}
		if (isset($data['fieldid'])) { $output.=' id="' . $data['fieldid'] . '"'; } else { $output.=' id="' . $data['fieldname'] . '"'; }
		if (isset($data['required'])) { if ($data['required']) { $output.=' required="required"'; } }
		if (isset($data['readonly'])) { if ($data['readonly']) { $output.=' readonly="readonly"'; } }
		if (isset($data['disabled'])) { if ($data['disabled']) { $output.=' disabled="disabled"'; } }
		if (isset($data['minvalue'])) { $output.=' min="' . $data['minvalue'] . '"'; }
		if (isset($data['maxvalue'])) { $output.=' max="' . $data['maxvalue'] . '"'; }
		if (isset($data['stepvalue'])) { $output.=' step="' . $data['stepvalue'] . '"'; }
		if (isset($data['help'])) { $output.=' placeholder="' . $data['help'] . '"'; }
		if (isset($data['checkscript'])) { $output.=' check-script="' . $data['checkscript'] . '"'; }
		
		if ($data['type']=="text") {
			echo '<input class="form-control" type="text"' . $output . ' value=\''. $data['value'] . '\'>';
		}
		if ($data['type']=="password") {
			echo '<input class="form-control" type="password"' . $output . ' value=\''. $data['value'] . '\'>';
		}
		if ($data['type']=="url") {
			echo '<div class="input-group"><span class="input-group-addon"><i class="fa fa-link"></i></span>';
			echo '<input class="form-control" type="url"' . $output . ' value="'. $data['value'] . '">';
			echo '</div>';
		}
		if ($data['type']=="email") {
			echo '<div class="input-group"><span class="input-group-addon"><i class="fa fa-envelope"></i></span>';
			echo '<input class="form-control" type="email"' . $output . ' value="'. $data['value'] . '">';
			echo '</div>';
		}
		if ($data['type']=="number") {
			echo '<div class="input-group input-medium spinner">';
			echo '<input class="form-control spinner-input" type="number"' . $output . ' value="'. $data['value'] . '">';
			echo '<div class="spinner-buttons input-group-btn"><button class="btn spinner-down red" type="button"><i class="fa fa-minus"></i></button><button class="btn spinner-up blue" type="button"><i class="fa fa-plus"></i></button></div>';
			echo '</div>';
		}
		if ($data['type']=="float") {
			echo '<div class="input-group input-medium">';
			echo '<input class="form-control spinner-input" type="number"' . $output . ' value="'. $data['value'] . '">';
			echo '</div>';
		}
		if ($data['type']=="date") {
			echo '<div class="input-group"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>';
			echo '<input class="form-control form-control-inline input-small date-picker" size="16" type="text"' . $output . ' value="'. $data['value'] . '"/>';
			echo '</div>';
		}
		if ($data['type']=="time") {
			echo '<div class="input-group"><span class="input-group-addon"><i class="fa fa-clock-o"></i></span>';
			echo '<input class="form-control form-control-inline input-small timepicker-no-seconds" size="16" type="text"' . $output . ' value="'. $data['value'] . '"/>';
			echo '</div>';
		}
		if ($data['type']=="datetime") {
			echo '<div class="input-group"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>';
			echo '<input class="form-control form-control-inline input-medium form_datetime" size="16"' . $output . ' value="'. $data['value'] . '"/>';
			echo '</div>';
		}
		if ($data['type']=="doubledate") {
			if (isset($data['weekcheck'])) { $output.=' weekcheck="' . $data['weekcheck'] . '"'; }
			$output2=$output;
			$output2=str_replace('name="' . $data['fieldname'] .'"', ' name="' . $data['fieldname2'] .'"', $output2);
			if (isset($data['fieldid2'])) { $reemplazar=$data['fieldid2']; } else { $reemplazar=$data['fieldname2']; }
			if (isset($data['fieldid'])) { $output2=str_replace(' id="' . $data['fieldid'] .'"', ' id="' . $reemplazar .'"', $output2); } else {$output2=str_replace(' id="' . $data['fieldname'] .'"', ' id="' . $reemplazar .'"', $output2); }
			echo '<div class="input-group">';
			echo '<span class="input-group-addon"><i class="fa fa-calendar"></i></span>';
			echo '<input class="form-control input-small date-picker check-double-date" size="16" type="text"' . $output . ' value="'. $data['value'] . '" validate-from="' . $data['fieldname'] . '" validate-to="' . $data['fieldname2'] . '"/>';
			echo '<input class="form-control input-small date-picker check-double-date" size="16" type="text"' . $output2 . ' value="'. $data['value2'] . '" validate-from="' . $data['fieldname'] . '" validate-to="' . $data['fieldname2'] . '"/>';
			echo '</div>';
		}
		if ($data['type']=="doubletime") {
			$output2=$output;
			$output2=str_replace('name="' . $data['fieldname'] .'"', ' name="' . $data['fieldname2'] .'"', $output2);
			if (isset($data['fieldid2'])) { $reemplazar=$data['fieldid2']; } else { $reemplazar=$data['fieldname2']; }
			if (isset($data['fieldid'])) { $output2=str_replace(' id="' . $data['fieldid'] .'"', ' id="' . $reemplazar .'"', $output2); } else {$output2=str_replace(' id="' . $data['fieldname'] .'"', ' id="' . $reemplazar .'"', $output2); }
			echo '<div class="input-group">';
			echo '<span class="input-group-addon"><i class="fa fa-clock-o"></i></span>';
			echo '<input class="form-control input-small check-double-time" size="16" type="text"' . $output . ' value="'. $data['value'] . '" validate-from="' . $data['fieldname'] . '" validate-to="' . $data['fieldname2'] . '" calc-total="' . $data['fieldname2'] . '"/>';
			echo '<input class="form-control input-small check-double-time" size="16" type="text"' . $output2 . ' value="'. $data['value2'] . '" validate-from="' . $data['fieldname'] . '" validate-to="' . $data['fieldname2'] . '" calc-total="' . $data['fieldname2'] . '"/>';
			echo '</div>';
		}
		if ($data['type']=="doubletime-withcalc") {
			$output2=$output;
			$output2=str_replace('name="' . $data['fieldname'] .'"', ' name="' . $data['fieldname2'] .'"', $output2);
			if (isset($data['fieldid2'])) { $reemplazar=$data['fieldid2']; } else { $reemplazar=$data['fieldname2']; }
			if (isset($data['fieldid'])) { $output2=str_replace(' id="' . $data['fieldid'] .'"', ' id="' . $reemplazar .'"', $output2); } else {$output2=str_replace(' id="' . $data['fieldname'] .'"', ' id="' . $reemplazar .'"', $output2); }
			$output3=$output;
			$output3=str_replace('name="' . $data['FieldName'] .'"', ' name="' . $data['FieldName3'] .'"', $output3);
			if (isset($data['fieldid3'])) { $reemplazar=$data['FieldID3']; } else { $reemplazar=$data['fieldname3']; }
			if (isset($data['fieldid'])) { $output2=str_replace(' id="' . $data['fieldid'] .'"', ' id="' . $reemplazar .'"', $output3); } else {$output3=str_replace(' id="' . $data['fieldname'] .'"', ' id="' . $reemplazar .'"', $output3); }
			echo '<div class="input-group">';
			echo '<span class="input-group-addon"><i class="fa fa-clock-o"></i></span>';
			echo '<input class="form-control input-small timepicker-no-seconds check-double-time" size="16" type="text"' . $output . ' value="'. $data['value'] . '" validate-from="' . $data['fieldname'] . '" validate-to="' . $data['fieldname2'] . '" calc-total="' . $data['fieldname3'] . '"/>';
			echo '<input class="form-control input-small timepicker-no-seconds check-double-time" size="16" type="text"' . $output2 . ' value="'. $data['value2'] . '" validate-from="' . $data['fieldname'] . '" validate-to="' . $data['fieldname2'] . '" calc-total="' . $data['fieldname3'] . '"/>';
			echo '<input class="form-control input-small" type="text"' . $output3 . ' value="'. $data['value3'] . '" readonly="readonly">';
			echo '</div>';
		}
		if ($data['type']=="doubledatetime") {
			if (isset($data['weekcheck'])) { $output.=' weekcheck="' . $data['weekcheck'] . '"'; }
			$output2=$output;
			$output2=str_replace('name="' . $data['fieldname'] .'"', ' name="' . $data['fieldname2'] .'"', $output2);
			if (isset($data['fieldid2'])) { $reemplazar=$data['fieldid2']; } else { $reemplazar=$data['fieldname2']; }
			if (isset($data['fieldid'])) { $output2=str_replace(' id="' . $data['fieldid'] .'"', ' id="' . $reemplazar .'"', $output2); } else {$output2=str_replace(' id="' . $data['fieldname'] .'"', ' id="' . $reemplazar .'"', $output2); }
			if (isset($data['fieldid'])) { $output3=str_replace(' id="' . $data['fieldid'] .'"', ' id="' . $reemplazar .'"', $output3); } else {$output3=str_replace(' id="' . $data['fieldname'] .'"', ' id="' . $reemplazar .'"', $output3); }			
			echo '<div class="input-group">';
			echo '<span class="input-group-addon"><i class="fa fa-calendar"></i></span>';
			echo '<input class="form-control form-control-inline input-medium form_datetime check-double-date" size="10" type="text"' . $output . ' value="'. $data['value'] . '" validate-from="' . $data['fieldname'] . '" validate-to="' . $data['fieldname2'] . '" />';
			echo '<input class="form-control form-control-inline input-medium form_datetime check-double-date" size="10" type="text"' . $output2 . ' value="'. $data['value2'] . '" validate-from="' . $data['fieldname'] . '" validate-to="' . $data['fieldname2'] . '"/>';
			echo '</div>';
		}
		if ($data['type']=="geo") {
			echo '<div class="input-group"><span class="input-group-addon"><i class="fa fa-map-marker"></i></span>';
			echo '<input class="form-control" type="text"' . $output . ' value="'. $data['value'] . '">';
			echo '<span class="input-group-btn"><a class="btn btn-success showgeo" type="button" href="../lib/maps/geolocalizar.php?' . $data['value'] . '"><i class="fa fa-external-link fa-fw"></i>Geolocalizar</a></span>';
			echo '</div>';
		}
		if ($data['type']=="fileopt") {
			echo '<div class="input-group"><span class="input-group-addon"><i class="fa fa-crop"></i></span>';
			echo '<input class="form-control" type="text"' . $output . ' value="'. $data['value'] . '">';
			echo '<span class="input-group-btn"><a class="btn btn-success showgeo" type="button" href="../lib/images/setoptions.php?f=' . $data['fieldname'] . '"><i class="fa fa-external-link fa-fw"></i>Configurar</a></span>';
			echo '</div>';
		}

		if ($data['type']=="textarea") {
			echo '<textarea class="form-control"' . $output . '>' . $data['value'] . '</textarea>';
		}
		if ($data['type']=="html") {
			echo '<textarea class="ckeditor form-control"' . $output . '>' . $data['value'] . '</textarea>';
		}
		if ($data['type']=="iframe") {
			echo '<iframe width="100%" height="300"' . $output . '>' . $data['value'] . '</iframe>';
		}
		if ($data['type']=="checkbox") {
			if ($data['value']==1) { $output.=" checked"; }
			echo '<div class="checkbox-list">';
			echo '<input type="checkbox" class="make-switch" value="1" data-on-color="primary" data-off-color="danger" data-on-text="<i class=\'fa fa-check\'></i>" data-off-text="<i class=\'fa fa-times\'></i>"' . $output . '>';
			echo '</div>';
		}
		if ($data['type']=="combo") {
			echo '<select class="form-control"' . $output . '>';
			$dat=get_class($GLOBALS['Core']);
			if (isset($data['disablevalue'])) { 
				$selected="";
				if ($data['disablevalue']==$data['value']) { $selected=' selected="selected"'; }
				echo '<option value="' . $data['disablevalue'] . '"' . $selected . '>' . _("Deshabilitado") . '</option>';
			}
			if (isset($data['nullvalue'])) { 
				$selected="";
				if ($data['nullvalue']==$data['value']) { $selected=' selected="selected"'; }
				echo '<option value="' . $data['nullvalue'] . '"' . $selected . '>' . _("Sin definir") . '</option>';
			}
			echo $dat::$db->PopulateCombo($data['listtable'],$data['listvalue'],$data['listoption'],$data['value'],$data['listorder']);
			echo '</select>';
		}
		if ($data['type']=="combo-json") {
			echo '<select class="form-control"' . $output . '>';
			if (isset($data['disablevalue'])) { 
				$selected="";
				if ($data['disablevalue']==$data['value']) { $selected=' selected="selected"'; }
				echo '<option value="' . $data['disablevalue'] . '"' . $selected . '>' . _("Deshabilitado") . '</option>';
			}
			if (isset($data['nullvalue'])) { 
				$selected="";
				if ($data['nullvalue']==$data['value']) { $selected=' selected="selected"'; }
				echo '<option value="' . $data['nullvalue'] . '"' . $selected . '>' . _("Sin definir") . '</option>';
			}
			foreach ($data['jsonvalues'] as $iditem=>$item) {
				$selected="";
				if ($data['value']==$iditem) { $selected=" selected"; }
				echo '<option value="' . $iditem . '"' .$selected . '>' . _($item) . '</option>';
			}
			echo '</select>';
		}
		if ($data['type']=="combo-multiple") {
			if (is_array($data['value']))  {  $data['value']=implode(",", $data['value']);}
			echo '<select class="form-control" multiple="multiple"' . $output . '>';
				$dat=get_class($GLOBALS['Core']);
				echo $dat::$db->PopulateCombo($data['listtable'],$data['listvalue'],$data['listoption'],$data['value'],$data['listorder']);
			echo '</select>';
		}
		if ($data['type']=="combo-multiple-json") {
			echo '<select class="form-control" multiple="multiple"' . $output . '>';
			$valores=explode(",",$data['value']);
			foreach ($data['jsonvalues'] as $iditem=>$item) {
				$selected="";
				if (in_array($iditem,$valores)) { $selected=" selected"; }
				echo '<option value="' . $iditem . '"' .$selected . '>' . _($item) . '</option>';
			}
			echo '</select>';
		}
		if ($data['type']=="upload") {
			$folder="public/thumbnails";
			$item="first";
			$external="";
			if (isset($data['uploaditem'])) { $item=$data['uploaditem']; }
			if (isset($data['external'])) { $external=$data['external']; }
			$block_upload="uplblk_" . $data['fieldname'];
			$change=str_replace('Form_', 'Change_', $data['fieldname']);
			$original=str_replace('Form_', 'Original_', $data['fieldname']);
            if(is_file(sitepath . $data['previewfolder'] . "/" . $data['value'])){
	            echo '<input type="hidden" id="' . $data['fieldname'] . '" name="' . $data['fieldname'] . '" value="' . $data['value'] . '">';
	            if ($data['uploadtype']=="file") {
	            	echo '<ul class="extras gallery ' . $item . ' no-edit" script="' . $_SERVER['PHP_SELF'] . '" external="' . $external . '" data-element="' . $data['fieldname'] . '" upl-block="' . $block_upload . '">';
	            	echo '<li><a href="../' . $data['previewfolder']. '/' . $data['value'] . '"><div class="fileupload-nofile">' . $data['value'] . '</div><span alt="Elimina el archivo actual y permite cargar otro nuevo" class="remove-this-item">Borrar</span></a></li>';
	            } else {
	            	$link="";
	            	if (isset($data['preview'])) { $link=' class="fancybox" href="../public/images/' . $data['value'] . '"'; }
	            	$edit=true;
	            	$del=true;
	            	if (isset($data['noedit'])) { $edit=$data['noedit']; }
	        		if (isset($data['nodelete'])) { $del=$data['nodelete']; }
	            	echo '<ul class="extras gallery ' . $item . ' edit-confirm" script="' . $_SERVER['PHP_SELF'] . '" module="' . $data['module'] .'" external="' . $external . '" data-element="' . $data['fieldname'] . '" upl-block="' . $block_upload . '">';
	            	echo '<li><a ' . $link . '><img src="../' . $data['previewfolder'] . '/' . $data['value'] . '" />';
	            	if ($edit) { echo '<span alt="Editar imagen" class="edit-this-item">Editar</span>'; }
	            	if ($del) { echo '<span alt="Elimina el archivo actual y permite cargar otro nuevo" class="remove-this-item">Borrar</span>'; }
	            	echo '</a></li>';
	            }
	            echo '</ul>';
	            $view='none';
            } else { 
                $view='block';
           	}
            echo '<div id="' . $block_upload . '" style="display:' . $view . ';" >';
            echo '<input type="file" id="' . $change . '" name="' . $change . '" data-allowed-extensions="[' . $data['extensions'] . ']" data-max-file-size="10000000" class="input_file">';
            echo '<input type="hidden" id="' . $original . '" name="' . $original . '" value="' . $data['value'] . '">';
            echo '</div>';
		}
		if ($data['type']=="upload-multiple") {
			echo '<input type="file" data-auto-upload="true" multiple data-allowed-extensions="[' . $data['extensions'] . ']" class="input_file" id="' . $data['fieldname'] . '" name="' . $data['fieldname'] . '">';
		}
		if ($data['type']=="tags") {
			echo '<input type="text" class="form-control tags" value="'. implode(",",$data['value']) . '"' . $output . '/>';
		}
		if ($data['type']=="tags-fixed") {
			$sugg="";
			$preldr="''";
			if (isset($data['suggestions'])) { $sugg=$data['suggestions']; }
			if (isset($data['preload'])) { $preldr=json_encode($data['preload']); }
			echo '<input type="text" class="form-control tags-fixed" preload=' . "'" . $preldr . "'" . ' url-suggestions="'. $sugg . '"' . $output . ' />';
		}
		if ($data['type']=="jstree") {
			if (isset($data['fieldid'])) { $datafield=' data-field="' . $data['fieldid'] . '"'; } else { $datafield=' data-field="' . $data['fieldname'] . '"'; }
			echo '<div class="jstree" data-url="' . $data['jsonurl']. '" ' . $datafield . '></div>';
			echo '<input type="hidden"' . $output . ' value=\''. $data['value'] . '\'>';

		}
		echo "</div>";
	}
?>