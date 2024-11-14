<?php 
	
	// crea el objeto con la vista
	$tpl = new Acme("blocked");

	// carga la vista
	$tpl->loadTPL();

	//array con las variables a cargar en la vista
	$vars = ["PROJECT_SECTION" => "Bloquear Cuenta"];

	//carga el array con las vaiables en la vista
	$tpl->setVarsTPL($vars);

	// imprime en la página la vista
	$tpl->printTPL();

 ?>