<?php	

	// crea el objeto con la vista
	$tpl = new Acme("landing");

	// carga la vista
	$tpl->loadTPL();

	// array para pasar variables a la vista
	$vars = ["PROJECT_SECTION" => "Landing"];

	// reemplaza las variables en la vista
	$tpl->setVarsTPL($vars);

	// imprime en la página la vista
	$tpl->printTPL();

 ?>