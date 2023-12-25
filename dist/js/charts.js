// donut-chart.js

// Function to render the donut chart
function renderDonutChart(data) {
  // Get context with jQuery - using jQuery's .get() method.
  var donutChartCanvas = $('#donutChart').get(0).getContext('2d');

  var donutData = {
      labels: data.labels,
      datasets: [
          {
              data: data.data,
              backgroundColor : ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'],
          }
      ]
  };

  var donutOptions = {
      maintainAspectRatio: false,
      responsive: true,
  };

  // Create pie or doughnut chart
  new Chart(donutChartCanvas, {
      type: 'doughnut',
      data: donutData,
      options: donutOptions
  });
}


// window.onload = function () {
//     renderDonutChart(donutChartData);
//   };