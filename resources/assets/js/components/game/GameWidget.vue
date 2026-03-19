<template>
    <div class="game-container">
        <!-- User auth section -->
        <game-auth-section
            ref="authSection"
            :auth-me-url="authMeUrl"
            :login-url="loginUrl"
            :logout-url="logoutUrl"
            :sync-url="syncUrl"
            :initial-sync-url="initialSyncUrl"
            :delete-url="deleteUrl"
            :privacy-url="privacyUrl"
            :auth-success="authSuccess"
            :csrf-token="csrfToken"
            :game-date="gameData.date"
            class="uk-margin-bottom"
            @auth-loaded="onAuthLoaded"
            @stats-synced="onStatsSynced"
            @played-today="onPlayedToday"
            @stats-deleted="onStatsDeleted"
        />

        <!-- Progress bar -->
        <div class="uk-margin-bottom">
            <div class="uk-text-center uk-margin-small-bottom">
                Round {{ gameState === 'already_played' || gameState === 'finished' ? totalRounds : currentRound }} / {{ totalRounds }}
            </div>
            <div class="round-indicators uk-flex uk-flex-center">
                <div 
                    v-for="i in totalRounds" 
                    :key="i"
                    class="round-indicator"
                    :class="{
                        'active': currentRound === i && gameState === 'playing',
                        'correct': roundResults[i - 1] === true,
                        'wrong': roundResults[i - 1] === false
                    }"
                ></div>
            </div>
        </div>

        <!-- Game area -->
        <div v-if="gameState === 'playing'" class="game-area">
            <div class="posts-grid" :class="{ 'transitioning': isTransitioning || showResult }">
                <!-- Left post (fades out during transition) -->
                <div class="post-slot post-slot-left">
                    <div class="post-wrapper" :class="{ 'fade-out': isTransitioning }">
                        <game-post-card
                            :post="leftPost"
                            :selected="selectedPost === 'left'"
                            :show-result="showResult"
                            :is-correct="leftPost.score >= rightPost.score"
                            :show-score="true"
                            @select="selectPost('left')"
                        />
                    </div>
                </div>

                <!-- Right post (slides to left position during transition) -->
                <div class="post-slot post-slot-right" :class="{ 'slide-to-left': isTransitioning }">
                    <div class="post-wrapper">
                        <game-post-card
                            :post="rightPost"
                            :selected="selectedPost === 'right'"
                            :show-result="showResult"
                            :is-correct="rightPost.score >= leftPost.score"
                            :show-score="showResult"
                            @select="selectPost('right')"
                        />
                    </div>
                </div>

                <!-- Incoming post (slides in from right during transition) -->
                <div v-if="isTransitioning && incomingPost" class="post-slot post-slot-incoming slide-in-right">
                    <div class="post-wrapper">
                        <game-post-card
                            :post="incomingPost"
                            :selected="false"
                            :show-result="false"
                            :is-correct="false"
                            :show-score="false"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Game Over modal -->
        <Transition name="fade">
            <div v-if="gameState === 'finished'" class="uk-text-center uk-margin-large-top">
                <div class="uk-card uk-card-body result-card" :class="correctRoundsCount === totalRounds ? 'uk-card-primary' : 'uk-card-secondary'">
                    <h2 class="uk-card-title">{{ correctRoundsCount === totalRounds ? 'Perfect!' : 'Game Over' }}</h2>
                    <p>You got {{ correctRoundsCount }} out of {{ totalRounds }} rounds correct!</p>
                    <game-countdown :active="true" />
                </div>

                <game-stats
                    :stats="stats"
                    :total-rounds="totalRounds"
                    class="uk-margin-top"
                />

                <game-post-links
                    :posts="playedPosts"
                    title="Posts in today's game:"
                    author-prefix="posted by "
                    class="uk-margin-top"
                />
            </div>
        </Transition>

        <!-- Already played today -->
        <div v-if="gameState === 'already_played'" class="uk-text-center uk-margin-large-top">
            <div class="uk-card uk-card-body" :class="savedCorrect === totalRounds ? 'uk-card-primary' : 'uk-card-secondary'">
                <h2 class="uk-card-title">{{ savedCorrect === totalRounds ? 'Perfect!' : 'Already Played Today' }}</h2>
                <p v-if="savedCorrect === totalRounds">You won today's game with a perfect score, here is your unemployment certificate!</p>
                <p v-else>You got {{ savedCorrect }} out of {{ totalRounds }} rounds correct today.</p>
                <game-countdown :active="true" />
            </div>

            <game-stats
                :stats="stats"
                :total-rounds="totalRounds"
                class="uk-margin-top"
            />
        </div>
    </div>
