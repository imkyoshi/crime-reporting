console.log("arechart.js loaded");

function renderBarChart(data) {
    var barChartCanvas = $('#barChart').get(0).getContext('2d');

    var barData = {
        labels: data.months.map(function (monthYear) {
            // Corrected the date format
            var dateObj = new Date(monthYear + "-01");
            var month = dateObj.toLocaleString('en-us', { month: 'long' });
            var year = dateObj.getFullYear();
            return month + ' ' + year;
        }),
        datasets: [
            {
                label: 'Crime Reports',
                data: data.data,
                backgroundColor: '#00c0ef', // Set to the desired color
            }
        ]
    };

    var barOptions = {
        maintainAspectRatio: false,
        responsive: true,
        scales: {
            x: { stacked: true },
            y: { stacked: true }
        }
    };

    new Chart(barChartCanvas, {
        type: 'bar',
        data: barData,
        options: barOptions
    });
}
