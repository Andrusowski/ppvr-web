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
    background-color: rgba(50, 210, 150, 0.1) !important;
}

[data-theme="dark"] .post-card.correct {
    background-color: rgba(50, 210, 150, 0.35) !important;
}

.post-card.incorrect {
    border: 3px solid #f0506e;
    background-color: rgba(240, 80, 110, 0.1) !important;
}

[data-theme="dark"] .post-card.incorrect {
    background-color: rgba(240, 80, 110, 0.35) !important;
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

[data-theme="dark"] .top-comment {
    background-color: #2a2a2a;
    border-left-color: #444;
}

.comment-label {
    font-size: 0.75rem;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

[data-theme="dark"] .comment-label {
    color: #a0a0a0;
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

[data-theme="dark"] .comment-body {
    color: #e0e0e0;
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
