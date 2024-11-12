const generatePdfButton = document.getElementById("generatePdfButton");

generatePdfButton.addEventListener("click", () => {
	// La URL de la API a la que deseas hacer la solicitud
	const url = `api/pdf/generateList`;

	getEmployees().then(data => {
		getPdfEmployees(url, data)
	})


	//window.open("/alumno/6904/Innovplast/api/pdf/generateFactura", "_blank");
})

async function getPdfEmployees(url, data) {
	const response = await fetch(url, {
		method: "POST",
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify(data)
	});
	console.log(response)
	if (response.ok) {
		const result = await response.json(); // Espera un objeto JSON
		window.open(result.list, "_blank"); // Abre la URL del PDF
	} else {
		console.error('Error al generar el PDF');
	}
}

async function getEmployees() {
	/*< consulta a la API */
	const response = await fetch("controllers/js/data/empleados.json");
	/*< convierte la respuesta a formato json */
	const data = await response.json();
	console.log(data)
	return data;
}

getEmployees().then(data => {
	for (let i = 0; i < data.length; i++) {
		document.querySelector('#table-empleados').innerHTML += `
					<tr>
						<td>${data[i].ID_EMPLEADOS}</td>
						<td>${data[i].NOMBRE} ${data[i].APELLIDO}</td>
						<td>${data[i].EMAIL}</td>
						<td>${data[i].DNI}</td>
						<td>${data[i].ROL}</td>
						<td>
							<p class="estado-conteiner estado">${data[i].ESTADO}</p>
						</td>
						<td><a class="letra-blanca" href="modificarEmpleado&id=${data[i].ID_EMPLEADOS}">Modificar</a></td>
					</tr>
				`;
	}
})

