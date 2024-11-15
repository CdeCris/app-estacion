// Obtener la cadena de consulta (query string) de la URL
const queryString = window.location.search;

// Crear un objeto URLSearchParams a partir de la cadena de consulta
const urlParams = new URLSearchParams(queryString);

// Obtener el valor de un parámetro específico
const token_action = urlParams.get('token_action');

box_charge.classList.remove("d-none")

verificarToken()

async function verifyToken() {
    /*< Consulta a la API */
    const response = await fetch(`/alumno/6904/app-estacion/api/user/verifyToken?token_action=${token_action}`); 
    /*< Convierte la respuesta a formato JSON */
    const data = await response.json();

    return data;
}

function verificarToken(){
    verifyToken().then( data =>{
        
        box_charge.classList.add("d-none")

        if (data.list.errno == 200) {
            form_reset.classList.remove("d-none")
        }else{
            error_conteiner.classList.remove("d-none")
            error_icon_check.classList.add("d-none")
            error_msg.textContent = data.list.error;
        }
    })
}

form_reset.addEventListener('submit', function(event) {

    form_reset.classList.add("d-none")

	box_charge.classList.remove("d-none")

    event.preventDefault();

    const formData = new FormData(form_reset);

    const data = {
    	pass : formData.get('txt_pass'),
    	sec_pass : formData.get('txt_pass_2'),
        token : token_action
    }
   	
    fetch('/alumno/6904/app-estacion/api/user/resetAccount', {
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

        if(data.list.errno==200){
            error_conteiner.classList.remove("d-none")
            error_msg.textContent = data.list.error;
            error_icon_cruz.classList.add("d-none")
            setTimeout(() => {
              window.location.href = 'login';
            }, 1500);

        }else{
            form_error_msg.textContent = data.list.error;
            form_reset.classList.remove("d-none")

        } 
    })

});

