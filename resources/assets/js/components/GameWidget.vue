<template>
    <div class="game-container">
        <!-- Progress bar -->
        <div class="uk-margin-bottom">
            <div class="uk-text-center uk-margin-small-bottom">
                Round {{ currentRound }} / {{ totalRounds }}
            </div>
            <progress class="uk-progress" :value="gameState === 'won' ? totalRounds : currentRound - 1" :max="totalRounds"></progress>
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
                            :show-score="showResult || revealedPostIds.has(leftPost.id)"
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

        <!-- Victory modal -->
        <Transition name="fade">
            <div v-if="gameState === 'won'" class="uk-text-center uk-margin-large-top">
                <div class="uk-card uk-card-primary uk-card-body result-card">
                    <h2 class="uk-card-title">Victory!</h2>
                    <p>You got all {{ totalRounds }} rounds correct!</p>
                    <game-countdown :active="true" />
                </div>

                <game-stats
                    :stats="stats"
                    :total-rounds="totalRounds"
                    class="uk-margin-top"
                />

                <game-post-links
                    :posts="posts"
                    title="Posts in today's game:"
                    author-prefix="by "
                    class="uk-margin-top"
                />
            </div>
        </Transition>

        <!-- Loss modal -->
        <Transition name="fade">
            <div v-if="gameState === 'lost'" class="uk-text-center uk-margin-large-top">
                <div class="uk-card uk-card-secondary uk-card-body result-card">
                    <h2 class="uk-card-title">Game Over</h2>
                    <p>You made it to round {{ currentRound }} of {{ totalRounds }}.</p>
                    <game-countdown :active="true" />
                </div>

                <game-stats
                    :stats="stats"
                    :total-rounds="totalRounds"
                    class="uk-margin-top"
                />

                <game-post-links
                    :posts="playedPosts"
                    title="Posts you encountered:"
                    author-prefix="posted by "
                    class="uk-margin-top"
                />
            </div>
        </Transition>

        <!-- Already played today -->
        <div v-if="gameState === 'already_played'" class="uk-text-center uk-margin-large-top">
            <div class="uk-card uk-card-default uk-card-body">
                <h2 class="uk-card-title">Already Played Today</h2>
                <p v-if="savedResult === 'won'">You won today's game!</p>
                <p v-else>You reached round {{ savedRound }} of {{ totalRounds }}.</p>
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
    },
    setup(props) {
        const axios = inject('axios');

        const currentRound = ref(1);
        const totalRounds = ref(props.gameData.total_rounds);
        const posts = ref(props.gameData.posts);
        const gameState = ref('playing'); // 'playing', 'won', 'lost', 'already_played'
        const selectedPost = ref(null);
        const showResult = ref(false);
        const savedResult = ref(null);
        const savedRound = ref(null);
        const revealedPostIds = ref(new Set());
        const isTransitioning = ref(false);

        // Statistics
        const statsStorageKey = 'ppvr_game_stats';
        const stats = ref({
            gamesPlayed: 0,
            totalCorrectRounds: 0,
            currentStreak: 0,
            maxStreak: 0,
            roundBreakdown: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], // Index = correct rounds (0-10)
        });

        const storageKey = `ppvr_game_${props.gameData.date}`;

        const leftPost = computed(() => {
            return posts.value[currentRound.value - 1] || {};
        });

        const rightPost = computed(() => {
            return posts.value[currentRound.value] || {};
        });

        // Posts encountered so far (for loss modal - shows posts up to and including current round)
        const playedPosts = computed(() => {
            return posts.value.slice(0, currentRound.value + 1);
        });

        // The post that will slide in from the right during transition
        const incomingPost = computed(() => {
            return posts.value[currentRound.value + 1] || null;
        });

        function loadStats() {
            const saved = localStorage.getItem(statsStorageKey);
            if (saved) {
                const data = JSON.parse(saved);
                stats.value = {
                    gamesPlayed: data.gamesPlayed || 0,
                    totalCorrectRounds: data.totalCorrectRounds || 0,
                    currentStreak: data.currentStreak || 0,
                    maxStreak: data.maxStreak || 0,
                    roundBreakdown: data.roundBreakdown || [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                };
            }
        }

        function saveStats() {
            localStorage.setItem(statsStorageKey, JSON.stringify(stats.value));
        }

        function updateStats(correctRounds, won) {
            stats.value.gamesPlayed++;
            stats.value.totalCorrectRounds += correctRounds;
            stats.value.roundBreakdown[correctRounds]++;

            if (won) {
                stats.value.currentStreak++;
                if (stats.value.currentStreak > stats.value.maxStreak) {
                    stats.value.maxStreak = stats.value.currentStreak;
                }
            } else {
                stats.value.currentStreak = 0;
            }

            saveStats();
        }

        function loadProgress() {
            const saved = localStorage.getItem(storageKey);
            if (saved) {
                const data = JSON.parse(saved);
                if (data.result) {
                    // Game was already completed today
                    savedResult.value = data.result;
                    savedRound.value = data.round;
                    gameState.value = 'already_played';
                    return true;
                } else if (data.round) {
                    // Game in progress - restore round and mark previous posts as revealed
                    currentRound.value = data.round;
                    // Mark all posts from previous rounds as revealed
                    for (let i = 0; i < currentRound.value; i++) {
                        if (posts.value[i]) {
                            revealedPostIds.value.add(posts.value[i].id);
                        }
                    }
                }
            }
            return false;
        }

        function saveProgress() {
            const data = {
                round: currentRound.value,
                result: null,
            };
            localStorage.setItem(storageKey, JSON.stringify(data));
        }

        function saveResult(result, correctRounds) {
            const data = {
                round: currentRound.value,
                result: result,
            };
            localStorage.setItem(storageKey, JSON.stringify(data));
            updateStats(correctRounds, result === 'won');
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

                if (response.data.correct) {
                    // Correct choice
                    setTimeout(() => {
                        if (currentRound.value >= totalRounds.value) {
                            // Won the game - all 10 rounds correct
                            gameState.value = 'won';
                            saveResult('won', totalRounds.value);
                        } else {
                            // Start transition animation
                            isTransitioning.value = true;

                            // After animation completes, update the round
                            setTimeout(() => {
                                currentRound.value++;
                                selectedPost.value = null;
                                showResult.value = false;
                                isTransitioning.value = false;
                                saveProgress();
                            }, 500);
                        }
                    }, 1500);
                } else {
                    // Wrong choice - game over
                    // Correct rounds = currentRound - 1 (failed on current round)
                    setTimeout(() => {
                        gameState.value = 'lost';
                        saveResult('lost', currentRound.value - 1);
                    }, 1500);
                }
            } catch (error) {
                console.error('Error validating choice:', error);
            }
        }

        onMounted(() => {
            loadStats();
            loadProgress();
        });

        return {
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
            savedResult,
            savedRound,
            revealedPostIds,
            isTransitioning,
            stats,
            selectPost,
        };
    },
};
</script>

<style scoped>
.game-container {
    max-width: 900px;
    margin: 0 auto;
}

.game-area {
    position: relative;
}

.posts-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
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

.post-slot {
    position: relative;
    min-height: 200px;
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
