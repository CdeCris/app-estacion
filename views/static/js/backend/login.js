
/*< Al presionar el botón de logueo */
btn_login.addEventListener("click", e => {

	/*< evita la recarga de la página */
	e.preventDefault();

	/*< Realiza el intento de logueo */
	login(txt_email.value, txt_pass.value).then(data => {

		let url = `${APP_URL_BASE}/productList`

		// si el logueo fue valido
		if (data.list.errno == 200) {
			if (data.list.admin) {
				url = `${APP_URL_BASE}/panel`
			}
			/*< Redirecciona al panel */
			window.location.href = url;
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
	const response = await fetch("/alumno/6904/Innovplast/api/user/login/?txt_email=" + email + "&txt_pass=" + pass);
	/*< convierte la respuesta a formato json */
	const data = await response.json();

	return data;

}
