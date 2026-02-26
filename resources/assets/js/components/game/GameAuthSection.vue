<template>
    <div class="auth-section">
        <div v-if="user" class="user-info">
            <img v-if="user.avatar_url" :src="user.avatar_url" :alt="user.name" class="user-avatar">
            <span class="user-name">{{ user.name }}</span>
            <span v-if="syncStatus" class="sync-status" :class="syncStatus">
                {{ syncStatusText }}
            </span>
            <form :action="logoutUrl" method="POST" class="logout-form">
                <input type="hidden" name="_token" :value="csrfToken">
                <button type="submit" class="logout-button">Logout</button>
            </form>
        </div>
        <div v-else-if="!loading" class="login-prompt">
            <a :href="loginUrl" class="login-button btn-osu">
                <span>Login with osu!</span>
            </a>
            <span class="login-hint">to sync your stats across devices</span>
        </div>
    </div>
</template>

<script>
import { ref, computed, inject, onMounted } from 'vue';

export default {
    name: 'GameAuthSection',
    props: {
        authMeUrl: {
            type: String,
            required: true,
        },
        loginUrl: {
            type: String,
            required: true,
        },
        logoutUrl: {
            type: String,
            required: true,
        },
        syncUrl: {
            type: String,
            required: true,
        },
        initialSyncUrl: {
            type: String,
            required: true,
        },
        authSuccess: {
            type: Boolean,
            default: false,
        },
        csrfToken: {
            type: String,
            required: true,
        },
        gameDate: {
            type: String,
            required: true,
        },
    },
    emits: ['auth-loaded', 'stats-synced', 'played-today'],
    setup(props, { emit }) {
        const axios = inject('axios');

        const loading = ref(true);
        const user = ref(null);
        const syncStatus = ref(null); // 'syncing', 'synced', 'error'

        const statsStorageKey = 'ppvr_game_stats';
        const storageKey = `ppvr_game_${props.gameDate}`;

        const syncStatusText = computed(() => {
            switch (syncStatus.value) {
                case 'syncing': return 'Syncing...';
                case 'synced': return 'Synced';
                case 'error': return 'Sync failed';
                default: return '';
            }
        });

        function loadLocalStats() {
            const saved = localStorage.getItem(statsStorageKey);
            if (saved) {
                const data = JSON.parse(saved);
                return {
                    gamesPlayed: data.gamesPlayed || 0,
                    totalCorrectRounds: data.totalCorrectRounds || 0,
                    currentStreak: data.currentStreak || 0,
                    maxStreak: data.maxStreak || 0,
                    roundBreakdown: data.roundBreakdown || [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                };
            }
            return null;
        }

        async function checkAuth() {
            try {
                const response = await axios.get(props.authMeUrl);
                if (response.data.authenticated) {
                    user.value = response.data.user;
                    return response.data;
                }
            } catch (error) {
                console.error('Failed to check auth:', error);
            }
            return null;
        }

        async function performInitialSync() {
            if (!user.value) return null;

            syncStatus.value = 'syncing';
            const localStats = loadLocalStats();

            try {
                const response = await axios.post(props.initialSyncUrl, {
                    localStats: localStats,
                }, {
                    headers: {
                        'X-CSRF-TOKEN': props.csrfToken,
                    },
                });

                const { action, stats: serverStats, playedToday } = response.data;

                syncStatus.value = 'synced';
                setTimeout(() => {
                    syncStatus.value = null;
                }, 2000);

                return { action, stats: serverStats, playedToday };
            } catch (error) {
                console.error('Failed to perform initial sync:', error);
                syncStatus.value = 'error';
                return null;
            }
        }

        async function syncStatsToServer(stats, gameRound = null) {
            if (!user.value) return;

            syncStatus.value = 'syncing';
            try {
                const payload = { ...stats };

                if (gameRound !== null) {
                    payload.gameRound = gameRound;
                }

                await axios.post(props.syncUrl, payload, {
                    headers: {
                        'X-CSRF-TOKEN': props.csrfToken,
                    },
                });
                syncStatus.value = 'synced';
                setTimeout(() => {
                    syncStatus.value = null;
                }, 2000);
            } catch (error) {
                console.error('Failed to sync stats:', error);
                syncStatus.value = 'error';
            }
        }

        function savePlayedTodayToLocal(playedToday) {
            if (playedToday) {
                localStorage.setItem(storageKey, JSON.stringify({
                    round: playedToday.round,
                    result: playedToday.won ? 'won' : 'lost',
                }));
            }
        }

        onMounted(async () => {
            const authData = await checkAuth();

            if (authData && props.authSuccess) {
                // User just logged in - perform initial sync
                const syncResult = await performInitialSync();
                if (syncResult) {
                    emit('stats-synced', {
                        action: syncResult.action,
                        stats: syncResult.stats,
                    });

                    if (syncResult.playedToday) {
                        savePlayedTodayToLocal(syncResult.playedToday);
                        emit('played-today', syncResult.playedToday);
                    }
                }
            } else if (authData) {
                // User was already logged in
                emit('stats-synced', {
                    action: 'existing',
                    stats: authData.stats,
                });

                if (authData.playedToday) {
                    savePlayedTodayToLocal(authData.playedToday);
                    emit('played-today', authData.playedToday);
                }
            }

            emit('auth-loaded', {
                user: user.value,
                authenticated: !!user.value,
            });

            loading.value = false;
        });

        return {
            loading,
            user,
            syncStatus,
            syncStatusText,
            syncStatsToServer,
        };
    },
};
</script>

<style scoped>
.auth-section {
    display: flex;
    justify-content: center;
    align-items: center;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background-color: #f8f8f8;
    border-radius: 8px;
}

[data-theme="dark"] .user-info {
    background-color: #2a2a2a;
}

.user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
}

.user-name {
    font-weight: 500;
    color: #333;
}

[data-theme="dark"] .user-name {
    color: #e0e0e0;
}

.sync-status {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
}

.sync-status.syncing {
    background-color: #fff3cd;
    color: #856404;
}

.sync-status.synced {
    background-color: #d4edda;
    color: #155724;
}

.sync-status.error {
    background-color: #f8d7da;
    color: #721c24;
}

.logout-form {
    margin: 0;
}

.logout-button {
    background: none;
    border: 1px solid #ddd;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.8rem;
    color: #666;
    transition: all 0.2s;
}

.logout-button:hover {
    background-color: #f0f0f0;
    border-color: #ccc;
}

[data-theme="dark"] .logout-button {
    border-color: #444;
    color: #a0a0a0;
}

[data-theme="dark"] .logout-button:hover {
    background-color: #3a3a3a;
    border-color: #555;
}

.login-prompt {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.login-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background-color: #ff66ab;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.2s;
}

.login-button:hover {
    background-color: #ff4499;
    text-decoration: none;
    color: white;
}

.login-hint {
    font-size: 0.8rem;
    color: #999;
}

[data-theme="dark"] .login-hint {
    color: #777;
}

/* Mobile: stack vertically */
@media (max-width: 639px) {
    .login-prompt {
        flex-direction: column;
        gap: 0.25rem;
    }
}
</style>
