<?php 

	/**
	* @file Pdf.php
	* @brief Declaraciones de la clase Pdf para la generacion de pdfs
	* @author Valdez Cristian
	* @date 2024
	* @contact contacto.innovplast@gmail.com
	*/
	include_once 'DBAbstract.php';

	// se crea la clase Pdf que hereda de DBAbstract
	class Pdf extends DBAbstract{

		/*< constructor de la clase*/
		function __construct(){
			// quiero salir de la clase actual e invocar al constructor
			parent::__construct();
		}

		/**
		 * 
		 * @brief genera un pdf de los empleados alojados en empleados.json
		 * @params array $params array asociativo [destinatario, motivo, contenido]
		 * @return 
		 * 
		 * */
		function generateList(){

			// levantamos el json y lo convertimos en una matriz
			$listado = json_decode(file_get_contents("../controllers/js/data/empleados.json",true));

			// cambiamos la zona horaria
			ini_set('date.timezone', 'America/Argentina/Buenos_Aires');

			// obtenemos la fecha y hora
			$time = date('d/m/y', time());

			// instanciamos la clase FPDF, se setea las medidas y el tamaño de la hoja
			$pdf = new FPDF("P", "mm", "Letter");

			// se añade una hoja
			$pdf->AddPage();
			// se añade una imagen
			$pdf->Image('../views/static/img/landing/logo.jpg',5,5,20,20,'jpg');
			// seteamos la fuente, su modo y el tamaño
			$pdf->SetFont("Arial", "B", 30);

			// Escribimos el pdf en una ubicación especifica "C" es la alineacion
			$pdf->Cell(200,40, "Listado de Empleados",0,1,"C");

			// Cambiamos el tamaño de la fuente
			$pdf->SetFont("Arial", "B", 14);

			// Escribimos en el pdf 
			$pdf->Cell(370,-70, 'Fecha: '.$time,0,1,"C");

			// Insertamos un salto de linea
			$pdf->Ln(63);

			// Se construye una tabla, y se escriben los encabezados, "C" especifica la alineacion
			$pdf->Cell(30,8,"Dni",1,0,"C");	
			$pdf->Cell(50,8,"Nombre",1,0,"C");
			$pdf->Cell(50,8,"Apellido",1,0,"C");
			$pdf->Cell(60,8,"Email",1,0,"C");

			//(largo, alto, contenido, borde (boolean), renglon (?), alienacion)

			// Cambiamos la fuente
			$pdf->SetFont("Arial", "", 10);

			// recorremos el la lista
			foreach ($listado as $key => $row) {
				// insertamos un salto de linea
				$pdf->Ln();
				// Se crean las celdas con los datos de un alumno
				$pdf->Cell(30,6, $row->DNI,1,0,"C");
				$pdf->Cell(50,6, $row->NOMBRE,1,0,"L");
				$pdf->Cell(50,6, $row->APELLIDO,1,0,"L");
				$pdf->Cell(60,6, $row->EMAIL,1,0,"L");

			}

			// Genero y guardo el PDF
			$pdf->Output('F', '../pdf/empleados.pdf'); 

			// Se devuelve la URL como un JSON
			return 'https://mattprofe.com.ar/alumno/6904/Innovplast/pdf/empleados.pdf';
		}



		/**
		 * 
		 * @brief genera una factura del ticket en formato pdf
		 * @params array $params array asociativo [destinatario, motivo, contenido]
		 * @return 
		 * 
		 * */
		function generateFactura($request){

			/*< recupera el method http*/
			$request_method = $_SERVER["REQUEST_METHOD"];

			/* si el method es invalido*/
			if($request_method!="POST"){
				return ["errno" => 410, "error" => "Metodo invalido"];
			}

			if(!isset($request->list)){
				return ["errno" => 404, "error" => "falta la lista de productos por POST"];
			}
			// Cell : (largo, alto, contenido, borde (boolean), renglon (?), alienacion)
			// Line : (x1, y1, x2, y2)

			$listado = $request->list;

			// levantamos el json y lo convertimos en una matriz
			//$listado = json_decode(file_get_contents("../controllers/js/data/ticket.json",true));

			// cambiamos la zona horaria
			ini_set('date.timezone', 'America/Argentina/Buenos_Aires');

			// obtenemos la fecha
			$date = date('d/m/y', time());

			// obtenemos la hora
			$hour = date('H:i:s', time());

			// instanciamos la clase FPDF, se setea las medidas y el tamaño de la hoja
			$pdf = new FPDF("P", "mm", "Letter");

			// se añade una hoja
			$pdf->AddPage();

			// se añade una imagen
			//$pdf->Image('../views/static/img/landing/logo.jpg',5,5,20,20,'jpg');

			// seteamos la fuente, su modo y el tamaño
			$pdf->SetFont("Arial", "B", 22);

			// Escribimos el pdf en una ubicación especifica "C" es la alineacion
			$pdf->Cell(200,12, "Ticket de Compra",0,1,"C");

			// Insertamos un salto de linea
			$pdf->Ln(5);

			// Cambiamos el tamaño de la fuente
			$pdf->SetFont("Arial", "", 11);

			// Escribimos en el pdf la fecha
			$pdf->Cell(20,8, 'Fecha:',0,0,"L");
			$pdf->Cell(20,8, $date,0,0,"L");

			//Insertamos una celda vacia
			$pdf->Cell(120,8, '',0,0,"C");

			// Escribimos en el pdf la hora
			$pdf->Cell(20,8, 'Hora: ',0,0,"R");
			$pdf->Cell(20,8, $hour,0,1,"L");

			// Insertamos un salto de linea
			$pdf->Ln(2);

			//Inserta una linea
			$pdf->Line(9,36,210,36);

			// Escribimos en el pdf el numero de ticket
			$pdf->Cell(32,8,"Nro Ticket:",0,0,"L");	
			$pdf->Cell(50,8, $request->chart_num,0,1,"L");

			// Escribimos en el pdf el metodo de pago
			$pdf->Cell(32,8,"Metodo de Pago:",0,0,"L");
			$pdf->Cell(50,8, $request->metodo_pago,0,1,"L");
			
			// Insertamos un salto de linea
			$pdf->Ln(4);

			//Inserta una linea
			$pdf->Line(9,54,210,54);

			// Cambiamos la fuente
			$pdf->SetFont("Arial", "B", 11);

			//Insertamos los encabezados de la tabla
			$pdf->Cell(70,6, "Producto",0,0,"L");
			$pdf->Cell(30,6, "Cantidad",0,0,"R");
			$pdf->Cell(50,6, "Precio Unitario",0,0,"R");
			$pdf->Cell(50,6, "Importe",0,1,"R");

			// Cambiamos la fuente
			$pdf->SetFont("Arial", "", 10);

			$total = 0;
			
			//var_dump($listado);

			// recorremos la lista de productos 
			foreach ($listado as $key => $row) {

				// Se consigue el importe total del producto
				$importe = $row->cantidad*$row->precio_unitario;

				// Se crean las celdas con los datos de un producto
				$pdf->Cell(70,7, $row->titulo,0,0,"L");
				$pdf->Cell(30,7, $row->cantidad.".00",0,0,"R");
				$pdf->Cell(50,7, $row->precio_unitario.".00",0,0,"R");
				$pdf->Cell(50,7, $importe.".00",0,1,"R");

				//Se va acumulando los importes para el total
				$total = $total + $importe;
			}
			// Insertamos un salto de linea
			$pdf->Ln(4);

			// Cambiamos la fuente
			$pdf->SetFont("Arial", "B", 12);

			//Inserta una linea consiguiendo y
			$pdf->Line(9,$pdf->GetY(),210,$pdf->GetY());

			// Insertamos un salto de linea
			$pdf->Ln(2);

			// Escribimos en el pdf el total del ticket
			$pdf->Cell(100,8, "TOTAL: ",0,0,"L");
			$pdf->Cell(100,8, "$".$total.".00",0,1,"R");


			$token_ticket = md5($_ENV['PROJECT_WEB_TOKEN'].date("Y-m-d+H:i:s+").mt_rand(0,5000));

			$ticket_name = 'T-'.date("Ymd").'-'.$token_ticket;

			$pdf->Output('F', '../pdf/tickets/'.$ticket_name.'.pdf'); // Esto descargará el PDF

			$url_pdf = 'https://mattprofe.com.ar/alumno/6904/Innovplast/pdf/tickets/'.$ticket_name.'.pdf';

			$id_chart = $request->chart_num - 30000;

			$ssql = "INSERT INTO `Innovplast__ticket` (`token`, `ticket`, `ID_CARRITO`, `metodo_pago`, `date_at`, `update_at`, `delete_at`) VALUES ('$token_ticket', '$url_pdf',$id_chart, 'request->metodo_pago', CURRENT_TIMESTAMP, '0000-00-00 00:00:00', '0000-00-00 00:00:00') ";

			$result = $this->query($ssql);

			$id_ticket = $this->db->insert_id;
		
			// $ssql = "SELECT `Innovplast__producto`.`ID_USUARIO` as 'id_propietario' FROM `Innovplast__producto` WHERE `Innovplast__producto`.`token` = '2c76f46cbbbb0d4338354cb5cc9715c6';";

			// $result = $this->query($ssql);

			// $id_user = $result[0]["id_propietario"];

			$id_user = 19;

			$ssql = "INSERT INTO `Innovplast__usuario_ticket` (`ID_USUARIO`, `ID_TICKET`, `date_at`, `update_at`, `delete_at`) VALUES ($id_user, $id_ticket, CURRENT_TIMESTAMP, '0000-00-00 00:00:00', '0000-00-00 00:00:00') ";

			$result = $this->query($ssql);

			// Se devuelve la URL como un JSON
			return $url_pdf;

		}


		/**
		 * 
		 * @brief genera un pdf de los empleados alojados en empleados.json
		 * @params array $params array asociativo [destinatario, motivo, contenido]
		 * @return 
		 * 
		//  * */
		function generateChart(){

			// cambiamos la zona horaria
			ini_set('date.timezone', 'America/Argentina/Buenos_Aires');

			// obtenemos la fecha y hora
			$time = date('d/m/y', time());

			// instanciamos la clase FPDF, se setea las medidas y el tamaño de la hoja
			$pdf = new FPDF("P", "mm", "Letter");

			// se añade una hoja
			$pdf->AddPage();

			// seteamos la fuente, su modo y el tamaño
			$pdf->SetFont("Arial", "B", 26);

			// Escribimos el pdf en una ubicación especifica "C" es la alineacion
			$pdf->Cell(200,40, "Graficos de Clasificacion de Tapas",0,1,"C");

			// Cambiamos el tamaño de la fuente
			$pdf->SetFont("Arial", "B", 14);

			// Escribimos en el pdf 
			$pdf->Cell(370,-70, 'Fecha: '.$time,0,1,"C");

			// Insertamos un salto de linea
			$pdf->Ln(63);

			$pdf->Image('../views/static/img/system/chart.png', 15, 60, 180); // Ajusta la posición y el tamaño según sea necesario

			$token_chart = md5($_ENV['PROJECT_WEB_TOKEN'].date("Y-m-d+H:i:s+").mt_rand(0,5000));

			$pdf->Output('F', '../pdf/charts/G-'.date("Ymd").'-'.$token_chart.'.pdf'); // Esto descargará el PDF

			// Se devuelve la URL como un JSON
			return 'https://mattprofe.com.ar/alumno/6904/Innovplast/pdf/charts/G-'.date("Ymd").'-'.$token_chart.'.pdf';
		}
	}
?>
