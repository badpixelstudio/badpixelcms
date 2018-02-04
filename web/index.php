<?php
//Cargamos el adaptador de la plantilla
require_once(sitepath . "templates/" . $this->template . "/adapter.php");
//Cargamos el slider
require_once(sitepath . "include/slider/slider.class.php");
//Cargamos contenidos, para mostrar la página 1
require_once(sitepath . "include/contents/contents.class.php");

//Instanciamos el slider y cargamos datos
$this->Slider= new Slider($this->params);
$this->Slider->GetSlider();
//Instanciamos contenidos y cargamos datos del elemento 1.
$this->Contents= new Contents($this->params);
$this->Contents->EditItem(1);

//Mostramos la plantilla
$this->loadtemplatepublic('index.tpl.php');
?>