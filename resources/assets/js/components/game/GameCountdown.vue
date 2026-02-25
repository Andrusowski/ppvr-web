<template>
    <div v-if="countdown" class="countdown-container">
        <p class="uk-text-meta">Next game in:</p>
        <p class="countdown-timer">{{ countdown }}</p>
    </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue';

export default {
    name: 'GameCountdown',
    props: {
        active: {
            type: Boolean,
            default: true,
        },
    },
    setup(props) {
        const countdown = ref('');
        let countdownInterval = null;

        function updateCountdown() {
            const now = new Date();
            const nextMidnightUtc = new Date(Date.UTC(
                now.getUTCFullYear(),
                now.getUTCMonth(),
                now.getUTCDate() + 1,
                0, 0, 0, 0
            ));
            const diff = nextMidnightUtc - now;

            const hours = Math.floor(diff / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

            countdown.value = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }

        function startCountdown() {
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
            updateCountdown();
            countdownInterval = setInterval(updateCountdown, 1000);
        }

        function stopCountdown() {
            if (countdownInterval) {
                clearInterval(countdownInterval);
                countdownInterval = null;
            }
        }

        onMounted(() => {
            if (props.active) {
                startCountdown();
            }
        });

        onUnmounted(() => {
            stopCountdown();
        });

        return {
            countdown,
        };
    },
};
</script>

<style scoped>
.countdown-container {
    margin-top: 1rem;
}

.countdown-timer {
    font-size: 2rem;
    font-weight: bold;
    font-family: 'Courier New', Courier, monospace;
    color: #1e87f0;
    margin: 0.5rem 0 0 0;
    letter-spacing: 2px;
}
</style>
