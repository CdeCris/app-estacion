// Obtén la URL actual
const url = new URL(window.location.href);
// Usa searchParams para obtener el valor del parámetro
const token_prod = url.searchParams.get('prod');

loadPage()

/**
 * 
 * @brief carga elementos en el listado
 * 
 * */
function loadPage() {
	// limpia el listado
	detail__prod.innerHTML = "";
	// Llamada a la función asincrona que obtiene un listado de productos
	getDetails(token_prod).then(({ list }) => {

		/*< si hay elementos en el listado*/
		if (list.errno != 411) {
            prod = list[0]
			document.querySelector("#detail__prod").innerHTML += `

        <div class="detalles_compra ">
            <div class="contenedor-cart p12L">
                <span class="letra-negra">${prod.titulo}</span>
                <div class="flex">
                    <div class="font-9 letra-negra m12t">${prod.cantidad} unidad/es</div>
                </div>

            </div>
            <div class="contenedor_detalles">
                <img class="round-image" src="${prod.imagen}" alt="">
            </div>
        </div>
        <section class="contenedor_estado letra-negra">

            <div class="contenedor_detalles m12t">
                <img class="round-image" src="${prod.imagen}" alt="">
            </div>
            <span class="font-20 m12t">¿Que te parecio este producto?</span>
            <span class="font-17 m12t">${prod.titulo}</span>
            <div class="columna-center m12t">
                <div class="flex">
                    <i class="fa-solid fa-star precio" data-value="1"></i>
                    <i class="fa-solid fa-star precio" data-value="2"></i>
                    <i class="fa-solid fa-star precio" data-value="3"></i>
                    <i class="fa-solid fa-star precio" data-value="4"></i>
                    <i class="fa-solid fa-star precio" data-value="5"></i>
                </div>
                <div class="flex pimba m12b">
                    <span>Malo</span>
                    <span>Bueno</span>
                </div>
            </div>
                <div class="seccion_comentario"> 
                    <textarea placeholder="Escribe tu comentario aquí..." class="m12b" id="prod_comment"></textarea>
                    <div class="font-subtitle letra-violeta" id="msg_box"><div>
                    <div class="contenedor_100 flex-end ">
                        <button class="letra-blanca btn_comentario" onclick="sendOpinion()">Enviar</button>
                    </div>

                </div>

        </section>

        <section class="contenedor_informacion_compra letra-negra">
            <div class="informacion_compra">
                <span class="font-20 border-bottom ">Informacion de la compra</span>
                <span class="font-9 m12t flex-end">Cargada el ${prod.fecha_comprado}</span>
                <div class="flex">

                    <i class="fa-sharp-duotone fa-solid fa-note titulo_subimportante letra-gris font-25 m12t ml-2 m10r"></i>
                    <div class="columna p12L">

                        <span class="m12t letra-violeta" title="${prod.titulo} ">${prod.titulo}</span>

                        <button type="submit" id="facturar" class="m12t letra-violeta btn_filtro">Descargar factura</button>
                    </div>

                </div>
            </div>
        </section>`

        //Facturar un solo producto
        facturar.addEventListener("click",()=>{

            getDetails(token_prod).then(data => {

                let url_api = `api/pdf/generateFactura`;
                data.list = [data.list[0]];                
                const params = {
                    ...data,
                    "metodo_pago" : data.list[0].metodo_pago,
                    "chart_num" : data.list[0].chart_num
                }
                console.log(params)

                sendListProducts(url_api, params)
            })

        })

        document.querySelectorAll('.precio').forEach(star => {
            star.addEventListener('click', function() {
                const value = this.getAttribute('data-value');

                // Remover la clase 'selected' de todas las estrellas
                document.querySelectorAll('.precio').forEach(s => s.classList.remove('selected'));

                // Agregar la clase 'selected' a las estrellas hasta la seleccionada
                for (let i = 1; i <= value; i++) {
                    document.querySelector(`.precio[data-value="${i}"]`).classList.add('selected');
                }

                // Guardar la calificación en localStorage
                localStorage.setItem('calificacion', value);
            });
        });

        localStorage.setItem('token_prod', prod.token_prod);

        } else {
			detail__prod.innerHTML = data.list.error;
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
async function getDetails(token_prod) {
	/*< consulta a la API */
	const response = await fetch("api/producto/getPurchaseDetails/?prod=" + token_prod);
	/*< convierte la respuesta a formato json */
	const data = await response.json();
	console.log(data)
	return data;
}


async function sendListProducts(url_api, data) {
    const response = await fetch(url_api, {
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


function sendOpinion() {
    const data = {
        token_prod: localStorage.getItem("token_prod"),
        comment: document.querySelector("#prod_comment").value,
        calification: localStorage.getItem('calificacion')
    }
    addCommentAndCalification(data).then(data =>{
        console.log(data)
        msg_box.textContent = data.list.error;
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
async function addCommentAndCalification(request) {
    // La URL de la API a la que deseas hacer la solicitud
    const url = `api/producto/addCommentAndCalification`;

    // Hacer la solicitud POST
    response = await fetch(url, {
        method: "POST", // Método de la solicitud
        headers: {
            'Content-Type': 'application/json', // Tipo de contenido
        },
        body: JSON.stringify(request) // Convertir los datos a JSON
    })
    if (response.ok) {
        return result = await response.json(); // Espera un objeto JSON
    }
}

