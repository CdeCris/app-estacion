<?php 

	/**
	* @file Tracker.php
	* @brief Declaraciones de la clase Tracker para la conexión con la base de datos.
	* @author ACME
	* @date 2024
	* @contact contacto.innovplast@gmail.com
	*/

	// incluye la libreria para conectar con la db
	include_once 'DBAbstract.php';

	/*< incluye la clase Mailer.php para enviar correo electrónico*/
	include_once 'Mailer.php';

	// se crea la clase Tracker que hereda de DBAbstract
	class Tracker extends DBAbstract{

		private $nameOfFields = array();

		/**
		 * 
		 * @brief Es el constructor de la clase Tracker
		 * 
		 * Al momento de instanciar Tracker se llama al padre para que ejecute su constructor
		 * 
		 * */
		function __construct(){

			// quiero salir de la clase actual e invocar al constructor
			parent::__construct();

			/**< Obtiene la estructura de la tabla */
			$result = $this->query('DESCRIBE `app_estacion__tracker`');

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
		 * Intenta loguear al usuario mediante email y contraseña
		 * @param array $form indexado de forma asociativa
		 * @return array que posee códigos de error especiales
		 * 
		 * */
		function trackerData(){
		
			//Inicio del ChatgGPT Moment xd
			$ip = $_SERVER['REMOTE_ADDR'] ?? 'IP no disponible';

			// Si estás detrás de un proxy, intenta obtener la IP real
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			    $ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}

			/* Consulta a la api para obtener latitud, longitud y pais con la ip*/
			$web = file_get_contents("http://ipwho.is/".$ip);

			/* Convierte el json recuperado en un objeto */
			$response = json_decode($web);

			$latitud = $response->latitude;

			$longitud = $response->longitude;

			$pais = $response->country;

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

			$token = null;

			if (isset($_SESSION['app-estacion']['user'])) {
				$token = $_SESSION['app-estacion']['user']->token;
			}

			$ssql = "INSERT INTO `app_estacion__tracker` (`token`, `ip`, `latitud`, `longitud`, `pais`, `navegador`, `sistema`, `add_date`) VALUES ('$token', '$ip', '$latitud', '$longitud', '$pais', '$navegador', '$sistemaOperativo', CURRENT_TIMESTAMP)";

			$result = $this->query($ssql);

			return ["errno" => 200];
		}


		/**
		 * 
		 * Intenta loguear al usuario mediante email y contraseña
		 * @param array $form indexado de forma asociativa
		 * @return array que posee códigos de error especiales
		 * 
		 * */
		function listClientsLocation(){

			/*< recupera el method http*/
			$request_method = $_SERVER["REQUEST_METHOD"];

			/* si el method es invalido*/
			if($request_method!="GET"){
				return ["errno" => 410, "error" => "Metodo invalido"];
			}
		
			$ssql = "SELECT `app_estacion__tracker`.`ip`, `app_estacion__tracker`.`latitud` , `app_estacion__tracker`.`longitud` , COUNT(*) AS 'accesos' FROM `app_estacion__tracker` GROUP BY `app_estacion__tracker`.`ip`, `app_estacion__tracker`.`latitud`, `app_estacion__tracker`.`longitud`;";

			$result = $this->query($ssql);

			return ["errno" => 200, "error" => "", "tracker" => $result];
		}
		
		/**
		 * 
		 * Intenta loguear al usuario mediante email y contraseña
		 * @param array $form indexado de forma asociativa
		 * @return array que posee códigos de error especiales
		 * 
		 * */
		function getClientsAndUsersCant(){

			/*< recupera el method http*/
			$request_method = $_SERVER["REQUEST_METHOD"];

			/* si el method es invalido*/
			if($request_method!="GET"){
				return ["errno" => 410, "error" => "Metodo invalido"];
			}
		
			$ssql = "SELECT COUNT(*) AS 'cant_users' FROM `app_estacion__usuario`;";
			
			$cant_users = $this->query($ssql)[0]['cant_users'];
			
			$ssql = "SELECT COUNT(DISTINCT `app_estacion__tracker`.`ip`) AS 'cant_clients' FROM `app_estacion__tracker` ;";

			$cant_clients = $this->query($ssql)[0]['cant_clients'];

			return ["errno" => 200, "error" => "", "cant_users" => $cant_users, "cant_client" => $cant_clients];
		}

		
		/**
		 * 
		 * Intenta loguear al usuario mediante email y contraseña
		 * @param array $form indexado de forma asociativa
		 * @return array que posee códigos de error especiales
		 * 
		//  * */
		// function trackerDataTroll(){
		
		// 	//Inicio del ChatgGPT Moment xd
		// 	$ip = $_SERVER['REMOTE_ADDR'] ?? 'IP no disponible';

		// 	// Si estás detrás de un proxy, intenta obtener la IP real
		// 	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		// 	    $ip = $_SERVER['HTTP_CLIENT_IP'];
		// 	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		// 	    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		// 	}

		// 	/* Consulta a la api para obtener latitud, longitud y pais con la ip*/
		// 	$web = file_get_contents("http://ipwho.is/".$ip);

		// 	/* Convierte el json recuperado en un objeto */
		// 	$response = json_decode($web);

		// 	$latitud = $response->latitude;

		// 	$longitud = $response->longitude;

		// 	$pais = $response->country;

		// 	$region = $response->region;

		// 	$city = $response->city;

		// 	$navegador = $_SERVER['HTTP_USER_AGENT'];

		// 	$sistemaOperativo = "Desconocido";

		// 	if (strpos($navegador, 'Windows') !== false) {
		// 	    $sistemaOperativo = "Windows";
		// 	} elseif (strpos($navegador, 'Mac') !== false) {
		// 	    $sistemaOperativo = "MacOS";
		// 	} elseif (strpos($navegador, 'Linux') !== false) {
		// 	    $sistemaOperativo = "Linux";
		// 	} elseif (strpos($navegador, 'Android') !== false) {
		// 	    $sistemaOperativo = "Android";
		// 	} elseif (strpos($navegador, 'iPhone') !== false) {
		// 	    $sistemaOperativo = "iOS";
		// 	}

		// 	$token = md5($ip.$latitud.$longitud.$navegador.mt_rand(0,5000));

		// 	$pais = $city.', '.$region.', '.$pais;

		// 	echo "Ahora se tu informacion: <br>";
						
		// 	echo "IP: ".$ip."<br>";

		// 	echo "Navegador: ".$navegador."<br>";

		// 	echo "Sistema Operativo: ".$sistemaOperativo." <br>";

		// 	echo "Ubicacion: ".$pais."<br>";

		// 	echo "Latitud: ".$latitud."<br>";

		// 	echo "Longitud: ".$longitud."<br>";

		// 	$ssql = "INSERT INTO `app_estacion__tracker` (`token`, `ip`, `latitud`, `longitud`, `pais`, `navegador`, `sistema`, `add_date`) VALUES ('$token', '$ip', '$latitud', '$longitud', '$pais', '$navegador', '$sistemaOperativo', CURRENT_TIMESTAMP)";

		// 	$result = $this->query($ssql);

		// 	exit();

		// 	return ["errno" => 200];
		// }
	}
 ?>