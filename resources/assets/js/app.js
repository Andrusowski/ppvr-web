import { createApp } from 'vue'
import Chart from "./components/Chart.vue";
import BarChart from "./components/BarChart.vue";
import VerticalChart from "./components/VerticalChart.vue";
import Search from "./components/Search.vue";
import GameWidget from "./components/game/GameWidget.vue";
import GameAuthSection from "./components/game/GameAuthSection.vue";
import SyncConsentModal from "./components/game/SyncConsentModal.vue";
import RoundBreakdownChart from "./components/RoundBreakdownChart.vue";
import GamePostCard from "./components/game/GamePostCard.vue";
import GameStats from "./components/game/GameStats.vue";
import GamePostLinks from "./components/game/GamePostLinks.vue";
import GameCountdown from "./components/game/GameCountdown.vue";
import DarkModeToggle from "./components/DarkModeToggle.vue";
import axios from 'axios';
import VueAxios from 'vue-axios';

const eva = require('eva-icons');

// Configure axios to use Laravel's XSRF-TOKEN cookie for CSRF protection.
// This ensures the token stays fresh even if the page is left open overnight.
axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Handle 419 CSRF token mismatch errors by reloading the page.
// This can happen if the session expires while the page is open.
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response && error.response.status === 419) {
            // CSRF token mismatch - reload page to get fresh token
            window.location.reload();
            return new Promise(() => {}); // Never resolve to prevent further error handling
        }
        return Promise.reject(error);
    }
);

const app = createApp({})
app.use(VueAxios, axios)
    .provide('axios', app.config.globalProperties.axios)
    .component('chart', Chart)
    .component('bar-chart', BarChart)
    .component('vertical-chart', VerticalChart)
    .component('search', Search)
    .component('game-widget', GameWidget)
    .component('game-auth-section', GameAuthSection)
    .component('sync-consent-modal', SyncConsentModal)
    .component('round-breakdown-chart', RoundBreakdownChart)
    .component('game-post-card', GamePostCard)
    .component('game-stats', GameStats)
    .component('game-post-links', GamePostLinks)
    .component('game-countdown', GameCountdown)
    .component('dark-mode-toggle', DarkModeToggle)
    .mount('#app');

function search() {
    var name = document.getElementById('search').value;
    window.location = "http://localhost/ppvr-web/public/search/" + name;
}

eva.replace()
