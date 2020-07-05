<template>
    <canvas :ref="name" height="70"></canvas>
</template>

<script>
    import Chart from 'chart.js';

    export default {
        props: {
            posts:  {
                type: String,
                required: true,
                validator: function (val) {
                    try {
                        JSON.parse(val);
                    } catch (e) {
                        return false;
                    }
                    return true;
                }
            },
            name: {
                type: String,
                required: true,
            },
            title: {
                type: String,
            },
            valueIndex: {
                type: String,
                required: true,
            },
            unit: {
                type: String,
                required: true,
            },
            reverse: {
                type: Boolean,
                default: false,
            },
            color: {
                type: String,
                default: 'rgb(255, 99, 132)',
            },
            xAxesDisplay: {
                type: Boolean,
                default: false,
            },
            yAxesDisplay: {
                type: Boolean,
                default: false,
            }
        },
        mounted() {
            const ctx = this.$refs[this.name].getContext('2d');
            const chartdata = this.prepareChartData();

            new Chart(ctx, {
                type: 'line',
                data: {
                    datasets: [{
                        borderColor: this.color,
                        data: chartdata,
                        pointRadius: 0,
                        fill: false
                    }]
                },
                options: {
                    scales: {
                        xAxes: [{
                            type: 'linear',
                            position: 'bottom',
                            ticks: {
                                precision:1
                            },
                            display: this.xAxesDisplay
                        }],
                        yAxes: [{
                            ticks: {
                                precision:1,
                                reverse: this.reverse
                            },
                            display: this.yAxesDisplay
                        }]
                    },
                    legend: {
                        display: false
                    },
                    tooltips: {
                        mode: 'index',
                        callbacks: {
                            title: (tooltipItem, data) => {
                                var days = data.datasets[0].data.length - tooltipItem[0].xLabel;
                                return days + ' days ago';
                            },
                            label: (tooltipItem) => {
                                return this.unit + ': ' + tooltipItem.yLabel;
                            }
                        },
                        intersect: false,
                        titleFontFamily: '',
                        displayColors: false
                    }
                }
            });
        },
        methods: {
            prepareChartData: function() {
                const posts = JSON.parse(this.posts);
                const chartdata = [];

                posts.forEach((item, index) => {
                    chartdata.push({
                        x: index,
                        y: item[this.valueIndex]
                    });
                });

                return chartdata;
            }
        }
    }
</script>
