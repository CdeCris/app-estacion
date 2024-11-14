// Obtener la cadena de consulta (query string) de la URL
const queryString = window.location.search;

// Crear un objeto URLSearchParams a partir de la cadena de consulta
const urlParams = new URLSearchParams(queryString);

// Obtener el valor de un parámetro específico
const token = urlParams.get('token');

/*< Realiza el intento de logueo */
blockAccount().then(data => {

	box_charge.classList.add("d-none")

	// si el logueo fue valido
	if (data.list.errno == 200) {
		/*< Redirecciona al panel */
		error_msg.textContent = data.list.error;
		error_conteiner.classList.remove("d-none")
		error_icon_cruz.classList.add("d-none")
		error_msg.classList.add("check")
	}else{
		/*< El logueo no fue valido, muestra el error */
		error_msg.textContent = data.list.error;
		error_conteiner.classList.remove("d-none")
		error_icon_check.classList.add("d-none")
		error_msg.classList.add("cruz")
	}

})


/**
 * 
 * @brief Realiza el logueo con el email y contraseña GET
 * @param string email correo electrónico del usuario
 * @param string pass contraseña del usuario
 * @return json respuesta del intento de logueo
 * 
 * */
async function blockAccount() {
	/*< consulta a la API */
	const response = await fetch("/alumno/6904/app-estacion/api/user/blockAccount/?token=" + token);
	/*< convierte la respuesta a formato json */
	const data = await response.json();

	return data;

}
