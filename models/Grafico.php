<?php 

	// incluye la libreria para conectar con la db
	include_once 'DBAbstract.php';

	// se crea la clase Grafico que hereda de DBAbstract
	class Grafico extends DBAbstract{
		/**
		 * 
		 * Al momento de instanciar Grafico se llama al padre para que ejecute su constructor
		 * 
		 * */
		function __construct(){
			// quiero salir de la clase actual e invocar al constructor
			parent::__construct();
		}

		/**
		 * 
		 * Consigue los colores, la cantidad y el codigo rgb de la base de datos, ordenados descendentemente por cantidad de stock
		 *
		 * @return array es un arreglo, con arreglos assoc 
		 * */
		function getData(){
		    $sql = "SELECT `Innovplast__color`.color,`Innovplast__tapa`.stock,`Innovplast__color`.`valor_rgb` FROM `Innovplast__tapa` INNER JOIN `Innovplast__color` ON `Innovplast__tapa`.ID_COLOR = `Innovplast__color`.ID_COLOR ORDER BY `Innovplast__tapa`.stock DESC;";
			$result = $this->query($sql);
			return $result;
		}

		/**
		 * 
		 * Consigue los colores, la cantidad y el codigo rgb de la base de datos, ordenados descendentemente por cantidad de stock
		 *
		 * @return array es un arreglo, con arreglos assoc 
		 * */
		function getProfits(){

			if (!$_SESSION['innovplast']['user']->is_admin) {
				return ["errno" => 404, "error" => "Permisos insuficientes para usar este metodo"];
			}

			$id_user = $_SESSION['innovplast']['user']->ID_USUARIO;

			$ssql = "SELECT (`Innovplast__producto_carrito`.`cantidad` * `Innovplast__producto`.`precio_unitario`) AS `totalXdiaProds`, (`Innovplast__producto_carrito`.`cantidad` * `Innovplast__tapa`.`precio_unitario`) AS `totalXdiaTapa`, LEFT(`Innovplast__producto_carrito`.`date_at`, 10) AS `fecha` FROM `Innovplast__producto_carrito` LEFT JOIN `Innovplast__producto` ON `Innovplast__producto_carrito`.`token_prod` = `Innovplast__producto`.`token` LEFT JOIN `Innovplast__tapa` ON `Innovplast__producto_carrito`.`token_prod` = `Innovplast__tapa`.`token` INNER JOIN `Innovplast__usuario` ON `Innovplast__usuario`.`ID_USUARIO` = $id_user INNER JOIN `Innovplast__carrito` ON `Innovplast__carrito`.`activo` = 0 GROUP BY `fecha`;";

			$result = $this->query($ssql);

			return $result;
		}

		/**
		 * 
		 * Consigue los colores, la cantidad y el codigo rgb de la base de datos, ordenados descendentemente por cantidad de stock
		 *
		 * @return array es un arreglo, con arreglos assoc 
		 * */
		function saveFile() {
		    if (isset($_FILES['chart'])) {

		        $uploads_dir = '../views/static/img/system/'; // Ajusta la ruta según sea necesario

		        $tmp_name = $_FILES['chart']['tmp_name'];
		        
		        $name = basename($_FILES['chart']['name']);

		        // Verifica si el directorio existe
		        if (!is_dir($uploads_dir)) {
		            return ['status' => 'error', 'message' => 'Directorio no encontrado.'];
		        }

		        // Verifica el tamaño del archivo
		        if ($_FILES['chart']['size'] == 0) {
		            return ['status' => 'error', 'message' => 'El archivo está vacío.'];
		        }

		        // Mueve el archivo
		        if (move_uploaded_file($tmp_name, "$uploads_dir/$name")) {
		            return ['status' => 'success', 'path' => "$uploads_dir/$name"];
		        } else {
		            return ['status' => 'error', 'message' => 'Error al mover el archivo.'];
		        }
		    } else {
		        return ['status' => 'error', 'message' => 'No se ha recibido el archivo.'];
		    }
		}

		

	}

?>
		