
/*< variables para la paginación*/
let cantidad = 6;
let inicio = 0;
/*< carga inicial de elementos en el listado*/
loadPage();

/*< si se presiona el botón de avanzar a los siguientes elementos del listado (Para la paginacion)*/
// mover.addEventListener("click", e => {
// 	inicio = inicio + cantidad;
// 	loadPage();
// })
// volver.addEventListener("click", e => {
// 	inicio = inicio - cantidad;
// 	loadPage();
// })


// async function buyProducts(url, data) {
// 	console.log(data);
// 	const response = await fetch(url, {
// 		method: "PUT",
// 		headers: {
// 			'Content-Type': 'application/json',
// 		},
// 		body: JSON.stringify(data)
// 	});

// 	if (response.ok) {
// 		return await response.json(); // Espera un objeto JSON
// 	} else {
// 		console.error('Error al comprar los productos');
// 	}
// }

// async function sendListProducts(url, data) {
// 	const response = await fetch(url, {
// 		method: "POST",
// 		headers: {
// 			'Content-Type': 'application/json',
// 		},
// 		body: JSON.stringify(data)
// 	});

// 	if (response.ok) {
// 		const result = await response.json(); // Espera un objeto JSON
// 		window.open(result.list, "_blank"); // Abre la URL del PDF
// 	} else {
// 		console.error('Error al generar el PDF');
// 	}
// }

/**
 * 
 * @brief carga elementos en el listado
 * 
 * */

 //<div class="title letra-negra">Precio Unitario: ${row.precio_unitario}</div>
function loadPage() {
	// Llamada a la función asincrona que obtiene un listado de productos
	listChartProducts().then(data => {
		/*< si hay elementos en el listado*/
		if (data.list.errno != 411) {
			// Recorre el listado de usuarios fila por fila
			if (data.list.length == 0) {
				resetChart()
				info__chart.classList.add("ocultar_contenido");
			}
			else{
				chart__null.classList.add("ocultar_contenido");
				data.list.forEach(row => {
					document.querySelector("#chart__list").innerHTML += `
		                <div class="info-contenedor">
		                    <div class="contenedor_detalles">
		                        <img class="imagen_produ" src="${row.imagen}" alt="">
		                    </div>
		                    <div class="flex-compu">
			                    <div class="contenedor-cart">
			                        <div class="title letra-negra">${row.titulo}</div>
			                    </div>
			                 </div>
		                    <div class="btn_cart">
		                        <button id="restar" class="iconos botonesCarrito"><i class="fa-solid fa-minus" id="hover"></i></button>
		                        <p id="cantidad" class="botonesCarrito letra-negra cantidad">${row.cantidad}</p>
		                        <button  id="sumar"  class="iconos botonesCarrito"><i class="fa-solid fa-plus" id="hover" ></i></button>
		                    </div>
		                    <div class="btn_cart columna-compu">
		                    	<button type="submit" name="btn_submit" id="submit__product__cant__${row.id_chart_prod}" class="product__add font-20" onclick="removeChart(this)"><i class="fa-solid fa-trash p10"></i></button>
		                    </div>
		                    <div class="ocultar_contenido">
		                        <div class="btn_cart">
		                            <button type="submit" name="btn_submit" id="submit__product__cant__${row.id_chart_prod}" class="product__add" onclick="removeChart(this)"><i class="fa-solid fa-trash p10"></i></button>
		                        </div>
		                    </div>
		                    <div class="price-absolute">
		                         <div class=" letra-negra">$ <span class="subtotal letra-17">${row.SubTotal}</span></div>
		                    </div>
	                    </div>`

                    const cants = document.querySelectorAll(".cantidad");
                    const subtotals = document.querySelectorAll(".subtotal");

                    let total = 0;

                    cants.forEach(cant => {

                    	const valor = cant.innerText; 

                    	total += parseInt(valor);
                    });

                    localStorage.setItem("cantProds", total);
                    document.querySelector("#cant__products").innerHTML = `${total}`
                    let sub_total = 0;
                    subtotals.forEach(subtotal => {
                    	const valor = subtotal.innerText; 
                    	sub_total += parseInt(valor);
                    });
                    localStorage.setItem("subTotal", sub_total.toFixed());
                    document.querySelector("#subtotal_prods").innerHTML = `${sub_total.toFixed()}`
                });
			}
		} else {
			chart__list.innerHTML = data.list.error;
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
async function listChartProducts() {
	/*< consulta a la API */
	const response = await fetch("api/producto/getChartProducts/");
	/*< convierte la respuesta a formato json */
	const data = await response.json();
	console.log(data)
	return data;
}

async function removeProductChart(id_chart) {
	/*< consulta a la API */
	const response = await fetch("api/producto/removeProductChart/?id_chart=" + id_chart);
	/*< convierte la respuesta a formato json */
	const data = await response.json();
	console.log(data)
	return data;
}

function removeChart(btn) {
	let product_id = btn.id.split("__")[btn.id.split("__").length - 1]
	removeProductChart(product_id);
	loadPage();
}

function resetChart() {
	chart__list.innerHTML = ` 
        <div class="flex-center columna" id="chart__null">
           <i class="fa-solid fa-cart-circle-exclamation letra-roja titulo_importante font-400"></i>
            <span class="font-20 letra-negra">Aún no tienes productos en tu carrito</span>
            <span class="font-20 letra-negra">¡Descubre nuestros productos!</span>
            <a href="${APP_URL_BASE}/productList" class="letra-violeta font-17">Ir a ver los productos</a>
        </div>`;
}

btn_compra = document.querySelector('#btn_buy')

btn_compra.addEventListener("click", e => {

	listChartProducts().then(data => {

		localStorage.setItem("productChartList", JSON.stringify(data));
		window.location.href=`${APP_URL_BASE}/metodoPago`
	})

})


// 	<div class="product__card"">
//  	<div class="product__img">
//  		<img src="${row.imagen}" class="${row.color}" alt="">
//  	</div>
//  	<div class="product__info">
//  		<div class="product__price">
//  			<span>$</span><span class="product__pricevalue">${row.precio_unitario}</span>
//  		</div>
//  		<div class="product__name">
//  			${row.titulo}
//  		</div>
//  		<div class="product__name">
//  			Cantidad: ${row.cantidad}
//  		</div>
//  		<div class="product__name">
//  			SubTotal: ${row.SubTotal}
//  		</div>
//  		<button type="submit" name="btn_submit" id="submit__product__cant__${row.id_chart_prod}" class="product__add" onclick="removeChart(this)">REMOVE</button>
//  	</div>
// </div>