
/*< variables para la paginación*/
let cantidad = CANT_PER_PAG;
let inicio = 0;

const url = `${APP_URL_BASE}/api/user/getTickets` 

/*< carga inicial de elementos en el listado*/
function paginaActual(data) {
    let paginaActual = Math.floor(inicio / cantidad) + 1;
 	const paginaNumeroElement = document.getElementById("inicio");

 	if (data.length === 0 || (data.length<cantidad && inicio==0)) {
        // Si no hay productos, ocultar el número de página
        paginaNumeroElement.style.display = 'none';
    } else {
        // Si hay más productos que la cantidad, mostrar el número de página
        if (data.length > cantidad || inicio >= 0) {
        	paginaNumeroElement.style.display = 'inline';
        	paginaNumeroElement.textContent = `${paginaActual}`;
        }
    }

	/*Oculta el boton de mover si no hay mas prodcutos que mostrar*/
	document.getElementById("mover").style.display = data.length < cantidad ? 'none' : 'inline'; //Ocurre un erro al momento de que la ultima pagina tiene la misma cantidad de productos, por que permite avanzar una mas y muestra undefined

    /*Oculta el boton de volver si el inicio es 0*/
    document.getElementById("volver").style.display = inicio === 0 ? 'none' : 'inline';
}

// paginaActual();
loadPage(url);

/*< si se presiona el botón de avanzar a los siguientes elementos del listado*/


/**
 * 
 * @brief carga elementos en el listado
 * 
 * */
function loadPage(url) {
	// limpia el listado
	factura__list.innerHTML = "";

	// Llamada a la función asincrona que obtiene un listado de productos
	list(inicio, cantidad, url).then(data => {

		/*< si hay elementos en el listado*/
			if (data.list && data.list.length > 0 ) {

				// Recorre el listado de productos fila por fila
				data.list.forEach(row => {

					document.querySelector("#factura__list").innerHTML += `

			<div class="card">
                <div class="card-header">Factura N° ${row.num_factura}</div>
                <div class="card-body">
                    <h4 class="card-title font-17 letra-negra" title="${row.nombre_cliente} ${row.apellido_cliente}">Cliente:
                        ${row.nombre_cliente} ${row.apellido_cliente}</h4>

                    <p class="card-text fw-bold letra-negra m12t">Detalle</p>
                    <li class="letra-negra p12L m12t">Metodo de pago: ${row.metodo_pago}</li>
                    <li class="letra-negra p12L m12t">Total: ${row.total}</li>
                    <p class="letra-negra font-9 m12t">${row.hora} - ${row.fecha}</p>
                    <div class="flex-end ">
                        <a href="" class=" letra-negra font-12 m12t">Ver más</a>
                    </div>


                </div>
                <div class="card-footer p10">

                    <button class="btn btn-primary" title="Imprimir comprobante" id="ImprimirComprobante"><span class="fa fa-print"></span>
                    </button>
                    <button class="btn btn-primary" title="Compartir comprobante"><span
                            class="fa fa-share-nodes"></span></button>
                    <button class="btn btn-primary" title="Descargar comprobante" data-url="${row.url_ticket}" onclick="download(this)" ><span
                            class="fa fa-download"></span></button>
                </div>
            </div>
					`;
				});

				// Actualiza la paginación
				paginaActual(data.list);

		} else {
			factura__list.innerHTML = data.list.error;
		}

	})
}

mover.addEventListener("click", e => {
	inicio = inicio + cantidad;
	loadPage(url);
})

volver.addEventListener("click", e => {
	// inicio = inicio - cantidad;
	// loadPage(url);

	if (inicio >= 1) {
		inicio = inicio - cantidad;
		loadPage(url);
    }
})


/**
 * 
 * @brief Retorna un listado de usuarios en formato JSON
 * @param int inicio desde que fila inicia.
 * @param int cantidad cantidad de filas a listar
 * @return json listado de usuarios
 * 
 * */
async function list(inicio, cantidad, url) {
	/*< consulta a la API */
	const response = await fetch(url + "?inicio=" + inicio + "&cantidad=" + cantidad);
	/*< convierte la respuesta a formato json */
	const data = await response.json();
	console.log(data)
	return data;
}

document.querySelector("#search__input").innerHTML +=`
			<input class="buscador" type="search" placeholder="Buscar factura">
			<input type="submit" class="material-icons-outlined btn_lupa" value="search">
`
function download(btn){
	let url = btn.getAttribute("data-url")
	window.open(url, "_blank");
}