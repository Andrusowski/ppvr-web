import { createApp } from 'vue'
import Chart from "./components/Chart.vue";

const eva = require('eva-icons');

createApp({})
    .component('chart', Chart)
    .mount('#app');

function search() {
    var name = document.getElementById('search').value;
    window.location = "http://localhost/ppvr-web/public/search/" + name;
}

eva.replace()
