<template>
    <Bar
        :chart-options="chartOptions"
        :chart-data="chartData"
        :height="700"
    />
</template>

<script>
    import { Bar } from 'vue-chartjs';
    import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale} from 'chart.js'

    ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

    export default {
        name: 'Chart',
        components: { Bar },
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
                required: false,
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
        setup(props) {
            const posts = JSON.parse(props.posts);
            const chartdata = [];
            const labels = []

            posts.forEach((item, index) => {
                labels.push(index);
                chartdata.push({
                    y: index,
                    x: item[props.valueIndex]
                });
            });

            return {
                chartData: {
                    labels: labels,
                    datasets: [{
                        axis: 'y',
                        backgroundColor: props.color,
                        data: chartdata,
                        pointRadius: 0,
                        fill: false
                    }]
                },
                chartOptions: {
                    type: 'bar',
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                title: (context) => {
                                    const days = context[0].dataset.data.length - context[0].raw.x;
                                    return days + ' days ago';
                                },
                                label: (context) => {
                                    return props.unit ? props.unit + ' ' + context.raw.y : context.raw.y;
                                }
                            },
                            titleFont: '',
                            displayColors: false
                        }
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        x: {
                            type: 'linear',
                            position: 'bottom',
                            ticks: {
                                precision:1
                            },
                            display: props.xAxesDisplay
                        },
                        y: {
                            ticks: {
                                precision:1,
                            },
                            reverse: props.reverse,
                            display: props.yAxesDisplay
                        }
                    }
                }
            }
        }
    }
</script>
