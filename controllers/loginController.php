<?php 
	
	// crea el objeto con la vista
	$tpl = new Acme("login");

	// carga la vista
	$tpl->loadTPL();

	// array para pasar variables a la vista
	$vars = ["PROJECT_SECTION" => "Login"];

	// reemplaza las variables en la vista
	$tpl->setVarsTPL($vars);

	// imprime en pantalla la página
	$tpl->printTPL();

 ?>