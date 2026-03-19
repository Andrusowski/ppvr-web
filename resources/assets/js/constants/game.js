export const STATS_STORAGE_KEY = 'ppvr_game_stats_v2';

export function getProgressStorageKey(date) {
    return `ppvr_game_v2_${date}`;
}

export const DEFAULT_STATS = {
    gamesPlayed: 0,
    totalCorrectRounds: 0,
    currentStreak: 0,
    maxStreak: 0,
    roundBreakdown: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
    lastPlayedDate: null,
};
