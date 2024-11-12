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
  const response = await fetch("api/grafico/getProfits/");
  /*< convierte la respuesta a formato json */
  const data = await response.json();

  return data;
}

// Llamada a la función asincrona que obtiene un listado de usuarios
getGraphic().then(data => {

  const { list } = data;

  const date = []; 
  const values = []; 

  for (let i = 0; i < list.length; i++) {
    date.push(list[i]["fecha"]);
    list[i]["totalXdiaProds"] = list[i]["totalXdiaProds"] == null ? 0 : parseInt(list[i]["totalXdiaProds"]);
    list[i]["totalXdiaTapa"] = list[i]["totalXdiaTapa"] == null ? 0 : parseInt(list[i]["totalXdiaTapa"]);
    values.push(list[i]["totalXdiaProds"] + list[i]["totalXdiaTapa"]);
  }

  var datos = {
    labels: date,
    datasets: [{
      label: 'Obtenido',
      data: values,
      backgroundColor: '#5AC25B',
      borderColor: 'green', // Cambia el color de la línea
      fill: false, // No rellena el área bajo la línea
      tension: 0.1, // Ajusta la suavidad de la línea
      borderWidth: 2 // Ancho del borde de la línea
    }]
  };
  
  // Configuración del gráfico
  var config = {
    type: 'line', // Cambiar tipo a 'line'
    data: datos,
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'top',
          labels: {
            color: 'black'
          }
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              return context.dataset.label + ': $' + context.raw;
            }
          },
          backgroundColor: '#5AC25B',
          titleColor: 'white',
          bodyColor: 'white'
        }
      },
      scales: {
        x: {
          ticks: {
            color: 'black'
          },
          title: {
            color: 'black'
          }
        },
        y: {
          ticks: {
            color: 'black'
          },
          title: {
            color: 'black'
          }
        }
      },
      maintainAspectRatio: false
    }
  };
  
  // Obtener el contenedor del gráfico
  var chartContainer = document.getElementById('chart-container-profits');
  
  // Crear el gráfico
  var myChart = new Chart(
    document.getElementById('donutChart-profits'),
    config
  );
  
  chartContainer.style.position = 'relative';
  chartContainer.style.width = '100%';
  chartContainer.style.height = '100%';
  
  // Función para actualizar el tamaño del gráfico cuando cambia el tamaño del contenedor
  function updateChartSize() {
    myChart.resize();
  }
  
  // Actualizar el tamaño del gráfico al cargar la página y al cambiar el tamaño de la ventana
  window.addEventListener('load', updateChartSize);
  window.addEventListener('resize', updateChartSize);
});
