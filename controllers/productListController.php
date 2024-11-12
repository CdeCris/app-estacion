<?php 

	// crea el objeto con la vista
	$tpl = new Acme("productList");

	// carga la vista
	$tpl->loadTPL();

	$vars = ["PROJECT_SECTION" => "Productos"];

	$tpl->setVarsTPL($vars);
	
	// imprime en la página la vista
	$tpl->printTPL();

 ?>