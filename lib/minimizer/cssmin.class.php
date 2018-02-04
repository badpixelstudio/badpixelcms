<?php
class CSSMin {
	function minify($css) {
		$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
    	/* remove tabs, spaces, newlines, etc. */
    	$css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);
    	return $css;
	}
}

?>