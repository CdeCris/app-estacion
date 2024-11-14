
/*< Al presionar el botón de logueo */
btn_login.addEventListener("click", e => {

	window_charge.classList.remove("d-none")

	/*< evita la recarga de la página */
	e.preventDefault();

	/*< Realiza el intento de logueo */
	login(txt_email.value, txt_pass.value).then(data => {

		window_charge.classList.add("d-none")

		// si el logueo fue valido
		if (data.list.errno == 200) {
			/*< Redirecciona al panel */
			window.location.href = "panel";
		}

		/*< El logueo no fue valido, muestra el error */
		msg_box.textContent = data.list.error;
	})
})


/**
 * 
 * @brief Realiza el logueo con el email y contraseña GET
 * @param string email correo electrónico del usuario
 * @param string pass contraseña del usuario
 * @return json respuesta del intento de logueo
 * 
 * */
async function login(email, pass) {
	/*< consulta a la API */
	const response = await fetch("/alumno/6904/app-estacion/api/user/login/?txt_email=" + email + "&txt_pass=" + pass);
	/*< convierte la respuesta a formato json */
	const data = await response.json();

	return data;

}
