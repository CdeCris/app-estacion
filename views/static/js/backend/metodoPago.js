
/*< variables para la paginación*/
let cantidad = 6;
let inicio = 0;
/*< carga inicial de elementos en el listado*/
// loadPage();

const prodList = JSON.parse(localStorage.getItem("productChartList"));

const cantProds = (localStorage.getItem("cantProds"));

const subTotal = (localStorage.getItem("subTotal"));

document.querySelector('#cant__prods').textContent = `${cantProds}`

document.querySelector('#subtotal').textContent = `${subTotal}`

impuesto = parseInt(document.querySelector('#impuesto').textContent)

document.querySelector('#total').textContent = `${parseInt(subTotal) + impuesto}`


btn_buy.addEventListener("click", () => {
	// La URL de la API a la que deseas hacer la solicitud
	let url = `api/producto/buyChart`;

	const headers = {
		"metodo_pago": document.querySelector('input[name="metodo_pago"]:checked').value
	}
	
	const params = {
		...prodList,
		...headers
	};

	buyProducts(url, params).then(data => {

		const pdf_req = {
			...params,
			...data.list.data
		};

		console.log(pdf_req)

		url = `api/pdf/generateFactura`;

		sendListProducts(url, pdf_req).then(() => {

			window.location.href = `${APP_URL_BASE}/misCompras`; 
		})

	})

})

async function buyProducts(url, data) {
	console.log(data);
	const response = await fetch(url, {
		method: "PUT",
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify(data)
	});

	if (response.ok) {
		return await response.json(); // Espera un objeto JSON
	} else {
		console.error('Error al comprar los productos');
	}
}

async function sendListProducts(url, data) {
	const response = await fetch(url, {
		method: "POST",
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify(data)
	});

	if (response.ok) {
		const result = await response.json(); // Espera un objeto JSON
		window.open(result.list, "_blank"); // Abre la URL del PDF
	} else {
		console.error('Error al generar el PDF');
	}
}
