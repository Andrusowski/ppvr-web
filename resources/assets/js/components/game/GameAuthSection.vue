<template>
    <div class="auth-section">
        <div v-if="user" class="user-info">
            <img v-if="user.avatar_url" :src="user.avatar_url" :alt="user.name" class="user-avatar">
            <span class="user-name">{{ user.name }}</span>
            <span v-if="syncStatus" class="sync-status" :class="syncStatus">
                {{ syncStatusText }}
            </span>
            <button
                type="button"
                class="delete-button"
                title="Delete my data"
                @click="showDeleteConfirm = true"
            >
                <i class="fas fa-trash"></i>
            </button>
            <form :action="logoutUrl" method="POST" class="logout-form">
                <input type="hidden" name="_token" :value="csrfToken">
                <button type="submit" class="logout-button">Logout</button>
            </form>
        </div>
        <div v-else-if="!loading" class="login-prompt">
            <a :href="loginUrl" class="login-button btn-osu">
                <span>Login with osu!</span>
            </a>
            <span class="login-hint">
                to sync your stats across devices.
                <a :href="privacyUrl" target="_blank" class="privacy-link">Privacy Policy</a>
            </span>
        </div>

        <!-- Sync Consent Modal -->
        <sync-consent-modal
            :visible="showSyncModal"
            :local-stats="pendingLocalStats"
            :server-stats="pendingServerStats"
            :privacy-url="privacyUrl"
            @choose="handleSyncChoice"
            @cancel="handleSyncCancel"
        />

        <!-- Delete Confirmation Modal -->
        <Teleport to="body">
            <Transition name="modal-fade">
                <div v-if="showDeleteConfirm" class="modal-overlay" @click.self="showDeleteConfirm = false">
                    <div class="modal-container">
                        <h3 class="modal-title">Delete Your Data</h3>
                        <div class="modal-body">
                            <p>Are you sure you want to delete your account and all game statistics?</p>
                            <p class="warning-text">This will permanently delete your account. This action cannot be undone.</p>
                            <p class="info-text">Your local statistics on this device will be kept.</p>
                            <div class="button-group">
                                <button
                                    class="btn btn-danger"
                                    :disabled="deleting"
                                    @click="deleteData"
                                >
                                    {{ deleting ? 'Deleting...' : 'Yes, Delete My Account' }}
                                </button>
                                <button
                                    class="btn btn-secondary"
                                    :disabled="deleting"
                                    @click="showDeleteConfirm = false"
                                >
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
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
        deleteUrl: {
            type: String,
            required: true,
        },
        privacyUrl: {
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
    emits: ['auth-loaded', 'stats-synced', 'played-today', 'stats-deleted'],
    setup(props, { emit }) {
        const axios = inject('axios');

        const loading = ref(true);
        const user = ref(null);
        const syncStatus = ref(null); // 'syncing', 'synced', 'error'

        // Sync consent modal state
        const showSyncModal = ref(false);
        const pendingLocalStats = ref(null);
        const pendingServerStats = ref(null);
        const pendingPlayedToday = ref(null);

        // Delete confirmation state
        const showDeleteConfirm = ref(false);
        const deleting = ref(false);

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
                    lastPlayedDate: data.lastPlayedDate || null,
                };
            }
            return null;
        }

        function saveLocalStats(stats) {
            localStorage.setItem(statsStorageKey, JSON.stringify(stats));
        }

        function clearLocalStats() {
            localStorage.removeItem(statsStorageKey);
            // Also clear today's game progress
            localStorage.removeItem(storageKey);
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

        async function fetchServerStats() {
            try {
                const response = await axios.post(props.initialSyncUrl, {
                    localStats: null, // Don't send local stats yet, just fetch server state
                });
                return response.data;
            } catch (error) {
                console.error('Failed to fetch server stats:', error);
                return null;
            }
        }

        async function uploadStatsToServer(stats) {
            syncStatus.value = 'syncing';
            try {
                const response = await axios.post(props.syncUrl, stats);
                syncStatus.value = 'synced';
                setTimeout(() => {
                    syncStatus.value = null;
                }, 2000);
                return response.data;
            } catch (error) {
                console.error('Failed to upload stats:', error);
                syncStatus.value = 'error';
                return null;
            }
        }

        async function syncStatsToServer(stats, gameRound = null, gameCorrectCount = null, gameRoundResults = null) {
            if (!user.value) return;

            syncStatus.value = 'syncing';
            try {
                const payload = { ...stats };

                if (gameRound !== null) {
                    payload.gameRound = gameRound;
                }
                
                if (gameCorrectCount !== null) {
                    payload.gameCorrectCount = gameCorrectCount;
                }
                
                if (gameRoundResults !== null) {
                    payload.gameRoundResults = gameRoundResults;
                }

                await axios.post(props.syncUrl, payload);
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
                    result: playedToday.won ? 'finished' : 'lost',
                    correctCount: playedToday.correctCount,
                    roundResults: playedToday.roundResults
                }));
            }
        }

        async function handleSyncChoice(choice) {
            showSyncModal.value = false;

            if (choice === 'server') {
                if (pendingServerStats.value) {
                    saveLocalStats(pendingServerStats.value);
                    emit('stats-synced', {
                        action: 'use_server',
                        stats: pendingServerStats.value,
                    });
                }
            } else if (choice === 'local') {
                if (pendingLocalStats.value) {
                    await uploadStatsToServer(pendingLocalStats.value);
                    emit('stats-synced', {
                        action: 'uploaded',
                        stats: pendingLocalStats.value,
                    });
                }
            } else {
                // 'none' - don't sync, keep things as they are
                // For new users, this just means we won't sync until they play
                emit('stats-synced', {
                    action: 'none',
                    stats: pendingLocalStats.value || pendingServerStats.value || {
                        gamesPlayed: 0,
                        totalCorrectRounds: 0,
                        currentStreak: 0,
                        maxStreak: 0,
                        roundBreakdown: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                        lastPlayedDate: null,
                    },
                });
            }

            // Always check if user has played today on the server (authoritative source)
            // This prevents playing multiple times across devices
            if (pendingPlayedToday.value) {
                savePlayedTodayToLocal(pendingPlayedToday.value);
                emit('played-today', pendingPlayedToday.value);
            }

            pendingLocalStats.value = null;
            pendingServerStats.value = null;
            pendingPlayedToday.value = null;

            loading.value = false;
        }

        function handleSyncCancel() {
            // User dismissed modal - treat as "don't sync"
            handleSyncChoice('none');
        }

        async function deleteData() {
            deleting.value = true;
            try {
                await axios.delete(props.deleteUrl);

                // Note: We intentionally keep local stats - only server data is deleted
                // This respects the user's right to erasure while preserving their local experience

                // Clear user state since account was deleted and user is logged out
                user.value = null;
                showDeleteConfirm.value = false;

                // Emit event so parent knows account was deleted
                emit('stats-deleted');
            } catch (error) {
                console.error('Failed to delete data:', error);
                syncStatus.value = 'error';
            } finally {
                deleting.value = false;
            }
        }

        onMounted(async () => {
            const authData = await checkAuth();

            if (authData && props.authSuccess) {
                // User just logged in - need to show sync consent modal
                const localStats = loadLocalStats();
                const serverData = await fetchServerStats();

                const serverStats = serverData?.stats || null;
                const playedToday = serverData?.playedToday || null;

                const hasLocal = localStats && localStats.gamesPlayed > 0;
                const hasServer = serverStats && serverStats.gamesPlayed > 0;

                if (hasLocal || hasServer) {
                    pendingLocalStats.value = localStats;
                    pendingServerStats.value = serverStats;
                    pendingPlayedToday.value = playedToday;
                    showSyncModal.value = true;
                } else {
                    // Neither has stats - no need for consent, just proceed
                    emit('stats-synced', {
                        action: 'none',
                        stats: {
                            gamesPlayed: 0,
                            totalCorrectRounds: 0,
                            currentStreak: 0,
                            maxStreak: 0,
                            roundBreakdown: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                            lastPlayedDate: null,
                        },
                    });
                    loading.value = false;
                }
            } else if (authData) {
                // User was already logged in - use server stats
                const serverData = await fetchServerStats();
                const serverStats = serverData?.stats || null;
                const playedToday = serverData?.playedToday || null;

                if (serverStats) {
                    saveLocalStats(serverStats);
                    emit('stats-synced', {
                        action: 'existing',
                        stats: serverStats,
                    });
                }

                if (playedToday) {
                    savePlayedTodayToLocal(playedToday);
                    emit('played-today', playedToday);
                }

                loading.value = false;
            } else {
                loading.value = false;
            }

            emit('auth-loaded', {
                user: user.value,
                authenticated: !!user.value,
            });
        });

        return {
            loading,
            user,
            syncStatus,
            syncStatusText,
            showSyncModal,
            pendingLocalStats,
            pendingServerStats,
            showDeleteConfirm,
            deleting,
            syncStatsToServer,
            handleSyncChoice,
            handleSyncCancel,
            deleteData,
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
    background-color: var(--panel-bg);
    border-radius: 8px;
    flex-wrap: wrap;
    justify-content: center;
}

