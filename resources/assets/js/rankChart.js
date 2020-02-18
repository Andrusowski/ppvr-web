import Chart from 'chart.js';

var ctx = document.getElementById('rankHistory').getContext('2d');
var chartdata = [];
ranks.forEach(function (item, index) {
    chartdata.push({
        x: index,
        y: item.rank
    });
});
console.table(chartdata);
var myChart = new Chart(ctx, {
    type: 'line',
    data: {
        datasets: [{
            backgroundColor: '#FF66AB',
            borderColor: 'rgb(255, 99, 132)',
            data: chartdata,
            pointRadius: 0,
            pointHitRadius: 5,
            pointHoverRadius: 8,
            fill: false
        }]
    },
    options: {
        scales: {
            xAxes: [{
                type: 'linear',
                position: 'bottom',
                ticks: {
                    precision:0
                },
                display: false
            }],
            yAxes: [{
                ticks: {
                    precision:0,
                    reverse: true
                },
                display: false
            }]
        },
        legend: {
            display: false
        },
        tooltips: {
            mode: 'single',
            callbacks: {
                title: function(tooltipItem, data) {
                    var days = data.datasets[0].data.length - tooltipItem[0].xLabel;
                    return days + ' days ago';
                },
                label: function(tooltipItem) {
                    return tooltipItem.yLabel;
                }
            },
            titleFontFamily: ''
        }
    }
});

console.log(myChart);