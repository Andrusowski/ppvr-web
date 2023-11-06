import { createApp } from 'vue'
import Chart from "./components/Chart.vue";
import BarChart from "./components/BarChart.vue";
import VerticalChart from "./components/VerticalChart.vue";

const eva = require('eva-icons');

createApp({})
    .component('chart', Chart)
    .component('bar-chart', BarChart)
    .component('vertical-chart', VerticalChart)
    .mount('#app');

function search() {
    var name = document.getElementById('search').value;
    window.location = "http://localhost/ppvr-web/public/search/" + name;
}

eva.replace()
