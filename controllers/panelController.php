<?php 

	// crea el objeto con la vista
	$tpl = new Acme("panel");

	// carga la vista
	$tpl->loadTPL();

	//array con las variables a cargar en la vista
	$vars = ["PROJECT_SECTION" => "Panel"];

	//carga el array con las vaiables en la vista
	$tpl->setVarsTPL($vars);

	// imprime en la vista en la página
	$tpl->printTPL();

 ?>