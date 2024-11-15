<?php 


	/**
	 * 
	 * Class Acme Motor de plantillas
	 * 
	 * */

	class Acme{

		/*< almacena la vista*/
		public $buffer;
		private $name_tpl;

		/**
		 * 
		 * Se ejecuta al instanciar el objeto
		 * 
		 * @param string $name_tpl nombre de la vista
		 * 
		 * */
		function __construct($name_tpl){

			$this->name_tpl = $name_tpl;
		}


		/**
		 * 
		 * Carga la vista dentro de buffer
		 * 
		 * @return bool existe|no existe la vista
		 * 
		 * */
		function loadTPL(){

			/*< comprueba que exista la vista*/
			if(!file_exists("views/".$this->name_tpl."View.html")){
				echo "No existe la vista <b>".$this->name_tpl."</b>";
				exit();
			}

			/*< carga la vista en el buffer*/
			$this->buffer = file_get_contents("views/".$this->name_tpl."View.html");

			/*< carga los extends que haya dentro de la vista*/
			$this->loadExtends();

			/*< comprueba que la vista sea un email*/
			$view = explode("/", $this->name_tpl)[0];

			/*< si la vista es un email*/
			if ($view=="emails") {
				/*< carga las variables de entorno que configuran el email*/
				$this->setVarsTPL(["PROJECT_NAME" => $_ENV['PROJECT_NAME'], "PROJECT_AUTHOR" => $_ENV['PROJECT_AUTHOR']]);
				return true;
			}
			/*< carga las variables de entorno que configuran el proyecto*/
			$this->setVarsTPL(["PROJECT_NAME" => $_ENV['PROJECT_NAME'],
				"PROJECT_DESCRIPTION" => $_ENV['PROJECT_DESCRIPTION'],
				"PROJECT_AUTHOR" => $_ENV['PROJECT_AUTHOR'],
				"PROJECT_AUTHOR_CONTACT" => $_ENV['PROJECT_AUTHOR_CONTACT'],
				"PROJECT_URL" => $_ENV['PROJECT_URL'],
				"PROJECT_KEYWORDS" => $_ENV['PROJECT_KEYWORDS'],
				"PROJECT_MODE" => $_ENV['PROJECT_MODE'],
				"APP_URL_BASE" => $_ENV["APP_URL_BASE"],
				"APP_URL_LOGO_MAIN" => $_ENV["APP_URL_LOGO_MAIN"],
				"APP_URL_LOGO_HEAD" =>$_ENV["APP_URL_LOGO_HEAD"]
			]);

			return true;
		}

		/**
		 * 
		 * @brief carga en el buffer las secciones que extienden la vista @extern('nombre vista extentendida')
		 * @return boolean true 
		 *
		 * */
		function loadExtends(){

			// vector de coincidencias
			$matches = []; 

			/*< busca todos los @extern('ss') dentro del buffer*/
			preg_match_all("/@extern\(['\"]([a-zA-Z0-9_]+)['\"]\)/", $this->buffer, $matches);

			/*< recorre todas las coincidencias y las reemplaza con el contenido de los archivos extern*/
			foreach ($matches[0] as $key => $extends) {

				if($matches[1][$key] == "navbar"){
					if (isset($_SESSION['app-estacion'])) {
						$matches[1][$key] = $_SESSION['app-estacion']['user']->is_admin ? "navbar_admin" : "navbar_cliente";
					}else{
						$matches[1][$key] = "navbar_anonimo"; 
					}
				}

				/*< carga el contenido del archivo*/
				$header = file_get_contents("views/externs/".$matches[1][$key].".html");

				/*< reemplaza @extern('xx') con el contenido del archivo*/
				$this->buffer = str_replace($extends, $header, $this->buffer);

			}

			if ($this->issetExtendsTPL()) {
				$this->loadExtends();
			}
			
			return true;			

		}

		/**
		 * 
		 * Altera el buffer con los valores de las variables
		 * 
		 * @param array $vars esta indexado asociativa key es nombre de la variable
		 * 
		 * */
		function setVarsTPL($vars){

			foreach ($vars as $needle => $str) {
				if($this->testVarTPL($needle)){
				$this->buffer = str_replace("{{".$needle."}}", $str, $this->buffer);
				}else{
					echo "no existe la variable <b>$needle</b>";
					exit();
				}
			}	
		}

		/**
		 * 
		 * Verifica si la variable existe en el buffer
		 * @return bool false si no existe la variable
		 * 
		 * */
		function testVarTPL($name_var){
			return strpos( $this->buffer, $name_var);
		}

		/**
		 * 
		 * Verifica si hay algun extern en el buffer
		 * @return bool false si no se encuentra ningún extern
		 * 
		 * */
		function issetExtendsTPL(){
			return strpos( $this->buffer, '@extern');
		}

		/**
		 * 
		 * imprime el buffer en pantalla
		 * 
		 * */
		function printTPL(){
			echo $this->buffer;
		}

		/**
		 * 
		 * imprime el buffer en pantalla
		 * 
		 * */
		function returnTPL(){
			return $this->buffer;
		}

		/**
		 * 
		 * resetea el buffer 
		 * 
		 * */
		function resetTPL(){
			$this->buffer = '';
			return;
		}

		/**
		 * 
		 * Carga la vista dentro de buffer
		 * 
		 * @return bool existe|no existe la vista
		 * 
		 * */
		function loadTPLFromAPI(){

			/*< comprueba que exista la vista*/
			if(!file_exists("../views/".$this->name_tpl."View.html")){
				echo "No existe la vista <b>".$this->name_tpl."</b>";
				exit();
			}

			/*< carga la vista en el buffer*/
			$this->buffer = file_get_contents("../views/".$this->name_tpl."View.html");

			/*< carga los extends que haya dentro de la vista*/
			$this->loadExtendsFromAPI();

			/*< comprueba que la vista sea un email*/
			$view = explode("/", $this->name_tpl)[0];

			/*< si la vista es un email*/
			if ($view=="emails") {
				/*< carga las variables de entorno que configuran el email*/
				$this->setVarsTPL(["PROJECT_NAME" => $_ENV['PROJECT_NAME'], "PROJECT_AUTHOR" => $_ENV['PROJECT_AUTHOR']]);
				return true;
			}
			/*< carga las variables de entorno que configuran el proyecto*/
			$this->setVarsTPL(["PROJECT_NAME" => $_ENV['PROJECT_NAME'],
				"PROJECT_DESCRIPTION" => $_ENV['PROJECT_DESCRIPTION'],
				"PROJECT_AUTHOR" => $_ENV['PROJECT_AUTHOR'],
				"PROJECT_AUTHOR_CONTACT" => $_ENV['PROJECT_AUTHOR_CONTACT'],
				"PROJECT_URL" => $_ENV['PROJECT_URL'],
				"PROJECT_KEYWORDS" => $_ENV['PROJECT_KEYWORDS'],
				"PROJECT_MODE" => $_ENV['PROJECT_MODE'],
				"APP_URL_BASE" => $_ENV["APP_URL_BASE"],
				"APP_URL_LOGO_MAIN" => $_ENV["APP_URL_LOGO_MAIN"],
				"APP_URL_LOGO_HEAD" =>$_ENV["APP_URL_LOGO_HEAD"]
			]);

			return true;
		}

		/**
		 * 
		 * @brief carga en el buffer las secciones que extienden la vista @extern('nombre vista extentendida')
		 * @return boolean true 
		 *
		 * */
		function loadExtendsFromAPI(){

			// vector de coincidencias
			$matches = []; 

			/*< busca todos los @extern('ss') dentro del buffer*/
			preg_match_all("/@extern\(['\"]([a-zA-Z0-9_]+)['\"]\)/", $this->buffer, $matches);

			/*< recorre todas las coincidencias y las reemplaza con el contenido de los archivos extern*/
			foreach ($matches[0] as $key => $extends) {

				if($matches[1][$key] == "navbar"){
					if (isset($_SESSION['innovplast'])) {
						$matches[1][$key] = $_SESSION['innovplast']['user']->is_admin ? "navbar_admin" : "navbar_cliente";
					}else{
						$matches[1][$key] = "navbar_anonimo"; 
					}
				}

				/*< carga el contenido del archivo*/
				$header = file_get_contents("../views/externs/".$matches[1][$key].".html");

				/*< reemplaza @extern('xx') con el contenido del archivo*/
				$this->buffer = str_replace($extends, $header, $this->buffer);

			}

			if ($this->issetExtendsTPL()) {
				$this->loadExtendsFromAPI();
			}
			
			return true;			

		}

	}
 ?>