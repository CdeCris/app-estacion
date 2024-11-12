
/*< variables para la paginación*/
let cantidad = 6;
let inicio = 0;
let url = 'api/producto/getMyProducts/'

/*< carga inicial de elementos en el listado*/
loadPage(url);

/*< si se presiona el botón de avanzar a los siguientes elementos del listado*/
mover.addEventListener("click", e => {
	inicio = inicio + cantidad;
	loadPage(url);
})
volver.addEventListener("click", e => {
	inicio = inicio - cantidad;
	loadPage(url);
})
// tapas.addEventListener("click", e => {
// 	url = "api/producto/getTapas/"
// 	loadPage(url);
// })
// products.addEventListener("click", e => {
// 	url = "api/producto/getMyProducts/"
// 	loadPage(url);
// })


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
		if (data.list.errno != 411) {

			document.querySelector("#cant__products").innerText = `${data.list.length}`
			
			// Recorre el listado de usuarios fila por fila
			data.list.forEach(row => {

			let estado = row.activo ? 'ACTIVO' : 'INACTIVO';


				document.querySelector("#product__list").innerHTML += `

		            <div class="card_productos_admin" id="${row.token}">
		                <div class="card-header-productos-admin letra-negra txt-center font-17 font-400" title="${row.titulo}">
		                    ${row.titulo}
		                </div>
		                <div class="card-body ">
		                    <div class="contenedor_produ  columna-center">
		                        <img class="round-image" src="${row.imagen}" alt="">
		                    </div>
		                    <div class="flex m12t">
		                        <h4 class="font-9 letra-negra m12t border-right p12R" title="${row.cant_vista}"> ${row.cant_vista} vistas</h4>
		                        <h4 class="font-9 letra-negra m12t" title="${row.cant_vendido} ">${row.cant_vendido} ventas
		                        </h4>

		                    </div>
		                    <div class="flex-end m12t">
		                        <span class="letra-negra font-12 m12t no-flex">FECHA PUBLICACIÓN:  <p class="letra-verde p12L">${row.fecha_publicacion}</p></span>
		                    </div>
		                    <div class="flex-end m12t">
		                        <span class="letra-negra font-12 m12t no-flex">STOCK: <p class="letra-verde p12L">${row.stock}</p> </span>
		                    </div>
		                    <div class="columna-center">
		                        <span class="letra-negra flex  m12t font-17"
		                            title="$${row.precio_unitario},00">$${row.precio_unitario},00</span>
		                    </div>
		                </div>
                		<div class="card-footer-productos-admin p10">
                    		<div class="flex-justify">
                        	<div class="flex-center">
                            	<label class="content-input">
                                <span class="letra-negra">Pausar</span>
                               	 <input  type="checkbox" name="Vehiculo" id="autopista" >
                            	</label>
                        </div>
                        <div class="flex-end">
							<button class="btn" title="Eliminar producto" id="btnEliminar" data-product-id="${row.token}"><span class="fa fa-trash letra-roja font-17"></span></button>                            <button class="btn" id="edit__prod" data-token="${row.token}" title="Editar producto" onclick="editProd(this)"><span class="fa fa-pen-to-square letra-naranja font-17"></span></button>
                        </div>
    
                        
                    </div>
   
                </div>

		            </div>


		        `

			});
		} else {
			product__list.innerHTML = data.list.error;
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


function editProd(btn) {
	let product_token = btn.getAttribute("data-token");	
	let url = "https://mattprofe.com.ar/alumno/6904/Innovplast/editarProducto?prod=" + product_token
	window.location.href = url
}
document.addEventListener('DOMContentLoaded', () => {
    const btnEliminar = document.getElementById('btnEliminar');
    if (btnEliminar) {
        btnEliminar.addEventListener('click', mostrarPopupConfirmacion);
    }
});

function mostrarPopupConfirmacion(event) {
    const token = event.currentTarget.dataset.productId; // Obtiene el token del botón

    Swal.fire({
        title: '¿Estás seguro de eliminar este producto?',
        text: 'No podrás revertir esta acción',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No, cancelar',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        didOpen: () => {
            const confirmButton = Swal.getConfirmButton();
            const cancelButton = Swal.getCancelButton();

            confirmButton.id = 'boton-confirmar'; // Asigna un ID al botón de confirmar
            cancelButton.id = 'boton-cancelar';   // Asigna un ID al botón de cancelar
        }
    }).then((result) => {
        if (result.isConfirmed) {
            eliminarProducto(token); // Pasa el token a la función
            Swal.fire('¡Eliminado!', 'El producto ha sido eliminado.', 'success');
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire('Cancelado', 'La acción ha sido cancelada.', 'error');
        }
    });
}

async function eliminarProducto(token) {
    const url = `${APP_URL_BASE}/api/producto/deleteMyProd?token=${token}`; // Construye la URL con el token

    try {
        const response = await fetch(url, {
            method: 'DELETE', // Asegúrate de que el método sea el correcto
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token_prod')}` // Si necesitas un token de autorización
            }
        });

        if (response.ok) {
            const data = await response.json(); // Convierte la respuesta a formato JSON
            console.log(data);
            // Aquí puedes actualizar la interfaz para reflejar la eliminación
        } else {
            console.error('Error al eliminar el producto:', response.status);
        }
    } catch (error) {
        console.error('Error de red:', error);
    }
}

function mostrarPopupConfirmacion(productId) {
    Swal.fire({
        title: '¿Estás seguro de eliminar este producto?',
        text: 'No podrás revertir esta acción',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No, cancelar',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6'
    }).then((result) => {
        if (result.isConfirmed) {
            eliminarProducto(productId); 
            Swal.fire('¡Eliminado!', 'El producto ha sido eliminado.', 'success');
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire('Cancelado', 'La acción ha sido cancelada.', 'error');
        }
    });
}
document.querySelector("#product__list").addEventListener('click', function(e) {
    if (e.target && e.target.id === 'btnEliminar') {
        const productId = e.target.getAttribute('data-product-id');
        mostrarPopupConfirmacion(productId); 
    }
});


//<button popovertarget="tarjeta${row.token}" popovertargetaction="toggle" class="myButton btn-comprar">Comprar</button>

// <div id="tarjeta${row.token}" popover>
// 	<div class="product--img">
// 		<img src="${row.imagen}" class="${row.color}" alt="">
// 	</div>
// 	<div class="product--info">
// 		<div class="product--price">
// 			<span>$</span><span class="product--pricevalue value${row.token}">${row.precio_unitario}</span>
// 		</div>
// 		<div class="product--name">
// 			${row.titulo}
// 		</div>
// 	</div>
//     <div class="product--cart">
// 	    <section class="product__chart">
//         	<input type="number" name="num_cant" id="input__product__cant__${row.token}" class="product__cant" value="1" min="1" max="${row.stock}">
//             <button type="submit" name="btn_submit" id="submit__product__cant__${row.token}" class="product__add" onclick="addChart(this)">ADD</button>
//        </section>
//     	<div class="product--subtotal">
//     		SubTotal: <span id="subtotal${row.token}"></span>
//     	</div>
// 	</div>
// </div>