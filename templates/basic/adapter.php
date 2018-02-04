<?php
//Cargamos la biblioteca que minimiza CSS y JS
require_once(sitepath . "lib/minimizer/functions.php");
//Cargamos la clase que controla las alertas web
if (is_file(sitepath . "include/sticker/sticker.class.php")) {
	require_once(sitepath . "include/sticker/sticker.class.php");
	$this->Sticker=new Sticker($this->params);
	$this->Sticker->GetSticker();
}
//Cargamos el menú de la web
if (is_file(sitepath . "include/menu/menu.class.php")) {
	require_once(sitepath . "include/menu/menu.class.php");
	$this->Menu=new Menu($this->params);
	$this->Menu->GenerateMenu($this->Menu->Menu);
}
//Establecemos la función que genera los paginadores
function TemplatePutPaginator($page,$offset,$lastpage,$linkpage,$linkparams) {
	if ($linkparams!="") { $linkparams="&".$linkparams; }
	$page--;
	if ($lastpage>1) {
		echo '<ul class="pagination">';
		$anteriorpag=$page;
		if ($anteriorpag<1) {$anteriorpag=1; }
		$siguientepag=$page+2;
		if ($siguientepag>$lastpage) {$siguientepag=$lastpage; }
		$iniciopag=$page-4;
		if ($iniciopag<0) {$iniciopag=0; }
		$finalpag=$iniciopag+9;
		if ($finalpag>$lastpage) {$finalpag=$lastpage; }
		echo '<li><a aria-label="Previous" href="' . $linkpage . "?page=$anteriorpag&offset=$offset" . $linkparams . '">&laquo;</a></li>';
		for($i=$iniciopag; $i<$finalpag; $i++) {
			$linkpag=$i+1;
			if ($i!=$page) { 
				echo '<li><a href="' . $linkpage . "?page=$linkpag&offset=$offset" . $linkparams . '">' . $linkpag . '</a></li>'; 
			} else {
				echo '<li class="active"><a href="' . $linkpage . "?page=$linkpag&offset=$offset" . $linkparams . '">' . $linkpag . ' </a></li>'; 
			}
		} 
		echo '<li> <a aria-label="Next" href="' . $linkpage . "?page=$lastpage&offset=$offset" . $linkparams . '">&raquo;</a></li>';
		echo "</ul>";
	}
}
?>