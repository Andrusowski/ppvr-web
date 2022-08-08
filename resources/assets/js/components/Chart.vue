<template>
    <Line
        :chart-options="chartOptions"
        :chart-data="chartData"
        :height="100"
    />
</template>

<script>
    import { Line } from 'vue-chartjs';
    import { Chart as ChartJS, Title, Tooltip, Legend, PointElement, LinearScale, LineElement} from 'chart.js'

    ChartJS.register(Title, Tooltip, Legend, PointElement, LinearScale, LineElement)

    export default {
        name: 'Chart',
        components: { Line },
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

            posts.forEach((item, index) => {
                chartdata.push({
                    x: index,
                    y: item[props.valueIndex]
                });
            });

            return {
                chartData: {
                    datasets: [{
                        borderColor: props.color,
                        data: chartdata,
                        pointRadius: 0,
                        fill: false
                    }]
                },
                chartOptions: {
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
