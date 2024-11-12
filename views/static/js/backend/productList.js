
/*< variables para la paginación*/
let cantidad = CANT_PER_PAG;
let inicio = 0;

let url

if (url === undefined) { url = 'api/producto/getTapas/' }

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
// loadPage(url);

/*< si se presiona el botón de avanzar a los siguientes elementos del listado*/


/**
 * 
 * @brief carga elementos en el listado
 * 
 * */
function loadPage(url) {
	// limpia el listado
	product__list.innerHTML = "";

	// Llamada a la función asincrona que obtiene un listado de productos
	list(inicio, cantidad, url).then(data => {

		/*< si hay elementos en el listado*/
			if (data.list && data.list.length > 0 ) {
				// data.list.errno != 411
				nameClass = url == "api/producto/getTapas/" ? "img_cover" : false;
				url_image_tapa = nameClass ? URL_IMG_TAPA : false;

				// Recorre el listado de productos fila por fila
				data.list.forEach(row => {

					url_image = url_image_tapa ? url_image_tapa : row.imagen;

					document.querySelector("#product__list").innerHTML += `
						<a href="${APP_URL_BASE}/details?prod=${row.token}" class="info-contenedor">
			                <div class="contenedor_produ" >
			                    <img class="imagen_produ ${nameClass} ${row.color}" src="${url_image}" alt="">
			                </div>
				            <div class="contenedor_info" >
				            	<div class="title-name p10">
				                	<span class="letra-negra font-subtitle">
				                    	${row.descripcion}
				                	</span>
				                </div>
				                <div class="productos-contenido p10">
		                       		<div class="letra-negra">
				                	    <span class="letra-negra font-price">$${row.precio_unitario},00</span>
				                	</div>
		                        	<div class="flex-end columna">
					                    <div class="letra-negra flex-end font-12 m12t">
						                    <p class="p12L">Stock : ${row.stock}</p>
						                    <p class="stock">Disponible</p>
					                    </div>
					                    <div class="icon-absolute-abajo flex-end ">
					                        <i class="fa-solid fa-star precio font-12"></i>
					                        <i class="fa-solid fa-star precio font-12"></i>
					                        <i class="fa-solid fa-star precio font-12"></i>
					                        <i class="fa-solid fa-star precio font-12"></i>
					                        <i class="fa-solid fa-star precio font-12"></i>
					                    </div>
				                    </div>
				                </div>
				            </div>
						</a>
					`;
				});

				// Actualiza la paginación
				paginaActual(data.list);
		} else {
			product__list.innerHTML = data.list.error;
		}

	})
}


loadPage(url);


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

tapas.addEventListener("click", e => {
	categoriaActual = "tapas";
	url = "api/producto/getTapas/"
	cantidad = CANT_PER_PAG;
	inicio = 0;
	loadPage(url);

})
products.addEventListener("click", e => {
	categoriaActual = "productos";
	url = "api/producto/getProducts/"
	cantidad = CANT_PER_PAG;
	inicio = 0;
	loadPage(url);

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

function showDetails(btn) {
	let product_token = btn.id.split("__")[btn.id.split("__").length - 1]
	let url = "https://mattprofe.com.ar/alumno/6904/Innovplast/details?prod=" + product_token
	window.location.href = url
}

document.querySelector("#search__input").innerHTML +=`
			<input class="buscador" type="search" placeholder="Buscar producto">
			<input type="submit" class="material-icons-outlined btn_lupa" value="search">
`