.user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
}

.user-name {
    font-weight: 500;
    color: var(--text-color);
}

.sync-status {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
}

.sync-status.syncing {
    background-color: var(--status-syncing-bg);
    color: var(--status-syncing-text);
}

.sync-status.synced {
    background-color: var(--status-synced-bg);
    color: var(--status-synced-text);
}

.sync-status.error {
    background-color: var(--status-error-bg);
    color: var(--status-error-text);
}

.logout-form {
    margin: 0;
}

.logout-button,
.delete-button {
    background: none;
    border: 1px solid var(--card-border);
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.8rem;
    color: var(--text-muted);
    transition: all 0.2s;
}

.logout-button:hover,
.delete-button:hover {
    background-color: var(--surface-hover);
    border-color: var(--card-border);
}

.delete-button {
    color: var(--color-danger, #dc3545);
    border-color: var(--color-danger, #dc3545);
}

.delete-button:hover {
    background-color: var(--color-danger, #dc3545);
    color: white;
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
    background-color: var(--color-osu);
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.2s;
}

.login-button:hover {
    background-color: var(--color-osu-hover);
    text-decoration: none;
    color: white;
}

.login-hint {
    font-size: 0.8rem;
    color: var(--text-subtle);
}

.privacy-link {
    color: var(--link-color, #ff66ab);
    text-decoration: underline;
}

.privacy-link:hover {
    color: var(--link-hover-color, #ff4499);
}

/* Delete confirmation modal */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    padding: 1rem;
}

.modal-container {
    background-color: var(--panel-bg, #fff);
    border-radius: 12px;
    padding: 1.5rem;
    max-width: 400px;
    width: 100%;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.modal-title {
    margin: 0 0 1rem 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-color, #333);
    text-align: center;
}

.modal-body {
    color: var(--text-color, #333);
}

.modal-body p {
    margin: 0 0 1rem 0;
    line-height: 1.5;
}

.warning-text {
    color: var(--color-danger, #dc3545);
    font-weight: 500;
}

.info-text {
    color: var(--text-muted, #666);
    font-size: 0.9rem;
}

.button-group {
    display: flex;
    gap: 0.75rem;
    justify-content: center;
    flex-wrap: wrap;
}

.btn {
    padding: 0.6rem 1.25rem;
    border-radius: 6px;
    font-size: 0.95rem;
    font-weight: 500;
    cursor: pointer;
    border: none;
    transition: all 0.2s;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-danger {
    background-color: var(--color-danger, #dc3545);
    color: white;
}

.btn-danger:hover:not(:disabled) {
    background-color: #c82333;
}

.btn-secondary {
    background-color: var(--surface-bg, #e0e0e0);
    color: var(--text-color, #333);
    border: 1px solid var(--card-border, #ccc);
}

.btn-secondary:hover:not(:disabled) {
    background-color: var(--surface-hover, #d0d0d0);
}

/* Modal transition */
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.2s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}

/* Mobile: stack vertically */
@media (max-width: 639px) {
    .login-prompt {
        flex-direction: column;
        gap: 0.25rem;
    }

    .user-info {
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>
