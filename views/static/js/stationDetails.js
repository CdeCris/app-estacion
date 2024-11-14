// Obtener la cadena de consulta (query string) de la URL
const queryString = window.location.search;

// Crear un objeto URLSearchParams a partir de la cadena de consulta
const urlParams = new URLSearchParams(queryString);

// Obtener el valor de un parámetro específico
const chipid = urlParams.get('chipid');

let myChart; 

const buttons = {
    btn_tem : document.querySelector("#btn_tem"),
    btn_vie : document.querySelector("#btn_vie"),
    btn_hum : document.querySelector("#btn_hum"),
    btn_pre : document.querySelector("#btn_pre"),
    btn_rin : document.querySelector("#btn_rin")
}

setInterval(() => {
    setChart({ value: localStorage.getItem("stat") });
}, 60000);

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

            estacion = [data[0]]
            /*< Recorre el listado de las estaciones */
            estacion.forEach(row => {
                
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
    const response = await fetch(`https://mattprofe.com.ar/proyectos/app-estacion/datos.php?chipid=${chipid}&cant=7`); 
    /*< Convierte la respuesta a formato JSON */
    const data = await response.json();

    return data;
}

/*< si no existe el grafico, setea el de temperatura */
if (!myChart) { setChart({value : "tem"}) }

function setChart(btn) {

    stat = btn.value

    setSelectedButton(stat)

    localStorage.setItem("stat",stat)

    getEstacionByChipid(chipid).then(data => {

        console.log(data)

        const temperatura = []
        const viento = []
        const humedad = []
        const presion = []
        const riesgo_incendio = []
        const hora = []

        for (let i = data.length - 1; i > 1; i--) {
            temperatura.push(data[i].temperatura) 
            viento.push(data[i].viento) 
            humedad.push(data[i].humedad) 
            presion.push(data[i].presion) 
            riesgo_incendio.push(data[i].fwi) 
            hora_stats = data[i].fecha.split(" ")[1].slice(0,5)
            hora.push(hora_stats) 
        }

        const values = {
            tem : temperatura,
            vie : viento,
            hum : humedad,
            pre : presion,
            rin : riesgo_incendio
        }

        const colors = {
            tem : "rgb(255 164 99)",
            vie : "rgb(151 216 255)",
            hum : "rgb(71 177 239)",
            pre : "rgb(87 235 99)",
            rin : "rgb(255 99 99"
        }

        var datos = {
            labels: hora,
            datasets: [{
                label: '',/*En caso de querer se puede poner un label sobre lo que estas mostrando*/
                data: values[stat],
                backgroundColor: colors[stat],
                borderWidth: 2 //Ancho del borde de las barras
            }]
        };

        let max_chart_y = Math.max(...values[stat]) > 10 ? Math.max(...values[stat]) * 1.2 : 10

        let min_chart_y = Math.min(...values[stat]) == 0 ? 0 : Math.min(...values[stat]) * 0.8

        // Configuración del gráfico
        var config = {
            type: 'line', /*doughnut - bar - line*/
            data: datos,
            options: {
                indexAxis: 'x', //Y: barras horizontales ; X: barras verticales
                plugins: {
                    legend: {
                        display: false // se desactiva la leyenda
                    }
                },
                scales: {
                    y: {
                        min: min_chart_y, // Establece el mínimo del eje Y (puedes ajustarlo según tus necesidades)
                        max: max_chart_y, // Establece el máximo del eje Y (ajústalo también)
                        ticks: {
                            stepSize: 2 // Controla el tamaño de los saltos entre los valores del eje Y
                        }
                    }
                }
            }
        };

        // Obtener el contenedor del grÃ¡fico
        var chartContainer = document.getElementById('chart-container');

        // Si existe, destruye el grafico
        if (myChart) { myChart.destroy(); }

        //Crea el grafico
        myChart = new Chart(
            document.getElementById('donutChart'),
            config
        );

        // Actualizar el tamaÃ±o del grÃ¡fico al cargar la pÃ¡gina y al cambiar el tamaÃ±o de la ventana
        window.addEventListener('load', updateChartSize);
        window.addEventListener('resize', updateChartSize);

    })
}

// FunciÃ³n para actualizar el tamaÃ±o del grÃ¡fico cuando cambia el tamaÃ±o del contenedor
function updateChartSize() {
    var containerWidth = 800;
    var containerHeight = 300;

    myChart.canvas.parentNode.style.width = containerWidth + 'px';
    myChart.canvas.parentNode.style.height = containerHeight + 'px';
    myChart.canvas.width = containerWidth;
    myChart.canvas.height = containerHeight;
    myChart.resize();
}

function setSelectedButton(btn_value_selected) {
    Object.values(buttons).forEach((btn)=>{
        if (!(btn.value == btn_value_selected)) {
            btn.classList.remove("btn_selected")
        }else{
            btn.classList.add("btn_selected")
        }
    })
}