<template>
    <div class="post-links">
        <p class="uk-text-muted">{{ title }}</p>
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
                    <span class="post-link-author">{{ authorPrefix }}u/{{ post.author }}</span>
                </span>
            </a>
        </div>
    </div>
</template>

<script>
export default {
    name: 'GamePostLinks',
    props: {
        posts: {
            type: Array,
            required: true,
        },
        title: {
            type: String,
            default: 'Posts:',
        },
        authorPrefix: {
            type: String,
            default: 'by ',
        },
    },
};
</script>

<style scoped>
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
    width: -moz-available;
    width: -webkit-fill-available;
    width: fill-available;
    padding: 0.75rem 1rem;
    margin-bottom: 0.5rem;
    background-color: var(--panel-bg);
    border: 1px solid var(--card-border);
    border-radius: 4px;
    text-decoration: none;
    color: inherit;
    transition: background-color 0.2s, border-color 0.2s, transform 0.2s;
}

.post-link-button:hover {
    background-color: var(--surface-bg);
    border-color: var(--accent-primary);
    transform: translateX(4px);
    text-decoration: none;
}

.post-link-title {
    font-size: 0.9rem;
    color: var(--text-color);
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
    color: var(--color-reddit);
    font-weight: 500;
}

.post-link-author {
    color: var(--text-muted);
}
</style>
