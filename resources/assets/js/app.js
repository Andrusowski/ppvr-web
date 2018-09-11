
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import 'particles.js/particles';
const particlesJS = window.particlesJS;

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example-component', require('./components/ExampleComponent.vue'));

const app = new Vue({
    el: '#app'
});

particlesJS.load('particles-js', base_url+'/js/particles/particlesjs-config.json');

function search() {
    var name = document.getElementById('search').value;
    window.location = "http://localhost/ppvr-web/public/search/" + name;
}
