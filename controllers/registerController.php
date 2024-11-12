<?php 

	// crea el objeto con la vista
	$tpl = new Acme("register");

	// carga la vista
	$tpl->loadTPL();

	// array para pasar variables a la vista
	$vars = ["MSG_REGISTER_ERROR" => "", "PROJECT_SECTION" => "Register"];

	// si se presiono el botón del formulario
	if(isset($_POST['btn_register'])){

		// crea un usuario
		$usuario = new User();

		// quitamos del array de post el boton
		unset($_POST['btn_register']);

		// procede a intentar el registro
		$response = $usuario->register($_POST);

		// el registro fue valido
		if($response["errno"]==200 || $response["errno"]==201){

			// se pasa el objeto de usuario a Session
			//$_SESSION["innovplast"]['user'] = $usuario;

			// redirecciona al panel
			header("Location: login");
		}

		// Si hubo cualquier error se carga el mensaje de error de la vista
		$vars = ["MSG_REGISTER_ERROR" => $response["error"], "PROJECT_SECTION" => "Register"];
	}

	// se pasan las variables a la vista
	$tpl->setVarsTPL($vars);

	// imprime en pantalla la página
	$tpl->printTPL();


 ?>