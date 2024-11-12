
/*< variables para la paginación*/
let cantidad = 6;
let inicio = 0;

/*< carga inicial de elementos en el listado*/
loadPage();

/*< si se presiona el botón de avanzar a los siguientes elementos del listado*/
// mover.addEventListener("click", e => {
// 	inicio = inicio + cantidad;
// 	loadPage();
// })
// volver.addEventListener("click", e => {
// 	inicio = inicio - cantidad;
// 	loadPage();
// })


/**
 * 
 * @brief carga elementos en el listado
 * 
 * */
function loadPage() {
	// limpia el listado
	purchase__list.innerHTML = "";
	// Llamada a la función asincrona que obtiene un listado de productos
	listPurchaseProducts(inicio, cantidad).then(data => {

		/*< si hay elementos en el listado*/
		if (data.list.errno != 411) {

			// Recorre el listado de usuarios fila por fila
			data.list.forEach(row => {

				if (row.token_prod != null) {
					document.querySelector("#purchase__list").innerHTML += `

					<div class="container_productos">
		                <div class="flex-justify">
		                    <div class="letra-negra">${row.date}</div>
		                    <a href="${APP_URL_BASE}/details?prod=${row.token_prod}" class="boton_link letra-violeta">Volver a comprar</a>
		                </div>
		                <a href="${APP_URL_BASE}/detalleCompra?prod=${row.token_compra}" class="info-contenedor">
		                    <div class="contenedor_detalles">
		                        <img class="imagen_produ" src="${row.image_prod}" alt="">
		                    </div>
		                    <div class="contenedor-cart">
		                        <span class="letra-negra">${row.name_prod}</span>
		                        <div class="font-9 letra-negra m12t">Cantidad: ${row.cantidad}</div>
		                        <div class="font-9 letra-negra m12t">Total: $${row.subtotal_prod}</div>
		                        <div class="font-9 letra-negra m12t">En las próximas 24Hs se estará poniendo en contacto el vendedor con usted. </div>
		                    </div>
		                    <div class="columna-compu">
		                        <span class="letra-negra">Este producto fue vendido por:</span>
		                        <span class="letra-violeta">INNOVPLAST</span>
		                    </div>

		                </a>
		            </div>`
				} else {
					document.querySelector("#purchase__list").innerHTML += `

					<a href="${row.token_tapa}" class="container_productos">
		                <div class="flex-justify">
		                    <div class="letra-azul">${row.date}</div>
		                    <a href="${APP_URL_BASE}/details?prod=${row.token_tapa}" class=" boton_link letra-azul">Volver a comprar</a>
		                </div>
		                <div class="info-contenedor">
		                    <div class="contenedor_detalles">
		                        <img class="imagen_produ" src="${row.image_tapa}" alt="">
		                    </div>
		                    <div class="contenedor-cart">
		                        <span class="letra-negra">${row.name_tapa}</span>
		                        <div class="font-9 letra-negra m12t">Cantidad: ${row.cantidad}</div>
		                        <div class="font-9 letra-negra m12t">Total: $${row.subtotal_tapa}</div>
		                        <div class="font-9 letra-negra m12t">En las próximas 24Hs se estará poniendo en contacto el vendedor con usted. </div>
		                    </div>
		                    <div class="columna-compu">
		                        <span class="letra-negra">Este producto fue vendido por:</span>
		                        <span class="letra-violeta">INNOVPLAST</span>
		                    </div>
		                    <div class="columna-compu">
		                        <button type="submit" value="Crear cuenta" class="btn_registro letra-blanca">Agregar al
		                            carrito</button>
		                        <button type="submit" value="Crear cuenta" class="btn_registro letra-blanca">Comprar
		                            ahora</button>
		                    </div>
		                </div>
		            </a>`
				}
				document.querySelector("#cant__purchase").innerText = `${data.list.length}`

				
			});
		} else {
			purchase__list.innerHTML = data.list.error;
		}

	})
}

/**
 * 
 * @brief Retorna un listado de usuarios en formato JSON
 * @param int inicio desde que fila inicia.
 * @param int cantidad cantidad de filas a listar
 * @return json listado de usuarios
 * 
 * */
async function listPurchaseProducts(inicio, cantidad) {
	/*< consulta a la API */
	const response = await fetch("api/producto/getPurchaseProducts/?inicio=" + inicio + "&cantidad=" + cantidad);
	/*< convierte la respuesta a formato json */
	const data = await response.json();
	console.log(data)
	return data;
}

function opinar(btn) {
	document.querySelector("#input_prod_token").value = btn.id.split("__")[btn.id.split("__").length - 1]
}


//<button popovertarget="pop__add" popovertargetaction="toggle" type="submit" name="btn_submit" id="submit__product__cant__${row.token_prod}" class="product__add" onclick="opinar(this)">Opinar</button>
