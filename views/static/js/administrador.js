window_charge.classList.remove("d-none")

/*< Realiza el intento de logueo */
getClientsAndUsersCant().then(data => {

	// si el logueo fue valido
	if (data.list.errno == 200) {

		/*< Redirecciona al panel */
		user_cant.textContent = data.list.cant_users;

		/*< El logueo no fue valido, muestra el error */
		client_cant.textContent = data.list.cant_client;

	}else{
		error_msg.textContent = data.list.error;

	}

	window_charge.classList.add("d-none")

})


/**
 * 
 * @brief Realiza el logueo con el email y contraseña GET
 * @param string email correo electrónico del usuario
 * @param string pass contraseña del usuario
 * @return json respuesta del intento de logueo
 * 
 * */
async function getClientsAndUsersCant() {
	/*< consulta a la API */
	const response = await fetch("/alumno/6904/app-estacion/api/tracker/getClientsAndUsersCant");
	/*< convierte la respuesta a formato json */
	const data = await response.json();

	return data;

}
