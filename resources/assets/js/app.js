import { createApp } from 'vue'
import Chart from "./components/Chart.vue";
import BarChart from "./components/BarChart.vue";
import VerticalChart from "./components/VerticalChart.vue";
import Search from "./components/Search.vue";
import axios from 'axios';
import VueAxios from 'vue-axios';

const eva = require('eva-icons');

const app = createApp({})
app.use(VueAxios, axios)
    .provide('axios', app.config.globalProperties.axios)
    .component('chart', Chart)
    .component('bar-chart', BarChart)
    .component('vertical-chart', VerticalChart)
    .component('search', Search)
    .mount('#app');

function search() {
    var name = document.getElementById('search').value;
    window.location = "http://localhost/ppvr-web/public/search/" + name;
}

eva.replace()
