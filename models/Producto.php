<?php 

	// incluye la libreria para conectar con la db
	include_once 'DBAbstract.php';

	// se crea la clase Producto que hereda de DBAbstract
	class Producto extends DBAbstract{

		private $nameOfFields = array();

		/**
		 * 
		 * Al momento de instanciar Producto se llama al padre para que ejecute su constructor
		 * 
		 * */
		function __construct(){
			// quiero salir de la clase actual e invocar al constructor
			parent::__construct();

			/**< Obtiene la estructura de la tabla */
			$result = $this->query('DESCRIBE `Innovplast__producto`');

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
		 * Consigue los colores, la cantidad y el codigo rgb de la base de datos, ordenados descendentemente por cantidad de stock
		 *
		 * @return array es un arreglo, con arreglos assoc 
		 * */
		function getTapas($request){

			/*< recupera el method http*/
			$request_method = $_SERVER["REQUEST_METHOD"];

			/* si el method es invalido*/
			if($request_method!="GET"){
				return ["errno" => 410, "error" => "Metodo invalido"];
			}

			$inicio = 0;

			if(isset($request["inicio"])){
				$inicio = $request["inicio"];
			}

			if(!isset($request["cantidad"])){
				return ["errno" => 404, "error" => "falta cantidad por GET"];
			}

			$cantidad = $request["cantidad"];

		    $sql = "SELECT `Innovplast__tapa`.`token`, `Innovplast__tapa`.`titulo` as 'descripcion', `Innovplast__tapa`.`precio_unitario`, LOWER(`Innovplast__color`.`color`) as 'color', `Innovplast__tapa`.`stock` FROM `Innovplast__tapa` INNER JOIN `Innovplast__color` ON `Innovplast__tapa`.ID_COLOR = `Innovplast__color`.ID_COLOR WHERE `Innovplast__tapa`.`stock` > 0 ORDER BY `Innovplast__tapa`.ID_TAPA LIMIT $inicio, $cantidad;";
			$result = $this->query($sql);
			return $result;
		}

		/**
		 * 
		 * Consigue los colores, la cantidad y el codigo rgb de la base de datos, ordenados descendentemente por cantidad de stock
		 *
		 * @return array es un arreglo, con arreglos assoc 
		 * */
		function getProducts($request){

			/*< recupera el method http*/
			$request_method = $_SERVER["REQUEST_METHOD"];

			/* si el method es invalido*/
			if($request_method!="GET"){
				return ["errno" => 410, "error" => "Metodo invalido"];
			}

			$inicio = 0;

			if(isset($request["inicio"])){
				$inicio = $request["inicio"];
			}

			if(!isset($request["cantidad"])){
				return ["errno" => 404, "error" => "falta cantidad por GET"];
			}

			$cantidad = $request["cantidad"];

		    $sql = "SELECT `Innovplast__producto`.`token`, `Innovplast__producto`.`nombre` AS 'titulo', `Innovplast__producto`.`precio_unitario`, `Innovplast__producto`.`imagen`, `Innovplast__producto`.`descripcion` , `Innovplast__producto`.`stock` FROM `Innovplast__producto` WHERE `Innovplast__producto`.`stock` > 0 ORDER BY `Innovplast__producto`.ID_PRODUCTO LIMIT $inicio, $cantidad;";
			$result = $this->query($sql);
			//var_dump($result);
			return $result;
		}

		function getMyProducts($request){

			/*< recupera el method http*/
			$request_method = $_SERVER["REQUEST_METHOD"];

			/* si no es admin*/
			if (!$_SESSION["innovplast"]["user"]->is_admin) {
				return ["errno" => 418, "error" => "Acceso denegado"];
			}

			/* si el method es invalido*/
			if($request_method!="GET"){
				return ["errno" => 410, "error" => "Metodo invalido"];
			}

			$inicio = 0;

			if(isset($request["inicio"])){
				$inicio = $request["inicio"];
			}

			if(!isset($request["cantidad"])){
				return ["errno" => 404, "error" => "falta cantidad por GET"];
			}

			$cantidad = $request["cantidad"];

			$id_user = $_SESSION["innovplast"]["user"]->ID_USUARIO;

		    $sql = "SELECT `Innovplast__producto`.`token`, `Innovplast__producto`.`nombre` AS 'titulo', `Innovplast__producto`.`precio_unitario`, `Innovplast__producto`.`imagen`, `Innovplast__producto`.`descripcion` AS 'color', `Innovplast__producto`.`stock`, `Innovplast__producto`.`cant_vista`, `Innovplast__producto`.`cant_vendido`, `Innovplast__producto`.`activo`, LEFT(date_at, 10) AS 'fecha_publicacion' FROM `Innovplast__producto` WHERE `Innovplast__producto`.`ID_USUARIO` = $id_user ORDER BY `Innovplast__producto`.ID_PRODUCTO LIMIT $inicio, $cantidad;";
			$result = $this->query($sql);
			//var_dump($result);
			return $result;
		}
		/**
		 * CARRITO
		 * Agrega a la base de datos el producto con los datos recibidos del formulario
		 *
		 * @param array $form un arreglo con los datos del producto
		 * @return array arreglo con el código de error y descripción
		 * */

		function addProduct($request){

			//ADD PRODUCT TO CHART

			/*< recupera el method http*/
			$request_method = $_SERVER["REQUEST_METHOD"];

			/* si el method es invalido*/
			if($request_method!="POST"){
				return ["errno" => 410, "error" => "Metodo invalido"];
			}

			if(!isset($request->prod)){
				return ["errno" => 404, "error" => "falta el token del producto por POST"];
			}

			if(!isset($request->cant)){
				return ["errno" => 404, "error" => "falta la cantidad por POST"];
			}

			$token_prod=$request->prod;
			$cant_prod=intval($request->cant);
			$id_user = $_SESSION["innovplast"]["user"]->ID_USUARIO;

			$sql = "SELECT `Innovplast__carrito`.`ID_CARRITO` AS 'id_chart' FROM `Innovplast__carrito` WHERE `Innovplast__carrito`.`ID_USUARIO` = '$id_user' AND `Innovplast__carrito`.`activo`= 1 ;";

			$result = @$this->query($sql)[0];

			if (is_null($result)) {

				$token_chart = md5($_ENV['PROJECT_WEB_TOKEN'].date("Y-m-d+H:i:s+").mt_rand(0, 5000));

				$sql = "INSERT INTO `Innovplast__carrito` (`token`,`activo`, `ID_USUARIO`, `date_at`, `update_at`, `delete_at`) VALUES ('$token_chart',1, $id_user, CURRENT_TIMESTAMP, '0000-00-00 00:00:00', '0000-00-00 00:00:00');";

				$result = $this->query($sql);
				
				/*< se recupera el id del nuevo usuario*/
				$id_chart = $this->db->insert_id;

			}else{
				$id_chart = intval($result["id_chart"]);
			}

			$token_compra = md5($_ENV['PROJECT_WEB_TOKEN'].date("Y-m-d+H:i:s+").mt_rand(0, 5000));


			$sql = "INSERT INTO `Innovplast__producto_carrito` (`token_compra`, `cantidad` , `ID_CARRITO`, `token_prod`) VALUES ('$token_compra', $cant_prod, $id_chart, '$token_prod');";

			$result = $this->query($sql);

			return ["error" => "Se añadió correctamente", "errno" => 200];
		}

		/**
		 * 
		 * Agrega a la base de datos el producto con los datos recibidos del formulario
		 *
		 * @param array $form un arreglo con los datos del producto
		 * @return array arreglo con el código de error y descripción
		 * */
		function addNewProduct($request){

			/*< recupera el method http*/
			$request_method = $_SERVER["REQUEST_METHOD"];

			/* si el method es invalido*/
			if($request_method!="POST"){
				return ["errno" => 410, "error" => "Metodo invalido"];
			}

			if(!isset($request->name)){
				return ["errno" => 404, "error" => "falta el nombre del producto por POST"];
			}

			if(!isset($request->desc)){
				return ["errno" => 404, "error" => "falta la descripcion por POST"];
			}
			if(!isset($request->unit_price)){
				return ["errno" => 404, "error" => "falta el precio unitario del producto por POST"];
			}

			if(!isset($request->stock)){
				return ["errno" => 404, "error" => "falta el stock por POST"];
			}



			$prod_name = $request->name;
			$prod_desc = $request->desc;
			$prod_unit_price = $request->unit_price;
			$prod_stock = $request->stock;
			$prod_image = $_ENV["APP_URL_UPLOAD_IMAGE"].$request->url_image;

			//$prod_image = "pepe";

			$token_prod = md5($_ENV['PROJECT_WEB_TOKEN'].date("Y-m-d+H:i:s+").mt_rand(0, 5000));

			$id_user = $_SESSION["innovplast"]["user"]->ID_USUARIO;

			$sql = "INSERT INTO `Innovplast__producto` (`token`, `nombre`, `descripcion`, `precio_unitario`, `stock`, `imagen`, `cant_vista`, `cant_vendido`, `activo`, `ID_USUARIO`, `date_at`, `update_at`, `delete_at`) VALUES ('$token_prod', '$prod_name', '$prod_desc', $prod_unit_price, $prod_stock, '$prod_image', 0, 0, 1, '$id_user', CURRENT_TIMESTAMP, '0000-00-00 00:00:00', '0000-00-00 00:00:00');";

			$result = $this->query($sql);
	
			/*< se recupera el id del nuevo usuario*/
			$id_prod = $this->db->insert_id;

			/*< inserta al producto como parte de la empresa innovplast */
			$ssql = "INSERT INTO `Innovplast__empresa_producto` (`ID_EMPRESA`, `ID_PRODUCTO`, `activo`, `date_at`, `update_at`, `delete_at`) VALUES (1, '$id_prod',1, CURRENT_TIMESTAMP, '0000-00-00 00:00:00', '0000-00-00 00:00:00');";

			/*< ejecuta la consulta*/
			$result = $this->query($ssql);

			return ["error" => "Se añadió correctamente", "errno" => 200];

		}

		function getChartProducts(){

			/*< recupera el method http*/
			$request_method = $_SERVER["REQUEST_METHOD"];

			/* si el method es invalido*/
			if($request_method!="GET"){
				return ["errno" => 410, "error" => "Metodo invalido"];
			}

			// if(isset($request["inicio"])){
			// 	$inicio = $request["inicio"];
			// }

			// $inicio = 0;

			// if(isset($request["inicio"])){
			// 	$inicio = $request["inicio"];
			// }

			// if(!isset($request["cantidad"])){
			// 	return ["errno" => 404, "error" => "falta cantidad por GET"];
			// }

			// $cantidad = $request["cantidad"];

			$id_user = $_SESSION["innovplast"]["user"]->ID_USUARIO;

			$sql = "SELECT `Innovplast__carrito`.`ID_CARRITO` AS 'id_chart' FROM `Innovplast__carrito` WHERE `Innovplast__carrito`.`ID_USUARIO` = '$id_user' AND `Innovplast__carrito`.`activo`= 1 ;";

			$result = @$this->query($sql)[0];

			if (is_null($result)) {

				$token_chart = md5($_ENV['PROJECT_WEB_TOKEN'].date("Y-m-d+H:i:s+").mt_rand(0, 5000));

				$sql = "INSERT INTO `Innovplast__carrito` (`token`,`activo`, `ID_USUARIO`, `date_at`, `update_at`, `delete_at`) VALUES ('$token_chart',1, $id_user, CURRENT_TIMESTAMP, '0000-00-00 00:00:00', '0000-00-00 00:00:00');";

				$result = $this->query($sql);
				
				/*< se recupera el id del nuevo carrito*/
				$id_chart = $this->db->insert_id;

			}else{
				$id_chart = intval($result["id_chart"]);
			}

			$sql = "SELECT `Innovplast__producto`.`token`, `Innovplast__producto_carrito`.`ID_PRODUCTO_CARRITO` AS 'id_chart_prod', `Innovplast__producto`.`nombre` AS 'titulo', `Innovplast__producto`.`precio_unitario`, `Innovplast__producto`.`imagen`, `Innovplast__producto_carrito`.`cantidad`, `Innovplast__producto`.`descripcion` AS 'color', `Innovplast__producto`.`precio_unitario` * `Innovplast__producto_carrito`.`cantidad` AS 'SubTotal' FROM `Innovplast__producto` INNER JOIN `Innovplast__producto_carrito` ON `Innovplast__producto_carrito`.`ID_CARRITO` = $id_chart WHERE `Innovplast__producto`.`token` = `Innovplast__producto_carrito`.`token_prod` UNION ALL SELECT `Innovplast__tapa`.`token`, `Innovplast__producto_carrito`.`ID_PRODUCTO_CARRITO` AS 'id_chart_prod', `Innovplast__tapa`.`titulo`, `Innovplast__tapa`.`precio_unitario`, `Innovplast__tapa`.`imagen`, `Innovplast__producto_carrito`.`cantidad`, `Innovplast__tapa`.`descripcion` AS 'color', `Innovplast__tapa`.`precio_unitario` * `Innovplast__producto_carrito`.`cantidad` AS 'SubTotal' FROM `Innovplast__tapa` INNER JOIN `Innovplast__producto_carrito` ON `Innovplast__producto_carrito`.`ID_CARRITO` = $id_chart WHERE `Innovplast__tapa`.`token` = `Innovplast__producto_carrito`.`token_prod` AND `Innovplast__tapa`.`token` NOT IN (SELECT `Innovplast__producto`.`token` FROM `Innovplast__producto` INNER JOIN `Innovplast__producto_carrito` ON `Innovplast__producto_carrito`.`ID_CARRITO` = $id_chart WHERE `Innovplast__producto`.`token` = `Innovplast__producto_carrito`.`token_prod`) ORDER BY `id_chart_prod` DESC ;";
				//LIMIT $inicio, $cantidad; Para la paginacion del carrito

			$result = $this->query($sql);

			return $result;
			
		}

		/**
		 * 
		 * Agrega a la base de datos el producto con los datos recibidos del formulario
		 *
		 * @param array $form un arreglo con los datos del producto
		 * @return array arreglo con el código de error y descripción
		 * */
		function removeProductChart($request){

			$id_chart = $request["id_chart"];

			$id_user = $_SESSION["innovplast"]["user"]->ID_USUARIO;

			$sql = "DELETE pc FROM `Innovplast__producto_carrito` AS pc INNER JOIN `Innovplast__carrito` AS c ON c.`ID_CARRITO` = pc.`ID_CARRITO` WHERE pc.`ID_PRODUCTO_CARRITO` = $id_chart AND c.`ID_USUARIO` = $id_user;";

			$result = $this->query($sql);

			return ["error" => "Se elimino correctamente", "errno" => 200];
		}


		/**
		 * Agrega a la base de datos el producto con los datos recibidos del formulario
		 *
		 * @param array $form un arreglo con los datos del producto
		 * @return array arreglo con el código de error y descripción
		 * */

		function buyChart($request){

			/*< recupera el method http*/
			$request_method = $_SERVER["REQUEST_METHOD"];

			/* si el method es invalido*/
			if($request_method!="PUT"){
				return ["errno" => 410, "error" => "Metodo invalido"];
			}

			/* si el request esta vacio*/
			if(!count($request->list)){
				return ["errno" => 404, "error" => "falta los productos por PUT"];
			}

			$metodo_de_pago = $request->metodo_pago;

			$id_user = $_SESSION["innovplast"]["user"]->ID_USUARIO;

			$token_user = $_SESSION["innovplast"]["user"]->token;

			$total = 0;

			foreach ($request->list as $key => $prod) {

				$ssql = "SELECT * FROM `Innovplast__tapa` AS t WHERE t.`token` LIKE '$prod->token';";

				$result = @$this->query($ssql)[0];

				if (!is_null($result)) {

					$ssql = "UPDATE `Innovplast__tapa` AS t SET t.`stock` = t.`stock` - $prod->cantidad WHERE t.`token` = '$prod->token';";

					$result = @$this->query($ssql)
					;
				}else{
					$ssql = "UPDATE `Innovplast__producto` AS p SET p.`stock` = p.`stock` - $prod->cantidad WHERE p.`token` = '$prod->token';";

					$result = @$this->query($ssql);

				}

				$total = $total + ($prod->precio_unitario * $prod->cantidad);

			}

			$ssql = "SELECT `Innovplast__carrito`.`ID_CARRITO`, `Innovplast__carrito`.`token` FROM `Innovplast__carrito` WHERE `activo` = 1 AND `ID_USUARIO` = $id_user";

			$request = @$this->query($ssql)[0];

			$chart_info = ["chart_num" => intval($request["ID_CARRITO"])+30000, "token_chart" => $request["token"]];

			$ssql = "UPDATE `Innovplast__carrito` AS c SET c.`activo` = 0 , c.`total` = $total , c.`metodo_pago` = '$metodo_de_pago' WHERE c.`ID_USUARIO` = '$id_user' AND c.`activo` = 1 ;";

			$result = @$this->query($ssql);

			return ["errno" => 200, "error" => "Carrito comprado exitosamente", "data" => $chart_info];
		}
		/**
		 * 
		 * Consigue los colores, la cantidad y el codigo rgb de la base de datos, ordenados descendentemente por cantidad de stock
		 *
		 * @return array es un arreglo, con arreglos assoc 
		 * */
		function saveFile() {
		    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {

		            $uploads_dir = '../views/static/img/client/'; // Directorio donde se guardarán las imágenes

		            $tmp_name = $_FILES['image']['tmp_name'];

		            $original_name = basename($_FILES['image']['name']); // Obtener el nombre original

		            $file_extension = ".".pathinfo($original_name, PATHINFO_EXTENSION);

		            $name_img = str_replace($file_extension, '', $original_name);
		            			
		            $token = md5($_SESSION["innovplast"]["user"]->token.date("Ymd:His").mt_rand(0,5000));

		            // Crear un nuevo nombre de archivo
		            $name = "I-". $name_img ."-". date("Ymd") . "-" . $token . $file_extension; // Asegúrate de incluir el punto

		            $upload_file = "$uploads_dir/$name";

			        if (move_uploaded_file($tmp_name, $upload_file)) {
			            return ["errno" => 200 , "error" => "Imagen subida con éxito", "name" => $name] ;
			        } else {
			            return ["errno" => 408 , "error" => "Error al subir la imagen."] ;
			        }
			    } else {
			        return ["errno" => 404 , "error" => "No se recibió ningún archivo o hubo un error en la carga."] ;
			    }
			} else {
			    return ["errno" => 406 , "error" => "Método no permitido."] ;
			}
		}


		/**
		 * 
		 * Consigue los colores, la cantidad y el codigo rgb de la base de datos, ordenados descendentemente por cantidad de stock
		 *
		 * @return array es un arreglo, con arreglos assoc 
		 * */
		function getPurchaseProducts($request){

			/*< recupera el method http*/
			$request_method = $_SERVER["REQUEST_METHOD"];

			/* si el method es invalido*/
			if($request_method!="GET"){
				return ["errno" => 410, "error" => "Metodo invalido"];
			}

			$inicio = 0;

			if(isset($request["inicio"])){
				$inicio = $request["inicio"];
			}

			if(!isset($request["cantidad"])){
				return ["errno" => 404, "error" => "falta cantidad por GET"];
			}

			$cantidad = $request["cantidad"];

			$id_user = $_SESSION["innovplast"]["user"]->ID_USUARIO;

		    $sql = "SELECT LEFT(`Innovplast__carrito`.`update_at`, 10) AS 'date', `Innovplast__producto_carrito`.`token_compra` ,`Innovplast__producto_carrito`.`cantidad`,`Innovplast__producto`.`token` AS 'token_prod', `Innovplast__producto`.`nombre` AS 'name_prod', `Innovplast__producto`.`descripcion` AS 'desc_prod', `Innovplast__producto`.`imagen` AS 'image_prod', `Innovplast__producto_carrito`.`cantidad` * `Innovplast__producto`.`precio_unitario` AS 'subtotal_prod', `Innovplast__tapa`.`token` AS 'token_tapa', `Innovplast__tapa`.`titulo` AS 'name_tapa', `Innovplast__tapa`.`descripcion` AS 'desc_tapa', `Innovplast__tapa`.`imagen` AS 'image_tapa', `Innovplast__producto_carrito`.`cantidad` * `Innovplast__tapa`.`precio_unitario` AS 'subtotal_tapa' FROM `Innovplast__producto_carrito` LEFT JOIN `Innovplast__producto` ON `Innovplast__producto_carrito`.`token_prod` = `Innovplast__producto`.`token` LEFT JOIN `Innovplast__tapa` ON `Innovplast__producto_carrito`.`token_prod` = `Innovplast__tapa`.`token` INNER JOIN `Innovplast__carrito` ON `Innovplast__producto_carrito`.`ID_CARRITO` = `Innovplast__carrito`.`ID_CARRITO` WHERE `Innovplast__carrito`.`ID_USUARIO` = '$id_user' AND `Innovplast__carrito`.`activo` = '0' ORDER BY `Innovplast__carrito`.`date_at` DESC LIMIT $inicio, $cantidad;
";
			$result = $this->query($sql);

			return $result;
		}
		
		/**
		 * 
		 * Agrega a la base de datos el producto con los datos recibidos del formulario
		 *
		 * @param array $form un arreglo con los datos del producto
		 * @return array arreglo con el código de error y descripción
		 * */
		function addCommentAndCalification($request){

			/*< recupera el method http*/
			$request_method = $_SERVER["REQUEST_METHOD"];

			/* si el method es invalido*/
			if($request_method!="POST"){
				return ["errno" => 410, "error" => "Metodo invalido"];
			}

			if(!isset($request->token_prod)){
				return ["errno" => 404, "error" => "falta el token del producto por POST"];
			}

			if(!isset($request->comment)){
				return ["errno" => 404, "error" => "falta el comentario del producto por POST"];
			}

			if(!isset($request->calification)){
				return ["errno" => 404, "error" => "falta la calificacion por POST"];
			}

			$token_prod = $request->token_prod;

			$id_user = $_SESSION["innovplast"]["user"]->ID_USUARIO;

			$prod_comment = $request->comment;

			$prod_calification = $request->calification;

			$token_comment = md5($_ENV['PROJECT_WEB_TOKEN'].date("Y-m-d+H:i:s+").mt_rand(0, 5000));

			$ssql = "INSERT INTO `Innovplast__comentario` (`token`, `contenido`, `date_at`, `update_at`, `delete_at`) VALUES ('$token_comment', '$prod_comment', CURRENT_TIMESTAMP, '0000-00-00 00:00:00', '0000-00-00 00:00:00');";

			$result = $this->query($ssql);

			/*< se recupera el id del nuevo carrito*/
			$id_comment = $this->db->insert_id;

			/*< inserta al producto como parte de la empresa innovplast */
			$ssql = "INSERT INTO `Innovplast__calificacion` (`valor`, `date_at`, `update_at`, `delete_at`) VALUES ('$prod_calification', CURRENT_TIMESTAMP, '0000-00-00 00:00:00', '0000-00-00 00:00:00');";

			/*< ejecuta la consulta*/
			$result = $this->query($ssql);

			/*< se recupera el id del nuevo carrito*/
			$id_calification = $this->db->insert_id;


			$ssql = "INSERT INTO `Innovplast__producto_opinion` (`token_producto`, `ID_USUARIO`, `ID_CALIFICACION`, `ID_COMENTARIO`, `date_at`, `update_at`, `delete_at`) VALUES ('$token_prod', '$id_user', '$id_calification', '$id_comment', CURRENT_TIMESTAMP, '0000-00-00 00:00:00', '0000-00-00 00:00:00'); ";

			/*< ejecuta la consulta*/
			$result = $this->query($ssql);

			return ["error" => "Se añadió el comentario y la calificacion correctamente", "errno" => 200];
		}

		/**
		 * Agrega a la base de datos el producto con los datos recibidos del formulario
		 *
		 * @param array $form un arreglo con los datos del producto
		 * @return array arreglo con el código de error y descripción
		 * */
		function getDetails($request){
			
			/*< recupera el method http*/
			$request_method = $_SERVER["REQUEST_METHOD"];
			
			/* si el method es invalido*/
			if($request_method!="GET"){
				return ["errno" => 410, "error" => "Metodo invalido"];
			}

			if(!isset($request["prod"])){
				return ["errno" => 404, "error" => "falta el token del producto por POST"];
			}

			$token_prod = $request["prod"];

			$ssql = "SELECT * FROM `Innovplast__tapa` AS t WHERE t.`token` LIKE '$token_prod';";

			$prod_info = @$this->query($ssql)[0];

			if (is_null($prod_info)) {

				$ssql = "SELECT * FROM `Innovplast__producto` AS t WHERE t.`token` LIKE '$token_prod';";

				$prod_info = $this->query($ssql)[0];
				
			}

			$ssql = "SELECT `Innovplast__usuario`.`nombre` , `Innovplast__calificacion`.`valor`, `Innovplast__comentario`.`contenido` FROM `Innovplast__producto_opinion` INNER JOIN `Innovplast__calificacion` ON `Innovplast__producto_opinion`.`ID_CALIFICACION` = `Innovplast__calificacion`.`ID_CALIFICACION` INNER JOIN `Innovplast__comentario` ON `Innovplast__producto_opinion`.`ID_COMENTARIO` = `Innovplast__comentario`.`ID_COMENTARIO` INNER JOIN `Innovplast__usuario` ON `Innovplast__producto_opinion`.`ID_USUARIO` = `Innovplast__usuario`.`ID_USUARIO` WHERE `Innovplast__producto_opinion`.`token_producto` = '$token_prod'; ";

			$prod_opinion = $this->query($ssql);

			return ["product_info" => $prod_info, "product_opinion" => $prod_opinion];

		}

		/**
		 * Agrega a la base de datos el producto con los datos recibidos del formulario
		 *
		 * @param array $form un arreglo con los datos del producto
		 * @return array arreglo con el código de error y descripción
		 * */
		function getPurchaseDetails($request){
			
			// var_dump($request);
			// exit();
			/*< recupera el method http*/
			$request_method = $_SERVER["REQUEST_METHOD"];
			
			/* si el method es invalido*/
			if($request_method!="GET"){
				return ["errno" => 410, "error" => "Metodo invalido"];
			}

			if(!isset($request["prod"])){
				return ["errno" => 404, "error" => "falta el token del producto por POST"];
			}

			$id_user = $_SESSION["innovplast"]["user"]->ID_USUARIO;

			$token_compra = $request["prod"];

			$ssql = "SELECT (`Innovplast__carrito`.`ID_CARRITO` + 30000) AS 'chart_num', `Innovplast__producto_carrito`.`ID_PRODUCTO_CARRITO` AS 'id_chart_prod', `Innovplast__producto_carrito`.`token_compra`, `Innovplast__producto_carrito`.`cantidad`, `Innovplast__producto`.`precio_unitario`, `Innovplast__carrito`.`metodo_pago`, `Innovplast__producto_carrito`.`token_prod`, `Innovplast__producto`.`nombre` AS 'titulo', `Innovplast__producto`.`imagen`, `Innovplast__producto`.`descripcion`, `Innovplast__calificacion`.`valor`, `Innovplast__comentario`.`contenido`, LEFT(`Innovplast__carrito`.`update_at`, 10) AS 'fecha_comprado' FROM `Innovplast__producto_carrito` INNER JOIN `Innovplast__producto` ON `Innovplast__producto_carrito`.`token_prod` = `Innovplast__producto`.`token` INNER JOIN `Innovplast__carrito` ON `Innovplast__carrito`.`ID_CARRITO` = `Innovplast__producto_carrito`.`ID_CARRITO` LEFT JOIN `Innovplast__producto_opinion` ON `Innovplast__producto_carrito`.`token_prod` = `Innovplast__producto_opinion`.`token_producto` AND `Innovplast__producto_opinion`.`ID_USUARIO` = '$id_user' LEFT JOIN `Innovplast__calificacion` ON `Innovplast__calificacion`.`ID_CALIFICACION` = `Innovplast__producto_opinion`.`ID_CALIFICACION` LEFT JOIN `Innovplast__comentario` ON `Innovplast__comentario`.`ID_COMENTARIO` = `Innovplast__producto_opinion`.`ID_COMENTARIO` WHERE `Innovplast__producto_carrito`.`token_compra` = '$token_compra';";

			$result = $this->query($ssql);

			return $result;

		}


		function editMyProduct($request){

			/*< recupera el method http*/
			$request_method = $_SERVER["REQUEST_METHOD"];

			/* si no es admin*/
			if (!$_SESSION["innovplast"]["user"]->is_admin) {
				return ["errno" => 418, "error" => "Acceso denegado"];
			}

			/* si el method es invalido*/
			if($request_method!="PUT"){
				return ["errno" => 410, "error" => "Metodo invalido"];
			}


			$token = $request->token;
			$name = $request->name;
			$desc = $request->desc;
			$unit_price = $request->unit_price;
			$stock = $request->stock;

			$id_user = $_SESSION["innovplast"]["user"]->ID_USUARIO;

			$ssql = "UPDATE `Innovplast__producto` AS c SET c.`nombre` = '$name' , c.`descripcion` = '$desc' , c.`precio_unitario` = $unit_price, c.`stock` = $stock WHERE c.`ID_USUARIO` = '$id_user' AND c.token = '$token' ;";

			$result = $this->query($ssql);

			return ["errno" => 200, "error" => "Producto modificado exitosamente"];
		}


		function deleteMyProd($request){

			/*< recupera el method http*/
			$request_method = $_SERVER["REQUEST_METHOD"];

			/* si no es admin*/
			if (!$_SESSION["innovplast"]["user"]->is_admin) {
				return ["errno" => 418, "error" => "Acceso denegado"];
			}

			/* si el method es invalido*/
			if($request_method!="DELETE"){
				return ["errno" => 410, "error" => "Metodo invalido"];
			}

			$token = $request->token;

			$ssql = "DELETE * FROM `Innovplast__producto` WHERE `Innovplast__producto`.`token` = '$token';";

			$result = $this->query($ssql);

			return ["errno" => 200, "error" => "Producto eliminado exitosamente"];
		}
	}

 ?>