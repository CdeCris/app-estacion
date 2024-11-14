<?php 

	// crea el objeto con la vista
	$tpl = new Acme("register");

	// carga la vista
	$tpl->loadTPL();

	// array para pasar variables a la vista
	$vars = ["PROJECT_SECTION" => "Register"];

	// se pasan las variables a la vista
	$tpl->setVarsTPL($vars);

	// imprime en pantalla la página
	$tpl->printTPL();





 ?>