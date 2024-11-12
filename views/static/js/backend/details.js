// Obtén la URL actual
const url = new URL(window.location.href);
// Usa searchParams para obtener el valor del parámetro
const token_prod = url.searchParams.get('prod');
let cant_product = document.querySelector('#product__cant');
formulario = document.querySelector('.flex-start');

let stockDisponible = 0; 

calificacion_total = 0
loadPage()

/**
 * 
 * @brief carga elementos en el listado
 * 
 * */

 // <div class="contenedor-cart">
 // <div class="title letra-negra">${list.product_info.nombre}</div>
 // <div class="title letra-negra">${list.product_info.descripcion}</div>
 // </div>
// <div class="product--subtotal">
//  SubTotal: <span id="subtotal${list.product_info.token}" class="subtotal"></span>
// </div>
function loadPage() {
	// limpia el listado
	prod__main.innerHTML = "";
	// Llamada a la función asincrona que obtiene un listado de productos
	getDetails(token_prod).then(({ list }) => {

		/*< si hay elementos en el listado*/
		if (list.errno != 411) {
            stockDisponible = list.product_info.stock;
			document.querySelector("#prod__main").innerHTML += `
                <div class="info-contenedor">
                    <div class="contenedor_1">
                        <div class="contenedor_detalles">
                            <img class="imagen_produ" src="${list.product_info.imagen}" alt="">
                        </div>
                    </div>
                    <div class="contenedor_2">
                            <div class="container_desc_producto letra-negra">

                            <div class="title-desc-producto letra-negra">${list.product_info.nombre}</div>
                            <p class="letra-negra flex">Califica este producto:</p>
                            <div class="icon-absolute-abajo flex ">
                                <i class="fa-solid fa-star precio"></i>
                                <i class="fa-solid fa-star precio"></i>
                                <i class="fa-solid fa-star precio"></i>
                                <i class="fa-solid fa-star precio"></i>
                                <i class="fa-solid fa-star precio"></i>
                            </div>
                            <div class="precio font-title">$<span id="product__price">${list.product_info.precio_unitario}</span></div>
                            <div class="font-17 letra-negra m12t">Stock disponible:<span class="letra-violeta"> ${list.product_info.stock}</span> </div>

                            <div class="flex-start m12t">
                                <span class="p12R">Cantidad: </span>
                                <input class="input-price" type="text" name="num_cant" id="product__cant" value="1" min="1" max="${list.product_info.stock}">
                                <span class="p12L">unidades</span>
                            </div>

                            
                            <div class="columna m12t">
                                 <div class="product--cart">
                                    <section class="product__chart">
                                        
                                        <button type="submit" name="btn_submit" id="submit__product__cant" class="product__add btn_registro letra-blanca " onclick="addChart(this)">Añadir Al Carrito</button>
                                      </section>

                                <button type="submit" value="Crear cuenta" class="btn_registro letra-blanca">Comprar
                                    ahora</button>
                            </div>
                            <p class="letra-negra">Vendido por <span class="letra-violeta txt_mayus">
                                    Innovplast</span> </p>
                            <p class="letra-negra m12t"><span class="letra-violeta txt_mayus"><i class="fa-sharp fa-solid fa-plus icon-chico"></i>100</span> Ventas </p>
                            <p class="letra-violeta "><i class="fa-solid fa-location-dot m12t p12R letra-gris"></i>Nuestra
                                tienda</p>
                        </div>
                    </div>
                </div>`
                //<button popovertarget="pop__add" popovertargetaction="toggle" type="submit" name="btn_submit" id="submit__product__cant__${list.product_info.token_prod}" class="product__add" onclick="opinar(this)">Opinar</button>
                document.querySelector("#desc__prod").innerHTML = `${list.product_info.descripcion}`
            //Inicio Mely
            document.getElementById("product__cant").addEventListener("input", function (e) {
                //*PROBLEMS*//
                let value = e.target.value.replace(/\D/g, "").slice(0, 3);; 
                e.target.value = Math.min(value, stockDisponible); 

            });
            //Fin Mely
            calificacion_total = 0 

            document.querySelector("#list__comments").innerHTML = ``

			list.product_opinion.forEach(row => {

				document.querySelector("#list__comments").innerHTML += `
			 				<div class="product__card comentario m12t m12b>
							 	<div class="product__info">
                                    <div class="product__name font-9 letra-gris">
                                        ${row.nombre}
                                    </div>
							 		<div class="product__name icon-absolute-abajo fila">
							 			${`<i class="fa-solid fa-star precio"></i>`.repeat(row.valor)}
                                        ${`<i class="fa-solid fa-star precio letra-gris"></i>`.repeat(5 - row.valor)}
							 		</div>
							 		<div class="product__name font-comentario p12R m10l">
							 			${row.contenido}
							 		</div>
							 	</div>
							</div>`
                calificacion_total = calificacion_total + parseInt(row.valor)

			});

    		    calificacion_media = (calificacion_total / list.product_opinion.length).toFixed(1)

                document.querySelector("#prod__score").innerText = `${calificacion_media}`;

                total_stars = Math.round(calificacion_media)

                document.querySelector("#prod__score__star").innerHTML = `${`<i class="fa-solid fa-star precio"></i>`.repeat(total_stars)}`;

                document.querySelector("#prod__score__star").innerHTML += `${`<i class="fa-solid fa-star precio letra-gris"></i>`.repeat(5 - total_stars)}`;

                document.querySelector("#cant__opoinions").innerText = `${list.product_opinion.length}`;

                document.querySelector("#cant__scores").innerText = `${list.product_opinion.length}`;

        } else {
			prod__main.innerHTML = data.list.error;
		}

	})

}

// <div class="product__name">
//     ${row.nombre}
// </div>

// document.getElementById("product__cant").addEventListener("input", function (e) {
//     let value = e.target.value.replace(/\D/g, "");
//     e.target.value = Math.min(value.slice(0, 3), stockDisponible);
//     document.getElementById("product__cant").value = e.target.value;

// });

async function getDetails(token_prod) {
	/*< consulta a la API */
	const response = await fetch("api/producto/getDetails/?prod=" + token_prod);
	/*< convierte la respuesta a formato json */
	const data = await response.json();
	console.log(data)
	return data;
}

async function addChart(btn) {
    let cant_product = document.querySelector("#product__cant").value

    //window.location.href = url

    // La URL de la API a la que deseas hacer la solicitud
    const url = `api/producto/addProduct`;

    // Los datos que deseas enviar
    const data = {
        prod: token_prod,
        cant: cant_product
    };

    // Hacer la solicitud POST
    response = await fetch(url, {
        method: "POST", // Método de la solicitud
        headers: {
            'Content-Type': 'application/json', // Tipo de contenido
        },
        body: JSON.stringify(data) // Convertir los datos a JSON
    })

    if (response.ok) {
        window.location.href = 'chartList'
    }
}
