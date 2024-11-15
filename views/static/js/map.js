const map = L.map('map').setView([-27.4692131, -58.8306349], 2);

const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19
}).addTo(map);


/* Obtenemos el resultado de la peticion a la api */
loadTracker().then( ({list}) => {

	/* Obtenemos el tracker de datos */
	tracker = list.tracker

    /* Recorremos el tracker por fila */
    tracker.forEach( fila => {

        /* Recuperamos la información necesaria para colocar los marcadores */
        let latitud = fila["latitud"];
        let longitud = fila["longitud"];
        let accesos = fila["accesos"];

        /* Genera un marcador con un popup dentro del mapa*/
        const marker = L.marker([latitud, longitud]).addTo(map)
        .bindPopup('Accesos: '+accesos)
        .openPopup();
    })
})

/**
 * 
 * Función asincrona para acceder al listado que tiene las latitudes
 * y longitudes a pintar como marcadores en el mapa
 * 
 * */
async function loadTracker(){
    const response = await fetch("/alumno/6904/app-estacion/api/tracker/listClientsLocation");
    const data = await response.json();

    return data;
}
