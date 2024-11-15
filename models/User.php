<?php 

	/**
	* @file User.php
	* @brief Declaraciones de la clase User para la conexión con la base de datos.
	* @author ACME
	* @date 2024
	* @contact contacto.innovplast@gmail.com
	*/

	// incluye la libreria para conectar con la db
	include_once 'DBAbstract.php';

	/*< incluye la clase Mailer.php para enviar correo electrónico*/
	include_once 'Mailer.php';

	// se crea la clase User que hereda de DBAbstract
	class User extends DBAbstract{

		private $nameOfFields = array();

		/**
		 * 
		 * @brief Es el constructor de la clase User
		 * 
		 * Al momento de instanciar User se llama al padre para que ejecute su constructor
		 * 
		 * */
		function __construct(){

			// quiero salir de la clase actual e invocar al constructor
			parent::__construct();

			/**< Obtiene la estructura de la tabla */
			$result = $this->query('DESCRIBE `app_estacion__usuario`');

			foreach ($result as $key => $row) {
				$buff =$row["Field"];
				
				/**< Almacena los nombres de los campos*/
				$this->nameOfFields[] = $buff;

				/**< Autocarga de atributos a la clase */
				$this->$buff=NULL;
			}
			

		}

		/**
		 * 
		 * Hace soft delete del registro
		 * @return bool siempre verdadero
		 * 
		 * */
		function leaveOut(){

			$ID_USUARIO = $this->ID_USUARIO;
			$fecha_hora = date("Y-m-d H:i:s");

			$ssql = "UPDATE `app_estacion__usuario` SET delete_at='$fecha_hora' WHERE ID_USUARIO=$ID_USUARIO";

			$this->query($ssql);

			return true;
		}

		/**
		 * 
		 * Finaliza la sesión
		 * @return bool true
		 * 
		 * */
		function logout(){
			// Borra las variables de sesión
			session_unset();

			// Elimina la sesión
			session_destroy();

			// Redirecciona a landing
			header("Location: landing");
		}

		/**
		 * 
		 * Intenta loguear al usuario mediante email y contraseña
		 * @param array $form indexado de forma asociativa
		 * @return array que posee códigos de error especiales
		 * 
		 * */
		function login($form){
			
			/*< recupera el method http*/
			$request_method = $_SERVER["REQUEST_METHOD"];

			/* si el method es invalido*/
			if($request_method!="GET"){
				return ["errno" => 410, "error" => "Metodo invalido"];
			}

			/*< recupera el email del formulario*/
			$email = $form["txt_email"];

			/*< consultamos si existe el email*/
			$result = $this->query("CALL `app_estacion_Login`('$email')");

			// el email no existe
			if(count($result)==0){
				return ["error" => "Email no registrado", "errno" => 404];
			}

			/*< seleccionamos solo la primer fila de la matriz*/
			$result = $result[0];
		

			//Inicio del ChatgGPT Moment xd
			$ip = $_SERVER['REMOTE_ADDR'] ?? 'IP no disponible';

			// Si estás detrás de un proxy, intenta obtener la IP real
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			    $ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}

			$navegador = $_SERVER['HTTP_USER_AGENT'];

			$sistemaOperativo = "Desconocido";

			if (strpos($navegador, 'Windows') !== false) {
			    $sistemaOperativo = "Windows";
			} elseif (strpos($navegador, 'Mac') !== false) {
			    $sistemaOperativo = "MacOS";
			} elseif (strpos($navegador, 'Linux') !== false) {
			    $sistemaOperativo = "Linux";
			} elseif (strpos($navegador, 'Android') !== false) {
			    $sistemaOperativo = "Android";
			} elseif (strpos($navegador, 'iPhone') !== false) {
			    $sistemaOperativo = "iOS";
			}
			//Fin del ChatgGPT Moment


			// si el email existe y la contraseña es valida
			if($result["contrasenia"]==md5($form["txt_pass"]."app-estacion")){

				if(!$result["activo"]){
					return ["error" => "Aún no ha validado su email, revise su casilla de correos", "errno" => 405];
				}

				if($result["bloqueado"]){
					return ["error" => "Su usuario está bloqueado, revise su casilla de correo", "errno" => 406];
				}

				if($result["recupero"]){
					return ["error" => "Su usuario está bloqueado, revise su casilla de correo", "errno" => 407];
				}

				/*< instancia la clase Mailer para enviar el correo electrónico de validación de correo electrónico*/
				$correo = new Mailer();

				// crea el objeto con la vista
				$tpl = new ACME("emails/loginSuccess");

				// carga la vista
				$tpl->loadTPLFromAPI();

				$vars = ["TOKEN_USER" => $result["token"], "IP_CLIENTE" => $ip, "NAVEGADOR_WEB" => $navegador, "SISTEMA_OPERATIVO" => $sistemaOperativo, "APP_URL_BASE" => $_ENV["APP_URL_BASE"]];

				/*< pasa el valor de la variable token a la vista*/
				$tpl->setVarsTPL($vars);

				/*< plantilla de email para validar cuenta*/
				$cuerpo_email = $tpl->returnTPL();

				/*< envia el correo electrónico de validación*/
				$correo->send(["destinatario" => $result["email"], "motivo" => "Nuevo Inicio de Sesion", "contenido" => $cuerpo_email] );

				/**< autocarga de valores en los atributos de la clase */
				foreach ($this->nameOfFields as $key => $value) {
					$this->$value = $result[$value];
				}

				/*< carga la clase en la sesión*/
				$_SESSION["app-estacion"]['user'] = $this;

				$_SESSION["app-estacion"]['user']->is_admin = 0;

				if ($result["email"] == 'admin-estacion') {
					$_SESSION["app-estacion"]['user']->is_admin = 1;

					return ["error" => "", "errno" => 210];

				}

				/*< usuario valido*/
				return ["error" => "", "errno" => 200];
			}

			/*< instancia la clase Mailer para enviar el correo electrónico de validación de correo electrónico*/
			$correo = new Mailer();

			// crea el objeto con la vista
			$tpl = new ACME("emails/loginFailure");

			// carga la vista
			$tpl->loadTPLFromAPI();

			$vars = ["TOKEN_USER" => $result["token"], "IP_CLIENTE" => $ip, "NAVEGADOR_WEB" => $navegador, "SISTEMA_OPERATIVO" => $sistemaOperativo, "APP_URL_BASE" => $_ENV["APP_URL_BASE"]];

			/*< pasa el valor de la variable token a la vista*/
			$tpl->setVarsTPL($vars);

			/*< plantilla de email para validar cuenta*/
			$cuerpo_email = $tpl->returnTPL();

			/*< envia el correo electrónico de validación*/
			$correo->send(["destinatario" => $result["email"], "motivo" => "Intento de Inicio de Sesion", "contenido" => $cuerpo_email] );


			// email existe pero la contraseña invalida
			return ["error" => "Error en las credenciales", "errno" => 405];

		}

		/**
		 * 
		 * Agrega un nuevo usuario si no existe el correo electronico en la tabla users
		 * @param array $form es un arreglo assoc con los datos del formulario
		 * @return array que posee códigos de error especiales 
		 * 
		 * */
		function register($form){

			/*< recupera el email*/
			$email = $form->email;

			/*< evalúa si el email es válido */
		    $email_valido = preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $email);
		
			/*< si el mail no es valido */
			if (!$email_valido) {
				return ["error" => "Correo inválido", "errno" => 406];
			}

			/*< evalúa si la contraseña es válida */
			$pass_valida = preg_match('/^\S{4,}$/', $form->pass);
		
			/*< si la contraseña no es valida */
			if (!$pass_valida) {
				return ["error" => "La contraseña no debe contener espacion y tener al menos 4 caracteres ", "errno" => 406];
			}

			/*< si las contraseñas ingresadas no son iguales */
			if (!($form->pass == $form->sec_pass)) {
				return ["error" => "Verifique que las contraseña ingresadas sean iguales ", "errno" => 407];
			}


			/*< consulta si el email ya esta en la tabla de usuarios*/
			$result = @$this->query("SELECT * FROM `app_estacion__usuario` WHERE email = '$email'")[0];

			// el email no existe entonces se registra
			if(is_null($result)){

				/*< encripta la contraseña*/
				$pass = md5($form->pass."app-estacion");

				/*< se crea el token único para validar el correo electrónico*/
				$token_email = md5($_ENV['PROJECT_WEB_TOKEN'].$email.mt_rand(0,5000));

				$token_user = md5($_ENV['PROJECT_WEB_TOKEN'].$email);

				/*< agrega el nuevo usuario*/
				//$ssql = "CALL `Innovplast_Register`('$token_email','$email','$pass')";
				$ssql = "INSERT INTO `app_estacion__usuario` (`token`, `email`, `nombres`, `contrasenia`, `activo`, `bloqueado`, `recupero`, `token_action`, `add_date`, `update_date`, `delete_date`, `active_date`, `blocked_date`, `recover_date`) VALUES ('$token_user', '$email', ' ', '$pass', '0', '0', '0', '$token_email', CURRENT_TIMESTAMP, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00')";

				/*< ejecuta la consulta*/
				$result = $this->query($ssql);

				/*< se recupera el id del nuevo usuario*/
				$this->id_user = $this->db->insert_id;

				$id_user = $this->id_user;

				/*< instancia la clase Mailer para enviar el correo electrónico de validación de correo electrónico*/
				$correo = new Mailer();

				// crea el objeto con la vista
				$tpl = new ACME("emails/register");

				// carga la vista
				$tpl->loadTPLFromAPI();

				$vars = ["TOKEN_EMAIL" => $token_email, "APP_URL_BASE" => $_ENV["APP_URL_BASE"]];

				/*< pasa el valor de la variable token a la vista*/
				$tpl->setVarsTPL($vars);

				/*< plantilla de email para validar cuenta*/
				$cuerpo_email = $tpl->returnTPL();

				/*< envia el correo electrónico de validación*/
				$correo->send(["destinatario" => $email, "motivo" => "Confirmación de registro", "contenido" => $cuerpo_email] );

				/*< aviso de registro exitoso*/
				return ["error" => "Usuario registrado", "errno" => 200];
			}

			$date_zero = '0000-00-00 00:00:00';

			// El usuario volvio a la aplicacion
			if($result['delete_date']!=$date_zero){

				/*< recupera el id del usuario que quiere volver a nuestra app*/
				$ID_USUARIO=$result["ID_USUARIO"];
				$this->ID_USUARIO = $result["ID_USUARIO"];

				/*< encripta la nueva contraseña*/
				$pass = md5($form["txt_pass"]."app_estacion");

				/*< consulta para volver a activar el usuario que se había ido*/
				//$ssql = "UPDATE `App-ACME__usuario` SET nombre='', apellido='', `password`='$pass', delete_at='0000-00-00 00:00:00' WHERE ID_USUARIO=$ID_USUARIO";

				/*< ejecuta la consulta*/
				$result = $this->query($ssql);

				/*< mensaje de usuario volvio a la app*/
				return ["error" => "Usuario que abandono volvio a la app", "errno" => 201];
			}

			// si el email existe 
			return ["error" => "Correo ya registrado", "errno" => 405];

		}

		/**
		 * 
		 * @brief Retorna un listado limitado
		 * @param string $request_method espera a GET
		 * @param array $request [inicio][cantidad]
		 * @return array lista con los datos de los usuarios 
		 * 
		 * */
		function validate($request){

			$token = $request["token_action"];

			$ssql= "SELECT * FROM `app_estacion__usuario` WHERE `app_estacion__usuario`.`token_action` LIKE '$token' AND `activo` = 0";
			
			$user = $this->query($ssql);
			
			if (!$user) {
				return ["errno" => 416, "error" => "El token no corresponde a un usuario"];
			}

			$result = $this->query("UPDATE `app_estacion__usuario` SET `app_estacion__usuario`.`activo` = '1', `app_estacion__usuario`.`token_action` = null, `app_estacion__usuario`.`active_date` = CURRENT_TIMESTAMP WHERE `app_estacion__usuario`.`token_action` LIKE '$token';");

			/*< instancia la clase Mailer para enviar el correo electrónico de validación de correo electrónico*/
			$correo = new Mailer();

			// crea el objeto con la vista
			$tpl = new ACME("emails/active");

			// carga la vista
			$tpl->loadTPLFromAPI();

			/*< plantilla de email para validar cuenta*/
			$cuerpo_email = $tpl->returnTPL();

			/*< envia el correo electrónico de validación*/
			$correo->send(["destinatario" => $user[0]["email"], "motivo" => "Cuenta Activada", "contenido" => $cuerpo_email] );

			return ["errno" => 200, "error" => "Correo validado con exito, inicie sesion!"];
		}


		/**
		 * 
		 * @brief Retorna un listado limitado
		 * @param string $request_method espera a GET
		 * @param array $request [inicio][cantidad]
		 * @return array lista con los datos de los usuarios 
		 * 
		 * */
		function blockAccount($request){

			$token = $request["token"];

			$ssql= "SELECT * FROM `app_estacion__usuario` WHERE `app_estacion__usuario`.`token` LIKE '$token'";
			
			$user = $this->query($ssql);
			
			if (!$user) {
				return ["errno" => 416, "error" => "El token no corresponde a un usuario"];
			}

			if ($user[0]["bloqueado"]) {
				return ["errno" => 200, "error" => "Esta cuenta ya ha sido bloqueada"];
			}

			$token_action = md5($_ENV['PROJECT_WEB_TOKEN'].$token.mt_rand(0,5000));

			$result = $this->query("UPDATE `app_estacion__usuario` SET `app_estacion__usuario`.`token_action` = '$token_action', `app_estacion__usuario`.`bloqueado` = '1', `app_estacion__usuario`.`blocked_date` = CURRENT_TIMESTAMP WHERE `app_estacion__usuario`.`token` LIKE '$token';");

			/*< instancia la clase Mailer para enviar el correo electrónico de validación de correo electrónico*/
			$correo = new Mailer();

			// crea el objeto con la vista
			$tpl = new ACME("emails/blocked");

			// carga la vista
			$tpl->loadTPLFromAPI();

			$vars = ["TOKEN_ACTION" => $token_action, "APP_URL_BASE" => $_ENV["APP_URL_BASE"]];

			/*< pasa el valor de las variables a la vista*/
			$tpl->setVarsTPL($vars);

			/*< plantilla de email para validar cuenta*/
			$cuerpo_email = $tpl->returnTPL();

			/*< envia el correo electrónico de validación*/
			$correo->send(["destinatario" => $user[0]["email"], "motivo" => "Cuenta Bloqueada", "contenido" => $cuerpo_email] );

			return ["errno" => 200, "error" => "Usuario bloqueado, revise su correo electrónico"];
		}

		/**
		 * 
		 * @brief Retorna un listado limitado
		 * @param string $request_method espera a GET
		 * @param array $request [inicio][cantidad]
		 * @return array lista con los datos de los usuarios 
		 * 
		 * */
		function resetAccount($request){

			$pass = $request->pass;
			$pass2 = $request->sec_pass;
			$token_action = $request->token;

			if (!($pass == $pass2)) {
				return ["errno" => 404, "error" => "Las contraseñas ingresadas deben ser iguales"];
			}

			$new_pass = md5($pass."app-estacion");

			$ssql = "SELECT * FROM `app_estacion__usuario` WHERE `app_estacion__usuario`.`token_action` LIKE '$token_action';";

			$user = @$this->query($ssql)[0];
			
			$ssql = "UPDATE `app_estacion__usuario` SET `app_estacion__usuario`.`contrasenia` = '$new_pass', `app_estacion__usuario`.`token_action` = null, `app_estacion__usuario`.`bloqueado` = 0, `app_estacion__usuario`.`recupero` = 0, `app_estacion__usuario`.`recover_date` = CURRENT_TIMESTAMP  WHERE `app_estacion__usuario`.`token_action` LIKE '$token_action';";

			$result = $this->query($ssql);


			//Inicio del ChatgGPT Moment xd
			$ip = $_SERVER['REMOTE_ADDR'] ?? 'IP no disponible';

			// Si estás detrás de un proxy, intenta obtener la IP real
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			    $ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}

			$navegador = $_SERVER['HTTP_USER_AGENT'];

			$sistemaOperativo = "Desconocido";

			if (strpos($navegador, 'Windows') !== false) {
			    $sistemaOperativo = "Windows";
			} elseif (strpos($navegador, 'Mac') !== false) {
			    $sistemaOperativo = "MacOS";
			} elseif (strpos($navegador, 'Linux') !== false) {
			    $sistemaOperativo = "Linux";
			} elseif (strpos($navegador, 'Android') !== false) {
			    $sistemaOperativo = "Android";
			} elseif (strpos($navegador, 'iPhone') !== false) {
			    $sistemaOperativo = "iOS";
			}
			//Fin del ChatgGPT Moment

			/*< instancia la clase Mailer para enviar el correo electrónico de validación de correo electrónico*/
			$correo = new Mailer();

			// crea el objeto con la vista
			$tpl = new ACME("emails/loginSuccess");

			// carga la vista
			$tpl->loadTPLFromAPI();

			$vars = ["TOKEN_USER" => $user["token"], "IP_CLIENTE" => $ip, "NAVEGADOR_WEB" => $navegador, "SISTEMA_OPERATIVO" => $sistemaOperativo, "APP_URL_BASE" => $_ENV["APP_URL_BASE"]];

			/*< pasa el valor de la variable token a la vista*/
			$tpl->setVarsTPL($vars);

			/*< plantilla de email para validar cuenta*/
			$cuerpo_email = $tpl->returnTPL();

			/*< envia el correo electrónico de validación*/
			$correo->send(["destinatario" => $user["email"], "motivo" => "Cuenta Reestablecida", "contenido" => $cuerpo_email] );

			return ["errno" => 200, "error" => "Su cuenta ha sido reestablecida"];

		
		}

		function verifyToken($request) {

			$token_action = $request["token_action"];

			$ssql = "SELECT * FROM `app_estacion__usuario` WHERE `app_estacion__usuario`.`token_action` LIKE '$token_action';";

			$user = @$this->query($ssql)[0];
			
			if (!is_null($user)) {
				return ["errno" => 200, "error" => ""];
			}

			return ["errno" => 404, "error" => "El token no corresponde a un usuario"];

		}

		/**
		 * 
		 * @brief Retorna un listado limitado
		 * @param string $request_method espera a GET
		 * @param array $request [inicio][cantidad]
		 * @return array lista con los datos de los usuarios 
		 * 
		 * */
		function recovery($request){

			/*< recupera el email*/
			$email = $request->email;

			/*< evalúa si el email es válido */
		    $email_valido = preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $email);

			/*< si el mail no es valido */
			if (!$email_valido) {
				return ["error" => "Correo inválido", "errno" => 406];
			}

			$ssql = "SELECT * FROM `app_estacion__usuario` WHERE `app_estacion__usuario`.`email` LIKE '$email';";

			$user = @$this->query($ssql)[0];
			
			if (is_null($user)) {
				return ["errno" => 404, "error" => "El usuario no se encuentra registrado"];
			}

			/*< si el email es válido, se crea el token único para recuperar la contraseña*/
			$token_action = md5($_ENV['PROJECT_WEB_TOKEN'].$email.date("Y-m-d+H:i:s+"));

			$ssql = "UPDATE `app_estacion__usuario` SET `app_estacion__usuario`.`token_action` = '$token_action', `app_estacion__usuario`.`recupero` = 1, `app_estacion__usuario`.`recover_date` = CURRENT_TIMESTAMP  WHERE `app_estacion__usuario`.`email` LIKE '$email';";

			$result = $this->query($ssql);

			/*< instancia la clase Mailer para enviar el correo electrónico de validación de correo electrónico*/
			$correo = new Mailer();

			// crea el objeto con la vista
			$tpl = new ACME("emails/recovery");

			// carga la vista
			$tpl->loadTPLFromAPI();

			$vars = ["TOKEN_ACTION" => $token_action, "APP_URL_BASE" => $_ENV["APP_URL_BASE"]];

			/*< pasa el valor de las variables a la vista*/
			$tpl->setVarsTPL($vars);

			/*< plantilla de email para validar cuenta*/
			$cuerpo_email = $tpl->returnTPL();

			/*< envia el correo electrónico de validación*/
			$correo->send(["destinatario" => $email, "motivo" => "Recuperar Contraseña", "contenido" => $cuerpo_email] );

			return ["errno" => 200, "error" => "Email enviado con éxito"];
		}

	}
 ?>