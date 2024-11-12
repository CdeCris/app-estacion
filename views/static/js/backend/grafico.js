/**
 * 
 * @brief Retorna un listado de usuarios en formato JSON
 * @param int inicio desde que fila inicia.
 * @param int cantidad cantidad de filas a listar
 * @return json listado de usuarios
 * 
 * */
async function getGraphic() {
  /*< consulta a la API */
  const response = await fetch("api/grafico/getData/");
  /*< convierte la respuesta a formato json */
  const data = await response.json();

  return data;
}

// Llamada a la función asincrona que obtiene un listado de usuarios
getGraphic().then(data => {
  const { list } = data

  const colors = []; 
  const values = []; 
  const rgbs = [];

  for (let i = 0; i < list.length; i++) {
    colors.push(list[i]["color"]);
    values.push(list[i]["stock"]);
    rgbs.push(list[i]["valor_rgb"]);
  }

  var datos = {
    labels: colors,
    datasets: [{
      label: 'CANTIDAD DE TAPAS',/*En caso de que sea grafico de barra se puede poner un label sobre lo que estas mostrando*/
      data: values,
      backgroundColor: rgbs,
      borderWidth: 1 //Ancho del borde de las barras
    }]
  };
  
    // Configuración del gráfico
    var config = {
        type: 'bar',
        data: datos,
        options: {
            indexAxis: 'y', // Y: barras horizontales ; X: barras verticales
            responsive: true, // Hace que el gráfico sea responsive
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        color: 'black' // Cambia el color del texto de la leyenda
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.raw;
                        }
                    },
                    // Configuración del color del texto del tooltip
                    backgroundColor: '#333', // Color de fondo del tooltip
                    titleColor: 'white', // Color del texto del título del tooltip
                    bodyColor: 'blue' // Color del texto del cuerpo del tooltip
                }
            },
            scales: {
                x: {
                    ticks: {
                        color: 'black', // Color del texto en el eje X
                    },
                    title: {
                        color: 'black' // Color del título del eje X
                    }
                },
                y: {
                    ticks: {
                        color: 'black', // Color del texto en el eje Y
                    },
                    title: {
                        color: 'white' // Color del título del eje Y
                    }
                }
            },
            maintainAspectRatio: false // Permite que el gráfico ajuste su aspecto
        }
    };
  
    // Obtener el contenedor del gráfico
    var chartContainer = document.getElementById('chart-container');
  
    // Crear el gráfico
    var myChart = new Chart(
        document.getElementById('donutChart'),
        config
    );
  
    chartContainer.style.position = 'relative';
    chartContainer.style.width = '100%';
    chartContainer.style.height = '100%';
  
    // Función para actualizar el tamaño del gráfico cuando cambia el tamaño del contenedor
    function updateChartSize() {
        // No es necesario ajustar manualmente el tamaño con Chart.js si es responsive
        myChart.resize();
    }
  
    // Actualizar el tamaño del gráfico al cargar la página y al cambiar el tamaño de la ventana
    window.addEventListener('load', updateChartSize);
    window.addEventListener('resize', updateChartSize);
});