</template>

<script>
import { ref, computed, onMounted, inject } from 'vue';
import { useGameState } from '../../composables/useGameState';

export default {
    name: 'GameWidget',
    props: {
        gameData: {
            type: Object,
            required: true,
        },
        validateUrl: {
            type: String,
            required: true,
        },
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
        isNewUser: {
            type: Boolean,
            default: false,
        },
        csrfToken: {
            type: String,
            required: true,
        },
    },
    setup(props) {
        const axios = inject('axios');

        const authSection = ref(null);

        const currentRound = ref(1);
        const totalRounds = ref(props.gameData.total_rounds);
        const posts = ref(props.gameData.posts);
        const gameState = ref('playing'); // 'playing', 'finished', 'already_played'
        const selectedPost = ref(null);
        const showResult = ref(false);
        const savedRound = ref(null);
        const savedCorrect = ref(null);
        const revealedPostIds = ref(new Set());
        const isTransitioning = ref(false);
        const roundResults = ref(Array(props.gameData.total_rounds).fill(null));

        const correctRoundsCount = computed(() => {
            return roundResults.value.filter(r => r === true).length;
        });

        // User state (set by auth section)
        const isAuthenticated = ref(false);

        // Statistics
        const { stats, loadStats, saveStats, applyStats, storageKey } = useGameState(props.gameData.date);

        const leftPost = computed(() => {
            return posts.value[currentRound.value - 1] || {};
        });

        const rightPost = computed(() => {
            return posts.value[currentRound.value] || {};
        });

        const playedPosts = computed(() => {
            return posts.value.slice(0, currentRound.value + 1);
        });

        const incomingPost = computed(() => {
            return posts.value[currentRound.value + 1] || null;
        });

        function updateStats(correctRounds) {
            stats.value.gamesPlayed++;
            stats.value.totalCorrectRounds += correctRounds;
            stats.value.roundBreakdown[correctRounds]++;

            // Check if the user played yesterday to maintain streak
            const today = props.gameData.date; // Format: YYYY-MM-DD
            const lastPlayed = stats.value.lastPlayedDate;

            if (lastPlayed) {
                const lastDate = new Date(lastPlayed);
                const todayDate = new Date(today);
                const diffTime = todayDate.getTime() - lastDate.getTime();
                const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24));

                if (diffDays === 1) {
                    // Played yesterday - continue streak
                    stats.value.currentStreak++;
                } else if (diffDays === 0) {
                    // Same day - shouldn't happen but don't change streak
                } else {
                    // Missed a day - reset streak to 1
                    stats.value.currentStreak = 1;
                }
            } else {
                // First time playing - start streak at 1
                stats.value.currentStreak = 1;
            }

            stats.value.lastPlayedDate = today;

            if (stats.value.currentStreak > stats.value.maxStreak) {
                stats.value.maxStreak = stats.value.currentStreak;
            }

            saveStats();

            // Sync with server if authenticated
            if (authSection.value && isAuthenticated.value) {
                authSection.value.syncStatsToServer(stats.value, roundResults.value);
            }
        }

        function loadProgress() {
            const saved = localStorage.getItem(storageKey);
            if (saved) {
                const data = JSON.parse(saved);
                if (data.result === 'finished') {
                    // Game was already completed today
                    savedRound.value = data.round;
                    savedCorrect.value = data.correctCount;
                    gameState.value = 'already_played';
                } else if (data.round) {
                    // Game in progress - restore round
                    currentRound.value = data.round;
                    
                    for (let i = 0; i < currentRound.value; i++) {
                        if (posts.value[i]) {
                            revealedPostIds.value.add(posts.value[i].id);
                        }
                    }
                }

                if (data.roundResults) {
                    roundResults.value = data.roundResults;
                }
            }
            return false;
        }

        function saveProgress() {
            const data = {
                round: currentRound.value,
                roundResults: roundResults.value,
                result: null,
            };
            localStorage.setItem(storageKey, JSON.stringify(data));
        }

        function saveResult(correctRounds) {
            const data = {
                round: totalRounds.value,
                correctCount: correctRounds,
                roundResults: roundResults.value,
                result: 'finished',
            };
            localStorage.setItem(storageKey, JSON.stringify(data));
            updateStats(correctRounds);
        }

        async function selectPost(side) {
            if (showResult.value || gameState.value !== 'playing' || isTransitioning.value) {
                return;
            }

            selectedPost.value = side;
            const chosenPostId = side === 'left' ? leftPost.value.id : rightPost.value.id;

            try {
                const response = await axios.post(props.validateUrl, {
                    round: currentRound.value,
                    chosen_post_id: chosenPostId,
                });

                showResult.value = true;
                revealedPostIds.value.add(leftPost.value.id);
                revealedPostIds.value.add(rightPost.value.id);
                
                const isCorrect = response.data.correct;
                roundResults.value[currentRound.value - 1] = isCorrect;

                setTimeout(() => {
                    if (currentRound.value >= totalRounds.value) {
                        gameState.value = 'finished';
                        saveResult(correctRoundsCount.value);
                    } else {
                        isTransitioning.value = true;

                        setTimeout(() => {
                            currentRound.value++;
                            selectedPost.value = null;
                            showResult.value = false;
                            isTransitioning.value = false;
                            saveProgress();
                        }, 500);
                    }
                }, 1500);
            } catch (error) {
                console.error('Error validating choice:', error);
            }
        }

        // Event handlers from GameAuthSection
        function onAuthLoaded(authData) {
            isAuthenticated.value = authData.authenticated;
        }

        function onStatsSynced(syncData) {
            if (syncData.stats && syncData.stats.gamesPlayed > 0) {
                applyStats(syncData.stats);
                saveStats();
            }
        }

        function onPlayedToday(playedTodayData) {
            if (gameState.value === 'playing' && playedTodayData) {
                savedRound.value = playedTodayData.round;
                savedCorrect.value = playedTodayData.correctCount;
                if (playedTodayData.roundResults) {
                    roundResults.value = playedTodayData.roundResults;
                }
                gameState.value = 'already_played';
            }
        }

        function onStatsDeleted() {
            // Server stats were deleted, but local stats are kept
            // No need to reset stats - they remain in localStorage and in memory
            // The user can continue playing with their local stats
        }

        onMounted(() => {
            loadStats();
            loadProgress();
        });

        return {
            authSection,
            currentRound,
            totalRounds,
            posts,
            leftPost,
            rightPost,
            incomingPost,
            playedPosts,
            gameState,
            selectedPost,
            showResult,
            savedRound,
            savedCorrect,
            correctRoundsCount,
            roundResults,
            revealedPostIds,
            isTransitioning,
            stats,
            selectPost,
            onAuthLoaded,
            onStatsSynced,
            onPlayedToday,
            onStatsDeleted,
        };
    },
};
</script>

