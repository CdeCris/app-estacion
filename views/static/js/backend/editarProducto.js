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
	update__prod.innerHTML = "";
	// Llamada a la función asincrona que obtiene un listado de productos
	getDetails(token_prod).then(({ list }) => {

		/*< si hay elementos en el listado*/
		if (list.errno != 411) {

            const { product_info } = list;

			document.querySelector("#update__prod").innerHTML += `
                    <div class="contenedor_agregar_producto">   
        
                <form method="POST" id="uploadForm" enctype="multipart/form-data" class=" letra-negra font-400" onsubmit="editProd(event)">
                    <h4 class="letra-negra font-25 flex-center m12b"> Edite su producto   </h4>

                            <div class="flex-center m12b">
                                <img class="round-image" src="${product_info.imagen}" alt="">
                            </div>


                            <input id="image" type="file" name="avatar" class="font-400 m12b" accept="image/*" >

                            <div class="flex m12b">
                                <p class="no-flex">Ingrese el nombre:</p>
                                <input type="text" name="txt_product_name" class="change-contra" id="prod_name" value="${product_info.nombre}" required>
                            </div>
                            <div class="columna m12b">
                                <p class="flex-start ">Ingrese la descripción:</p>
                                <input type="text" name="txt_product_desc" class="change-contra" id="prod_desc" value="${product_info.descripcion}" required>
                            </div>
                            <div class="flex m12b">
                                <p class="no-flex">Ingrese el precio unitario: $</p>
                                <input type="number" name="txt_product_price" class="change-contra" id="prod_unit_price" value="${product_info.precio_unitario}" required>
                            </div>
                            <div class="flex m12b">
                               <p class="no-flex">Ingrese el stock :</p>
                                <input type="number" name="txt_product_stock" class="change-contra" id="prod_stock" value="${product_info.stock}" required>
                            </div>
                
                        <div class="flex-end">
                            <button class="btn__cancel ml-2" id="volver">Volver</button> 
                            <button class="btn__cancel btn__edit ml-2" id="edit">Editar</button> 
                        </div>
                    </div>
                </form>  
                </div>`


 
        localStorage.setItem('token_prod', product_info.token_prod);

        document.getElementById('uploadForm').addEventListener('submit', editProd);

        } else {
			update__prod.innerHTML = data.list.error;
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
	const response = await fetch("api/producto/getDetails/?prod=" + token_prod);
	/*< convierte la respuesta a formato json */
	const data = await response.json();
	console.log(data)
	return data;
}

async function editProd(event) {

    event.preventDefault(); // Evita que se recargue la página

    let prod_name = document.querySelector("#prod_name").value;
    let prod_desc = document.querySelector("#prod_desc").value;
    let prod_unit_price = document.querySelector("#prod_unit_price").value;
    let prod_stock = document.querySelector("#prod_stock").value;
    
    // La URL de la API a la que deseas hacer la solicitud
    const url = `${APP_URL_BASE}/api/producto/editMyProduct`;

    // Los datos que deseas enviar
    const data = {
        token: token_prod,
        name: prod_name,
        desc: prod_desc,                
        unit_price: prod_unit_price,
        stock: prod_stock
    };

    // Hacer la solicitud POST
    const response = await fetch(url, {
        method: "PUT",
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    });

    window.location.href = `${APP_URL_BASE}/myProducts`

}