loadPage()

/**
 * @brief Carga elementos en el listado
 */
function loadPage() {

    /*< Limpia el listado */
    const listadoEstaciones = document.querySelector("#listado__estaciones");
    listadoEstaciones.innerHTML = "";

    const template = document.querySelector("#template_estacion");

    /*< Llamada a la función asincrónica que obtiene un listado de estaciones */
    getEstaciones().then(data => {

        /*< Si hay elementos en el listado */
        if (data && data.length > 0) {

            /*< Recorre el listado de las estaciones */
            data.forEach(row => {
                /*< Clona el template */
                const clone = template.content.cloneNode(true);
                
                /*< Modifica los elementos del clone con los datos de cada estación meteorologica*/
                clone.querySelector(".content_estacion").href = `detalle?chipid=${row.chipid}`;
                clone.querySelector(".estacion_nombre").textContent = row.apodo;
                clone.querySelector(".visitas-count").textContent = row.visitas;
                clone.querySelector(".estacion_ubicacion").textContent = row.ubicacion;
                
                /*< Añade el clone al listado */
                listadoEstaciones.appendChild(clone);
            });

        } else {
            listadoEstaciones.innerHTML = data.list.error;
        }
    });
}

/**
 * @brief Retorna un listado de usuarios en formato JSON
 * @param {int} inicio desde que fila inicia
 * @param {int} cantidad cantidad de filas a listar
 * @return {json} listado de usuarios
 */
async function getEstaciones() {
    /*< Consulta a la API */
    const response = await fetch("https://mattprofe.com.ar/proyectos/app-estacion/datos.php?mode=list-stations"); 
    /*< Convierte la respuesta a formato JSON */
    const data = await response.json();
    console.log(data);
    return data;
}
