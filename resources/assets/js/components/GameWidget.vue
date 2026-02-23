<template>
    <div class="game-container">
        <!-- Progress bar -->
        <div class="uk-margin-bottom">
            <div class="uk-text-center uk-margin-small-bottom">
                Round {{ currentRound }} / {{ totalRounds }}
            </div>
            <progress class="uk-progress" :value="gameState === 'won' ? totalRounds :currentRound - 1" :max="totalRounds"></progress>
        </div>

        <!-- Game area -->
        <div v-if="gameState === 'playing'" class="game-area">
            <div class="posts-grid" :class="{ 'transitioning': isTransitioning || showResult }">
                <!-- Left post (fades out during transition) -->
                <div class="post-slot post-slot-left">
                    <div class="post-wrapper" :class="{ 'fade-out': isTransitioning }">
                        <div
                            class="uk-card uk-card-default uk-card-hover post-card"
                            :class="{
                                'selected': selectedPost === 'left',
                                'correct': showResult && leftPost.score >= rightPost.score,
                                'incorrect': showResult && leftPost.score < rightPost.score
                            }"
                            @click="selectPost('left')"
                        >
                            <div class="uk-card-body">
                                <h4 class="uk-card-title reddit-title">
                                    {{ leftPost.reddit_title || leftPost.title }}
                                </h4>
                                <div v-if="leftPost.top_comment" class="top-comment uk-margin-small-top">
                                    <span class="comment-label">
                                        Top comment
                                        <span v-if="leftPost.top_comment_author"> by u/{{ leftPost.top_comment_author }}</span>:
                                    </span>
                                    <p class="comment-body">{{ leftPost.top_comment }}</p>
                                </div>
                                <hr class="uk-divider-small">
                                <p class="uk-text-meta uk-margin-small-top">
                                    posted by u/{{ leftPost.author }} on {{ formatDate(leftPost.created_at) }}
                                </p>
                                <Transition name="fade">
                                    <p v-if="showResult || revealedPostIds.has(leftPost.id)" class="uk-text-bold score-reveal">
                                        Score: {{ leftPost.score }}
                                    </p>
                                </Transition>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right post (slides to left position during transition) -->
                <div class="post-slot post-slot-right" :class="{ 'slide-to-left': isTransitioning }">
                    <div class="post-wrapper">
                        <div
                            class="uk-card uk-card-default uk-card-hover post-card"
                            :class="{
                                'selected': selectedPost === 'right',
                                'correct': showResult && rightPost.score >= leftPost.score,
                                'incorrect': showResult && rightPost.score < leftPost.score
                            }"
                            @click="selectPost('right')"
                        >
                            <div class="uk-card-body">
                                <h4 class="uk-card-title reddit-title">
                                    {{ rightPost.reddit_title || rightPost.title }}
                                </h4>
                                <div v-if="rightPost.top_comment" class="top-comment uk-margin-small-top">
                                    <span class="comment-label">
                                        Top comment
                                        <span v-if="rightPost.top_comment_author"> by u/{{ rightPost.top_comment_author }}</span>:
                                    </span>
                                    <p class="comment-body">{{ rightPost.top_comment }}</p>
                                </div>
                                <hr class="uk-divider-small">
                                <p class="uk-text-meta uk-margin-small-top">
                                    posted by u/{{ rightPost.author }} on {{ formatDate(rightPost.created_at) }}
                                </p>
                                <Transition name="fade">
                                    <p v-if="showResult" class="uk-text-bold score-reveal">
                                        Score: {{ rightPost.score }}
                                    </p>
                                </Transition>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Incoming post (slides in from right during transition) -->
                <div v-if="isTransitioning && incomingPost" class="post-slot post-slot-incoming slide-in-right">
                    <div class="post-wrapper">
                        <div class="uk-card uk-card-default uk-card-hover post-card">
                            <div class="uk-card-body">
                                <h4 class="uk-card-title reddit-title">
                                    {{ incomingPost.reddit_title || incomingPost.title }}
                                </h4>
                                <div v-if="incomingPost.top_comment" class="top-comment uk-margin-small-top">
                                    <span class="comment-label">
                                        Top comment
                                        <span v-if="incomingPost.top_comment_author"> by u/{{ incomingPost.top_comment_author }}</span>:
                                    </span>
                                    <p class="comment-body">{{ incomingPost.top_comment }}</p>
                                </div>
                                <hr class="uk-divider-small">
                                <p class="uk-text-meta uk-margin-small-top">
                                    posted by u/{{ incomingPost.author }} on {{ formatDate(incomingPost.created_at) }}
                                </p>
                            </div>
                        </div>
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
                    <div v-if="countdown" class="countdown-container">
                        <p class="uk-text-meta">Next game in:</p>
                        <p class="countdown-timer">{{ countdown }}</p>
                    </div>
                </div>
                <div class="post-links uk-margin-top">
                    <p class="uk-text-muted">Posts in today's game:</p>
                    <div class="uk-flex uk-flex-column uk-flex-middle">
                        <a
                            v-for="post in posts"
                            :key="post.id"
                            :href="'/post/' + post.id"
                            class="post-link-button"
                        >
                            <span class="post-link-title">{{ post.title }}</span>
                            <span class="post-link-meta">
                                <span class="post-link-score">{{ post.score }} points</span>
                                <span class="post-link-author">by u/{{ post.author }}</span>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Loss modal -->
        <Transition name="fade">
            <div v-if="gameState === 'lost'" class="uk-text-center uk-margin-large-top">
                <div class="uk-card uk-card-secondary uk-card-body result-card">
                    <h2 class="uk-card-title">Game Over</h2>
                    <p>You made it to round {{ currentRound }} of {{ totalRounds }}.</p>
                    <div v-if="countdown" class="countdown-container">
                        <p class="uk-text-meta">Next game in:</p>
                        <p class="countdown-timer">{{ countdown }}</p>
                    </div>
                </div>
                <div class="post-links uk-margin-top">
                    <p class="uk-text-muted">Posts you encountered:</p>
                    <div class="uk-flex uk-flex-column uk-flex-middle">
                        <a
                            v-for="post in playedPosts"
                            :key="post.id"
                            :href="'/post/' + post.id"
                            class="post-link-button"
                        >
                            <span class="post-link-title">{{ post.title }}</span>
                            <span class="post-link-meta">
                                <span class="post-link-score">{{ post.score }} points</span>
                                <span class="post-link-author">posted by u/{{ post.author }}</span>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Already played today -->
        <div v-if="gameState === 'already_played'" class="uk-text-center uk-margin-large-top">
            <div class="uk-card uk-card-default uk-card-body">
                <h2 class="uk-card-title">Already Played Today</h2>
                <p v-if="savedResult === 'won'">You won today's game!</p>
                <p v-else>You reached round {{ savedRound }} of {{ totalRounds }}.</p>
                <div v-if="countdown" class="countdown-container">
                    <p class="uk-text-meta">Next game in:</p>
                    <p class="countdown-timer">{{ countdown }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted, inject } from 'vue';

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
        const countdown = ref('');
        let countdownInterval = null;

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

        function formatDate(timestamp) {
            if (!timestamp) return '';
            const date = new Date(timestamp * 1000);
            return date.toLocaleDateString(undefined, {
                year: 'numeric',
                month: 'numeric',
                day: 'numeric',
            });
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

        function saveResult(result) {
            const data = {
                round: currentRound.value,
                result: result,
            };
            localStorage.setItem(storageKey, JSON.stringify(data));
            startCountdown();
        }

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
                            // Won the game
                            gameState.value = 'won';
                            saveResult('won');
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
                    setTimeout(() => {
                        gameState.value = 'lost';
                        saveResult('lost');
                    }, 1500);
                }
            } catch (error) {
                console.error('Error validating choice:', error);
            }
        }

        onMounted(() => {
            const alreadyPlayed = loadProgress();
            if (alreadyPlayed) {
                startCountdown();
            }
        });

        onUnmounted(() => {
            stopCountdown();
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
            countdown,
            formatDate,
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

.post-card {
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s, border-color 0.3s, background-color 0.3s;
    height: 100%;
}

.post-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

.post-card.selected {
    border: 3px solid #1e87f0;
}

.post-card.correct {
    border: 3px solid #32d296;
    background-color: rgba(50, 210, 150, 0.1);
}

.post-card.incorrect {
    border: 3px solid #f0506e;
    background-color: rgba(240, 80, 110, 0.1);
}

.reddit-title {
    font-size: 1rem;
    line-height: 1.4;
    margin-bottom: 0.5rem;
    word-wrap: break-word;
}

.top-comment {
    background-color: #f8f8f8;
    border-left: 3px solid #ddd;
    padding: 0.5rem 0.75rem;
    border-radius: 0 4px 4px 0;
}

.comment-label {
    font-size: 0.75rem;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.comment-body {
    font-size: 0.875rem;
    color: #333;
    margin: 0.25rem 0 0 0;
    white-space: pre-wrap;
    word-wrap: break-word;
    max-height: 100px;
    overflow-y: auto;
}

.score-reveal {
    font-size: 1.2em;
    color: #ff4500;
    margin-top: 0.5rem;
}

.uk-card-title {
    font-size: 1rem;
    margin-bottom: 0.5rem;
}

.result-card {
    animation: pop-in 0.4s ease-out;
}

/* Fade transition for scores and modals */
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

/* Post link buttons for win/loss modals */
.post-links {
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.post-link-button {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    width: 100%;
    padding: 0.75rem 1rem;
    margin-bottom: 0.5rem;
    background-color: #f8f8f8;
    border: 1px solid #e5e5e5;
    border-radius: 4px;
    text-decoration: none;
    color: inherit;
    transition: background-color 0.2s, border-color 0.2s, transform 0.2s;
}

.post-link-button:hover {
    background-color: #fff;
    border-color: #1e87f0;
    transform: translateX(4px);
    text-decoration: none;
}

.post-link-title {
    font-size: 0.9rem;
    color: #333;
    font-weight: 500;
    line-height: 1.3;
    word-wrap: break-word;
    text-align: left;
}

.post-link-meta {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.25rem;
    font-size: 0.75rem;
}

.post-link-score {
    color: #ff4500;
    font-weight: 500;
}

.post-link-author {
    color: #999;
}

/* Countdown timer styles */
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
