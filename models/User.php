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
			$result = $this->query('DESCRIBE `Innovplast__usuario`');

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

			$ssql = "UPDATE `Innovplast__usuario` SET delete_at='$fecha_hora' WHERE ID_USUARIO=$ID_USUARIO";

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
			$result = $this->query("CALL `Innovplast_Login`('$email')");

			// el email no existe
			if(count($result)==0){
				return ["error" => "Email no registrado", "errno" => 404];
			}

			/*< seleccionamos solo la primer fila de la matriz*/
			$result = $result[0];

			// si el email existe y la contraseña es valida
			if($result["password"]==md5($form["txt_pass"]."innovplast")){

				if(!$result["validado"]){
					return ["error" => "Aún no ha validado su email, revise su casilla de correos", "errno" => 405];
				}

				/**< autocarga de valores en los atributos de la clase */
				foreach ($this->nameOfFields as $key => $value) {
					$this->$value = $result[$value];
				}
				$this->is_admin = $result["admin"];
				// para que los avatares sean gatitos
				//$this->avatar = str_replace("set5", "set4", $this->avatar); 

				/*< carga la clase en la sesión*/
				$_SESSION["innovplast"]['user'] = $this;

				/*< usuario valido*/
				return ["error" => "", "errno" => 200, "admin" => $this->is_admin];
			}

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
			$email = $form["txt_email"];

			/*< evalúa si el email es válido */
		    $email_valido = preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $email);
		
			/*< si el mail no es valido */
			if (!$email_valido) {
				return ["error" => "Correo inválido", "errno" => 406];
			}

			/*< evalúa si la contraseña es válida */
		    $pass_valida = preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{4,}$/', $form["txt_pass"]);
		
			/*< si la contraseña no es valida */
			if (!$pass_valida) {
				return ["error" => "Verifique las requisitos necesarios para la contraseña ", "errno" => 406];
			}

			/*< si las contraseñas ingresadas no son iguales */
			if (!($form["txt_pass"] == $form["txt_pass_2"])) {
				return ["error" => "Verifique que las contraseña ingresadas sean iguales ", "errno" => 407];
			}


			/*< consulta si el email ya esta en la tabla de usuarios*/
			$result = @$this->query("SELECT * FROM `Innovplast__usuario` WHERE email = '$email'")[0];

			// el email no existe entonces se registra
			if(is_null($result)){

				/*< encripta la contraseña*/
				$pass = md5($form["txt_pass"]."innovplast");

				/*< se crea el token único para validar el correo electrónico*/
				$token_email = md5($_ENV['PROJECT_WEB_TOKEN'].$email.mt_rand(0,5000));

				$token_user = md5($_ENV['PROJECT_WEB_TOKEN'].$email);

				$avatar_default = $_ENV['APP_URL_BASE'].'/views/static/img/system/user_default.png';

				// Verifica si la IP está en un proxy
			    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {

			        $ip = $_SERVER['HTTP_CLIENT_IP'];

			    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {

			        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

			    } else {

			        $ip = $_SERVER['REMOTE_ADDR'];

			    }

				/*< agrega el nuevo usuario*/
				//$ssql = "CALL `Innovplast_Register`('$token_email','$email','$pass')";
				$ssql = "INSERT INTO `Innovplast__usuario` (`token`, `email`, `password`,`ip`, `avatar`, `date_at`, `update_at`, `delete_at`) VALUES ('$token_user', '$email', '$pass', '$ip', '$avatar_default', CURRENT_TIMESTAMP, '0000-00-00 00:00:00', '0000-00-00 00:00:00')";

				/*< ejecuta la consulta*/
				$result = $this->query($ssql);

				/*< se recupera el id del nuevo usuario*/
				$this->id_user = $this->db->insert_id;
				$id_user = $this->id_user;

				/*< agrega el token del usuario para su posterior validacion de su email*/
				$ssql = "CALL `Innovplast_SetToken`('$token_email','$id_user')";

				/*< ejecuta la consulta*/
				$result = $this->query($ssql);

				/*< setea al usuario como activo, no validado y no administrador*/
				$ssql = "CALL `Innovplast_SetEstado`('$id_user')";

				/*< ejecuta la consulta*/
				$result = $this->query($ssql);

				/*< inserta al usuario como parte de la empresa innovplast */
				$ssql = "INSERT INTO `Innovplast__empresa_usuario` (`ID_EMPRESA`, `ID_USUARIO`, `activo`, `date_at`, `update_at`, `delete_at`) VALUES (1, '$id_user',1, CURRENT_TIMESTAMP, '0000-00-00 00:00:00', '0000-00-00 00:00:00');";

				/*< ejecuta la consulta*/
				$result = $this->query($ssql);

				/*< inserta la accion en la base de datos */
				$ssql = "INSERT INTO `Innovplast__log` (`titulo`, `ID_USUARIO`, `date_at`, `update_at`, `delete_at`) VALUES ('user_register_successfully', '$id_user', CURRENT_TIMESTAMP, '0000-00-00 00:00:00', '0000-00-00 00:00:00');";

				/*< ejecuta la consulta*/
				$result = $this->query($ssql);


				/*< instancia la clase Mailer para enviar el correo electrónico de validación de correo electrónico*/
				$correo = new Mailer();

				// crea el objeto con la vista
				$tpl = new ACME("emails/register");

				// carga la vista
				$tpl->loadTPL();

				$vars = ["TOKEN_EMAIL" => $token_email];

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
			if($result['delete_at']!=$date_zero){

				/*< recupera el id del usuario que quiere volver a nuestra app*/
				$ID_USUARIO=$result["ID_USUARIO"];
				$this->ID_USUARIO = $result["ID_USUARIO"];

				/*< encripta la nueva contraseña*/
				$pass = md5($form["txt_pass"]."innovplast");

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
		 * Actualiza los datos del usuario con los datos de un formulario
		 * @param array $form es un arregle asociativo con los datos a actualizar
		 * @return array arreglo con el código de error y descripción
		 * 
		 * */
		function update($form){

			$nombre = $form["txt_first_name"];
			$apellido = $form["txt_last_name"];
			$email = $form["txt_email_user"];
			$telefono = $form["txt_tel_user"];
			$domicilio = $form["txt_dir_user"];

			$id_user = $this->ID_USUARIO;

			$this->nombre = $nombre;
			$this->apellido = $apellido;
			$this->email = $email;
			$this->telefono = $telefono;
			$this->domicilio = $domicilio;

			$ssql = "UPDATE `Innovplast__usuario` SET `Innovplast__usuario`.`nombre`='$nombre', `Innovplast__usuario`.`apellido`='$apellido', `Innovplast__usuario`.`email`='$email', `Innovplast__usuario`.`telefono`='$telefono', `Innovplast__usuario`.`domicilio`='$domicilio' WHERE `Innovplast__usuario`.`ID_USUARIO`= '$id_user';";

			$result = $this->query($ssql);

			return ["error" => "Actualizado con éxito", "errno" => 200];
		}

		/**
		 * 
		 * Cantidad de usuarios registrados
		 * @return int cantidad de usuarios registrados
		 * 
		 * */
		function getCantUsers(){

			$result = $this->query("SELECT * FROM users");

			return $this->db->affected_rows;
		}


		/**
		 * 
		 * @brief Retorna un listado limitado
		 * @param string $request_method espera a GET
		 * @param array $request [inicio][cantidad]
		 * @return array lista con los datos de los usuarios 
		 * 
		 * */
		function getAllUsers($request){

			$request_method = $_SERVER["REQUEST_METHOD"];

			/*< Es el método correcto en HTTP?*/
			if($request_method!="GET"){
				return ["errno" => 410, "error" => "Metodo invalido"];
			}

			/*< Solo un usuario logueado puede ver el listado */
			if(!isset($_SESSION["morphyx"])){
				return ["errno" => 411, "error" => "Para usar este método debe estar logueado"];
			}

			/*

			if(!isset($_SESSION["morphyx"]['user_level'])){

				if($_SESSION["morphyx"]['user_level']!='admin'){
				return ["errno" => 412, "error" => "Solo el 	administrador puede utilizar el metodo"];
				}
			}

			*/


			$inicio = 0;

			if(isset($request["inicio"])){
				$inicio = $request["inicio"];
			}

			if(!isset($request["cantidad"])){
				return ["errno" => 404, "error" => "falta cantidad por GET"];
			}

			$cantidad = $request["cantidad"];

			$result = $this->query("SELECT * FROM users LIMIT $inicio, $cantidad");

			return $result;
		}

		/**
		 * 
		 * @brief Retorna un listado limitado
		 * @param string $request_method espera a GET
		 * @param array $request [inicio][cantidad]
		 * @return array lista con los datos de los usuarios 
		 * 
		 * */
		function verify($request){

			$token = $request["TOKEN_EMAIL"];

			$ssql= "SELECT * FROM `Innovplast__token` WHERE `token_email` LIKE '$token' AND `token_activo` = 1";
			
			$result = $this->query($ssql);
			
			var_dump($result);

			if (!$result) {
				return ["errno" => 416, "error" => "Token inválido"];
			}

			$result = $this->query("UPDATE `Innovplast__estado` INNER JOIN `Innovplast__token` ON `Innovplast__estado`.`ID_USUARIO` = `Innovplast__token`.`ID_USUARIO` SET `Innovplast__estado`.validado = '1',`Innovplast__token`.token_activo = '0' WHERE `Innovplast__token`.`token_email` = '$token';");

			return ["errno" => 200, "error" => "Correo validado con éxito"];
		}

		/**
		 * 
		 * @brief Retorna un listado limitado
		 * @param string $request_method espera a GET
		 * @param array $request [inicio][cantidad]
		 * @return array lista con los datos de los usuarios 
		 * 
		 * */
		function sendRecoveryEmail($request){

			/*< recupera el email*/
			$email = $request["txt_email"];

			/*< evalúa si el email es válido */
		    $email_valido = preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $email);

			/*< si el mail no es valido */
			if (!$email_valido) {
				return ["error" => "Correo inválido", "errno" => 406];
			}

			/*< si el email es válido, se crea el token único para recuperar la contraseña*/
			$token_email = md5($_ENV['PROJECT_WEB_TOKEN'].$email.date("Y-m-d+H:i:s+"));

			$exist_user = $this->getIdUserByEmail($email);

			if ($exist_user["errno"]!=200) {
				return ["errno" => 404 ,"error" => $exist_user["error"]];
			}

			$id_user = $exist_user["id_user"];

			/*< agrega el token del usuario para su posterior validacion de su email*/
			$ssql = "CALL `Innovplast_SetToken`('$token_email','$id_user')";

			/*< ejecuta la consulta*/
			$result = $this->query($ssql);

			/*< instancia la clase Mailer para enviar el correo electrónico de validación de correo electrónico*/
			$correo = new Mailer();

			// crea el objeto con la vista
			$tpl = new ACME("emails/recoveryPassword");

			// carga la vista
			$tpl->loadTPL();

			$vars = ["TOKEN_EMAIL" => $token_email];

			/*< pasa el valor de la variable token a la vista*/
			$tpl->setVarsTPL($vars);

			/*< plantilla de email para recuperar cuenta*/
			$cuerpo_email = $tpl->returnTPL();

			/*< envia el correo electrónico de vrecuperacion*/
			$correo->send(["destinatario" => $email, "motivo" => "Recuperacion de Contraseña", "contenido" => $cuerpo_email] );

			return ["errno" => 200, "error" => "Email enviado con éxito"];
		}

		/**
		 * 
		 * @brief Retorna un listado limitado
		 * @param string $request_method espera a GET
		 * @param array $request [inicio][cantidad]
		 * @return array lista con los datos de los usuarios 
		 * 
		 * */
		function recoveryPassword($request){

			$token_email = $request["TOKEN_EMAIL"];
			$pass = md5($request["POST"]["txt_pass"]."innovplast");

			$result = $this->query("UPDATE `Innovplast__usuario` INNER JOIN `Innovplast__token` ON `Innovplast__usuario`.`ID_USUARIO` = `Innovplast__token`.`ID_USUARIO` SET `Innovplast__usuario`.password = '$pass',`Innovplast__token`.token_activo = '0' WHERE `Innovplast__token`.`token_email` = '$token_email' AND `Innovplast__token`.`token_activo` = '1';");

			return ["errno" => 200, "error" => "Contraseña modificada con éxito"];
		}

		/**
		 * 
		 * @brief Retorna un listado limitado
		 * @param string $request_method espera a GET
		 * @param array $request [inicio][cantidad]
		 * @return array lista con los datos de los usuarios 
		 * 
		 * */
		function getIdUserByEmail($email){

			$result = @$this->query("SELECT * FROM `Innovplast__usuario` WHERE `email` LIKE '$email'")[0];
			
			if (!is_null($result)) {
				return ["errno" => 200, "error" => "", "id_user" => $result["ID_USUARIO"]];
			}
			return ["errno" => 404, "error" => "No se ha encontrado ninguna cuenta vinculada al email proporcionado"];
			
		}

		/**
		 * 
		 * @brief Retorna un listado limitado
		 * @param string $request_method espera a GET
		 * @param array $request [inicio][cantidad]
		 * @return array lista con los datos de los usuarios 
		 * 
		 * */
		function setNewPass($request){

			$email = $this->email;

			$actual_pass = md5($request["pass"]."innovplast");

			$new_pass = md5($request["new_pass"]."innovplast");

			$ssql = "SELECT * FROM `Innovplast__usuario` WHERE `Innovplast__usuario`.`email` LIKE '$email' AND `Innovplast__usuario`.`password` = '$actual_pass';";

			$result = @$this->query($ssql)[0];
			
			if (!is_null($result)) {

				$ssql = "UPDATE `Innovplast__usuario` SET `password` = '$new_pass' WHERE `Innovplast__usuario`.`email` LIKE '$email';";

				$result = $this->query($ssql);

				return ["errno" => 200, "error" => "Contraseña modificada con éxito"];
			}
			return ["errno" => 404, "error" => "Ha ocurrido un error al intentar modificar la contraseña, revise las credenciales"];
			
		}


		/**
		 * 
		 * @brief Retorna un listado limitado
		 * @param string $request_method espera a GET
		 * @param array $request [inicio][cantidad]
		 * @return array lista con los datos de los usuarios 
		 * 
		 * */
		function getMyOpinions($request){

			$request_method = $_SERVER["REQUEST_METHOD"];

			/*< Es el método correcto en HTTP?*/
			if($request_method!="GET"){
				return ["errno" => 410, "error" => "Metodo invalido"];
			}

			$id_user = $_SESSION["innovplast"]["user"]->ID_USUARIO;

			$ssql = "SELECT LEFT(`Innovplast__producto_opinion`.`date_at`, 10) as 'fecha', SUBSTRING(`Innovplast__producto_opinion`.`date_at`, 12, 5) as 'hora', `Innovplast__usuario`.`nombre`, `Innovplast__usuario`.`apellido`, `Innovplast__comentario`.`contenido`, `Innovplast__calificacion`.`valor`, `Innovplast__producto`.`nombre`, `Innovplast__producto`.`imagen`, `Innovplast__producto`.`descripcion` FROM `Innovplast__producto_opinion` INNER JOIN `Innovplast__producto` ON `Innovplast__producto_opinion`.`token_producto` = `Innovplast__producto`.`token` INNER JOIN `Innovplast__comentario` ON `Innovplast__producto_opinion`.`ID_COMENTARIO` = `Innovplast__comentario`.`ID_COMENTARIO` INNER JOIN `Innovplast__calificacion` ON `Innovplast__producto_opinion`.`ID_CALIFICACION` = `Innovplast__calificacion`.`ID_CALIFICACION` INNER JOIN `Innovplast__usuario` ON `Innovplast__producto_opinion`.`ID_USUARIO` = `Innovplast__usuario`.`ID_USUARIO` WHERE `Innovplast__producto_opinion`.`ID_USUARIO` = $id_user ORDER BY `Innovplast__producto_opinion`.`ID_CALIFICACION` DESC; ";


			$result = $this->query($ssql);
			
			if (!is_null($result)) {

				return ["errno" => 200, "error" => "", "res" => $result];
			}
			return ["errno" => 404, "error" => "El usuario no ha realizado ninguna opinion"];
			
		}

		/**
		 * 
		 * @brief Retorna un listado limitado
		 * @param string $request_method espera a GET
		 * @param array $request [inicio][cantidad]
		 * @return array lista con los datos de los usuarios 
		 * 
		 * */
		function getTickets($request){

			$request_method = $_SERVER["REQUEST_METHOD"];

			/*< Es el método correcto en HTTP?*/
			if($request_method!="GET"){
				return ["errno" => 410, "error" => "Metodo invalido"];
			}

			/*< Solo un usuario logueado puede ver el listado */
			if(!$_SESSION["innovplast"]['user']->is_admin){
				return ["errno" => 411, "error" => "Acceso denegado"];
			}

			/*

			if(!isset($_SESSION["morphyx"]['user_level'])){

				if($_SESSION["morphyx"]['user_level']!='admin'){
				return ["errno" => 412, "error" => "Solo el 	administrador puede utilizar el metodo"];
				}
			}

			*/

			if(isset($request["inicio"])){
				$inicio = $request["inicio"];
			}

			if(!isset($request["cantidad"])){
				return ["errno" => 404, "error" => "falta cantidad por GET"];
			}

			$cantidad = $request["cantidad"];

			$result = $this->query("SELECT `Innovplast__usuario_ticket`.`ID_USUARIO_TICKET` as 'num_factura', `Innovplast__usuario`.`nombre` as 'nombre_cliente', `Innovplast__usuario`.`apellido` as 'apellido_cliente', `Innovplast__ticket`.`ticket` as 'url_ticket', `Innovplast__carrito`.`metodo_pago` as 'metodo_pago', `Innovplast__carrito`.`total` as 'total', LEFT(`Innovplast__usuario_ticket`.`date_at` , 10) as 'fecha', SUBSTRING(`Innovplast__usuario_ticket`.`date_at` , 12 , 5) as 'hora' FROM `Innovplast__usuario_ticket` INNER JOIN `Innovplast__ticket` ON `Innovplast__usuario_ticket`.`ID_TICKET` = `Innovplast__ticket`.`ID_TICKET` INNER JOIN `Innovplast__carrito` ON `Innovplast__ticket`.`ID_CARRITO` = `Innovplast__carrito`.`ID_CARRITO` INNER JOIN `Innovplast__usuario` ON `Innovplast__carrito`.`ID_USUARIO` = `Innovplast__usuario`.`ID_USUARIO` WHERE `Innovplast__usuario_ticket`.`ID_USUARIO` = 19 LIMIT $inicio, $cantidad");

			return $result;
		}







	}
 ?>