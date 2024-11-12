<?php 

	// se muestra el contenido de SESSION (para debug)
	$usuario = $_SESSION["innovplast"]["user"];

	// crea el objeto con la vista
	$tpl = new Acme("perfil");

	// carga la vista
	$tpl->loadTPL();

	// crea el array con variables a reemplazar en la vista
	$vars = ["MSG_REGISTER_ERROR" => "", "APELLIDO_USUARIO" => $usuario->apellido, "EMAIL_USUARIO" => $usuario->email, "TELEFONO_USUARIO" => $usuario->telefono, "DOMICILIO_USUARIO" => $usuario->domicilio, "PROJECT_SECTION" => "Perfil"];

	// si se presiono el botón del formulario
	if(isset($_POST['btn_guardar'])){

		// quitamos del array de post el boton
		unset($_POST['btn_guardar']);

		// procede a intentar el registro del perfil
		$response = $usuario->update($_POST);

		// se actualizo correctamente
		if($response["errno"]==200){

			//header("Location: perfil");
		}
		// Si hubo cualquier error se carga el mensaje de error de la vista
		$vars = ["MSG_REGISTER_ERROR" => $response["error"], "NOMBRE_USUARIO" => $usuario->nombre, "APELLIDO_USUARIO" => $usuario->apellido, "AVATAR_USUARIO" => $usuario->avatar, "EMAIL_USUARIO" => $usuario->email, "TELEFONO_USUARIO" => $usuario->telefono, "DOMICILIO_USUARIO" => $usuario->domicilio, "PROJECT_SECTION" => "Perfil"];	
	}

	// se pasan las variables a la vista
	$tpl->setVarsTPL($vars);

	// imprime en pantalla la página
	$tpl->printTPL();


 ?>