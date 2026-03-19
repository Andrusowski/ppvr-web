import { ref, computed } from 'vue';
import { STATS_STORAGE_KEY, getProgressStorageKey, DEFAULT_STATS } from '../constants/game';

export function useGameState(gameDate) {
    const stats = ref({ ...DEFAULT_STATS });
    const storageKey = getProgressStorageKey(gameDate);

    function applyStats(newStats) {
        stats.value = {
            gamesPlayed: newStats.gamesPlayed || 0,
            totalCorrectRounds: newStats.totalCorrectRounds || 0,
            currentStreak: newStats.currentStreak || 0,
            maxStreak: newStats.maxStreak || 0,
            roundBreakdown: newStats.roundBreakdown || [...DEFAULT_STATS.roundBreakdown],
            lastPlayedDate: newStats.lastPlayedDate || null,
        };
    }

    function loadStats() {
        const saved = localStorage.getItem(STATS_STORAGE_KEY);
        if (saved) {
            try {
                const data = JSON.parse(saved);
                applyStats(data);
                return data;
            } catch (e) {
                console.error('Failed to parse stats', e);
            }
        }
        return null;
    }

    function saveStats() {
        localStorage.setItem(STATS_STORAGE_KEY, JSON.stringify(stats.value));
    }

    function clearLocalStats() {
        localStorage.removeItem(STATS_STORAGE_KEY);
        localStorage.removeItem(storageKey);
    }

    return {
        stats,
        storageKey,
        applyStats,
        loadStats,
        saveStats,
        clearLocalStats
    };
}
