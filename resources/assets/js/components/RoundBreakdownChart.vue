<template>
    <Bar
        :chart-options="chartOptions"
        :chart-data="chartData"
        :height="height"
    />
</template>

<script>
import { ref, computed, watch } from 'vue';
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

export default {
    name: 'RoundBreakdownChart',
    components: { Bar },
    props: {
        breakdown: {
            type: Array,
            required: true,
            validator: (val) => val.length === 11,
        },
        highlightIndex: {
            type: Number,
            default: 10,
        },
        height: {
            type: Number,
            default: 150,
        },
    },
    setup(props) {
        const labels = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10'];

        const chartData = computed(() => {
            const backgroundColors = props.breakdown.map((_, index) =>
                index === props.highlightIndex ? '#32d296' : '#1e87f0'
            );

            return {
                labels: labels,
                datasets: [{
                    data: props.breakdown,
                    backgroundColor: backgroundColors,
                    borderRadius: 3,
                }],
            };
        });

        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    callbacks: {
                        title: (context) => {
                            const rounds = context[0].label;
                            return rounds === '10' ? 'Perfect games' : `${rounds} correct rounds`;
                        },
                        label: (context) => {
                            const count = context.raw;
                            return count === 1 ? '1 game' : `${count} games`;
                        },
                    },
                    displayColors: false,
                },
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                    },
                    title: {
                        display: true,
                        text: 'Correct Rounds',
                        font: {
                            size: 11,
                        },
                    },
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                    },
                },
            },
        };

        return {
            chartData,
            chartOptions,
        };
    },
};
</script>
