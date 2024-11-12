
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

/**
 * 
 * @brief carga elementos en el listado
 * 
 * */

 //<div class="title letra-negra">Precio Unitario: ${row.precio_unitario}</div>
function loadPage() {
	// limpia el listado
	user__opinions.innerHTML = "";

	// Llamada a la función asincrona que obtiene un listado de productos
	getMyOpinions().then(({list}) => {

		/*< si hay elementos en el listado*/
		if (list.errno != 411) {

			// Recorre el listado de usuarios fila por fila
			//              // <a href="" class="container_productos m12t ">
			list.res.forEach(row => {
				console.log(row)
				document.querySelector("#user__opinions").innerHTML += `

  
                <a href="" class="info-contenedor m12t">
                    <div class="contenedor_detalles">
                        <img class="imagen_produ" src="${row.imagen}" alt="">
                    </div>
                    <div class="contenedor-cart">
                        <span class="letra-negra font-20">${row.nombre}</span>
                        <div class="font-12 letra-negra-azul m12t">Comentario: 
                            <div class="letra-negra font-400"> ${row.contenido}</div>
                        </div>
                    </div>

                    <div class="columna-center m12t bloquear_contenedor">
                    	
                    <span class="m12b letra-negra" >Calificacion:	</span>
                        <div class="flex color-star selected">
                            ${`<i class="fa-solid fa-star precio "></i>`.repeat(row.valor)}
                            ${`<i class="fa-solid fa-star precio letra-gris"></i>`.repeat(5 - row.valor)}
                        </div>
                    </div>
                    <div class="columna bloquear_contenedor letra-negra-azul">
                        <span>Comprado el ${row.fecha}
                        </span><span > a las ${row.hora}</span>
                    </div>
                </a>
            `
      
			});
		} else {

		document.querySelector("#user__opinions").innerHTML += `
			<div class="flex-center columna">
	            <i class="fa-sharp fa-light fa-cart-circle-exclamation letra-roja titulo_importante"></i>
	            <span class="font-20 letra-negra">Aún no tienes productos en tu carrito</span>
	            <span class="font-20 letra-negra">¡Descubre nuestros productos!</span>
	            <a href="" class="letra-violeta font-17">Ir a ver los productos</a>
        	</div>`

			// user__opinions.innerHTML = data.list.error;
		}

		// const cants = document.querySelectorAll(".cantidad"); // Cambia a clase
		// const subtotals = document.querySelectorAll(".subtotal"); // Cambia a clase

		// let total = 0;

		// cants.forEach(function(cant) {
		//     const valor = cant.innerText.trim(); 
		//     const num = parseInt(valor);
		//     if (!isNaN(num)) {
		//         total += num;
		//     } else {
		//         console.warn(`El valor de cantidad no es un número: ${valor}`);
		//     }
		// });

		// document.querySelector("#cant__products").innerHTML = `${total}`;

		// let sub_total = 0;

		// subtotals.forEach(function(subtotal) {
		//     const valor = subtotal.innerText.trim();
		//     const num = parseInt(valor);
		//     if (!isNaN(num)) {
		//         sub_total += num;
		//     } else {
		//         console.warn(`El valor de subtotal no es un número: ${valor}`);
		//     }
		// });

		// document.querySelector("#subtotal_prods").innerHTML = `${sub_total}`;

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
async function getMyOpinions() {
	/*< consulta a la API */
	const response = await fetch(`${APP_URL_BASE}/api/user/getMyOpinions/`);
	/*< convierte la respuesta a formato json */
	const data = await response.json();
	console.log(data)
	return data;
}