<style scoped>
.game-container {
    max-width: 900px;
    margin: 0 auto;
}

.round-indicators {
    gap: 0.5rem;
}

.round-indicator {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background-color: #e5e5e5;
    border: 2px solid #ccc;
    transition: all 0.3s ease;
}

.round-indicator.active {
    border-color: #1e87f0;
    transform: scale(1.2);
}

.round-indicator.correct {
    background-color: #32d296;
    border-color: #32d296;
}

.round-indicator.wrong {
    background-color: #f0506e;
    border-color: #f0506e;
}

.game-area {
    position: relative;
}

.posts-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.post-slot {
    position: relative;
    min-height: 200px;
    min-width: 0; /* Allow grid items to shrink below content size */
}

.transitioning {
    pointer-events: none;
}

/* Mobile: stack vertically */
@media (max-width: 639px) {
    .posts-grid {
        grid-template-columns: 1fr;
    }
}

.post-wrapper {
    height: 100%;
}

/* Left post fade out animation */
.post-wrapper.fade-out {
    animation: fadeOut 0.5s ease forwards;
}

@keyframes fadeOut {
    to {
        opacity: 0;
    }
}

/* Right post slides to left position */
.post-slot-right.slide-to-left {
    animation: slideToLeft 0.5s ease forwards;
}

@keyframes slideToLeft {
    to {
        transform: translateX(calc(-100% - 1rem));
    }
}

/* Mobile: slide up instead of left */
@media (max-width: 639px) {
    @keyframes slideToLeft {
        to {
            transform: translateY(calc(-100% - 1rem));
        }
    }
}

/* Incoming post slides in from right */
.post-slot-incoming {
    position: absolute;
    top: 0;
    right: 0;
    width: calc(50% - 0.5rem);
}

.post-slot-incoming.slide-in-right {
    animation: slideInRight 0.5s ease forwards;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Mobile: incoming post slides in from bottom */
@media (max-width: 639px) {
    .post-slot-incoming {
        position: absolute;
        top: auto;
        bottom: 0;
        right: 0;
        left: 0;
        width: 100%;
    }

    @keyframes slideInRight {
        from {
            transform: translateY(100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
}

.result-card {
    animation: pop-in 0.4s ease-out;
}

/* Fade transition for modals */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Pop-in animation for result cards */
@keyframes pop-in {
    0% {
        opacity: 0;
        transform: scale(0.8);
    }
    70% {
        transform: scale(1.05);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}
</style>
