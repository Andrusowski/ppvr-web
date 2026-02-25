<template>
    <div v-if="stats.gamesPlayed > 0" class="stats-container">
        <h3 class="stats-title">Your Statistics</h3>
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-value">{{ stats.gamesPlayed }}</span>
                <span class="stat-label">Games Played</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">{{ averageCorrectRounds }}</span>
                <span class="stat-label">Avg. Correct</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">{{ stats.currentStreak }}</span>
                <span class="stat-label">Current Streak</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">{{ stats.maxStreak }}</span>
                <span class="stat-label">Max Streak</span>
            </div>
        </div>
        <div class="breakdown-container">
            <h4 class="breakdown-title">Round Breakdown</h4>
            <round-breakdown-chart
                :breakdown="stats.roundBreakdown"
                :highlight-index="totalRounds"
            />
        </div>
    </div>
</template>

<script>
import { computed } from 'vue';

export default {
    name: 'GameStats',
    props: {
        stats: {
            type: Object,
            required: true,
        },
        totalRounds: {
            type: Number,
            required: true,
        },
    },
    setup(props) {
        const averageCorrectRounds = computed(() => {
            if (props.stats.gamesPlayed === 0) return 0;
            return (props.stats.totalCorrectRounds / props.stats.gamesPlayed).toFixed(1);
        });

        return {
            averageCorrectRounds,
        };
    },
};
</script>

<style scoped>
.stats-container {
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
    padding: 1rem;
    background-color: #f8f8f8;
    border-radius: 8px;
}

[data-theme="dark"] .stats-container {
    background-color: #2a2a2a;
}

.stats-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0 0 1rem 0;
    color: #333;
}

[data-theme="dark"] .stats-title {
    color: #e0e0e0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.5rem;
    margin-bottom: 1.5rem;
}

@media (max-width: 500px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

.stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0.5rem;
    background-color: #fff;
    border-radius: 4px;
}

[data-theme="dark"] .stat-item {
    background-color: #3a3a3a;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: bold;
    color: #1e87f0;
}

.stat-label {
    font-size: 0.7rem;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 0.25rem;
}

[data-theme="dark"] .stat-label {
    color: #a0a0a0;
}

.breakdown-container {
    margin-top: 1rem;
}

.breakdown-title {
    font-size: 0.9rem;
    font-weight: 600;
    margin: 0 0 0.75rem 0;
    color: #333;
}

[data-theme="dark"] .breakdown-title {
    color: #e0e0e0;
}
</style>
