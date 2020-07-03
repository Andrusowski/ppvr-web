import Vue from "vue";
import Chart from "./components/Chart";

const eva = require('eva-icons');

Vue.component('chart', Chart);

const app = new Vue({
    el: '#app'
});

function search() {
    var name = document.getElementById('search').value;
    window.location = "http://localhost/ppvr-web/public/search/" + name;
}

eva.replace()
