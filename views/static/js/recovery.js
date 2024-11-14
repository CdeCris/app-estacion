// Obtener la cadena de consulta (query string) de la URL
const queryString = window.location.search;

// Crear un objeto URLSearchParams a partir de la cadena de consulta
const urlParams = new URLSearchParams(queryString);

// Obtener el valor de un parámetro específico
const token_action = urlParams.get('token_action');

form_recovery.addEventListener('submit', function(event) {

	box_charge.classList.remove("d-none")

    error_conteiner.classList.add("d-none")
    error_icon_check.classList.remove("d-none")
    error_icon_cruz.classList.remove("d-none")

    form_recovery.classList.add("d-none")

    event.preventDefault();

    const formData = new FormData(form_recovery);

    const data = {
    	email : formData.get('txt_email')
    }
   	
    fetch('/alumno/6904/app-estacion/api/user/recovery', {
        method: "POST",
        headers: {
            'Content-Type': 'application/json', 
        },
        body: JSON.stringify(data)
    })
    .then(response => {
		return response.json();
    })
    .then(data => {

    	box_charge.classList.add("d-none")

        error_conteiner.classList.remove("d-none")

        if(data.list.errno==200){
            error_msg.textContent = data.list.error;
            error_icon_cruz.classList.add("d-none")

        }else{
            error_msg.textContent = data.list.error;
            error_icon_check.classList.add("d-none")
            form_recovery.classList.remove("d-none")

        } 
    })

});

