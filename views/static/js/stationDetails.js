// Obtener la cadena de consulta (query string) de la URL
const queryString = window.location.search;

// Crear un objeto URLSearchParams a partir de la cadena de consulta
const urlParams = new URLSearchParams(queryString);

// Obtener el valor de un parámetro específico
const chipid = urlParams.get('chipid');

loadPage()

/**
 * @brief Carga elementos en el listado
 */
function loadPage() {

    /*< Limpia el listado */
    const detallEstacion = document.querySelector("#detalle__estacion");
    detallEstacion.innerHTML = "";

    const template = document.querySelector("#template_estacion");

    /*< Llamada a la función asincrónica que obtiene un listado de estaciones */
    getEstacionByChipid(chipid).then(data => {

        /*< Si hay elementos en el listado */
        if (data && data.length > 0) {

            /*< Recorre el listado de las estaciones */
            data.forEach(row => {
                
                /*< Clona el template */
                const clone = template.content.cloneNode(true);
                
                /*< Modifica los elementos del clone con los datos de cada estación meteorologica*/
                clone.querySelector(".estacion_nombre").textContent = row.estacion;
                clone.querySelector(".estacion_ubicacion").textContent = row.ubicacion;
                
                /*< Añade el clone al listado */
                detallEstacion.appendChild(clone);
            });

        } else {
            detallEstacion.innerHTML = data.list.error;
        }
    });
}

/**
 * @brief Retorna un listado de usuarios en formato JSON
 * @param {int} inicio desde que fila inicia
 * @param {int} cantidad cantidad de filas a listar
 * @return {json} listado de usuarios
 */
async function getEstacionByChipid(chipid) {
    /*< Consulta a la API */
    const response = await fetch(`https://mattprofe.com.ar/proyectos/app-estacion/datos.php?chipid=${chipid}&cant=1`); 
    /*< Convierte la respuesta a formato JSON */
    const data = await response.json();
    console.log(data);
    return data;
}
