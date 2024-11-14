form_register.addEventListener('submit', function(event) {

	window_charge.classList.remove("d-none")

    event.preventDefault();

    const formData = new FormData(form_register);

    const data = {
    	email : formData.get('txt_email'),
    	pass : formData.get('txt_pass'),
    	sec_pass : formData.get('txt_pass_2')
    }
   	
    fetch('/alumno/6904/app-estacion/api/user/register', {
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

    	window_charge.classList.add("d-none")

        if(data.list.errno==200){
            error_msg.textContent = data.list.error;
            window.location.href = 'login';
        }else{
            error_msg.textContent = data.list.error;
        } 
    })

});

