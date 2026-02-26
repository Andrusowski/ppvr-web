<template>
    <Teleport to="body">
        <Transition name="modal-fade">
            <div v-if="visible" class="modal-overlay" @click.self="$emit('cancel')">
                <div class="modal-container">
                    <h3 class="modal-title">Sync Game Statistics</h3>

                    <div class="modal-body">
                        <!-- Case: Server has stats, local has stats -->
                        <template v-if="hasServerStats && hasLocalStats">
                            <p>You have game stats on both this device and your account.</p>

                            <div class="stats-comparison">
                                <div class="stats-box">
                                    <h4>This Device</h4>
                                    <ul class="stats-list">
                                        <li><strong>{{ localStats.gamesPlayed }}</strong> games played</li>
                                        <li><strong>{{ localStats.maxStreak }}</strong> max streak</li>
                                    </ul>
                                </div>
                                <div class="stats-box">
                                    <h4>Your Account</h4>
                                    <ul class="stats-list">
                                        <li><strong>{{ serverStats.gamesPlayed }}</strong> games played</li>
                                        <li><strong>{{ serverStats.maxStreak }}</strong> max streak</li>
                                    </ul>
                                </div>
                            </div>

                            <p class="choice-prompt">Which statistics would you like to keep?</p>

                            <div class="button-group">
                                <button class="btn btn-primary" @click="$emit('choose', 'server')">
                                    Use Account Stats
                                </button>
                                <button class="btn btn-secondary" @click="$emit('choose', 'local')">
                                    Use Device Stats
                                </button>
                            </div>
                        </template>

                        <!-- Case: Server has stats, local is empty -->
                        <template v-else-if="hasServerStats && !hasLocalStats">
                            <p>Your account has existing game statistics.</p>

                            <div class="stats-box stats-box-single">
                                <h4>Your Account</h4>
                                <ul class="stats-list">
                                    <li><strong>{{ serverStats.gamesPlayed }}</strong> games played</li>
                                    <li><strong>{{ serverStats.maxStreak }}</strong> max streak</li>
                                </ul>
                            </div>

                            <p>Would you like to load these statistics on this device?</p>

                            <div class="button-group">
                                <button class="btn btn-primary" @click="$emit('choose', 'server')">
                                    Yes, Load Stats
                                </button>
                                <button class="btn btn-secondary" @click="$emit('choose', 'none')">
                                    No, Start Fresh
                                </button>
                            </div>
                        </template>

                        <!-- Case: Local has stats, server is empty -->
                        <template v-else-if="!hasServerStats && hasLocalStats">
                            <p>You have game statistics on this device.</p>

                            <div class="stats-box stats-box-single">
                                <h4>This Device</h4>
                                <ul class="stats-list">
                                    <li><strong>{{ localStats.gamesPlayed }}</strong> games played</li>
                                    <li><strong>{{ localStats.maxStreak }}</strong> max streak</li>
                                </ul>
                            </div>

                            <p>These will be synced to your account.</p>

                            <div class="button-group">
                                <button class="btn btn-primary" @click="$emit('choose', 'local')">
                                    Got it!
                                </button>
                            </div>
                        </template>

                        <!-- Case: Neither has stats -->
                        <template v-else>
                            <p>Welcome! Your game statistics will be synced to your account automatically.</p>

                            <div class="button-group">
                                <button class="btn btn-primary" @click="$emit('choose', 'none')">
                                    Got it!
                                </button>
                            </div>
                        </template>
                    </div>

                    <p class="privacy-note">
                        Your data is handled according to our
                        <a :href="privacyUrl" target="_blank">Privacy Policy</a>.
                    </p>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script>
import { computed } from 'vue';

export default {
    name: 'SyncConsentModal',
    props: {
        visible: {
            type: Boolean,
            default: false,
        },
        localStats: {
            type: Object,
            default: null,
        },
        serverStats: {
            type: Object,
            default: null,
        },
        privacyUrl: {
            type: String,
            required: true,
        },
    },
    emits: ['choose', 'cancel'],
    setup(props) {
        const hasLocalStats = computed(() => {
            return props.localStats && props.localStats.gamesPlayed > 0;
        });

        const hasServerStats = computed(() => {
            return props.serverStats && props.serverStats.gamesPlayed > 0;
        });

        return {
            hasLocalStats,
            hasServerStats,
        };
    },
};
</script>

<style scoped>
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
    max-width: 450px;
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

.stats-comparison {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1rem;
}

.stats-box {
    background-color: var(--surface-bg, #f5f5f5);
    border-radius: 8px;
    padding: 1rem;
}

.stats-box-single {
    max-width: 200px;
    margin: 0 auto 1rem auto;
}

.stats-box h4 {
    margin: 0 0 0.5rem 0;
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-muted, #666);
}

.stats-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.stats-list li {
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
}

.stats-list strong {
    color: var(--accent-primary, #ff66ab);
}

.choice-prompt {
    font-weight: 500;
    text-align: center;
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

.btn-primary {
    background-color: var(--color-osu, #ff66ab);
    color: white;
}

.btn-primary:hover {
    background-color: var(--color-osu-hover, #ff4499);
}

.btn-secondary {
    background-color: var(--surface-bg, #e0e0e0);
    color: var(--text-color, #333);
    border: 1px solid var(--card-border, #ccc);
}

.btn-secondary:hover {
    background-color: var(--surface-hover, #d0d0d0);
}

.privacy-note {
    margin: 1rem 0 0 0;
    font-size: 0.8rem;
    color: var(--text-muted, #666);
    text-align: center;
}

.privacy-note a {
    color: var(--link-color, #ff66ab);
}

.privacy-note a:hover {
    text-decoration: underline;
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

.modal-fade-enter-active .modal-container,
.modal-fade-leave-active .modal-container {
    transition: transform 0.2s ease;
}

.modal-fade-enter-from .modal-container,
.modal-fade-leave-to .modal-container {
    transform: scale(0.95);
}

@media (max-width: 500px) {
    .stats-comparison {
        grid-template-columns: 1fr;
    }
}
</style>
