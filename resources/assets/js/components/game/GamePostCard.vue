<template>
    <div
        class="uk-card uk-card-default uk-card-hover post-card"
        :class="{
            'selected': selected,
            'correct': showResult && isCorrect,
            'incorrect': showResult && !isCorrect
        }"
        @click="$emit('select')"
    >
        <div class="uk-card-body">
            <h4 class="uk-card-title reddit-title">
                {{ post.reddit_title || post.title }}
            </h4>
            <div v-if="post.top_comment" class="top-comment uk-margin-small-top">
                <span class="comment-label">
                    Top comment
                    <span v-if="post.top_comment_author"> by u/{{ post.top_comment_author }}</span>:
                </span>
                <p class="comment-body">{{ post.top_comment }}</p>
            </div>
            <hr class="uk-divider-small">
            <p class="uk-text-meta uk-margin-small-top">
                posted by u/{{ post.author }} on {{ formatDate(post.created_at) }}
            </p>
            <Transition name="fade">
                <p v-if="showScore" class="uk-text-bold score-reveal">
                    Score: {{ post.score }}
                </p>
            </Transition>
        </div>
    </div>
</template>

<script>
export default {
    name: 'GamePostCard',
    props: {
        post: {
            type: Object,
            required: true,
        },
        selected: {
            type: Boolean,
            default: false,
        },
        showResult: {
            type: Boolean,
            default: false,
        },
        isCorrect: {
            type: Boolean,
            default: false,
        },
        showScore: {
            type: Boolean,
            default: false,
        },
    },
    emits: ['select'],
    setup() {
        function formatDate(timestamp) {
            if (!timestamp) return '';
            const date = new Date(timestamp * 1000);
            return date.toLocaleDateString(undefined, {
                year: 'numeric',
                month: 'numeric',
                day: 'numeric',
                timeZone: 'UTC',
            });
        }

        return {
            formatDate,
        };
    },
};
</script>

<style scoped>
.post-card {
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s, border-color 0.3s, background-color 0.3s;
    height: 100%;
    overflow: hidden; /* Prevent content overflow */
}

.post-card .uk-card-body {
    overflow: hidden;
}

.post-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

.post-card.selected {
    border: 3px solid var(--accent-primary);
}

.post-card.correct {
    border: 3px solid var(--color-success);
    background-color: var(--color-success-bg) !important;
}

.post-card.incorrect {
    border: 3px solid var(--color-error);
    background-color: var(--color-error-bg) !important;
}

.reddit-title {
    font-size: 1rem;
    line-height: 1.4;
    margin-bottom: 0.5rem;
    word-wrap: break-word;
}

.top-comment {
    background-color: var(--panel-bg);
    border-left: 3px solid var(--card-border);
    padding: 0.5rem 0.75rem;
    border-radius: 0 4px 4px 0;
}

.comment-label {
    font-size: 0.75rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.comment-body {
    font-size: 0.875rem;
    color: var(--text-color);
    margin: 0.25rem 0 0 0;
    white-space: pre-wrap;
    word-wrap: break-word;
    overflow-wrap: break-word;
    word-break: break-word;
    max-height: 200px;
    overflow-y: auto;
}

.score-reveal {
    font-size: 1.2em;
    color: var(--color-reddit);
    margin-top: 0.5rem;
}

.uk-card-title {
    font-size: 1rem;
    margin-bottom: 0.5rem;
}

/* Fade transition for scores */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
