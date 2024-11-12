<?php 
	
	// crea el objeto con la vista
	$tpl = new Acme("detalle");

	// carga la vista
	$tpl->loadTPL();

	//array con las variables a cargar en la vista
	$vars = ["PROJECT_SECTION" => "Detalle"];

	//carga el array con las vaiables en la vista
	$tpl->setVarsTPL($vars);

	// imprime en la página la vista
	$tpl->printTPL();

 ?>